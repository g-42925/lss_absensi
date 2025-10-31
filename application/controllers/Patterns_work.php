<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Patterns_work extends CI_Controller {
    public $email;
    public $session;
    public $form_validation;
    public $upload;
    public $pagination;
    public $other;
    public $menu;
    public $patterns;

    public function __construct() {
        parent::__construct();
        is_logged_in();
        $this->load->library('form_validation');
        $this->load->model('other_model', 'other');
        $this->load->model('user/menu_model', 'menu');
        $this->load->model('user/patterns_model', 'patterns');
    }

    public function index() {
        cek_menu_access();
        $data['htmlpagejs'] = 'none';
        $data['nmenu']      = 'Pola Kerja';
        $data['title']      = 'Mingguan';
        $data['namalabel']  = $data['title'];
        $data['auth']       = authUser();

        $companyId = $this->session->userdata('company_id');

        $data['datas']      = $this->patterns->get_data($companyId);

        $this->load->view('templates/header', $data);
        $this->load->view('templates/sidemenu', $data);
        $this->load->view('templates/sidenav', $data);
        $this->load->view('module/patterns_work/index', $data);
        $this->load->view('templates/footer', $data);
        $this->load->view('templates/fscript-html-end', $data);
    }

    public function shift(){
        cek_menu_access();
        $data['htmlpagejs'] = 'none';
        $data['nmenu']      = 'Pola Kerja';
        $data['title']      = 'Shift';
        $data['namalabel']  = $data['title'];
        $data['auth']       = authUser();

        $companyId = $this->session->userdata('company_id');

        $data['datas'] = $this->db->query("select * from shift where company_id = ?",[$companyId])->result_array();

        foreach($data['datas'] as $index => $d){
          $q = $this->db->query("select * from shift_detail where shift_id = ?",[$d['id']]);
          $data['datas'][$index]['jumlahJadwal'] = $q->num_rows();
        }

        $this->load->view('templates/header', $data);
        $this->load->view('templates/sidemenu', $data);
        $this->load->view('templates/sidenav', $data);
        $this->load->view('module/patterns_work/shift', $data);
        $this->load->view('templates/footer', $data);
        $this->load->view('templates/fscript-html-end', $data);
    }

    public function shift_edit($shiftId){
        cek_menu_access();
        $data['htmlpagejs'] = 'none';
        $data['nmenu']      = 'Pola Kerja';
        $data['title']      = 'Shift';
        $data['namalabel']  = $data['title'];
        $data['auth']       = authUser();

        $data['failed'] = filter_var($this->input->get('failed'),FILTER_VALIDATE_BOOLEAN);

        $data['datas'] = $this->db->query("select * from shift where id = ?",[$shiftId])->row_array();

        $this->load->view('templates/header', $data);
        $this->load->view('templates/sidemenu', $data);
        $this->load->view('templates/sidenav', $data);
        $this->load->view('module/patterns_work/shift_edit', $data);
        $this->load->view('templates/footer', $data);
        $this->load->view('templates/fscript-html-end', $data);
    }

    public function shift_edit_proses(){
        $data['failed'] = filter_var($this->input->get('failed'), FILTER_VALIDATE_BOOLEAN);
        $data = ['id' => $this->input->post('id'),'name' => $this->input->post('name')];
        $this->db->where('id',$this->input->post('id'));
        $q = $this->db->update('shift',$data);

        if($q){
          redirect('patterns_work/shift/'.$this->input->post('id'));
        }
        else{
          $this->session->set_flashdata('message', '<div class="alert alert-danger p-cg" role="alert">Proses gagal, silahkan coba lagi.</div>');
          redirect('patterns_work/shift_edit/'.$this->input->post('shiftId').'?failed=true');
        }
    }

    public function shift_detail($shiftId){
        cek_menu_access();
        $data['htmlpagejs'] = 'none';
        $data['nmenu']      = 'Pola Kerja';
        $data['title']      = 'Shift';
        $data['namalabel']  = $data['title'];
        $data['auth']       = authUser();

        $data['shiftId'] = $shiftId;

        $data['shift'] = $this->db->query("select * from shift where id = ?",[$data['shiftId']])->row_array();
 
        $data['datas'] = $this->db->query("select * from shift_detail where shift_id = ?",[$shiftId])->result_array();

        $this->load->view('templates/header', $data);
        $this->load->view('templates/sidemenu', $data);
        $this->load->view('templates/sidenav', $data);
        $this->load->view('module/patterns_work/shift_detail', $data);
        $this->load->view('templates/footer', $data);
        $this->load->view('templates/fscript-html-end', $data);
    }

    public function shift_set($id){
        cek_menu_access();
        $data['htmlpagejs'] = 'none';
        $data['nmenu']      = 'Pola Kerja';
        $data['title']      = 'Shift';
        $data['namalabel']  = $data['title'];
        $data['auth']       = authUser();

        $data['id'] = $id;

        $filter1 = [];

        $data['failed'] = filter_var($this->input->get('failed'),FILTER_VALIDATE_BOOLEAN);
        $data['division'] = $this->db->query("select * from divisions where work_system = ?",['s-'.$id])->row_array();        
        $data['employees'] = $this->db->query("select * from m_pegawai where division_id = ? and is_del = 'n'",[$data['division']['id'] ?? ''])->result_array();
       
        $data['schedule'] = $this->db->query("select * from shift_detail where shift_id = ?",[$id])->result_array();

        foreach($data['schedule'] as $index => $s){
          $data['schedule'][$index]['employees'] = [];
          $q = $this->db->query("select * from employee_shift where shift_detail_id = ?",[$s['shift_detail_id']])->result_array();
          foreach($q as $es){
            $data['schedule'][$index]['employees'][] = $es['employee_id'];
          }
        }        
        $this->load->view('templates/header', $data);
        $this->load->view('templates/sidemenu', $data);
        $this->load->view('templates/sidenav', $data);
        $this->load->view('module/patterns_work/shift_set', $data);
        $this->load->view('templates/footer', $data);
        $this->load->view('templates/fscript-html-end', $data);
    }

    public function shift_set_proses($id){
      $this->db->trans_begin();

      foreach($this->input->post('schedules') as $s){
        $this->db->where('shift_detail_id',$s)->delete('employee_shift');

        foreach($this->input->post($s.'-employees') as $e){
          $data = [
            'employee_shift_id' => uniqid(),
            'employee_id' => $e,
            'shift_detail_id' => $s
          ];

          $q = $this->db->insert(
            'employee_shift',
            $data
          );

        }
      }

      if($this->db->trans_status() === FALSE) {
        $this->db->trans_rollback();
        $this->session->set_flashdata(
            'message',
            '<div class="alert alert-danger">Proses gagal. Silakan coba lagi.</div>'
        );
        redirect('patterns_work/shift_set/' . $id . '?failed=true');
      } 
      else {
        $this->db->trans_commit();
        redirect('patterns_work/shift');
      }
    }

    public function  shift_off_set($id){
      cek_menu_access();
      $data['htmlpagejs'] = 'none';
      $data['nmenu']      = 'Pola Kerja';
      $data['title']      = 'Shift';
      $data['namalabel']  = $data['title'];
      $data['auth']       = authUser();

      $data['id'] = $id;

      $data['failed'] = filter_var($this->input->get('failed'),FILTER_VALIDATE_BOOLEAN);

      $data['division'] = $this->db->query("select * from divisions where work_system = ?",['s-'.$id])->row_array();        
      $data['employees'] = $this->db->query("select * from m_pegawai where division_id = ? and is_del = 'n'",[$data['division']['id'] ?? ''])->result_array();

      foreach($data['employees'] as $index => $e){
        $q = $this->db->query("select * from shift_off where employee_id = ?",[$e['pegawai_id']])->row_array();
        
        if($q){
          $data['employees'][$index]['off'] = $q['day'];
        }
        else{
          $data['employees'][$index]['off'] = 0;
        }

      }
       
      $this->load->view('templates/header', $data);
      $this->load->view('templates/sidemenu', $data);
      $this->load->view('templates/sidenav', $data);
      $this->load->view('module/patterns_work/shift_off_set', $data);
      $this->load->view('templates/footer', $data);
      $this->load->view('templates/fscript-html-end', $data);
    }

    public function shift_off_set_proses($id){
      $this->db->trans_begin(); // to start db transaction
        
      $this->db->where('shift_id',$id)->delete('shift_off');

      foreach($this->input->post('employees') as $index => $e){
      
        $day = $this->input->post('day')[$index];
        
        $data = [
          'shift_off_id' => uniqid(),
          'shift_id' => $id,
          'employee_id' => $e,
          'day' => $day
        ];

        $this->db->insert(
          'shift_off',
          $data
        );
        
      }

      if($this->db->trans_status() === FALSE) {
        $this->db->trans_rollback();
        $this->session->set_flashdata(
            'message',
            '<div class="alert alert-danger">Proses gagal. Silakan coba lagi.</div>'
        );
        redirect('patterns_work/shift_off_set/' . $id . '?failed=true');
      } 
      else {
        $this->db->trans_commit();
        redirect('patterns_work/shift');
      }

    }

    public function shift_add(){
        cek_menu_access();
        $data['htmlpagejs'] = 'none';
        $data['nmenu']      = 'Pola Kerja';
        $data['title']      = 'Shift';
        $data['namalabel']  = $data['title'];
        $data['auth']       = authUser();

        $companyId = $this->session->userdata('company_id');

        $data['failed'] = filter_var($this->input->get('failed'),FILTER_VALIDATE_BOOLEAN);

        $data['datas'] = $this->db->query("select * from shift where company_id = ?",[$companyId])->result_array();

        $this->load->view('templates/header', $data);
        $this->load->view('templates/sidemenu', $data);
        $this->load->view('templates/sidenav', $data);
        $this->load->view('module/patterns_work/shift_add', $data);
        $this->load->view('templates/footer', $data);
        $this->load->view('templates/fscript-html-end', $data);
    }

    public function shift_detail_add($shiftId){
        cek_menu_access();
        $data['htmlpagejs'] = 'none';
        $data['nmenu']      = 'Pola Kerja';
        $data['title']      = 'Shift';
        $data['namalabel']  = $data['title'];
        $data['auth']       = authUser();

        $data['shiftId'] = $shiftId;

        $data['failed'] = filter_var($this->input->get('failed'), FILTER_VALIDATE_BOOLEAN);

        $this->load->view('templates/header', $data);
        $this->load->view('templates/sidemenu', $data);
        $this->load->view('templates/sidenav', $data);
        $this->load->view('module/patterns_work/shift_detail_add', $data);
        $this->load->view('templates/footer', $data);
        $this->load->view('templates/fscript-html-end', $data);
    }

    public function shift_detail_edit_proses($shiftId,$id){
        $data = [
          'shift_id' => $this->input->post(
            'shiftId'
          ),
          'shift_detail_id' => $this->input->post(
            'jadwalId'
          ),
          'name' => $this->input->post(
            'name'
          ),
          'tardiness_tolerance' => $this->input->post(
            'tt'
          ),
          'clock_in' => $this->input->post(
            'clockin'
          ),
          'clock_out' => $this->input->post(
            'clockout'
          ),
          'break' => $this->input->post(
            'break'
          ),
          'after_break' => $this->input->post(
            'afterbreak'
          )
        ];
        
        $this->db->where('shift_detail_id',$id);
        $query = $this->db->update('shift_detail',$data);

        if($query){
          redirect('patterns_work/shift_detail/'.$shiftId);
        }
        else{
          $this->session->set_flashdata('message', '<div class="alert alert-danger p-cg" role="alert">Proses gagal, silahkan coba lagi.</div>');
          redirect('patterns_work/shift_detail_edit/'.$shiftId.'/'.$id.'?failed=true');
        }
    }

    public function shift_detail_edit($shiftId,$id){
        cek_menu_access();
        $data['htmlpagejs'] = 'none';
        $data['nmenu']      = 'Pola Kerja';
        $data['title']      = 'Pola Kerja';
        $data['namalabel']  = $data['title'];
        $data['auth']       = authUser();

        $data['shiftId'] = $shiftId;

        $data['failed'] = filter_var($this->input->get('failed'), FILTER_VALIDATE_BOOLEAN);

        $data['info'] = $this->db->query("select * from shift_detail where shift_detail_id = ?",[$id])->row_array();

        $this->load->view('templates/header', $data);
        $this->load->view('templates/sidemenu', $data);
        $this->load->view('templates/sidenav', $data);
        $this->load->view('module/patterns_work/shift_detail_edit', $data);
        $this->load->view('templates/footer', $data);
        $this->load->view('templates/fscript-html-end', $data);
    }

    public function shift_detail_add_proses(){
      $filter = [
        'name' => $this->input->post(
            'name'
        ),
        'shift_id' => $this->input->post(
            'shiftId'
        ),
      ];
      $q = $this->db->get_where(
        'shift_detail',
        $filter
      );

      if($q->num_rows() < 1){
        $data = [
          'shift_id' => $this->input->post(
            'shiftId'
          ),
          'shift_detail_id' => $this->input->post(
            'jadwalId'
          ),
          'name' => $this->input->post(
            'name'
          ),
          'tardiness_tolerance' => $this->input->post(
            'tt'
          ),
          'clock_in' => $this->input->post(
            'clockin'
          ),
          'clock_out' => $this->input->post(
            'clockout'
          ),
          'break' => $this->input->post(
            'break'
          ),
          'after_break' => $this->input->post(
            'afterbreak'
          )
        ];


        $this->db->trans_begin();

        try{
          $q2 = $this->db->insert(
            'shift_detail', 
            $data
          );
        
          if(!$q2){
            throw new Exception("failed to run query");
          }
          else{
            $this->db->trans_commit();
            redirect('patterns_work/shift_detail/'.$this->input->post('shiftId'));
          }
        }
        catch(Exception $e){
          $this->db->trans_rollback();
          $this->session->set_flashdata('message', '<div class="alert alert-danger p-cg" role="alert">Proses gagal, silahkan coba lagi.</div>');
          redirect('patterns_work/shift_detail_add/'.$this->input->post('shiftId').'?failed=true');
        }
      }
    }

    public function shift_add_proses(){
        $this->form_validation->set_rules('name', 'Name', 'required');

        $name  = $this->input->post('name');

        $companyId = $this->session->userdata('company_id');


        if ($this->form_validation->run() == false) {
            $this->session->set_flashdata('message', '<div class="alert alert-danger p-cg" role="alert">'.validation_errors().'</div>');
            redirect('patterns_work/shift_add?failed=true');
        } 
        else{
           $q = $this->db->get_where('shift', ['name' => $name, 'company_id' => $companyId])->num_rows();
           
           if($q < 1){
              $data = [
                'id' => $this->input->post('id'),
                'company_id' => $companyId,
                'name' => $name,
              ];
              $res = $this->db->insert(
                'shift', 
                $data
              );
              if($res==true) {
                redirect('patterns_work/shift');
              }
              else{
                $this->session->set_flashdata('message', '<div class="alert alert-danger p-cg" role="alert">Proses gagal, silahkan coba lagi.</div>');
                redirect('patterns_work/shift_add?failed=true');
              }
           }
           else{
               $this->session->set_flashdata('message', '<div class="alert alert-warning p-cg" role="alert">Proses gagal,shift <b>'.$name.'</b> sudah ada</div>');
               redirect('patterns_work/shift_add?failed=true');
           }
        }
    }

    public function add() {
        cek_menu_access();
        $data['htmlpagejs'] = 'none';
        $data['nmenu']      = 'Pola Kerja';
        $data['title']      = 'Mingguan';
        $data['namalabel']  = $data['title'];
        $data['auth']       = authUser();

        $data['failed'] = filter_var($this->input->get('failed'), FILTER_VALIDATE_BOOLEAN);
        
        if($data['auth']['tambah']!='y'){
            redirect('patterns_work/');
        }

        $companyId = $this->session->userdata('company_id');

        $data['roles']      = $this->other->get_roles($companyId);
        $data['permission'] = $this->other->get_permission();

        $this->load->view('templates/header', $data);
        $this->load->view('templates/sidemenu', $data);
        $this->load->view('templates/sidenav', $data);
        $this->load->view('module/patterns_work/add', $data);
        $this->load->view('templates/footer', $data);
        $this->load->view('templates/fscript-html-end', $data);
    }

    public function add_proses() {
        cek_menu_access();
        $unama  = $this->input->post('nama');

        $this->form_validation->set_rules('nama', 'Nama', 'trim|required|xss_clean|htmlspecialchars');
        $this->form_validation->set_rules('tolet', 'Toleransi Telat', 'trim|xss_clean|htmlspecialchars');
        $this->form_validation->set_rules('jumlahhari', 'Jumlah Hari', 'trim|required|xss_clean|htmlspecialchars');
        $this->form_validation->set_rules('work[]', '', 'trim|xss_clean|htmlspecialchars');
        $this->form_validation->set_rules('sistemkerja[]', '', 'trim|xss_clean|htmlspecialchars');
        $this->form_validation->set_rules('masuk[]', '', 'trim|xss_clean|htmlspecialchars');
        $this->form_validation->set_rules('pulang[]', '', 'trim|xss_clean|htmlspecialchars');
        $this->form_validation->set_rules('break[]','Break','required');
        $this->form_validation->set_rules('breakEnd[]','Break End','required');


        if ($this->form_validation->run() == false) {
            $this->session->set_flashdata('message', '<div class="alert alert-danger p-cg" role="alert">'.validation_errors().'</div>');
            redirect('patterns_work/add?failed=true');
        } 
        else {
            $query = $this->db->get_where('m_pola_kerja', ['nama_pola' => $unama, 'is_del' => 'n'])->num_rows();
            if($query < 1) {
                $res = $this->patterns->add_proses(
                    $this->session->userdata('company_id')
                );
                if($res==true) {
                    redirect('patterns_work');
                }
                else{
                    $this->session->set_flashdata('message', '<div class="alert alert-danger p-cg" role="alert">Proses gagal, silahkan coba lagi.</div>');
                    redirect('patterns_work/add?failed=true');
                }
            } 
            else {
                $this->session->set_flashdata('message', '<div class="alert alert-warning p-cg" role="alert">Proses gagal, nama pola <b>"'.$unama.'"</b> ini sudah digunakan.</div>');
                redirect('patterns_work/add?failed=true');
            }
        }
    }

    public function edit($id = null) {
        cek_menu_access();
        if ($id==null) { redirect('patterns_work'); }
        $check = $this->db->get_where('m_pola_kerja', ['pola_kerja_id' => $id]);
        if ($check->num_rows()==0) { 
            $this->session->set_flashdata('message', '<div class="me-3 ms-3 mt-3"><div class="alert alert-danger p-cg" role="alert">Data tidak ditemukan.</div></div>');
            redirect('patterns_work'); 
        }

        $data['htmlpagejs'] = 'none';
        $data['nmenu']      = 'Pola Kerja';
        $data['title']      = 'Mingguan';
        $data['namalabel']  = $data['title'];
        $data['auth']       = authUser();

        $data['failed'] = filter_var($this->input->get('failed'), FILTER_VALIDATE_BOOLEAN);

        
        if($data['auth']['edit']!='y'){
            $this->session->set_flashdata('message', '<div class="me-3 ms-3 mt-3"><div class="alert alert-danger p-cg" role="alert">Tidak ada akses.</div></div>');
            redirect('patterns_work/');
        }

        $data['edit']       = $check->row_array();
        $data['edit_pola']  = $this->db->get_where('m_pola_kerja_det', ['pola_kerja_id' => $data['edit']['pola_kerja_id']])->result_array();

        $this->load->view('templates/header', $data);
        $this->load->view('templates/sidemenu', $data);
        $this->load->view('templates/sidenav', $data);
        $this->load->view('module/patterns_work/edit', $data);
        $this->load->view('templates/footer', $data);
        $this->load->view('templates/fscript-html-end', $data);
    }

    public function edit_proses($id = null) {
        cek_menu_access();
        $unama  = $this->input->post('nama');

            if ($id==null) { redirect('patterns_work'); }
            $check = $this->db->get_where('m_pola_kerja', ['pola_kerja_id' => $id]);
            $rowcheck = $check->row_array();
            if($check->num_rows()==0) {
                redirect('patterns_work'); 
            }

            $this->form_validation->set_rules('nama', 'Nama', 'trim|required|xss_clean|htmlspecialchars');
            $this->form_validation->set_rules('tolet', 'Toleransi Telat', 'trim|xss_clean|htmlspecialchars');
            $this->form_validation->set_rules('jumlahhari', 'Jumlah Hari', 'trim|required|xss_clean|htmlspecialchars');
            $this->form_validation->set_rules('work[]', '', 'trim|xss_clean|htmlspecialchars');
            $this->form_validation->set_rules('sistemkerja[]', '', 'trim|xss_clean|htmlspecialchars');
            $this->form_validation->set_rules('masuk[]', '', 'trim|xss_clean|htmlspecialchars');
            $this->form_validation->set_rules('pulang[]', '', 'trim|xss_clean|htmlspecialchars');
            $this->form_validation->set_rules('break[]','','trim|required|htmlspecialchars');
            $this->form_validation->set_rules('breakEnd[]','','trim|required|htmlspecialchars');


            if ($this->form_validation->run() == false) {
                $this->session->set_flashdata('message', '<div class="alert alert-danger p-cg" role="alert">'.validation_errors().'</div>');
                redirect('patterns_work/edit/'.$id.'?failed=true');
            } 
            else {
                $query = $this->db->get_where('m_pola_kerja', ['nama_pola' => $unama, 'is_del' => 'n', 'pola_kerja_id!=' => $id])->num_rows();
                if ($query < 1) {
                    $res = $this->patterns->edit_proses($id);
                    if($res==true) {
                        redirect('patterns_work');
                    }
                    else{
                        $this->session->set_flashdata('message', '<div class="alert alert-danger p-cg" role="alert">Proses gagal, silahkan coba lagi.</div>');
                        redirect('patterns_work/edit/'.$id.'?failed=false');
                    }
                } 
                else {
                    $this->session->set_flashdata('message', '<div class="alert alert-warning p-cg" role="alert">Proses gagal, pola kerja <b>"'.$unama.'"</b> ini sudah digunakan.</div>');
                    redirect('patterns_work/edit/'.$id.'?failed=false');
                }
            }

    }

    public function hapus($id){
        cek_menu_access();
        
        $data['auth'] = authUser();
        if($data['auth']['hapus']!='y'){
            $this->session->set_flashdata('message', '<div class="me-3 ms-3 mt-3"><div class="alert alert-danger p-cg" role="alert">Tidak ada akses.</div></div>');
            redirect('patterns_work/');
        }
        $num = $this->db->query("SELECT * FROM m_pegawai_pola a JOIN m_pegawai b ON a.pegawai_id=b.pegawai_id WHERE a.pola_kerja_id='$id' AND b.is_del='n'")->num_rows();
        if ($num<=0) {
            $res = $this->other->hapus_data('m_pola_kerja','pola_kerja_id',$id);
            if ($res==true) {
                $this->session->set_flashdata('message', '<div class="me-3 ms-3 mt-3"><div class="alert alert-success p-cg" role="alert">Data berhasil dihapus.</div></div>');
            }
            else{
                $this->session->set_flashdata('message', '<div class="me-3 ms-3 mt-3"><div class="alert alert-danger p-cg" role="alert">Proses gagal, silahkan coba lagi.</div></div>');
            }
        }
        else{
            $this->session->set_flashdata('message', '<div class="me-3 ms-3 mt-3"><div class="alert alert-warning p-cg" role="alert">Data tidak bisa dihapus, karna masih ada '.$num.' data karyawan didalamnya.</div></div>');
        }
        redirect('patterns_work');

    }

    public function assign($id = null) {
        cek_menu_access();

        if ($id==null) { redirect('patterns_work'); }
        $check = $this->db->get_where('m_pola_kerja', ['pola_kerja_id' => $id]);
        if ($check->num_rows()==0) {
            $this->session->set_flashdata('message', '<div class="me-3 ms-3 mt-3"><div class="alert alert-danger p-cg" role="alert">Data tidak ditemukan.</div></div>');
            redirect('patterns_work'); 
        }

        $pola = $check->row_array();

        $data['htmlpagejs'] = 'none';
        $data['nmenu']      = 'Pola Kerja';
        $data['title']      = 'Pola Kerja';
        $data['namalabel']  = $pola['nama_pola'];
        $data['auth']       = authUser();
        $data['id']         = $id;

        $data['datas']      = $this->patterns->get_assign($id);
        $data['karyawan']   = $this->patterns->get_karyawan($id);

        $this->load->view('templates/header', $data);
        $this->load->view('templates/sidemenu', $data);
        $this->load->view('templates/sidenav', $data);
        $this->load->view('module/patterns_work/assign', $data);
        $this->load->view('templates/footer', $data);
        $this->load->view('templates/fscript-html-end', $data);
    }

    public function assign_proses($id = null) {
        cek_menu_access();
        
        $data['auth'] = authUser();
        if($data['auth']['tambah']!='y'){
            $this->session->set_flashdata('message', '<div class="me-3 ms-3 mt-3"><div class="alert alert-danger p-cg" role="alert">Tidak ada akses.</div></div>');
            redirect('patterns_work/assign/'.$id);
        }

        if ($id==null) { redirect('patterns_work'); }
        $check = $this->db->get_where('m_pola_kerja', ['pola_kerja_id' => $id]);
        if ($check->num_rows()==0) {
            $this->session->set_flashdata('message', '<div class="me-3 ms-3 mt-3"><div class="alert alert-danger p-cg" role="alert">Data tidak ditemukan.</div></div>');
            redirect('patterns_work'); 
        }

        $this->form_validation->set_rules('idp[]', 'Karyawan', 'trim|required|xss_clean|htmlspecialchars');
        $this->form_validation->set_rules('pola', 'Pola', 'trim|required|xss_clean|htmlspecialchars');
        $this->form_validation->set_rules('tglmulai', 'Tanggal', 'trim|required|xss_clean|htmlspecialchars');
        $this->form_validation->set_rules('harike', 'Hari', 'trim|required|xss_clean|htmlspecialchars');

        if ($this->form_validation->run() == false) {
            $this->session->set_flashdata('message', '<div class="me-3 ms-3 mt-3"><div class="alert alert-danger p-cg" role="alert">'.validation_errors().'</div></div>');
            redirect('patterns_work/assign/'.$id);
        } else {
            $res = $this->patterns->assign_proses($id);
            if ($res==true) {
                $this->session->set_flashdata('message', '<div class="me-3 ms-3 mt-3"><div class="alert alert-success p-cg" role="alert">Data berhasil disimpan.</div></div>');
                redirect('patterns_work/assign/'.$id);
            }else{
                $this->session->set_flashdata('message', '<div class="me-3 ms-3 mt-3"><div class="alert alert-danger p-cg" role="alert">Proses gagal, silahkan coba lagi.</div></div>');
                redirect('patterns_work/assign/'.$id);
            }
        }
    }

    public function hapus_assign($id = null, $idx = null){
        cek_menu_access();
        
        $data['auth'] = authUser();
        if($data['auth']['hapus']!='y'){
            $this->session->set_flashdata('message', '<div class="me-3 ms-3 mt-3"><div class="alert alert-danger p-cg" role="alert">Tidak ada akses.</div></div>');
            redirect('patterns_work/assign/'.$id.'?failed=true');
        }

        if ($id==null || $idx==null) { redirect('patterns_work/assign/'.$id); }
        $check = $this->db->get_where('m_pegawai_pola', ['pegawai_pola_id' => $idx]);
        if ($check->num_rows()==0) {
            $this->session->set_flashdata('message', '<div class="me-3 ms-3 mt-3"><div class="alert alert-danger p-cg" role="alert">Data tidak ditemukan.</div></div>');
            redirect('patterns_work/assign/'.$id.'?failed=true'); 
        }
        $check = $check->row_array();
        $res = $this->db->delete('m_pegawai_pola', ['pegawai_pola_id' => $idx]);
        
        $check = $this->db->order_by('pegawai_pola_id', 'DESC')->get_where('m_pegawai_pola', ['pegawai_id' => $check['pegawai_id']]);

        if ($check->num_rows()>0) {
            $check = $check->row_array();
            $this->db->set(['is_selected' => 'y']);
            $this->db->where('pegawai_pola_id', $check['pegawai_pola_id']);
            $this->db->update('m_pegawai_pola');
        }

        if ($res==true) {
            $this->session->set_flashdata('message', '<div class="me-3 ms-3 mt-3"><div class="alert alert-success p-cg" role="alert">Data berhasil dihapus.</div></div>');
            redirect('patterns_work/assign/'.$id);
        }
        else{
            $this->session->set_flashdata('message', '<div class="me-3 ms-3 mt-3"><div class="alert alert-danger p-cg" role="alert">Proses gagal, silahkan coba lagi.</div></div>');
            redirect('patterns_work/assign/'.$id.'?failed=true');
        }
    }


}
