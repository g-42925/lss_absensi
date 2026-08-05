-- =============================================
-- SQL: Tabel tx_kpi_absensi
-- Modul: Employee Monthly KPI (berbasis absensi)
-- Deskripsi: Menyimpan snapshot hasil KPI bulanan karyawan
--            yang dihitung otomatis dari data tx_absensi.
--            Bulan yang dihitung = bulan sebelum bulan berjalan.
-- =============================================

CREATE TABLE IF NOT EXISTS `tx_kpi_absensi` (
  `id`                      INT(11)        NOT NULL AUTO_INCREMENT,
  `company_id`              INT(11)        NOT NULL,
  `pegawai_id`              INT(11)        NOT NULL,
  `periode_bulan`           TINYINT(2)     NOT NULL COMMENT 'Bulan penilaian (1-12)',
  `periode_tahun`           SMALLINT(4)    NOT NULL COMMENT 'Tahun penilaian',

  -- Kehadiran
  `hari_kerja_efektif`      SMALLINT(3)    NOT NULL DEFAULT 0 COMMENT 'Total hari kerja di periode tsb',
  `hari_hadir`              SMALLINT(3)    NOT NULL DEFAULT 0 COMMENT 'Status hhk',
  `hari_izin`               SMALLINT(3)    NOT NULL DEFAULT 0 COMMENT 'Status i',
  `hari_sakit`              SMALLINT(3)    NOT NULL DEFAULT 0 COMMENT 'Status s',
  `hari_cuti`               SMALLINT(3)    NOT NULL DEFAULT 0 COMMENT 'Status c',
  `hari_alpha`              SMALLINT(3)    NOT NULL DEFAULT 0 COMMENT 'Status alpha-2',
  `persen_kehadiran`        DECIMAL(5,2)   NOT NULL DEFAULT 0.00,

  -- Keterlambatan
  `jumlah_terlambat`        SMALLINT(3)    NOT NULL DEFAULT 0,
  `total_menit_terlambat`   INT(11)        NOT NULL DEFAULT 0,
  `rata_menit_terlambat`    DECIMAL(7,2)   NOT NULL DEFAULT 0.00,
  `persen_tepat_waktu_masuk` DECIMAL(5,2)  NOT NULL DEFAULT 0.00,

  -- Pulang
  `persen_tepat_waktu_pulang` DECIMAL(5,2) NOT NULL DEFAULT 0.00,

  -- Jam kerja
  `total_menit_kerja`       INT(11)        NOT NULL DEFAULT 0 COMMENT 'Total menit kerja aktual',
  `total_jam_kerja`         DECIMAL(7,2)   NOT NULL DEFAULT 0.00 COMMENT 'Dikonversi ke jam',

  -- Audit
  `generated_at`            DATETIME       NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at`              DATETIME       NULL ON UPDATE CURRENT_TIMESTAMP,

  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_kpi_absensi_periode` (`company_id`, `pegawai_id`, `periode_bulan`, `periode_tahun`),
  INDEX `idx_kpi_periode` (`periode_bulan`, `periode_tahun`, `company_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =============================================
-- SQL INSERT menu navigasi
-- Sesuaikan parent_id dengan menu "KPI" atau
-- "Rekap Kehadiran" yang ada di tabel m_menu / menu
-- Jalankan SETELAH mengecek tabel menu yang digunakan.
-- =============================================
-- Contoh (sesuaikan nama tabel & kolom):
-- INSERT INTO m_menu (nama_menu, link_url, icon, tipe, parent_id, urutan)
-- VALUES ('KPI Absensi', 'kpi_absensi', 'ti ti-chart-bar', 2, <parent_id>, 99);
