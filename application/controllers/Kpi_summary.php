<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Kpi_summary extends MY_Controller {

    public $session;
    public $menu;

    public $email;

    public $form_validation;

    public $upload;

    public $pagination;

    public $kpi_m;


    public function __construct() {
        parent::__construct();
        $this->load->model('user/menu_model', 'menu');
        $this->load->model('user/Kpi_absensi_model', 'kpi_m');
    }

    private function _check_pegawai($pegawai_id) {
        $companyId = $this->session->userdata('company_id');
        $pegawai = $this->db->query(
            "SELECT p.*, d.division_name, pos.name AS position_name 
             FROM m_pegawai p
             LEFT JOIN divisions d ON p.division_id = d.id
             LEFT JOIN position pos ON p.position_id = pos.id
             WHERE p.pegawai_id = ? AND p.company_id = ? AND p.is_del = 'n' LIMIT 1",
            [$pegawai_id, $companyId]
        )->row_array();

        if (!$pegawai) {
            redirect('karyawan/data');
        }
        return $pegawai;
    }

    private function _nama_bulan($n) {
        $months = [
            1 => 'Januari',    2 => 'Februari', 3 => 'Maret',
            4 => 'April',      5 => 'Mei',       6 => 'Juni',
            7 => 'Juli',       8 => 'Agustus',   9 => 'September',
            10 => 'Oktober',  11 => 'November', 12 => 'Desember',
        ];
        return $months[$n] ?? '-';
    }

    public function index($pegawai_id = null) {
        if (!$pegawai_id) {
            $this->load->model('user/karyawan/data_model', 'karyawan_data');
            $companyId = $this->session->userdata('company_id');
            
            $data['htmlpagejs'] = 'none';
            $data['nmenu']      = 'Karyawan';
            $data['title']      = 'KPI Summary';
            $data['namalabel']  = 'KPI Summary';
            $data['auth']       = authUser();
            
            $divisionId = $this->input->get('divisionId');
            $nik = $this->input->get('nik');
            
            if ($divisionId && $divisionId != 'all') {
                $filter = ['div' => $divisionId, 'nik' => $nik];
                $datas = $this->karyawan_data->getWithFilter($companyId, $filter);
            } else if ($nik) {
                // If only NIK/name is provided but division is all
                $filter = ['div' => 'all', 'nik' => $nik];
                $datas = $this->karyawan_data->getWithFilter($companyId, $filter);
            } else {
                $datas = $this->karyawan_data->get_data($companyId);
            }
            
            // Get divisions for dropdown
            $data['divisions'] = $this->db->query("SELECT * FROM divisions WHERE company_id = ?", [$companyId])->result_array();
            $data['div'] = $divisionId;
            $data['nik'] = $nik;

            foreach($datas as $index => $d){
                $division = $this->db->query("SELECT division_name FROM divisions WHERE id = ?", [$d['division_id']])->row_array();
                $datas[$index]['divisi'] = $division ? $division['division_name'] : '-';
            }
            $data['datas'] = $datas;

            $this->load->view('templates/header', $data);
            $this->load->view('templates/sidemenu', $data);
            $this->load->view('templates/sidenav', $data);
            $this->load->view('module/kpi_summary/list_employee', $data);
            $this->load->view('templates/footer', $data);
            $this->load->view('templates/fscript-html-end', $data);
            return;
        }

        $pegawai   = $this->_check_pegawai($pegawai_id);
        $companyId = $this->session->userdata('company_id');

        // Tentukan periode dari POST atau default ke bulan sebelumnya
        if ($this->input->post('bulan_awal') && $this->input->post('bulan_akhir') && $this->input->post('tahun')) {
            $bulan_awal = intval($this->input->post('bulan_awal'));
            $bulan_akhir = intval($this->input->post('bulan_akhir'));
            $tahun = intval($this->input->post('tahun'));
        } else if ($this->input->post('bulan') && $this->input->post('tahun')) {
            $bulan_awal = intval($this->input->post('bulan'));
            $bulan_akhir = intval($this->input->post('bulan'));
            $tahun = intval($this->input->post('tahun'));
        } else {
            $prev  = new DateTime('first day of last month');
            $bulan_awal = intval($prev->format('n'));
            $bulan_akhir = intval($prev->format('n'));
            $tahun = intval($prev->format('Y'));
        }
        
        $bulan = $bulan_awal; // For backward compatibility with some labels

        // -- Ambil KPI Absensi (dari snapshot jika ada, jika tidak hitung langsung) --
        if ($bulan_awal == $bulan_akhir) {
            $kpi_absensi_snapshot = $this->db->query(
                "SELECT * FROM tx_kpi_absensi 
                 WHERE company_id = ? AND pegawai_id = ? AND periode_bulan = ? AND periode_tahun = ?
                 LIMIT 1",
                [$companyId, $pegawai_id, $bulan_awal, $tahun]
            )->row_array();
        } else {
            $kpi_absensi_snapshot = null; // multi-month uses real-time calculation
        }

        // Hitung KPI Absensi real-time juga agar selalu update
        $kpi_absensi_calc = $this->kpi_m->calculate_kpi($pegawai_id, $bulan_awal, $bulan_akhir, $tahun);

        // Gunakan snapshot jika tersedia, fallback ke kalkulasi
        $kpi_absensi_score = $kpi_absensi_snapshot 
            ? floatval($kpi_absensi_snapshot['kpi_score']) 
            : floatval($kpi_absensi_calc['kpi_score']);

        // -- Ambil KPI Biasa (Evaluasi) dari DB --
        $kpi_evaluasi = null;
        $kpi_evaluasi_score = null;
        $kpi_evaluasi_details = [];
        $kpi_eval_by_cat      = [];

        if ($bulan_awal == $bulan_akhir) {
            $kpi_evaluasi = $this->db->query(
                "SELECT e.*, 
                        (SELECT nama_pegawai FROM m_pegawai WHERE pegawai_id = e.evaluator_id LIMIT 1) AS evaluator_name
                 FROM tx_kpi_evaluation e
                 WHERE e.pegawai_id = ? AND e.company_id = ? AND e.periode_bulan = ? AND e.periode_tahun = ?
                 LIMIT 1",
                [$pegawai_id, $companyId, $bulan_awal, $tahun]
            )->row_array();
    
            $kpi_evaluasi_score = $kpi_evaluasi ? floatval($kpi_evaluasi['total_nilai']) : null;
    
            if ($kpi_evaluasi) {
                $kpi_evaluasi_details = $this->db->query(
                    "SELECT d.*, m.nama_kpi, m.kategori, m.bobot, m.nilai_max
                     FROM tx_kpi_evaluation_detail d
                     JOIN m_kpi_master m ON d.kpi_master_id = m.id
                     WHERE d.evaluation_id = ?
                     ORDER BY m.kategori, m.nama_kpi",
                    [$kpi_evaluasi['id']]
                )->result_array();
    
                foreach ($kpi_evaluasi_details as $det) {
                    $kpi_eval_by_cat[$det['kategori']][] = $det;
                }
            }
        } else {
            $kpi_evaluasi_list = $this->db->query(
                "SELECT e.* 
                 FROM tx_kpi_evaluation e
                 WHERE e.pegawai_id = ? AND e.company_id = ? AND e.periode_bulan >= ? AND e.periode_bulan <= ? AND e.periode_tahun = ?",
                [$pegawai_id, $companyId, $bulan_awal, $bulan_akhir, $tahun]
            )->result_array();

            if (!empty($kpi_evaluasi_list)) {
                $total_nilai_sum = 0;
                $evaluations_count = count($kpi_evaluasi_list);
                $eval_ids = [];
                foreach ($kpi_evaluasi_list as $e) {
                    $total_nilai_sum += floatval($e['total_nilai']);
                    $eval_ids[] = $e['id'];
                }
                
                $kpi_evaluasi_score = $total_nilai_sum / $evaluations_count;
                
                $kpi_evaluasi = [
                    'evaluator_name' => 'Multiple (' . $evaluations_count . ' evaluasi)',
                    'total_nilai' => $kpi_evaluasi_score,
                    'catatan' => 'Agregasi untuk periode beberapa bulan'
                ];
                
                $id_list = implode(',', $eval_ids);
                $details_raw = $this->db->query(
                    "SELECT d.kpi_master_id, m.nama_kpi, m.kategori, m.bobot, m.nilai_max, 
                            AVG(d.nilai_aktual) as avg_aktual, AVG(d.nilai_bobot) as avg_bobot
                     FROM tx_kpi_evaluation_detail d
                     JOIN m_kpi_master m ON d.kpi_master_id = m.id
                     WHERE d.evaluation_id IN ($id_list)
                     GROUP BY d.kpi_master_id, m.nama_kpi, m.kategori, m.bobot, m.nilai_max
                     ORDER BY m.kategori, m.nama_kpi"
                )->result_array();
                
                foreach ($details_raw as $d) {
                    $d['nilai_aktual'] = $d['avg_aktual'];
                    $d['nilai_bobot'] = $d['avg_bobot'];
                    $d['catatan_kpi'] = '-'; 
                    $kpi_evaluasi_details[] = $d;
                    $kpi_eval_by_cat[$d['kategori']][] = $d;
                }
            }
        }

        // -- Hitung total gabungan (rata-rata tertimbang 50/50 atau jumlah) --
        // Logika: jika keduanya ada, total = rata-rata. Jika hanya absensi, total = absensi saja.
        if ($kpi_evaluasi_score !== null) {
            $total_kpi = ($kpi_absensi_score + $kpi_evaluasi_score) / 2;
        } else {
            $total_kpi = $kpi_absensi_score;
        }

        // -- Breakdown KPI per bulan (untuk tampilan multi-bulan) --
        $is_multi_month = ($bulan_awal != $bulan_akhir);
        $kpi_per_bulan  = [];
        if ($is_multi_month) {
            for ($m = $bulan_awal; $m <= $bulan_akhir; $m++) {
                // Hitung KPI Absensi per bulan
                $abs_calc = $this->kpi_m->calculate_kpi($pegawai_id, $m, $m, $tahun);

                // Ambil snapshot absensi per bulan
                $abs_snap = $this->db->query(
                    "SELECT kpi_score FROM tx_kpi_absensi
                     WHERE company_id = ? AND pegawai_id = ? AND periode_bulan = ? AND periode_tahun = ?
                     LIMIT 1",
                    [$companyId, $pegawai_id, $m, $tahun]
                )->row_array();

                $abs_score_m = $abs_snap ? floatval($abs_snap['kpi_score']) : floatval($abs_calc['kpi_score']);

                // Ambil KPI Evaluasi per bulan
                $eval_row = $this->db->query(
                    "SELECT total_nilai FROM tx_kpi_evaluation
                     WHERE pegawai_id = ? AND company_id = ? AND periode_bulan = ? AND periode_tahun = ?
                     LIMIT 1",
                    [$pegawai_id, $companyId, $m, $tahun]
                )->row_array();

                $eval_score_m = $eval_row ? floatval($eval_row['total_nilai']) : null;

                $total_m = ($eval_score_m !== null)
                    ? ($abs_score_m + $eval_score_m) / 2
                    : $abs_score_m;

                $kpi_per_bulan[] = [
                    'bulan'            => $m,
                    'nama_bulan'       => $this->_nama_bulan($m),
                    'tahun'            => $tahun,
                    'kpi_absensi'      => $abs_score_m,
                    'kpi_evaluasi'     => $eval_score_m,
                    'total'            => $total_m,
                    'hari_hadir'       => $abs_calc['hari_hadir'],
                    'hari_alpha'       => $abs_calc['hari_alpha'],
                    'hari_izin'        => $abs_calc['hari_izin'],
                    'hari_sakit'       => $abs_calc['hari_sakit'],
                    'hari_cuti'        => $abs_calc['hari_cuti'],
                    'hari_kerja_efektif' => $abs_calc['hari_kerja_efektif'],
                    'persen_kehadiran' => $abs_calc['persen_kehadiran'],
                    'jumlah_terlambat' => $abs_calc['jumlah_terlambat'],
                    'jumlah_sp'        => $abs_calc['jumlah_sp'],
                    'snap'             => $abs_snap ? true : false,
                ];
            }
        }

        // -- Riwayat KPI Summary (semua periode yang pernah ada evaluasi ATAU absensi) --
        $riwayat_absensi = $this->db->query(
            "SELECT periode_bulan, periode_tahun, kpi_score, generated_at
             FROM tx_kpi_absensi
             WHERE company_id = ? AND pegawai_id = ?
             ORDER BY periode_tahun DESC, periode_bulan DESC",
            [$companyId, $pegawai_id]
        )->result_array();

        $riwayat_evaluasi = $this->db->query(
            "SELECT periode_bulan, periode_tahun, total_nilai, created_at
             FROM tx_kpi_evaluation
             WHERE company_id = ? AND pegawai_id = ?
             ORDER BY periode_tahun DESC, periode_bulan DESC",
            [$companyId, $pegawai_id]
        )->result_array();

        // Merge riwayat ke dalam map [bulan-tahun]
        $riwayat_map = [];
        foreach ($riwayat_absensi as $r) {
            $key = $r['periode_tahun'] . '-' . $r['periode_bulan'];
            $riwayat_map[$key]['bulan']       = $r['periode_bulan'];
            $riwayat_map[$key]['tahun']        = $r['periode_tahun'];
            $riwayat_map[$key]['kpi_absensi']  = floatval($r['kpi_score']);
        }
        foreach ($riwayat_evaluasi as $r) {
            $key = $r['periode_tahun'] . '-' . $r['periode_bulan'];
            if (!isset($riwayat_map[$key])) {
                $riwayat_map[$key]['bulan'] = $r['periode_bulan'];
                $riwayat_map[$key]['tahun'] = $r['periode_tahun'];
            }
            $riwayat_map[$key]['kpi_evaluasi'] = floatval($r['total_nilai']);
        }

        // Hitung total per periode
        foreach ($riwayat_map as $key => &$row) {
            $a = isset($row['kpi_absensi'])  ? $row['kpi_absensi']  : null;
            $b = isset($row['kpi_evaluasi']) ? $row['kpi_evaluasi'] : null;
            if ($a !== null && $b !== null) {
                $row['total'] = ($a + $b) / 2;
            } elseif ($a !== null) {
                $row['total'] = $a;
            } elseif ($b !== null) {
                $row['total'] = $b;
            } else {
                $row['total'] = 0;
            }
        }
        unset($row);

        // Urutkan riwayat: tahun desc, bulan desc
        usort($riwayat_map, function($x, $y) {
            if ($x['tahun'] != $y['tahun']) return $y['tahun'] - $x['tahun'];
            return $y['bulan'] - $x['bulan'];
        });

        $data['htmlpagejs']       = 'none';
        $data['nmenu']            = 'Karyawan';
        $data['title']            = 'KPI Summary';
        $data['namalabel']        = 'KPI Summary – ' . $pegawai['nama_pegawai'];
        $data['auth']             = authUser();
        $data['pegawai']          = $pegawai;
        $data['bulan_awal']       = $bulan_awal;
        $data['bulan_akhir']      = $bulan_akhir;
        $data['bulan']            = $bulan; // backward compatibility
        $data['tahun']            = $tahun;
        $data['is_multi_month']   = $is_multi_month;
        if ($bulan_awal == $bulan_akhir) {
            $data['nama_bulan'] = $this->_nama_bulan($bulan_awal);
        } else {
            $data['nama_bulan'] = $this->_nama_bulan($bulan_awal) . ' – ' . $this->_nama_bulan($bulan_akhir);
        }
        $data['kpi_absensi_score']  = $kpi_absensi_score;
        $data['kpi_absensi_snap']   = $kpi_absensi_snapshot;  // null = belum di-generate
        $data['kpi_absensi_calc']   = $kpi_absensi_calc;       // data breakdown (agregat)
        $data['kpi_per_bulan']      = $kpi_per_bulan;           // breakdown per bulan (array)
        $data['kpi_evaluasi']         = $kpi_evaluasi;           // null = belum dinilai
        $data['kpi_evaluasi_score']   = $kpi_evaluasi_score;     // null = belum dinilai
        $data['kpi_evaluasi_details'] = $kpi_evaluasi_details;   // array item detail KPI evaluasi
        $data['kpi_eval_by_cat']      = $kpi_eval_by_cat;         // detail dikelompokkan per kategori
        $data['total_kpi']            = $total_kpi;
        $data['riwayat_map']          = $riwayat_map;

        $this->load->view('templates/header', $data);
        $this->load->view('templates/sidemenu', $data);
        $this->load->view('templates/sidenav', $data);
        $this->load->view('module/kpi_summary/index', $data);
        $this->load->view('templates/footer', $data);
        $this->load->view('templates/fscript-html-end', $data);
    }

    // =========================================================
    // EXPORT: Export KPI Summary satu karyawan ke CSV/Excel
    // =========================================================

    public function export_excel($pegawai_id = null)
    {
        if (!$pegawai_id) { redirect('kpi_summary'); }

        $pegawai   = $this->_check_pegawai($pegawai_id);
        $companyId = $this->session->userdata('company_id');

        // Baca parameter dari GET
        $bulan_awal  = intval($this->input->get('bulan_awal'));
        $bulan_akhir = intval($this->input->get('bulan_akhir'));
        $tahun       = intval($this->input->get('tahun'));

        // Validasi minimal
        if (!$bulan_awal || !$bulan_akhir || !$tahun) {
            $prev        = new DateTime('first day of last month');
            $bulan_awal  = intval($prev->format('n'));
            $bulan_akhir = intval($prev->format('n'));
            $tahun       = intval($prev->format('Y'));
        }
        if ($bulan_awal > $bulan_akhir) { $bulan_awal = $bulan_akhir; }

        $is_multi  = ($bulan_awal != $bulan_akhir);
        $nama_bln  = $is_multi
            ? $this->_nama_bulan($bulan_awal) . '-' . $this->_nama_bulan($bulan_akhir)
            : $this->_nama_bulan($bulan_awal);
        $safe_name = preg_replace('/[^A-Za-z0-9_\-]/', '_', $pegawai['nama_pegawai']);
        $filename  = 'KPI_Summary_' . $safe_name . '_' . $nama_bln . '_' . $tahun . '_' . date('Ymd_His') . '.csv';

        header('Content-Type: text/csv; charset=UTF-8');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        header('Pragma: no-cache');
        header('Expires: 0');

        $out = fopen('php://output', 'w');
        // BOM agar Excel baca UTF-8 dengan benar
        fputs($out, "\xEF\xBB\xBF");

        // ── INFO KARYAWAN ────────────────────────────────────
        fputcsv($out, ['LAPORAN KPI SUMMARY KARYAWAN']);
        fputcsv($out, ['']);
        fputcsv($out, ['Nama',     $pegawai['nama_pegawai']]);
        fputcsv($out, ['NIK',      $pegawai['nik']]);
        fputcsv($out, ['Divisi',   $pegawai['division_name']  ?? '-']);
        fputcsv($out, ['Jabatan',  $pegawai['position_name']  ?? '-']);
        fputcsv($out, ['Periode',  $nama_bln . ' ' . $tahun]);
        fputcsv($out, ['Dicetak',  date('d/m/Y H:i')]);
        fputcsv($out, ['']);

        if ($is_multi) {
            // ── MODE MULTI-BULAN: satu baris per bulan ───────
            fputcsv($out, [
                'No', 'Bulan', 'Tahun',
                'Hari Efektif', 'Hari Hadir', 'Hari Alpha', 'Hari Izin', 'Hari Sakit', 'Hari Cuti',
                '% Kehadiran', 'Jml Terlambat', 'Jumlah SP',
                'KPI Absensi', 'KPI Evaluasi', 'Total KPI',
            ]);

            $no           = 1;
            $sum_efektif  = $sum_hadir = $sum_alpha = $sum_izin = 0;
            $sum_sakit    = $sum_cuti  = $sum_terlambat = $sum_sp = 0;
            $kpi_abs_all  = [];
            $kpi_eval_all = [];

            for ($m = $bulan_awal; $m <= $bulan_akhir; $m++) {
                $abs_calc = $this->kpi_m->calculate_kpi($pegawai_id, $m, $m, $tahun);

                $abs_snap = $this->db->query(
                    "SELECT kpi_score FROM tx_kpi_absensi
                     WHERE company_id = ? AND pegawai_id = ? AND periode_bulan = ? AND periode_tahun = ?
                     LIMIT 1",
                    [$companyId, $pegawai_id, $m, $tahun]
                )->row_array();
                $abs_score_m = $abs_snap ? floatval($abs_snap['kpi_score']) : floatval($abs_calc['kpi_score']);

                $eval_row = $this->db->query(
                    "SELECT total_nilai FROM tx_kpi_evaluation
                     WHERE pegawai_id = ? AND company_id = ? AND periode_bulan = ? AND periode_tahun = ?
                     LIMIT 1",
                    [$pegawai_id, $companyId, $m, $tahun]
                )->row_array();
                $eval_score_m = $eval_row ? floatval($eval_row['total_nilai']) : null;

                $total_m = ($eval_score_m !== null)
                    ? ($abs_score_m + $eval_score_m) / 2
                    : $abs_score_m;

                fputcsv($out, [
                    $no++,
                    $this->_nama_bulan($m),
                    $tahun,
                    $abs_calc['hari_kerja_efektif'],
                    $abs_calc['hari_hadir'],
                    $abs_calc['hari_alpha'],
                    $abs_calc['hari_izin'],
                    $abs_calc['hari_sakit'],
                    $abs_calc['hari_cuti'],
                    number_format($abs_calc['persen_kehadiran'], 2) . '%',
                    $abs_calc['jumlah_terlambat'],
                    $abs_calc['jumlah_sp'],
                    number_format($abs_score_m, 2),
                    $eval_score_m !== null ? number_format($eval_score_m, 2) : '-',
                    number_format($total_m, 2),
                ]);

                // Akumulator
                $sum_efektif   += $abs_calc['hari_kerja_efektif'];
                $sum_hadir     += $abs_calc['hari_hadir'];
                $sum_alpha     += $abs_calc['hari_alpha'];
                $sum_izin      += $abs_calc['hari_izin'];
                $sum_sakit     += $abs_calc['hari_sakit'];
                $sum_cuti      += $abs_calc['hari_cuti'];
                $sum_terlambat += $abs_calc['jumlah_terlambat'];
                $sum_sp        += $abs_calc['jumlah_sp'];
                $kpi_abs_all[]  = $abs_score_m;
                if ($eval_score_m !== null) $kpi_eval_all[] = $eval_score_m;
            }

            // Baris TOTAL
            $avg_persen   = $sum_efektif > 0 ? ($sum_hadir / $sum_efektif * 100) : 0;
            $avg_abs      = count($kpi_abs_all)  ? array_sum($kpi_abs_all)  / count($kpi_abs_all)  : 0;
            $avg_eval     = count($kpi_eval_all) ? array_sum($kpi_eval_all) / count($kpi_eval_all) : null;
            $avg_total    = $avg_eval !== null ? ($avg_abs + $avg_eval) / 2 : $avg_abs;

            fputcsv($out, ['']);
            fputcsv($out, [
                '', 'TOTAL / RATA-RATA', '',
                $sum_efektif, $sum_hadir, $sum_alpha, $sum_izin, $sum_sakit, $sum_cuti,
                number_format($avg_persen, 2) . '%',
                $sum_terlambat, $sum_sp,
                number_format($avg_abs, 2),
                $avg_eval !== null ? number_format($avg_eval, 2) : '-',
                number_format($avg_total, 2),
            ]);

        } else {
            // ── MODE SINGLE BULAN: detail lengkap ─────────────
            $abs_calc = $this->kpi_m->calculate_kpi($pegawai_id, $bulan_awal, $bulan_akhir, $tahun);

            $abs_snap = $this->db->query(
                "SELECT * FROM tx_kpi_absensi
                 WHERE company_id = ? AND pegawai_id = ? AND periode_bulan = ? AND periode_tahun = ?
                 LIMIT 1",
                [$companyId, $pegawai_id, $bulan_awal, $tahun]
            )->row_array();
            $abs_score = $abs_snap ? floatval($abs_snap['kpi_score']) : floatval($abs_calc['kpi_score']);

            $kpi_evaluasi = $this->db->query(
                "SELECT e.*,
                        (SELECT nama_pegawai FROM m_pegawai WHERE pegawai_id = e.evaluator_id LIMIT 1) AS evaluator_name
                 FROM tx_kpi_evaluation e
                 WHERE e.pegawai_id = ? AND e.company_id = ? AND e.periode_bulan = ? AND e.periode_tahun = ?
                 LIMIT 1",
                [$pegawai_id, $companyId, $bulan_awal, $tahun]
            )->row_array();
            $eval_score = $kpi_evaluasi ? floatval($kpi_evaluasi['total_nilai']) : null;
            $total_kpi  = $eval_score !== null ? ($abs_score + $eval_score) / 2 : $abs_score;

            // Section: RINGKASAN SKOR
            fputcsv($out, ['=== RINGKASAN SKOR ===']);
            fputcsv($out, ['Komponen',     'Skor']);
            fputcsv($out, ['KPI Absensi',  number_format($abs_score, 2)]);
            fputcsv($out, ['KPI Evaluasi', $eval_score !== null ? number_format($eval_score, 2) : 'Belum Dinilai']);
            fputcsv($out, ['Total KPI',    number_format($total_kpi, 2)]);
            fputcsv($out, ['']);

            // Section: DETAIL ABSENSI
            fputcsv($out, ['=== DETAIL KEHADIRAN ===']);
            fputcsv($out, ['Hari Kerja Efektif',         $abs_calc['hari_kerja_efektif']]);
            fputcsv($out, ['Hari Hadir',                 $abs_calc['hari_hadir']]);
            fputcsv($out, ['Hari Izin',                  $abs_calc['hari_izin']]);
            fputcsv($out, ['Hari Sakit',                 $abs_calc['hari_sakit']]);
            fputcsv($out, ['Hari Cuti',                  $abs_calc['hari_cuti']]);
            fputcsv($out, ['Hari Alpha (Tidak Hadir)',   $abs_calc['hari_alpha']]);
            fputcsv($out, ['% Kehadiran',                number_format($abs_calc['persen_kehadiran'], 2) . '%']);
            fputcsv($out, ['Jumlah Terlambat',           $abs_calc['jumlah_terlambat']]);
            fputcsv($out, ['Total Menit Terlambat',      $abs_calc['total_menit_terlambat']]);
            fputcsv($out, ['Rata-rata Menit Terlambat',  number_format($abs_calc['rata_menit_terlambat'], 2)]);
            fputcsv($out, ['% Tepat Waktu Masuk',        number_format($abs_calc['persen_tepat_waktu_masuk'], 2) . '%']);
            fputcsv($out, ['% Tepat Waktu Pulang',       number_format($abs_calc['persen_tepat_waktu_pulang'], 2) . '%']);
            fputcsv($out, ['Total Jam Kerja',            number_format($abs_calc['total_jam_kerja'], 2) . ' jam']);
            fputcsv($out, ['Jumlah SP',                  $abs_calc['jumlah_sp']]);
            fputcsv($out, ['KPI Absensi Score',          number_format($abs_score, 2)]);
            fputcsv($out, ['']);

            // Section: DETAIL KPI EVALUASI
            if ($kpi_evaluasi) {
                fputcsv($out, ['=== DETAIL KPI EVALUASI ===']);
                fputcsv($out, ['Evaluator',    $kpi_evaluasi['evaluator_name'] ?? '-']);
                fputcsv($out, ['Total Nilai',  number_format($eval_score, 2)]);
                fputcsv($out, ['Catatan',      $kpi_evaluasi['catatan'] ?? '-']);
                fputcsv($out, ['']);

                $eval_details = $this->db->query(
                    "SELECT d.*, m.nama_kpi, m.kategori, m.bobot, m.nilai_max
                     FROM tx_kpi_evaluation_detail d
                     JOIN m_kpi_master m ON d.kpi_master_id = m.id
                     WHERE d.evaluation_id = ?
                     ORDER BY m.kategori, m.nama_kpi",
                    [$kpi_evaluasi['id']]
                )->result_array();

                if (!empty($eval_details)) {
                    fputcsv($out, ['Kategori', 'Indikator KPI', 'Bobot', 'Maks.', 'Aktual', 'Nilai Bobot', 'Catatan']);
                    foreach ($eval_details as $d) {
                        fputcsv($out, [
                            $d['kategori'],
                            $d['nama_kpi'],
                            number_format($d['bobot'], 1),
                            number_format($d['nilai_max'], 1),
                            number_format($d['nilai_aktual'], 1),
                            number_format($d['nilai_bobot'], 2),
                            $d['catatan_kpi'] ?? '-',
                        ]);
                    }
                    fputcsv($out, ['']);
                }
            }
        }

        fclose($out);
        exit;
    }
}
