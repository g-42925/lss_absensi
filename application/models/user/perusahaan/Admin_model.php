<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Admin_model extends CI_Model {

    public function __construct() {
        parent::__construct();
    }
	
    public function get_data() {
        $company = $this->session->userdata('company_id');
        $query = $this->db->query("SELECT a.*, c.nama_role, d.nama_permission FROM m_user a 
        	JOIN m_role c ON a.role_id=c.role_id 
        	JOIN m_permission d ON a.permission_id=d.permission_id 
        	WHERE a.is_del='n' AND a.company_id='".$company."'")->result_array();
        return $query;
    }

    public function add_proses($companyId) {
        $data = [
            'company_id'        => $companyId,
            'role_id'  			=> $this->input->post('roles'),
            'permission'  	    => $this->input->post('permission'),
            'nama_lengkap'  	=> $this->input->post('nama'),
            'email_address'  	=> $this->input->post('email'),
            'password'  		=> password_hash($this->input->post('password'), PASSWORD_DEFAULT),
            'is_status'  		=> $this->input->post('status'),
            'position_id'       => $this->input->post('position'),
            'created_at'  		=> date('Y-m-d H:i:s'),
            'permission_id'     => 1,

        ];
        
        $res = $this->db->insert('m_user', $data);
        return $res;
    }

    public function edit_proses($id,$passwordlama) {
    	$pass = $this->input->post('password');
        if ($pass=='') {
            $passnya = $passwordlama;
        }
        else{
            $passnya = password_hash($this->input->post('password'), PASSWORD_DEFAULT);
        }

        $this->db->set([
            'company_id'        => $this->session->userdata('company_id'),
            'permission'  	    => $this->input->post('permission'),
            'nama_lengkap'  	=> $this->input->post('nama'),
            'email_address'  	=> $this->input->post('email'),
            'role_id'  			=> $this->input->post('roles'),
            'is_status'  		=> $this->input->post('status'),
            'password'  		=> $passnya,
        ]);
        
        $this->db->where('user_id', $id);
        $res = $this->db->update('m_user');
        return $res;
    }

}