<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Sliders extends CI_Controller {

    public function __construct() {
        parent::__construct();
        is_logged_in();
        $this->load->library('form_validation');
        $this->load->model('other_model', 'other');
        $this->load->model('user/menu_model', 'menu');
        $this->load->model('user/sliders_model', 'news');
    }

    public function index() {
        cek_menu_access();
        $data['htmlpagejs'] = 'none';
        $data['nmenu']      = 'Slider';
        $data['title']      = 'Slider';
        $data['namalabel']  = $data['title'];
        $data['auth']       = authUser();

        $data['datas']      = $this->news->get_data();

        $this->load->view('templates/header', $data);
        $this->load->view('templates/sidemenu', $data);
        $this->load->view('templates/sidenav', $data);
        $this->load->view('module/slider/index', $data);
        $this->load->view('templates/footer', $data);
        $this->load->view('templates/fscript-html-end', $data);
    }

    public function add() {
        cek_menu_access();
        $data['htmlpagejs'] = 'none';
        $data['nmenu']      = 'Slider';
        $data['title']      = 'Slider';
        $data['namalabel']  = $data['title'];
        $data['auth']       = authUser();
        
        if($data['auth']['tambah']!='y'){
            $this->session->set_flashdata('message', '<div class="me-3 ms-3 mt-3"><div class="alert alert-danger p-cg" role="alert">Tidak ada akses.</div></div>');
            redirect('sliders/');
        }

        $this->load->view('templates/header', $data);
        $this->load->view('templates/sidemenu', $data);
        $this->load->view('templates/sidenav', $data);
        $this->load->view('module/slider/add', $data);
        $this->load->view('templates/footer', $data);
        $this->load->view('templates/fscript-html-end', $data);
    }

    public function add_proses() {
        cek_menu_access();
        $tipe  = $this->input->post('tipe');

        $this->form_validation->set_rules('tipe', 'Tipe', 'trim|required|xss_clean|htmlspecialchars');
        $this->form_validation->set_rules('status', 'Status', 'trim|required|xss_clean|htmlspecialchars');
        if($tipe==1){
            $this->form_validation->set_rules('ilink', 'Link IG Post', 'trim|required|xss_clean|htmlspecialchars');
        }else{
            $this->form_validation->set_rules('gambar', 'Gambar', 'trim|xss_clean|htmlspecialchars');
        }

        if ($this->form_validation->run() == false) {
            $this->session->set_flashdata('message', '<div class="alert alert-danger p-cg" role="alert">'.validation_errors().'</div>');
            redirect('sliders/add');
        } else {
            if($tipe==1){
                $res = $this->news->add_proses();
                if ($res==true) {
                    $this->session->set_flashdata('message', '<div class="me-3 ms-3 mt-3"><div class="alert alert-success p-cg" role="alert">Data berhasil disimpan.</div></div>');
                    redirect('sliders');
                }else{
                    $this->session->set_flashdata('message', '<div class="alert alert-danger p-cg" role="alert">Proses gagal, silahkan coba lagi.</div>');
                    redirect('sliders/add/');
                }
            }else{
                $ceklogo = $_FILES['gambar']['name'];
                $upload = $this->other->upload_gambar('gambar','new','components','img_slider_');
                if($upload['result'] == "success"){
                    $res = $this->news->add_proses($upload);
                    if ($res==true) {
                        $this->session->set_flashdata('message', '<div class="me-3 ms-3 mt-3"><div class="alert alert-success p-cg" role="alert">Data berhasil disimpan.</div></div>');
                        redirect('sliders');
                    }else{
                        unlink(FCPATH.$upload['path'].$upload['file']['file_name']);
                        $this->session->set_flashdata('message', '<div class="alert alert-danger p-cg" role="alert">Proses gagal, silahkan coba lagi.</div>');
                        redirect('sliders/add/');
                    }
                }else{
                    $this->session->set_flashdata('message', '<div class="alert alert-danger p-cg" role="alert">'.$upload['error'].'</div>');
                    redirect('sliders/add/');
                }
            }
        }
    }

    public function edit($id = null) {
        cek_menu_access();
        if ($id==null) { redirect('sliders'); }
        $check = $this->db->get_where('m_slider', ['slider_id' => $id]);
        if ($check->num_rows()==0) { 
            $this->session->set_flashdata('message', '<div class="me-3 ms-3 mt-3"><div class="alert alert-danger p-cg" role="alert">Data tidak ditemukan.</div></div>');
            redirect('sliders'); 
        }

        $data['htmlpagejs'] = 'none';
        $data['nmenu']      = 'Slider';
        $data['title']      = 'Slider';
        $data['namalabel']  = $data['title'];
        $data['auth']       = authUser();
        
        if($data['auth']['edit']!='y'){
            $this->session->set_flashdata('message', '<div class="me-3 ms-3 mt-3"><div class="alert alert-danger p-cg" role="alert">Tidak ada akses.</div></div>');
            redirect('sliders');
        }

        $data['edit'] = $check->row_array();

        $this->load->view('templates/header', $data);
        $this->load->view('templates/sidemenu', $data);
        $this->load->view('templates/sidenav', $data);
        $this->load->view('module/slider/edit', $data);
        $this->load->view('templates/footer', $data);
        $this->load->view('templates/fscript-html-end', $data);
    }

    public function edit_proses($id = null) {
        cek_menu_access();
        $tipe  = $this->input->post('tipe');

        if ($id==null) { redirect('sliders'); }
        $check = $this->db->get_where('m_slider', ['slider_id' => $id]);
        $rowcheck = $check->row_array();
        if ($check->num_rows()==0) {
            $this->session->set_flashdata('message', '<div class="me-3 ms-3 mt-3"><div class="alert alert-danger p-cg" role="alert">Data tidak ditemukan.</div></div>');
            redirect('sliders'); 
        }

        $this->form_validation->set_rules('tipe', 'Tipe', 'trim|required|xss_clean|htmlspecialchars');
        $this->form_validation->set_rules('status', 'Status', 'trim|required|xss_clean|htmlspecialchars');
        if($tipe==1){
            $this->form_validation->set_rules('ilink', 'Link IG Post', 'trim|required|xss_clean|htmlspecialchars');
        }else{
            $this->form_validation->set_rules('gambar', 'Gambar', 'trim|xss_clean|htmlspecialchars');
        }

        if ($this->form_validation->run() == false) {
            $this->session->set_flashdata('message', '<div class="alert alert-danger p-cg" role="alert">'.validation_errors().'</div>');
            redirect('sliders/edit/'.$id);
        } else {
            if($tipe==1){
                $res = $this->news->edit_proses($id);
                if ($res==true) {
                    $this->session->set_flashdata('message', '<div class="me-3 ms-3 mt-3"><div class="alert alert-success p-cg" role="alert">Data berhasil diperbarui.</div></div>');
                    redirect('sliders');
                }else{
                    $this->session->set_flashdata('message', '<div class="alert alert-danger p-cg" role="alert">Proses gagal, silahkan coba lagi.</div>');
                    redirect('sliders/edit/'.$id);
                }
            }else{
                $ceklogo = $_FILES['gambar']['name'];
                $upload = $this->other->upload_gambar('gambar',$rowcheck['gambar_slider'],'components','img_slider_');
                if($upload['result'] == "success" || ($ceklogo=='' && $rowcheck['is_tipe']=='2')){
                    $res = $this->news->edit_proses($id,$ceklogo,$upload,$rowcheck['gambar_slider']);
                    if ($res==true) {
                        $this->session->set_flashdata('message', '<div class="me-3 ms-3 mt-3"><div class="alert alert-success p-cg" role="alert">Data berhasil diperbarui.</div></div>');
                        redirect('sliders');
                    }else{
                        unlink(FCPATH.$upload['path'].$upload['file']['file_name']);
                        $this->session->set_flashdata('message', '<div class="alert alert-danger p-cg" role="alert">Proses gagal, silahkan coba lagi.</div>');
                        redirect('sliders/edit/'.$id);
                    }
                }else{
                    $this->session->set_flashdata('message', '<div class="alert alert-danger p-cg" role="alert">'.$upload['error'].'</div>');
                    redirect('sliders/edit/'.$id);
                }
            }
        }
    }

    public function hapus($id){
        cek_menu_access();

        $data['auth'] = authUser();
        if($data['auth']['hapus']!='y'){
            $this->session->set_flashdata('message', '<div class="me-3 ms-3 mt-3"><div class="alert alert-danger p-cg" role="alert">Tidak ada akses.</div></div>');
            redirect('sliders');
        }

        $check = $this->db->get_where('m_slider', ['slider_id' => $id])->row_array();
        $res = $this->db->delete('m_slider', ['slider_id' => $id]);
        if ($res==true) {
            if($check['is_tipe']=='2'){
                if (file_exists(FCPATH.$check['gambar_slider'])){
                    unlink(FCPATH.$check['gambar_slider']);
                }
            }
            $this->session->set_flashdata('message', '<div class="me-3 ms-3 mt-3"><div class="alert alert-success p-cg" role="alert">Data berhasil dihapus.</div></div>');
            redirect('sliders');
        }else{
            $this->session->set_flashdata('message', '<div class="me-3 ms-3 mt-3"><div class="alert alert-danger p-cg" role="alert">Proses gagal, silahkan coba lagi.</div></div>');
            redirect('sliders');
        }
    }

}
