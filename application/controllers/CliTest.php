<?php
class CliTest extends CI_Controller {
    public function index() {
        $q = $this->db->query("SELECT * FROM companies")->result_array();
        print_r($q);
        $w = $this->db->query("SELECT * FROM warning")->result_array();
        print_r($w);
    }
}
