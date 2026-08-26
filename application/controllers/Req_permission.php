<?php

use Bunny\Storage\Client;
use Bunny\Storage\Region;

defined('BASEPATH') or exit('No direct script access allowed');

class Req_permission extends MY_Controller {
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
        $this->load->library('form_validation');
        $this->load->model('other_model', 'other');
        $this->load->model('user/menu_model', 'menu');
        $this->load->model('user/req_permission_model', 'rp');
    }

    public function index($awal = null, $akhir = null) {        
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

    #[SkipPermission]
    public function add_proses() {
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
        }
        else{
            $this->form_validation->set_rules('jmasuk', 'Masuk', 'trim|xss_clean|htmlspecialchars');
            $this->form_validation->set_rules('jkeluar', 'Keluar', 'trim|xss_clean|htmlspecialchars');
        }

        $this->form_validation->set_rules('catatanl', 'Catatan Lembur', 'trim|xss_clean|htmlspecialchars');

        if ($this->form_validation->run() == false) {
            $this->session->set_flashdata('message', '<div class="alert alert-danger p-cg" role="alert">'.validation_errors().'</div>');
            redirect('req_permission/add');
        } 
        else {
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
    

    #[SkipPermission]
    public function edit_proses($id = null) {

        function makePath($rootDir,$fileName,$year,$month){
            $key = "absensi_{$rootDir}_exception_{$year}_{$month}/{$fileName}";

            return [
                "key"    => $key,
                "result" => "https://leryn-ljm-3.b-cdn.net/".$key
            ];
        }

        if ($id==null) redirect('req_permission');
       
        $check = $this->db->get_where('tx_request_izin', ['request_izin_id' => $id]);
       
        $rowcheck = $check->row_array();

        $ukat  = $this->input->post('kat');

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
                $c = $this->db->query("select * from companies where id = ?",[$cmpId])->row_array();
                $secretAccessKey = "625c8418-f512-4642-98885cb3a927-3b8b-442a";
                $rootDir = explode('@', $c['email'])[0];
                $name = $_FILES['attachment']['name'];    
                $fileName = time() . '_' . basename($name);
                $key = makePath($rootDir,$fileName,date('Y'),date('m'))['key'];
                $cdn = makePath($rootDir,$fileName,date('Y'),date('m'))['result'];

                $client = new Client(
                    '625c8418-f512-4642-98885cb3a927-3b8b-442a',
                    'leryn-ljm-3',
                    Region::FALKENSTEIN // Optional: storage zone region
                );

                $client->upload($_FILES['attachment']['tmp_name'], $key);
   
                
                $cekimgpdf = $_FILES['attachment']['name'];
                $imgold = $rowcheck['file_dokumen'];
                $upload = $this->other->upload_digital('attachment',$imgold,'others','file_');

                $res = $this->rp->edit_proses($id,$cekimgpdf,$upload,$imgold,$cdn);
                
                if ($res==true) {
                    $this->session->set_flashdata('message', '<div class="me-3 ms-3 mt-3"><div class="alert alert-success p-cg" role="alert">Data berhasil disimpan.</div></div>');
                    redirect('req_permission');
                }
                else{
                    $this->session->set_flashdata('message', '<div class="alert alert-danger p-cg" role="alert">Proses gagal, silahkan coba lagi.</div>');
                    redirect('req_permission/edit/'.$id);
                }

                if($upload['result'] == "success" || $cekimgpdf==''){
                    // do something
                }
                else{
                    $this->session->set_flashdata('message', '<div class="alert alert-danger p-cg" role="alert">'.$upload['error'].'</div>');
                    redirect('req_permission/add');
                }   
            }
            else{
                // $cekimgpdf = $_FILES['attachment']['name'];
                // $imgold = $rowcheck['file_dokumen'];
                // $upload = $this->other->upload_digital('attachment',$imgold,'others','file_');

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

    #[SkipPermission]
    public function hapus($id = null){
        
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
        $data['tgl_awal'] = $mulai;
        $data['tgl_akhir'] = $akhir;
        $data['all_data'] = $this->rp->get_data($mulai,$akhir);
        $this->load->view('module/req_permission/download', $data);
    }

    public function action($id,$idp) {
        $data['datar'] = $this->db->get_where('tx_request_izin', ['request_izin_id' => $id])->row_array();
        $data['datap'] = $this->db->query("SELECT * FROM tx_request_izin_pegawai a LEFT JOIN m_pegawai b ON a.pegawai_id=b.pegawai_id WHERE a.request_izin_id='$id' AND a.pegawai_id='$idp' AND b.is_del='n' ")->row_array();
        $data['datatl'] = $this->rp->get_data_tl($id,$idp);
        $this->load->view('module/req_permission/action', $data);
    }

    public function download_perid($id,$idp) {
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
                  'deduction_type' => 'denda sakit',
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

    #[SkipPermission]
    public function print($id){
      $data['data'] = $this->db->query("select * from tx_request_izin tri join tx_request_izin_pegawai trip on tri.request_izin_id = trip.request_izin_id join m_pegawai mp on trip.pegawai_id = mp.pegawai_id join position p on p.id = mp.position_id join divisions d on mp.division_id = d.id where tri.request_izin_id = ?",[$id])->row_array();

      $data['htmlpagejs'] = 'none';
      $data['nmenu']      = 'Data Request Izin';
      $data['title']      = 'Request Izin';
      $data['namalabel']  = $data['title'];
      $data['auth']       = authUser();
      
      $this->load->view('module/req_permission/print', $data);

    }

}
