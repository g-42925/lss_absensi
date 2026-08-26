<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Leave_balance extends MY_Controller {
    public $email;

    public $session;

    public $form_validation;

    public $upload;

    public $pagination;

    public $menu;

    public function __construct() {
        parent::__construct();
        $this->load->library('form_validation');
        $this->load->model('user/menu_model', 'menu');
    }

    public function index() {
        $data['htmlpagejs'] = 'none';
        $data['nmenu']      = 'Leave Balance';
        $data['title']      = 'Employee Leave Balance';
        $data['namalabel']  = $data['title'];
        $data['auth']       = authUser();

        $companyId = $this->session->userdata('company_id');

        $div = $this->input->get('divisionId');
        $name = $this->input->get('name');

        $data['div'] = $div;
        $data['name'] = $name;

        $data['divisions'] = $this->db->query("SELECT * FROM divisions WHERE company_id = ?", [$companyId])->result_array();

        $query = "
            SELECT b.*, p.nama_pegawai, p.nik, d.division_name as divisi
            FROM employee_leave_balance b
            JOIN m_pegawai p ON p.pegawai_id = b.employee_id
            LEFT JOIN divisions d ON d.id = p.division_id
            WHERE p.company_id = ?
        ";
        $params = [$companyId];

        if (!empty($div) && $div != 'all') {
            $query .= " AND p.division_id = ?";
            $params[] = $div;
        }

        if (!empty($name)) {
            $query .= " AND p.nama_pegawai LIKE ?";
            $params[] = "%" . $name . "%";
        }

        $query .= " ORDER BY b.id DESC";

        $data['balances'] = $this->db->query($query, $params)->result_array();

        $this->load->view('templates/header',$data);
        $this->load->view('templates/sidemenu',$data);
        $this->load->view('templates/sidenav');
        $this->load->view('module/leave_balance/index',$data);
        $this->load->view('templates/footer');
        $this->load->view('templates/fscript-html-end');
    }

    public function add(){
        isEditable();
        $data['htmlpagejs'] = 'none';
        $data['nmenu']      = 'Leave Balance';
        $data['title']      = 'Tambah Leave Balance';
        $data['namalabel']  = $data['title'];
        $data['auth']       = authUser();

        $companyId = $this->session->userdata('company_id');
        $div = $this->input->get('div');
        
        
        $all = "select * from m_pegawai where company_id = ? and is_del = 'n'";
        $byDivision = "select * from m_pegawai where division_id = ? and is_del = 'n'";
        $q = $div ? $div === 'all' ? $all : $byDivision : $all;
        $params = $div ? $div === 'all' ? [$companyId] : [$div] : [$companyId];

        $data['div'] = $div;
       
        $data['employees'] = $this->db->query($q, $params)->result_array();
                
        $data['divisions'] = $this->db->query("SELECT * FROM divisions WHERE company_id = ?", [$companyId])->result_array();
        
        $this->load->view('templates/header',$data);
        $this->load->view('templates/sidemenu',$data);
        $this->load->view('templates/sidenav');
        $this->load->view('module/leave_balance/add',$data);
        $this->load->view('templates/footer');
        $this->load->view('templates/fscript-html-end');       
    }

    #[SkipPermission]
    public function add_proses(){
        $employee_ids = $this->input->post('employee_id');
        if (!empty($employee_ids) && is_array($employee_ids)) {
            foreach ($employee_ids as $emp_id) {
                $this->db->insert('employee_leave_balance', [
                    'employee_id' => $emp_id,
                    'from' => $this->input->post('from'),
                    'to' => $this->input->post('to'),
                    'quota' => $this->input->post('quota'),
                    'used' => $this->input->post('used') ? $this->input->post('used') : 0
                ]);
            }
        }
        
        redirect('leave_balance');
    }

    public function edit($id){
        isEditable();
        $data['htmlpagejs'] = 'none';
        $data['nmenu']      = 'Leave Balance';
        $data['title']      = 'Edit Leave Balance';
        $data['namalabel']  = $data['title'];
        $data['auth']       = authUser();

        $companyId = $this->session->userdata('company_id');
        $data['data'] = $this->db->query("SELECT * FROM employee_leave_balance where id='$id'")->row_array();
        $data['employees'] = $this->db->query("SELECT pegawai_id, nama_pegawai, nik FROM m_pegawai WHERE company_id = ? AND is_status = 'y' AND is_del = 'n' ORDER BY nama_pegawai ASC", [$companyId])->result_array();

        $this->load->view('templates/header',$data);
        $this->load->view('templates/sidemenu',$data);
        $this->load->view('templates/sidenav');
        $this->load->view('module/leave_balance/edit',$data);
        $this->load->view('templates/footer');
        $this->load->view('templates/fscript-html-end');    
    }

    #[SkipPermission]
    public function edit_proses(){
        $id = $this->input->post('id');
        $this->db->where('id', $id);
        $this->db->update('employee_leave_balance', [
            'employee_id' => $this->input->post('employee_id'),
            'from' => $this->input->post('from'),
            'to' => $this->input->post('to'),
            'quota' => $this->input->post('quota'),
            'used' => $this->input->post('used') ? $this->input->post('used') : 0
        ]);
        redirect('leave_balance');
    }

    #[SkipPermission]
    public function delete($id){
        isEditable();
        $this->db->where('id', $id);
        $this->db->delete('employee_leave_balance');
        redirect('leave_balance');
    }
}
