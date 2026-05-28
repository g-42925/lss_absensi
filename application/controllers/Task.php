<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Task extends CI_Controller
{
    public $email;
    public $session;
    public $form_validation;
    public $upload;
    public $pagination;
    public $other;
    public $menu;
    public $rp;

    public function __construct()
    {
        parent::__construct();
        is_logged_in();
        $this->load->library('form_validation');
        $this->load->model('other_model', 'other');
        $this->load->model('user/menu_model', 'menu');
        $this->load->model('user/req_permission_model', 'rp');
    }

    public function index()
    {
        cek_menu_access();
        $data['htmlpagejs'] = 'none';
        $data['nmenu']      = 'Task';
        $data['title']      = 'Task Management';
        $data['namalabel']  = $data['title'];
        $data['auth']       = authUser();

        $companyId = $this->session->userdata('company_id');

        $data['data'] = $this->db->query("SELECT ot.*, mp.nama_pegawai, mp.id_pegawai
             FROM office_task ot
             JOIN m_pegawai mp ON ot.assigned_to = mp.pegawai_id
             WHERE ot.company_id = ? ORDER BY ot.created_at DESC",
            [$companyId]
        )->result_array();

        $this->load->view('templates/header', $data);
        $this->load->view('templates/sidemenu', $data);
        $this->load->view('templates/sidenav', $data);
        $this->load->view('module/task/m_index', $data);
        $this->load->view('templates/footer', $data);
        $this->load->view('templates/fscript-html-end', $data);
    }

    public function add()
    {
        cek_menu_access();
        isEditable();
        $data['htmlpagejs'] = 'none';
        $data['nmenu']      = 'Task';
        $data['title']      = 'Task Management';
        $data['namalabel']  = 'Tambah Task';
        $data['auth']       = authUser();

        $data['failed'] = filter_var($this->input->get('failed'), FILTER_VALIDATE_BOOLEAN);

        $this->load->view('templates/header', $data);
        $this->load->view('templates/sidemenu', $data);
        $this->load->view('templates/sidenav', $data);
        $this->load->view('module/task/add', $data);
        $this->load->view('templates/footer', $data);
        $this->load->view('templates/fscript-html-end', $data);
    }

    public function add_proses()
    {
        cek_menu_access();
        isEditable();

     
        $nik = explode('(', $this->input->post('nik'))[1]; 
        $nik = str_replace(')', '', $nik);
        
        $data = [
            'office_task_id'   => uniqid(),
            'company_id'  => $this->session->userdata('company_id'),
            'assigned_to' => $nik,
            'task'       =>  $this->input->post('task'),
            'description' => $this->input->post('description'),
            'deadline'    => $this->input->post('deadline'),
            'solved'   => $this->input->post('is_solved'),
            'created_at'  => date('Y-m-d'),
        ];

        $q = $this->db->insert('office_task', $data);

        if($q) {
            $this->session->set_flashdata('message', '<div class="me-3 ms-3 mt-3"><div class="alert alert-success p-cg" role="alert">Task berhasil disimpan.</div></div>');
            redirect('task/list');
        } 
        else {
            $this->session->set_flashdata('message', '<div class="alert alert-danger p-cg" role="alert">Proses gagal, silahkan coba lagi.</div>');
            redirect('task/add?failed=true');
        }
    }

    public function edit($id = null){
        cek_menu_access();
        isEditable();
        
        if ($id == null) { redirect('task/list'); }

        $row = $this->db->get_where('office_task', ['office_task_id' => $id])->row_array();

        $data['htmlpagejs'] = 'none';
        $data['nmenu']      = 'Task';
        $data['title']      = 'Task Management';
        $data['namalabel']  = 'Edit Task';
        $data['auth']       = authUser();
        $data['task']       = $row;
        $data['failed']     = filter_var($this->input->get('failed'), FILTER_VALIDATE_BOOLEAN);

        // Get assigned employee name for pre-filling the autocomplete
        $emp = $this->db->query("SELECT pegawai_id, nama_pegawai, id_pegawai FROM m_pegawai WHERE pegawai_id = ?", [$row['assigned_to']])->row_array();
        $data['emp']   = $emp;

        $this->load->view('templates/header', $data);
        $this->load->view('templates/sidemenu', $data);
        $this->load->view('templates/sidenav', $data);
        $this->load->view('module/task/edit', $data);
        $this->load->view('templates/footer', $data);
        $this->load->view('templates/fscript-html-end', $data);
    }

    public function edit_proses($id = null){
        cek_menu_access();
        isEditable();
        
        $nik = explode('(', $this->input->post('nik'))[1]; 
        $nik = str_replace(')', '', $nik);

        $data = [
            'office_task_id'   => uniqid(),
            'company_id'  => $this->session->userdata('company_id'),
            'assigned_to' => $nik,
            'task'       =>  $this->input->post('task'),
            'description' => $this->input->post('description'),
            'deadline'    => $this->input->post('deadline'),
            'solved'   => $this->input->post('is_solved'),
            'created_at'  => date('Y-m-d'),
        ];


        $this->db->set($data);
        $this->db->where('office_task_id', $id);
      
        $q = $this->db->update('office_task');

        if ($q) {
            $this->session->set_flashdata('message', '<div class="me-3 ms-3 mt-3"><div class="alert alert-success p-cg" role="alert">Task berhasil diperbarui.</div></div>');
            redirect('task/list');
        } 
        else {
            $this->session->set_flashdata('message', '<div class="alert alert-danger p-cg" role="alert">Proses gagal, silahkan coba lagi.</div>');
            redirect('task/edit/' . $id . '?failed=true');
        }
    }

    public function delete($id = null)
    {
        cek_menu_access();
        isEditable();
        if ($id == null) { redirect('task/list'); }

        $this->db->set(['is_del' => 'y']);
        $this->db->where('m_task_id', $id);
        $q = $this->db->update('m_task');

        if ($q) {
            $this->session->set_flashdata('message', '<div class="me-3 ms-3 mt-3"><div class="alert alert-success p-cg" role="alert">Task berhasil dihapus.</div></div>');
        } else {
            $this->session->set_flashdata('message', '<div class="alert alert-danger p-cg" role="alert">Proses gagal, silahkan coba lagi.</div>');
        }
        redirect('task/list');
    }

    public function toggle_solved($id = null)
    {
        cek_menu_access();
        isEditable();
        if ($id == null) { redirect('task/list'); }

        $task = $this->db->get_where('m_task', ['m_task_id' => $id, 'is_del' => 'n'])->row_array();
        if (!$task) { redirect('task/list'); }

        $newStatus = $task['is_solved'] == 1 ? 0 : 1;
        $this->db->set(['is_solved' => $newStatus]);
        $this->db->where('m_task_id', $id);
        $this->db->update('m_task');

        redirect('task/list');
    }

    /**
     * AJAX endpoint: search employees as user types
     * Returns JSON array of {id, text} for select2 AJAX
     */
    public function search_employee()
    {
        $keyword   = $this->input->get('q');
        $companyId = $this->session->userdata('company_id');

        $results = $this->db->query(
            "SELECT pegawai_id as id, CONCAT(nama_pegawai, ' (', id_pegawai, ')') as text
             FROM m_pegawai
             WHERE company_id = ? AND is_del = 'n' AND is_status = 'y'
             AND (nama_pegawai LIKE ? OR id_pegawai LIKE ?)
             ORDER BY nama_pegawai ASC
             LIMIT 20",
            [$companyId, '%' . $keyword . '%', '%' . $keyword . '%']
        )->result_array();

        $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode(['results' => $results]));
    }

    // Keep backward-compatible route for the old activity-based task list
    public function list()
    {
        $this->index();
    }
}
