<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Sliders_model extends CI_Model {

    public function __construct() {
        parent::__construct();
    }
	
    public function get_data() {
        $query = $this->db->query("SELECT * FROM m_slider")->result_array();
        return $query;
    }

    public function get_sliders() {
        $query = $this->db->query("SELECT * FROM m_slider WHERE is_status='y'")->result_array();
        return $query;
    }

    public function add_proses($upload = '') {

        $tipe = $this->input->post('tipe');

        if($tipe==1){
            $file_logo = '';
        }else{
            $file_logo = $upload['path'].$upload['file']['file_name']; 
        }

        $data = [
            'isi_slider'  		=> $this->input->post('ilink'),
            'is_tipe'  	        => $this->input->post('tipe'),
            'is_status'  		=> $this->input->post('status'),
            'gambar_slider'  	=> $file_logo,
            'created_at'  		=> date('Y-m-d H:i:s')
        ];
        $res = $this->db->insert('m_slider', $data);
        return $res;
    }

    public function edit_proses($id = '', $ceklogo = '', $upload = '', $imgold = '') {

        $tipe = $this->input->post('tipe');

        if($tipe==1){
            $file_logo = '';
        }else{
            if ($ceklogo=='') { 
                $file_logo = $imgold; 
            }else{ 
                $file_logo = $upload['path'].$upload['file']['file_name']; 
            }
        }

        

		$this->db->set([
            'isi_slider'  		=> $this->input->post('ilink'),
            'is_tipe'  	        => $this->input->post('tipe'),
            'is_status'  		=> $this->input->post('status'),
            'gambar_slider'     => $file_logo
        ]);
        $this->db->where('slider_id', $id);
        $res = $this->db->update('m_slider');

        return $res;

	}

}