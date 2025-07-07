<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Other_model extends CI_Model {

    public function __construct() {
        parent::__construct();
    }

    public function cronjob() {

        $tgl = date('Y-m-d');
        $time = date('H:i');

        if($time=='00:00' || $time=='00:59'){

            $check_tgl = $this->db->query("SELECT * FROM tx_tanggal WHERE tanggal='$tgl'")->num_rows();
            if ($check_tgl==0) {
                $data = [
                    'tanggal' => $tgl
                ];
                $this->db->insert('tx_tanggal', $data);
            }            

            $query = "SELECT a.pegawai_id as pid, a.nama_pegawai, a.tanggal_mulai_kerja, b.*, c.mulai_berlaku_tanggal, c.dari_hari_ke, c.is_day, c.pola_kerja_id FROM m_pegawai a
                LEFT JOIN tx_absensi b ON a.pegawai_id=b.pegawai_id AND b.tanggal_absen='$tgl' AND b.is_pending='n'
                LEFT JOIN m_pegawai_pola c ON a.pegawai_id=c.pegawai_id AND c.is_selected='y'
                WHERE a.is_del='n'";

            $result = $this->db->query($query)->result_array();

            foreach ($result as $row) {

                $q2 = $this->db->query("SELECT * FROM tx_absensi WHERE tanggal_absen='$tgl' AND pegawai_id='$row[pid]'")->num_rows();

                if (isset($row['mulai_berlaku_tanggal'])) {
                    if ($tgl>=$row['mulai_berlaku_tanggal']) {

                        $tgl1 = strtotime($row['mulai_berlaku_tanggal']); 
                        $tgl2 = strtotime($tgl); 

                        $jarak = $tgl2 - $tgl1;
                        $hari = $jarak / 60 / 60 / 24;
                        // $checkJumlahPola = checkJumlahPola($row['pola_kerja_id'],$hari+($row['dari_hari_ke']-1));
                        $checkJumlahPola = checkJumlahPola($row['pola_kerja_id'],$hari+($row['dari_hari_ke']));
                        $q = $this->db->query("SELECT a.*, b.toleransi_terlambat FROM m_pola_kerja_det a 
                            JOIN m_pola_kerja b ON a.pola_kerja_id=b.pola_kerja_id 
                            WHERE a.pola_kerja_id='$row[pola_kerja_id]' AND a.is_day='$checkJumlahPola'")->row_array();

                        if (!isset($q['jam_masuk'])) { $q['jam_masuk'] = ''; }
                        if (!isset($q['jam_pulang'])) { $q['jam_pulang'] = ''; }
                        if (!isset($q['toleransi_terlambat'])) { $q['toleransi_terlambat'] = ''; }

                        if (isset($q['is_work']) && $q['is_work']=='n') { $st = 'l'; }else{ $st = 'ts'; }
                        
                        
                            if ($q2==0) {
                                $datains = [
                                    'tanggal_absen'       => $tgl,
                                    'pegawai_id'          => $row['pid'],
                                    'j_masuk'             => $q['jam_masuk'],
                                    'j_pulang'            => $q['jam_pulang'],
                                    'j_toleransi'         => $q['toleransi_terlambat'],
                                    'is_status'           => $st
                                ];
                                $this->db->insert('tx_absensi', $datains);
                            }
                    }
                }
            }
        }
    }

    public function hapus_data($tabel,$field,$id,$setfield='is_del') {
        $this->db->set([
            $setfield     => 'y'
        ]);
        $this->db->where($field, $id);
        return $this->db->update($tabel);
    }

    public function get_roles() {
        $query = $this->db->query("SELECT * FROM m_role WHERE is_del='n' AND is_status='y'")->result_array();
        return $query;
    }

    public function get_permission() {
        $query = $this->db->query("SELECT * FROM m_permission WHERE is_del='n' AND is_status='y'")->result_array();
        return $query;
    }

    public function upload_gambar($nama_name='gambar',$old='new',$dir='components',$namafile = "file_"){

        if ($nama_name=='' || $nama_name==null) { $nama_name = 'gambar'; }

        $path = 'assets/uploaded/'.$dir.'/';
        $config['upload_path'] = $path;
        $config['allowed_types'] = 'jpg|png|jpeg';
        $config['max_size']  = '2000';
        $config['max_width'] = '3024';
        $config['max_height'] = '3024';
        $config['remove_space'] = TRUE;
        $nmfile = $namafile.time();
        $config['file_name'] = $nmfile;
      
        $this->load->library('upload', $config); 
        $this->upload->initialize($config);
        if($this->upload->do_upload($nama_name)){
          if ($old!='new') {
            $expl = explode("/", $old);
            if (isset($expl[3])) {
                if ($expl[3]!='default-logo.png') {
                    if ($expl[3]!='default-cover.png') {
                        if ($expl[3]!='') {
                            if (file_exists(FCPATH.$old)){ unlink(FCPATH.$old); }
                        }
                        if (file_exists(FCPATH .'assets/uploaded/thumbnails/'.$expl[3])){
                            unlink(FCPATH .'assets/uploaded/thumbnails/'.$expl[3]);
                        }
                    }
                }
            }
          }

          $result = $this->upload->data();
          $this->resize_image($path.$result['file_name']);
          $return = array('result' => 'success', 'path' => $path, 'file' => $result, 'error' => '');
          return $return;
        }else{
          // Jika gagal :
          $return = array('result' => 'failed', 'error' => $this->upload->display_errors());
          return $return;
        }
    }

    public function upload_digital($nama_name='digital',$old='new',$dir='components',$namafile = "file_"){

        if ($nama_name=='' || $nama_name==null) { $nama_name = 'digital'; }

        $path = 'assets/uploaded/'.$dir.'/';
        $config['upload_path'] = $path;

        if ($nama_name=='npwp') {
            $config['allowed_types'] = 'pdf|doc|docx|word|jpg|jpeg|png';
        }else if ($nama_name=='imgpdf') {
            $config['allowed_types'] = 'pdf||jpg|jpeg|png';
        }else{
            $config['allowed_types'] = 'pdf|xls|xlsx|doc|docx|word|ppt|pptx|zip|rar|csv|jpg|jpeg|png';
        }
        
        $config['max_size']  = '8000';
        $config['remove_space'] = TRUE;
        $nmfile = $namafile.time();
        $config['file_name'] = $nmfile;
      
        $this->load->library('upload', $config); 
        $this->upload->initialize($config);
        if($this->upload->do_upload($nama_name)){
          if ($old!='new') {
            $expl = explode("/", $old);
            if (isset($expl[3])) {
                if ($expl[3]!='default-logo.png') {
                    if ($expl[3]!='default-cover.png') {
                        if ($expl[3]!='') {
                            if (file_exists(FCPATH.$old)){ unlink(FCPATH.$old); }
                        }
                        if (file_exists(FCPATH .'assets/uploaded/thumbnails/'.$expl[3])){
                            unlink(FCPATH .'assets/uploaded/thumbnails/'.$expl[3]);
                        }
                    }
                }
            }
          }
          $return = array('result' => 'success', 'path' => $path, 'file' => $this->upload->data(), 'error' => '');
          return $return;
        }else{
          $return = array('result' => 'failed', 'error' => $this->upload->display_errors());
          return $return;
        }
    }

    public function resize_image($source_path) {
      $target_path = 'assets/uploaded/thumbnails/';
      $config_manip = array(
          'image_library' => 'gd2',
          'source_image' => $source_path,
          'new_image' => $target_path,
          'maintain_ratio' => TRUE,
          'width' => 150,
      );
   
      $this->load->library('image_lib', $config_manip);
      $this->image_lib->resize();
      // if (!$this->image_lib->resize()) {
      //     echo $this->image_lib->display_errors();
      // }
      $this->image_lib->clear();
   }

}
