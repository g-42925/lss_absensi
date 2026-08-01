<?php

class Console extends CI_Controller{
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
  
  function companylist(){
      $list = $this->db->query("select * from companies where active='1'")->result_array();
      echo json_encode($list);
  }

  function locationlist($cId){
     $query = "select * from m_lokasi ml join companies c on ml.company_id = c.id where c.id = ? and ml.is_del='n'";
     $list =  $this->db->query($query,[$cId])->result_array();
     echo json_encode($list);
  }
  
  function addLocation($cId){
    $json = file_get_contents('php://input');
    $data = json_decode($json, true);
    
    $locationName = $data['nama_lokasi'];
    $address = $data['alamat_lokasi'];
    $latitude = $data['garis_lintang'];
    $longitude = $data['garis_bujur'];
    $radius = $data['jangkauan_radius'];
    
    $this->db->insert('m_lokasi', [
        'nama_lokasi' => $locationName,
        'alamat_lokasi' => $address,
        'garis_lintang' => $latitude,
        'garis_bujur' => $longitude,
        'company_id' => $cId, 
        'jangkauan_radius' => $radius,
        'is_del' => 'n'
    ]);


    if ($this->db->affected_rows() > 0) {
        echo json_encode([
            'success' => true,
            'message' => 'Data berhasil disimpan'
        ]);
    } else {
        echo json_encode([
            'success' => false,
            'message' => 'Gagal menyimpan data'
        ]);
    }
  }
  
  function updatelocation($locationId){
    $locationName = $this->input->post('nama_lokasi');
    $address = $this->input->post('alamat_lokasi');
    $latitude = $this->input->post('garis_lintang');
    $longitude = $this->input->post('garis_bujur');
    $radius = $this->input->post('jangkauan_radius');
    $this->db->where('lokasi_id',$this->input->post('lokasi_id'));
     
    $params = [
      'nama_lokasi' => $locationName,
    ];
    
    $result = $this->db->update('m_lokasi',$params);

    if ($result) {
        echo json_encode([
            'success' => true,
            'message' => 'Data berhasil diupdate'
        ]);
    } 
    else {
        echo json_encode([
            'success' => false,
            'message' => 'Data gagal diupdate'
        ]);
    }
  }
}
