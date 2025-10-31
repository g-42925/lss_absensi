<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Job extends CI_Controller {

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

        $data['data'] = $this->db->query("select * from job j join position p on j.position_id = p.id where p.company_id = ?",[$companyId])->result_array();

        $this->load->view('templates/header', $data);
        $this->load->view('templates/sidemenu', $data);
        $this->load->view('templates/sidenav', $data);
        $this->load->view('module/job/index',$data);
        $this->load->view('templates/footer', $data);
        $this->load->view('templates/fscript-html-end', $data);
    }


    public function add() {
        cek_menu_access();
        $data['htmlpagejs'] = 'none';
        $data['nmenu']      = 'Karyawan';
        $data['title']      = 'Recruitment';
        $data['namalabel']  = 'Pengaturan '.$data['title'].' Karyawan';
        $data['auth']       = authUser();

        $companyId = $this->session->userdata('company_id');

        $data['failed'] = filter_var($this->input->get('failed'),FILTER_VALIDATE_BOOLEAN);

        $data['positions'] = $this->db->query("select * from position where company_id = ?",[$companyId])->result_array();

        $this->load->view('templates/header', $data);
        $this->load->view('templates/sidemenu', $data);
        $this->load->view('templates/sidenav', $data);
        $this->load->view('module/job/add',$data);
        $this->load->view('templates/footer', $data);
        $this->load->view('templates/fscript-html-end', $data);
    }

    public function edit($jobId){
        cek_menu_access();
        $data['htmlpagejs'] = 'none';
        $data['nmenu']      = 'Karyawan';
        $data['title']      = 'Recruitment';
        $data['namalabel']  = 'Pengaturan '.$data['title'].' Karyawan';
        $data['auth']       = authUser();

        $data['jobId'] = $jobId;

        $companyId = $this->session->userdata('company_id');

        $data['failed'] = filter_var($this->input->get('failed'),FILTER_VALIDATE_BOOLEAN);

        $data['positions'] = $this->db->query("select * from position where company_id = ?",[$companyId])->result_array();

        $data['job'] = $this->db->query("select * from job where job_id = ?",[$jobId])->row_array();

        $this->load->view('templates/header', $data);
        $this->load->view('templates/sidemenu', $data);
        $this->load->view('templates/sidenav', $data);
        $this->load->view('module/job/edit',$data);
        $this->load->view('templates/footer', $data);
        $this->load->view('templates/fscript-html-end', $data);
    }

    public function edit_proccess($jobId){
      $data = [
        'position_id' => $this->input->post('position'),
        'accepted_for' => $this->input->post('amt'),
        'closed' => $this->input->post('closed')
      ];

      $this->db->set(
        $data
      );
      $this->db->where(
        'job_id', 
        $jobId
      );
      $q = $this->db->update(
        'job'
      );

      if($q){
        redirect('job');
      }
      else{
        $this->session->set_flashdata('message', '<div class="alert alert-danger p-cg" role="alert">proses gagal, silahkan coba lagi</div>');
        redirect('job/edit/'.$jobId.'?failed=true');
      }
    }

    public function add_proccess(){
      $params = [
        'job_id' => uniqid(),
        'position_id' => $this->input->post('position'),
        'accepted_for' => $this->input->post('amt'),
        'closed' => false
      ];

      $q = $this->db->insert(
        'job',
        $params
      );

      if(!$q){
        $this->session->set_flashdata(
          'message','<div class="alert alert-danger">Proses gagal. Silakan coba lagi.</div>'
        );
        redirect(
          'job/add?failed=true'
        );
      }
      else{
        redirect(
          'karyawan/recruitment'
        );
      }
    }

    public function delete($id){
      $this->db->where('job_id', $id);
      $this->db->delete('job');
      redirect('job');
    }
}
