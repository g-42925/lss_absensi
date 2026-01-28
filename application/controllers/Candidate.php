<?php

use Aws\S3\S3Client;
use Aws\Credentials\Credentials;
use Aws\Exception\AwsException;

defined('BASEPATH') or exit('No direct script access allowed');

class Candidate extends CI_Controller {

    public $email;
    public $session;
    public $form;
    public $upload;
    public $pagination;
    public $other;
    public $menu;
    public $data;
    public $patterns;
    public $tw;
    public $form_validation;
    


    public function __construct() {
        parent::__construct();
        is_logged_in();
        $this->load->library('form_validation');
        $this->load->model('other_model', 'other');
        $this->load->model('user/menu_model', 'menu');
        $this->load->model('user/karyawan/data_model', 'data');
        $this->load->model('user/patterns_model', 'patterns');
        $this->load->model('user/karyawan/timework_model', 'tw');
    }

    public function accept_proccess($candidateId){
        $companyId = $this->session->userdata('company_id');
        $totalEmployee = $this->db->query("select * from m_pegawai where company_id = ?",[$companyId])->num_rows();
        $company = $this->db->query("select * from companies where id = ?",[$companyId])->row_array();
        $idPegawai = str_pad($totalEmployee,3,'0',STR_PAD_LEFT);

        $idsync = date('Ymdhis').$this->input->post('nom');

        $initials = "";

        foreach(explode(" ", $company['company_name']) as $w){
          $initials = $initials . substr($w, 0, 1);
        };

        $nik = $this->input->post('nik');

        $data = [
            'id_sync'             => $idsync,
            'id_pegawai'          => $initials."-".$idPegawai."-".substr($nik, -3),
            'company_id'          => $companyId,
            'nama_pegawai'  	  => $this->input->post('nama'),
            'email_pegawai'  	  => $this->input->post('email'),
            'nomor_pegawai'       => $this->input->post('nom'),
            'jenis_kelamin'       => $this->input->post('jeniskelamin'),
            'tanggal_mulai_kerja' => $this->input->post('tglmulai'),
            'jumlah_cuti'         => $this->input->post('jumlahCuti'),
            'salary'              => $this->input->post('salary'),
            'division_id'         => $this->input->post('division'),
            'password_pegawai'    => password_hash($this->input->post('password'), PASSWORD_DEFAULT),
            'foto_pegawai'        => 'assets/uploaded/users/default-logo.png',
            'is_status'  		  => $this->input->post('status'),
            'status_pegawai'      => $this->input->post('statusPegawai'),
            'position_id'         => $this->input->post('position'),
            'created_at'  		  => date('Y-m-d H:i:s'),
            'nik'                 => $this->input->post('nik'),
            'contract_start_date' => $this->input->post('contract_start_date'),
            'contract_end_date'   => $this->input->post('contract_end_date'),
            'on_training'         => $this->input->post('on_training')
        ];

        $this->db->trans_begin();
        
        $qAssets = $this->db->query("select * from candidate_file where candidate_id = ?",[$candidateId])->result_array();
        $qByNik = $this->db->query("select * from m_pegawai where company_id = ? and nik = ?",[$companyId,$nik])->row_array();

        if(!$qByNik){
            $this->db->insert('m_pegawai',$data);
            $newInsertedId = $this->db->insert_id();

            $absence = [
                'absen_id' => uniqid(),
                'company_id' => $companyId,
                'pegawai_id' => $newInsertedId,
                'tanggal_absen' => date('Y-m-d'),
                'is_status' => 'alpha-2',
                'jam_masuk' => '00:00',
                'jam_istirahat' => '00:00',
                'jam_sistirahat' => '00:00',
                'jam_keluar' => '00:00',
                'catatan_masuk' => '...',
                'catatan_keluar' => '...',
                'j_masuk' => '00:00',
                'j_pulang' => '00:00',
                'j_toleransi' => '00:00',
                's_istirahat_photo' => 'no',
                's_istirahat_latitude' => '',
                's_istirahat_longitude' => '',
                'foto_absen_masuk' => 'no',
                'foto_absen_keluar' => 'no',
                'point_latitude' => 0,
                'point_longitude' => 0,
                'latitude_masuk' => 0,
                'longitude_masuk' => 0,
                'latitude_keluar' => 0,
                'longitude_keluar' => 0,
                'is_point_masuk' => 'n',
                'is_point_keluar' => 'n',
                'is_request' => 0,
                'acc_masuk' => 'y',
                'acc_keluar' => 'y',
                'is_pending' => 'n',
                'htu' => 0,
                'isLate' => 0
            ];

            $this->db->insert('tx_absensi',$absence);
            $this->db->where('candidate_id', $candidateId);
            $this->db->delete('candidate');

            foreach($qAssets as $a){
              $data = [
                'employee_file_id' => uniqid(),
                'file_id' => $a['file_id'],
                'employee_id' => $newInsertedId,
                'source' => $a['source']
              ];

              $this->db->insert('employee_file',$data);
              $this->db->where('candidate_file_id', $a['candidate_file_id']);
              $this->db->delete('candidate_file');
            }

            if($this->db->trans_status() === FALSE) {
                $this->db->trans_rollback();
                $this->session->set_flashdata(
                    'message','<div class="alert alert-danger">Proses gagal. Silakan coba lagi.</div>'
                );
                redirect(
                    'candidate/accept/' . $candidateId . '?failed=true'
                );
            } 
            else{
                $this->db->trans_commit();
                redirect('karyawan/data');
            }
        }
        else{
            $this->session->set_flashdata(
                'message','<div class="alert alert-danger">Proses gagal. Silakan coba lagi.</div>'
            );
            redirect(
                'candidate/accept/' . $candidateId . '?failed=true'
            );
        }
    }

    public function accept($candidateId){
        cek_menu_access();
        $data['htmlpagejs'] = 'none';
        $data['nmenu']      = 'Karyawan';
        $data['title']      = 'Recruitment';
        $data['namalabel']  = $data['title'];
        $data['auth']       = authUser();
       
 
        $companyId = $this->session->userdata('company_id');

        $data['candidateId'] = $candidateId;
        
        $data['roles'] = $this->other->get_roles($companyId);
        $data['permission'] = $this->other->get_permission();
        $data['failed'] = filter_var($this->input->get('failed'),FILTER_VALIDATE_BOOLEAN);
        $data['mindate'] = date("Y-m-d", strtotime(date('Y-m-d')." -7 day"));
        $data['maxdate'] = date("Y-m-d", strtotime(date('Y-m-d')." +7 day"));

        $data['divisions'] = $this->db->query("select * from divisions where company_id = ?",[$companyId])->result_array();

        $data['position'] = $this->db->query("select * from position where company_id = ?",[$companyId])->result_array();

        $data['candidate'] = $this->db->query("select *from candidate where candidate_id = ?",[$candidateId])->row_array();

        $this->load->view('templates/header', $data);
        $this->load->view('templates/sidemenu', $data);
        $this->load->view('templates/sidenav', $data);
        $this->load->view('module/candidate/accept', $data);
        $this->load->view('templates/footer', $data);
        $this->load->view('templates/fscript-html-end', $data);

    }

     public function index() {
        cek_menu_access();
        $data['htmlpagejs'] = 'none';
        $data['nmenu']      = 'Karyawan';
        $data['title']      = 'Recruitment';
        $data['namalabel']  = 'Pengaturan '.$data['title'].' Karyawan';
        $data['auth']       = authUser();

        $companyId = $this->session->userdata('company_id');

        $data['data'] = $this->db->query("select * from candidate where company_id = ?",[$companyId])->result_array();

        $this->load->view('templates/header', $data);
        $this->load->view('templates/sidemenu', $data);
        $this->load->view('templates/sidenav', $data);
        $this->load->view('module/candidate/index',$data);
        $this->load->view('templates/footer', $data);
        $this->load->view('templates/fscript-html-end', $data);
    }

    public function add(){
        $data['failed'] = filter_var($this->input->get('failed'),FILTER_VALIDATE_BOOLEAN);
        
        cek_menu_access();
        isCreatable();
        $data['htmlpagejs'] = 'none';
        $data['nmenu']      = 'Karyawan';
        $data['title']      = 'Recruitment';
        $data['namalabel']  = $data['title'];
        $data['auth']       = authUser();
        $companyId = $this->session->userdata('company_id');

        $data['failed'] = filter_var($this->input->get('failed'),FILTER_VALIDATE_BOOLEAN);
        $data['position'] = $this->db->query("select * from job j join position p on j.position_id = p.id where p.company_id = ?",[$companyId])->result_array();

        $this->load->view('templates/header', $data);
        $this->load->view('templates/sidemenu', $data);
        $this->load->view('templates/sidenav', $data);
        $this->load->view('module/candidate/add',$data);
        $this->load->view('templates/footer', $data);
        $this->load->view('templates/fscript-html-end', $data);
    }

    public function edit($candidateId){        
        cek_menu_access();
        isEditable();
        $data['htmlpagejs'] = 'none';
        $data['nmenu']      = 'Karyawan';
        $data['title']      = 'Recruitment';
        $data['namalabel']  = $data['title'];
        $data['auth']       = authUser();

        $data['id'] = $candidateId;
        $companyId = $this->session->userdata('company_id');
        $data['failed'] = filter_var($this->input->get('failed'),FILTER_VALIDATE_BOOLEAN);

        $data['candidate'] = $this->db->query("select * from candidate where candidate_id = ?",[$candidateId])->row_array();
        $data['position'] = $this->db->query("select * from job j join position p on j.position_id = p.id where p.company_id = ?",[$companyId])->result_array();

        $this->load->view('templates/header', $data);
        $this->load->view('templates/sidemenu', $data);
        $this->load->view('templates/sidenav', $data);
        $this->load->view('module/candidate/edit',$data);
        $this->load->view('templates/footer', $data);
        $this->load->view('templates/fscript-html-end', $data);
    }

    public function edit_proccess($candidateId){
       $file = $_FILES['photo'];
       $cFile = $_FILES['photo']['tmp_name'];
       $fileName = time() . '_' . basename($file['name']);


        if($this->input->post('changeMarker') == 1){
            if (!$file || $file['error'] !== UPLOAD_ERR_OK) {
                $this->session->set_flashdata('message','<div class="alert alert-danger">Proses gagal. Silakan coba lagi.</div>');
                redirect('candidate/edit/'.$candidateId.'?failed=true');
            }
            else{
                $s3 = new S3Client([
                    'version'     => 'latest',
                    'region'      => 'us-east-1',
                    'endpoint'    => 'https://s3.filebase.com',
                    'use_path_style_endpoint' => false,
                    'credentials' => [
                    'key'    => 'B8F0135956143AE0685E',
                    'secret' => 'gKrbIZJnzLWBXZ0VGQvnlAumvngpBH35PsXN5zUp'
                    ],
                    'Metadata' => [
                    'cid' => 'true'
                    ],
                ]);

                $result = $s3->putObject([
                    'Bucket' => 'leryn-storage',
                    'Key'    => $fileName,
                    'SourceFile' => $cFile,
                    'ContentType' => 'image/png',
                ]);

                $cid = $result['@metadata']['headers']['x-amz-meta-cid'];
                $r = "https://wooden-plum-woodpecker.myfilebase.com/ipfs/".$cid;


                $data = [
                    'nik' => $this->input->post('nik'),
                    'phone_number' => $this->input->post('phone_number'),
                    'email' => $this->input->post('email'),
                    'sex' => $this->input->post('sex'),
                    'candidate_picture' => $r,
                    'candidate_name' => $this->input->post('name'),
                    'position_id' => $this->input->post('position_id')
                ];

                $this->db->set(
                    $data
                );
                $this->db->where(
                    'candidate_id', 
                    $candidateId
                );
                $q = $this->db->update(
                    'candidate'
                );

                if(!$q){
                    $this->session->set_flashdata(
                        'message','<div class="alert alert-danger">Proses gagal. Silakan coba lagi.</div>'
                    );
                    redirect(
                        'candidate/edit/'.$candidateId.'?failed=true'
                    );
                }
                else{
                    redirect(
                        'candidate'
                    );
                }
            }
        }
        else{
            $data = [
                'nik' => $this->input->post('nik'),
                'phone_number' => $this->input->post('phone_number'),
                'email' => $this->input->post('email'),
                'sex' => $this->input->post('sex'),
                'candidate_name' => $this->input->post('name'),
                'position_id' => $this->input->post('position_id')
            ];

            $this->db->set(
                $data
            );
            $this->db->where(
                'candidate_id', 
                $candidateId
            );
            $q = $this->db->update(
                'candidate'
            );

            if(!$q){
                $this->session->set_flashdata(
                    'message','<div class="alert alert-danger">Proses gagal. Silakan coba lagi.</div>'
                );
                redirect(
                    'candidate/edit/'.$candidateId.'?failed=true'
                );
            }
            else{
                redirect(
                    'candidate'
                );
            }
        }
    }

    public function add_proccess(){
       $file = $_FILES['photo'];
       $cFile = $_FILES['photo']['tmp_name'];
       $fileName = time() . '_' . basename($file['name']);
       

        if(!$file || $file['error'] !== UPLOAD_ERR_OK) {
            $this->session->set_flashdata(
                'message','<div class="alert alert-danger">Proses gagal. Silakan coba lagi.</div>'
            );
            redirect(
                'candidate/add?failed=true'
            );
        }
        else{
            $s3 = new S3Client([
                'version'     => 'latest',
                'region'      => 'us-east-1',
                'endpoint'    => 'https://s3.filebase.com',
                'use_path_style_endpoint' => false,
                'credentials' => [
                'key'    => 'B8F0135956143AE0685E',
                'secret' => 'gKrbIZJnzLWBXZ0VGQvnlAumvngpBH35PsXN5zUp'
                ],
                'Metadata' => [
                'cid' => 'true'
                ],
            ]);

            $result = $s3->putObject([
                'Bucket' => 'leryn-storage',
                'Key'    => $fileName,
                'SourceFile' => $cFile,
                'ContentType' => 'image/png',
            ]);

            $cid = $result['@metadata']['headers']['x-amz-meta-cid'];
            $r = "https://wooden-plum-woodpecker.myfilebase.com/ipfs/".$cid;

            $data = [
                'candidate_id' => uniqid(),
                'company_id' => $companyId = $this->session->userdata('company_id'),
                'nik' => $this->input->post('nik'),
                'phone_number' => $this->input->post('phone_number'),
                'email' => $this->input->post('email'),
                'sex' => $this->input->post('sex'),
                'candidate_picture' => $r,
                'candidate_name' => $this->input->post('name'),
                'position_id' => $this->input->post('position_id')
            ];

            $q = $this->db->insert(
                'candidate',
                $data
            );

            if(!$q){
                $this->session->set_flashdata(
                    'message','<div class="alert alert-danger">Proses gagal. Silakan coba lagi.</div>'
                );
                redirect(
                    'candidate/add?failed=true'
                );
            }
            else{
                redirect(
                    'candidate'
                );
            }
        }
    }

}
