<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Overtime extends CI_Controller {

    public function __construct() {
        parent::__construct();
        is_logged_in();
        $this->load->library('form_validation');
        $this->load->model('other_model', 'other');
        $this->load->model('user/menu_model', 'menu');
        $this->load->model('user/Overtime_model', 'rp');
    }

    public function index() {
        cek_menu_access();
        $data['htmlpagejs'] = 'none';
        $data['nmenu']      = 'Data Lembur';
        $data['title']      = 'Data Lembur';
        $data['namalabel']  = $data['title'];
        $data['auth']       = authUser();

        $data['datas']      = $this->rp->get_data();

        $this->load->view('templates/header', $data);
        $this->load->view('templates/sidemenu', $data);
        $this->load->view('templates/sidenav', $data);
        $this->load->view('module/overtime/index', $data);
        $this->load->view('templates/footer', $data);
        $this->load->view('templates/fscript-html-end', $data);
    }

    public function add() {
        cek_menu_access();
        $data['htmlpagejs'] = 'none';
        $data['nmenu']      = 'Data Lembur';
        $data['title']      = 'Data Lembur';
        $data['namalabel']  = $data['title'];
        $data['auth']       = authUser();
        
        if($data['auth']['tambah']!='y'){
            $this->session->set_flashdata('message', '<div class="me-3 ms-3 mt-3"><div class="alert alert-danger p-cg" role="alert">Tidak ada akses.</div></div>');
            redirect('overtime/');
        }

        $data['karyawan']   = dataKaryawan();
        $data['thismonth'] = date('Y-m-t');

        $this->load->view('templates/header', $data);
        $this->load->view('templates/sidemenu', $data);
        $this->load->view('templates/sidenav', $data);
        $this->load->view('module/overtime/add', $data);
        $this->load->view('templates/footer', $data);
        $this->load->view('templates/fscript-html-end', $data);
    }

    public function add_proses() {
        cek_menu_access();
        $ukat  = $this->input->post('idp');
        $unama  = $this->input->post('tgl1');

        $this->form_validation->set_rules('idp[]', 'Karyawan', 'trim|required|xss_clean|htmlspecialchars');
        $this->form_validation->set_rules('tgl1', 'Tanggal', 'trim|required|xss_clean|htmlspecialchars');
        $this->form_validation->set_rules('jmasuk', 'Masuk', 'trim|required|xss_clean|htmlspecialchars');
        $this->form_validation->set_rules('jkeluar', 'Keluar', 'trim|required|xss_clean|htmlspecialchars');

        $this->form_validation->set_rules('amasuk', 'Absen Masuk', 'trim|xss_clean|htmlspecialchars');
        $this->form_validation->set_rules('akeluar', 'Absen Keluar', 'trim|xss_clean|htmlspecialchars');
        $this->form_validation->set_rules('catatanhl', 'Catatan Hasil Lembur', 'trim|xss_clean|htmlspecialchars');

        if ($this->form_validation->run() == false) {
            $this->session->set_flashdata('message', '<div class="alert alert-danger p-cg" role="alert">'.validation_errors().'</div>');
            redirect('overtime/add');
        } else {
            $res = $this->rp->add_proses();
            if ($res==true) {
                $this->session->set_flashdata('message', '<div class="me-3 ms-3 mt-3"><div class="alert alert-success p-cg" role="alert">Data berhasil disimpan.</div></div>');
                redirect('overtime');
            }else{
                $this->session->set_flashdata('message', '<div class="alert alert-danger p-cg" role="alert">Proses gagal, silahkan coba lagi.</div>');
                redirect('overtime/add');
            }
        }
    }

    public function edit($id = null) {
        cek_menu_access();
        if ($id==null) { redirect('overtime'); }
        $check = $this->db->get_where('tx_lembur', ['lembur_id' => $id]);
        if ($check->num_rows()==0) { 
            $this->session->set_flashdata('message', '<div class="me-3 ms-3 mt-3"><div class="alert alert-danger p-cg" role="alert">Data tidak ditemukan.</div></div>');
            redirect('overtime'); 
        }

        $data['htmlpagejs'] = 'none';
        $data['nmenu']      = 'Data Lembur';
        $data['title']      = 'Data Lembur';
        $data['namalabel']  = $data['title'];
        $data['auth']       = authUser();
        
        if($data['auth']['edit']!='y'){
            $this->session->set_flashdata('message', '<div class="me-3 ms-3 mt-3"><div class="alert alert-danger p-cg" role="alert">Tidak ada akses.</div></div>');
            redirect('overtime/');
        }

        $data['karyawan']   = $this->rp->get_karyawan($id);
        $data['edit']       = $check->row_array();
        $data['thismonth'] = date('Y-m-t');

        $this->load->view('templates/header', $data);
        $this->load->view('templates/sidemenu', $data);
        $this->load->view('templates/sidenav', $data);
        $this->load->view('module/overtime/edit', $data);
        $this->load->view('templates/footer', $data);
        $this->load->view('templates/fscript-html-end', $data);
    }

    public function edit_proses($id = null) {
        cek_menu_access();

        if ($id==null) { redirect('overtime'); }
        $check = $this->db->get_where('tx_lembur', ['lembur_id' => $id]);
        if ($check->num_rows()==0) {
            $this->session->set_flashdata('message', '<div class="me-3 ms-3 mt-3"><div class="alert alert-danger p-cg" role="alert">Data tidak ditemukan.</div></div>');
            redirect('overtime'); 
        }

        $ukat  = $this->input->post('idp');
        $unama  = $this->input->post('tgl1');

        $this->form_validation->set_rules('idp[]', 'Karyawan', 'trim|required|xss_clean|htmlspecialchars');
        $this->form_validation->set_rules('tgl1', 'Tanggal', 'trim|required|xss_clean|htmlspecialchars');
        $this->form_validation->set_rules('jmasuk', 'Masuk', 'trim|required|xss_clean|htmlspecialchars');
        $this->form_validation->set_rules('jkeluar', 'Keluar', 'trim|required|xss_clean|htmlspecialchars');
        
        $this->form_validation->set_rules('amasuk', 'Absen Masuk', 'trim|xss_clean|htmlspecialchars');
        $this->form_validation->set_rules('akeluar', 'Absen Keluar', 'trim|xss_clean|htmlspecialchars');
        $this->form_validation->set_rules('catatanhl', 'Catatan Hasil Lembur', 'trim|xss_clean|htmlspecialchars');
        $this->form_validation->set_rules('status', 'Status', 'trim|required|xss_clean|htmlspecialchars');

        if ($this->form_validation->run() == false) {
            $this->session->set_flashdata('message', '<div class="alert alert-danger p-cg" role="alert">'.validation_errors().'</div>');
            redirect('overtime/edit/'.$id);
        } else {
            $res = $this->rp->edit_proses($id);
            if ($res==true) {
                $this->session->set_flashdata('message', '<div class="me-3 ms-3 mt-3"><div class="alert alert-success p-cg" role="alert">Data berhasil disimpan.</div></div>');
                redirect('overtime');
            }else{
                $this->session->set_flashdata('message', '<div class="alert alert-danger p-cg" role="alert">Proses gagal, silahkan coba lagi.</div>');
                redirect('overtime/edit/'.$id);
            }
        }
    }

    public function action($id) {
        cek_menu_access();
        $data['datal'] = $this->db->get_where('tx_lembur', ['lembur_id' => $id])->row_array();
        $data['userupd'] = dataUser($data['datal']['is_acc_updated']);
        $data['result'] = $this->rp->detail_user_lembur($id);
        $this->load->view('module/overtime/option', $data);
    }

    public function hapus($id = null){
        cek_menu_access();
        
        $data['auth']       = authUser();
        if($data['auth']['hapus']!='y'){
            $this->session->set_flashdata('message', '<div class="me-3 ms-3 mt-3"><div class="alert alert-danger p-cg" role="alert">Tidak ada akses.</div></div>');
            redirect('overtime/');
        }

        if ($id==null) { redirect('overtime'); }
        $check = $this->db->get_where('tx_lembur', ['lembur_id' => $id]);
        if ($check->num_rows()==0) {
            $this->session->set_flashdata('message', '<div class="me-3 ms-3 mt-3"><div class="alert alert-danger p-cg" role="alert">Data tidak ditemukan.</div></div>');
            redirect('overtime'); 
        }

        $res = $this->db->delete('tx_lembur', ['lembur_id' => $id]);
        $res = $this->db->delete('tx_lembur_pegawai', ['lembur_id' => $id]);
        if ($res==true) {
            $this->session->set_flashdata('message', '<div class="me-3 ms-3 mt-3"><div class="alert alert-success p-cg" role="alert">Data berhasil dihapus.</div></div>');
            redirect('overtime');
        }else{
            $this->session->set_flashdata('message', '<div class="me-3 ms-3 mt-3"><div class="alert alert-danger p-cg" role="alert">Proses gagal, silahkan coba lagi.</div></div>');
            redirect('overtime');
        }
    }

}
