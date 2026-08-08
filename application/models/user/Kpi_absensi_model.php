<?php
defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Kpi_absensi_model
 *
 * Model untuk modul Employee Monthly KPI berbasis data absensi.
 * KPI hanya dihitung untuk bulan yang sudah selesai (bukan bulan berjalan).
 *
 * Mapping is_status di tx_absensi:
 *   hhk     = Hadir Hari Kerja
 *   alpha-2 = Alpha (tidak hadir tanpa keterangan)
 *   c       = Cuti
 *   i       = Izin
 *   s       = Sakit
 *   on duty = Tugas luar kantor
 *   free    = Bebas
 *   cb      = Cuti bersama / libur nasional
 *   l       = Libur (hari jadwal libur)
 *   ts      = Belum ada status
 *   isLate  = 1 → terlambat
 */
class Kpi_absensi_model extends CI_Model
{
    public function __construct()
    {
        parent::__construct();
    }

    // =========================================================
    // CORE: Hitung KPI dari raw data absensi
    // =========================================================

    /**
     * Hitung KPI untuk satu karyawan di satu periode.
     *
     * @param  int    $pegawai_id
     * @param  int    $bulan       1-12
     * @param  int    $tahun       YYYY
     * @return array  Array hasil KPI
     */
    public function calculate_kpi($pegawai_id, $bulan, $tahun)
    {
        $date_start = sprintf('%04d-%02d-01', $tahun, $bulan);
        $date_end   = date('Y-m-t', strtotime($date_start)); // last day of month

        // ── Ambil data absensi seluruh bulan ──────────────────
        $rows = $this->db->query(
            "SELECT
                a.tanggal_absen,
                a.is_status,
                a.jam_masuk,
                a.jam_keluar,
                a.j_masuk,
                a.j_pulang,
                a.j_toleransi,
                a.isLate
             FROM tx_absensi a
             WHERE a.pegawai_id = ?
               AND a.tanggal_absen BETWEEN ? AND ?
               AND a.is_pending = 'n'
             ORDER BY a.tanggal_absen ASC",
            [$pegawai_id, $date_start, $date_end]
        )->result_array();

        // ── Hitung hari kerja efektif (exclude libur & cuti bersama) ─
        $hari_kerja_efektif = $this->_hitung_hari_kerja_efektif($pegawai_id, $date_start, $date_end);

        // ── Counter dasar ─────────────────────────────────────
        $hari_hadir              = 0;
        $hari_izin               = 0;
        $hari_sakit              = 0;
        $hari_cuti               = 0;
        $hari_alpha              = 0;
        $jumlah_terlambat        = 0;
        $total_menit_terlambat   = 0;
        $total_menit_kerja       = 0;
        $jumlah_tepat_masuk      = 0;
        $jumlah_tepat_pulang     = 0;
        $hadir_bisa_cek_pulang   = 0; // hari hadir yg ada jam keluar

        $breakdown = [];

        foreach ($rows as $row) {
            $status = strtolower(trim($row['is_status']));

            // ── Hitung status kehadiran ───────────────────────
            switch ($status) {
                case 'hhk':
                case 'hbhk':
                    $hari_hadir++;
                    break;
                case 'i':
                    $hari_izin++;
                    break;
                case 's':
                    $hari_sakit++;
                    break;
                case 'c':
                    $hari_cuti++;
                    break;
                case 'alpha-2':
                case 'alpha':
                case 'th':
                    $hari_alpha++;
                    break;
            }

            // ── Hitung keterlambatan (hanya hari hadir) ───────
            $menit_terlambat   = 0;
            $is_hadir          = in_array($status, ['hhk', 'hbhk']);
            $jam_masuk_aktual  = $row['jam_masuk'];
            $jam_masuk_jadwal  = $row['j_masuk'];
            $jam_toleransi     = intval($row['j_toleransi']); // menit
            $jam_pulang_jadwal = $row['j_pulang'];
            $jam_keluar_aktual = $row['jam_keluar'];

            // ── Periksa & cari jadwal valid jika 00:00 ─────────
            if (empty($jam_masuk_jadwal) || $jam_masuk_jadwal === '00:00' || $jam_masuk_jadwal === '00:00:00') {
                $valid_schedule = $this->_get_valid_schedule($pegawai_id, $row['tanggal_absen']);
                if ($valid_schedule) {
                    $jam_masuk_jadwal  = $valid_schedule['jam_masuk'];
                    $jam_pulang_jadwal = $valid_schedule['jam_pulang'];
                    $jam_toleransi     = intval($valid_schedule['toleransi_terlambat']);
                }
            }

            $jadwal_masuk_valid = !empty($jam_masuk_jadwal) && $jam_masuk_jadwal !== '00:00' && $jam_masuk_jadwal !== '00:00:00';
            $jadwal_pulang_valid = !empty($jam_pulang_jadwal) && $jam_pulang_jadwal !== '00:00' && $jam_pulang_jadwal !== '00:00:00';

            // Cek kondisi isLate dari database sebagai parameter utama
            $is_late = (isset($row['isLate']) && (filter_var($row['isLate'], FILTER_VALIDATE_BOOLEAN) || (int)$row['isLate'] === 1));

            if ($is_hadir && !empty($jam_masuk_aktual)) {
                if ($is_late) {
                    $jumlah_terlambat++;
                    
                    if ($jadwal_masuk_valid) {
                        $batas_ts = strtotime($row['tanggal_absen'] . ' ' . $jam_masuk_jadwal) + ($jam_toleransi * 60);
                        $aktual_ts = strtotime($row['tanggal_absen'] . ' ' . $jam_masuk_aktual);
                        
                        if ($aktual_ts > $batas_ts) {
                            $menit_terlambat = (int) round(($aktual_ts - $batas_ts) / 60);
                        } else {
                            // Jika isLate true tetapi secara timestamp tidak lewat batas (misalnya ada penyesuaian manual),
                            // tetap hitung dari jadwal aslinya, minimal 1 menit.
                            $batas_tanpa_toleransi = strtotime($row['tanggal_absen'] . ' ' . $jam_masuk_jadwal);
                            $menit_terlambat = max(1, (int) round(($aktual_ts - $batas_tanpa_toleransi) / 60));
                        }
                    } else {
                        // Jika jadwal tidak valid tetapi diset terlambat
                        $menit_terlambat = 1; 
                    }
                    
                    $total_menit_terlambat += $menit_terlambat;
                } else {
                    $jumlah_tepat_masuk++;
                }
            }

            // ── Cek tepat waktu pulang ────────────────────────
            $tepat_pulang = false;
            if ($is_hadir && !empty($jam_keluar_aktual) && $jadwal_pulang_valid) {
                $jadwal_pulang_ts = strtotime($row['tanggal_absen'] . ' ' . $jam_pulang_jadwal);
                $aktual_pulang_ts = strtotime($row['tanggal_absen'] . ' ' . $jam_keluar_aktual);
                $hadir_bisa_cek_pulang++;

                // Toleransi pulang lebih awal: 0 menit (harus >= jam jadwal)
                if ($aktual_pulang_ts >= $jadwal_pulang_ts) {
                    $jumlah_tepat_pulang++;
                    $tepat_pulang = true;
                }
            } elseif ($is_hadir && !empty($jam_keluar_aktual) && !$jadwal_pulang_valid) {
                // Jika jadwal pulang tidak valid, tapi dia keluar, asumsikan tepat waktu
                $hadir_bisa_cek_pulang++;
                $jumlah_tepat_pulang++;
                $tepat_pulang = true;
            }

            // ── Hitung total menit kerja ──────────────────────
            $menit_kerja = 0;
            if ($is_hadir && !empty($jam_masuk_aktual) && !empty($jam_keluar_aktual)) {
                $masuk_ts  = strtotime($row['tanggal_absen'] . ' ' . $jam_masuk_aktual);
                $keluar_ts = strtotime($row['tanggal_absen'] . ' ' . $jam_keluar_aktual);
                if ($keluar_ts > $masuk_ts) {
                    $menit_kerja        = (int) round(($keluar_ts - $masuk_ts) / 60);
                    $total_menit_kerja += $menit_kerja;
                }
            }

            // ── Breakdown harian ──────────────────────────────
            $breakdown[] = [
                'tanggal'         => $row['tanggal_absen'],
                'is_status'       => $row['is_status'],
                'jam_masuk'       => $jam_masuk_aktual,
                'jam_keluar'      => $jam_keluar_aktual,
                'j_masuk'         => $jam_masuk_jadwal,
                'j_pulang'        => $jam_pulang_jadwal,
                'j_toleransi'     => $jam_toleransi,
                'isLate'          => intval($row['isLate']),
                'menit_terlambat' => $menit_terlambat,
                'tepat_pulang'    => $tepat_pulang,
                'menit_kerja'     => $menit_kerja,
            ];
        }

        // ── Surat Peringatan (SP) ─────────────────────────────
        $sp_query = $this->db->query(
            "SELECT COUNT(id) as jml_sp FROM warning 
             WHERE employeeId = ?",
            [$pegawai_id]
        )->row_array();
        $jumlah_sp = $sp_query ? (int) $sp_query['jml_sp'] : 0;

        // ── Kalkulasi persentase ──────────────────────────────
        $persen_kehadiran = $hari_kerja_efektif > 0
            ? round(($hari_hadir / $hari_kerja_efektif) * 100, 2)
            : 0;

        $rata_menit_terlambat = $jumlah_terlambat > 0
            ? round($total_menit_terlambat / $jumlah_terlambat, 2)
            : 0;

        // Persen tepat masuk = (hadir tepat waktu + hadir tapi tidak ada data masuk yg late) / hari hadir * 100
        $persen_tepat_masuk = $hari_hadir > 0
            ? round(($jumlah_tepat_masuk / $hari_hadir) * 100, 2)
            : 0;

        $persen_tepat_pulang = $hadir_bisa_cek_pulang > 0
            ? round(($jumlah_tepat_pulang / $hadir_bisa_cek_pulang) * 100, 2)
            : 0;

        $total_jam_kerja = round($total_menit_kerja / 60, 2);

        // ── Skor Konversi ──────────────────────────────
        $skor_terlambat = 100;
        if ($total_menit_terlambat > 240) {
            $skor_terlambat = 0;
        } elseif ($total_menit_terlambat > 120) {
            $skor_terlambat = 40;
        } elseif ($total_menit_terlambat > 60) {
            $skor_terlambat = 60;
        } elseif ($total_menit_terlambat > 0) {
            $skor_terlambat = 80;
        }

        $skor_alpha = 100;
        if ($hari_alpha >= 4) {
            $skor_alpha = 0;
        } elseif ($hari_alpha == 3) {
            $skor_alpha = 40;
        } elseif ($hari_alpha == 2) {
            $skor_alpha = 60;
        } elseif ($hari_alpha == 1) {
            $skor_alpha = 80;
        }

        // ── Kalkulasi Skor Akhir KPI ──────────────────────────
        $w_kehadiran = 40 / 85;
        $w_masuk     = 25 / 85;
        $w_terlambat = 10 / 85;
        $w_alpha     = 10 / 85;

        $kpi_dasar = ($persen_kehadiran * $w_kehadiran) +
                     ($persen_tepat_masuk * $w_masuk) +
                     ($skor_terlambat * $w_terlambat) +
                     ($skor_alpha * $w_alpha);
                     
        $kpi_score = $kpi_dasar - ($jumlah_sp * 30);

        return [
            // Meta
            'pegawai_id'              => $pegawai_id,
            'periode_bulan'           => $bulan,
            'periode_tahun'           => $tahun,
            // Kehadiran
            'hari_kerja_efektif'      => $hari_kerja_efektif,
            'hari_hadir'              => $hari_hadir,
            'hari_izin'               => $hari_izin,
            'hari_sakit'              => $hari_sakit,
            'hari_cuti'               => $hari_cuti,
            'hari_alpha'              => $hari_alpha,
            'persen_kehadiran'        => $persen_kehadiran,
            // Keterlambatan
            'jumlah_terlambat'             => $jumlah_terlambat,
            'total_menit_terlambat'        => $total_menit_terlambat,
            'rata_menit_terlambat'         => $rata_menit_terlambat,
            'persen_tepat_waktu_masuk'     => $persen_tepat_masuk,
            // Pulang
            'persen_tepat_waktu_pulang'    => $persen_tepat_pulang,
            // Jam kerja
            'total_menit_kerja'       => $total_menit_kerja,
            'total_jam_kerja'         => $total_jam_kerja,
            // SP & Final Score
            'jumlah_sp'               => $jumlah_sp,
            'skor_terlambat'          => $skor_terlambat,
            'skor_alpha'              => $skor_alpha,
            'kpi_dasar'               => $kpi_dasar,
            'kpi_score'               => $kpi_score,
            // Breakdown harian (tidak disimpan ke DB, hanya untuk tampilan)
            'breakdown'               => $breakdown,
        ];
    }

    // =========================================================
    // SAVE: Simpan / update snapshot KPI ke DB
    // =========================================================

    /**
     * Simpan atau update hasil KPI ke tabel tx_kpi_absensi.
     *
     * @param  int   $company_id
     * @param  int   $pegawai_id
     * @param  int   $bulan
     * @param  int   $tahun
     * @param  array $kpi  Hasil dari calculate_kpi()
     * @return bool
     */
    public function save_kpi($company_id, $pegawai_id, $bulan, $tahun, $kpi)
    {
        $existing = $this->db->query(
            "SELECT id FROM tx_kpi_absensi
             WHERE company_id = ? AND pegawai_id = ? AND periode_bulan = ? AND periode_tahun = ?
             LIMIT 1",
            [$company_id, $pegawai_id, $bulan, $tahun]
        )->row_array();

        $payload = [
            'company_id'                => $company_id,
            'pegawai_id'                => $pegawai_id,
            'periode_bulan'             => $bulan,
            'periode_tahun'             => $tahun,
            'hari_kerja_efektif'        => $kpi['hari_kerja_efektif'],
            'hari_hadir'                => $kpi['hari_hadir'],
            'hari_izin'                 => $kpi['hari_izin'],
            'hari_sakit'                => $kpi['hari_sakit'],
            'hari_cuti'                 => $kpi['hari_cuti'],
            'hari_alpha'                => $kpi['hari_alpha'],
            'persen_kehadiran'          => $kpi['persen_kehadiran'],
            'jumlah_terlambat'          => $kpi['jumlah_terlambat'],
            'total_menit_terlambat'     => $kpi['total_menit_terlambat'],
            'rata_menit_terlambat'      => $kpi['rata_menit_terlambat'],
            'persen_tepat_waktu_masuk'  => $kpi['persen_tepat_waktu_masuk'],
            'persen_tepat_waktu_pulang' => $kpi['persen_tepat_waktu_pulang'],
            'total_menit_kerja'         => $kpi['total_menit_kerja'],
            'total_jam_kerja'           => $kpi['total_jam_kerja'],
            'jumlah_sp'                 => $kpi['jumlah_sp'],
            'kpi_score'                 => $kpi['kpi_score'],
            'generated_at'              => date('Y-m-d H:i:s'),
        ];

        if ($existing) {
            $this->db->where('id', $existing['id']);
            return $this->db->update('tx_kpi_absensi', $payload);
        }

        return $this->db->insert('tx_kpi_absensi', $payload);
    }

    // =========================================================
    // GET: Ambil data KPI tersimpan
    // =========================================================

    /**
     * Ambil KPI semua karyawan untuk satu periode dari snapshot DB.
     * Jika belum pernah di-generate, return array kosong.
     *
     * @param  int $company_id
     * @param  int $bulan
     * @param  int $tahun
     * @return array
     */
    public function get_all_kpi($company_id, $bulan, $tahun)
    {
        return $this->db->query(
            "SELECT k.*, p.nama_pegawai, p.nik, p.foto_pegawai,
                    d.division_name, pos.name AS position_name
             FROM tx_kpi_absensi k
             JOIN m_pegawai p ON k.pegawai_id = p.pegawai_id
             LEFT JOIN divisions d ON p.division_id = d.id
             LEFT JOIN position pos ON p.position_id = pos.id
             WHERE k.company_id = ? AND k.periode_bulan = ? AND k.periode_tahun = ?
               AND p.is_del = 'n'
             ORDER BY k.persen_kehadiran DESC, p.nama_pegawai ASC",
            [$company_id, $bulan, $tahun]
        )->result_array();
    }

    /**
     * Ambil KPI satu karyawan dari snapshot DB.
     *
     * @param  int $company_id
     * @param  int $pegawai_id
     * @param  int $bulan
     * @param  int $tahun
     * @return array|null
     */
    public function get_kpi_one($company_id, $pegawai_id, $bulan, $tahun)
    {
        return $this->db->query(
            "SELECT k.*, p.nama_pegawai, p.nik, p.foto_pegawai,
                    d.division_name, pos.name AS position_name
             FROM tx_kpi_absensi k
             JOIN m_pegawai p ON k.pegawai_id = p.pegawai_id
             LEFT JOIN divisions d ON p.division_id = d.id
             LEFT JOIN position pos ON p.position_id = pos.id
             WHERE k.company_id = ? AND k.pegawai_id = ?
               AND k.periode_bulan = ? AND k.periode_tahun = ?
             LIMIT 1",
            [$company_id, $pegawai_id, $bulan, $tahun]
        )->row_array();
    }

    /**
     * Ambil daftar semua karyawan aktif perusahaan.
     *
     * @param  int $company_id
     * @return array
     */
    public function get_all_pegawai($company_id)
    {
        return $this->db->query(
            "SELECT p.pegawai_id, p.nama_pegawai, p.nik, p.foto_pegawai,
                    p.tanggal_mulai_kerja,
                    d.division_name, pos.name AS position_name
             FROM m_pegawai p
             LEFT JOIN divisions d ON p.division_id = d.id
             LEFT JOIN position pos ON p.position_id = pos.id
             WHERE p.company_id = ? AND p.is_del = 'n'
             ORDER BY p.nama_pegawai ASC",
            [$company_id]
        )->result_array();
    }

    // =========================================================
    // HELPER: Hitung hari kerja efektif
    // =========================================================

    /**
     * Hitung hari kerja efektif dalam rentang tanggal,
     * dikurangi hari libur nasional dan hari libur perusahaan.
     *
     * Catatan: Hari sabtu/minggu dikecualikan apabila jadwal karyawan
     * menandai hari itu sebagai libur (is_work='n').
     * Karena data polkerja per-karyawan kompleks, pendekatan sederhana:
     * kita hitung hari kerja = jumlah baris di tx_absensi karyawan tsb
     * yang berstatus BUKAN libur & cuti bersama & ts awal.
     *
     * @param  int    $pegawai_id
     * @param  string $date_start Y-m-d
     * @param  string $date_end   Y-m-d
     * @return int
     */
    private function _hitung_hari_kerja_efektif($pegawai_id, $date_start, $date_end)
    {
        // Hari kerja efektif tidak termasuk hari libur (l, cb, free, off)
        $result = $this->db->query(
            "SELECT COUNT(*) as total
             FROM tx_absensi
             WHERE pegawai_id = ?
               AND tanggal_absen BETWEEN ? AND ?
               AND is_pending = 'n'
               AND LOWER(is_status) NOT IN ('l', 'cb', 'free', 'off')",
            [$pegawai_id, $date_start, $date_end]
        )->row_array();

        return intval($result['total']);
    }

    /**
     * Helper untuk mengambil ulang jadwal yang valid secara dinamis
     * apabila data di tx_absensi bernilai 00:00 atau kosong.
     *
     * @param int $pegawai_id
     * @param string $tanggal_absen
     * @return array|null
     */
    private function _get_valid_schedule($pegawai_id, $tanggal_absen)
    {
        $this->load->helper('i'); // memanggil helper untuk checkJumlahPola
        
        $pola = $this->db->query(
            "SELECT mulai_berlaku_tanggal, dari_hari_ke, pola_kerja_id 
             FROM m_pegawai_pola 
             WHERE pegawai_id=? AND is_selected='y'",
            [$pegawai_id]
        )->row_array();
        
        $pola_kerja_id = null;
        $hari_ke = 1;
        
        if ($pola && $tanggal_absen >= $pola['mulai_berlaku_tanggal']) {
            $hari = (strtotime($tanggal_absen) - strtotime($pola['mulai_berlaku_tanggal'])) / (60 * 60 * 24);
            $hari_ke = round($hari) + $pola['dari_hari_ke'];
            $pola_kerja_id = $pola['pola_kerja_id'];
        } else {
            // Fallback: Cari dari pola kerja berdasarkan Divisi karyawan
            $fallback = $this->db->query(
                "SELECT pk.pola_kerja_id, p.tanggal_mulai_kerja
                 FROM m_pegawai p
                 JOIN divisions d ON p.division_id = d.id
                 JOIN m_pola_kerja pk ON LOWER(pk.nama_pola) = LOWER(d.division_name) AND pk.company_id = p.company_id
                 WHERE p.pegawai_id = ? AND pk.is_del = 'n' LIMIT 1",
                [$pegawai_id]
            )->row_array();
            
            if ($fallback) {
                $pola_kerja_id = $fallback['pola_kerja_id'];
                $tgl_mulai = !empty($fallback['tanggal_mulai_kerja']) ? $fallback['tanggal_mulai_kerja'] : '2020-01-01';
                if ($tanggal_absen >= $tgl_mulai) {
                    $hari = (strtotime($tanggal_absen) - strtotime($tgl_mulai)) / (60 * 60 * 24);
                    $hari_ke = round($hari) + 1;
                }
            }
        }
        
        if ($pola_kerja_id) {
            $checkJumlahPola = checkJumlahPola($pola_kerja_id, $hari_ke);
            
            $det = $this->db->query(
                "SELECT a.jam_masuk, a.jam_pulang, b.toleransi_terlambat 
                 FROM m_pola_kerja_det a 
                 JOIN m_pola_kerja b ON a.pola_kerja_id=b.pola_kerja_id 
                 WHERE a.pola_kerja_id=? AND a.is_day=?",
                [$pola_kerja_id, $checkJumlahPola]
            )->row_array();
            
            if ($det) {
                return $det;
            }
        }
        return null;
    }
}
