import json
from pathlib import Path
import sys
from unittest.mock import Mock, patch

import pytest
from shapely.geometry import box

sys.path.insert(0, str(Path(__file__).resolve().parents[1]))
from alternatives import download_overture, normalize


def feature(props=None, x=97.4, y=1.0):
    return {"type":"Feature", "geometry":{"type":"Point","coordinates":[x,y]}, "properties":props or {}}


def test_import_does_not_guess_category_from_name():
    data = normalize({"type":"FeatureCollection","features":[feature({"names":{"primary":"Sekolah dalam nama"}})]})
    assert data["features"][0]["properties"]["category"] == "unclassified"
    assert data["features"][0]["properties"]["name"] == "Sekolah dalam nama"


def test_categories_from_old_and_new_overture_schema():
    data = normalize({"type":"FeatureCollection","features":[
        feature({"taxonomy":{"primary":"school"}}), feature({"categories":{"primary":"dental_clinic"}}),
        feature({"basic_category":"government_office"}), feature({"tags":{"amenity":"hospital"}})]})
    assert [f["properties"]["category"] for f in data["features"]] == ["school","health","government","health"]


def test_excludes_neighbouring_regency_and_closed_places():
    data = normalize({"type":"FeatureCollection","features":[feature(), feature(x=98),
        feature({"operating_status":"permanently_closed"})]}, box(97.3,.9,97.5,1.1))
    assert len(data["features"]) == 1


def test_accepts_raw_overpass_json():
    data = normalize({"elements":[{"type":"node","id":1,"lon":97.4,"lat":1,"tags":{"amenity":"school"}}]})
    assert data["features"][0]["properties"]["category"] == "school"


def test_malformed_geometry_import_returns_readable_error():
    data = {"type":"FeatureCollection","features":[{"type":"Feature","geometry":{"type":"Polygon","coordinates":[[[1,1],[2,2]]]},"properties":{}}]}
    with pytest.raises(ValueError, match="Geometri"):
        normalize(data)


def test_overture_failure_is_reported_and_subprocess_is_bounded():
    result = Mock(returncode=1, stderr="network unavailable")
    with patch("alternatives.boundary", return_value=box(97.3,.9,97.5,1.1)), patch("alternatives.subprocess.run", return_value=result) as run:
        with pytest.raises(RuntimeError, match="network unavailable"):
            download_overture()
        assert run.call_args.kwargs["timeout"] == 180
        assert "shell" not in run.call_args.kwargs


def test_bundled_snapshot_has_real_buildings_and_category_counts():
    path = Path(__file__).resolve().parents[3] / "public/data/gis/nias-barat-buildings.geojson"
    # tests -> spasial-python -> tools -> project
    data = json.loads(path.read_text(encoding="utf-8"))
    from collections import Counter
    assert Counter(f["properties"]["category"] for f in data["features"]) == {
        "unclassified":26615, "school":34, "government":23, "health":7}
    assert data["release"] == "2026-11-18.0"
    assert all(f["properties"].get("sources") for f in data["features"])
