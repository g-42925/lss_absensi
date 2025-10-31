<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Profile_model extends CI_Model {

	public function edit_proses($ceklogo,$upload) {
		// $company = pengaturanSistem();
		// if ($ceklogo=='') { 
		// 	$file_logo = $company['logo_perusahaan']; 
		// }else{ 
		// 	$file_logo = $upload['path'].$upload['file']['file_name']; 
		// }

        $companyId = $this->session->userdata('company_id');

        if(!isset($upload['file']['filename'])){
          $file_logo = 'zzz'; 
        }
        else{
            $file_logo= '';
        }

		$this->db->set([
            'id' => $companyId,
            'name'      => $this->input->post('name'),
            'address'    => $this->input->post('address'),
            'phone'     => $this->input->post('phone'),
            'email'     => $this->input->post('email'),
            'logo'      => $file_logo
        ]);
        
        $this->db->where('id', $companyId);
        $res = $this->db->update('companies');

        return $res;

	}
}