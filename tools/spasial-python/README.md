# Aplikasi Python Bangunan Publik Nias Barat

Jalankan dari PowerShell pada root proyek:

```powershell
powershell -ExecutionPolicy Bypass -File tools/spasial-python/run.ps1
```

Buka http://127.0.0.1:8501 lalu klik **Ambil / Perbarui OpenStreetMap**. Python 3.10+ diperlukan. Dependensi dipasang pada virtual environment lokal `.venv-spasial`.

## Fitur

- Peta Leaflet/OpenStreetMap dengan kategori pemerintah, sekolah, dan fasilitas kesehatan.
- Menampilkan poligon asli jika tersedia, termasuk multipolygon; lokasi tanpa tapak tampil sebagai titik. Lubang poligon dipertahankan.
- Cari nama, pilih kategori, filter titik/poligon, dan fokus ke fasilitas.
- Ekspor GeoJSON WGS84 dan peta HTML. Peta HTML masih memakai pustaka dan tile daring.
- Unduhan berhasil disimpan di `data/nias-barat-facilities.geojson`, diabaikan Git. Kegagalan unduhan tidak menghapus data lama. Aplikasi mencoba tiga penyedia Overpass.
- Buka kembali GeoJSON dengan properti `category` berisi `government`, `school`, atau `health`.

Data OSM belum tentu lengkap. Kategori mengikuti tag sumber, bukan tebakan dari nama/atap. Area fasilitas tidak disamakan dengan tapak bangunan. Jumlah adalah jumlah objek OSM, bukan jumlah resmi fasilitas unik (node dan area bisa mewakili lokasi sama).

Ini tahap penampil dan pengelompokan data OSM, sesuai pilihan pengguna. Belum melakukan segmentasi citra atau membuat DSM/objek 3D. Segmentasi atap memerlukan citra bergeoreferensi dan validasi label fungsi bangunan.

## Tes

```powershell
.venv-spasial/Scripts/python.exe -m pip install pytest
.venv-spasial/Scripts/python.exe -m pytest tools/spasial-python/tests -q
```

Sumber: [OpenStreetMap/ODbL](https://www.openstreetmap.org/copyright), [pedoman tag Indonesia](https://wiki.openstreetmap.org/wiki/Indonesian_Tagging_Guidelines). Aplikasi membatasi query ke batas kabupaten, `admin_level=5`, bukan kotak yang mencakup kabupaten tetangga.
