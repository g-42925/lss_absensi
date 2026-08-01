-- =============================================
-- SQL: Tabel tx_kpi_evaluation & detail
-- Modul: KPI Master (HRM)
-- Deskripsi: Menyimpan riwayat penilaian KPI
--            karyawan per bulan dan tahun.
-- =============================================

CREATE TABLE IF NOT EXISTS `tx_kpi_evaluation` (
  `id`            INT(11)       NOT NULL AUTO_INCREMENT,
  `company_id`    INT(11)       NOT NULL,
  `pegawai_id`    INT(11)       NOT NULL COMMENT 'Karyawan yang dinilai',
  `evaluator_id`  INT(11)       NULL     COMMENT 'Yang melakukan penilaian',
  `periode_bulan` INT(2)        NOT NULL COMMENT 'Bulan penilaian (1-12)',
  `periode_tahun` INT(4)        NOT NULL COMMENT 'Tahun penilaian (contoh: 2026)',
  `total_nilai`   DECIMAL(10,2) NOT NULL DEFAULT 0.00,
  `catatan`       TEXT          NULL,
  `created_at`    DATETIME      NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at`    DATETIME      NULL     ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  INDEX `idx_pegawai_periode` (`pegawai_id`, `periode_bulan`, `periode_tahun`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `tx_kpi_evaluation_detail` (
  `id`            INT(11)       NOT NULL AUTO_INCREMENT,
  `evaluation_id` INT(11)       NOT NULL COMMENT 'FK ke tx_kpi_evaluation',
  `kpi_master_id` INT(11)       NOT NULL COMMENT 'FK ke m_kpi_master',
  `nilai_aktual`  DECIMAL(10,2) NOT NULL DEFAULT 0.00,
  `nilai_bobot`   DECIMAL(10,2) NOT NULL DEFAULT 0.00,
  `catatan_kpi`   TEXT          NULL,
  PRIMARY KEY (`id`),
  INDEX `idx_evaluation` (`evaluation_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
