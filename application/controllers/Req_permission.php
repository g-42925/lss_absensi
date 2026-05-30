<?php
use Aws\S3\S3Client;
use Aws\Credentials\Credentials;
use Aws\Exception\AwsException;

defined('BASEPATH') or exit('No direct script access allowed');

class Req_permission extends CI_Controller {
    public $email;
    public $session;
    public $form_validation;
    public $upload;
    public $pagination;
    public $other;
    public $menu;
    public $rp;

    public function __construct() {
        parent::__construct();
        is_logged_in();
        $this->load->library('form_validation');
        $this->load->model('other_model', 'other');
        $this->load->model('user/menu_model', 'menu');
        $this->load->model('user/req_permission_model', 'rp');
    }

    public function index($awal = null, $akhir = null) {
        cek_menu_access();
        
        $data['htmlpagejs'] = 'none';
        $data['nmenu']      = 'Data Request Izin';
        $data['title']      = 'Request Izin';
        $data['namalabel']  = $data['title'];
        $data['auth']       = authUser();

        $data['tglawal'] = date('Y-m-01');
        if ($awal!=null) {
            $data['tglawal'] = $awal;
        }

        $data['today'] = date('Y-m-d');
        $data['tglakhir'] = date('Y-m-d');
        if ($akhir!=null) {
            $data['tglakhir'] = $akhir;
        }
         
        $companyId = $this->session->userdata('company_id');

        $data['datas']  = $this->rp->get_data($data['tglawal'],$data['tglakhir']);
        $data['divisions'] = $this->db->query("select * from divisions where company_id = ?",[$companyId])->result_array();


        $this->load->view('templates/header', $data);
        $this->load->view('templates/sidemenu', $data);
        $this->load->view('templates/sidenav', $data);
        $this->load->view('module/req_permission/index', $data);
        $this->load->view('templates/footer', $data);
        $this->load->view('templates/fscript-html-end', $data);
    }

    public function filter(){
        cek_menu_access();
        $data['htmlpagejs'] = 'none';
        $data['nmenu']      = 'Data Request Izin';
        $data['title']      = 'Request Izin';
        $data['namalabel']  = $data['title'];
        $data['auth']       = authUser();

        $div = $this->input->get('divisionId');
        $status = $this->input->get('status');
        $start = $this->input->get('start');
        $until = $this->input->get('until');

        $companyId = $this->session->userdata('company_id');
       
        $data['divisions'] = $this->db->query("select * from divisions where company_id = ?",[$companyId])->result_array();  
        
        $data['div'] = $div == 'Any' ? '' : $div;
        $data['status'] = $status == 'all' ? '' : $status;
        $data['tglawal'] = $start ?: date('Y-m-01');
        $data['tglakhir'] = $until ?: date('Y-m-d');
        $data['keyword'] = $this->input->get('keyword');


        $data['datas']  = $this->rp->withFilter($data['tglawal'],$data['tglakhir'],$data);
        $data['status'] = $status;

        $this->load->view('templates/header', $data);
        $this->load->view('templates/sidemenu', $data);
        $this->load->view('templates/sidenav', $data);
        $this->load->view('module/req_permission/filter', $data);
        $this->load->view('templates/footer', $data);
        $this->load->view('templates/fscript-html-end', $data);
         
    }

    public function add() {
        cek_menu_access();
        isEditable();
        $data['htmlpagejs'] = 'none';
        $data['nmenu']      = 'Data Request Izin';
        $data['title']      = 'Data Request Izin';
        $data['namalabel']  = $data['title'];
        $data['auth']       = authUser();
        

        $data['karyawan']   = dataKaryawan();
        $data['thismonth'] = date('Y-m-t');

        $this->load->view('templates/header', $data);
        $this->load->view('templates/sidemenu', $data);
        $this->load->view('templates/sidenav', $data);
        $this->load->view('module/req_permission/add', $data);
        $this->load->view('templates/footer', $data);
        $this->load->view('templates/fscript-html-end', $data);
    }

    public function add_proses() {
        cek_menu_access();
        $ukat  = $this->input->post('kat');
        $unama  = $this->input->post('tgl1');

        $this->form_validation->set_rules('idp[]', 'Karyawan', 'trim|required|xss_clean|htmlspecialchars');
        $this->form_validation->set_rules('kat', 'Kategori', 'trim|required|xss_clean|htmlspecialchars');
        $this->form_validation->set_rules('tgl1', 'Tanggal', 'trim|required|xss_clean|htmlspecialchars');

        if ($ukat=='csh') {
            $this->form_validation->set_rules('tgl2', 'Tanggal', 'trim|xss_clean|htmlspecialchars');
        }else{
            $this->form_validation->set_rules('tgl2', 'Sampai Tanggal', 'trim|required|xss_clean|htmlspecialchars');
        }

        if ($ukat=='lm' || $ukat=='csh' || $ukat=='tl') {
            $this->form_validation->set_rules('jmasuk', 'Masuk', 'trim|required|xss_clean|htmlspecialchars');
            $this->form_validation->set_rules('jkeluar', 'Keluar', 'trim|required|xss_clean|htmlspecialchars');
        }else{
            $this->form_validation->set_rules('jmasuk', 'Masuk', 'trim|xss_clean|htmlspecialchars');
            $this->form_validation->set_rules('jkeluar', 'Keluar', 'trim|xss_clean|htmlspecialchars');
        }

        $this->form_validation->set_rules('catatanl', 'Catatan Lembur', 'trim|xss_clean|htmlspecialchars');

        if ($this->form_validation->run() == false) {
            $this->session->set_flashdata('message', '<div class="alert alert-danger p-cg" role="alert">'.validation_errors().'</div>');
            redirect('req_permission/add');
        } else {
            $cekimgpdf = $_FILES['imgpdf']['name'];
            $upload = $this->other->upload_digital('imgpdf','new','others','file_');
            if($upload['result'] == "success" || $cekimgpdf==''){
                $res = $this->rp->add_proses($cekimgpdf,$upload);
                if ($res==true) {
                    $this->session->set_flashdata('message', '<div class="me-3 ms-3 mt-3"><div class="alert alert-success p-cg" role="alert">Data berhasil disimpan.</div></div>');
                    redirect('req_permission');
                }else{
                    $this->session->set_flashdata('message', '<div class="alert alert-danger p-cg" role="alert">Proses gagal, silahkan coba lagi.</div>');
                    redirect('req_permission/add');
                }
            }else{
                $this->session->set_flashdata('message', '<div class="alert alert-danger p-cg" role="alert">'.$upload['error'].'</div>');
                redirect('req_permission/add');
            }
        }
    }

    public function edit($id = null) {
        cek_menu_access();
        isEditable();
        if ($id==null) { redirect('req_permission'); }
        $check = $this->db->get_where('tx_request_izin', ['request_izin_id' => $id]);
        if ($check->num_rows()==0) { 
            $this->session->set_flashdata('message', '<div class="me-3 ms-3 mt-3"><div class="alert alert-danger p-cg" role="alert">Data tidak ditemukan.</div></div>');
            redirect('req_permission'); 
        }

        $data['htmlpagejs'] = 'none';
        $data['nmenu']      = 'Data Request Izin';
        $data['title']      = 'Data Request Izin';
        $data['namalabel']  = $data['title'];
        $data['auth']       = authUser();

        $data['karyawan'] = $this->db->query("select * from tx_request_izin_pegawai trip join m_pegawai mp on mp.pegawai_id = trip.pegawai_id where trip.request_izin_id = ?",[$id])->row_array();

        $data['edit']       = $check->row_array();
        $data['thismonth'] = date('Y-m-t');

        $this->load->view('templates/header', $data);
        $this->load->view('templates/sidemenu', $data);
        $this->load->view('templates/sidenav', $data);
        $this->load->view('module/req_permission/edit', $data);
        $this->load->view('templates/footer', $data);
        $this->load->view('templates/fscript-html-end', $data);
    }
    

    public function edit_proses($id = null) {
        cek_menu_access();

        if ($id==null) { redirect('req_permission'); }
        $check = $this->db->get_where('tx_request_izin', ['request_izin_id' => $id]);
        $rowcheck = $check->row_array();
        if ($check->num_rows()==0) {
            $this->session->set_flashdata('message', '<div class="me-3 ms-3 mt-3"><div class="alert alert-danger p-cg" role="alert">Data tidak ditemukan.</div></div>');
            redirect('req_permission'); 
        }

        $ukat  = $this->input->post('kat');
        $unama  = $this->input->post('tgl1');

        $this->form_validation->set_rules('idp[]', 'Karyawan', 'trim|required|xss_clean|htmlspecialchars');
        $this->form_validation->set_rules('kat', 'Kategori', 'trim|required|xss_clean|htmlspecialchars');
        $this->form_validation->set_rules('tgl1', 'Tanggal', 'trim|required|xss_clean|htmlspecialchars');

        if ($ukat=='csh') {
            $this->form_validation->set_rules('tgl2', 'Tanggal', 'trim|xss_clean|htmlspecialchars');
        }
        else{
            $this->form_validation->set_rules('tgl2', 'Sampai Tanggal', 'trim|required|xss_clean|htmlspecialchars');
        }

        if ($ukat=='lm' || $ukat=='csh' || $ukat=='tl') {
            $this->form_validation->set_rules('jmasuk', 'Masuk', 'trim|required|xss_clean|htmlspecialchars');
            $this->form_validation->set_rules('jkeluar', 'Keluar', 'trim|required|xss_clean|htmlspecialchars');
        }
        else{
            $this->form_validation->set_rules('jmasuk', 'Masuk', 'trim|xss_clean|htmlspecialchars');
            $this->form_validation->set_rules('jkeluar', 'Keluar', 'trim|xss_clean|htmlspecialchars');
        }

        $this->form_validation->set_rules('catatanl', 'Catatan Lembur', 'trim|xss_clean|htmlspecialchars');
        $this->form_validation->set_rules('status', 'Status', 'trim|required|xss_clean|htmlspecialchars');

        if ($this->form_validation->run() == false) {
            $this->session->set_flashdata('message', '<div class="alert alert-danger p-cg" role="alert">'.validation_errors().'</div>');
            redirect('req_permission/edit/'.$id);
        } 
        else {
            if(isset($_FILES['attachment']) && $_FILES['attachment']['error'] !== UPLOAD_ERR_NO_FILE){
                $cmpId = $this->session->userdata('company_id');
                $company = $this->db->query("select * from companies where id = ?",[$cmpId])->row_array();
                $rootDir = explode('@', $company['email'])[0];
                $path = 'absensi/'.$rootDir .'/attendance/';
                $cFile = $_FILES['attachment']['tmp_name'];
                $name = $_FILES['attachment']['name'];    
                $tmpName = $_FILES['attachment']['tmp_name'];
                $fileName = time() . '_' . basename($name);

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
                    'Key'    => $path.$fileName,
                    'SourceFile' => $cFile,
                    'ContentType' => 'image/png',
                ]);   


                $cid = $result['@metadata']['headers']['x-amz-meta-cid']; 

                $fUrl =  "https://wooden-plum-woodpecker.myfilebase.com/ipfs/".$cid;    
                
                $cekimgpdf = $_FILES['attachment']['name'];
                $imgold = $rowcheck['file_dokumen'];
                $upload = $this->other->upload_digital('attachment',$imgold,'others','file_');


                $res = $this->rp->edit_proses($id,$cekimgpdf,$upload,$imgold,$fUrl);
                
                if ($res==true) {
                    $this->session->set_flashdata('message', '<div class="me-3 ms-3 mt-3"><div class="alert alert-success p-cg" role="alert">Data berhasil disimpan.</div></div>');
                    redirect('req_permission');
                }
                else{
                    $this->session->set_flashdata('message', '<div class="alert alert-danger p-cg" role="alert">Proses gagal, silahkan coba lagi.</div>');
                    redirect('req_permission/edit/'.$id);
                }

                if($upload['result'] == "success" || $cekimgpdf==''){}
            else{
                $this->session->set_flashdata('message', '<div class="alert alert-danger p-cg" role="alert">'.$upload['error'].'</div>');
                redirect('req_permission/add');
            }   
            }
            else{
                $cekimgpdf = $_FILES['attachment']['name'];
                $imgold = $rowcheck['file_dokumen'];
                $upload = $this->other->upload_digital('attachment',$imgold,'others','file_');

                $res = $this->rp->edit_proses($id,null,null,null,null);
                if ($res==true) {
                    $this->session->set_flashdata('message', '<div class="me-3 ms-3 mt-3"><div class="alert alert-success p-cg" role="alert">Data berhasil disimpan.</div></div>');
                    redirect('req_permission');
                }
                else{
                    $this->session->set_flashdata('message', '<div class="alert alert-danger p-cg" role="alert">Proses gagal, silahkan coba lagi.</div>');
                    redirect('req_permission/edit/'.$id);
                } 
            }
        }
    }

    public function hapus($id = null){
        cek_menu_access();
        
        $data['auth'] = authUser();

        if ($id==null) { redirect('req_permission'); }
        $check = $this->db->get_where('tx_request_izin', ['request_izin_id' => $id]);
        if ($check->num_rows()==0) {
            $this->session->set_flashdata('message', '<div class="me-3 ms-3 mt-3"><div class="alert alert-danger p-cg" role="alert">Data tidak ditemukan.</div></div>');
            redirect('req_permission'); 
        }

        $res = $this->db->delete('tx_request_izin', ['request_izin_id' => $id]);
        $res = $this->db->delete('tx_request_izin_pegawai', ['request_izin_id' => $id]);
        $res = $this->db->delete('tx_absensi', ['is_request' => $id]);
        if ($res==true) {
            $this->session->set_flashdata('message', '<div class="me-3 ms-3 mt-3"><div class="alert alert-success p-cg" role="alert">Data berhasil dihapus.</div></div>');
            redirect('req_permission');
        }else{
            $this->session->set_flashdata('message', '<div class="me-3 ms-3 mt-3"><div class="alert alert-danger p-cg" role="alert">Proses gagal, silahkan coba lagi.</div></div>');
            redirect('req_permission');
        }
    }

    public function download_laporan($mulai,$akhir) {
        cek_menu_access();
        $data['tgl_awal'] = $mulai;
        $data['tgl_akhir'] = $akhir;
        $data['all_data'] = $this->rp->get_data($mulai,$akhir);
        $this->load->view('module/req_permission/download', $data);
    }

    public function action($id,$idp) {
        cek_menu_access();
        $data['datar'] = $this->db->get_where('tx_request_izin', ['request_izin_id' => $id])->row_array();
        $data['datap'] = $this->db->query("SELECT * FROM tx_request_izin_pegawai a LEFT JOIN m_pegawai b ON a.pegawai_id=b.pegawai_id WHERE a.request_izin_id='$id' AND a.pegawai_id='$idp' AND b.is_del='n' ")->row_array();
        $data['datatl'] = $this->rp->get_data_tl($id,$idp);
        $this->load->view('module/req_permission/action', $data);
    }

    public function download_perid($id,$idp) {
        cek_menu_access();
        $data['setting'] = pengaturanSistem();
        $data['datar'] = $this->db->get_where('tx_request_izin', ['request_izin_id' => $id])->row_array();        
        $data['datap'] = $this->db->query("SELECT * FROM tx_request_izin_pegawai a LEFT JOIN m_pegawai b ON a.pegawai_id=b.pegawai_id WHERE a.request_izin_id='$id' AND a.pegawai_id='$idp' AND b.is_del='n' ")->row_array();
        if($data['datar']['tipe_request']=='tl'){
            $this->load->view('module/req_permission/download_perid_tl', $data);
        }else{
            $this->load->view('module/req_permission/download_perid', $data);
        }
    }

    public function cut($employeeId,$startFrom,$until){
      $check_izin = $this->db->query("SELECT tri.tipe_request FROM tx_request_izin tri JOIN tx_request_izin_pegawai trip ON tri.request_izin_id = trip.request_izin_id WHERE trip.pegawai_id = ? AND tri.tanggal_request = ? AND tri.tanggal_request_end = ?", [$employeeId, $startFrom, $until])->row_array();

      if (!$check_izin || $check_izin['tipe_request'] !== 's') {
          $this->session->set_flashdata('message', '<div class="me-3 ms-3 mt-3"><div class="alert alert-danger p-cg" role="alert">Fungsi ini hanya untuk karyawan dengan izin sakit.</div></div>');
          redirect('req_permission');
          return;
      }

      $tanggalX = new DateTime($startFrom);
      $tanggalZ = new DateTime($until);
      $tanggalZ->modify('+1 day');

      $interval = new DateInterval('P1D');
      $periode = new DatePeriod($tanggalX, $interval, $tanggalZ);

      $employee = $this->db->query("select * from m_pegawai where pegawai_id = ?",[$employeeId])->row_array();

      if (date('m') === '02') {
        $amount = $employee['salary'] / 24;
      }
      else{
        $amount = $employee['salary'] / 26;
      }

      $this->db->trans_begin();

      foreach ($periode as $tanggal) {
          if ($employee['jumlah_cuti'] > 0) {
              $offDaysAmount = $employee['jumlah_cuti'];
              $data = ['jumlah_cuti' => $offDaysAmount - 1];
              $this->db->where('pegawai_id', $employeeId);
              $this->db->update('m_pegawai', $data);
              $employee['jumlah_cuti'] = $employee['jumlah_cuti'] - 1;
          } else {
              $data = [
                  'deduction_id' => uniqid(),
                  'employee_id' => $employeeId,
                  'deduction_type' => 'mmc',
                  'date' => $tanggal->format('Y-m-d'),
                  'amount' => $amount,
                  'note' => '...'
              ];

              $this->db->insert('salary_deduction', $data);
          }
      }

      

      if($this->db->trans_status() === FALSE) {
        $this->db->trans_rollback();
        redirect('req_permission/index?failed=true');

      } 
      else {
        $this->db->trans_commit();
        redirect('req_permission/index?failed=false');
      }
    }

    public function print($id){
      $data['data'] = $this->db->query("select * from tx_request_izin tri join tx_request_izin_pegawai trip on tri.request_izin_id = trip.request_izin_id join m_pegawai mp on trip.pegawai_id = mp.pegawai_id join position p on p.id = mp.position_id join divisions d on mp.division_id = d.id where tri.request_izin_id = ?",[$id])->row_array();

      cek_menu_access();
      $data['htmlpagejs'] = 'none';
      $data['nmenu']      = 'Data Request Izin';
      $data['title']      = 'Request Izin';
      $data['namalabel']  = $data['title'];
      $data['auth']       = authUser();
      
      $this->load->view('module/req_permission/print', $data);

    }

}
