-- =============================================
-- SQL: Tabel m_kpi_master
-- Modul: KPI Master (HRM)
-- Deskripsi: Menyimpan definisi jenis-jenis KPI
--            yang dapat dinilai terhadap karyawan
-- =============================================

CREATE TABLE IF NOT EXISTS `m_kpi_master` (
  `id`          INT(11)       NOT NULL AUTO_INCREMENT,
  `company_id`  INT(11)       NOT NULL COMMENT 'Referensi ke tabel companies',
  `nama_kpi`    VARCHAR(150)  NOT NULL COMMENT 'Nama indikator KPI',
  `kategori`    VARCHAR(50)   NOT NULL COMMENT 'Kategori: Produktivitas, Kualitas, Kehadiran, dll',
  `deskripsi`   TEXT          NULL     COMMENT 'Panduan cara mengukur / deskripsi KPI',
  `satuan`      VARCHAR(50)   NOT NULL COMMENT 'Satuan pengukuran: Persen, Nilai, Hari, dll',
  `bobot`       DECIMAL(5,2)  NOT NULL DEFAULT 0.00 COMMENT 'Bobot persentase dalam penilaian (%)',
  `nilai_min`   DECIMAL(10,2) NOT NULL DEFAULT 0.00 COMMENT 'Batas nilai minimum',
  `nilai_max`   DECIMAL(10,2) NOT NULL DEFAULT 100.00 COMMENT 'Batas nilai maksimum',
  `is_aktif`    ENUM('y','n') NOT NULL DEFAULT 'y' COMMENT 'Status aktif KPI',
  `is_del`      ENUM('y','n') NOT NULL DEFAULT 'n' COMMENT 'Soft delete flag',
  `created_at`  DATETIME      NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at`  DATETIME      NULL     ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  INDEX `idx_company` (`company_id`),
  INDEX `idx_kategori` (`kategori`),
  INDEX `idx_aktif_del` (`is_aktif`, `is_del`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
  COMMENT='Master data jenis KPI untuk penilaian karyawan';

-- =============================================
-- Contoh data awal (opsional - sesuaikan company_id)
-- =============================================
-- INSERT INTO `m_kpi_master`
--   (`company_id`, `nama_kpi`, `kategori`, `deskripsi`, `satuan`, `bobot`, `nilai_min`, `nilai_max`, `is_aktif`)
-- VALUES
--   (1, 'Tingkat Kehadiran', 'Kehadiran', 'Persentase hari hadir dibanding hari kerja efektif dalam periode penilaian.', 'Persen (%)', 20.00, 0, 100, 'y'),
--   (1, 'Pencapaian Target', 'Produktivitas', 'Persentase pencapaian target kerja yang telah ditetapkan.', 'Persen (%)', 30.00, 0, 100, 'y'),
--   (1, 'Kualitas Pekerjaan', 'Kualitas', 'Penilaian terhadap kualitas dan akurasi hasil kerja karyawan.', 'Nilai (1-10)', 25.00, 1, 10, 'y'),
--   (1, 'Kedisiplinan', 'Perilaku', 'Penilaian terhadap kepatuhan terhadap aturan dan kedisiplinan.', 'Nilai (1-10)', 15.00, 1, 10, 'y'),
--   (1, 'Inovasi & Inisiatif', 'Inovasi', 'Penilaian terhadap kontribusi ide dan inisiatif karyawan.', 'Nilai (1-10)', 10.00, 1, 10, 'y');
