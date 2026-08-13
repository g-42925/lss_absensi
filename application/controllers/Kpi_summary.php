<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Kpi_summary extends CI_Controller {

    public $session;
    public $menu;

    public $email;

    public $form_validation;

    public $upload;

    public $pagination;

    public $kpi_m;


    public function __construct() {
        parent::__construct();
        is_logged_in();
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
        if ($this->input->post('bulan') && $this->input->post('tahun')) {
            $bulan = intval($this->input->post('bulan'));
            $tahun = intval($this->input->post('tahun'));
        } else {
            $prev  = new DateTime('first day of last month');
            $bulan = intval($prev->format('n'));
            $tahun = intval($prev->format('Y'));
        }

        // -- Ambil KPI Absensi (dari snapshot jika ada, jika tidak hitung langsung) --
        $kpi_absensi_snapshot = $this->db->query(
            "SELECT * FROM tx_kpi_absensi 
             WHERE company_id = ? AND pegawai_id = ? AND periode_bulan = ? AND periode_tahun = ?
             LIMIT 1",
            [$companyId, $pegawai_id, $bulan, $tahun]
        )->row_array();

        // Hitung KPI Absensi real-time juga agar selalu update
        $kpi_absensi_calc = $this->kpi_m->calculate_kpi($pegawai_id, $bulan, $tahun);

        // Gunakan snapshot jika tersedia, fallback ke kalkulasi
        $kpi_absensi_score = $kpi_absensi_snapshot 
            ? floatval($kpi_absensi_snapshot['kpi_score']) 
            : floatval($kpi_absensi_calc['kpi_score']);

        // -- Ambil KPI Biasa (Evaluasi) dari DB --
        $kpi_evaluasi = $this->db->query(
            "SELECT e.*, 
                    (SELECT nama_pegawai FROM m_pegawai WHERE pegawai_id = e.evaluator_id LIMIT 1) AS evaluator_name
             FROM tx_kpi_evaluation e
             WHERE e.pegawai_id = ? AND e.company_id = ? AND e.periode_bulan = ? AND e.periode_tahun = ?
             LIMIT 1",
            [$pegawai_id, $companyId, $bulan, $tahun]
        )->row_array();

        $kpi_evaluasi_score = $kpi_evaluasi ? floatval($kpi_evaluasi['total_nilai']) : null;

        // -- Ambil detail item KPI Evaluasi (dipindah dari view ke controller) --
        $kpi_evaluasi_details = [];
        $kpi_eval_by_cat      = [];
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

        // -- Hitung total gabungan (rata-rata tertimbang 50/50 atau jumlah) --
        // Logika: jika keduanya ada, total = rata-rata. Jika hanya absensi, total = absensi saja.
        if ($kpi_evaluasi_score !== null) {
            $total_kpi = ($kpi_absensi_score + $kpi_evaluasi_score) / 2;
        } else {
            $total_kpi = $kpi_absensi_score;
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
        $data['bulan']            = $bulan;
        $data['tahun']            = $tahun;
        $data['nama_bulan']       = $this->_nama_bulan($bulan);
        $data['kpi_absensi_score']  = $kpi_absensi_score;
        $data['kpi_absensi_snap']   = $kpi_absensi_snapshot;  // null = belum di-generate
        $data['kpi_absensi_calc']   = $kpi_absensi_calc;       // data breakdown
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
}
