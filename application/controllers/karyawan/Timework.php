<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Timework extends CI_Controller {

    public function __construct() {
        parent::__construct();
        is_logged_in();
        $this->load->library('form_validation');
        $this->load->model('other_model', 'other');
        $this->load->model('user/menu_model', 'menu');
        $this->load->model('user/karyawan/data_model', 'data');
        $this->load->model('user/patterns_model', 'patterns');
        $this->load->model('user/karyawan/timework_model', 'tw');
    }

    public function index() {
        cek_menu_access();
        $data['htmlpagejs'] = 'none';
        $data['nmenu']      = 'Karyawan';
        $data['title']      = 'Waktu Kerja';
        $data['namalabel']  = 'Pengaturan '.$data['title'].' Karyawan';
        $data['auth']       = authUser();

        $data['datas']      = $this->tw->get_data();

        $this->load->view('templates/header', $data);
        $this->load->view('templates/sidemenu', $data);
        $this->load->view('templates/sidenav', $data);
        $this->load->view('module/karyawan/timework/index', $data);
        $this->load->view('templates/footer', $data);
        $this->load->view('templates/fscript-html-end', $data);
    }

    public function add($id = null) {
        cek_menu_access();
        if ($id==null) { redirect('karyawan/timework'); }

        $data['htmlpagejs'] = 'none';
        $data['nmenu']      = 'Karyawan';
        $data['title']      = 'Waktu Kerja';
        $data['namalabel']  = $data['title'];
        $data['auth']       = authUser();
        
        $data['auth']       = authUser();
        if($data['auth']['tambah']!='y'){
            $this->session->set_flashdata('message', '<div class="me-3 ms-3 mt-3"><div class="alert alert-danger p-cg" role="alert">Tidak ada akses.</div></div>');
            redirect('karyawan/timework/');
        }

        $data['pola']      = $this->patterns->get_data();
        $data['karyawan']  = $this->db->get_where('m_pegawai', ['pegawai_id' => $id])->row_array();

        $this->load->view('templates/header', $data);
        $this->load->view('templates/sidemenu', $data);
        $this->load->view('templates/sidenav', $data);
        $this->load->view('module/karyawan/timework/add', $data);
        $this->load->view('templates/footer', $data);
        $this->load->view('templates/fscript-html-end', $data);
    }

    public function pola($id = null) {
        cek_menu_access();
        $data['datas']  = $this->db->get_where('m_pola_kerja_det', ['pola_kerja_id' => $id])->result_array();
        $this->load->view('module/karyawan/timework/pola', $data);
    }

    public function add_proses($id = null) {
        cek_menu_access();
        if ($id==null) { redirect('karyawan/timework'); }

        $this->form_validation->set_rules('pola', 'Pola', 'trim|required|xss_clean|htmlspecialchars');
        $this->form_validation->set_rules('tglmulai', 'Tanggal', 'trim|required|xss_clean|htmlspecialchars');
        $this->form_validation->set_rules('harike', 'Hari', 'trim|required|xss_clean|htmlspecialchars');

        if ($this->form_validation->run() == false) {
            $this->session->set_flashdata('message', '<div class="alert alert-danger p-cg" role="alert">'.validation_errors().'</div>');
            redirect('karyawan/timework/add/'.$id);
        } else {
            $res = $this->tw->add_proses($id);
            if ($res==true) {
                $this->session->set_flashdata('message', '<div class="me-3 ms-3 mt-3"><div class="alert alert-success p-cg" role="alert">Data berhasil disimpan.</div></div>');
                redirect('karyawan/timework');
            }else{
                $this->session->set_flashdata('message', '<div class="alert alert-danger p-cg" role="alert">Proses gagal, silahkan coba lagi.</div>');
                redirect('karyawan/timework/add/'.$id);
            }
        }
    }

    public function edit($id = null) {
        cek_menu_access();
        if ($id==null) { redirect('karyawan/timework'); }
        $check = $this->db->get_where('m_pegawai_pola', ['pegawai_pola_id' => $id]);
        if ($check->num_rows()==0) { 
            $this->session->set_flashdata('message', '<div class="me-3 ms-3 mt-3"><div class="alert alert-danger p-cg" role="alert">Data tidak ditemukan.</div></div>');
            redirect('karyawan/timework'); 
        }

        $data['htmlpagejs'] = 'none';
        $data['nmenu']      = 'Karyawan';
        $data['title']      = 'Waktu Kerja';
        $data['namalabel']  = $data['title'];
        $data['auth']       = authUser();
        
        if($data['auth']['edit']!='y'){
            $this->session->set_flashdata('message', '<div class="me-3 ms-3 mt-3"><div class="alert alert-danger p-cg" role="alert">Tidak ada akses.</div></div>');
            redirect('karyawan/timework/');
        }

        $data['edit']       = $check->row_array();

        $this->load->view('templates/header', $data);
        $this->load->view('templates/sidemenu', $data);
        $this->load->view('templates/sidenav', $data);
        $this->load->view('module/karyawan/timework/edit', $data);
        $this->load->view('templates/footer', $data);
        $this->load->view('templates/fscript-html-end', $data);
    }

    public function edit_proses($id = null) {
        cek_menu_access();

        if ($id==null) { redirect('karyawan/timework'); }
        $check = $this->db->get_where('m_pegawai_pola', ['pegawai_pola_id' => $id]);
        $rowcheck = $check->row_array();
        if ($check->num_rows()==0) {
            $this->session->set_flashdata('message', '<div class="me-3 ms-3 mt-3"><div class="alert alert-danger p-cg" role="alert">Data tidak ditemukan.</div></div>');
            redirect('karyawan/timework'); 
        }

        $this->form_validation->set_rules('pola', 'Pola', 'trim|required|xss_clean|htmlspecialchars');
        $this->form_validation->set_rules('tglmulai', 'Tanggal', 'trim|required|xss_clean|htmlspecialchars');
        $this->form_validation->set_rules('harike', 'Hari', 'trim|required|xss_clean|htmlspecialchars');

        if ($this->form_validation->run() == false) {
            $this->session->set_flashdata('message', '<div class="alert alert-danger p-cg" role="alert">'.validation_errors().'</div>');
            redirect('karyawan/timework/edit/'.$id);
        } else {
            $res = $this->tw->edit_proses($id);
            if ($res==true) {
                $this->session->set_flashdata('message', '<div class="me-3 ms-3 mt-3"><div class="alert alert-success p-cg" role="alert">Data berhasil disimpan.</div></div>');
                redirect('karyawan/timework');
            }else{
                $this->session->set_flashdata('message', '<div class="alert alert-danger p-cg" role="alert">Proses gagal, silahkan coba lagi.</div>');
                redirect('karyawan/timework/edit/'.$id);
            }
        }
    }

    public function hapus($id){
        cek_menu_access();
        
        $data['auth']       = authUser();
        if($data['auth']['hapus']!='y'){
            $this->session->set_flashdata('message', '<div class="me-3 ms-3 mt-3"><div class="alert alert-danger p-cg" role="alert">Tidak ada akses.</div></div>');
            redirect('karyawan/timework/');
        }
        
        $res = $this->other->hapus_data('m_pegawai_pola','pegawai_pola_id',$id);
        if ($res==true) {
            $this->session->set_flashdata('message', '<div class="me-3 ms-3 mt-3"><div class="alert alert-success p-cg" role="alert">Data berhasil dihapus.</div></div>');
            redirect('karyawan/timework');
        }else{
            $this->session->set_flashdata('message', '<div class="me-3 ms-3 mt-3"><div class="alert alert-danger p-cg" role="alert">Proses gagal, silahkan coba lagi.</div></div>');
            redirect('karyawan/timework');
        }
    }

    public function record($id = null) {
        cek_menu_access();

        if ($id==null) { redirect('karyawan/timework'); }
        $check = $this->db->get_where('m_pegawai', ['pegawai_id' => $id]);
        if ($check->num_rows()==0) {
            $this->session->set_flashdata('message', '<div class="me-3 ms-3 mt-3"><div class="alert alert-danger p-cg" role="alert">Data tidak ditemukan.</div></div>');
            redirect('karyawan/timework'); 
        }

        $pola = $check->row_array();

        $data['htmlpagejs'] = 'none';
        $data['nmenu']      = 'Karyawan';
        $data['title']      = 'Waktu Kerja';
        $data['namalabel']  = "Riwayat Pola ".$pola['nama_pegawai'];
        $data['auth']       = authUser();
        $data['id']         = $id;

        $data['datas']      = $this->tw->get_record($id);

        $this->load->view('templates/header', $data);
        $this->load->view('templates/sidemenu', $data);
        $this->load->view('templates/sidenav', $data);
        $this->load->view('module/karyawan/timework/record', $data);
        $this->load->view('templates/footer', $data);
        $this->load->view('templates/fscript-html-end', $data);
    }

    public function hapus_record($id = null, $idx = null){
        cek_menu_access();
        
        $data['auth']       = authUser();
        if($data['auth']['hapus']!='y'){
            $this->session->set_flashdata('message', '<div class="me-3 ms-3 mt-3"><div class="alert alert-danger p-cg" role="alert">Tidak ada akses.</div></div>');
            redirect('karyawan/timework/');
        }

        if ($id==null || $idx==null) { redirect('karyawan/timework/record/'.$id); }
        $check = $this->db->get_where('m_pegawai_pola', ['pegawai_pola_id' => $idx]);
        if ($check->num_rows()==0) {
            $this->session->set_flashdata('message', '<div class="me-3 ms-3 mt-3"><div class="alert alert-danger p-cg" role="alert">Data tidak ditemukan.</div></div>');
            redirect('karyawan/timework/record/'.$id); 
        }
        $res = $this->db->delete('m_pegawai_pola', ['pegawai_pola_id' => $idx]);
        
        $check = $this->db->order_by('pegawai_pola_id', 'DESC')->get_where('m_pegawai_pola', ['pegawai_id' => $id]);

        if ($check->num_rows()>0) {
            $check = $check->row_array();
            $this->db->set(['is_selected' => 'y']);
            $this->db->where('pegawai_pola_id', $check['pegawai_pola_id']);
            $this->db->update('m_pegawai_pola');
        }

        if ($res==true) {
            $this->session->set_flashdata('message', '<div class="me-3 ms-3 mt-3"><div class="alert alert-success p-cg" role="alert">Data berhasil dihapus.</div></div>');
            redirect('karyawan/timework/record/'.$id);
        }else{
            $this->session->set_flashdata('message', '<div class="me-3 ms-3 mt-3"><div class="alert alert-danger p-cg" role="alert">Proses gagal, silahkan coba lagi.</div></div>');
            redirect('karyawan/timework/record/'.$id);
        }
    }

}
