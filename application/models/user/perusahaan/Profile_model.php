<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Profile_model extends CI_Model {

	public function edit_proses($ceklogo,$upload) {
		$company = pengaturanSistem();
		if ($ceklogo=='') { 
			$file_logo = $company['logo_perusahaan']; 
		}else{ 
			$file_logo = $upload['path'].$upload['file']['file_name']; 
		}

		$this->db->set([
            'nama_perusahaan'      => $this->input->post('nama'),
            'alamat_perusahaan'    => $this->input->post('alamat'),
            'nomor_perusahaan'     => $this->input->post('nomor'),
            'email_perusahaan'     => $this->input->post('email'),
            'label_informasi_app'  => $this->input->post('label_sambutan'),
            'text_informasi_app'   => $this->input->post('label_info'),
            'logo_perusahaan'      => $file_logo
        ]);
        $this->db->where('setting_id', 1);
        $res = $this->db->update('m_setting');

        return $res;

	}
}