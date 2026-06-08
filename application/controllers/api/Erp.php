<?php

class Erp extends CI_Controller{
  public $email;
  public $session;
  public $form_validation;
  public $upload;
  public $pagination;

  function __construct() {
    parent::__construct();
    header('Access-Control-Allow-Origin: *');
    header("Access-Control-Allow-Methods: PUT, GET, POST, DELETE, OPTIONS");
    header("Access-Control-Allow-Headers: Origin, Content-Type, Authorization, Accept, X-Requested-With, x-xsrf-token");
    header("Content-Type: application/json; charset=utf-8");
  }

  function emp($erpId){
    $query = "select * from companies c join m_pegawai mp on c.id = mp.company_id where c.erpId = ? and mp.is_del = 'n'";
    $employeeList = $this->db->query($query,[$erpId])->result_array();
    echo json_encode($employeeList);
  }
}
