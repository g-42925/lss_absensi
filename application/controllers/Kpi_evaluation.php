<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Kpi_evaluation extends CI_Controller {

    public $session;
    public $menu;

    public $att;
    public $email;
    public $form_validation;
    public $upload;
    public $pagination;

    public $karyawan_data;

    public function __construct() {
        parent::__construct();
        is_logged_in();
        $this->load->model('user/menu_model', 'menu');
    }

    /**
     * Helper untuk mengecek data pegawai dan akses company
     */
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

    /**
     * Menampilkan riwayat/histori evaluasi KPI untuk karyawan tertentu
     */
    public function index($pegawai_id = null) {
        if (!$pegawai_id) {
            $this->load->model('user/karyawan/data_model', 'karyawan_data');
            $companyId = $this->session->userdata('company_id');
            
            $data['htmlpagejs'] = 'none';
            $data['nmenu']      = 'KPI';
            $data['title']      = 'Evaluasi KPI';
            $data['namalabel']  = 'Evaluasi KPI';
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
            $this->load->view('module/kpi_evaluation/list_employee', $data);
            $this->load->view('templates/footer', $data);
            $this->load->view('templates/fscript-html-end', $data);
            return;
        }

        $pegawai = $this->_check_pegawai($pegawai_id);
        $companyId = $this->session->userdata('company_id');

        $data['htmlpagejs'] = 'none';
        $data['nmenu']      = 'KPI'; // Keep sidebar active on KPI
        $data['title']      = 'Evaluasi KPI';
        $data['namalabel']  = 'Histori Evaluasi KPI';
        $data['auth']       = authUser();
        $data['pegawai']    = $pegawai;

        // Ambil riwayat evaluasi
        $data['history'] = $this->db->query(
            "SELECT e.*, 
                    (SELECT nama_pegawai FROM m_pegawai WHERE pegawai_id = e.evaluator_id LIMIT 1) AS evaluator_name
             FROM tx_kpi_evaluation e
             WHERE e.pegawai_id = ? AND e.company_id = ?
             ORDER BY e.periode_tahun DESC, e.periode_bulan DESC",
            [$pegawai_id, $companyId]
        )->result_array();

        $this->load->view('templates/header', $data);
        $this->load->view('templates/sidemenu', $data);
        $this->load->view('templates/sidenav', $data);
        $this->load->view('module/kpi_evaluation/index', $data);
        $this->load->view('templates/footer', $data);
        $this->load->view('templates/fscript-html-end', $data);
    }

    /**
     * Menampilkan form penilaian KPI (Create / Update)
     */
    public function form($pegawai_id = null, $bulan = null, $tahun = null) {
        isEditable();
        if (!$pegawai_id || !$bulan || !$tahun) {
            // Jika form disubmit dari halaman index tanpa parameter URL
            $bulan = $this->input->post('bulan');
            $tahun = $this->input->post('tahun');
            if (!$bulan || !$tahun) {
                redirect('kpi_evaluation/index/' . $pegawai_id);
            }
            redirect('kpi_evaluation/form/' . $pegawai_id . '/' . $bulan . '/' . $tahun);
        }

        $pegawai = $this->_check_pegawai($pegawai_id);
        $companyId = $this->session->userdata('company_id');

        $data['htmlpagejs'] = 'none';
        $data['nmenu']      = 'KPI';
        $data['title']      = 'Evaluasi KPI';
        $data['namalabel']  = 'Form Penilaian KPI';
        $data['auth']       = authUser();
        $data['pegawai']    = $pegawai;
        $data['bulan']      = $bulan;
        $data['tahun']      = $tahun;

        // Cek apakah sudah ada evaluasi di periode ini
        $eval = $this->db->query(
            "SELECT * FROM tx_kpi_evaluation 
             WHERE pegawai_id = ? AND periode_bulan = ? AND periode_tahun = ? AND company_id = ? LIMIT 1",
            [$pegawai_id, $bulan, $tahun, $companyId]
        )->row_array();

        $data['eval'] = $eval;

        // Ambil daftar KPI
        // Jika sudah ada evaluasi, kita ambil detailnya + kpi master (termasuk yg is_aktif='n' jika sudah pernah dinilai)
        // Jika belum ada, kita hanya ambil kpi_master yg is_aktif='y'
        
        if ($eval) {
            $data['kpi_list'] = $this->db->query(
                "SELECT m.*, d.id as detail_id, d.nilai_aktual, d.nilai_bobot, d.catatan_kpi
                 FROM m_kpi_master m
                 LEFT JOIN tx_kpi_evaluation_detail d ON m.id = d.kpi_master_id AND d.evaluation_id = ?
                 WHERE m.company_id = ? AND m.is_del = 'n'
                 AND (m.is_aktif = 'y' OR d.id IS NOT NULL)
                 ORDER BY m.kategori, m.nama_kpi",
                [$eval['id'], $companyId]
            )->result_array();
        } else {
            $data['kpi_list'] = $this->db->query(
                "SELECT * FROM m_kpi_master 
                 WHERE company_id = ? AND is_aktif = 'y' AND is_del = 'n'
                 ORDER BY kategori, nama_kpi",
                [$companyId]
            )->result_array();
        }

        $this->load->view('templates/header', $data);
        $this->load->view('templates/sidemenu', $data);
        $this->load->view('templates/sidenav', $data);
        $this->load->view('module/kpi_evaluation/form', $data);
        $this->load->view('templates/footer', $data);
        $this->load->view('templates/fscript-html-end', $data);
    }

    /**
     * Proses simpan nilai (Insert/Update)
     */
    public function save($pegawai_id = null, $bulan = null, $tahun = null) {
        isEditable();
        if (!$pegawai_id || !$bulan || !$tahun) {
            redirect('karyawan/data');
        }

        $pegawai = $this->_check_pegawai($pegawai_id);
        $companyId = $this->session->userdata('company_id');
        
        $nilai_kpi = $this->input->post('nilai_kpi'); // array: kpi_id => nilai
        $catatan_kpi = $this->input->post('catatan_kpi'); // array: kpi_id => catatan
        $catatan_umum = $this->input->post('catatan_umum');

        if (empty($nilai_kpi)) {
            $this->session->set_flashdata('message', '<div class="alert alert-warning">Tidak ada data KPI yang dinilai.</div>');
            redirect('kpi_evaluation/form/' . $pegawai_id . '/' . $bulan . '/' . $tahun);
        }

        $this->db->trans_start();

        // 1. Cek apakah ada header evaluasi sebelumnya
        $eval = $this->db->query(
            "SELECT id FROM tx_kpi_evaluation 
             WHERE pegawai_id = ? AND periode_bulan = ? AND periode_tahun = ? AND company_id = ? LIMIT 1",
            [$pegawai_id, $bulan, $tahun, $companyId]
        )->row_array();

        $evaluator_id = $this->session->userdata('pegawai_id'); // ID pegawai yang login
        $total_nilai_akhir = 0;

        if ($eval) {
            $eval_id = $eval['id'];
            // Update header nanti (karena butuh total_nilai_akhir)
            // Hapus detail lama agar bisa di-insert ulang
            $this->db->where('evaluation_id', $eval_id);
            $this->db->delete('tx_kpi_evaluation_detail');
        } else {
            // Insert header baru
            $this->db->insert('tx_kpi_evaluation', [
                'company_id'    => $companyId,
                'pegawai_id'    => $pegawai_id,
                'evaluator_id'  => $evaluator_id,
                'periode_bulan' => $bulan,
                'periode_tahun' => $tahun,
                'total_nilai'   => 0, // diupdate nanti
                'catatan'       => $catatan_umum,
                'created_at'    => date('Y-m-d H:i:s')
            ]);
            $eval_id = $this->db->insert_id();
        }

        // 2. Loop dan insert detail, sekalian hitung bobot
        $details_to_insert = [];
        
        // Ambil info master KPI untuk menghitung bobot
        $kpi_ids = array_keys($nilai_kpi);
        if(!empty($kpi_ids)){
            $masters = $this->db->query(
                "SELECT id, bobot, nilai_max FROM m_kpi_master WHERE id IN ? AND company_id = ?",
                [$kpi_ids, $companyId]
            )->result_array();
            
            $master_map = [];
            foreach($masters as $m) {
                $master_map[$m['id']] = $m;
            }

            foreach ($nilai_kpi as $kpi_id => $aktual) {
                if (!isset($master_map[$kpi_id])) continue; // Skip jika ID KPI tidak valid
                
                $m = $master_map[$kpi_id];
                $aktual_val = floatval($aktual);
                
                // Rumus nilai bobot: (Aktual / Maksimal) * Bobot
                // Cegah division by zero
                $max_val = floatval($m['nilai_max']) > 0 ? floatval($m['nilai_max']) : 1;
                
                // Pastikan aktual tidak melebihi maksimal dalam perhitungan (opsional, tapi disarankan)
                $aktual_calc = $aktual_val > $max_val ? $max_val : $aktual_val;
                
                $nilai_bobot = ($aktual_calc / $max_val) * floatval($m['bobot']);
                
                $total_nilai_akhir += $nilai_bobot;

                $details_to_insert[] = [
                    'evaluation_id' => $eval_id,
                    'kpi_master_id' => $kpi_id,
                    'nilai_aktual'  => $aktual_val,
                    'nilai_bobot'   => round($nilai_bobot, 2),
                    'catatan_kpi'   => isset($catatan_kpi[$kpi_id]) ? $catatan_kpi[$kpi_id] : null
                ];
            }
        }

        if (!empty($details_to_insert)) {
            $this->db->insert_batch('tx_kpi_evaluation_detail', $details_to_insert);
        }

        // 3. Update header dengan total_nilai_akhir
        $this->db->where('id', $eval_id);
        $this->db->update('tx_kpi_evaluation', [
            'total_nilai'  => round($total_nilai_akhir, 2),
            'catatan'      => $catatan_umum,
            'evaluator_id' => $evaluator_id,
            'updated_at'   => date('Y-m-d H:i:s')
        ]);

        $this->db->trans_complete();

        if ($this->db->trans_status() === FALSE) {
            $this->session->set_flashdata('message', '<div class="alert alert-danger p-cg">Terjadi kesalahan saat menyimpan evaluasi.</div>');
            redirect('kpi_evaluation/form/' . $pegawai_id . '/' . $bulan . '/' . $tahun);
        } else {
            $this->session->set_flashdata('message', '<div class="alert alert-success p-cg">Evaluasi KPI untuk periode ' . date('F', mktime(0, 0, 0, $bulan, 10)) . ' ' . $tahun . ' berhasil disimpan!</div>');
            redirect('kpi_evaluation/index/' . $pegawai_id);
        }
    }

    /**
     * Hapus record evaluasi KPI
     */
    public function delete($id = null, $pegawai_id = null) {
        isEditable();
        if (!$id || !$pegawai_id) { redirect('karyawan/data'); }
        
        $companyId = $this->session->userdata('company_id');

        $this->db->trans_start();
        $this->db->where('evaluation_id', $id);
        $this->db->delete('tx_kpi_evaluation_detail');

        $this->db->where('id', $id);
        $this->db->where('company_id', $companyId);
        $this->db->delete('tx_kpi_evaluation');
        $this->db->trans_complete();

        if ($this->db->trans_status() === FALSE) {
            $this->session->set_flashdata('message', '<div class="alert alert-danger p-cg">Gagal menghapus evaluasi.</div>');
        } else {
            $this->session->set_flashdata('message', '<div class="alert alert-success p-cg">Data evaluasi berhasil dihapus.</div>');
        }

        redirect('kpi_evaluation/index/' . $pegawai_id);
    }
}
