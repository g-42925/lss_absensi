<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Off extends CI_Controller {
    public function __construct() {
        parent::__construct();
        is_logged_in();
        $this->load->library('form_validation');
        $this->load->model('other_model', 'other');
        $this->load->model('user/menu_model', 'menu');
        $this->load->model('user/attendance_model', 'att');
    }

    public $email;
    public $session;
    public $form_validation;
    public $upload;
    public $pagination;
    public $other;
    public $menu;
    public $att;

    public function index() {
        $data['htmlpagejs'] = 'none';
        $data['nmenu']      = 'Off Days';
        $data['title']      = 'Off Days';
        $data['namalabel']  = $data['title'];
        $data['auth']       = authUser();

        $data['offdays']    = $this->db->query("SELECT * FROM tx_cuti_bersama")->result_array();

        $this->load->view('templates/header',$data);
        $this->load->view('templates/sidemenu',$data);
        $this->load->view('templates/sidenav');
        $this->load->view('module/off/index',$data);
        $this->load->view('templates/footer');
        $this->load->view('templates/fscript-html-end');
    }

    public function add(){
        $data['htmlpagejs'] = 'none';
        $data['nmenu']      = 'Add Off Day';
        $data['title']      = 'Add Off Day';
        $data['namalabel']  = $data['title'];
        $data['auth']       = authUser();

        $this->load->view('templates/header',$data);
        $this->load->view('templates/sidemenu',$data);
        $this->load->view('templates/sidenav');
        $this->load->view('module/off/add',$data);
        $this->load->view('templates/footer');
        $this->load->view('templates/fscript-html-end');       
    }

    public function add_proses(){
        $this->db->insert('tx_cuti_bersama', [
            'tanggal' => $this->input->post('tanggal'),
            'keterangan' => $this->input->post('keterangan')
        ]);
        redirect('off');
    }

    public function edit($id){
        $data['htmlpagejs'] = 'none';
        $data['nmenu']      = 'Edit Off Day';
        $data['title']      = 'Edit Off Day';
        $data['namalabel']  = $data['title'];
        $data['auth']       = authUser();
        $data['data']       = $this->db->query("SELECT * FROM tx_cuti_bersama where id='$id'")->row_array();
        $this->load->view('templates/header',$data);
        $this->load->view('templates/sidemenu',$data);
        $this->load->view('templates/sidenav');
        $this->load->view('module/off/edit',$data);
        $this->load->view('templates/footer');
        $this->load->view('templates/fscript-html-end');    
    }

    public function edit_proses(){
        $id = $this->input->post('id');
        $this->db->where('id', $id);
        $this->db->update('tx_cuti_bersama', [
            'tanggal' => $this->input->post('tanggal'),
            'keterangan' => $this->input->post('keterangan')
        ]);
        redirect('off');
    }

    public function delete($id){
        $this->db->where('id', $id);
        $this->db->delete('tx_cuti_bersama');
        redirect('off');
    }
}
