<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Task extends CI_Controller {
    public $email;
    public $session;
    public $form_validation;
    public $upload;
    public $pagination;
    public $other;
    public $menu;
    public $rp;

    public function __construct() {
        parent::__construct();
        is_logged_in();
        $this->load->library('form_validation');
        $this->load->model('other_model', 'other');
        $this->load->model('user/menu_model', 'menu');
        $this->load->model('user/req_permission_model', 'rp');
    }
    
    public function filter($date){
        $data['htmlpagejs'] = 'none';
        $data['nmenu']      = 'Tugas';
        $data['title']      = 'Tugas';
        $data['namalabel']  = $data['title'];
        $data['auth']       = authUser(); 
        
        $companyId = $this->session->userdata('company_id');
        
        $data['data'] = $this->db->query("select * from task t join task_detail td on t.task_id = td.task_id join m_pegawai mp on t.employee_id = mp.pegawai_id where mp.company_id = ? and t.date = ? order by t.created_at desc",[$companyId,$date])->result_array();
        
        $this->load->view('templates/header', $data);
        $this->load->view('templates/sidemenu', $data);
        $this->load->view('templates/sidenav', $data);
        $this->load->view('module/task/index',$data);
        $this->load->view('templates/footer', $data);
        $this->load->view('templates/fscript-html-end', $data);
    }

    public function index() {
        cek_menu_access();
        $data['htmlpagejs'] = 'none';
        $data['nmenu']      = 'Tugas';
        $data['title']      = 'Tugas';
        $data['namalabel']  = $data['title'];
        $data['auth']       = authUser();

	    $companyId = $this->session->userdata('company_id');

    	$data['data'] = $this->db->query("select * from task t join task_detail td on t.task_id = td.task_id join m_pegawai mp on t.employee_id = mp.pegawai_id where mp.company_id = ? order by date desc",[$companyId])->result_array();

        $this->load->view('templates/header', $data);
        $this->load->view('templates/sidemenu', $data);
        $this->load->view('templates/sidenav', $data);
        $this->load->view('module/task/index',$data);
        $this->load->view('templates/footer', $data);
        $this->load->view('templates/fscript-html-end', $data);
    }
}
