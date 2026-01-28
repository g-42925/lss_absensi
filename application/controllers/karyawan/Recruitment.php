<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Recruitment extends CI_Controller {

    public $email;
    public $session;
    public $form;
    public $upload;
    public $pagination;
    public $other;
    public $menu;
    public $data;
    public $patterns;
    public $tw;
    public $form_validation;
    


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
        $data['title']      = 'Recruitment';
        $data['namalabel']  = 'Pengaturan '.$data['title'].' Karyawan';
        $data['auth']       = authUser();

        $companyId = $this->session->userdata('company_id');

        $data['page'] = filter_var($this->input->get('page'));

        $data['job'] = $this->db->query("select * from job j join position p on p.id = j.position_id where p.company_id = ?",[$companyId])->result_array();

        $data['candidate'] = $this->db->query("select * from candidate where company_id = ?",[$companyId])->result_array();

        $data['interview'] = $this->db->query("select * from interview i join candidate c on i.candidate_id = c.candidate_id join position p on c.position_id = p.id where c.company_id = ?",[$companyId])->result_array();
        
        $this->load->view('templates/header', $data);
        $this->load->view('templates/sidemenu', $data);
        $this->load->view('templates/sidenav', $data);
        $this->load->view('module/karyawan/recruitment/index',$data);
        $this->load->view('templates/footer', $data);
        $this->load->view('templates/fscript-html-end', $data);
    }
}
