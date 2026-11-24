"""Produce a smaller display file while retaining original geometry and full source."""
import json
from pathlib import Path


def export():
    root = Path(__file__).resolve().parents[2]
    source = root / "public/data/gis/nias-barat-buildings.geojson"
    target = root / "public/data/gis/nias-barat-buildings-map.geojson"
    data = json.loads(source.read_text(encoding="utf-8"))
    for feature in data["features"]:
        feature["properties"] = {key: feature["properties"][key] for key in ["name", "category", "geometry_label"]}
    data["full_source"] = source.name
    target.write_text(json.dumps(data, ensure_ascii=False, separators=(",", ":"), allow_nan=False), encoding="utf-8")
    print(f"{len(data['features'])} objects exported to {target}")


if __name__ == "__main__":
    export()
