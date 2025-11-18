<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Reimburse extends CI_Controller {
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
        $data['nmenu']      = 'Bonus & Tunjangan';
        $data['title']      = 'Reimburse';
        $data['namalabel']  = $data['title'];
        $data['auth']       = authUser();

        $companyId = $this->session->userdata('company_id');

        $data['data'] = $this->db->query("select * from reimburse where company_id = ?",[$companyId])->result_array();

        $this->load->view('templates/header', $data);
        $this->load->view('templates/sidemenu', $data);
        $this->load->view('templates/sidenav', $data);
        $this->load->view('module/reimburse/index',$data);
        $this->load->view('templates/footer', $data);
        $this->load->view('templates/fscript-html-end', $data);
    }

    public function add(){
        cek_menu_access();
        $data['htmlpagejs'] = 'none';
        $data['nmenu']      = 'Bonus & Tunjangan';
        $data['title']      = 'Reimburse';
        $data['namalabel']  = $data['title'];
        $data['auth']       = authUser();

        $data['failed'] = filter_var($this->input->get('failed'),FILTER_VALIDATE_BOOLEAN);

        $this->load->view('templates/header', $data);
        $this->load->view('templates/sidemenu', $data);
        $this->load->view('templates/sidenav', $data);
        $this->load->view('module/reimburse/add',$data);
        $this->load->view('templates/footer', $data);
        $this->load->view('templates/fscript-html-end', $data);
    }

     public function edit($id){
        cek_menu_access();
        $data['htmlpagejs'] = 'none';
        $data['nmenu']      = 'Bonus & Tunjangan';
        $data['title']      = 'Reimburse';
        $data['namalabel']  = $data['title'];
        $data['auth']       = authUser();

        $data['id'] = $id;

        $data['failed'] = filter_var($this->input->get('failed'),FILTER_VALIDATE_BOOLEAN);

        $data['data'] = $this->db->query("select * from reimburse where reimburse_id = ?",[$id])->row_array();

        $this->load->view('templates/header', $data);
        $this->load->view('templates/sidemenu', $data);
        $this->load->view('templates/sidenav', $data);
        $this->load->view('module/reimburse/edit',$data);
        $this->load->view('templates/footer', $data);
        $this->load->view('templates/fscript-html-end', $data);
    }

    public function add_proccess(){
      $companyId = $this->session->userdata('company_id');


      $data = [
        'reimburse_id' => uniqid(),
        'company_id' => $companyId,
        'reimburse_name' => $this->input->post('reimburseName')
      ];

      $q = $this->db->insert(
        'reimburse',
        $data
      );

      if(!$q){
        $this->session->set_flashdata(
          'message','<div class="alert alert-danger">Proses gagal. Silakan coba lagi.</div>'
        );
        redirect(
          'reimburse/add/?failed=true'
        );
      }
      else{
        redirect(
          'reimburse/'
        );
      }
    }

    public function claim($id){
       cek_menu_access();
        $data['htmlpagejs'] = 'none';
        $data['nmenu']      = 'Bonus & Tunjangan';
        $data['title']      = 'Reimburse';
        $data['namalabel']  = $data['title'];
        $data['auth']       = authUser();

        $companyId = $this->session->userdata('company_id');

        $data['data'] = $this->db->query("select * from reimburse_claim rc join reimburse r on rc.reimburse_id = r.reimburse_id join m_pegawai mp on mp.pegawai_id = rc.employee_id where rc.reimburse_id = ? order by date desc",[$id])->result_array();

        $this->load->view('templates/header', $data);
        $this->load->view('templates/sidemenu', $data);
        $this->load->view('templates/sidenav', $data);
        $this->load->view('module/reimburse/claim',$data);
        $this->load->view('templates/footer', $data);
        $this->load->view('templates/fscript-html-end', $data);
    }

    public function claim_edit($reimburseId, $claimId){
        cek_menu_access();
        $data['htmlpagejs'] = 'none';
        $data['nmenu']      = 'Bonus & Tunjangan';
        $data['title']      = 'Reimburse';
        $data['namalabel']  = $data['title'];
        $data['auth']       = authUser();

        $data['reimburseId'] = $reimburseId;
        $data['claimId'] = $claimId;

        $data['failed'] = filter_var($this->input->get('failed'),FILTER_VALIDATE_BOOLEAN);

        $data['data'] = $this->db->query("select * from reimburse_claim rc join reimburse r on rc.reimburse_id = r.reimburse_id where rc.reimburse_claim_id = ?",[$claimId])->row_array();

        $this->load->view('templates/header', $data);
        $this->load->view('templates/sidemenu', $data);
        $this->load->view('templates/sidenav', $data);
        $this->load->view('module/reimburse/claim_edit');
        $this->load->view('templates/footer', $data);
        $this->load->view('templates/fscript-html-end', $data);
    }

    public function claim_edit_proccess($reimburseId, $claimId){
      $data = [
        'status' => $this->input->post('status')
      ];

      $this->db->set(
          $data
        );
        $this->db->where(
          'reimburse_claim_id', 
          $claimId
        );
        $q = $this->db->update(
          'reimburse_claim'
        );

        if($q){
          redirect('reimburse/claim/'.$reimburseId);
        }
        else{
          $this->session->set_flashdata('message', '<div class="alert alert-danger p-cg" role="alert">proses gagal, silahkan coba lagi</div>');
          redirect('reimburse/edit/'.$id.'?failed=true');
        }
    }

    public function edit_proccess($id){
        $data = [
          'reimburse_name' => $this->input->post('reimburseName')
        ];

        $this->db->set(
          $data
        );
        $this->db->where(
          'reimburse_id', 
          $id
        );
        $q = $this->db->update(
          'reimburse'
        );

        if($q){
          redirect('reimburse');
        }
        else{
          $this->session->set_flashdata('message', '<div class="alert alert-danger p-cg" role="alert">proses gagal, silahkan coba lagi</div>');
          redirect('reimburse/edit/'.$id.'?failed=true');
        }
    }

    function delete($reimburseId){
      $this->db->trans_begin();  // to start db transaction
      $this->db->delete('reimburse',['reimburse_id' => $reimburseId]);
      $this->db->delete('reimburse_claim',['reimburse_id' => $reimburseId]);
      if($this->db->trans_status() === FALSE) {
        $this->db->trans_rollback();
        $this->session->set_flashdata(
            'message',
            '<div class="alert alert-danger">Proses gagal. Silakan coba lagi.</div>'
        );
        redirect('reimburse?failed=true');
      } 
      else {
        $this->db->trans_commit();
        redirect('reimburse');

      }      
    }
}
