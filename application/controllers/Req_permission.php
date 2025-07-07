<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Req_permission extends CI_Controller {

    public function __construct() {
        parent::__construct();
        is_logged_in();
        $this->load->library('form_validation');
        $this->load->model('other_model', 'other');
        $this->load->model('user/menu_model', 'menu');
        $this->load->model('user/req_permission_model', 'rp');
    }

    public function index($awal = null, $akhir = null) {
        cek_menu_access();
        $data['htmlpagejs'] = 'none';
        $data['nmenu']      = 'Data Request Izin';
        $data['title']      = 'Data Request Izin';
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

        $data['datas']      = $this->rp->get_data($data['tglawal'],$data['tglakhir']);

        $this->load->view('templates/header', $data);
        $this->load->view('templates/sidemenu', $data);
        $this->load->view('templates/sidenav', $data);
        $this->load->view('module/req_permission/index', $data);
        $this->load->view('templates/footer', $data);
        $this->load->view('templates/fscript-html-end', $data);
    }

    public function add() {
        cek_menu_access();
        $data['htmlpagejs'] = 'none';
        $data['nmenu']      = 'Data Request Izin';
        $data['title']      = 'Data Request Izin';
        $data['namalabel']  = $data['title'];
        $data['auth']       = authUser();
        
        if($data['auth']['tambah']!='y'){
            $this->session->set_flashdata('message', '<div class="me-3 ms-3 mt-3"><div class="alert alert-danger p-cg" role="alert">Tidak ada akses.</div></div>');
            redirect('req_permission/');
        }

        $data['karyawan']   = dataKaryawan();
        $data['thismonth'] = date('Y-m-t');

        $this->load->view('templates/header', $data);
        $this->load->view('templates/sidemenu', $data);
        $this->load->view('templates/sidenav', $data);
        $this->load->view('module/req_permission/add', $data);
        $this->load->view('templates/footer', $data);
        $this->load->view('templates/fscript-html-end', $data);
    }

    public function add_proses() {
        cek_menu_access();
        $ukat  = $this->input->post('kat');
        $unama  = $this->input->post('tgl1');

        $this->form_validation->set_rules('idp[]', 'Karyawan', 'trim|required|xss_clean|htmlspecialchars');
        $this->form_validation->set_rules('kat', 'Kategori', 'trim|required|xss_clean|htmlspecialchars');
        $this->form_validation->set_rules('tgl1', 'Tanggal', 'trim|required|xss_clean|htmlspecialchars');

        if ($ukat=='csh') {
            $this->form_validation->set_rules('tgl2', 'Tanggal', 'trim|xss_clean|htmlspecialchars');
        }else{
            $this->form_validation->set_rules('tgl2', 'Sampai Tanggal', 'trim|required|xss_clean|htmlspecialchars');
        }

        if ($ukat=='lm' || $ukat=='csh' || $ukat=='tl') {
            $this->form_validation->set_rules('jmasuk', 'Masuk', 'trim|required|xss_clean|htmlspecialchars');
            $this->form_validation->set_rules('jkeluar', 'Keluar', 'trim|required|xss_clean|htmlspecialchars');
        }else{
            $this->form_validation->set_rules('jmasuk', 'Masuk', 'trim|xss_clean|htmlspecialchars');
            $this->form_validation->set_rules('jkeluar', 'Keluar', 'trim|xss_clean|htmlspecialchars');
        }

        $this->form_validation->set_rules('catatanl', 'Catatan Lembur', 'trim|xss_clean|htmlspecialchars');

        if ($this->form_validation->run() == false) {
            $this->session->set_flashdata('message', '<div class="alert alert-danger p-cg" role="alert">'.validation_errors().'</div>');
            redirect('req_permission/add');
        } else {
            $cekimgpdf = $_FILES['imgpdf']['name'];
            $upload = $this->other->upload_digital('imgpdf','new','others','file_');
            if($upload['result'] == "success" || $cekimgpdf==''){
                $res = $this->rp->add_proses($cekimgpdf,$upload);
                if ($res==true) {
                    $this->session->set_flashdata('message', '<div class="me-3 ms-3 mt-3"><div class="alert alert-success p-cg" role="alert">Data berhasil disimpan.</div></div>');
                    redirect('req_permission');
                }else{
                    $this->session->set_flashdata('message', '<div class="alert alert-danger p-cg" role="alert">Proses gagal, silahkan coba lagi.</div>');
                    redirect('req_permission/add');
                }
            }else{
                $this->session->set_flashdata('message', '<div class="alert alert-danger p-cg" role="alert">'.$upload['error'].'</div>');
                redirect('req_permission/add');
            }
        }
    }

    public function edit($id = null) {
        cek_menu_access();
        if ($id==null) { redirect('req_permission'); }
        $check = $this->db->get_where('tx_request_izin', ['request_izin_id' => $id]);
        if ($check->num_rows()==0) { 
            $this->session->set_flashdata('message', '<div class="me-3 ms-3 mt-3"><div class="alert alert-danger p-cg" role="alert">Data tidak ditemukan.</div></div>');
            redirect('req_permission'); 
        }

        $data['htmlpagejs'] = 'none';
        $data['nmenu']      = 'Data Request Izin';
        $data['title']      = 'Data Request Izin';
        $data['namalabel']  = $data['title'];
        $data['auth']       = authUser();
        
        if($data['auth']['edit']!='y'){
            $this->session->set_flashdata('message', '<div class="me-3 ms-3 mt-3"><div class="alert alert-danger p-cg" role="alert">Tidak ada akses.</div></div>');
            redirect('req_permission/');
        }

        $data['karyawan']   = $this->rp->get_karyawan($id);
        $data['edit']       = $check->row_array();
        $data['thismonth'] = date('Y-m-t');

        $this->load->view('templates/header', $data);
        $this->load->view('templates/sidemenu', $data);
        $this->load->view('templates/sidenav', $data);
        $this->load->view('module/req_permission/edit', $data);
        $this->load->view('templates/footer', $data);
        $this->load->view('templates/fscript-html-end', $data);
    }

    public function edit_proses($id = null) {
        cek_menu_access();

        if ($id==null) { redirect('req_permission'); }
        $check = $this->db->get_where('tx_request_izin', ['request_izin_id' => $id]);
        $rowcheck = $check->row_array();
        if ($check->num_rows()==0) {
            $this->session->set_flashdata('message', '<div class="me-3 ms-3 mt-3"><div class="alert alert-danger p-cg" role="alert">Data tidak ditemukan.</div></div>');
            redirect('req_permission'); 
        }

        $ukat  = $this->input->post('kat');
        $unama  = $this->input->post('tgl1');

        $this->form_validation->set_rules('idp[]', 'Karyawan', 'trim|required|xss_clean|htmlspecialchars');
        $this->form_validation->set_rules('kat', 'Kategori', 'trim|required|xss_clean|htmlspecialchars');
        $this->form_validation->set_rules('tgl1', 'Tanggal', 'trim|required|xss_clean|htmlspecialchars');

        if ($ukat=='csh') {
            $this->form_validation->set_rules('tgl2', 'Tanggal', 'trim|xss_clean|htmlspecialchars');
        }else{
            $this->form_validation->set_rules('tgl2', 'Sampai Tanggal', 'trim|required|xss_clean|htmlspecialchars');
        }

        if ($ukat=='lm' || $ukat=='csh' || $ukat=='tl') {
            $this->form_validation->set_rules('jmasuk', 'Masuk', 'trim|required|xss_clean|htmlspecialchars');
            $this->form_validation->set_rules('jkeluar', 'Keluar', 'trim|required|xss_clean|htmlspecialchars');
        }else{
            $this->form_validation->set_rules('jmasuk', 'Masuk', 'trim|xss_clean|htmlspecialchars');
            $this->form_validation->set_rules('jkeluar', 'Keluar', 'trim|xss_clean|htmlspecialchars');
        }

        $this->form_validation->set_rules('catatanl', 'Catatan Lembur', 'trim|xss_clean|htmlspecialchars');
        $this->form_validation->set_rules('status', 'Status', 'trim|required|xss_clean|htmlspecialchars');

        if ($this->form_validation->run() == false) {
            $this->session->set_flashdata('message', '<div class="alert alert-danger p-cg" role="alert">'.validation_errors().'</div>');
            redirect('req_permission/edit/'.$id);
        } else {
            $cekimgpdf = $_FILES['imgpdf']['name'];
            $imgold = $rowcheck['file_dokumen'];
            if ($imgold=='') { $imgold = 'new'; }
            $upload = $this->other->upload_digital('imgpdf',$imgold,'others','file_');
            if($upload['result'] == "success" || $cekimgpdf==''){
                $res = $this->rp->edit_proses($id,$cekimgpdf,$upload,$imgold);
                if ($res==true) {
                    $this->session->set_flashdata('message', '<div class="me-3 ms-3 mt-3"><div class="alert alert-success p-cg" role="alert">Data berhasil disimpan.</div></div>');
                    redirect('req_permission');
                }else{
                    $this->session->set_flashdata('message', '<div class="alert alert-danger p-cg" role="alert">Proses gagal, silahkan coba lagi.</div>');
                    redirect('req_permission/edit/'.$id);
                }
            }else{
                $this->session->set_flashdata('message', '<div class="alert alert-danger p-cg" role="alert">'.$upload['error'].'</div>');
                redirect('req_permission/add');
            }
        }
    }

    public function hapus($id = null){
        cek_menu_access();
        
        $data['auth'] = authUser();
        if($data['auth']['hapus']!='y'){
            $this->session->set_flashdata('message', '<div class="me-3 ms-3 mt-3"><div class="alert alert-danger p-cg" role="alert">Tidak ada akses.</div></div>');
            redirect('req_permission/');
        }

        if ($id==null) { redirect('req_permission'); }
        $check = $this->db->get_where('tx_request_izin', ['request_izin_id' => $id]);
        if ($check->num_rows()==0) {
            $this->session->set_flashdata('message', '<div class="me-3 ms-3 mt-3"><div class="alert alert-danger p-cg" role="alert">Data tidak ditemukan.</div></div>');
            redirect('req_permission'); 
        }

        $res = $this->db->delete('tx_request_izin', ['request_izin_id' => $id]);
        $res = $this->db->delete('tx_request_izin_pegawai', ['request_izin_id' => $id]);
        $res = $this->db->delete('tx_absensi', ['is_request' => $id]);
        if ($res==true) {
            $this->session->set_flashdata('message', '<div class="me-3 ms-3 mt-3"><div class="alert alert-success p-cg" role="alert">Data berhasil dihapus.</div></div>');
            redirect('req_permission');
        }else{
            $this->session->set_flashdata('message', '<div class="me-3 ms-3 mt-3"><div class="alert alert-danger p-cg" role="alert">Proses gagal, silahkan coba lagi.</div></div>');
            redirect('req_permission');
        }
    }

    public function download_laporan($mulai,$akhir) {
        cek_menu_access();
        $data['tgl_awal'] = $mulai;
        $data['tgl_akhir'] = $akhir;
        $data['all_data'] = $this->rp->get_data($mulai,$akhir);
        $this->load->view('module/req_permission/download', $data);
    }

    public function action($id,$idp) {
        cek_menu_access();
        $data['datar'] = $this->db->get_where('tx_request_izin', ['request_izin_id' => $id])->row_array();
        $data['datap'] = $this->db->query("SELECT * FROM tx_request_izin_pegawai a LEFT JOIN m_pegawai b ON a.pegawai_id=b.pegawai_id WHERE a.request_izin_id='$id' AND a.pegawai_id='$idp' AND b.is_del='n' ")->row_array();
        $data['datatl'] = $this->rp->get_data_tl($id,$idp);
        $this->load->view('module/req_permission/action', $data);
    }

    public function download_perid($id,$idp) {
        cek_menu_access();
        $data['setting'] = pengaturanSistem();
        $data['datar'] = $this->db->get_where('tx_request_izin', ['request_izin_id' => $id])->row_array();        
        $data['datap'] = $this->db->query("SELECT * FROM tx_request_izin_pegawai a LEFT JOIN m_pegawai b ON a.pegawai_id=b.pegawai_id WHERE a.request_izin_id='$id' AND a.pegawai_id='$idp' AND b.is_del='n' ")->row_array();
        if($data['datar']['tipe_request']=='tl'){
            $this->load->view('module/req_permission/download_perid_tl', $data);
        }else{
            $this->load->view('module/req_permission/download_perid', $data);
        }
    }

}
