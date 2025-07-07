<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Attendance extends CI_Controller {

    public function __construct() {
        parent::__construct();
        is_logged_in();
        $this->load->library('form_validation');
        $this->load->model('other_model', 'other');
        $this->load->model('user/menu_model', 'menu');
        $this->load->model('user/attendance_model', 'att');
    }

    public function index($tgl = null) {
        cek_menu_access();
        $data['htmlpagejs'] = 'none';
        $data['nmenu']      = 'Kehadiran Harian';
        $data['title']      = 'Kehadiran Harian';
        $data['namalabel']  = $data['title'];
        $data['auth']       = authUser();

        if ($tgl=='') {
            $data['today']  = date('Y-m-d');
        }else{
            $data['today']  = $tgl;
        }

        $data['maxdate'] = date("Y-m-d", strtotime(date('Y-m-d')." +3 day"));

        if ($data['today']>$data['maxdate']) {
            $data['today']  = date('Y-m-d');
            $this->session->set_flashdata('message', '<div class="me-3 ms-3"><div class="alert alert-warning p-cg" role="alert">Maksimal 3 hari kedepan dari tanggal sekarang.</div></div>');
        }

        $data['datas']  = $this->att->get_data($data['today'],'n');

        $this->load->view('templates/header', $data);
        $this->load->view('templates/sidemenu', $data);
        $this->load->view('templates/sidenav', $data);
        $this->load->view('module/attendance/index', $data);
        $this->load->view('templates/footer', $data);
        $this->load->view('templates/fscript-html-end', $data);
    }

    public function absensi($tgl,$tipe,$idp,$value) {
        cek_menu_access();
        $res = $this->att->absensi_proses($tgl,$tipe,$idp,$value);
    }

    public function action($inout,$id,$tipe) {
        cek_menu_access();
        $data['inout'] = $inout;
        $data['tipe'] = $tipe;
        $data['datas'] = $this->db->get_where('tx_absensi', ['absen_id' => $id])->row_array();
        $this->load->view('module/attendance/action', $data);
    }

    public function req_cancel($idp = null,$tgl = null) {
        cek_menu_access();

        if ($idp==null || $tgl==null) { redirect('attendance'); }
        $check = $this->db->get_where('m_pegawai', ['pegawai_id' => $idp]);
        if ($check->num_rows()==0) {
            $this->session->set_flashdata('message', '<div class="me-3 ms-3 mt-3"><div class="alert alert-danger p-cg" role="alert">Data tidak ditemukan.</div></div>');
        }else{
            $res = $this->att->req_cancel($idp,$tgl);
            if ($res==true) {
                $this->session->set_flashdata('message', '<div class="me-3 ms-3 mt-3"><div class="alert alert-success p-cg" role="alert">Data request berhasil dihapus.</div></div>');
            }else{
                $this->session->set_flashdata('message', '<div class="me-3 ms-3 mt-3"><div class="alert alert-warning p-cg" role="alert">Data request gagal dihapus.</div></div>');
            }
        }

        redirect('attendance'); 

    }

}
