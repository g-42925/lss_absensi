<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Terkini extends CI_Controller {
    public $email;
    public $session;
    public $form_validation;
    public $upload;
    public $pagination;
    public $other;
    public $menu;
    public $terkini;

    public function __construct() {
        parent::__construct();
        is_logged_in();
        $this->load->library('form_validation');
        $this->load->model('other_model', 'other');
        $this->load->model('user/menu_model', 'menu');
        $this->load->model('user/terkini_model', 'terkini');
    }

    public function index($awal = null, $akhir = null) {
        cek_menu_access();
        $data['htmlpagejs'] = 'none';
        $data['nmenu']      = 'Data Terkini';
        $data['title']      = 'Data Terkini';
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

        $data['datas']  = $this->terkini->get_data($data['tglawal'],$data['tglakhir']);

        $this->load->view('templates/header', $data);
        $this->load->view('templates/sidemenu', $data);
        $this->load->view('templates/sidenav', $data);
        $this->load->view('module/terkini/index', $data);
        $this->load->view('templates/footer', $data);
        $this->load->view('templates/fscript-html-end', $data);
    }

    public function action($id,$tipe) {
        cek_menu_access();
        $data['tipe'] = $tipe;
        $data['datas'] = $this->db->get_where('tx_lokasi_terkini', ['lt_id' => $id])->row_array();
        $this->load->view('module/terkini/action', $data);
    }

    public function hapus($id){
        cek_menu_access();

        $data['auth'] = authUser();
        if($data['auth']['hapus']!='y'){
            $this->session->set_flashdata('message', '<div class="me-3 ms-3 mt-3"><div class="alert alert-danger p-cg" role="alert">Tidak ada akses.</div></div>');
            redirect('terkini');
        }

        $check = $this->db->get_where('tx_lokasi_terkini', ['lt_id' => $id])->row_array();
        $res = $this->db->delete('tx_lokasi_terkini', ['lt_id' => $id]);
        if ($res==true) {
            if (file_exists(FCPATH.$check['foto'])){
                unlink(FCPATH.$check['foto']);
            }
            $this->session->set_flashdata('message', '<div class="me-3 ms-3 mt-3"><div class="alert alert-success p-cg" role="alert">Data berhasil dihapus.</div></div>');
            redirect('terkini');
        }else{
            $this->session->set_flashdata('message', '<div class="me-3 ms-3 mt-3"><div class="alert alert-danger p-cg" role="alert">Proses gagal, silahkan coba lagi.</div></div>');
            redirect('terkini');
        }
    }

}
