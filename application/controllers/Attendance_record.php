<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Attendance_record extends CI_Controller {

    public $email;
    public $session;
    public $form_validation;
    public $upload;
    public $pagination;
    public $other;
    public $menu;
    public $attr;

    public function __construct() {
        parent::__construct();
        is_logged_in();
        $this->load->library('form_validation');
        $this->load->model('other_model', 'other');
        $this->load->model('user/menu_model', 'menu');
        $this->load->model('user/attendance_record_model', 'attr');
    }

    public function index($awal = null, $akhir = null) {
        cek_menu_access();
        $data['htmlpagejs'] = 'none';
        $data['nmenu']      = 'Rekap Kehadiran';
        $data['title']      = 'Rekap Kehadiran';
        $data['namalabel']  = $data['title'];
        $data['auth']       = authUser();

        $data['tglawal'] = date('Y-m-01');
        if ($awal!=null) {
            $data['tglawal'] = $awal;
        }

        $data['today'] = date('Y-m-d');
        $data['tglakhir'] = date('Y-m-d');
        if ($akhir!=null) {
            $data['tglakhir'] = $akhir;
        }

        $data['datas']      = $this->attr->get_data($data['tglawal'],$data['tglakhir']);

        $this->load->view('templates/header', $data);
        $this->load->view('templates/sidemenu', $data);
        $this->load->view('templates/sidenav', $data);
        $this->load->view('module/attendance_record/index', $data);
        $this->load->view('templates/footer', $data);
        $this->load->view('templates/fscript-html-end', $data);
    }

    public function detail($id = null, $awal = null, $akhir = null) {
        cek_menu_access();
        if ($id==null) { redirect('attendance_record'); }
        $check = $this->db->get_where('m_pegawai', ['pegawai_id' => $id]);
        if ($check->num_rows()==0) { 
            $this->session->set_flashdata('message', '<div class="me-3 ms-3 mt-3"><div class="alert alert-danger p-cg" role="alert">Data tidak ditemukan.</div></div>');
            redirect('attendance_record'); 
        }

        $data['tglawal'] = date('Y-m-01');
        if ($awal!=null) {
            $data['tglawal'] = $awal;
        }

        $data['today'] = date('Y-m-d');
        $data['tglakhir'] = date('Y-m-d');
        if ($akhir!=null) {
            $data['tglakhir'] = $akhir;
        }

        $data['htmlpagejs'] = 'none';
        $data['nmenu']      = 'Rekap Kehadiran';
        $data['title']      = 'Rekap Kehadiran';
        $data['namalabel']  = $data['title'];
        $data['auth']       = authUser();

        $data['id']         = $id;
        $data['datas']      = $this->attr->get_detail($id,$data['tglawal'],$data['tglakhir']);

        $this->load->view('templates/header', $data);
        $this->load->view('templates/sidemenu', $data);
        $this->load->view('templates/sidenav', $data);
        $this->load->view('module/attendance_record/detail', $data);
        $this->load->view('templates/footer', $data);
        $this->load->view('templates/fscript-html-end', $data);
    }

    public function download_laporan($mulai,$akhir) {
        $data['tgl_awal'] = $mulai;
        $data['tgl_akhir'] = $akhir;
        $data['all_data'] = $this->attr->get_data($mulai,$akhir);
        $this->load->view('module/attendance_record/download', $data);
    }

    public function download_laporan_detail($id,$mulai,$akhir) {

        cek_menu_access();
        if ($id==null) { redirect('attendance_record'); }
        $check = $this->db->get_where('m_pegawai', ['pegawai_id' => $id]);
        if ($check->num_rows()==0) { 
            $this->session->set_flashdata('message', '<div class="me-3 ms-3 mt-3"><div class="alert alert-danger p-cg" role="alert">Data tidak ditemukan.</div></div>');
            redirect('attendance_record'); 
        }
        
        if ($id=='all') {
            $id = '';
        }
        $data['user'] = authKaryawan($id);
        $data['tgl_awal'] = $mulai;
        $data['tgl_akhir'] = $akhir;
        $data['all_data'] = $this->attr->get_detail($id,$mulai,$akhir);
        $this->load->view('module/attendance_record/download_detail', $data);
    }

}
