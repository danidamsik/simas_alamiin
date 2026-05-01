-- SIMAS Al-Amiin database schema
-- MySQL DDL, schema only, ready to import into DrawSQL.

CREATE TABLE `users` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(255) NOT NULL,
  `email` VARCHAR(255) NOT NULL,
  `email_verified_at` TIMESTAMP NULL DEFAULT NULL,
  `password` VARCHAR(255) NOT NULL,
  `role` ENUM('admin', 'guru', 'kepala_sekolah') NOT NULL DEFAULT 'admin',
  `remember_token` VARCHAR(100) NULL DEFAULT NULL,
  `created_at` TIMESTAMP NULL DEFAULT NULL,
  `updated_at` TIMESTAMP NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `users_email_unique` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `password_reset_tokens` (
  `email` VARCHAR(255) NOT NULL,
  `token` VARCHAR(255) NOT NULL,
  `created_at` TIMESTAMP NULL DEFAULT NULL,
  PRIMARY KEY (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `failed_jobs` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `uuid` VARCHAR(255) NOT NULL,
  `connection` TEXT NOT NULL,
  `queue` TEXT NOT NULL,
  `payload` LONGTEXT NOT NULL,
  `exception` LONGTEXT NOT NULL,
  `failed_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `personal_access_tokens` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `tokenable_type` VARCHAR(255) NOT NULL,
  `tokenable_id` BIGINT UNSIGNED NOT NULL,
  `name` VARCHAR(255) NOT NULL,
  `token` VARCHAR(64) NOT NULL,
  `abilities` TEXT NULL DEFAULT NULL,
  `last_used_at` TIMESTAMP NULL DEFAULT NULL,
  `expires_at` TIMESTAMP NULL DEFAULT NULL,
  `created_at` TIMESTAMP NULL DEFAULT NULL,
  `updated_at` TIMESTAMP NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `personal_access_tokens_token_unique` (`token`),
  KEY `personal_access_tokens_tokenable_type_tokenable_id_index` (`tokenable_type`, `tokenable_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `kelas` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `nama_kelas` VARCHAR(255) NOT NULL,
  `created_at` TIMESTAMP NULL DEFAULT NULL,
  `updated_at` TIMESTAMP NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `periode` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `tahun_ajaran` VARCHAR(255) NOT NULL,
  `semester` ENUM('ganjil', 'genap') NOT NULL,
  `tanggal_mulai` DATE NULL DEFAULT NULL,
  `tanggal_selesai` DATE NULL DEFAULT NULL,
  `is_active` TINYINT(1) NOT NULL DEFAULT 0,
  `created_at` TIMESTAMP NULL DEFAULT NULL,
  `updated_at` TIMESTAMP NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `periode_is_active_index` (`is_active`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `sessions` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `nama_sesi` VARCHAR(255) NOT NULL,
  `jam_mulai` TIME NOT NULL,
  `jam_selesai` TIME NOT NULL,
  `created_at` TIMESTAMP NULL DEFAULT NULL,
  `updated_at` TIMESTAMP NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `guru` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id` BIGINT UNSIGNED NOT NULL,
  `nama` VARCHAR(255) NOT NULL,
  `nip` VARCHAR(255) NULL DEFAULT NULL,
  `is_active` TINYINT(1) NOT NULL DEFAULT 1,
  `deleted_at` TIMESTAMP NULL DEFAULT NULL,
  `created_at` TIMESTAMP NULL DEFAULT NULL,
  `updated_at` TIMESTAMP NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `guru_user_id_unique` (`user_id`),
  CONSTRAINT `guru_user_id_foreign`
    FOREIGN KEY (`user_id`) REFERENCES `users` (`id`)
    ON UPDATE CASCADE ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `siswa` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `nama` VARCHAR(255) NOT NULL,
  `nis` VARCHAR(255) NOT NULL,
  `kelas_id` BIGINT UNSIGNED NOT NULL,
  `is_active` TINYINT(1) NOT NULL DEFAULT 1,
  `deleted_at` TIMESTAMP NULL DEFAULT NULL,
  `created_at` TIMESTAMP NULL DEFAULT NULL,
  `updated_at` TIMESTAMP NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `siswa_nis_unique` (`nis`),
  KEY `siswa_kelas_id_foreign` (`kelas_id`),
  CONSTRAINT `siswa_kelas_id_foreign`
    FOREIGN KEY (`kelas_id`) REFERENCES `kelas` (`id`)
    ON UPDATE CASCADE ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `jadwal` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `kelas_id` BIGINT UNSIGNED NOT NULL,
  `guru_id` BIGINT UNSIGNED NOT NULL,
  `mata_pelajaran` VARCHAR(255) NOT NULL,
  `hari` ENUM('senin', 'selasa', 'rabu', 'kamis', 'jumat', 'sabtu') NOT NULL,
  `session_id` BIGINT UNSIGNED NOT NULL,
  `periode_id` BIGINT UNSIGNED NOT NULL,
  `created_at` TIMESTAMP NULL DEFAULT NULL,
  `updated_at` TIMESTAMP NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `jadwal_kelas_id_index` (`kelas_id`),
  KEY `jadwal_guru_id_index` (`guru_id`),
  KEY `jadwal_session_id_foreign` (`session_id`),
  KEY `jadwal_periode_id_foreign` (`periode_id`),
  CONSTRAINT `jadwal_kelas_id_foreign`
    FOREIGN KEY (`kelas_id`) REFERENCES `kelas` (`id`)
    ON UPDATE CASCADE ON DELETE RESTRICT,
  CONSTRAINT `jadwal_guru_id_foreign`
    FOREIGN KEY (`guru_id`) REFERENCES `guru` (`id`)
    ON UPDATE CASCADE ON DELETE RESTRICT,
  CONSTRAINT `jadwal_session_id_foreign`
    FOREIGN KEY (`session_id`) REFERENCES `sessions` (`id`)
    ON UPDATE CASCADE ON DELETE RESTRICT,
  CONSTRAINT `jadwal_periode_id_foreign`
    FOREIGN KEY (`periode_id`) REFERENCES `periode` (`id`)
    ON UPDATE CASCADE ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `absensi` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `tanggal` DATE NOT NULL,
  `kelas_id` BIGINT UNSIGNED NOT NULL,
  `guru_id` BIGINT UNSIGNED NOT NULL,
  `session_id` BIGINT UNSIGNED NOT NULL,
  `periode_id` BIGINT UNSIGNED NOT NULL,
  `created_at` TIMESTAMP NULL DEFAULT NULL,
  `updated_at` TIMESTAMP NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `absensi_tanggal_kelas_session_periode_unique` (`tanggal`, `kelas_id`, `session_id`, `periode_id`),
  KEY `absensi_tanggal_index` (`tanggal`),
  KEY `absensi_kelas_id_index` (`kelas_id`),
  KEY `absensi_guru_id_foreign` (`guru_id`),
  KEY `absensi_session_id_foreign` (`session_id`),
  KEY `absensi_periode_id_index` (`periode_id`),
  CONSTRAINT `absensi_kelas_id_foreign`
    FOREIGN KEY (`kelas_id`) REFERENCES `kelas` (`id`)
    ON UPDATE CASCADE ON DELETE RESTRICT,
  CONSTRAINT `absensi_guru_id_foreign`
    FOREIGN KEY (`guru_id`) REFERENCES `guru` (`id`)
    ON UPDATE CASCADE ON DELETE RESTRICT,
  CONSTRAINT `absensi_session_id_foreign`
    FOREIGN KEY (`session_id`) REFERENCES `sessions` (`id`)
    ON UPDATE CASCADE ON DELETE RESTRICT,
  CONSTRAINT `absensi_periode_id_foreign`
    FOREIGN KEY (`periode_id`) REFERENCES `periode` (`id`)
    ON UPDATE CASCADE ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `absensi_detail` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `absensi_id` BIGINT UNSIGNED NOT NULL,
  `siswa_id` BIGINT UNSIGNED NOT NULL,
  `status` ENUM('hadir', 'izin', 'sakit', 'alfa') NOT NULL DEFAULT 'hadir',
  `created_at` TIMESTAMP NULL DEFAULT NULL,
  `updated_at` TIMESTAMP NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `absensi_detail_absensi_id_siswa_id_unique` (`absensi_id`, `siswa_id`),
  KEY `absensi_detail_siswa_id_foreign` (`siswa_id`),
  CONSTRAINT `absensi_detail_absensi_id_foreign`
    FOREIGN KEY (`absensi_id`) REFERENCES `absensi` (`id`)
    ON UPDATE CASCADE ON DELETE CASCADE,
  CONSTRAINT `absensi_detail_siswa_id_foreign`
    FOREIGN KEY (`siswa_id`) REFERENCES `siswa` (`id`)
    ON UPDATE CASCADE ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `siswa_kelas_history` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `siswa_id` BIGINT UNSIGNED NOT NULL,
  `kelas_id` BIGINT UNSIGNED NOT NULL,
  `periode_id` BIGINT UNSIGNED NOT NULL,
  `tanggal_masuk` DATE NOT NULL,
  `tanggal_keluar` DATE NULL DEFAULT NULL,
  `keterangan` VARCHAR(255) NULL DEFAULT NULL,
  `created_at` TIMESTAMP NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `siswa_kelas_history_siswa_id_foreign` (`siswa_id`),
  KEY `siswa_kelas_history_kelas_id_foreign` (`kelas_id`),
  KEY `siswa_kelas_history_periode_id_foreign` (`periode_id`),
  CONSTRAINT `siswa_kelas_history_siswa_id_foreign`
    FOREIGN KEY (`siswa_id`) REFERENCES `siswa` (`id`)
    ON UPDATE CASCADE ON DELETE RESTRICT,
  CONSTRAINT `siswa_kelas_history_kelas_id_foreign`
    FOREIGN KEY (`kelas_id`) REFERENCES `kelas` (`id`)
    ON UPDATE CASCADE ON DELETE RESTRICT,
  CONSTRAINT `siswa_kelas_history_periode_id_foreign`
    FOREIGN KEY (`periode_id`) REFERENCES `periode` (`id`)
    ON UPDATE CASCADE ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `audit_log` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id` BIGINT UNSIGNED NOT NULL,
  `action` ENUM('create', 'update', 'delete') NOT NULL,
  `model` VARCHAR(255) NOT NULL,
  `model_id` BIGINT UNSIGNED NOT NULL,
  `old_value` JSON NULL DEFAULT NULL,
  `new_value` JSON NULL DEFAULT NULL,
  `ip_address` VARCHAR(255) NULL DEFAULT NULL,
  `created_at` TIMESTAMP NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `audit_log_user_id_foreign` (`user_id`),
  KEY `audit_log_model_model_id_index` (`model`, `model_id`),
  CONSTRAINT `audit_log_user_id_foreign`
    FOREIGN KEY (`user_id`) REFERENCES `users` (`id`)
    ON UPDATE CASCADE ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
