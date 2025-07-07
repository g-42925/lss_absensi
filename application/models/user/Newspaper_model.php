<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Newspaper_model extends CI_Model {

    public function __construct() {
        parent::__construct();
    }
	
    public function get_data() {
        $query = $this->db->query("SELECT * FROM m_berita ORDER BY nama_berita ASC")->result_array();
        return $query;
    }

    public function get_berita() {
        $query = $this->db->query("SELECT * FROM m_berita WHERE is_status='y'")->result_array();
        return $query;
    }

    public function get_berita_id($id) {
        $query = $this->db->query("SELECT * FROM m_berita WHERE is_status='y' AND berita_id='$id'")->row_array();
        return $query;
    }

    public function add_proses($ceklogo,$upload) {

        if ($ceklogo=='') { 
			$file_logo = ''; 
		}else{ 
			$file_logo = $upload['path'].$upload['file']['file_name']; 
		}

        $data = [
            'nama_berita'  		=> $this->input->post('nama'),
            'isi_berita'  	    => $this->input->post('berita'),
            'is_status'  		=> $this->input->post('status'),
            'gambar_berita'  	=> $file_logo,
            'created_at'  		=> date('Y-m-d H:i:s')
        ];
        $res = $this->db->insert('m_berita', $data);
        return $res;
    }

    public function edit_proses($id,$ceklogo,$upload,$imgold) {
		if ($ceklogo=='') { 
			$file_logo = $imgold; 
		}else{ 
			$file_logo = $upload['path'].$upload['file']['file_name']; 
		}

		$this->db->set([
            'nama_berita'  		=> $this->input->post('nama'),
            'isi_berita'  	    => $this->input->post('berita'),
            'is_status'  		=> $this->input->post('status'),
            'gambar_berita'     => $file_logo
        ]);
        $this->db->where('berita_id', $id);
        $res = $this->db->update('m_berita');

        return $res;

	}

}