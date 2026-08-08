<?php
class Api_test extends CI_Controller {
    public function test() {
        $this->load->model('user/kpi_absensi_model', 'kpi_m');
        $res = $this->kpi_m->calculate_kpi(78, 11, 2025); // from the db test, peg 78 had 00:00
        echo json_encode($res);
    }
}
