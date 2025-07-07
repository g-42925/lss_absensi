<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Attendance_approval extends CI_Controller {

    public function __construct() {
        parent::__construct();
        is_logged_in();
        $this->load->library('form_validation');
        $this->load->model('other_model', 'other');
        $this->load->model('user/menu_model', 'menu');
        $this->load->model('user/attendance_approval_model', 'att');
    }

    public function index($tgl = null) {
        cek_menu_access();
        $data['htmlpagejs'] = 'none';
        $data['nmenu']      = 'Menunggu Persetujuan';
        $data['title']      = 'Menunggu Persetujuan';
        $data['namalabel']  = $data['title'];
        $data['auth']       = authUser();

        if ($tgl=='') {
            $data['today']  = date('Y-m-d');
        }else{
            $data['today']  = $tgl;
        }

        $data['maxdate'] = date("Y-m-d", strtotime(date('Y-m-d')." +10 day"));

        if ($data['today']>$data['maxdate']) {
            $data['today']  = date('Y-m-d');
            $this->session->set_flashdata('message', '<div class="me-3 ms-3 mt-3"><div class="alert alert-warning p-cg" role="alert">Maksimal 10 hari kedepan dari tanggal sekarang.</div></div>');
        }

        $data['datas']  = $this->att->get_data($data['today'],'y');

        $this->load->view('templates/header', $data);
        $this->load->view('templates/sidemenu', $data);
        $this->load->view('templates/sidenav', $data);
        $this->load->view('module/attendance_approval/index', $data);
        $this->load->view('templates/footer', $data);
        $this->load->view('templates/fscript-html-end', $data);
    }

    public function acc($tipe = null,$id = null) {
        cek_menu_access();

        if ($tipe==null || $id==null) { redirect('attendance_approval'); }
        $check = $this->db->get_where('tx_absensi', ['absen_id' => $id]);
        $res_check = $check->row_array();
        if ($check->num_rows()==0) {
            $this->session->set_flashdata('message', '<div class="me-3 ms-3 mt-3"><div class="alert alert-danger p-cg" role="alert">Data tidak ditemukan.</div></div>');
        }else{
            $res = $this->att->update_att('acc',$tipe,$id);
            if ($res==true) {
                $this->session->set_flashdata('message', '<div class="me-3 ms-3 mt-3"><div class="alert alert-success p-cg" role="alert">Data berhasil diperbarui.</div></div>');
            }else{
                $this->session->set_flashdata('message', '<div class="me-3 ms-3 mt-3"><div class="alert alert-warning p-cg" role="alert">Proses dihentikan, data gagal diperbarui.</div></div>');
            }
        }

        redirect('attendance_approval/index/'.$res_check['tanggal_absen']); 

    }

    public function reject($tipe = null,$id = null) {
        cek_menu_access();

        if ($tipe==null || $id==null) { redirect('attendance_approval'); }
        $check = $this->db->get_where('tx_absensi', ['absen_id' => $id]);
        $res_check = $check->row_array();
        if ($check->num_rows()==0) {
            $this->session->set_flashdata('message', '<div class="me-3 ms-3 mt-3"><div class="alert alert-danger p-cg" role="alert">Data tidak ditemukan.</div></div>');
        }else{
            $res = $this->att->update_att('reject',$tipe,$id);
            if ($res==true) {
                $this->session->set_flashdata('message', '<div class="me-3 ms-3 mt-3"><div class="alert alert-success p-cg" role="alert">Data berhasil diperbarui.</div></div>');
            }else{
                $this->session->set_flashdata('message', '<div class="me-3 ms-3 mt-3"><div class="alert alert-warning p-cg" role="alert">Proses dihentikan, data gagal diperbarui.</div></div>');
            }
        }

        redirect('attendance_approval/index/'.$res_check['tanggal_absen']); 

    }

}
