<?php

class Pinata extends CI_Controller {
    public function __construct() {
        parent::__construct();
    }

    public $att;
    public $email;
    public $session;
    public $form_validation;
    public $upload;
    public $pagination;
    
    private function uploadToPinata($file, $fileName, $path) {
      $jwt = 'eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJ1c2VySW5mb3JtYXRpb24iOnsiaWQiOiJmYWUxOTIzYi0wNTlkLTQyMTQtODFhNS03NDk3MDQxMjRmMTgiLCJlbWFpbCI6Imxlcnluc29mdHdhcmVAZ21haWwuY29tIiwiZW1haWxfdmVyaWZpZWQiOnRydWUsInBpbl9wb2xpY3kiOnsicmVnaW9ucyI6W3siZGVzaXJlZFJlcGxpY2F0aW9uQ291bnQiOjEsImlkIjoiRlJBMSJ9LHsiZGVzaXJlZFJlcGxpY2F0aW9uQ291bnQiOjEsImlkIjoiTllDMSJ9XSwidmVyc2lvbiI6MX0sIm1mYV9lbmFibGVkIjpmYWxzZSwic3RhdHVzIjoiQUNUSVZFIn0sImF1dGhlbnRpY2F0aW9uVHlwZSI6InNjb3BlZEtleSIsInNjb3BlZEtleUtleSI6IjU4ZDA1YTUxODZiMDAzMThjMjdiIiwic2NvcGVkS2V5U2VjcmV0IjoiOGQ4NmFmNzhlMTliZWE1MTA5NDQ3NTE1MjJlYTNjZTE0MTU1NzhmNDMyMDMzZjNiZjNhOGE2Y2EzMGJlYWZiNCIsImV4cCI6MTgxNDM0MjY5MX0.MlVFt53T6gTeVoi9nVVD1xq5YsK1n15A70ycZDf8i2o';
      $postFields = [ "network" => "public", "file" => new CURLFile($file), "name" => $fileName, "keyvalues" => json_encode([ "path" => $path ])];
      
      $curl = curl_init();

      curl_setopt_array($curl, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_POST => true,
        CURLOPT_POSTFIELDS => $postFields,
        CURLOPT_HTTPHEADER => [ "Authorization: Bearer {$jwt}" ]
      ]);

      $response = curl_exec($curl);

      if (curl_errno($curl)) throw new Exception(curl_error($curl));

      $result = json_decode($response, true);

      if (!isset($result['IpfsHash'])) throw new Exception($response);
    
      return [
          "cid" => $result["data"]["cid"],
          "url" => "https://brown-rational-hornet-626.mypinata.cloud/ipfs/" . $result["data"]["cid"]
      ];
    }

    public function attendance($fileName, $id) {
      $company = $this->db->query("SELECT * FROM companies WHERE id = ?", [$id])->row_array();

      $rootDir = explode("@", $company["email"])[0];

      $result = $this->uploadToPinata($_FILES["file"]["tmp_name"], $fileName, 'absensi_'.$rootDir .'_attendance');

      echo $result["url"];
    }

    public function unknown($fileName, $id) {
      $company = $this->db->query("SELECT * FROM companies WHERE id = ?", [$id])->row_array();

      $rootDir = explode("@", $company["email"])[0];

      $result = $this->uploadToPinata($_FILES["file"]["tmp_name"], $fileName, 'absensi_'.$rootDir .'_unknown');

      echo $result["url"];
    }

    public function exception($fileName, $id) {
      $company = $this->db->query("SELECT * FROM companies WHERE id = ?", [$id])->row_array();

      $rootDir = explode("@", $company["email"])[0];

      $result = $this->uploadToPinata($_FILES["file"]["tmp_name"], $fileName, 'absensi_'.$rootDir .'_exception');

      echo $result["url"];
    }

    public function task($fileName, $id) {
      $company = $this->db->query("SELECT * FROM companies WHERE id = ?", [$id])->row_array();

      $rootDir = explode("@", $company["email"])[0];

      $result = $this->uploadToPinata($_FILES["file"]["tmp_name"], $fileName, 'absensi_'.$rootDir .'_task');

      echo $result["url"];
     }

    public function upload($fileName, $id) {
      $company = $this->db->query("SELECT * FROM companies WHERE id = ?", [$id])->row_array();

      $rootDir = explode("@", $company["email"])[0];

      $result = $this->uploadToPinata($_FILES["file"]["tmp_name"], $fileName, 'absensi_'.$rootDir .'unknown');

      echo $result["url"];
    }

}