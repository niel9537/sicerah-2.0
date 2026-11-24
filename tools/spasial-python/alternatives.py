"""Overture downloads and imports that do not depend on Overpass."""
from datetime import datetime, timezone
import json
from pathlib import Path
import subprocess
import sys
import tempfile

from shapely.geometry import shape
from shapely.errors import GEOSException
from shapely.ops import unary_union

from core import CATEGORIES, category, convert_osm, validate_geojson


def boundary():
    path = Path(__file__).resolve().parents[2] / "public/data/gis/nias-barat-desa.geojson"
    data = json.loads(path.read_text(encoding="utf-8"))
    result = unary_union([shape(f["geometry"]) for f in data["features"]])
    if result.is_empty or not result.is_valid:
        raise ValueError("Batas Nias Barat tidak valid.")
    return result


def normalize(data, region=None):
    if not isinstance(data, dict):
        raise ValueError("Data harus berupa objek GeoJSON atau hasil Overpass.")
    if "elements" in data:
        return convert_osm(data)
    if data.get("type") != "FeatureCollection" or not isinstance(data.get("features"), list):
        raise ValueError("Berkas harus berupa GeoJSON FeatureCollection.")
    features = []
    for feature in data["features"]:
        if not isinstance(feature, dict) or not isinstance(feature.get("properties") or {}, dict):
            raise ValueError("Objek GeoJSON tidak valid.")
        props = dict(feature.get("properties") or {})
        try:
            geom = shape(feature.get("geometry"))
        except (ValueError, TypeError, KeyError, AttributeError, GEOSException) as error:
            raise ValueError("Geometri GeoJSON tidak dapat dibaca.") from error
        if region is not None and not region.covers(geom.representative_point()):
            continue
        if props.get("operating_status") == "permanently_closed":
            continue
        group = props.get("category")
        if not isinstance(group, str) or group not in CATEGORIES:
            tags = {**props, **(props.get("tags") or {})}
            taxonomy = props.get("taxonomy") or {}
            legacy = props.get("categories") or {}
            labels = {props.get("basic_category"), taxonomy.get("primary"), legacy.get("primary"), props.get("subtype"), props.get("class")}
            labels.update(taxonomy.get("hierarchy") or [])
            if labels & {"school", "education", "elementary_school", "high_school", "kindergarten", "college", "university"}:
                group = "school"
            elif labels & {"hospital", "medical_clinic", "clinic", "healthcare", "doctors", "dentist", "community_health_center", "outpatient_care_facility", "dental_clinic", "medical_center"}:
                group = "health"
            elif labels & {"government_office", "government", "town_hall", "police_station", "courthouse", "fire_station"}:
                group = "government"
            else:
                group = category(tags) or "unclassified"
        names = props.get("names") or {}
        props.update(category=group, name=props.get("name") or names.get("primary") or "Bangunan tanpa nama")
        props.setdefault("geometry_label", "Titik lokasi" if geom.geom_type == "Point" else "Poligon dari sumber")
        # Retain original IDs, source attribution and metadata in the exported data.
        features.append({**feature, "properties": props})
    return validate_geojson({**data, "features": features})


def download_overture(kind="building", progress=None):
    if kind not in {"building", "place"}:
        raise ValueError("Jenis Overture tidak didukung.")
    region = boundary()
    if progress:
        progress("Mengunduh Overture, maksimal 3 menit...")
    with tempfile.TemporaryDirectory(prefix="sicerah-overture-") as temporary:
        output = Path(temporary) / "data.geojson"
        command = [sys.executable, "-m", "overturemaps", "download", "--bbox=" + ",".join(map(str, region.bounds)),
                   "-f", "geojson", "--type", kind, "--connect_timeout", "5", "--request_timeout", "20", "-o", str(output)]
        try:
            result = subprocess.run(command, capture_output=True, text=True, timeout=180,
                                    creationflags=subprocess.CREATE_NO_WINDOW if sys.platform == "win32" else 0)
        except subprocess.TimeoutExpired as error:
            raise RuntimeError("Unduhan Overture melewati 3 menit. Coba unduh GeoJSON melalui explore.overturemaps.org.") from error
        if result.returncode or not output.exists():
            raise RuntimeError("Overture belum dapat diunduh. " + result.stderr[-1500:])
        data = normalize(json.loads(output.read_text(encoding="utf-8")), region)
    data.update(source="Overture Maps Foundation", updated_at=datetime.now(timezone.utc).isoformat())
    return data
