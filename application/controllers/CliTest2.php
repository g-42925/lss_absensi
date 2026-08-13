<?php
class CliTest2 extends CI_Controller {
    public function index() {
        $q = $this->db->query("SHOW COLUMNS FROM companies")->result_array();
        echo json_encode($q);
    }
}
