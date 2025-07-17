<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Admin_model extends CI_Model {

    public function __construct() {
        parent::__construct();
    }
	
    public function get_data() {
        $query = $this->db->query("SELECT a.*, c.nama_role, d.nama_permission FROM m_user a 
        	JOIN m_role c ON a.role_id=c.role_id 
        	JOIN m_permission d ON a.permission_id=d.permission_id 
        	WHERE a.is_del='n'")->result_array();
        return $query;
    }

    public function add_proses() {
        $data = [
            'role_id'  			=> $this->input->post('roles'),
            'permission_id'  	=> $this->input->post('izin'),
            'nama_lengkap'  	=> $this->input->post('nama'),
            'email_address'  	=> $this->input->post('email'),
            'password'  		=> password_hash($this->input->post('password'), PASSWORD_DEFAULT),
            'is_status'  		=> $this->input->post('status'),
            'created_at'  		=> date('Y-m-d H:i:s')
        ];
        $res = $this->db->insert('m_user', $data);
        return $res;
    }

    public function edit_proses($id,$passwordlama) {
    	$pass = $this->input->post('password');
        if ($pass=='') {
            $passnya = $passwordlama;
        }else{
            $passnya = password_hash($this->input->post('password'), PASSWORD_DEFAULT);
        }

        $this->db->set([
            'role_id'  			=> $this->input->post('roles'),
            'permission_id'  	=> $this->input->post('izin'),
            'nama_lengkap'  	=> $this->input->post('nama'),
            'email_address'  	=> $this->input->post('email'),
            'password'  		=> $passnya,
            'is_status'  		=> $this->input->post('status')
        ]);
        $this->db->where('user_id', $id);
        $res = $this->db->update('m_user');
        return $res;
    }

}