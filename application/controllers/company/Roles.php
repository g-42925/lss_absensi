<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Roles extends MY_Controller {
    public $email;
    public $session;
    public $form_validation;
    public $upload;
    public $pagination;
    public $other;
    public $menu;
    public $roles;

    public function __construct() {
        parent::__construct();
        is_logged_in();
        $this->load->library('form_validation');
        $this->load->model('other_model', 'other');
        $this->load->model('user/menu_model', 'menu');
        $this->load->model('user/perusahaan/roles_model', 'roles');
    }

    public function index() {
        $data['htmlpagejs'] = 'none';
        $data['nmenu']      = 'Perusahaan';
        $data['nmenusub']   = 'Jabatan & Izin';
        $data['title']      = 'Jabatan';
        $data['namalabel']  = $data['title'];

        $data['auth']       = authUser();

        $companyId = $this->session->userdata('company_id');

        $data['datas'] = $this->roles->get_data($companyId);

        $this->load->view('templates/header', $data);
        $this->load->view('templates/sidemenu', $data);
        $this->load->view('templates/sidenav', $data);
        $this->load->view('module/company/roles/index', $data);
        $this->load->view('templates/footer', $data);
        $this->load->view('templates/fscript-html-end', $data);
    }

    public function add($failed = 0) {
        isEditable();

        $data['htmlpagejs'] = 'none';
        $data['nmenu']      = 'Perusahaan';
        $data['nmenusub']   = 'Jabatan & Izin';
        $data['title']      = 'Jabatan';
        $data['namalabel']  = $data['title'];
        $data['auth']       = authUser();
        $data['failed']     = $failed;
        
        $actions = $this->db->query("select * from actions")->result_array();
        
        $grouped_actions = array_reduce($actions, function ($result, $item) {
          $result[$item['feature']][] = $item;
          return $result;
        }, []);

        $data['actions']    = $grouped_actions;
        $data['slugs']      = [];
        
        $data['menu'] = $this->menu->getMenu();

        $this->load->view('templates/header', $data);
        $this->load->view('templates/sidemenu', $data);
        $this->load->view('templates/sidenav', $data);
        $this->load->view('module/company/roles/add', $data);
        $this->load->view('templates/footer', $data);
        $this->load->view('templates/fscript-html-end', $data);
    }

    #[SkipPermission]
    public function add_proses() {
        
        $this->form_validation->set_rules('nama', 'Nama', 'trim|required|xss_clean|htmlspecialchars');
        $this->form_validation->set_rules('status', 'Status', 'trim|required|xss_clean|htmlspecialchars');
        $this->form_validation->set_rules('roles[]', 'Menu', 'trim|xss_clean|htmlspecialchars');

        if ($this->form_validation->run() == false) {
            $this->session->set_flashdata('message', '<div class="alert alert-danger p-cg" role="alert">'.validation_errors().'</div>');
            redirect('company/roles/add/1');
        } 
        else {            
            $dataInsert = [
                'company_id'        => $this->session->userdata('company_id'),
                'nama_role'         => $this->input->post('nama'),
                'is_status'         => $this->input->post('status'),
                'created_at'        => date('Y-m-d H:i:s')
            ];
            
            $res = $this->db->insert('m_role', $dataInsert);
            $id = $this->db->insert_id();

            $roles = $this->input->post('roles') ?? [];

            foreach($roles as $role){
                $this->db->insert('role_actions',[
                'role_id' => $id,
                'slug' => $role
                ]);
            }
            
            $this->session->set_flashdata('message', '<div class="alert alert-danger p-cg" role="alert">Proses gagal, silahkan coba lagi.</div>');
            redirect('company/roles');
        }
    }

    public function edit($id = null) {
        if ($id==null) redirect('user/company/roles');
        
        $check = $this->db->get_where('m_role', ['role_id' => $id]);

        $actions = $this->db->query("select * from role_actions where role_id = '$id'")->result_array();

        $slugs = array_column($actions, 'slug');
        
        $actions = $this->db->query("select * from actions")->result_array();
        
        $grouped_actions = array_reduce($actions, function ($result, $item) {
          $result[$item['feature']][] = $item;
          return $result;
        }, []);

        $data['htmlpagejs'] = 'none';
        $data['nmenu']      = 'Perusahaan';
        $data['nmenusub']   = 'Jabatan & Izin';
        $data['title']      = 'Jabatan';
        $data['namalabel']  = $data['title'];
        $data['auth']       = authUser();
        $data['actions']    = $grouped_actions;

        $data['menu']       = $this->menu->getMenu();
        $data['edit']       = $check->row_array();
        $data['slugs']      = $slugs;

        $this->load->view('templates/header', $data);
        $this->load->view('templates/sidemenu', $data);
        $this->load->view('templates/sidenav', $data);
        $this->load->view('module/company/roles/edit', $data);
        $this->load->view('templates/footer', $data);
        $this->load->view('templates/fscript-html-end', $data);
    }

    #[SkipPermission]
    public function edit_proses($id = null) {
            
        $this->form_validation->set_rules('nama', 'Nama', 'trim|required|xss_clean|htmlspecialchars');
        $this->form_validation->set_rules('status', 'Status', 'trim|required|xss_clean|htmlspecialchars');
        $this->form_validation->set_rules('roles[]', 'Menu', 'trim|xss_clean|htmlspecialchars');

        if ($this->form_validation->run() == false) {
            $this->session->set_flashdata('message', '<div class="alert alert-danger p-cg" role="alert">'.validation_errors().'</div>');
            redirect('company/roles/edit/'.$id);
        }
        else{
            $nama = $this->input->post('nama');
            $status = $this->input->post('status');
            $params = ['nama_role' => $nama,'is_status' => $status];
            $this->db->where('role_id', $id);
            $this->db->update('m_role', $params);
            $this->db->where('role_id', $id);
            $this->db->delete('role_actions');
            
            $roles = $this->input->post('roles') ?? [];

            foreach($roles as $role){
                $this->db->insert('role_actions',[
                'role_id' => $id,
                'slug' => $role
                ]);
            }
            
            redirect(
                'company/roles'
            );
            
        }
    }

    public function hapus($id){
        
        
        if($id==1){
            $this->session->set_flashdata('message', '<div class="me-3 ms-3 mt-3"><div class="alert alert-danger p-cg" role="alert">Jabatan Super Admin ini tidak bisa dihapus ya.</div></div>');
            redirect('company/roles');
        }
        else{
            $data['auth'] = authUser();

            if($id==1){
                $this->session->set_flashdata('message', '<div class="me-3 ms-3 mt-3"><div class="alert alert-danger p-cg" role="alert">[DEFAULT] - Jabatan ini tidak bisa dihapus.</div></div>');
                redirect('company/roles/');
            }
            
            $res = $this->other->hapus_data('m_role','role_id',$id);
            if ($res==true) {
                $this->session->set_flashdata('message', '<div class="me-3 ms-3 mt-3"><div class="alert alert-success p-cg" role="alert">Data berhasil dihapus.</div></div>');
                redirect('company/roles');
            }else{
                $this->session->set_flashdata('message', '<div class="me-3 ms-3 mt-3"><div class="alert alert-danger p-cg" role="alert">Proses gagal, silahkan coba lagi.</div></div>');
                redirect('company/roles');
            }
        }
    }

}
