<?php

class Console extends CI_Controller{
  public $email;
  public $session;
  public $form_validation;
  public $upload;
  public $pagination;
  public $s3;

  function __construct() {
    
    parent::__construct();
    
    header('Access-Control-Allow-Origin: *');
    header("Access-Control-Allow-Methods: PUT, GET, POST, DELETE, OPTIONS");
    header("Access-Control-Allow-Headers: Origin, Content-Type, Authorization, Accept, X-Requested-With, x-xsrf-token");
    header("Content-Type: application/json; charset=utf-8");
    
    $this->load->model('S3_model','s3');

  }
  
  function login(){
    $json = file_get_contents('php://input');
    $data = json_decode($json, true);
    $q = $this->db-> query("select * from dev where username = ?",[$data['username']])->row_array();
    $pwdVerify = $q ? password_verify($data['password'],$q['password']) : false;
    if($q && $pwdVerify) echo json_encode(['success' => true,'token' => bin2hex(random_bytes(32))]);
    if($q && !$pwdVerify) echo json_encode(['success' => false,'message' => 'invalid password']);
    if(!$q) echo json_encode(['success' => false,'message' => 'no developer account found']);
  }
  
  function companylist(){
      $list = $this->db->query("select * from companies")->result_array();
      echo json_encode($list);
  }

  function locationlist($cId){
     $query = "select * from m_lokasi ml join companies c on ml.company_id = c.id where c.id = ? and ml.is_del='n'";
     $list =  $this->db->query($query,[$cId])->result_array();
     echo json_encode($list);
  }
  
  function add_location($cId){
    if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
        http_response_code(200);
        exit;
    }  
      
    $createdAt = date('Y-m-d H:i:s');
    $data = json_decode(file_get_contents('php://input'), true);
    $additional = ['company_id' => $cId,'is_del' => 'n','created_at' => $createdAt];
    $this->db->insert('m_lokasi',[...$data,...$additional]);
    echo json_encode(['success' => true]);
  }
  
  function edit_location($locationId){
    $data = json_decode(file_get_contents('php://input'), true);
    
    if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
        http_response_code(200);
        exit;
    }
    else{
        $this->db->where('lokasi_id',$locationId);
        $this->db->update('m_lokasi',[...$data]);
        echo json_encode(['success' => true]);        
    }
  }
  
  function add_company(){
    $this->db->insert('companies', [
        'company_name' => $this->input->post('company_name'),
        'phone'        => $this->input->post('phone'),
        'address'      => $this->input->post('address'),
        'email'        => $this->input->post('email'),
        'erpId'        => 'xxx'
    ]);
    
    $companyId = $this->db->insert_id();
    $adminPwd = $this->input->post('admin_password');
    $pwdHash = password_hash($adminPwd,PASSWORD_BCRYPT);

    $this->db->insert('m_user',[
      'company_id' => $companyId,
      'role_id' => 1,
      'permission_id' => 1,
      'nama_lengkap' => $this->input->post('admin_name'),
      'email_address' => $this->input->post('email'),
      'password' => $pwdHash,
      'is_status' => 'y',
      'is_del' => 'n',
      'permission' => 'rw',
      'created_at' => date('Y-m-d H:i:s')
    ]);
    
    $name = $_FILES['logo']['name'];
    $tmpName = $_FILES['logo']['tmp_name'];
    $type = $_FILES['logo']['type'];
    $fileName = time() . '_' . basename($name);
          
    $r = $this->s3->upload(
        $fileName,
        $companyId,
        'logo',
        $tmpName,
        $type
    );  
    
    $this->db->where('id', $companyId)->update(
        'companies', ['logo' => $r]
    );
    
    
    echo json_encode(
        [
            'succcess' => true
        ]
    );
  }
}
