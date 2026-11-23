# Indikator RPJMD

Menu **RPJMD dan Indikator → RPJMD 2025–2030** (`/rpjmd`) tersedia untuk super admin dan admin Bappeda.

Sinkronisasi lima file asli:

```powershell
php artisan migrate --force
php artisan rpjmd:import 'D:\Akhari\ARK-00001\SICERAH\RPJMD'
```

Impor membaca seluruh file sebelum transaksi. Nama dicocokkan per jenis dengan normalisasi kapitalisasi dan spasi. Target daerah menjadi acuan target bersama; perbedaan target per perangkat daerah tetap disimpan pada baris sumber. Baseline 2024 tidak dijadikan realisasi triwulan. Nilai kosong tidak menghapus target lama; teks rentang, nol, status arah IKK, dan keterangan dipertahankan. Nama sama yang muncul berulang tetap memiliki rincian sumber terpisah. Realisasi dan status verifikasi tidak diubah oleh importer.

Duplikat lama tidak dihapus: importer mengutamakan baris Bank Data lalu ID terkecil, dan melaporkan ID duplikat. OPD dicocokkan berdasarkan kode lengkap atau nama yang dinormalisasi. Nama OPD yang belum tersedia tetap tersimpan dalam rincian sumber dan dilaporkan. Satu indikator dapat memiliki beberapa penanggung jawab pada rincian sumber; kolom OPD tunggal lama hanya diisi bila kosong.

Hasil impor lokal: 335 nama IKD, 132 nama IKK, 12 IKU, 1.065 baris sumber. Laporan terakhir: `storage/logs/rpjmd-import-report.json`. Tujuh kantor kecamatan belum ada dalam master OPD.

Pengujian importer wajib menggunakan database khusus berakhiran `_test`:

```powershell
$env:DB_DATABASE='sicerah_rpjmd_test'
php vendor/phpunit/phpunit/phpunit --filter=RpjmdIndicatorImporterTest
```

Catatan pemulihan sesi: pengujian awal sempat menggunakan database aplikasi. Data dipulihkan dari `bappeli4_sicerah123.sql` (dump 7 November 2026, 20:50), migrasi diterapkan ulang, kemudian Bank Data dan lima file RPJMD diimpor kembali. Perubahan data setelah waktu dump yang tidak tersedia dalam dokumen sumber belum dapat dipastikan pulih. Tes baru menolak database tanpa akhiran `_test`.
