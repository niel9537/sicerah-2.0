# Aplikasi Python Bangunan Publik Nias Barat

Jalankan dari PowerShell pada root proyek:

```powershell
powershell -ExecutionPolicy Bypass -File tools/spasial-python/run.ps1
```

Buka http://127.0.0.1:8501. Data Nias Barat tersimpan dibuka otomatis; gunakan **Buka data Nias Barat tersimpan** untuk memulihkannya atau **Ambil dari Overture Maps** untuk memperbarui. Python 3.10+ diperlukan. Dependensi dipasang pada virtual environment lokal `.venv-spasial`.

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

## Alternatif ketika Overpass timeout

Klik **Buka data Nias Barat tersimpan**. Snapshot nyata dari Overture disertakan di `public/data/gis/nias-barat-buildings.geojson`, otomatis dibuka saat belum ada cache lokal. Tombol **Ambil dari Overture Maps** mengunduh langsung dari penyimpanan Overture, tanpa Overpass. Unggahan GeoJSON mentah dari Overture Explorer dan JSON Overpass juga didukung.

Snapshot rilis 2026-11-18.0, diunduh 24 November 2026: 26.615 tapak bangunan (fungsi belum diketahui), 34 titik pendidikan, 23 titik pemerintah, dan 7 titik kesehatan. Ini jumlah objek sumber, bukan inventaris resmi. Kategori `unclassified` berwarna abu-abu; aplikasi tidak menebak fungsi dari atap.

Seleksi memakai titik representatif bangunan/lokasi fasilitas di dalam gabungan batas desa `public/data/gis/nias-barat-desa.geojson`. Bentuk asli dipertahankan. Bukan segmentasi citra baru. Properti `sources` mempertahankan atribusi dan lisensi masing-masing sumber: [Atribusi Overture](https://docs.overturemaps.org/attribution/). Data facilities berupa titik tetap ditampilkan sebagai titik.

Tiap unduhan baru mengganti dataset aktif setelah berhasil disimpan. Tombol data tersimpan mengembalikan snapshot gabungan. File cache lokal tetap diabaikan Git.
