<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Patterns_work extends CI_Controller {
    public $email;
    public $session;
    public $form_validation;
    public $upload;
    public $pagination;
    public $other;
    public $menu;
    public $patterns;

    public function __construct() {
        parent::__construct();
        is_logged_in();
        $this->load->library('form_validation');
        $this->load->model('other_model', 'other');
        $this->load->model('user/menu_model', 'menu');
        $this->load->model('user/patterns_model', 'patterns');
    }

    public function index() {
        cek_menu_access();
        $data['htmlpagejs'] = 'none';
        $data['nmenu']      = 'Pola Kerja';
        $data['title']      = 'Pola Kerja';
        $data['namalabel']  = $data['title'];
        $data['auth']       = authUser();

        $data['datas']      = $this->patterns->get_data();

        $this->load->view('templates/header', $data);
        $this->load->view('templates/sidemenu', $data);
        $this->load->view('templates/sidenav', $data);
        $this->load->view('module/patterns_work/index', $data);
        $this->load->view('templates/footer', $data);
        $this->load->view('templates/fscript-html-end', $data);
    }

    public function add() {
        cek_menu_access();
        $data['htmlpagejs'] = 'none';
        $data['nmenu']      = 'Pola Kerja';
        $data['title']      = 'Pola Kerja';
        $data['namalabel']  = $data['title'];
        $data['auth']       = authUser();
        
        if($data['auth']['tambah']!='y'){
            $this->session->set_flashdata('message', '<div class="me-3 ms-3 mt-3"><div class="alert alert-danger p-cg" role="alert">Tidak ada akses.</div></div>');
            redirect('patterns_work/');
        }

        $data['roles']      = $this->other->get_roles();
        $data['permission'] = $this->other->get_permission();

        $this->load->view('templates/header', $data);
        $this->load->view('templates/sidemenu', $data);
        $this->load->view('templates/sidenav', $data);
        $this->load->view('module/patterns_work/add', $data);
        $this->load->view('templates/footer', $data);
        $this->load->view('templates/fscript-html-end', $data);
    }

    public function add_proses() {
        cek_menu_access();
        $unama  = $this->input->post('nama');

        $this->form_validation->set_rules('nama', 'Nama', 'trim|required|xss_clean|htmlspecialchars');
        $this->form_validation->set_rules('tolet', 'Toleransi Telat', 'trim|xss_clean|htmlspecialchars');
        $this->form_validation->set_rules('jumlahhari', 'Jumlah Hari', 'trim|required|xss_clean|htmlspecialchars');
        $this->form_validation->set_rules('work[]', '', 'trim|xss_clean|htmlspecialchars');
        $this->form_validation->set_rules('sistemkerja[]', '', 'trim|xss_clean|htmlspecialchars');
        $this->form_validation->set_rules('masuk[]', '', 'trim|xss_clean|htmlspecialchars');
        $this->form_validation->set_rules('pulang[]', '', 'trim|xss_clean|htmlspecialchars');

        if ($this->form_validation->run() == false) {
            $this->session->set_flashdata('message', '<div class="alert alert-danger p-cg" role="alert">'.validation_errors().'</div>');
            redirect('patterns_work/add');
        } else {
            $query = $this->db->get_where('m_pola_kerja', ['nama_pola' => $unama, 'is_del' => 'n'])->num_rows();
            if ($query < 1) {
                $res = $this->patterns->add_proses();
                if ($res==true) {
                    $this->session->set_flashdata('message', '<div class="me-3 ms-3 mt-3"><div class="alert alert-success p-cg" role="alert">Data berhasil disimpan.</div></div>');
                    redirect('patterns_work');
                }else{
                    $this->session->set_flashdata('message', '<div class="alert alert-danger p-cg" role="alert">Proses gagal, silahkan coba lagi.</div>');
                    redirect('patterns_work/add');
                }
            } else {
                $this->session->set_flashdata('message', '<div class="alert alert-warning p-cg" role="alert">Proses gagal, nama pola <b>"'.$unama.'"</b> ini sudah digunakan.</div>');
                redirect('patterns_work/add');
            }
        }
    }

    public function edit($id = null) {
        cek_menu_access();
        if ($id==null) { redirect('patterns_work'); }
        $check = $this->db->get_where('m_pola_kerja', ['pola_kerja_id' => $id]);
        if ($check->num_rows()==0) { 
            $this->session->set_flashdata('message', '<div class="me-3 ms-3 mt-3"><div class="alert alert-danger p-cg" role="alert">Data tidak ditemukan.</div></div>');
            redirect('patterns_work'); 
        }

        $data['htmlpagejs'] = 'none';
        $data['nmenu']      = 'Pola Kerja';
        $data['title']      = 'Pola Kerja';
        $data['namalabel']  = $data['title'];
        $data['auth']       = authUser();
        
        if($data['auth']['edit']!='y'){
            $this->session->set_flashdata('message', '<div class="me-3 ms-3 mt-3"><div class="alert alert-danger p-cg" role="alert">Tidak ada akses.</div></div>');
            redirect('patterns_work/');
        }

        $data['edit']       = $check->row_array();
        $data['edit_pola']  = $this->db->get_where('m_pola_kerja_det', ['pola_kerja_id' => $data['edit']['pola_kerja_id']])->result_array();

        $this->load->view('templates/header', $data);
        $this->load->view('templates/sidemenu', $data);
        $this->load->view('templates/sidenav', $data);
        $this->load->view('module/patterns_work/edit', $data);
        $this->load->view('templates/footer', $data);
        $this->load->view('templates/fscript-html-end', $data);
    }

    public function edit_proses($id = null) {
        cek_menu_access();
        $unama  = $this->input->post('nama');

            if ($id==null) { redirect('patterns_work'); }
            $check = $this->db->get_where('m_pola_kerja', ['pola_kerja_id' => $id]);
            $rowcheck = $check->row_array();
            if ($check->num_rows()==0) {
                $this->session->set_flashdata('message', '<div class="me-3 ms-3 mt-3"><div class="alert alert-danger p-cg" role="alert">Data tidak ditemukan.</div></div>');
                redirect('patterns_work'); 
            }

            $this->form_validation->set_rules('nama', 'Nama', 'trim|required|xss_clean|htmlspecialchars');
            $this->form_validation->set_rules('tolet', 'Toleransi Telat', 'trim|xss_clean|htmlspecialchars');
            $this->form_validation->set_rules('jumlahhari', 'Jumlah Hari', 'trim|required|xss_clean|htmlspecialchars');
            $this->form_validation->set_rules('work[]', '', 'trim|xss_clean|htmlspecialchars');
            $this->form_validation->set_rules('sistemkerja[]', '', 'trim|xss_clean|htmlspecialchars');
            $this->form_validation->set_rules('masuk[]', '', 'trim|xss_clean|htmlspecialchars');
            $this->form_validation->set_rules('pulang[]', '', 'trim|xss_clean|htmlspecialchars');

            if ($this->form_validation->run() == false) {
                $this->session->set_flashdata('message', '<div class="alert alert-danger p-cg" role="alert">'.validation_errors().'</div>');
                redirect('patterns_work/edit/'.$id);
            } else {
                $query = $this->db->get_where('m_pola_kerja', ['nama_pola' => $unama, 'is_del' => 'n', 'pola_kerja_id!=' => $id])->num_rows();
                if ($query < 1) {
                    $res = $this->patterns->edit_proses($id);
                    if ($res==true) {
                        $this->session->set_flashdata('message', '<div class="me-3 ms-3 mt-3"><div class="alert alert-success p-cg" role="alert">Data berhasil disimpan.</div></div>');
                        redirect('patterns_work');
                    }else{
                        $this->session->set_flashdata('message', '<div class="alert alert-danger p-cg" role="alert">Proses gagal, silahkan coba lagi.</div>');
                        redirect('patterns_work/edit/'.$id);
                    }
                } else {
                    $this->session->set_flashdata('message', '<div class="alert alert-warning p-cg" role="alert">Proses gagal, pola kerja <b>"'.$unama.'"</b> ini sudah digunakan.</div>');
                    redirect('patterns_work/edit/'.$id);
                }
            }

    }

    public function hapus($id){
        cek_menu_access();
        
        $data['auth'] = authUser();
        if($data['auth']['hapus']!='y'){
            $this->session->set_flashdata('message', '<div class="me-3 ms-3 mt-3"><div class="alert alert-danger p-cg" role="alert">Tidak ada akses.</div></div>');
            redirect('patterns_work/');
        }
        $num = $this->db->query("SELECT * FROM m_pegawai_pola a JOIN m_pegawai b ON a.pegawai_id=b.pegawai_id WHERE a.pola_kerja_id='$id' AND b.is_del='n'")->num_rows();
        if ($num<=0) {
            $res = $this->other->hapus_data('m_pola_kerja','pola_kerja_id',$id);
            if ($res==true) {
                $this->session->set_flashdata('message', '<div class="me-3 ms-3 mt-3"><div class="alert alert-success p-cg" role="alert">Data berhasil dihapus.</div></div>');
            }else{
                $this->session->set_flashdata('message', '<div class="me-3 ms-3 mt-3"><div class="alert alert-danger p-cg" role="alert">Proses gagal, silahkan coba lagi.</div></div>');
            }
        }else{
            $this->session->set_flashdata('message', '<div class="me-3 ms-3 mt-3"><div class="alert alert-warning p-cg" role="alert">Data tidak bisa dihapus, karna masih ada '.$num.' data karyawan didalamnya.</div></div>');
        }
        redirect('patterns_work');

    }

    public function assign($id = null) {
        cek_menu_access();

        if ($id==null) { redirect('patterns_work'); }
        $check = $this->db->get_where('m_pola_kerja', ['pola_kerja_id' => $id]);
        if ($check->num_rows()==0) {
            $this->session->set_flashdata('message', '<div class="me-3 ms-3 mt-3"><div class="alert alert-danger p-cg" role="alert">Data tidak ditemukan.</div></div>');
            redirect('patterns_work'); 
        }

        $pola = $check->row_array();

        $data['htmlpagejs'] = 'none';
        $data['nmenu']      = 'Pola Kerja';
        $data['title']      = 'Pola Kerja';
        $data['namalabel']  = $pola['nama_pola'];
        $data['auth']       = authUser();
        $data['id']         = $id;

        $data['datas']      = $this->patterns->get_assign($id);
        $data['karyawan']   = $this->patterns->get_karyawan($id);

        $this->load->view('templates/header', $data);
        $this->load->view('templates/sidemenu', $data);
        $this->load->view('templates/sidenav', $data);
        $this->load->view('module/patterns_work/assign', $data);
        $this->load->view('templates/footer', $data);
        $this->load->view('templates/fscript-html-end', $data);
    }

    public function assign_proses($id = null) {
        cek_menu_access();
        
        $data['auth'] = authUser();
        if($data['auth']['tambah']!='y'){
            $this->session->set_flashdata('message', '<div class="me-3 ms-3 mt-3"><div class="alert alert-danger p-cg" role="alert">Tidak ada akses.</div></div>');
            redirect('patterns_work/assign/'.$id);
        }

        if ($id==null) { redirect('patterns_work'); }
        $check = $this->db->get_where('m_pola_kerja', ['pola_kerja_id' => $id]);
        if ($check->num_rows()==0) {
            $this->session->set_flashdata('message', '<div class="me-3 ms-3 mt-3"><div class="alert alert-danger p-cg" role="alert">Data tidak ditemukan.</div></div>');
            redirect('patterns_work'); 
        }

        $this->form_validation->set_rules('idp[]', 'Karyawan', 'trim|required|xss_clean|htmlspecialchars');
        $this->form_validation->set_rules('pola', 'Pola', 'trim|required|xss_clean|htmlspecialchars');
        $this->form_validation->set_rules('tglmulai', 'Tanggal', 'trim|required|xss_clean|htmlspecialchars');
        $this->form_validation->set_rules('harike', 'Hari', 'trim|required|xss_clean|htmlspecialchars');

        if ($this->form_validation->run() == false) {
            $this->session->set_flashdata('message', '<div class="me-3 ms-3 mt-3"><div class="alert alert-danger p-cg" role="alert">'.validation_errors().'</div></div>');
            redirect('patterns_work/assign/'.$id);
        } else {
            $res = $this->patterns->assign_proses($id);
            if ($res==true) {
                $this->session->set_flashdata('message', '<div class="me-3 ms-3 mt-3"><div class="alert alert-success p-cg" role="alert">Data berhasil disimpan.</div></div>');
                redirect('patterns_work/assign/'.$id);
            }else{
                $this->session->set_flashdata('message', '<div class="me-3 ms-3 mt-3"><div class="alert alert-danger p-cg" role="alert">Proses gagal, silahkan coba lagi.</div></div>');
                redirect('patterns_work/assign/'.$id);
            }
        }
    }

    public function hapus_assign($id = null, $idx = null){
        cek_menu_access();
        
        $data['auth'] = authUser();
        if($data['auth']['hapus']!='y'){
            $this->session->set_flashdata('message', '<div class="me-3 ms-3 mt-3"><div class="alert alert-danger p-cg" role="alert">Tidak ada akses.</div></div>');
            redirect('patterns_work/assign/'.$id);
        }

        if ($id==null || $idx==null) { redirect('patterns_work/assign/'.$id); }
        $check = $this->db->get_where('m_pegawai_pola', ['pegawai_pola_id' => $idx]);
        if ($check->num_rows()==0) {
            $this->session->set_flashdata('message', '<div class="me-3 ms-3 mt-3"><div class="alert alert-danger p-cg" role="alert">Data tidak ditemukan.</div></div>');
            redirect('patterns_work/assign/'.$id); 
        }
        $check = $check->row_array();
        $res = $this->db->delete('m_pegawai_pola', ['pegawai_pola_id' => $idx]);
        
        $check = $this->db->order_by('pegawai_pola_id', 'DESC')->get_where('m_pegawai_pola', ['pegawai_id' => $check['pegawai_id']]);

        if ($check->num_rows()>0) {
            $check = $check->row_array();
            $this->db->set(['is_selected' => 'y']);
            $this->db->where('pegawai_pola_id', $check['pegawai_pola_id']);
            $this->db->update('m_pegawai_pola');
        }

        if ($res==true) {
            $this->session->set_flashdata('message', '<div class="me-3 ms-3 mt-3"><div class="alert alert-success p-cg" role="alert">Data berhasil dihapus.</div></div>');
            redirect('patterns_work/assign/'.$id);
        }else{
            $this->session->set_flashdata('message', '<div class="me-3 ms-3 mt-3"><div class="alert alert-danger p-cg" role="alert">Proses gagal, silahkan coba lagi.</div></div>');
            redirect('patterns_work/assign/'.$id);
        }
    }


}
