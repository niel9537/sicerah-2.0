"""Leaflet rendering, separated from Streamlit for validation and HTML export."""
from html import escape
import re

import folium
from folium.plugins import Fullscreen, MarkerCluster
from shapely.geometry import shape

from core import CATEGORIES


def make_map(data, focus=None):
    result = folium.Map(location=[1.0, 97.45], zoom_start=10, tiles="OpenStreetMap", control_scale=True, prefer_canvas=True)
    Fullscreen().add_to(result)
    groups = {key: folium.FeatureGroup(name=label).add_to(result) for key, (label, _, _) in CATEGORIES.items()}
    clusters = {key: MarkerCluster(name=label, control=False).add_to(groups[key])
                for key, (label, _, _) in CATEGORIES.items()}
    bounds = []
    polygons = {key: [] for key in CATEGORIES}
    for feature in data["features"]:
        props, geom = feature["properties"], shape(feature["geometry"])
        group = props["category"]
        label, color, marker_color = CATEGORIES[group]
        name = escape(str(props.get("name") or "Tanpa nama"))
        kind = escape(str(props.get("geometry_label") or ("Lokasi" if geom.geom_type == "Point" else "Poligon sumber")))
        identity = str(props.get("osm_id", ""))
        source = (f'<a href="https://www.openstreetmap.org/{identity}" target="_blank" rel="noopener noreferrer">Sumber OpenStreetMap</a>'
                  if re.fullmatch(r"(node|way|relation)/[1-9][0-9]*", identity) else escape(str(data.get("source", "GeoJSON unggahan"))))
        popup = folium.Popup(f"<strong>{name}</strong><br>{label}<br>{kind}<br>{source}", max_width=320)
        if geom.geom_type == "Point":
            folium.Marker([geom.y, geom.x], tooltip=name, popup=popup,
                          icon=folium.Icon(color=marker_color, icon="info-sign")).add_to(clusters[group])
        else:
            # Only sanitized display fields enter generated HTML/JavaScript.
            safe = {"type": "Feature", "geometry": feature["geometry"], "properties": {
                "name": name, "category_label": label, "geometry_label": kind, "source": source}}
            polygons[group].append(safe)
        west, south, east, north = geom.bounds
        bounds.extend([[south, west], [north, east]])
    for key, features in polygons.items():
        if features:
            folium.GeoJson({"type": "FeatureCollection", "features": features},
                style_function=lambda _, c=CATEGORIES[key][1]: {"color": c, "weight": 1, "fillOpacity": .35},
                tooltip=folium.GeoJsonTooltip(fields=["name"], labels=False),
                popup=folium.GeoJsonPopup(fields=["name", "category_label", "geometry_label", "source"],
                                         aliases=["Nama", "Kategori", "Geometri", "Sumber"])).add_to(groups[key])
    if focus is not None:
        west, south, east, north = shape(focus["geometry"]).bounds
        result.fit_bounds([[south, west], [north, east]], max_zoom=18)
    elif bounds:
        result.fit_bounds([[min(p[0] for p in bounds), min(p[1] for p in bounds)],
                           [max(p[0] for p in bounds), max(p[1] for p in bounds)]], max_zoom=15)
    folium.LayerControl(collapsed=False).add_to(result)
    return result
