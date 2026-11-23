<?php

namespace Database\Seeders;

use App\Models\User;
use App\Services\BankDataIndicatorImporter;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // -------- OPD --------
        DB::table('opd')->insert([
            ['id' => 1, 'kode' => '1.01', 'nama' => 'Dinas Pendidikan'],
            ['id' => 2, 'kode' => '1.02', 'nama' => 'Dinas Kesehatan'],
            ['id' => 3, 'kode' => '1.03', 'nama' => 'Dinas PUPR'],
            ['id' => 4, 'kode' => '5.01', 'nama' => 'Badan Perencanaan Pembangunan, Penelitian dan Pengembangan'],
        ]);

        // -------- Urusan --------
        DB::table('urusan')->insert([
            ['id' => 1, 'kode' => '1', 'nama' => 'Pendidikan'],
            ['id' => 2, 'kode' => '2', 'nama' => 'Kesehatan'],
            ['id' => 3, 'kode' => '3', 'nama' => 'Pekerjaan Umum'],
            ['id' => 4, 'kode' => '5', 'nama' => 'Perencanaan'],
        ]);

        // -------- Sasaran RPJMD --------
        DB::table('sasaran_rpjmd')->insert([
            ['id' => 1, 'kode' => 'SR01', 'nama' => 'Meningkatkan kualitas pelayanan publik', 'periode_awal' => 2025, 'periode_akhir' => 2030],
        ]);

        // -------- Tujuan PD --------
        DB::table('tujuan_pd')->insert([
            ['id' => 1, 'opd_id' => 1, 'sasaran_rpjmd_id' => 1, 'kode' => 'TP01', 'nama' => 'Meningkatkan mutu pendidikan',   'tahun_awal' => 2025, 'tahun_akhir' => 2030],
            ['id' => 2, 'opd_id' => 2, 'sasaran_rpjmd_id' => 1, 'kode' => 'TP02', 'nama' => 'Meningkatkan pelayanan kesehatan', 'tahun_awal' => 2025, 'tahun_akhir' => 2030],
            ['id' => 3, 'opd_id' => 3, 'sasaran_rpjmd_id' => 1, 'kode' => 'TP03', 'nama' => 'Meningkatkan infrastruktur jalan', 'tahun_awal' => 2025, 'tahun_akhir' => 2030],
        ]);

        // -------- Sasaran PD --------
        DB::table('sasaran_pd')->insert([
            ['id' => 1, 'tujuan_pd_id' => 1, 'kode' => 'SP01', 'nama' => 'Naiknya angka partisipasi sekolah',     'tahun_awal' => 2025, 'tahun_akhir' => 2030],
            ['id' => 2, 'tujuan_pd_id' => 2, 'kode' => 'SP02', 'nama' => 'Naiknya derajat kesehatan masyarakat',  'tahun_awal' => 2025, 'tahun_akhir' => 2030],
            ['id' => 3, 'tujuan_pd_id' => 3, 'kode' => 'SP03', 'nama' => 'Meningkatnya kualitas jalan',           'tahun_awal' => 2025, 'tahun_akhir' => 2030],
        ]);

        // -------- Program --------
        DB::table('program')->insert([
            ['id' => 1, 'urusan_id' => 1, 'opd_id' => 1, 'sasaran_pd_id' => 1, 'kode' => '1.01.01', 'nama' => 'Program Pengelolaan Pendidikan'],
            ['id' => 2, 'urusan_id' => 2, 'opd_id' => 2, 'sasaran_pd_id' => 2, 'kode' => '1.02.01', 'nama' => 'Program Pelayanan Kesehatan'],
            ['id' => 3, 'urusan_id' => 3, 'opd_id' => 3, 'sasaran_pd_id' => 3, 'kode' => '1.03.01', 'nama' => 'Program Pembangunan Jalan'],
        ]);

        // -------- Kegiatan --------
        DB::table('kegiatan')->insert([
            ['id' => 1, 'program_id' => 1, 'kode' => '1.01.01.01', 'nama' => 'Pengelolaan Sekolah Dasar'],
            ['id' => 2, 'program_id' => 2, 'kode' => '1.02.01.01', 'nama' => 'Pelayanan Puskesmas'],
            ['id' => 3, 'program_id' => 3, 'kode' => '1.03.01.01', 'nama' => 'Pembangunan Jalan Kabupaten'],
        ]);

        // -------- Sub Kegiatan --------
        DB::table('subkegiatan')->insert([
            ['id' => 1, 'kegiatan_id' => 1, 'kode' => '1.01.01.01.001', 'nama' => 'Penyediaan Sarana SD',      'indikator' => 'Jumlah SD menerima bantuan', 'satuan' => 'Unit'],
            ['id' => 2, 'kegiatan_id' => 1, 'kode' => '1.01.01.01.002', 'nama' => 'Pelatihan Guru',            'indikator' => 'Jumlah Guru dilatih',        'satuan' => 'Orang'],
            ['id' => 3, 'kegiatan_id' => 2, 'kode' => '1.02.01.01.001', 'nama' => 'Pelayanan Rawat Jalan',     'indikator' => 'Jumlah Pasien',              'satuan' => 'Orang'],
            ['id' => 4, 'kegiatan_id' => 2, 'kode' => '1.02.01.01.002', 'nama' => 'Imunisasi Balita',          'indikator' => 'Jumlah Balita',              'satuan' => 'Anak'],
            ['id' => 5, 'kegiatan_id' => 3, 'kode' => '1.03.01.01.001', 'nama' => 'Pembangunan Jalan Aspal',   'indikator' => 'Panjang Jalan',              'satuan' => 'Km'],
            ['id' => 6, 'kegiatan_id' => 3, 'kode' => '1.03.01.01.002', 'nama' => 'Pemeliharaan Jalan',        'indikator' => 'Panjang Jalan',              'satuan' => 'Km'],
        ]);

        // -------- Periode 2025 & 2026 --------
        $periode = [];
        foreach ([2025, 2026] as $tahun) {
            foreach ([1, 2, 3, 4] as $tw) {
                $periode[] = [
                    'tahun'    => $tahun,
                    'triwulan' => $tw,
                    'nama'     => "Triwulan {$tw} {$tahun}",
                    'status'   => ($tahun === 2026 && $tw === 4) ? 'aktif' : 'aktif',
                ];
            }
        }
        DB::table('periode')->insert($periode);

        // -------- Target tahunan 2026 --------
        DB::table('target_tahunan')->insert([
            ['subkegiatan_id' => 1, 'tahun' => 2026, 'target_kinerja' => 20,   'pagu_renstra' => 500000000,  'pagu_renja' => 500000000],
            ['subkegiatan_id' => 2, 'tahun' => 2026, 'target_kinerja' => 100,  'pagu_renstra' => 150000000,  'pagu_renja' => 150000000],
            ['subkegiatan_id' => 3, 'tahun' => 2026, 'target_kinerja' => 5000, 'pagu_renstra' => 800000000,  'pagu_renja' => 800000000],
            ['subkegiatan_id' => 4, 'tahun' => 2026, 'target_kinerja' => 3500, 'pagu_renstra' => 250000000,  'pagu_renja' => 250000000],
            ['subkegiatan_id' => 5, 'tahun' => 2026, 'target_kinerja' => 12,   'pagu_renstra' => 2500000000, 'pagu_renja' => 2500000000],
            ['subkegiatan_id' => 6, 'tahun' => 2026, 'target_kinerja' => 30,   'pagu_renstra' => 1200000000, 'pagu_renja' => 1200000000],
        ]);

        // -------- Realisasi triwulanan 2026 --------
        $data = [
            [1, 1, 4,    80000000],   [2, 1, 20,   25000000],
            [3, 1, 1200, 150000000],  [4, 1, 900,  50000000],
            [5, 1, 2,    400000000],  [6, 1, 6,    180000000],
            [1, 2, 10,   200000000],  [2, 2, 55,   70000000],
            [3, 2, 2500, 350000000],  [4, 2, 1800, 100000000],
            [5, 2, 5,    900000000],  [6, 2, 12,   450000000],
            [1, 3, 15,   350000000],  [2, 3, 80,   110000000],
            [3, 3, 3700, 520000000],  [4, 3, 2600, 170000000],
            [5, 3, 9,    1700000000], [6, 3, 20,   800000000],
            [1, 4, 20,   500000000],  [2, 4, 100,  150000000],
            [3, 4, 5000, 800000000],  [4, 4, 3500, 250000000],
            [5, 4, 12,   2500000000], [6, 4, 30,   1200000000],
        ];

        foreach ($data as $row) {
            DB::table('realisasi_triwulanan')->insert([
                'subkegiatan_id'      => $row[0],
                'tahun'               => 2026,
                'triwulan'            => $row[1],
                'realisasi_kinerja'   => $row[2],
                'realisasi_anggaran'  => $row[3],
                'status'              => 'diverifikasi',
            ]);
        }

        // -------- Users default --------
        User::create([
            'nama'     => 'Super Administrator',
            'email'    => 'admin@sicerah.test',
            'password' => Hash::make('password'),
            'role'     => User::ROLE_SUPER_ADMIN,
        ]);
        User::create([
            'nama'     => 'Admin Bappeda',
            'email'    => 'bappeda@sicerah.test',
            'password' => Hash::make('password'),
            'role'     => User::ROLE_ADMIN_BAPPEDA,
            'opd_id'   => 4,
        ]);
        User::create([
            'nama'     => 'Operator Dinas Pendidikan',
            'email'    => 'operator@sicerah.test',
            'password' => Hash::make('password'),
            'role'     => User::ROLE_OPERATOR_OPD,
            'opd_id'   => 1,
        ]);

        $this->call(GisSeeder::class);

        $bankDataPath = storage_path('app/imports/Bank Data.xlsx');
        if (is_file($bankDataPath)) {
            app(BankDataIndicatorImporter::class)->import($bankDataPath);
        }
    }
}
