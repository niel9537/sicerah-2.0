"""Run with: python -m streamlit run app.py."""
import hashlib
import json
from pathlib import Path

import streamlit as st
from streamlit_folium import st_folium

from core import CATEGORIES, download, save, select, validate_geojson
from viewer import make_map

ROOT = Path(__file__).resolve().parent
CACHE = ROOT / "data" / "nias-barat-facilities.geojson"
st.set_page_config(page_title="SICERAH · Bangunan Publik", page_icon="🗺️", layout="wide")
st.title("Bangunan Publik Nias Barat")
st.caption("Peta Leaflet · Pemerintah, sekolah, dan fasilitas kesehatan · Sumber OpenStreetMap")

if "dataset" not in st.session_state:
    st.session_state.dataset = None
    if CACHE.exists():
        try:
            st.session_state.dataset = validate_geojson(json.loads(CACHE.read_text(encoding="utf-8")))
        except (ValueError, TypeError, KeyError) as error:
            st.warning(f"Data tersimpan tidak dapat dibaca: {error}")

with st.sidebar:
    st.header("Sumber data")
    if st.button("Ambil / Perbarui OpenStreetMap", type="primary", use_container_width=True):
        progress = st.empty()
        try:
            with st.spinner("Mengambil fasilitas seluruh Kabupaten Nias Barat..."):
                data = download(lambda endpoint: progress.caption(f"Menghubungi {endpoint}"))
                validate_geojson(data)
                save(data, CACHE)
                st.session_state.dataset = data
            st.success(f"{len(data['features'])} objek OSM berhasil disimpan.")
        except (RuntimeError, ValueError, OSError) as error:
            st.error("Unduhan gagal. Data sebelumnya tidak dihapus.")
            with st.expander("Detail koneksi"):
                st.text(str(error))
        finally:
            progress.empty()
    uploaded = st.file_uploader("Buka GeoJSON tersimpan", type=["geojson", "json"])
    if uploaded:
        content = uploaded.getvalue()
        digest = hashlib.sha256(content).hexdigest()
        if digest != st.session_state.get("uploaded_digest"):
            try:
                if len(content) > 20 * 1024 * 1024:
                    raise ValueError("Maksimal berkas 20 MB.")
                st.session_state.dataset = validate_geojson(json.loads(content))
                st.session_state.uploaded_digest = digest
            except (ValueError, TypeError, KeyError, AttributeError) as error:
                st.error(f"Berkas tidak dapat dibaca: {error}")
    st.divider()
    groups = st.multiselect("Kategori", options=list(CATEGORIES), default=list(CATEGORIES),
                            format_func=lambda key: CATEGORIES[key][0])
    search = st.text_input("Cari nama fasilitas", placeholder="Nama sekolah, puskesmas, kantor...")
    geom_type = st.selectbox("Geometri", ["Semua", "Poligon", "Titik"])
    st.caption("Ungu: pemerintah · Biru: sekolah · Merah: kesehatan")

data = st.session_state.dataset
if data is None:
    st.info("Klik Ambil / Perbarui OpenStreetMap untuk memuat data nyata, atau unggah GeoJSON hasil unduhan sebelumnya.")
    st_folium(make_map({"type": "FeatureCollection", "features": []}), height=550,
              use_container_width=True, returned_objects=[])
    st.stop()

filtered = select(data, groups, search, geom_type)
columns = st.columns(3)
for column, (key, (label, _, _)) in zip(columns, CATEGORIES.items()):
    column.metric(label, sum(f["properties"]["category"] == key for f in filtered["features"]))
st.caption(f"{len(filtered['features'])} objek ditampilkan · Waktu unduh: {data.get('updated_at', 'tidak tercatat')}")
st.info("Tapak bangunan dan area fasilitas mengikuti geometri OSM. Titik berarti tapak belum tersedia. "
        "Kategori berasal dari tag OSM; ini belum segmentasi atap berbasis citra dan bukan inventaris lengkap.")

focus_index = st.selectbox("Fokus ke fasilitas", options=[None] + list(range(len(filtered["features"]))),
    format_func=lambda index: "Seluruh hasil" if index is None else (
        CATEGORIES[filtered["features"][index]["properties"]["category"]][0] + " · " +
        filtered["features"][index]["properties"]["name"] + f" [{index + 1}]"))
focus = None if focus_index is None else filtered["features"][focus_index]
map_view = make_map(filtered, focus=focus)
st_folium(map_view, height=600, use_container_width=True, returned_objects=[], key="facility_map")

if not filtered["features"]:
    st.warning("Tidak ada objek untuk filter ini. Jika unduhan kosong, cakupan fasilitas OSM mungkin belum tersedia.")
rows = [{"Nama": f["properties"]["name"], "Kategori": CATEGORIES[f["properties"]["category"]][0],
         "Geometri": f["properties"].get("geometry_label", f["geometry"]["type"]),
         "ID OSM": f["properties"].get("osm_id", "Data unggahan")}
        for f in filtered["features"]]
st.dataframe(rows, use_container_width=True, hide_index=True)
left, right = st.columns(2)
left.download_button("Unduh GeoJSON hasil filter", json.dumps(filtered, ensure_ascii=False, allow_nan=False),
                     file_name="nias-barat-facilities.geojson", mime="application/geo+json")
right.download_button("Unduh peta HTML", map_view.get_root().render(),
                      file_name="nias-barat-facilities-map.html", mime="text/html")
st.caption("Data © OpenStreetMap contributors · ODbL · https://www.openstreetmap.org/copyright. "
           "Tile peta dan pustaka peta HTML memerlukan internet; GeoJSON dapat dibuka secara luring di QGIS.")
