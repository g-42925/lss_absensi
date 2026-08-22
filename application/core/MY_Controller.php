<?php
defined('BASEPATH') or exit('No direct script access allowed');

class MY_Controller extends CI_Controller {

    public function __construct() {
        parent::__construct();

        // 1. Cek Sesi Login
        if (!$this->session->userdata('u_id')) {
          redirect('auth');
        }

        // 2. Ambil Segmen URL/Route Saat Ini
        $directory = $this->router->directory;
        $class     = $this->router->class;
        $method    = $this->router->method;

        

        // 3. Susun Format Current Slug (Handling Sub-folder & Root)
        $current_slug = $directory.$class.'/'.$method;

        // 4. Whitelist Halaman Bebas Akses (Tanpa Perlu Cek Permission)
        $whitelisted_slugs = [
          'dashboard/index',
          'auth/logout',
          'company/profile/edit_proses',
          'company/admin/add_proses',
          'company/admin/edit_proses',
          'company/admin/hapus',
          'company/roles/add_proses',
          'company/roles/edit_proses',
          'karyawan/data/filter',
          'karyawan/data/all',
          'karyawan/data/get_shift_details',
          'karyawan/data/add_proses',
          'karyawan/data/edit_proses',
          'karyawan/deduction/filter',
          'karyawan/division/edit_proses',
          'karyawan/division/add_proses',
          'karyawan/position/add_proses',
          'karyawan/position/edit_proses',
          'karyawan/premutation/next_proccess',
          'job/edit_proccess',
          'job/add_proccess',
          'candidate/add_proccess',
          'candidate/edit_proccess'
        ];

        // 5. Validasi Akses
        $allowed_slugs = $this->session->userdata('slugs') ;

        if(!in_array($current_slug,$whitelisted_slugs) && !in_array($current_slug,$allowed_slugs)){
          show_error('Anda tidak memiliki hak akses untuk membuka halaman ini.', 403, '403 Forbidden - Akses Ditolak');
          exit();
        }
    }
}