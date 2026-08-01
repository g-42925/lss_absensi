<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Kpi_master extends CI_Controller {

    public $session;
    public $menu;

    public $att;
    public $email;
    public $form_validation;
    public $upload;
    public $pagination;


    public function __construct() {
        parent::__construct();
        is_logged_in();
        $this->load->model('user/menu_model', 'menu');
    }

    /**
     * Halaman daftar KPI Master
     */
    public function index() {
        $data['htmlpagejs'] = 'none';
        $data['nmenu']      = 'KPI Master';
        $data['title']      = 'KPI Master';
        $data['namalabel']  = 'Daftar KPI Master';
        $data['auth']       = authUser();

        $companyId = $this->session->userdata('company_id');

        $data['data'] = $this->db->query(
            "SELECT * FROM m_kpi_master
             WHERE company_id = ? AND is_del = 'n'
             ORDER BY kategori ASC, nama_kpi ASC",
            [$companyId]
        )->result_array();

        $this->load->view('templates/header', $data);
        $this->load->view('templates/sidemenu', $data);
        $this->load->view('templates/sidenav', $data);
        $this->load->view('module/kpi_master/index', $data);
        $this->load->view('templates/footer', $data);
        $this->load->view('templates/fscript-html-end', $data);
    }

    /**
     * Halaman tambah KPI Master
     */
    public function add() {
        isEditable();

        $data['htmlpagejs'] = 'none';
        $data['nmenu']      = 'KPI Master';
        $data['title']      = 'KPI Master';
        $data['namalabel']  = 'Tambah KPI';
        $data['auth']       = authUser();

        $this->load->view('templates/header', $data);
        $this->load->view('templates/sidemenu', $data);
        $this->load->view('templates/sidenav', $data);
        $this->load->view('module/kpi_master/add', $data);
        $this->load->view('templates/footer', $data);
        $this->load->view('templates/fscript-html-end', $data);
    }

    /**
     * Proses simpan KPI baru
     */
    public function add_proses() {
        isEditable();

        $companyId = $this->session->userdata('company_id');

        $data = [
            'company_id'  => $companyId,
            'nama_kpi'    => $this->input->post('nama_kpi'),
            'kategori'    => $this->input->post('kategori'),
            'deskripsi'   => $this->input->post('deskripsi'),
            'satuan'      => $this->input->post('satuan'),
            'bobot'       => $this->input->post('bobot'),
            'nilai_min'   => $this->input->post('nilai_min'),
            'nilai_max'   => $this->input->post('nilai_max'),
            'is_aktif'    => $this->input->post('is_aktif') ? 'y' : 'n',
            'is_del'      => 'n',
            'created_at'  => date('Y-m-d H:i:s'),
        ];

        $q = $this->db->insert('m_kpi_master', $data);

        if ($q) {
            $this->session->set_flashdata('message', '<div class="me-3 ms-3 mt-3"><div class="alert alert-success p-cg" role="alert">KPI berhasil ditambahkan.</div></div>');
            redirect('kpi_master');
        } else {
            $this->session->set_flashdata('message', '<div class="me-3 ms-3 mt-3"><div class="alert alert-danger p-cg" role="alert">Proses gagal, silahkan coba lagi.</div></div>');
            redirect('kpi_master/add');
        }
    }

    /**
     * Halaman edit KPI Master
     */
    public function edit($id = null) {
        isEditable();

        if (!$id) { redirect('kpi_master'); }

        $companyId = $this->session->userdata('company_id');

        $row = $this->db->query(
            "SELECT * FROM m_kpi_master WHERE id = ? AND company_id = ? AND is_del = 'n' LIMIT 1",
            [$id, $companyId]
        )->row_array();

        if (!$row) { redirect('kpi_master'); }

        $data['htmlpagejs'] = 'none';
        $data['nmenu']      = 'KPI Master';
        $data['title']      = 'KPI Master';
        $data['namalabel']  = 'Edit KPI';
        $data['auth']       = authUser();
        $data['kpi']        = $row;

        $this->load->view('templates/header', $data);
        $this->load->view('templates/sidemenu', $data);
        $this->load->view('templates/sidenav', $data);
        $this->load->view('module/kpi_master/edit', $data);
        $this->load->view('templates/footer', $data);
        $this->load->view('templates/fscript-html-end', $data);
    }

    /**
     * Proses update KPI
     */
    public function edit_proses($id = null) {
        isEditable();

        if (!$id) { redirect('kpi_master'); }

        $data = [
            'nama_kpi'    => $this->input->post('nama_kpi'),
            'kategori'    => $this->input->post('kategori'),
            'deskripsi'   => $this->input->post('deskripsi'),
            'satuan'      => $this->input->post('satuan'),
            'bobot'       => $this->input->post('bobot'),
            'nilai_min'   => $this->input->post('nilai_min'),
            'nilai_max'   => $this->input->post('nilai_max'),
            'is_aktif'    => $this->input->post('is_aktif') ? 'y' : 'n',
            'updated_at'  => date('Y-m-d H:i:s'),
        ];

        $this->db->where('id', $id);
        $q = $this->db->update('m_kpi_master', $data);

        if ($q) {
            $this->session->set_flashdata('message', '<div class="me-3 ms-3 mt-3"><div class="alert alert-success p-cg" role="alert">KPI berhasil diperbarui.</div></div>');
            redirect('kpi_master');
        } else {
            $this->session->set_flashdata('message', '<div class="me-3 ms-3 mt-3"><div class="alert alert-danger p-cg" role="alert">Proses gagal, silahkan coba lagi.</div></div>');
            redirect('kpi_master/edit/' . $id);
        }
    }

    /**
     * Hapus KPI (soft delete)
     */
    public function delete($id = null) {
        if (!$id) { redirect('kpi_master'); }

        $this->db->where('id', $id);
        $q = $this->db->update('m_kpi_master', ['is_del' => 'y']);

        if ($q) {
            $this->session->set_flashdata('message', '<div class="me-3 ms-3 mt-3"><div class="alert alert-success p-cg" role="alert">KPI berhasil dihapus.</div></div>');
        } else {
            $this->session->set_flashdata('message', '<div class="me-3 ms-3 mt-3"><div class="alert alert-danger p-cg" role="alert">Proses gagal, silahkan coba lagi.</div></div>');
        }

        redirect('kpi_master');
    }

    /**
     * Toggle status aktif KPI (AJAX)
     */
    public function toggle_aktif($id = null) {
        if (!$id) {
            echo json_encode(['status' => 'error']);
            return;
        }

        $row = $this->db->query("SELECT is_aktif FROM m_kpi_master WHERE id = ? LIMIT 1", [$id])->row_array();
        if (!$row) {
            echo json_encode(['status' => 'error']);
            return;
        }

        $newStatus = $row['is_aktif'] === 'y' ? 'n' : 'y';
        $this->db->where('id', $id);
        $this->db->update('m_kpi_master', ['is_aktif' => $newStatus]);

        echo json_encode(['status' => 'ok', 'is_aktif' => $newStatus]);
    }

    /**
     * API: ambil daftar KPI untuk dropdown/AJAX
     */
    public function api_list() {
        $companyId = $this->session->userdata('company_id');
        $result = $this->db->query(
            "SELECT id, nama_kpi, kategori, satuan, bobot FROM m_kpi_master
             WHERE company_id = ? AND is_del = 'n' AND is_aktif = 'y'
             ORDER BY kategori, nama_kpi",
            [$companyId]
        )->result_array();

        header('Content-Type: application/json');
        echo json_encode($result);
    }
}
