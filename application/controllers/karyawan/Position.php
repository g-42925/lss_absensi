<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Position extends CI_Controller {

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
        $data['title']      = 'Posisi';
        $data['namalabel']  = 'Pengaturan '.$data['title'].' Karyawan';
        $data['auth']       = authUser();

        $companyId = $this->session->userdata('company_id');

        $data['position'] = $this->db->query("select * from position where company_id = ?",[$companyId])->result_array();

        foreach($data['position'] as $index => $p){
          if($p['parent'] != 0){
            $q = $this->db->query("select * from position where id = ?",[$p['parent']])->row_array();
            $data['position'][$index]['hirarki'] = $q['name'];
          }
          else{
            $data['position'][$index]['hirarki'] = 'Above All';
          }
        }


        $this->load->view('templates/header', $data);
        $this->load->view('templates/sidemenu', $data);
        $this->load->view('templates/sidenav', $data);
        $this->load->view('module/karyawan/position/index', $data);
        $this->load->view('templates/footer', $data);
        $this->load->view('templates/fscript-html-end', $data);
    }

    public function add() {
        cek_menu_access();
        isCreatable();
        $data['htmlpagejs'] = 'none';
        $data['nmenu']      = 'Karyawan';
        $data['title']      = 'Posisi';
        $data['namalabel']  = $data['title'];
        $data['auth']       = authUser();

        $companyId = $this->session->userdata('company_id');

        $data['position'] = $this->db->query("select * from position where company_id = ?",[$companyId])->result_array();

        $data['location'] = $this->db->query("select * from m_lokasi where company_id = ?",[$companyId])->result_array();

        $data['failed'] = filter_var($this->input->get('failed'),FILTER_VALIDATE_BOOLEAN);

        $this->load->view('templates/header', $data);
        $this->load->view('templates/sidemenu', $data);
        $this->load->view('templates/sidenav', $data);
        $this->load->view('module/karyawan/position/add', $data);
        $this->load->view('templates/footer', $data);
        $this->load->view('templates/fscript-html-end', $data);
    }

    public function add_proses(){
        $id = time();

        $attendance_target = [];

        $data = [
            'id'                    => $id,
            'company_id'            => $this->session->userdata('company_id'),
            'name'  	            => $this->input->post('name'),
            'parent'  	            => $this->input->post('parent'),
        ];

        $this->db->trans_begin();

        try{
          $q = $this->db->insert(
            'position',
            $data
          );

          if(!$q){
            throw new Exception("failed to run query");
          }

          foreach($this->input->post('location') as $key => $l){
            $target = $this->input->post('target')[$key];

            $data = [
              'id' => uniqid('', true),
              'position_id' => $id,
              'location_id' => $l,
              'target'=> $target,
            ];

            $q = $this->db->insert('attendance_target', $data);

            if(!$q){
              throw new Exception("failed to run query");
            }
          }
          
          $this->db->trans_commit();
          redirect('karyawan/position');
        }
        catch(Exception $e){
          $this->db->trans_rollback();
          $this->session->set_flashdata('message', '<div class="alert alert-danger">proses gagal. silahkan coba lagi</div>');
          redirect('karyawan/position/add?failed=true');
        }
    }

    public function edit_proccess($id){

        $data = [
          'name' => $this->input->post('name'),
          'parent' => $this->input->post('parent'),
        ];

        $this->db->trans_begin();

        try{
          $this->db->where('id',$id);

          $q = $this->db->update(
            'position',
            $data
          );

          if(!$q){
            throw new Exception("failed to run query");
          }
          
          $this->db->trans_commit();
          redirect('karyawan/position');
        }
        catch(Exception $e){
          $this->db->trans_rollback();
          $this->session->set_flashdata('message', '<div class="alert alert-danger">proses gagal. silahkan coba lagi</div>');
          redirect('karyawan/position/add?failed=true');
        }
    }

    public function edit($id){
        cek_menu_access();
        isEditable();
        $data['htmlpagejs'] = 'none';
        $data['nmenu']      = 'Karyawan';
        $data['title']      = 'Posisi';
        $data['namalabel']  = $data['title'];
        $data['auth']       = authUser();

        $data['id'] = $id;

        $companyId = $this->session->userdata('company_id');

        $data['failed'] = filter_var($this->input->get('failed'),FILTER_VALIDATE_BOOLEAN);

        $data['current'] = $this->db->query("select * from position where id = ?",[$id])->row_array();

        $data['position'] = $this->db->query("select * from position where company_id = ?",[$companyId])->result_array();

        $data['location'] = $this->db->query("select * from m_lokasi where company_id = ?",[$companyId])->result_array();

        $data['failed'] = filter_var($this->input->get('failed'),FILTER_VALIDATE_BOOLEAN);

        $this->load->view('templates/header', $data);
        $this->load->view('templates/sidemenu', $data);
        $this->load->view('templates/sidenav', $data);
        $this->load->view('module/karyawan/position/edit', $data);
        $this->load->view('templates/footer', $data);
        $this->load->view('templates/fscript-html-end', $data);
    }
}
