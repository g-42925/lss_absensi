<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Profile extends CI_Controller {
    public $email;
    public $session;
    public $form_validation;
    public $upload;
    public $pagination;
    public $other;
    public $menu;
    public $profile;

    public function __construct() {
        parent::__construct();
        is_logged_in();
        $this->load->library('form_validation');
        $this->load->model('other_model', 'other');
        $this->load->model('user/menu_model', 'menu');
        $this->load->model('user/perusahaan/profile_model', 'profile');
        $this->load->library('upload');
    }

    public function index() {
        cek_menu_access();
        $data['htmlpagejs'] = 'none';
        $data['nmenu']      = 'Perusahaan';
        $data['title']      = 'Profil';
        $data['namalabel']  = $data['title'];
        $data['auth']       = authUser();
        $data['company']    = pengaturanSistem();
        
        $params = array($this->session->userdata('company_id'));

        $data['profile'] = $this->db->query("select * from companies where id = ?",$params)->row_array();

        $this->load->view('templates/header', $data);
        $this->load->view('templates/sidemenu', $data);
        $this->load->view('templates/sidenav', $data);
        $this->load->view('module/company/profile/index', $data);
        $this->load->view('templates/footer', $data);
        $this->load->view('templates/fscript-html-end', $data);
    }

    public function edit($failed) {
        cek_menu_access();
        $data['htmlpagejs'] = 'none';
        $data['nmenu']      = 'Perusahaan';
        $data['title']      = 'Profil';
        $data['auth']       = authUser();
        $data['edit']       = pengaturanSistem();
        $data['failed']     = $failed;
        $params = array($this->session->userdata('company_id'));
        $data['profile'] = $this->db->query("select * from companies where id = ?",$params)->row_array();
        $data['status'] = $this->session->flashdata('success');
        if($data['auth']['edit']!='y'){
            $this->session->set_flashdata('message', '<div class="me-3 ms-3 mt-3"><div class="alert alert-danger p-cg" role="alert">Tidak ada akses.</div></div>');
            redirect('company/profile/');
        }

        $this->load->view('templates/header', $data);
        $this->load->view('templates/sidemenu', $data);
        $this->load->view('templates/sidenav', $data);
        $this->load->view('module/company/profile/edit', $data);
        $this->load->view('templates/footer', $data);
        $this->load->view('templates/fscript-html-end', $data);
    }

    public function edit_proses() {
        cek_menu_access();
        $company = pengaturanSistem();

        $companyId = $this->session->userdata('company_id');
				$params = array($this->session->userdata('company_id'));
        $profile = $this->db->query("select * from companies where id = ?",$params)->row_array();

        
				$this->form_validation->set_rules('name', 'Name', 'trim|required|xss_clean|htmlspecialchars');
        $this->form_validation->set_rules('address', 'Address', 'trim|required|xss_clean|htmlspecialchars');
        $this->form_validation->set_rules('phone', 'Phone', 'trim|required|xss_clean|htmlspecialchars');
        $this->form_validation->set_rules('email', 'Email', 'trim|required|xss_clean|htmlspecialchars');
        $this->form_validation->set_rules('salary_date','Date','trim|required|xss_clean|htmlspecialchars');
        
        if($_FILES['logo']['name'] != ''){
          $name = pathinfo($_FILES['logo']['name'],PATHINFO_FILENAME);
          $ext = pathinfo($_FILES['logo']['name'],PATHINFO_EXTENSION);
          $fileName = preg_replace('/[^a-zA-Z0-9]/','',$name);
          $config['file_name'] = 'cp-'.$companyId.'-'.$fileName.'.'.$ext;
					$config['upload_path']  = './assets/uploaded/components/';
          $config['allowed_types'] = 'gif|jpg|jpeg|png';
          $config['max_size'] = 500;
					
          $this->upload->initialize($config);
          $upload = $this->upload->do_upload('logo');

          if($upload){
            $this->db->set([
              'id' => $companyId,
              'company_name' => $this->input->post('name'),
              'address' => $this->input->post('address'),
              'phone' => $this->input->post('phone'),
              'email' => $this->input->post('email'),
              'logo' => $config['file_name'],
              'salary_date' => $this->input->post('salary_date')
            ]);

            $this->db->where('id',$companyId);

            $this->db->update('companies');

						$this->session->set_flashdata('success','yes');

            redirect('company/profile');
					}
          else{
            $this->session->set_flashdata('success','not');

            $this->session->set_flashdata(
							'message', 
							'<div class="alert alert-danger p-cg" role="alert">
							  Proses gagal, silahkan coba lagi.
							</div>'
						);
						redirect('company/profile/edit/1');
          }
        }
        else{
          $this->db->set([
            'id' => $companyId,
            'company_name' => $this->input->post('name'),
            'address' => $this->input->post('address'),
            'phone' => $this->input->post('phone'),
            'email' => $this->input->post('email'),
            'logo' => $profile['logo'],
            'salary_date' => $this->input->post('salary_date')
          ]);

					$this->db->where('id',$companyId);

          $this->db->update('companies');

					$this->session->set_flashdata('success','yes');

          redirect('company/profile');
        }
        

        // if ($this->form_validation->run() == false) {
        //     $this->session->set_flashdata('message', '<div class="alert alert-danger p-cg" role="alert">'.validation_errors().'</div>');
        //     redirect('company/profile/edit/');
        // } 
        // else {
        //     $ceklogo = $_FILES['logo']['name'];
        //     $upload = $this->other->upload_gambar('gambar','xxx','logo','logo_');
        //     if($upload['result'] == "success" || $ceklogo==''){
        //         $res = $this->profile->edit_proses($ceklogo,$upload);
        //         if ($res==true) {
        //             $this->session->set_flashdata('message', '<div class="me-3 ms-3 mt-3"><div class="alert alert-success p-cg" role="alert">Data berhasil diperbarui.</div></div>');
        //             redirect('company/profile');
        //         }else{
        //             unlink(FCPATH.$upload['path'].$upload['file']['file_name']);
        //             $this->session->set_flashdata('message', '<div class="alert alert-danger p-cg" role="alert">Proses gagal, silahkan coba lagi.</div>');
        //                 redirect('company/profile/edit/');
        //         }
        //     }else{
        //         $this->session->set_flashdata('message', '<div class="alert alert-danger p-cg" role="alert">'.$upload['error'].'</div>');
        //         redirect('company/profile/edit/');
        //     } 
        // }
    }

}
