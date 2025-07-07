<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Roles_model extends CI_Model {

    public function __construct() {
        parent::__construct();
    }

	public function get_data() {
        $query = $this->db->query("SELECT * FROM m_role WHERE is_del='n' AND is_status='y'")->result_array();
        return $query;
    }

    public function add_proses() {
        $data = [
            'nama_role'  		=> $this->input->post('nama'),
            'is_status'  		=> $this->input->post('status'),
            'created_at'  		=> date('Y-m-d H:i:s')
        ];
        $res = $this->db->insert('m_role', $data);
        $idnya = $this->db->insert_id();

        if ($res==true) {
            $hakrole = $this->input->post('roles');
            if ($hakrole!='') {
                foreach ($hakrole as $key => $value) {
                    if($value!=""){
                        $explo=explode("~",$value);
                        $value= $explo[1];
                        $query = $this->db->query("SELECT * FROM m_role_access WHERE id_role='$idnya' AND id_menu='$explo[0]'");
                        $cekrow = $query->num_rows();
                        if($cekrow==0){
                            $data = [
                                'id_role' => $idnya,
                                'id_menu' => $explo[0]
                            ];
                            $this->db->insert('m_role_access', $data);
                        }
                        $query = $this->db->query("SELECT * FROM m_menu WHERE menu_id='$explo[0]'")->row_array();
                        if ($query['tipe']==1) {
    	                    $data = [
    	                        'id_role' => $idnya,
    	                        'id_menu' => $explo[1]
    	                    ];
    	                    $this->db->insert('m_role_access', $data);
    	                }
                    }
                }
            }
        }

        return $res;
    }

    public function edit_proses($id) {

        $this->db->set([
            'nama_role'    => $this->input->post('nama'),
            'is_status'    => $this->input->post('status')
        ]);
        $this->db->where('role_id', $id);
        $res = $this->db->update('m_role');

        if ($res==true) {
            $this->db->delete('m_role_access', ['id_role' => $id]);
            $hakrole = $this->input->post('roles');
            if ($hakrole!='') {
                foreach ($hakrole as $key => $value) {
                    if($value!=""){
                        $explo=explode("~",$value);
                        $value= $explo[1];
                        $query = $this->db->query("SELECT * FROM m_role_access WHERE id_role='$id' AND id_menu='$explo[0]'");
                        $cekrow = $query->num_rows();
                        if($cekrow==0){
                            $data = [
                                'id_role' => $id,
                                'id_menu' => $explo[0]
                            ];
                            $this->db->insert('m_role_access', $data);
                        }
                        $query = $this->db->query("SELECT * FROM m_menu WHERE menu_id='$explo[0]'")->row_array();
                        if ($query['tipe']==1) {
                            $data = [
                                'id_role' => $id,
                                'id_menu' => $explo[1]
                            ];
                            $this->db->insert('m_role_access', $data);
                        }
                    }
                }
            }
        }

        return $res;
    }

}