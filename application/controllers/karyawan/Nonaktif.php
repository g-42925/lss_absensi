<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Nonaktif extends MY_Controller {

    public $email;
    public $session;
    public $form;
    public $upload;
    public $pagination;
    public $other;
    public $menu;
    public $data;
    public $patterns;
    public $tw;
    public $form_validation;
    
    public function __construct() {
        parent::__construct();
        $this->load->library('form_validation');
        $this->load->model('other_model', 'other');
        $this->load->model('user/menu_model', 'menu');
        $this->load->model('user/karyawan/data_model', 'data');
        $this->load->model('user/patterns_model', 'patterns');
        $this->load->model('user/karyawan/timework_model', 'tw');
    }

    public function index() {
        $data['htmlpagejs'] = 'none';
        $data['nmenu']      = 'Karyawan';
        $data['title']      = 'Nonaktif';
        $data['namalabel']  = 'Pengaturan '.$data['title'].' Karyawan';
        $data['auth']       = authUser();

        $data['failed'] = filter_var($this->input->get('failed'),FILTER_VALIDATE_BOOLEAN);

        $companyId = $this->session->userdata('company_id');

        $data['datas'] = $this->db->query("select * from m_pegawai where company_id = ? and is_del = 'y' order by created_at desc",[$companyId])->result_array();

        $divisions = $this->db->query("select * from divisions where company_id = ?",[$companyId])->result_array();

        foreach($data['datas'] as $index => $d){
          $division = $this->db->query("select * from divisions where id = ?",[$d['division_id']])->row_array();
          $data['datas'][$index]['divisi'] = $division['division_name'];
        }
        
        $this->load->view('templates/header', $data);
        $this->load->view('templates/sidemenu', $data);
        $this->load->view('templates/sidenav', $data);
        $this->load->view('module/karyawan/nonaktif/index',$data);
        $this->load->view('templates/footer', $data);
        $this->load->view('templates/fscript-html-end', $data);
    }

    #[SkipPermission]
    public function undo($employeeId){
        isEditable();
        $this->db->set(['is_del' => 'n']);
        $this->db->where('pegawai_id', $employeeId);
        $q = $this->db->update('m_pegawai');

        if($q){
          redirect('karyawan/data');
        }
        else{
          $this->session->set_flashdata(
            'message',
            '<div class="alert alert-danger">Proses gagal. Silakan coba lagi.</div>'
          );
          redirect('karyawan/nonaktif?failed=true');
        }
    }

    #[SkipPermission]
    public function set_end_working($employeeId){
        isEditable();

        $endWorkingAt = $this->input->post('end_working_at');

        // Jika kosong, set null; jika ada, validasi format tanggal
        if (empty($endWorkingAt)) {
            $endWorkingAt = null;
        } else {
            // Pastikan format valid (YYYY-MM-DD)
            $parsed = date_create_from_format('Y-m-d', $endWorkingAt);
            if (!$parsed) {
                $this->session->set_flashdata(
                    'message',
                    '<div class="alert alert-danger">Format tanggal tidak valid.</div>'
                );
                redirect('karyawan/nonaktif?failed=true');
                return;
            }
        }

        $this->db->set(['endWorkingAt' => $endWorkingAt]);
        $this->db->where('pegawai_id', $employeeId);
        $q = $this->db->update('m_pegawai');

        if ($q) {
            $this->session->set_flashdata(
                'message',
                '<div class="alert alert-success">Tanggal berhenti kerja berhasil diperbarui.</div>'
            );
            redirect('karyawan/nonaktif');
        } else {
            $this->session->set_flashdata(
                'message',
                '<div class="alert alert-danger">Proses gagal. Silakan coba lagi.</div>'
            );
            redirect('karyawan/nonaktif?failed=true');
        }
    }
}
