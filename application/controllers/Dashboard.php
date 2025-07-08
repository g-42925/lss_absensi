<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Dashboard extends CI_Controller {
    public function __construct() {
        parent::__construct();
        is_logged_in();
        $this->load->library('form_validation');
        $this->load->model('user/menu_model', 'menu');
        $this->load->model('master_model', 'master');
    }

    public $email;
    public $session;
    public $form_validation;
    public $upload;
    public $pagination;
    public $menu;
    public $master;
    

    public function index() {
        $data['htmlpagejs'] = 'dashboard';
        $data['nmenu'] = 'Dashboard';
        $data['title'] = 'Dashboard';
        $data['auth'] = authUser();

        $today = date('Y-m-d');

        $data['t_pegawai'] = $this->db->get_where('m_pegawai', ['is_del' => 'n'])->num_rows();
        $data['t_pending'] = count_pending();
        $data['t_izin'] = count_izin();

        $data['a_masuk'] = $this->db->query("SELECT * FROM tx_absensi WHERE (is_status='hhk' OR is_status='hbhk') AND tanggal_absen='$today'")->num_rows();
        $data['a_sakit'] = $this->db->query("SELECT * FROM tx_absensi WHERE is_status='s' AND tanggal_absen='$today'")->num_rows();

        $izin = $this->db->query("SELECT * FROM tx_absensi WHERE (is_status='i' OR is_status='c' OR is_status='cb' OR is_status='ct' OR is_status='csh') AND tanggal_absen='$today'")->num_rows();
        $izin2 = $this->db->query("SELECT * FROM tx_request_izin WHERE (tipe_request='i' OR tipe_request='c' OR tipe_request='cb' OR tipe_request='ct' OR tipe_request='csh') AND tanggal_request_end<='$today'")->num_rows();

        $data['a_izin'] = $izin+$izin2;

        $data['a_tl'] = $this->db->query("SELECT * FROM tx_absensi WHERE is_status='tl' AND tanggal_absen='$today'")->num_rows();
        
        $ts = $this->db->query("SELECT * FROM tx_absensi WHERE (is_status='ts' OR is_status='th') AND tanggal_absen='$today'")->num_rows();
        $a_ts = $ts-$izin2;

        $data['a_l'] = $this->db->query("SELECT * FROM tx_absensi WHERE is_status='l' AND tanggal_absen='$today'")->num_rows();

        $data['a_ts'] = $data['t_pegawai']-($data['a_masuk']+$data['a_sakit']+$data['a_izin']+$data['a_tl']+$data['a_l']);

        $data['t_terkini'] = $this->db->get_where('tx_lokasi_terkini', ['is_read' => 'n'])->num_rows();

        $this->load->view('templates/header', $data);
        $this->load->view('templates/sidemenu', $data);
        $this->load->view('templates/sidenav', $data);
        $this->load->view('module/dashboard/index', $data);
        $this->load->view('templates/footer', $data);
        $this->load->view('templates/fscript-html-end', $data);
    }

}
