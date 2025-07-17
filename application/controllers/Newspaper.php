<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Newspaper extends CI_Controller {

    public function __construct() {
        parent::__construct();
        is_logged_in();
        $this->load->library('form_validation');
        $this->load->model('other_model', 'other');
        $this->load->model('user/menu_model', 'menu');
        $this->load->model('user/newspaper_model', 'news');
    }

    public function index() {
        cek_menu_access();
        $data['htmlpagejs'] = 'none';
        $data['nmenu']      = 'Berita';
        $data['title']      = 'Berita';
        $data['namalabel']  = $data['title'];
        $data['auth']       = authUser();

        $data['datas']      = $this->news->get_data();

        $this->load->view('templates/header', $data);
        $this->load->view('templates/sidemenu', $data);
        $this->load->view('templates/sidenav', $data);
        $this->load->view('module/newspaper/index', $data);
        $this->load->view('templates/footer', $data);
        $this->load->view('templates/fscript-html-end', $data);
    }

    public function add() {
        cek_menu_access();
        $data['htmlpagejs'] = 'none';
        $data['nmenu']      = 'Berita';
        $data['title']      = 'Berita';
        $data['namalabel']  = $data['title'];
        $data['auth']       = authUser();
        
        if($data['auth']['tambah']!='y'){
            $this->session->set_flashdata('message', '<div class="me-3 ms-3 mt-3"><div class="alert alert-danger p-cg" role="alert">Tidak ada akses.</div></div>');
            redirect('newspaper/');
        }

        $this->load->view('templates/header', $data);
        $this->load->view('templates/sidemenu', $data);
        $this->load->view('templates/sidenav', $data);
        $this->load->view('module/newspaper/add', $data);
        $this->load->view('templates/footer', $data);
        $this->load->view('templates/fscript-html-end', $data);
    }

    public function add_proses() {
        cek_menu_access();
        $unama  = $this->input->post('nama');

        $this->form_validation->set_rules('nama', 'Nama', 'trim|required|xss_clean|htmlspecialchars');
        $this->form_validation->set_rules('status', 'Status', 'trim|required|xss_clean|htmlspecialchars');
        $this->form_validation->set_rules('berita', 'Isi Berita', 'trim|required');

        if ($this->form_validation->run() == false) {
            $this->session->set_flashdata('message', '<div class="alert alert-danger p-cg" role="alert">'.validation_errors().'</div>');
            redirect('newspaper/add');
        } else {
            $ceklogo = $_FILES['gambar']['name'];
            $upload = $this->other->upload_gambar('gambar','new','components','img_berita_');
            if($upload['result'] == "success" || $ceklogo==''){
                $res = $this->news->add_proses($ceklogo,$upload);
                if ($res==true) {
                    $this->session->set_flashdata('message', '<div class="me-3 ms-3 mt-3"><div class="alert alert-success p-cg" role="alert">Data berhasil disimpan.</div></div>');
                    redirect('newspaper');
                }else{
                    unlink(FCPATH.$upload['path'].$upload['file']['file_name']);
                    $this->session->set_flashdata('message', '<div class="alert alert-danger p-cg" role="alert">Proses gagal, silahkan coba lagi.</div>');
                    redirect('newspaper/add/');
                }
            }else{
                $this->session->set_flashdata('message', '<div class="alert alert-danger p-cg" role="alert">'.$upload['error'].'</div>');
                redirect('newspaper/add/');
            }
        }
    }

    public function edit($id = null) {
        cek_menu_access();
        if ($id==null) { redirect('newspaper'); }
        $check = $this->db->get_where('m_berita', ['berita_id' => $id]);
        if ($check->num_rows()==0) { 
            $this->session->set_flashdata('message', '<div class="me-3 ms-3 mt-3"><div class="alert alert-danger p-cg" role="alert">Data tidak ditemukan.</div></div>');
            redirect('newspaper'); 
        }

        $data['htmlpagejs'] = 'none';
        $data['nmenu']      = 'Berita';
        $data['title']      = 'Berita';
        $data['namalabel']  = $data['title'];
        $data['auth']       = authUser();
        
        if($data['auth']['edit']!='y'){
            $this->session->set_flashdata('message', '<div class="me-3 ms-3 mt-3"><div class="alert alert-danger p-cg" role="alert">Tidak ada akses.</div></div>');
            redirect('newspaper');
        }

        $data['edit'] = $check->row_array();

        $this->load->view('templates/header', $data);
        $this->load->view('templates/sidemenu', $data);
        $this->load->view('templates/sidenav', $data);
        $this->load->view('module/newspaper/edit', $data);
        $this->load->view('templates/footer', $data);
        $this->load->view('templates/fscript-html-end', $data);
    }

    public function edit_proses($id = null) {
        cek_menu_access();
        $unama  = $this->input->post('nama');

        if ($id==null) { redirect('newspaper'); }
        $check = $this->db->get_where('m_berita', ['berita_id' => $id]);
        $rowcheck = $check->row_array();
        if ($check->num_rows()==0) {
            $this->session->set_flashdata('message', '<div class="me-3 ms-3 mt-3"><div class="alert alert-danger p-cg" role="alert">Data tidak ditemukan.</div></div>');
            redirect('newspaper'); 
        }

        $this->form_validation->set_rules('nama', 'Nama', 'trim|required|xss_clean|htmlspecialchars');
        $this->form_validation->set_rules('status', 'Status', 'trim|required|xss_clean|htmlspecialchars');
        $this->form_validation->set_rules('berita', 'Isi Berita', 'trim|required');

        if ($this->form_validation->run() == false) {
            $this->session->set_flashdata('message', '<div class="alert alert-danger p-cg" role="alert">'.validation_errors().'</div>');
            redirect('newspaper/edit/'.$id);
        } else {
            $ceklogo = $_FILES['gambar']['name'];
            $upload = $this->other->upload_gambar('gambar',$rowcheck['gambar_berita'],'components','img_berita_');
            if($upload['result'] == "success" || $ceklogo==''){
                $res = $this->news->edit_proses($id,$ceklogo,$upload,$rowcheck['gambar_berita']);
                if ($res==true) {
                    $this->session->set_flashdata('message', '<div class="me-3 ms-3 mt-3"><div class="alert alert-success p-cg" role="alert">Data berhasil diperbarui.</div></div>');
                    redirect('newspaper');
                }else{
                    unlink(FCPATH.$upload['path'].$upload['file']['file_name']);
                    $this->session->set_flashdata('message', '<div class="alert alert-danger p-cg" role="alert">Proses gagal, silahkan coba lagi.</div>');
                    redirect('newspaper/edit/'.$id);
                }
            }else{
                $this->session->set_flashdata('message', '<div class="alert alert-danger p-cg" role="alert">'.$upload['error'].'</div>');
                redirect('newspaper/edit/'.$id);
            }
        }
    }

    public function hapus($id){
        cek_menu_access();

        $data['auth'] = authUser();
        if($data['auth']['hapus']!='y'){
            $this->session->set_flashdata('message', '<div class="me-3 ms-3 mt-3"><div class="alert alert-danger p-cg" role="alert">Tidak ada akses.</div></div>');
            redirect('newspaper');
        }

        $check = $this->db->get_where('m_berita', ['berita_id' => $id])->row_array();
        $res = $this->db->delete('m_berita', ['berita_id' => $id]);
        if ($res==true) {
            if (file_exists(FCPATH.$check['gambar_berita'])){
                unlink(FCPATH.$check['gambar_berita']);
            }
            $this->session->set_flashdata('message', '<div class="me-3 ms-3 mt-3"><div class="alert alert-success p-cg" role="alert">Data berhasil dihapus.</div></div>');
            redirect('newspaper');
        }else{
            $this->session->set_flashdata('message', '<div class="me-3 ms-3 mt-3"><div class="alert alert-danger p-cg" role="alert">Proses gagal, silahkan coba lagi.</div></div>');
            redirect('newspaper');
        }
    }

}
