<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Permission_model extends CI_Model {

    public function __construct() {
        parent::__construct();
    }

	public function get_data() {
        $query = $this->db->query("SELECT * FROM m_permission WHERE is_del='n' AND is_status='y'")->result_array();
        return $query;
    }

    public function add_proses() {

        $tambah = $this->input->post('tambah');
        $edit = $this->input->post('edit');
        $hapus = $this->input->post('hapus');
        if ($tambah=='on') { $tambah = 'y'; }else{ $tambah = 'n'; }
        if ($edit=='on') { $edit = 'y'; }else{ $edit = 'n'; }
        if ($hapus=='on') { $hapus = 'y'; }else{ $hapus = 'n'; }

        $data = [
            'nama_permission'  	=> $this->input->post('nama'),
            'tambah'  	        => $tambah,
            'edit'  	        => $edit,
            'hapus'             => $hapus,
            'created_at'  		=> date('Y-m-d H:i:s')
        ];
        $res = $this->db->insert('m_permission', $data);
        return $res;
    }

    public function edit_proses($id) {

        $tambah = $this->input->post('tambah');
        $edit = $this->input->post('edit');
        $hapus = $this->input->post('hapus');
        if ($tambah=='on') { $tambah = 'y'; }else{ $tambah = 'n'; }
        if ($edit=='on') { $edit = 'y'; }else{ $edit = 'n'; }
        if ($hapus=='on') { $hapus = 'y'; }else{ $hapus = 'n'; }

        $this->db->set([
            'nama_permission'   => $this->input->post('nama'),
            'tambah'            => $tambah,
            'edit'              => $edit,
            'hapus'             => $hapus
        ]);
        $this->db->where('permission_id', $id);
        $res = $this->db->update('m_permission');
        return $res;
    }

}