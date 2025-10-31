<?php
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

        $nik = $this->input->post('nik');

        $params = [
            'company_id' =>  $companyId,
            'division_id' => $this->input->post('division'),
            'position_id' => $this->input->post('position'),
            'id_sync' => '2025100205064608120812081',
            'id_pegawai' => $this->input->post('id_pegawai'),
            'nik' => $nik,
            'nama_pegawai' => $this->input->post('nama_pegawai'),
            'email_pegawai' => $this->input->post('email_pegawai'),
            'nomor_pegawai' => $this->input->post('nomor_pegawai'),
            'password_pegawai' => password_hash($this->input->post('password'), PASSWORD_DEFAULT),
            'jenis_kelamin' => $this->input->post('jeniskelamin'),
            'tanggal_mulai_kerja' => $this->input->post('tgl_mulai'),
            'foto_pegawai' => $this->input->post('foto_pegawai'),
            'jumlah_cuti' => $this->input->post('jumlahCuti'),
            'salary' => $this->input->post('salary'),
            'is_status' => $this->input->post('status'),
            'status_pegawai' => $this->input->post('statusPegawai'),
            'created_at' => date('Y-m-d H:i:s'),
            'is_del' => 'n',
        ];

        $this->db->trans_begin();
        
        $qAssets = $this->db->query("select * from candidate_file where candidate_id = ?",[$candidateId])->result_array();
        $qByNik = $this->db->query("select * from m_pegawai where company_id = ? and nik = ?",[$companyId,$nik])->row_array();

        if(!$qByNik){
            $this->db->insert('m_pegawai',$params);
            $newInsertedId = $this->db->insert_id();

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
       
        // if($data['auth']['tambah']!='y'){
        //     $this->session->set_flashdata('message', '<div class="me-3 ms-3 mt-3"><div class="alert alert-danger p-cg" role="alert">Tidak ada akses.</div></div>');
        //     redirect('karyawan/data/');
        // }
 
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
       $supabaseUrl = "https://vgbkdwivxidacojvcnbr.supabase.co";
       $supabaseKey = "eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJpc3MiOiJzdXBhYmFzZSIsInJlZiI6InZnYmtkd2l2eGlkYWNvanZjbmJyIiwicm9sZSI6InNlcnZpY2Vfcm9sZSIsImlhdCI6MTc1NDg4MDEwOCwiZXhwIjoyMDcwNDU2MTA4fQ.u4n62Z_I3mO7etIJAXpzL3ScTc9QhY04hx1_n-Tg4K4";

       if($this->input->post('changeMarker') == 1){
            if (!$file || $file['error'] !== UPLOAD_ERR_OK) {
                $this->session->set_flashdata(
                    'message','<div class="alert alert-danger">Proses gagal. Silakan coba lagi.</div>'
                );
                redirect(
                    'candidate/edit/'.$candidateId.'?failed=true'
                );
            }
            else{
                $fileName = time() . '_' . basename($file['name']);
                $ch = curl_init($supabaseUrl . "/storage/v1/object/storage/" . $fileName);

                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                curl_setopt_array($ch, [
                    CURLOPT_RETURNTRANSFER => true,
                    CURLOPT_HTTPHEADER => [
                        'Authorization: Bearer ' . $supabaseKey,
                        'Content-Type: ' . mime_content_type($file['tmp_name'])
                   ],
                   CURLOPT_UPLOAD => true, // ✅ penting
                   CURLOPT_CUSTOMREQUEST => 'POST',
                   CURLOPT_INFILE => fopen($file['tmp_name'], 'r'),
                   CURLOPT_INFILESIZE => filesize($file['tmp_name'])
                ]);
          
                curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
                curl_setopt($ch, CURLOPT_INFILE, fopen($file['tmp_name'], 'r'));
                curl_setopt($ch, CURLOPT_INFILESIZE, filesize($file['tmp_name']));

                $response = curl_exec($ch);
                $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
                curl_close($ch);

                if ($httpCode === 200 || $httpCode === 201) {
                    $publicUrl = $supabaseUrl . "/storage/v1/object/public/storage/" . $fileName;
                    $data = [
                        'nik' => $this->input->post('nik'),
                        'phone_number' => $this->input->post('phone_number'),
                        'email' => $this->input->post('email'),
                        'sex' => $this->input->post('sex'),
                        'candidate_picture' => $publicUrl,
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
                else {
                    $this->session->set_flashdata(
                        'message','<div class="alert alert-danger">Proses gagal. Silakan coba lagi.</div>'
                    );
                    redirect(
                        'candidate/edit/'.$candidateId.'?failed=true'
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
       $supabaseUrl = "https://vgbkdwivxidacojvcnbr.supabase.co";
       $supabaseKey = "eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJpc3MiOiJzdXBhYmFzZSIsInJlZiI6InZnYmtkd2l2eGlkYWNvanZjbmJyIiwicm9sZSI6InNlcnZpY2Vfcm9sZSIsImlhdCI6MTc1NDg4MDEwOCwiZXhwIjoyMDcwNDU2MTA4fQ.u4n62Z_I3mO7etIJAXpzL3ScTc9QhY04hx1_n-Tg4K4";
       

        if (!$file || $file['error'] !== UPLOAD_ERR_OK) {
            $this->session->set_flashdata(
                'message','<div class="alert alert-danger">Proses gagal. Silakan coba lagi.</div>'
            );
            redirect(
                'candidate/add?failed=true'
            );
        }
        else{
            $fileName = time() . '_' . basename($file['name']);
            $ch = curl_init($supabaseUrl . "/storage/v1/object/storage/" . $fileName);

            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt_array($ch, [
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_HTTPHEADER => [
                   'Authorization: Bearer ' . $supabaseKey,
                   'Content-Type: ' . mime_content_type($file['tmp_name'])
                ],
                CURLOPT_UPLOAD => true, // ✅ penting
                CURLOPT_CUSTOMREQUEST => 'POST',
                CURLOPT_INFILE => fopen($file['tmp_name'], 'r'),
                CURLOPT_INFILESIZE => filesize($file['tmp_name'])
            ]);
          
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
            curl_setopt($ch, CURLOPT_INFILE, fopen($file['tmp_name'], 'r'));
            curl_setopt($ch, CURLOPT_INFILESIZE, filesize($file['tmp_name']));

            $response = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);

            if ($httpCode === 200 || $httpCode === 201) {
                $publicUrl = $supabaseUrl . "/storage/v1/object/public/storage/" . $fileName;
                $data = [
                    'candidate_id' => uniqid(),
                    'company_id' => $companyId = $this->session->userdata('company_id'),
                    'nik' => $this->input->post('nik'),
                    'phone_number' => $this->input->post('phone_number'),
                    'email' => $this->input->post('email'),
                    'sex' => $this->input->post('sex'),
                    'candidate_picture' => $publicUrl,
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
            else {
                $this->session->set_flashdata(
                    'message','<div class="alert alert-danger">Proses gagal. Silakan coba lagi.</div>'
                );
                redirect(
                    'candidate/add?failed=true'
                );
            }
        }
    }

}
