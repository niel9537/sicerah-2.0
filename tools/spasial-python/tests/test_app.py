import json
from pathlib import Path
import sys
from unittest.mock import Mock, patch

import pytest
from shapely.geometry import shape

sys.path.insert(0, str(Path(__file__).resolve().parents[1]))
from core import convert_osm, download, save, select, validate_geojson
from viewer import make_map


def ring(a=97.4, b=1.0, size=.01):
    return [{"lon": x, "lat": y} for x, y in [(a,b),(a+size,b),(a+size,b+size),(a,b+size),(a,b)]]


def dataset():
    return convert_osm({"elements": [
        {"type":"node","id":1,"lon":97.4,"lat":1,"tags":{"office":"government","name":"Kantor Uji"}},
        {"type":"way","id":2,"geometry":ring(),"tags":{"amenity":"school","name":"Sekolah Uji"}},
        {"type":"way","id":3,"geometry":ring(),"tags":{"healthcare":"clinic","building":"yes"}},
    ]})


def test_distinguishes_location_site_and_footprint():
    features = dataset()["features"]
    assert [f["properties"]["category"] for f in features] == ["government", "school", "health"]
    assert features[0]["geometry"]["type"] == "Point"
    assert features[1]["properties"]["geometry_label"] == "Area fasilitas (bukan tapak bangunan)"
    assert features[2]["properties"]["geometry_label"] == "Tapak bangunan"


def test_preserves_multipolygon_hole():
    data = convert_osm({"elements":[{"type":"relation","id":10,"tags":{"type":"multipolygon","amenity":"school"},
        "members":[{"type":"way","role":"outer","geometry":ring(size=.02)},
                   {"type":"way","role":"inner","geometry":ring(97.405,1.005,.005)}]}]})
    geom = shape(data["features"][0]["geometry"])
    assert geom.geom_type == "Polygon"
    assert len(geom.interiors) == 1
    assert geom.area == pytest.approx(.02**2 - .005**2)


def test_unclosed_way_is_not_fabricated_into_polygon():
    feature = {"type":"way","id":1,"geometry":ring()[:-1],"center":{"lon":97.4,"lat":1},"tags":{"amenity":"hospital"}}
    assert convert_osm({"elements":[feature]})["features"][0]["geometry"]["type"] == "Point"


def test_does_not_classify_by_name():
    assert convert_osm({"elements":[{"type":"node","id":1,"lon":97.4,"lat":1,
                                    "tags":{"name":"Sekolah palsu","building":"yes"}}]})["features"] == []


def test_filters_and_serializes(tmp_path):
    data = dataset()
    assert len(select(data, ["school"], "sekolah", "Poligon")["features"]) == 1
    assert select(data, [], "")["features"] == []
    path = tmp_path / "cache.geojson"
    save(data, path)
    assert len(validate_geojson(json.loads(path.read_text(encoding="utf-8")))["features"]) == 3


def test_invalid_coordinate_upload_is_rejected():
    data = dataset()
    data["features"][0]["geometry"]["coordinates"] = [999, 1]
    with pytest.raises(ValueError):
        validate_geojson(data)


def test_endpoint_fallback_and_missing_boundary():
    invalid = Mock()
    invalid.raise_for_status.return_value = None
    invalid.json.return_value = {"elements":[{"type":"count","tags":{"areas":"0"}}]}
    valid = Mock()
    valid.raise_for_status.return_value = None
    valid.json.return_value = {"elements":[{"type":"count","tags":{"areas":"1"}}]}
    with patch("core.requests.get", side_effect=[invalid, valid]) as get:
        assert download()["features"] == []
        assert get.call_count == 2
        assert '"admin_level"="5"' in get.call_args.kwargs["params"]["data"]


def test_map_html_is_safe_and_contains_real_geometry():
    data = dataset()
    data["features"][0]["properties"]["name"] = '<script>alert("x")</script>'
    html = make_map(data).get_root().render()
    assert '<script>alert("x")</script>' not in html
    assert "openstreetmap.org" in html
    assert "Sekolah Uji" in html
    assert "Polygon" in html
    assert "MarkerCluster" in html or "markerClusterGroup" in html


def test_streamlit_initial_screen():
    from streamlit.testing.v1 import AppTest
    app = AppTest.from_file(str(Path(__file__).resolve().parents[1] / "app.py"))
    app.run(timeout=25)
    assert not app.exception
    assert "Bangunan Publik Nias Barat" in app.title[0].value


def test_streamlit_can_render_loaded_dataset():
    from streamlit.testing.v1 import AppTest
    app = AppTest.from_file(str(Path(__file__).resolve().parents[1] / "app.py"))
    app.session_state["dataset"] = dataset()
    app.run(timeout=25)
    assert not app.exception
