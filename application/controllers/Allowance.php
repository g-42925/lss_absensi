<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Allowance extends MY_Controller {
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

    public function index() {
        $data['htmlpagejs'] = 'none';
        $data['nmenu']      = 'Bonus & Tunjangan';
        $data['title']      = 'Harian';
        $data['namalabel']  = $data['title'];
        $data['auth']       = authUser();

        $companyId = $this->session->userdata('company_id');

        $data['data'] = $this->db->query("select * from allowance where company_id = ? and period = 'daily'",[$companyId])->result_array();

        $this->load->view('templates/header', $data);
        $this->load->view('templates/sidemenu', $data);
        $this->load->view('templates/sidenav', $data);
        $this->load->view('module/allowance/index',$data);
        $this->load->view('templates/footer', $data);
        $this->load->view('templates/fscript-html-end', $data);
    }

    public function monthly(){
        $data['htmlpagejs'] = 'none';
        $data['nmenu']      = 'Bonus & Tunjangan';
        $data['title']      = 'Bulanan';
        $data['namalabel']  = $data['title'];
        $data['auth']       = authUser();

        $companyId = $this->session->userdata('company_id');

        $data['data'] = $this->db->query("select * from allowance where company_id = ? and period = 'monthly'",[$companyId])->result_array();

        $this->load->view('templates/header', $data);
        $this->load->view('templates/sidemenu', $data);
        $this->load->view('templates/sidenav', $data);
        $this->load->view('module/allowance/monthly_index',$data);
        $this->load->view('templates/footer', $data);
        $this->load->view('templates/fscript-html-end', $data);
    }

    public function add(){
        isEditable();
        $data['htmlpagejs'] = 'none';
        $data['nmenu']      = 'Bonus & Tunjangan';
        $data['title']      = 'Harian';
        $data['namalabel']  = $data['title'];
        $data['auth']       = authUser();

        $data['failed'] = filter_var($this->input->get('failed'),FILTER_VALIDATE_BOOLEAN);

        $this->load->view('templates/header', $data);
        $this->load->view('templates/sidemenu', $data);
        $this->load->view('templates/sidenav', $data);
        $this->load->view('module/allowance/add',$data);
        $this->load->view('templates/footer', $data);
        $this->load->view('templates/fscript-html-end', $data);
    }

     public function monthly_add(){
        isEditable();
        $data['htmlpagejs'] = 'none';
        $data['nmenu']      = 'Bonus & Tunjangan';
        $data['title']      = 'Bulanan';
        $data['namalabel']  = $data['title'];
        $data['auth']       = authUser();

        $data['failed'] = filter_var($this->input->get('failed'),FILTER_VALIDATE_BOOLEAN);

        $this->load->view('templates/header', $data);
        $this->load->view('templates/sidemenu', $data);
        $this->load->view('templates/sidenav', $data);
        $this->load->view('module/allowance/monthly_add',$data);
        $this->load->view('templates/footer', $data);
        $this->load->view('templates/fscript-html-end', $data);
    }

    public function monthly_edit($id){
        isEditable();
        $data['htmlpagejs'] = 'none';
        $data['nmenu']      = 'Bonus & Tunjangan';
        $data['title']      = 'Bulanan';
        $data['namalabel']  = $data['title'];
        $data['auth']       = authUser();

        $data['id'] = $id;

        $data['failed'] = filter_var($this->input->get('failed'),FILTER_VALIDATE_BOOLEAN);        

        $data['data'] = $this->db->query("select * from allowance where allowance_id = ?",[$id])->row_array();

        $this->load->view('templates/header', $data);
        $this->load->view('templates/sidemenu', $data);
        $this->load->view('templates/sidenav', $data);
        $this->load->view('module/allowance/monthly_edit',$data);
        $this->load->view('templates/footer', $data);
        $this->load->view('templates/fscript-html-end', $data);
    }

    public function edit($id){
        isEditable();
        $data['htmlpagejs'] = 'none';
        $data['nmenu']      = 'Bonus & Tunjangan';
        $data['title']      = 'Harian';
        $data['namalabel']  = $data['title'];
        $data['auth']       = authUser();

        $data['id'] = $id;

        $data['failed'] = filter_var($this->input->get('failed'),FILTER_VALIDATE_BOOLEAN);        

        $data['data'] = $this->db->query("select * from allowance where allowance_id = ?",[$id])->row_array();

        $this->load->view('templates/header', $data);
        $this->load->view('templates/sidemenu', $data);
        $this->load->view('templates/sidenav', $data);
        $this->load->view('module/allowance/edit',$data);
        $this->load->view('templates/footer', $data);
        $this->load->view('templates/fscript-html-end', $data);
    }

    #[SkipPermission]
    public function daily_add_proses(){
      $data = [
        'allowance_id' => $this->input->post('id'),
        'company_id' => $this->session->userdata('company_id'),
        'name' => $this->input->post('name'),
        'period' => 'daily',
        'foa' => false,
        'boa' => $this->input->post('boa')
      ];

      $q = $this->db->insert(
        'allowance',
        $data
      );

      if(!$q){
        $this->session->set_flashdata(
          'message','<div class="alert alert-danger">Proses gagal. Silakan coba lagi.</div>'
        );
        redirect(
          'allowance/add/?failed=true'
        );
      }
      else{
        redirect(
          'allowance/'
        );
      }
    }

    #[SkipPermission]
     public function monthly_add_proses(){
      $data = [
        'allowance_id' => $this->input->post('id'),
        'company_id' => $this->session->userdata('company_id'),
        'name' => $this->input->post('name'),
        'period' => 'monthly',
        'foa' => $this->input->post('foa'),
        'fol' => $this->input->post('fol')
      ];

      $q = $this->db->insert(
        'allowance',
        $data
      );

      if(!$q){
        $this->session->set_flashdata(
          'message','<div class="alert alert-danger">Proses gagal. Silakan coba lagi.</div>'
        );
        redirect(
          'allowance/monthly_add/?failed=true'
        );
      }
      else{
        redirect(
          'allowance/monthly'
        );
      }
    }

    #[SkipPermission]
    public function daily_edit_process($id){
      $data = [
        'name' => $this->input->post('name'),
        'boa' => $this->input->post('boa')
      ];

      $this->db->set(
        $data
      );
      $this->db->where(
        'allowance_id', 
        $id
      );
      $q = $this->db->update(
        'allowance'
      );

      if($q){
        redirect('allowance');
      }
      else{
        $this->session->set_flashdata('message', '<div class="alert alert-danger p-cg" role="alert">proses gagal, silahkan coba lagi</div>');
        redirect('allowance/edit/'.$id.'?failed=true');
      }
    }

    #[SkipPermission]
    public function monthly_edit_process($id){
      $data = [
        'name' => $this->input->post('name'),
        'foa' => $this->input->post('foa'),
        'fol' => $this->input->post('fol')
      ];

      $this->db->set(
        $data
      );
      $this->db->where(
        'allowance_id', 
        $id
      );
      $q = $this->db->update(
        'allowance'
      );

      if($q){
        redirect('allowance/monthly');
      }
      else{
        $this->session->set_flashdata('message', '<div class="alert alert-danger p-cg" role="alert">proses gagal, silahkan coba lagi</div>');
        redirect('allowance/monthly_edit/'.$id.'?failed=true');
      }
    }

    #[SkipPermission]
    public function config_process($id) {
      $allowances = $this->input->post('allowance');
      $values = $this->input->post('values');
      $dates = $this->input->post('dates');
      $overwrites = $this->input->post('overwrite');
      $confirmed = $this->input->post('confirm_overwrite') === '1';

      $companyId = $this->session->userdata('company_id');

      $fail = function ($message) use ($id) {
        $this->session->set_flashdata(
          'message',
          '<div class="alert alert-danger">' . $message . '</div>'
        );
        redirect('allowance/config/' . $id);
      };

      // Validasi struktur input
      if (
        !is_array($allowances) ||
        !is_array($values) ||
        !is_array($dates) ||
        count($allowances) !== count($values) ||
        count($allowances) !== count($dates)      
      ) {
        $fail('Data form tidak valid.');
        return;
      }

      $pending = [
        'allowance' => $allowances,
        'values' => $values,
        'dates' => $dates,
        'overwrite' => $overwrites
      ];

      $items = [];
      $conflicts = [];

      foreach ($allowances as $index => $allowanceId) {
        $value = $values[$index];
        $period = $dates[$index];
        $overwrite = $overwrites[$index] === '1';

        // Validasi input dasar
        if (!is_numeric($value)) {
          $fail('Nilai tunjangan atau opsi overwrite tidak valid.');
          return;
        }

        // Periode kosong berarti tidak ada record periode yang ditargetkan.
        if ($period !== '' && !preg_match('/^\d{4}-\d{2}-\d{2}$/', $period)) {
          $fail('Format periode tidak valid.');
          return;
        }

        // Pastikan tunjangan milik perusahaan yang sedang login.
        $allowance = $this->db->get_where('allowance', [
          'allowance_id' => $allowanceId,
          'company_id' => $companyId
        ])->row_array();

        if (!$allowance) {
          $fail('Tunjangan tidak valid.');
          return;
        }

        $exist = null;

        if ($period !== '') {
          $exist = $this->db->get_where('employee_allowance', [
            'employee_id' => $id,
            'allowance_id' => $allowanceId,
            'payroll_period' => $period
          ])->row_array();
        }

        $items[] = [
          'allowance_id' => $allowanceId,
          'value' => $value,
          'payroll_period' => $period,
          'overwrite' => $overwrite,
          'exist' => $exist
        ];

        if (($overwrite && $exist) || $exist) {
          $conflicts[] = [
            'name' => $allowance['name'],
            'payroll_period' => $period
          ];
        }
      }

      // Minta konfirmasi sebelum menimpa record yang sudah ada.
      if ($conflicts && !$confirmed) {
        $this->session->set_flashdata('allowance_pending', $pending);
        $this->session->set_flashdata('allowance_conflicts', $conflicts);
        $this->session->set_flashdata(
          'message',
          '<div class="alert alert-warning">
            Ditemukan record tunjangan pada periode yang sama.
            Periksa daftar di bawah sebelum mengonfirmasi.
          </div>'
        );

        redirect('allowance/config/' . $id);
        return;
      }

      $this->db->trans_begin();

      foreach ($items as $item) {
        $exist = null;

        // Periksa ulang di dalam transaksi sebelum update.
        if ($item['overwrite'] && $item['payroll_period'] !== '') {
          $exist = $this->db->get_where('employee_allowance', [
            'employee_id' => $id,
            'allowance_id' => $item['allowance_id'],
            'payroll_period' => $item['payroll_period']
          ])->row_array();
        }

        if ($exist) {
          $this->db->where(
            [
              'employee_id' => $id,
              'allowance_id' => $item['allowance_id'],
              'payroll_period' => $item['payroll_period']
            ]
          );
          $this->db->delete('employee_allowance');
          $this->db->where(
            'employee_allowance_id',
            $exist['employee_allowance_id']
          );
          $this->db->update('employee_allowance', [
            'value' => $item['value']
          ]);
        } 
        else {
          $this->db->where(
            [
              'employee_id' => $id,
              'allowance_id' => $item['allowance_id'],
              'payroll_period' => $item['payroll_period']
            ]
          );
          $this->db->delete('employee_allowance');
          $this->db->insert('employee_allowance', [
            'employee_allowance_id' => uniqid(),
            'employee_id' => $id,
            'allowance_id' => $item['allowance_id'],
            'value' => $item['value'],
            'payroll_period' => $item['payroll_period'],
          ]);
        }
      }

      if ($this->db->trans_status() === FALSE) {
        $this->db->trans_rollback();
        $fail('Proses gagal. Silakan coba lagi.');
        return;
      }

      $this->db->trans_commit();
      redirect('karyawan/data');
    }

    #[SkipPermission]
    public function history($employeeId, $allowanceId) {
      log_message('error', 'Allowance history dipanggil: ' . $employeeId . ' / ' . $allowanceId);
      
      $companyId = $this->session->userdata('company_id');

      $allowance = $this->db->get_where('allowance', [
        'allowance_id' => $allowanceId,
        'company_id' => $companyId
      ])->row_array();

      if (!$allowance) {
        return $this->output
          ->set_status_header(404)
          ->set_content_type('application/json')
          ->set_output(json_encode([
            'success' => false,
            'message' => 'Allowance tidak ditemukan.'
          ]));
      }

      $history = $this->db
        ->select('employee_allowance_id, payroll_period, value')
        ->from('employee_allowance')
        ->where('employee_id', $employeeId)
        ->where('allowance_id', $allowanceId)
        ->order_by('payroll_period', 'DESC')
        ->get()
        ->result_array();      

      return $this->output
        ->set_content_type('application/json')
        ->set_output(json_encode([
          'success' => true,
          'allowance' => $allowance['name'],
          'data' => $history
        ]));
    }


    #[SkipPermission]
    public function delete_history($employeeId, $allowanceId, $recordId) {
      if ($this->input->method(TRUE) !== 'POST') {
        return $this->output
          ->set_status_header(405)
          ->set_content_type('application/json')
          ->set_output(json_encode([
            'success' => false,
            'message' => 'Method tidak diizinkan.'
          ]));
      }

      $companyId = $this->session->userdata('company_id');

      $allowance = $this->db->get_where('allowance', [
        'allowance_id' => $allowanceId,
        'company_id' => $companyId
      ])->row_array();

      if (!$allowance) {
        return $this->output
          ->set_status_header(404)
          ->set_content_type('application/json')
          ->set_output(json_encode([
            'success' => false,
            'message' => 'Allowance tidak ditemukan.'
          ]));
      }

      $record = $this->db->get_where('employee_allowance', [
        'employee_allowance_id' => $recordId,
        'employee_id' => $employeeId,
        'allowance_id' => $allowanceId
      ])->row_array();

      if (!$record) {
        return $this->output
          ->set_status_header(404)
          ->set_content_type('application/json')
          ->set_output(json_encode([
            'success' => false,
            'message' => 'Entry allowance tidak ditemukan.'
          ]));
      }

      $this->db->trans_begin();

      $this->db->delete('employee_allowance', [
        'employee_allowance_id' => $recordId,
        'employee_id' => $employeeId,
        'allowance_id' => $allowanceId
      ]);

      if (
        $this->db->trans_status() === FALSE ||
        $this->db->affected_rows() !== 1
      ) {
        $this->db->trans_rollback();

        return $this->output
          ->set_status_header(500)
          ->set_content_type('application/json')
          ->set_output(json_encode([
            'success' => false,
            'message' => 'Gagal menghapus entry allowance.'
          ]));
      }

      $this->db->trans_commit();

      return $this->output
        ->set_content_type('application/json')
        ->set_output(json_encode([
          'success' => true,
          'message' => 'Entry allowance berhasil dihapus.'
        ]));
    }


    public function config($id){
        isEditable();
        $data['htmlpagejs'] = 'none';
        $data['nmenu']      = 'Karyawan';
        $data['title']      = 'Data Karyawan';
        $data['namalabel']  = $data['title'];
        $data['auth']       = authUser();

        $data['id'] = $id;

        $companyId = $this->session->userdata('company_id');

        $data['failed'] = filter_var($this->input->get('failed'),FILTER_VALIDATE_BOOLEAN);

        $data['data'] = $this->db->query("select * from allowance where company_id = ?",[$companyId])->result_array();

        foreach($data['data'] as $index => $a){
          $allowanceId = $a['allowance_id'];
          $q = $this->db->query("select * from employee_allowance where allowance_id = ? and employee_id = ? order by payroll_period desc limit 1 ",[$allowanceId,$id])->row_array();

          if($q){
            $data['data'][$index]['value'] = $q['value'];
            $data['data'][$index]['payroll_period'] = $q['payroll_period'];
          }
          else{
            $data['data'][$index]['value'] = 0;
            $data['data'][$index]['payroll_period'] = date('Y-m-d');
          }
        }


        $data['pending'] = $this->session->flashdata('allowance_pending') ?? [];
        $data['conflicts'] = $this->session->flashdata('allowance_conflicts') ?? [];
        $data['message'] = $this->session->flashdata('message');

        $this->load->view('templates/header', $data);
        $this->load->view('templates/sidemenu', $data);
        $this->load->view('templates/sidenav', $data);
        $this->load->view('module/allowance/config',$data);
        $this->load->view('templates/footer', $data);
        $this->load->view('templates/fscript-html-end', $data);
    }

    #[SkipPermission]
    function monthlyDelete($allowanceId){
      isEditable();
      $this->db->trans_begin();  // to start db transaction
      $this->db->delete('employee_allowance',['allowance_id' => $allowanceUd]);
      $this->db->delete('allowance',['allowance_id' => $allowanceId]);
      if($this->db->trans_status() === FALSE) {
        $this->db->trans_rollback();
        $this->session->set_flashdata(
            'message',
            '<div class="alert alert-danger">Proses gagal. Silakan coba lagi.</div>'
        );
        redirect('allowance/monthly?failed=true');
      } 
      else {
        $this->db->trans_commit();
        redirect('allowance/monthly');

      }      
    }

    #[SkipPermission]
    function delete($allowanceId){
      $this->db->trans_begin();  // to start db transaction
      $this->db->delete('employee_allowance',['allowance_id' => $allowanceUd]);
      $this->db->delete('allowance',['allowance_id' => $allowanceId]);
      if($this->db->trans_status() === FALSE) {
        $this->db->trans_rollback();
        $this->session->set_flashdata(
            'message',
            '<div class="alert alert-danger">Proses gagal. Silakan coba lagi.</div>'
        );
        redirect('allowance?failed=true');
      } 
      else {
        $this->db->trans_commit();
        redirect('allowance');

      }
    }
}
