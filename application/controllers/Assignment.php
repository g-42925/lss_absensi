<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Assignment extends CI_Controller {
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

    public function index() {
        cek_menu_access();
        $data['htmlpagejs'] = 'none';
        $data['nmenu']      = 'Penugasan';
        $data['title']      = '';
        $data['namalabel']  = $data['title'];
        $data['auth']       = authUser();

        $companyId = $this->session->userdata('company_id');

        $data['data'] = $this->db->query("select * from assignment a join m_pegawai mp on a.employee_id = mp.pegawai_id where mp.company_id = ? order by a.created_at desc",[$companyId])->result_array();

        $this->load->view('templates/header', $data);
        $this->load->view('templates/sidemenu', $data);
        $this->load->view('templates/sidenav', $data);
        $this->load->view('module/assignment/index', $data);
        $this->load->view('templates/footer', $data);
        $this->load->view('templates/fscript-html-end', $data);
    }

    public function add(){
        cek_menu_access();
        $data['htmlpagejs'] = 'none';
        $data['nmenu']      = 'Penugasan';
        $data['title']      = '';
        $data['namalabel']  = $data['title'];
        $data['auth']       = authUser();

        $companyId = $this->session->userdata('company_id');

        $data['failed'] = filter_var($this->input->get('failed'),FILTER_VALIDATE_BOOLEAN);

        $data['employees'] = $employees = $this->db->query("select * from m_pegawai where company_id = ? and is_del = 'n'",[$companyId])->result_array();

        $this->load->view('templates/header', $data);
        $this->load->view('templates/sidemenu', $data);
        $this->load->view('templates/sidenav', $data);
        $this->load->view('module/assignment/add', $data);
        $this->load->view('templates/footer', $data);
        $this->load->view('templates/fscript-html-end', $data);
    }

    public function edit($id){
        cek_menu_access();
        $data['htmlpagejs'] = 'none';
        $data['nmenu']      = 'Penugasan';
        $data['title']      = '';
        $data['namalabel']  = $data['title'];
        $data['auth']       = authUser();

        $data['id'] = $id;

        $companyId = $this->session->userdata('company_id');
        

        $data['failed'] = filter_var($this->input->get('failed'),FILTER_VALIDATE_BOOLEAN);

        $data['employees']  = $this->db->query("select * from m_pegawai where company_id = ? and is_del = 'n'",[$companyId])->result_array();

        $data['data'] = $this->db->query("select * from assignment where assignment_id = ?",[$id])->row_array();

        $this->load->view('templates/header', $data);
        $this->load->view('templates/sidemenu', $data);
        $this->load->view('templates/sidenav', $data);
        $this->load->view('module/assignment/edit', $data);
        $this->load->view('templates/footer', $data);
        $this->load->view('templates/fscript-html-end', $data);
    }

    public function add_proccess(){
      $startFrom = $this->input->post('startFrom');
      $until = $this->input->post('until');
      $description = $this->input->post('description');

      $notificationDescription = "$description ($startFrom - $until)";

      $data = [
        'assignment_id' => uniqid(),
        'employee_id' => $this->input->post('employeeId'),
        'start_from' => $this->input->post('startFrom'),
        'until' => $this->input->post('until'),
        'description' => $this->input->post('description'),
        'created_at' => date('Y-m-d H:i:s'),
      ];

      $data2 = [
        'notification_id' => uniqid(),
        'employee_id' => $this->input->post('employeeId'),
        'description' => $notificationDescription,
        'date' => date('Y-m-d H:i:s'),
        'seen' => 0
      ];

      $this->db->trans_begin();

      $this->db->insert(
        'assignment',$data
      );

      $this->db->insert(
        'notification',
        $data2
      );


      if($this->db->trans_status() === FALSE) {
        $this->db->trans_rollback();
        $this->session->set_flashdata('message', '<div class="alert alert-danger p-cg" role="alert">proses gagal, silahkan coba lagi</div>');
        redirect('assignment/add?failed=true');
      } 
      else {
        $this->db->trans_commit();
        redirect('assignment');
      }
    }

    public function edit_proccess($id){
      $tanggal1 = new DateTime($this->inpit->post('startFrom'));
      $tanggal2 = new DateTime($this->input->post('until'));
      
      $data = [
        'employee_id' => $this->input->post('employeeId'),
        'start_from' => $this->input->post('startFrom'),
        'until' => $this->input->post('until'),
        'description' => $this->input->post('description')
      ];

      

      $this->db->set(
        $data
      );
      $this->db->where(
        'assignment_id', 
        $id
      );
      $q = $this->db->update(
        'assignment'
      );

      if($q){
        redirect('assignment');
      }
      else{
        $this->session->set_flashdata('message', '<div class="alert alert-danger p-cg" role="alert">proses gagal, silahkan coba lagi</div>');
        redirect('assignment/edit/'.$id.'?failed=true');
      }
    }

    public function delete($id){
        $this->db->where('assignment_id', $id);
        $this->db->delete('assignment');
        redirect('assignment');
    }
}
