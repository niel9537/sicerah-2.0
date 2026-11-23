-- ====================================================================
-- SIDELA: SISTEM INFORMASI DESA & LAYANAN APARATUR
-- PEMERINTAH DESA IRAONOGELA, KECAMATAN MANDREHE, KABUPATEN NIAS BARAT
-- INITIAL DATABASE SEED DATA
-- ====================================================================

USE `sidela_db`;

-- 1. ROLES
INSERT INTO `roles` (`id`, `name`, `slug`, `description`) VALUES
(1, 'Administrator Desa', 'admin', 'Pengelola sistem, hak akses, data master dan konfigurasi presensi'),
(2, 'Kepala Desa', 'kepala_desa', 'Pimpinan pemerintah desa, verifikator utama presensi, cuti, dan laporan'),
(3, 'Sekretaris Desa', 'sekretaris_desa', 'Verifikator administrasi, kearsipan surat, aset dan jurnal kerja aparatur'),
(4, 'Perangkat Desa', 'perangkat_desa', 'Kaur dan Kasi pelaksana tugas pemerintahan desa, presensi dan jurnal harian'),
(5, 'Kepala Dusun', 'kadus', 'Aparatur kewilayahan dusun, pendataan penduduk dan laporan lapangan')
ON DUPLICATE KEY UPDATE `name`=VALUES(`name`);

-- 2. DUSUN (HAMLETS) DESA IRAONOGELA
INSERT INTO `hamlets` (`id`, `name`, `code`, `area_sqkm`, `population_count`) VALUES
(1, 'Dusun I Hilimbowo', 'DS-01', 2.15, 412),
(2, 'Dusun II Ononamolo', 'DS-02', 1.85, 385),
(3, 'Dusun III Bawodesolo', 'DS-03', 2.40, 468)
ON DUPLICATE KEY UPDATE `name`=VALUES(`name`);

-- 3. USERS (Default password untuk semua: 'Password123!')
-- BCrypt Hash: $2a$10$wTqKxM8vK9G6H7Y8u9I0Je1k2l3m4n5o6p7q8r9s0t1u2v3w4x5y6
INSERT INTO `users` (`id`, `role_id`, `username`, `email`, `password_hash`, `full_name`, `nik`, `phone`, `avatar_url`, `is_active`) VALUES
(1, 1, 'admin', 'admin@iraonogela.desa.id', '$2a$10$wTqKxM8vK9G6H7Y8u9I0Je1k2l3m4n5o6p7q8r9s0t1u2v3w4x5y6', 'Admin SIDELA Iraonogela', '1225010101900001', '081260000001', 'https://images.unsplash.com/photo-1534528741775-53994a69daeb?w=400', TRUE),
(2, 2, 'kades', 'kades@iraonogela.desa.id', '$2a$10$wTqKxM8vK9G6H7Y8u9I0Je1k2l3m4n5o6p7q8r9s0t1u2v3w4x5y6', 'Baziduhu Gulo', '1225011504780002', '081260000002', 'https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?w=400', TRUE),
(3, 3, 'sekdes', 'sekdes@iraonogela.desa.id', '$2a$10$wTqKxM8vK9G6H7Y8u9I0Je1k2l3m4n5o6p7q8r9s0t1u2v3w4x5y6', 'Faigizatulo Waruwu', '1225012008850003', '081260000003', 'https://images.unsplash.com/photo-1500648767791-00dcc994a43e?w=400', TRUE),
(4, 4, 'kaurkeu', 'keuangan@iraonogela.desa.id', '$2a$10$wTqKxM8vK9G6H7Y8u9I0Je1k2l3m4n5o6p7q8r9s0t1u2v3w4x5y6', 'Yuriman Hia', '1225011102890004', '081260000004', 'https://images.unsplash.com/photo-1494790108377-be9c29b29330?w=400', TRUE),
(5, 4, 'kaurpemerintahan', 'pemerintahan@iraonogela.desa.id', '$2a$10$wTqKxM8vK9G6H7Y8u9I0Je1k2l3m4n5o6p7q8r9s0t1u2v3w4x5y6', 'Ama Dian Gulo', '1225010506910005', '081260000005', 'https://images.unsplash.com/photo-1522075469751-3a6694fb2f61?w=400', TRUE),
(6, 4, 'rachel', 'rachel.kim@iraonogela.desa.id', '$2a$10$wTqKxM8vK9G6H7Y8u9I0Je1k2l3m4n5o6p7q8r9s0t1u2v3w4x5y6', 'Rachel Kim', '1225012601950006', '081260000006', 'https://images.unsplash.com/photo-1544005313-94ddf0286df2?w=400', TRUE),
(7, 5, 'kadus1', 'kadus1@iraonogela.desa.id', '$2a$10$wTqKxM8vK9G6H7Y8u9I0Je1k2l3m4n5o6p7q8r9s0t1u2v3w4x5y6', 'Faomasi Gulo', '1225011212870007', '081260000007', 'https://images.unsplash.com/photo-1506794778202-cad84cf45f1d?w=400', TRUE)
ON DUPLICATE KEY UPDATE `full_name`=VALUES(`full_name`);

-- UPDATE HAMLET HEAD
UPDATE `hamlets` SET `head_of_hamlet_user_id` = 7 WHERE `id` = 1;

-- 4. PERANGKAT DESA DETAIL
INSERT INTO `perangkat_desa` (`user_id`, `nipd`, `jabatan`, `dusun_id`, `alamat`, `tanggal_sk`, `nomor_sk`, `pendidikan_terakhir`, `status_keaktifan`) VALUES
(2, '197804152010011002', 'Kepala Desa', 1, 'Desa Iraonogela RT 01 / RW 01', '2022-01-10', '141/02/SK/KAD/2022', 'S1 Ilmu Pemerintahan', 'AKTIF'),
(3, '198508202012011003', 'Sekretaris Desa', 2, 'Desa Iraonogela RT 02 / RW 01', '2022-01-15', '141/03/SK/KAD/2022', 'S1 Administrasi Publik', 'AKTIF'),
(4, '198902112015012004', 'Kaur Keuangan', 1, 'Desa Iraonogela RT 01 / RW 02', '2022-01-15', '141/04/SK/KAD/2022', 'D3 Akuntansi', 'AKTIF'),
(5, '199106052016011005', 'Kasi Pemerintahan', 3, 'Desa Iraonogela RT 03 / RW 01', '2022-01-15', '141/05/SK/KAD/2022', 'SMA / Sederajat', 'AKTIF'),
(6, '199501262020012006', 'Kaur TU & Umum', 2, 'Desa Iraonogela RT 02 / RW 02', '2022-01-15', '141/06/SK/KAD/2022', 'S1 Manajemen', 'AKTIF'),
(7, '198712122018011007', 'Kepala Dusun I Hilimbowo', 1, 'Dusun I Hilimbowo RT 01', '2022-02-01', '141/07/SK/KAD/2022', 'SMA / Sederajat', 'AKTIF')
ON DUPLICATE KEY UPDATE `jabatan`=VALUES(`jabatan`);

-- 5. FACE PROFILES (Biometric embedding 192-dimension unit vector JSON)
INSERT INTO `face_profiles` (`user_id`, `face_embedding`, `face_image_url`, `is_enrolled`, `model_version`, `is_active`) VALUES
(6, '[0.045, -0.012, 0.089, 0.124, -0.055, 0.032, 0.071, -0.043, 0.098, -0.015, 0.062, -0.081, 0.035, 0.091, -0.024, 0.047, -0.063, 0.058, 0.019, -0.077, 0.034, 0.088, -0.042, 0.061, -0.019, 0.075, -0.033, 0.052, -0.068, 0.044, 0.027, -0.082, 0.039, 0.095, -0.031, 0.066, -0.022, 0.079, -0.045, 0.057, -0.073, 0.048, 0.031, -0.086, 0.042, 0.099, -0.035, 0.070, -0.025, 0.083, -0.049, 0.061, -0.076, 0.051, 0.036, -0.089, 0.046, 0.102, -0.038, 0.074, -0.028, 0.086, -0.052, 0.064, -0.080, 0.055, 0.039, -0.093, 0.049, 0.106, -0.041, 0.077, -0.032, 0.090, -0.056, 0.068, -0.084, 0.058, 0.043, -0.096, 0.053, 0.109, -0.045, 0.081, -0.035, 0.093, -0.059, 0.071, -0.087, 0.062, 0.046, -0.100, 0.056, 0.113, -0.048, 0.084, -0.038, 0.097, -0.063, 0.075, -0.091, 0.065, 0.050, -0.103, 0.060, 0.116, -0.051, 0.088, -0.042, 0.100, -0.066, 0.078, -0.094, 0.069, 0.053, -0.107, 0.063, 0.120, -0.055, 0.091, -0.045, 0.104, -0.070, 0.082, -0.098, 0.072, 0.057, -0.110, 0.067, 0.123, -0.058, 0.095, -0.048, 0.107, -0.073, 0.085, -0.101, 0.076, 0.060, -0.114, 0.070, 0.127, -0.062, 0.098, -0.052, 0.111, -0.077, 0.089, -0.105, 0.079, 0.064, -0.117, 0.074, 0.130, -0.065, 0.102, -0.055, 0.114, -0.080, 0.092, -0.108, 0.083, 0.067, -0.121, 0.077, 0.134, -0.068, 0.105, -0.059, 0.118, -0.084, 0.096, -0.112, 0.086, 0.071, -0.124, 0.081, 0.137, -0.072, 0.109, -0.062, 0.121, -0.087, 0.099, -0.115, 0.090, 0.074, -0.128, 0.084, 0.141, -0.075, 0.112, -0.066, 0.125, -0.091, 0.103]', 'https://images.unsplash.com/photo-1544005313-94ddf0286df2?w=400', TRUE, 'MobileFaceNet-V1-MLKit', TRUE)
ON DUPLICATE KEY UPDATE `is_enrolled`=VALUES(`is_enrolled`);

-- 6. ATTENDANCE SETTINGS
INSERT INTO `attendance_settings` (`id`, `office_name`, `latitude`, `longitude`, `radius_meters`, `work_start_time`, `work_end_time`, `late_tolerance_minutes`, `break_start_time`, `break_end_time`, `is_active`) VALUES
(1, 'Kantor Pemerintah Desa Iraonogela', 1.0425000, 97.5258000, 50, '08:00:00', '16:00:00', 15, '12:00:00', '13:00:00', TRUE)
ON DUPLICATE KEY UPDATE `office_name`=VALUES(`office_name`);

-- 7. ATTENDANCE LOGS (Sesuai mockup tabel riwayat)
INSERT INTO `attendance` (`user_id`, `date`, `clock_in`, `clock_out`, `clock_in_lat`, `clock_in_lon`, `clock_in_distance_meters`, `status`, `late_minutes`, `early_leave_minutes`, `total_work_minutes`, `verification_method`) VALUES
(6, '2026-01-20', '08:25:00', '18:25:00', 1.0425010, 97.5258020, 12.5, 'TERLAMBAT', 25, 0, 540, 'FACE_RECOGNITION'),
(6, '2026-01-21', '08:25:00', '18:25:00', 1.0425005, 97.5258015, 14.2, 'TERLAMBAT', 25, 0, 540, 'FACE_RECOGNITION'),
(6, '2026-01-22', '08:25:00', '18:25:00', 1.0424995, 97.5257990, 11.8, 'TERLAMBAT', 25, 0, 540, 'FACE_RECOGNITION'),
(6, '2026-01-23', '09:00:00', '18:17:00', 1.0425020, 97.5258030, 18.0, 'TERLAMBAT', 60, 0, 497, 'FACE_RECOGNITION'),
(6, CURRENT_DATE(), '08:10:00', NULL, 1.0425000, 97.5258000, 8.5, 'HADIR', 10, 0, 0, 'FACE_RECOGNITION')
ON DUPLICATE KEY UPDATE `clock_in`=VALUES(`clock_in`);

-- 8. LEAVE REQUESTS (Statistik: 25 Pengajuan, 8 Disetujui, 16 Ditolak, 1 Pending)
INSERT INTO `leave_requests` (`id`, `user_id`, `leave_type`, `start_date`, `end_date`, `total_days`, `reason`, `status`, `approved_by`, `approved_at`, `rejection_notes`) VALUES
(1, 6, 'CUTI_TAHUNAN', '2026-01-10', '2026-01-12', 3, 'Keperluan keluarga di Gunungsitoli', 'APPROVED', 2, '2026-01-09 10:00:00', NULL),
(2, 6, 'SAKIT', '2026-01-15', '2026-01-15', 1, 'Demam dan flu (Surat Dokter Terlampir)', 'APPROVED', 2, '2026-01-15 08:30:00', NULL),
(3, 4, 'IZIN', '2026-01-18', '2026-01-18', 1, 'Mengurus administrasi perbankan kas desa', 'APPROVED', 3, '2026-01-17 14:00:00', NULL),
(4, 5, 'DINAS_LUAR', '2026-01-28', '2026-01-29', 2, 'Rapat Koordinasi PMD Kabupaten Nias Barat', 'APPROVED', 2, '2026-01-27 11:00:00', NULL),
(5, 6, 'CUTI_TAHUNAN', '2026-02-02', '2026-02-05', 4, 'Cuti tahunan tahap I', 'REJECTED', 2, '2026-02-01 16:00:00', 'Agenda desa padat pada awal bulan'),
(6, 6, 'IZIN', '2026-02-10', '2026-02-10', 1, 'Urusan mendesak pribadi', 'PENDING', NULL, NULL, NULL)
ON DUPLICATE KEY UPDATE `reason`=VALUES(`reason`);

-- 9. WORK JOURNALS (Sesuai mockup Task cards)
INSERT INTO `work_journals` (`id`, `user_id`, `date`, `title`, `description`, `start_time`, `end_time`, `work_output`, `status`) VALUES
(1, 6, CURRENT_DATE(), 'New Year Brand Campaign Meeting', 'Koordinasi publikasi program pembangunan desa tahun 2026 bersama perangkat desa dan perwakilan dusun', '10:00:00', '11:00:00', 'Notulensi rapat dan timeline publikasi', 'BACKLOG'),
(2, 6, CURRENT_DATE(), 'Meet New Client (Culture Dream)', 'Pertemuan dengan fasilitator program pemberdayaan masyarakat adat dan kebudayaan Nias Barat', '09:00:00', '10:00:00', 'Draft kesepakatan program kolaborasi', 'ON_PROGRESS'),
(3, 6, CURRENT_DATE(), 'Create Moodboard for CityNeo Brand', 'Penyusunan format visual papan informasi publik desa dan media sosial resmi Desa Iraonogela', '09:00:00', '10:00:00', 'Desain infografis APBDes 2026', 'ON_PROGRESS'),
(4, 6, CURRENT_DATE(), 'Luminous Kick-Off Meeting', 'Persiapan pembukaan musyawarah rencana kerja pembangunan desa (RKPDes) tahun berjalan', '13:00:00', '15:00:00', 'Dokumen TOR dan undangan peserta musdes', 'ON_HOLD')
ON DUPLICATE KEY UPDATE `title`=VALUES(`title`);

-- 10. VILLAGE PROFILE (Profil Desa Iraonogela)
INSERT INTO `village_profiles` (`id`, `village_name`, `district`, `regency`, `province`, `postal_code`, `address`, `phone`, `email`, `website`, `logo_url`, `vision`, `mission`) VALUES
(1, 'Iraonogela', 'Mandrehe', 'Nias Barat', 'Sumatera Utara', '22862', 'Jalan Poros Desa Iraonogela, Kec. Mandrehe, Kab. Nias Barat', '081260000001', 'pemdes@iraonogela.desa.id', 'https://iraonogela.desa.id', 'https://upload.wikimedia.org/wikipedia/commons/thumb/d/d4/Lambang_Kabupaten_Nias_Barat.png/400px-Lambang_Kabupaten_Nias_Barat.png', 
'Terwujudnya Desa Iraonogela yang Mandiri, Sejahtera, Transparan, Berbudaya, dan Unggul Berbasis Teknologi Informasi.',
'1. Menyelenggarakan tata kelola pemerintahan desa yang bersih, transparan, akuntabel, dan digital.\n2. Meningkatkan pelayanan publik aparatur desa yang cepat, santun, dan responsif.\n3. Mengembangkan infrastruktur desa yang merata, berkelanjutan, dan berwawasan lingkungan.\n4. Mendorong pemberdayaan ekonomi masyarakat desa melalui pertanian, perikanan, dan UMKM lokal.')
ON DUPLICATE KEY UPDATE `village_name`=VALUES(`village_name`);

-- 11. FAMILIES (Data KK)
INSERT INTO `families` (`id`, `no_kk`, `head_of_family_nik`, `head_of_family_name`, `hamlet_id`, `address`, `rt`, `rw`) VALUES
(1, '1225010101900010', '1225011504780002', 'Baziduhu Gulo', 1, 'Jalan Poros Desa RT 01', '001', '001'),
(2, '1225010101900020', '1225012008850003', 'Faigizatulo Waruwu', 2, 'Dusun II Ononamolo RT 02', '002', '001'),
(3, '1225010101900030', '1225011102890004', 'Yuriman Hia', 1, 'Dusun I Hilimbowo RT 01', '001', '001')
ON DUPLICATE KEY UPDATE `head_of_family_name`=VALUES(`head_of_family_name`);

-- 12. RESIDENTS (Data Penduduk)
INSERT INTO `residents` (`id`, `family_id`, `hamlet_id`, `nik`, `nama_lengkap`, `jenis_kelamin`, `tempat_lahir`, `tanggal_lahir`, `agama`, `pendidikan`, `pekerjaan`, `status_perkawinan`, `hubungan_keluarga`, `status_hidup`) VALUES
(1, 1, 1, '1225011504780002', 'Baziduhu Gulo', 'L', 'Iraonogela', '1978-04-15', 'KRISTEN_PROTESTAN', 'S1', 'KEPALA DESA', 'KAWIN', 'KEPALA_KELUARGA', 'HIDUP'),
(2, 1, 1, '1225015005820001', 'Yuliana Daeli', 'P', 'Mandrehe', '1982-05-10', 'KRISTEN_PROTESTAN', 'S1', 'GURU', 'KAWIN', 'ISTRI', 'HIDUP'),
(3, 2, 2, '1225012008850003', 'Faigizatulo Waruwu', 'L', 'Iraonogela', '1985-08-20', 'KRISTEN_PROTESTAN', 'S1', 'SEKRETARIS DESA', 'KAWIN', 'KEPALA_KELUARGA', 'HIDUP'),
(4, 3, 1, '1225011102890004', 'Yuriman Hia', 'L', 'Hilimbowo', '1989-02-11', 'KRISTEN_PROTESTAN', 'D3', 'PERANGKAT DESA', 'KAWIN', 'KEPALA_KELUARGA', 'HIDUP'),
(5, NULL, 3, '1225012601950006', 'Rachel Kim Gulo', 'P', 'Iraonogela', '1995-01-26', 'KRISTEN_PROTESTAN', 'S1', 'PERANGKAT DESA', 'BELUM_KAWIN', 'KEPALA_KELUARGA', 'HIDUP')
ON DUPLICATE KEY UPDATE `nama_lengkap`=VALUES(`nama_lengkap`);

-- 13. VILLAGE ASSETS (Aset Desa)
INSERT INTO `village_assets` (`asset_code`, `asset_name`, `category`, `acquisition_year`, `source_of_funds`, `acquisition_cost`, `quantity`, `unit`, `condition`, `location`, `person_in_charge`) VALUES
('AST-2021-001', 'Gedung Balai & Kantor Desa Iraonogela', 'BANGUNAN', 2021, 'Dana Desa (APBDes)', 350000000.00, 1, 'Unit', 'BAIK', 'Jalan Poros Desa Iraonogela', 'Baziduhu Gulo'),
('AST-2022-002', 'Sepeda Motor Dinas Honda CRF 150L', 'KENDARAAN', 2022, 'Dana Desa (APBDes)', 36500000.00, 1, 'Unit', 'BAIK', 'Kantor Desa Iraonogela', 'Faigizatulo Waruwu'),
('AST-2023-003', 'Laptop Asus ExpertBook Core i5 (Kaur Keu)', 'PERALATAN_KANTOR', 2023, 'Dana Desa (APBDes)', 12800000.00, 1, 'Unit', 'BAIK', 'Ruang Tata Usaha', 'Yuriman Hia'),
('AST-2023-004', 'Printer Epson EcoTank L3210 All-in-One', 'PERALATAN_KANTOR', 2023, 'Dana Desa (APBDes)', 2950000.00, 2, 'Unit', 'BAIK', 'Ruang Pelayanan', 'Rachel Kim'),
('AST-2024-005', 'Genset Silent Perkins 5 KVA', 'PERALATAN_KANTOR', 2024, 'Dana Desa (APBDes)', 15000000.00, 1, 'Unit', 'BAIK', 'Gudang Kantor Desa', 'Ama Dian Gulo')
ON DUPLICATE KEY UPDATE `asset_name`=VALUES(`asset_name`);

-- 14. INCOMING & OUTGOING LETTERS (Arsip Surat)
INSERT INTO `incoming_letters` (`letter_number`, `letter_date`, `received_date`, `sender`, `subject`, `summary`, `status`) VALUES
('005/124/PMD/2026', '2026-01-15', '2026-01-16', 'Dinas PMD Kab. Nias Barat', 'Undangan Rapat Koordinasi Penyaluran BLT Dana Desa TA 2026', 'Rakor dijadwalkan pada hari Selasa di Kantor Bupati Nias Barat', 'DIDISPOSISIKAN'),
('141/045/KEC-MDR/2026', '2026-01-20', '2026-01-21', 'Camat Mandrehe', 'Verifikasi Dokumen Rancangan APBDes 2026', 'Penyempurnaan dokumen anggaran belanja pembangunan dusun', 'DITINDAKLANJUTI')
ON DUPLICATE KEY UPDATE `subject`=VALUES(`subject`);

INSERT INTO `outgoing_letters` (`letter_number`, `letter_date`, `recipient`, `subject`, `summary`, `status`) VALUES
('141/01/PEM-IRG/2026', '2026-01-18', 'Camat Mandrehe', 'Laporan Realisasi APBDes Akhir Tahun TA 2025', 'Penyampaian pertanggungjawaban realisasi fisik dan keuangan desa', 'TERKIRIM'),
('141/02/PEM-IRG/2026', '2026-01-25', 'BPD Desa Iraonogela', 'Penyampaian Rancangan Peraturan Desa tentang RKPDes 2026', 'Konsultasi dan pembahasan bersama Badan Permusyawaratan Desa', 'TERKIRIM')
ON DUPLICATE KEY UPDATE `subject`=VALUES(`subject`);

-- 15. NOTIFICATIONS
INSERT INTO `notifications` (`user_id`, `title`, `message`, `type`, `is_read`) VALUES
(6, 'Presensi Berhasil', 'Presensi masuk Anda pada tanggal 26 Jan 2026 pukul 08:25 berhasil dicatat.', 'ATTENDANCE', FALSE),
(6, 'Pengajuan Cuti Disetujui', 'Pengajuan Cuti Tahunan Anda telah disetujui oleh Kepala Desa.', 'LEAVE', TRUE),
(6, 'Pengingat Jurnal Kerja', 'Jangan lupa mengisi jurnal aktivitas harian sebelum pukul 17:00 WIB.', 'JOURNAL', FALSE)
ON DUPLICATE KEY UPDATE `title`=VALUES(`title`);
