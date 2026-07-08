<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Warning extends CI_Controller{
    public $email;
    public $session;
    public $form_validation;
    public $upload;
    public $pagination;
    public $other;
    public $menu;
    public $rp;

    public function __construct(){
        parent::__construct();
        is_logged_in();
        $this->load->model('other_model', 'other');
        $this->load->model('user/menu_model', 'menu');
    }

    public function index(){
        cek_menu_access();
        $data['htmlpagejs'] = 'none';
        $data['nmenu']      = 'Warning';
        $data['title']      = 'Surat Peringatan';
        $data['namalabel']  = $data['title'];
        $data['auth']       = authUser();

        $companyId = $this->session->userdata('company_id');

        $data['data'] = $this->db->query(
            "SELECT w.*, mp.nama_pegawai, mp.id_pegawai
             FROM warning w
             JOIN m_pegawai mp ON w.employeeId = mp.pegawai_id
             WHERE mp.company_id = ?
             ORDER BY w.createdAt DESC",
            [$companyId]
        )->result_array();

        $this->load->view('templates/header', $data);
        $this->load->view('templates/sidemenu', $data);
        $this->load->view('templates/sidenav', $data);
        $this->load->view('module/warning/index', $data);
        $this->load->view('templates/footer', $data);
        $this->load->view('templates/fscript-html-end', $data);
    }

    public function add(){
        cek_menu_access();
        isEditable();
        $data['htmlpagejs'] = 'none';
        $data['nmenu']      = 'Warning';
        $data['title']      = 'Surat Peringatan';
        $data['namalabel']  = 'Tambah Surat Peringatan';
        $data['auth']       = authUser();

        // Auto-generate SP number: SP-YYYY-XXXX
        $year      = date('Y');
        $lastRow   = $this->db->query(
            "SELECT sp_number FROM warning WHERE sp_number LIKE ? ORDER BY id DESC LIMIT 1",
            ["SP-$year-%"]
        )->row_array();

        if ($lastRow) {
            $parts  = explode('-', $lastRow['sp_number']);
            $seq    = intval(end($parts)) + 1;
        } else {
            $seq = 1;
        }
        $data['sp_number'] = 'SP-' . $year . '-' . str_pad($seq, 4, '0', STR_PAD_LEFT);

        $this->load->view('templates/header', $data);
        $this->load->view('templates/sidemenu', $data);
        $this->load->view('templates/sidenav', $data);
        $this->load->view('module/warning/add', $data);
        $this->load->view('templates/footer', $data);
        $this->load->view('templates/fscript-html-end', $data);
    }

    public function add_proses(){
        cek_menu_access();
        isEditable();

        $nik = explode('(', $this->input->post('nik'))[1]; 
        $nik = str_replace(')', '', $nik);

        $data = [
            'employeeId' => $nik,
            'sp_number'  => $this->input->post('sp_number'),
            'level'      => $this->input->post('level'),
            'title'      => $this->input->post('title'),
            'violation'  => $this->input->post('violation'),
            'date'       => $this->input->post('date'),
            'createdAt'  => date('Y-m-d'),
        ];

        $q = $this->db->insert('warning', $data);

        if ($q) {
            $this->session->set_flashdata('message', '<div class="me-3 ms-3 mt-3"><div class="alert alert-success p-cg" role="alert">Surat Peringatan berhasil disimpan.</div></div>');
            redirect('warning');
        } 
        else {
            $this->session->set_flashdata('message', '<div class="me-3 ms-3 mt-3"><div class="alert alert-danger p-cg" role="alert">Proses gagal, silahkan coba lagi.</div></div>');
            redirect('warning/add');
        }
    }

    public function edit($id = null){
        cek_menu_access();
        isEditable();

        if (!$id) { redirect('warning'); }

        $row = $this->db->query(
            "SELECT w.*, mp.nama_pegawai, mp.id_pegawai, mp.pegawai_id AS emp_id
             FROM warning w
             JOIN m_pegawai mp ON w.employeeId = mp.pegawai_id
             WHERE w.id = ?
             LIMIT 1",
            [$id]
        )->row_array();

        if (!$row) { redirect('warning'); }

        $data['htmlpagejs'] = 'none';
        $data['nmenu']      = 'Warning';
        $data['title']      = 'Surat Peringatan';
        $data['namalabel']  = 'Edit Surat Peringatan';
        $data['auth']       = authUser();
        $data['warning']    = $row;

        $this->load->view('templates/header', $data);
        $this->load->view('templates/sidemenu', $data);
        $this->load->view('templates/sidenav', $data);
        $this->load->view('module/warning/edit', $data);
        $this->load->view('templates/footer', $data);
        $this->load->view('templates/fscript-html-end', $data);
    }

    public function edit_proses($id = null){
        cek_menu_access();
        isEditable();

        if (!$id) { redirect('warning'); }

        $nik = explode('(', $this->input->post('nik'))[1];
        $nik = str_replace(')', '', $nik);

        $data = [
            'employeeId' => $nik,
            'level'      => $this->input->post('level'),
            'title'      => $this->input->post('title'),
            'violation'  => $this->input->post('violation'),
            'date'       => $this->input->post('date'),
        ];

        $this->db->where('id', $id);
        $q = $this->db->update('warning', $data);

        if ($q) {
            $this->session->set_flashdata('message', '<div class="me-3 ms-3 mt-3"><div class="alert alert-success p-cg" role="alert">Surat Peringatan berhasil diperbarui.</div></div>');
            redirect('warning');
        } else {
            $this->session->set_flashdata('message', '<div class="me-3 ms-3 mt-3"><div class="alert alert-danger p-cg" role="alert">Proses gagal, silahkan coba lagi.</div></div>');
            redirect('warning/edit/' . $id);
        }
    }

    function print($id = null){
        cek_menu_access();

        if (!$id) { redirect('warning'); }

        // Warning + employee detail (join division & position)
        $warning = $this->db->query(
            "SELECT w.*,
                    mp.nama_pegawai, mp.id_pegawai, mp.nik,
                    d.division_name,
                    p.name AS position_name,
                    mp.company_id
             FROM warning w
             JOIN m_pegawai mp ON w.employeeId = mp.pegawai_id
             LEFT JOIN divisions d   ON mp.division_id = d.id
             LEFT JOIN position  p   ON mp.position_id = p.id
             WHERE w.id = ?
             LIMIT 1",
            [$id]
        )->row_array();

        if (!$warning) { redirect('warning'); }

        // Company info
        $company = $this->db->get_where('company_information', ['company_id' => $warning['company_id']])->row_array();

        $data['warning']    = $warning;
        $data['company']    = $company;
        $data['htmlpagejs'] = 'none';
        $data['nmenu']      = 'Warning';
        $data['title']      = 'Surat Peringatan';
        $data['namalabel']  = 'Cetak Surat Peringatan';
        $data['auth']       = authUser();

        $this->load->view('templates/header', $data);
        $this->load->view('module/warning/print', $data);
        $this->load->view('templates/fscript-html-end', $data);
    }
}
