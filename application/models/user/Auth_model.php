<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Auth_model extends CI_Model {

    public function proses_login($username) {
        $this->db->where("email_address='$username'");
        return $this->db->get('m_user')->row_array();
    }

}
