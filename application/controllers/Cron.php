<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Cron extends CI_Controller {
    public function __construct() {
        parent::__construct();
        $this->load->model('user/attendance_model', 'att');
    }

    public $att;

    // This function is called by the cron job to update attendance data
    
    public function index() {
        $today = date('Y-m-d');
        $this->att->get_data($today,'n',true);
        
    }

}
