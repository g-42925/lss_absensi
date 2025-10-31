<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Interview extends CI_Controller {

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

    public function add(){
        cek_menu_access();
        $data['htmlpagejs'] = 'none';
        $data['nmenu']      = 'Karyawan';
        $data['title']      = 'Recruitment';
        $data['namalabel']  = 'Pengaturan '.$data['title'].' Karyawan';
        $data['auth']       = authUser();

        $companyId = $this->session->userdata('company_id');

        $data['failed'] = filter_var($this->input->get('failed'),FILTER_VALIDATE_BOOLEAN);

        $data['candidate'] = $this->db->query("select * from candidate where company_id = ?",[$companyId])->result_array();

        $data['job'] = $this->db->query("select * from job j join position p on j.position_id = p.id where p.company_id = ? and j.accepted_for > 0 and j.closed = 0",[$companyId])->result_array();

        $this->load->view('templates/header', $data);
        $this->load->view('templates/sidemenu', $data);
        $this->load->view('templates/sidenav', $data);
        $this->load->view('module/interview/add',$data);
        $this->load->view('templates/footer', $data);
        $this->load->view('templates/fscript-html-end', $data);
    }

    public function add_proccess(){        
        $this->form_validation->set_rules('date', 'Date', 'required');

        $candidate = $this->input->post('candidate_id');

        $job = $this->input->post('job_id');

        if ($this->form_validation->run() == false) {
            $this->session->set_flashdata(
                'message','<div class="alert alert-danger">Proses gagal. Silakan coba lagi.</div>'
            );
            redirect(
                'interview/add?failed=true'
            );
        } 
        else{
            $params = [
                'interview_id' => uniqid(),
                'candidate_id' => $candidate,
                'job_id' => $this->input->post('job_id'),
                'date' => $this->input->post('date')
            ];

            $q = $this->db->insert(
                'interview',
                $params
            );

            if(!$q){
                $this->session->set_flashdata(
                    'message','<div class="alert alert-danger">Proses gagal. Silakan coba lagi.</div>'
                );
                redirect(
                    'interview/add?failed=true'
                );
            }
            else{
                redirect(
                    'karyawan/recruitment'
                );
            }
        }
    }

    public function index() {
        cek_menu_access();
        $data['htmlpagejs'] = 'none';
        $data['nmenu']      = 'Karyawan';
        $data['title']      = 'Recruitment';
        $data['namalabel']  = 'Pengaturan '.$data['title'].' Karyawan';
        $data['auth']       = authUser();

        $companyId = $this->session->userdata('company_id');

        $data['data'] = $this->db->query("select * from interview i join candidate c on i.candidate_id = c.candidate_id join job j on j.job_id = i.job_id join position p on p.id = j.position_id where c.company_id = ?",[$companyId])->result_array();
 
        $this->load->view('templates/header', $data);
        $this->load->view('templates/sidemenu', $data);
        $this->load->view('templates/sidenav', $data);
        $this->load->view('module/interview/index',$data);
        $this->load->view('templates/footer', $data);
        $this->load->view('templates/fscript-html-end', $data);
    }
}
