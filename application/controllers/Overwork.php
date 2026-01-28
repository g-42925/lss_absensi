<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Overwork extends CI_Controller {

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
    public $rp;

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
        $data['nmenu']      = 'Data Request Izin';
        $data['title']      = 'Lembur';
        $data['namalabel']  = $data['title'];
        $data['auth']       = authUser();

        $companyId = $this->session->userdata('company_id');

        $data['data'] = $this->db->query("select * from employee_overwork eo join m_pegawai mp on eo.employee_id = mp.pegawai_id where mp.company_id = ? order by date desc ",[$companyId])->result_array();

        $this->load->view('templates/header', $data);
        $this->load->view('templates/sidemenu', $data);
        $this->load->view('templates/sidenav', $data);
        $this->load->view('module/overwork/index', $data);
        $this->load->view('templates/footer', $data);
        $this->load->view('templates/fscript-html-end', $data);
    }

    public function edit($employeeOverworkId){
        cek_menu_access();
        $data['htmlpagejs'] = 'none';
        $data['nmenu']      = 'Data Request Izin';
        $data['title']      = 'Lembur';
        $data['namalabel']  = $data['title'];
        $data['auth']       = authUser();

        $data['employeeOverworkId'] = $employeeOverworkId;

        $data['data'] = $this->db->query("select * from employee_overwork  where employee_overwork_id = ? order by date desc",[$employeeOverworkId])->row_array();

        $this->load->view('templates/header', $data);
        $this->load->view('templates/sidemenu', $data);
        $this->load->view('templates/sidenav', $data);
        $this->load->view('module/overwork/edit', $data);
        $this->load->view('templates/footer', $data);
        $this->load->view('templates/fscript-html-end', $data);
    }

    public function edit_proccess($employeeOverWorkId){
        $params = ['approved' => $this->input->post('approved')];

        $this->db->where('employee_overwork_id', $employeeOverWorkId);
        $q = $this->db->update('employee_overwork', $params);


        if($q){
          redirect('overwork');
        }
        else{
          $this->session->set_flashdata('message', '<div class="alert alert-danger p-cg" role="alert">proses gagal, silahkan coba lagi</div>');
          redirect('overwork/edit/'.$employeeOverWorkId.'?failed=true');
        }
    }
}
