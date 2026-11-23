# Indikator RPJMD

## Visi, misi, dan tujuan publik

Navbar publik **RPJMD** membuka `/rpjmd/visi-misi`. Beranda dan halaman ini memakai komponen hierarki yang sama: satu visi serta lima kartu pasangan misi dan tujuan, berdampingan pada desktop dan bertumpuk pada ponsel.

Super admin dapat mengedit melalui **CMS Portal → Kelola RPJMD** (`/dashboard/cms/rpjmd`). Form wajib berisi tepat lima pasangan. Data awal ada di `resources/data/rpjmd.json`, bersumber dari `rpjmd_Tabel_3.5.xls` (Tabel 3.5, Final RPJMD). Misi mengikuti urutan dalam kolom visi/misi; tujuan dipasangkan berdasarkan bidangnya karena urutan tujuan dalam tabel berbeda.

Perubahan CMS disimpan secara atomik di `storage/app/private/rpjmd-content.json`; sertakan file ini dalam backup dan pertahankan direktori storage saat deployment. Tidak memerlukan migrasi atau perubahan data indikator.

Hierarki dilengkapi 11 sasaran, 60 indikator dan target 2025–2030 dari `resources/data/rpjmd-hierarchy.json`. Kelompok tanpa sasaran adalah indikator tujuan. Beranda dan halaman RPJMD menampilkan seluruh rincian ini.

`RpjmdHierarchy` mencocokkan nama dengan tabel `indikator` tanpa menghapus kualifikasi substantif. Duplikat dipilih dengan prioritas IKD, kemudian IKU/IKK, sumber daerah, Bank Data, dan ID terkecil. Admin dapat mengganti tautan per baris sumber melalui pilihan indikator pada CMS; ID pilihan disimpan dalam `links`. Target ditarik langsung dari `indikator_target`, lalu target sumber RPJMD daerah jika nilainya tunggal, kemudian target Tabel 3.5. Nilai nol tetap valid. Nilai asli Tabel 3.5 selalu dipertahankan dan ditampilkan saat berbeda. Tautan detail publik memakai ID indikator dan menampilkan semua sumber target tanpa mencampur target perangkat daerah.

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
