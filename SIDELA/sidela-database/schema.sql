-- ====================================================================
-- SIDELA: SISTEM INFORMASI DESA & LAYANAN APARATUR
-- PEMERINTAH DESA IRAONOGELA, KECAMATAN MANDREHE, KABUPATEN NIAS BARAT
-- DATABASE SCHEMA (MySQL 8.0+ / MariaDB 10.5+)
-- ====================================================================

CREATE DATABASE IF NOT EXISTS `sidela_db` 
CHARACTER SET utf8mb4 
COLLATE utf8mb4_unicode_ci;

USE `sidela_db`;

-- SET FOREIGN_KEY_CHECKS = 0;

-- --------------------------------------------------------------------
-- 1. TABEL ROLES (Hak Akses)
-- --------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `roles` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `name` VARCHAR(50) NOT NULL UNIQUE,
    `slug` VARCHAR(50) NOT NULL UNIQUE,
    `description` VARCHAR(255) NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------------------
-- 2. TABEL USERS (Akun Pengguna)
-- --------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `users` (
    `id` BIGINT AUTO_INCREMENT PRIMARY KEY,
    `role_id` INT NOT NULL,
    `username` VARCHAR(60) NOT NULL UNIQUE,
    `email` VARCHAR(100) NOT NULL UNIQUE,
    `password_hash` VARCHAR(255) NOT NULL,
    `full_name` VARCHAR(120) NOT NULL,
    `nik` VARCHAR(16) NOT NULL UNIQUE,
    `phone` VARCHAR(20) NULL,
    `avatar_url` VARCHAR(255) NULL,
    `is_active` BOOLEAN DEFAULT TRUE,
    `must_change_password` BOOLEAN DEFAULT FALSE,
    `last_login_at` TIMESTAMP NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    `deleted_at` TIMESTAMP NULL,
    CONSTRAINT `fk_users_role` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE RESTRICT,
    INDEX `idx_users_username` (`username`),
    INDEX `idx_users_email` (`email`),
    INDEX `idx_users_nik` (`nik`),
    INDEX `idx_users_is_active` (`is_active`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------------------
-- 3. TABEL HAMLETS (Wilayah Dusun)
-- --------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `hamlets` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `name` VARCHAR(100) NOT NULL,
    `code` VARCHAR(20) NOT NULL UNIQUE,
    `head_of_hamlet_user_id` BIGINT NULL,
    `area_sqkm` DECIMAL(6,2) DEFAULT 0.00,
    `population_count` INT DEFAULT 0,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT `fk_hamlets_head` FOREIGN KEY (`head_of_hamlet_user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------------------
-- 4. TABEL PERANGKAT_DESA (Data Aparatur Desa)
-- --------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `perangkat_desa` (
    `id` BIGINT AUTO_INCREMENT PRIMARY KEY,
    `user_id` BIGINT NOT NULL UNIQUE,
    `nipd` VARCHAR(30) NULL UNIQUE,
    `jabatan` VARCHAR(100) NOT NULL,
    `dusun_id` INT NULL,
    `alamat` TEXT NULL,
    `tanggal_sk` DATE NULL,
    `nomor_sk` VARCHAR(100) NULL,
    `file_sk_url` VARCHAR(255) NULL,
    `pendidikan_terakhir` VARCHAR(50) NULL,
    `status_keaktifan` ENUM('AKTIF', 'CUTI', 'NONAKTIF', 'MUTASI') DEFAULT 'AKTIF',
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    `deleted_at` TIMESTAMP NULL,
    CONSTRAINT `fk_perangkat_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
    CONSTRAINT `fk_perangkat_dusun` FOREIGN KEY (`dusun_id`) REFERENCES `hamlets` (`id`) ON DELETE SET NULL,
    INDEX `idx_perangkat_jabatan` (`jabatan`),
    INDEX `idx_perangkat_status` (`status_keaktifan`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------------------
-- 5. TABEL FACE_PROFILES (Data Biometrik Wajah)
-- --------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `face_profiles` (
    `id` BIGINT AUTO_INCREMENT PRIMARY KEY,
    `user_id` BIGINT NOT NULL UNIQUE,
    `face_embedding` LONGTEXT NOT NULL COMMENT 'JSON array of 192/512 float embeddings',
    `face_image_url` VARCHAR(255) NOT NULL,
    `is_enrolled` BOOLEAN DEFAULT TRUE,
    `enrolled_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `last_verified_at` TIMESTAMP NULL,
    `model_version` VARCHAR(50) DEFAULT 'MobileFaceNet-V1-MLKit',
    `reset_count` INT DEFAULT 0,
    `last_reset_by` BIGINT NULL,
    `last_reset_at` TIMESTAMP NULL,
    `is_active` BOOLEAN DEFAULT TRUE,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT `fk_face_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
    CONSTRAINT `fk_face_reset_admin` FOREIGN KEY (`last_reset_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
    INDEX `idx_face_enrolled` (`is_enrolled`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------------------
-- 6. TABEL ATTENDANCE_SETTINGS (Pengaturan Jam & Radius Kantor)
-- --------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `attendance_settings` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `office_name` VARCHAR(150) NOT NULL DEFAULT 'Kantor Pemerintah Desa Iraonogela',
    `latitude` DECIMAL(10, 7) NOT NULL DEFAULT 1.0425000,
    `longitude` DECIMAL(10, 7) NOT NULL DEFAULT 97.5258000,
    `radius_meters` INT NOT NULL DEFAULT 50,
    `work_start_time` TIME NOT NULL DEFAULT '08:00:00',
    `work_end_time` TIME NOT NULL DEFAULT '16:00:00',
    `late_tolerance_minutes` INT NOT NULL DEFAULT 15,
    `break_start_time` TIME DEFAULT '12:00:00',
    `break_end_time` TIME DEFAULT '13:00:00',
    `is_active` BOOLEAN DEFAULT TRUE,
    `updated_by` BIGINT NULL,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT `fk_att_setting_user` FOREIGN KEY (`updated_by`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------------------
-- 7. TABEL ATTENDANCE (Presensi Harian)
-- --------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `attendance` (
    `id` BIGINT AUTO_INCREMENT PRIMARY KEY,
    `user_id` BIGINT NOT NULL,
    `date` DATE NOT NULL,
    `clock_in` TIME NULL,
    `clock_out` TIME NULL,
    `clock_in_lat` DECIMAL(10,7) NULL,
    `clock_in_lon` DECIMAL(10,7) NULL,
    `clock_in_distance_meters` DECIMAL(8,2) NULL,
    `clock_in_photo_url` VARCHAR(255) NULL,
    `clock_out_lat` DECIMAL(10,7) NULL,
    `clock_out_lon` DECIMAL(10,7) NULL,
    `clock_out_distance_meters` DECIMAL(8,2) NULL,
    `clock_out_photo_url` VARCHAR(255) NULL,
    `face_confidence_in` DECIMAL(5,2) NULL,
    `face_confidence_out` DECIMAL(5,2) NULL,
    `liveness_passed_in` BOOLEAN DEFAULT TRUE,
    `liveness_passed_out` BOOLEAN DEFAULT TRUE,
    `status` ENUM('HADIR', 'TERLAMBAT', 'PULANG_CEPAT', 'ALPHA', 'IZIN', 'SAKIT', 'CUTI', 'DINAS_LUAR') NOT NULL DEFAULT 'ALPHA',
    `late_minutes` INT DEFAULT 0,
    `early_leave_minutes` INT DEFAULT 0,
    `total_work_minutes` INT DEFAULT 0,
    `verification_method` ENUM('FACE_RECOGNITION', 'MANUAL_APPROVAL', 'ADMIN_OVERRIDE') DEFAULT 'FACE_RECOGNITION',
    `verified_by` BIGINT NULL,
    `notes` TEXT NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT `fk_att_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
    CONSTRAINT `fk_att_verifier` FOREIGN KEY (`verified_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
    UNIQUE KEY `uk_user_date` (`user_id`, `date`),
    INDEX `idx_att_date` (`date`),
    INDEX `idx_att_status` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------------------
-- 8. TABEL LEAVE_REQUESTS (Cuti, Izin, Sakit, & Dinas Luar)
-- --------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `leave_requests` (
    `id` BIGINT AUTO_INCREMENT PRIMARY KEY,
    `user_id` BIGINT NOT NULL,
    `leave_type` ENUM('CUTI_TAHUNAN', 'CUTI_MELAHIRKAN', 'IZIN', 'SAKIT', 'DINAS_LUAR') NOT NULL,
    `start_date` DATE NOT NULL,
    `end_date` DATE NOT NULL,
    `total_days` INT NOT NULL DEFAULT 1,
    `reason` TEXT NOT NULL,
    `attachment_url` VARCHAR(255) NULL,
    `destination_location` VARCHAR(150) NULL COMMENT 'Untuk Dinas Luar',
    `official_duty_number` VARCHAR(100) NULL COMMENT 'Nomor Surat Tugas/SPT',
    `status` ENUM('PENDING', 'APPROVED', 'REJECTED') DEFAULT 'PENDING',
    `approved_by` BIGINT NULL,
    `approved_at` TIMESTAMP NULL,
    `rejection_notes` TEXT NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT `fk_leave_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
    CONSTRAINT `fk_leave_approver` FOREIGN KEY (`approved_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
    INDEX `idx_leave_status` (`status`),
    INDEX `idx_leave_type` (`leave_type`),
    INDEX `idx_leave_dates` (`start_date`, `end_date`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------------------
-- 9. TABEL OFFICIAL_TRIPS (Detail Surat Perintah Tugas / Dinas Luar)
-- --------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `official_trips` (
    `id` BIGINT AUTO_INCREMENT PRIMARY KEY,
    `user_id` BIGINT NOT NULL,
    `leave_request_id` BIGINT NOT NULL,
    `spt_number` VARCHAR(100) NOT NULL UNIQUE,
    `trip_title` VARCHAR(200) NOT NULL,
    `destination` VARCHAR(200) NOT NULL,
    `departure_date` DATE NOT NULL,
    `return_date` DATE NOT NULL,
    `transportation_type` VARCHAR(100) DEFAULT 'Kendaraan Dinas / Roda Dua',
    `budget_source` VARCHAR(100) DEFAULT 'Dana Desa (APBDes)',
    `task_report` TEXT NULL,
    `report_attachment_url` VARCHAR(255) NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT `fk_trip_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
    CONSTRAINT `fk_trip_leave` FOREIGN KEY (`leave_request_id`) REFERENCES `leave_requests` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------------------
-- 10. TABEL WORK_JOURNALS (Jurnal Kerja Harian Aparatur)
-- --------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `work_journals` (
    `id` BIGINT AUTO_INCREMENT PRIMARY KEY,
    `user_id` BIGINT NOT NULL,
    `date` DATE NOT NULL,
    `title` VARCHAR(200) NOT NULL,
    `description` TEXT NOT NULL,
    `start_time` TIME NOT NULL,
    `end_time` TIME NOT NULL,
    `work_output` VARCHAR(255) NOT NULL,
    `status` ENUM('BACKLOG', 'ON_PROGRESS', 'COMPLETED', 'ON_HOLD') DEFAULT 'ON_PROGRESS',
    `feedback_notes` TEXT NULL,
    `verified_by` BIGINT NULL,
    `verified_at` TIMESTAMP NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT `fk_journal_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
    CONSTRAINT `fk_journal_verifier` FOREIGN KEY (`verified_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
    INDEX `idx_journal_date` (`date`),
    INDEX `idx_journal_status` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------------------
-- 11. TABEL JOURNAL_ATTACHMENTS (Lampiran Bukti Kerja Jurnal)
-- --------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `journal_attachments` (
    `id` BIGINT AUTO_INCREMENT PRIMARY KEY,
    `journal_id` BIGINT NOT NULL,
    `file_name` VARCHAR(255) NOT NULL,
    `file_url` VARCHAR(255) NOT NULL,
    `file_type` VARCHAR(50) NOT NULL,
    `file_size_bytes` BIGINT NOT NULL DEFAULT 0,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT `fk_att_journal` FOREIGN KEY (`journal_id`) REFERENCES `work_journals` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------------------
-- 12. TABEL VILLAGE_PROFILES (Profil Pemerintahan Desa)
-- --------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `village_profiles` (
    `id` INT PRIMARY KEY DEFAULT 1,
    `village_name` VARCHAR(100) NOT NULL DEFAULT 'Iraonogela',
    `district` VARCHAR(100) NOT NULL DEFAULT 'Mandrehe',
    `regency` VARCHAR(100) NOT NULL DEFAULT 'Nias Barat',
    `province` VARCHAR(100) NOT NULL DEFAULT 'Sumatera Utara',
    `postal_code` VARCHAR(10) NOT NULL DEFAULT '22862',
    `address` TEXT NOT NULL,
    `phone` VARCHAR(25) NULL,
    `email` VARCHAR(100) NULL,
    `website` VARCHAR(150) NULL,
    `logo_url` VARCHAR(255) NULL,
    `vision` TEXT NULL,
    `mission` TEXT NULL,
    `organizational_structure_url` VARCHAR(255) NULL,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------------------
-- 13. TABEL FAMILIES (Data Kartu Keluarga)
-- --------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `families` (
    `id` BIGINT AUTO_INCREMENT PRIMARY KEY,
    `no_kk` VARCHAR(16) NOT NULL UNIQUE,
    `head_of_family_nik` VARCHAR(16) NOT NULL,
    `head_of_family_name` VARCHAR(120) NOT NULL,
    `hamlet_id` INT NOT NULL,
    `address` TEXT NOT NULL,
    `rt` VARCHAR(5) DEFAULT '001',
    `rw` VARCHAR(5) DEFAULT '001',
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT `fk_fam_hamlet` FOREIGN KEY (`hamlet_id`) REFERENCES `hamlets` (`id`) ON DELETE RESTRICT,
    INDEX `idx_fam_nokk` (`no_kk`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------------------
-- 14. TABEL RESIDENTS (Data Kependudukan Desa)
-- --------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `residents` (
    `id` BIGINT AUTO_INCREMENT PRIMARY KEY,
    `family_id` BIGINT NULL,
    `hamlet_id` INT NOT NULL,
    `nik` VARCHAR(16) NOT NULL UNIQUE,
    `nama_lengkap` VARCHAR(150) NOT NULL,
    `jenis_kelamin` ENUM('L', 'P') NOT NULL,
    `tempat_lahir` VARCHAR(100) NOT NULL,
    `tanggal_lahir` DATE NOT NULL,
    `agama` ENUM('KRISTEN_PROTESTAN', 'KATOLIK', 'ISLAM', 'HINDU', 'BUDDHA', 'KONGHUCU') DEFAULT 'KRISTEN_PROTESTAN',
    `pendidikan` VARCHAR(50) DEFAULT 'SLTA/SEDERAJAT',
    `pekerjaan` VARCHAR(100) DEFAULT 'PETANI/PEKEBUN',
    `status_perkawinan` ENUM('BELUM_KAWIN', 'KAWIN', 'CERAI_HIDUP', 'CERAI_MATI') DEFAULT 'BELUM_KAWIN',
    `hubungan_keluarga` ENUM('KEPALA_KELUARGA', 'ISTRI', 'ANAK', 'FAMILI_LAIN') DEFAULT 'KEPALA_KELUARGA',
    `status_hidup` ENUM('HIDUP', 'MENINGGAL', 'PINDAH') DEFAULT 'HIDUP',
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    `deleted_at` TIMESTAMP NULL,
    CONSTRAINT `fk_res_family` FOREIGN KEY (`family_id`) REFERENCES `families` (`id`) ON DELETE SET NULL,
    CONSTRAINT `fk_res_hamlet` FOREIGN KEY (`hamlet_id`) REFERENCES `hamlets` (`id`) ON DELETE RESTRICT,
    INDEX `idx_res_nik` (`nik`),
    INDEX `idx_res_nama` (`nama_lengkap`),
    INDEX `idx_res_status` (`status_hidup`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------------------
-- 15. TABEL VILLAGE_ASSETS (Inventaris & Aset Desa)
-- --------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `village_assets` (
    `id` BIGINT AUTO_INCREMENT PRIMARY KEY,
    `asset_code` VARCHAR(50) NOT NULL UNIQUE,
    `asset_name` VARCHAR(200) NOT NULL,
    `category` ENUM('TANAH', 'BANGUNAN', 'KENDARAAN', 'PERALATAN_KANTOR', 'INFRASTRUKTUR') NOT NULL,
    `acquisition_year` INT NOT NULL,
    `source_of_funds` VARCHAR(100) DEFAULT 'APBDes / Dana Desa',
    `acquisition_cost` DECIMAL(15,2) DEFAULT 0.00,
    `quantity` INT NOT NULL DEFAULT 1,
    `unit` VARCHAR(30) DEFAULT 'Unit',
    `condition` ENUM('BAIK', 'RUSAK_RINGAN', 'RUSAK_BERAT') DEFAULT 'BAIK',
    `location` VARCHAR(150) NOT NULL,
    `person_in_charge` VARCHAR(120) NOT NULL,
    `photo_url` VARCHAR(255) NULL,
    `notes` TEXT NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX `idx_asset_category` (`category`),
    INDEX `idx_asset_condition` (`condition`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------------------
-- 16. TABEL INCOMING_LETTERS (Arsip Surat Masuk)
-- --------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `incoming_letters` (
    `id` BIGINT AUTO_INCREMENT PRIMARY KEY,
    `letter_number` VARCHAR(100) NOT NULL,
    `letter_date` DATE NOT NULL,
    `received_date` DATE NOT NULL,
    `sender` VARCHAR(150) NOT NULL,
    `subject` VARCHAR(255) NOT NULL,
    `summary` TEXT NULL,
    `file_url` VARCHAR(255) NULL,
    `disposition_to` BIGINT NULL,
    `disposition_notes` TEXT NULL,
    `status` ENUM('DITERIMA', 'DIDISPOSISIKAN', 'DITINDAKLANJUTI', 'SELESAI') DEFAULT 'DITERIMA',
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT `fk_in_letter_disp` FOREIGN KEY (`disposition_to`) REFERENCES `users` (`id`) ON DELETE SET NULL,
    INDEX `idx_in_letter_num` (`letter_number`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------------------
-- 17. TABEL OUTGOING_LETTERS (Arsip Surat Keluar)
-- --------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `outgoing_letters` (
    `id` BIGINT AUTO_INCREMENT PRIMARY KEY,
    `letter_number` VARCHAR(100) NOT NULL UNIQUE,
    `letter_date` DATE NOT NULL,
    `recipient` VARCHAR(150) NOT NULL,
    `subject` VARCHAR(255) NOT NULL,
    `summary` TEXT NULL,
    `file_url` VARCHAR(255) NULL,
    `signer_user_id` BIGINT NULL,
    `status` ENUM('DRAFT', 'DITANDATANGANI', 'TERKIRIM', 'ARSIP') DEFAULT 'TERKIRIM',
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT `fk_out_letter_signer` FOREIGN KEY (`signer_user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL,
    INDEX `idx_out_letter_num` (`letter_number`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------------------
-- 18. TABEL NOTIFICATIONS (Notifikasi Sistem & Pengajuan)
-- --------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `notifications` (
    `id` BIGINT AUTO_INCREMENT PRIMARY KEY,
    `user_id` BIGINT NOT NULL,
    `title` VARCHAR(150) NOT NULL,
    `message` TEXT NOT NULL,
    `type` ENUM('ATTENDANCE', 'LEAVE', 'JOURNAL', 'SYSTEM', 'ANNOUNCEMENT') NOT NULL,
    `reference_id` BIGINT NULL,
    `is_read` BOOLEAN DEFAULT FALSE,
    `read_at` TIMESTAMP NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT `fk_notif_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
    INDEX `idx_notif_user_read` (`user_id`, `is_read`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------------------
-- 19. TABEL HOLIDAYS (Kalender Libur Resmi Desa / Nasional)
-- --------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `holidays` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `date` DATE NOT NULL UNIQUE,
    `name` VARCHAR(150) NOT NULL,
    `description` VARCHAR(255) NULL,
    `is_recurring` BOOLEAN DEFAULT FALSE,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------------------
-- 20. TABEL APPROVALS (Riwayat Persetujuan Pejabat)
-- --------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `approvals` (
    `id` BIGINT AUTO_INCREMENT PRIMARY KEY,
    `module_type` ENUM('LEAVE', 'OFFICIAL_TRIP', 'WORK_JOURNAL', 'MANUAL_ATTENDANCE') NOT NULL,
    `module_reference_id` BIGINT NOT NULL,
    `reviewer_id` BIGINT NOT NULL,
    `action` ENUM('APPROVE', 'REJECT', 'REQUEST_REVISION') NOT NULL,
    `comment` TEXT NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT `fk_appr_reviewer` FOREIGN KEY (`reviewer_id`) REFERENCES `users` (`id`) ON DELETE RESTRICT,
    INDEX `idx_appr_ref` (`module_type`, `module_reference_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------------------
-- 21. TABEL AUDIT_LOGS (Log Jejak Audit & Keamanan)
-- --------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `audit_logs` (
    `id` BIGINT AUTO_INCREMENT PRIMARY KEY,
    `user_id` BIGINT NULL,
    `action` VARCHAR(100) NOT NULL,
    `ip_address` VARCHAR(45) NULL,
    `user_agent` VARCHAR(255) NULL,
    `details` JSON NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT `fk_audit_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL,
    INDEX `idx_audit_action` (`action`),
    INDEX `idx_audit_created` (`created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
