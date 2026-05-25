<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Premutation extends CI_Controller {

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
        $data['title']      = 'Premutation';
        $data['namalabel']  = $data['title'];
        $data['auth']       = authUser();

        $data['failed'] = filter_var($this->input->get('failed'),FILTER_VALIDATE_BOOLEAN);
        $companyId = $this->session->userdata('company_id');
        $data['employees'] = $this->db->query("select * from premutation p join m_pegawai mp on p.employee_id = mp.pegawai_id where p.company_id = ?",[$companyId])->result_array();

        $this->load->view('templates/header', $data);
        $this->load->view('templates/sidemenu', $data);
        $this->load->view('templates/sidenav', $data);
        $this->load->view('module/karyawan/premutation/index',$data);
        $this->load->view('templates/footer', $data);
        $this->load->view('templates/fscript-html-end', $data);
    }

    public function next($employeeId){
        cek_menu_access();
        isEditable();
        $data['htmlpagejs'] = 'none';
        $data['nmenu']      = 'Karyawan';
        $data['title']      = 'Data Karyawan';
        $data['namalabel']  = $data['title'];
        $data['auth']       = authUser();
 
        $companyId = $this->session->userdata('company_id');
        $data['roles'] = $this->other->get_roles($companyId);
        $data['permission'] = $this->other->get_permission();
        $data['mindate'] = date("Y-m-d", strtotime(date('Y-m-d')." -7 day"));
        $data['maxdate'] = date("Y-m-d", strtotime(date('Y-m-d')." +7 day"));

        $data['e'] = $this->db->query("select * from m_pegawai where pegawai_id = ?",[$employeeId])->row_array();
        $data['divisions'] = $this->db->query("select * from divisions where company_id = ?",[$companyId])->result_array();
        $data['position'] = $this->db->query("select * from position where company_id = ?",[$companyId])->result_array();

        $this->load->view('templates/header', $data);
        $this->load->view('templates/sidemenu', $data);
        $this->load->view('templates/sidenav', $data);
        $this->load->view('module/karyawan/premutation/next',$data);
        $this->load->view('templates/footer', $data);
        $this->load->view('templates/fscript-html-end', $data);
    }

    public function next_proccess($employeeId){
        $companyId = $this->session->userdata('company_id');

        $this->form_validation->set_rules('idkar', 'ID Karyawan', 'trim|required|xss_clean|htmlspecialchars');
        $this->form_validation->set_rules('nama', 'Nama', 'trim|required|xss_clean|htmlspecialchars');
        $this->form_validation->set_rules('email', 'Email', 'trim|required|xss_clean|htmlspecialchars');
        $this->form_validation->set_rules('nom', 'No WhatsApp', 'trim|xss_clean|htmlspecialchars');
        $this->form_validation->set_rules('status', 'Status', 'trim|required|xss_clean|htmlspecialchars');
        $this->form_validation->set_rules('jeniskelamin', 'Jenis Kelamin', 'trim|required|xss_clean|htmlspecialchars');
        $this->form_validation->set_rules('password', 'Password', 'trim|required|xss_clean|htmlspecialchars|min_length[4]');
        $this->form_validation->set_rules('nik','Nik','trim|required|xss_clean|htmlspecialchars|min_length[16]');

        if ($this->form_validation->run() == false) {
          $this->session->set_flashdata('message', '<div class="alert alert-danger p-cg" role="alert">'.validation_errors().'</div>');
          redirect('karyawan/data/add/1');
        }
        else{
          $password = $this->input->post('password');

          $update = [
            'is_status' => $this->input->post('status'),
            'tanggal_mulai_kerja' => $this->input->post('tglmulai'),
            'contract_start_date' => $this->input->post('contract_start_date'),
            'contract_end_date'   => $this->input->post('contract_end_date'),
            'division_id' => $this->input->post('division'),
            'position_id' => $this->input->post('position'),
            'jumlah_cuti' => $this->input->post('jumlahCuti'),
            'status_pegawai' => $this->input->post('statusPegawai'),
            'salary' => $this->input->post('salary'),
            'on_training' => $this->input->post('on_training'),
            'company_id' => $companyId,
            'password_pegawai' => password_hash($password,PASSWORD_DEFAULT),
            'is_del' => 'n',
          ];

          $this->db->set($update);
          $this->db->where('pegawai_id', $employeeId);
          $this->db->update('m_pegawai');
         
          redirect('karyawan/data');

        }
    }

    public function proses(){
      $this->db->trans_begin();

      $target = $this->input->post('target');
     
      foreach($this->input->post('idp[]') as $e){
        $data = [
          'premutation_id' => uniqid(),
          'company_id' => $target,
          'employee_id' =>  $e
        ];

        $this->db->set(['is_del' => 'y']);
        $this->db->where('pegawai_id',$e);
        $this->db->update('m_pegawai');

        $this->db->insert(
          'premutation',
          $data
        );
      }


      if($this->db->trans_status() === FALSE) {
        $this->db->trans_rollback();
        $this->session->set_flashdata(
            'message',
            '<div class="alert alert-danger">Proses gagal. Silakan coba lagi.</div>'
        );
        redirect('karyawan/mutation?failed=true');
      } 
      else {
        $this->db->trans_commit();
        redirect('karyawan/data');
      }      
    }

    
}
