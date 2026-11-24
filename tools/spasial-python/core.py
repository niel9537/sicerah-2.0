"""Read OSM facilities without inferring use from a building's appearance."""
from datetime import datetime, timezone
import json
import math
from pathlib import Path

import requests
from shapely.geometry import LineString, Point, Polygon, mapping, shape
from shapely.errors import GEOSException
from shapely.ops import polygonize_full, unary_union

CATEGORIES = {
    "government": ("Pemerintah", "#7c3aed", "purple"),
    "school": ("Sekolah", "#2563eb", "blue"),
    "health": ("Fasilitas kesehatan", "#dc2626", "red"),
}
ENDPOINTS = (
    "https://overpass-api.de/api/interpreter",
    "https://overpass.kumi.systems/api/interpreter",
    "https://overpass.private.coffee/api/interpreter",
)
QUERY = '''[out:json][timeout:20];
area["boundary"="administrative"]["admin_level"="5"]["name"~"^(Kabupaten )?Nias Barat$"]->.region;
.region out count;
(
nwr(area.region)["office"="government"];
nwr(area.region)["building"="government"];
nwr(area.region)["amenity"~"^(townhall|police|courthouse|fire_station|school|kindergarten|college|university|hospital|clinic|doctors|dentist|health_post)$"];
nwr(area.region)["healthcare"~"^(hospital|clinic|doctor|dentist|health_post|centre)$"];
);
out geom center;'''


def category(tags):
    amenity = tags.get("amenity")
    if amenity in {"school", "kindergarten", "college", "university"}:
        return "school"
    if amenity in {"hospital", "clinic", "doctors", "dentist", "health_post"} or tags.get("healthcare") in {
        "hospital", "clinic", "doctor", "dentist", "health_post", "centre"
    }:
        return "health"
    if tags.get("office") == "government" or tags.get("building") == "government" or amenity in {
        "townhall", "police", "courthouse", "fire_station"
    }:
        return "government"
    return None


def coordinate(point):
    lon, lat = float(point["lon"]), float(point["lat"])
    if not math.isfinite(lon) or not math.isfinite(lat) or not (-180 <= lon <= 180 and -90 <= lat <= 90):
        raise ValueError("Koordinat tidak valid.")
    return lon, lat


def geometry(element):
    """Keep closed outlines; unavailable outlines fall back to source centres."""
    kind = element["type"]
    if kind == "way":
        ring = [coordinate(p) for p in element.get("geometry", [])]
        if len(ring) >= 4 and ring[0] == ring[-1]:
            polygon = Polygon(ring)
            if polygon.is_valid and not polygon.is_empty and polygon.area > 0:
                return polygon
    if kind == "relation" and element.get("tags", {}).get("type") == "multipolygon":
        outer, inner = [], []
        for member in element.get("members", []):
            if member.get("type") != "way" or member.get("role", "outer") not in {"", "outer", "inner"}:
                continue
            line = [coordinate(p) for p in member.get("geometry", [])]
            if len(line) >= 2:
                (inner if member.get("role") == "inner" else outer).append(LineString(line))
        if outer:
            faces, cuts, dangles, invalid = polygonize_full(unary_union(outer))
            polygons = list(faces.geoms) if all(g.is_empty for g in (cuts, dangles, invalid)) else []
            if polygons:
                result = unary_union(polygons)
                holes = []
                if inner:
                    faces, cuts, dangles, invalid = polygonize_full(unary_union(inner))
                    if not all(g.is_empty for g in (cuts, dangles, invalid)):
                        centre = element.get("center")
                        return Point(coordinate(centre)) if centre else None
                    holes = list(faces.geoms)
                if holes:
                    result = result.difference(unary_union(holes))
                if result.geom_type in {"Polygon", "MultiPolygon"} and result.is_valid and not result.is_empty:
                    return result
    centre = element if kind == "node" else element.get("center")
    return Point(coordinate(centre)) if centre else None


def convert_osm(raw):
    if not isinstance(raw.get("elements"), list) or raw.get("remark"):
        raise ValueError("Respons OSM tidak lengkap; coba lagi nanti.")
    features = {}
    for element in raw["elements"]:
        tags = element.get("tags", {})
        group = category(tags)
        if not group or element.get("type") not in {"node", "way", "relation"}:
            continue
        if not isinstance(element.get("id"), int) or element["id"] <= 0:
            continue
        try:
            geom = geometry(element)
        except (KeyError, ValueError, TypeError, GEOSException):
            continue
        if geom is None:
            continue
        label = "Lokasi fasilitas (tapak belum tersedia)" if geom.geom_type == "Point" else (
            "Tapak bangunan" if tags.get("building") not in {None, "no"} else "Area fasilitas (bukan tapak bangunan)")
        identity = f"{element['type']}/{element['id']}"
        features[identity] = {"type": "Feature", "id": identity, "geometry": mapping(geom), "properties": {
            "name": tags.get("name") or "Fasilitas tanpa nama", "category": group,
            "geometry_label": label, "source": "OpenStreetMap contributors",
            "osm_url": f"https://www.openstreetmap.org/{identity}", "osm_id": identity,
            "building": tags.get("building", ""), "amenity": tags.get("amenity", ""),
            "operator": tags.get("operator", ""),
        }}
    return {"type": "FeatureCollection", "features": list(features.values()),
            "source": "OpenStreetMap contributors", "license": "ODbL-1.0",
            "updated_at": datetime.now(timezone.utc).isoformat()}


def download(progress=None):
    errors = []
    for endpoint in ENDPOINTS:
        if progress:
            progress(endpoint)
        try:
            response = requests.get(endpoint, params={"data": QUERY}, timeout=(5, 25),
                                    headers={"User-Agent": "SICERAH-Spasial/1.0 (+https://github.com/niel9537/sicerah-2.0)", "Accept": "application/json"})
            response.raise_for_status()
            raw = response.json()
            counts = [e.get("tags", {}) for e in raw.get("elements", []) if e.get("type") == "count"]
            if not counts or not any(int(c.get("areas", 0)) > 0 for c in counts):
                raise ValueError("Batas Kabupaten Nias Barat tidak ditemukan pada sumber.")
            return convert_osm(raw)
        except (requests.RequestException, ValueError, TypeError) as error:
            errors.append(f"{endpoint}: {error}")
    raise RuntimeError("Layanan OSM belum dapat dijangkau. Gunakan data tersimpan atau unggah GeoJSON.\n" + "\n".join(errors))


def validate_geojson(data):
    if not isinstance(data, dict) or data.get("type") != "FeatureCollection" or not isinstance(data.get("features"), list):
        raise ValueError("Unggah GeoJSON FeatureCollection.")
    if len(data["features"]) > 20000:
        raise ValueError("Maksimal 20.000 fitur per berkas.")
    result = {**data, "features": []}
    for feature in data["features"]:
        if not isinstance(feature, dict):
            raise ValueError("Setiap objek harus berupa GeoJSON Feature.")
        props = feature.get("properties") or {}
        if not isinstance(props, dict) or not isinstance(props.get("category"), str) or props["category"] not in CATEGORIES:
            raise ValueError("Setiap fitur harus memiliki category: government, school, atau health.")
        try:
            geom = shape(feature.get("geometry"))
        except (ValueError, TypeError, KeyError, AttributeError, GEOSException) as error:
            raise ValueError("Geometri GeoJSON tidak dapat dibaca.") from error
        if geom.is_empty or not geom.is_valid or geom.geom_type not in {"Point", "Polygon", "MultiPolygon"}:
            raise ValueError("Geometri harus berupa Point, Polygon, atau MultiPolygon yang valid.")
        west, south, east, north = geom.bounds
        if not all(math.isfinite(v) for v in geom.bounds) or west < -180 or east > 180 or south < -90 or north > 90:
            raise ValueError("GeoJSON harus memakai koordinat WGS84 (bujur, lintang).")
        result["features"].append({**feature, "properties": {**props, "name": str(props.get("name") or "Tanpa nama")}})
    return result


def save(data, path):
    path = Path(path)
    path.parent.mkdir(parents=True, exist_ok=True)
    temporary = path.with_suffix(".tmp")
    temporary.write_text(json.dumps(data, ensure_ascii=False, allow_nan=False), encoding="utf-8")
    temporary.replace(path)


def select(data, categories, query="", geometry_type="Semua"):
    features = [f for f in data["features"] if f["properties"]["category"] in categories
                and query.casefold() in f["properties"].get("name", "").casefold()
                and (geometry_type == "Semua" or
                     (geometry_type == "Poligon" and f["geometry"]["type"] != "Point") or
                     (geometry_type == "Titik" and f["geometry"]["type"] == "Point"))]
    return {**data, "features": features}
