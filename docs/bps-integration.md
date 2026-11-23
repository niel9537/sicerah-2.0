# Integrasi BPS SICERAH

Menu publik `/bps` menggunakan WebAPI BPS dan domain Nias Barat `1225`.
Kategori: tabel statistik, publikasi, dan indikator strategis. Detail tabel dirender sebagai sel teks yang di-escape; HTML BPS tidak dieksekusi.

## Konfigurasi server

Tambahkan pada `.env` hosting (jangan commit nilai kunci):

```dotenv
BPS_API_KEY=isi_kunci_dari_akun_BPS
BPS_DOMAIN=1225
BPS_CACHE_STORE=file
```

Untuk komputer lokal, kunci juga dapat disimpan sebagai teks pada `storage/app/private/bps-api-key` yang dikecualikan dari Git. Nilai `BPS_API_KEY` didahulukan jika tersedia. File lokal ini tidak ikut deploy.

Jika konfigurasi Laravel dicache, jalankan `php artisan config:cache` setelah mengganti kunci. Pastikan direktori cache Laravel dapat ditulis. API dipanggil di server dengan batas waktu 18 detik, tanpa redirect. Hasil sukses disimpan satu jam. Halaman mencantumkan waktu pengambilan, bukan menganggap semua data merupakan data tahun berjalan.

Jangan menyalin URL request yang memuat `/key/` ke log, kode frontend, dokumentasi publik, atau Git. Download dan gambar hanya mengizinkan HTTPS pada domain BPS, tanpa kunci API. Error upstream ditampilkan menggunakan pesan umum.

Spesifikasi: lampiran OpenAPI pengguna. Dokumentasi resmi: https://webapi.bps.go.id/documentation/

Jika PHP lokal membutuhkan CA bundle, gunakan `BPS_CA_BUNDLE` berisi path berkas PEM tepercaya, atau `storage/app/private/bps-ca-bundle.pem` (diabaikan Git). Pemeriksaan TLS tetap aktif; pada hosting dengan CA sistem yang sesuai tidak diperlukan pengaturan tambahan.
