<?php
defined('BASEPATH') or exit('No direct script access allowed');

class MY_Controller extends CI_Controller {

    public function __construct() {
        parent::__construct();

       

        // 2. Ambil Segmen URL/Route Saat Ini
        $directory = $this->router->directory;
        $class     = $this->router->class;
        $method    = $this->router->method;

        $reflection = new ReflectionMethod($this, $method);

        $skipPermission = !empty($reflection->getAttributes(SkipPermission::class));

        if ($skipPermission) return;

        if (!$this->session->userdata('u_id')) redirect('auth');

        // 3. Susun Format Current Slug (Handling Sub-folder & Root)
        $current_slug = $directory.$class.'/'.$method;

        // 4. Whitelist Halaman Bebas Akses (Tanpa Perlu Cek Permission)
        $whitelisted_slugs = [
          'dashboard/index',
          'auth/logout'
        ];

        // 5. Validasi Akses
        $allowed_slugs = $this->session->userdata('slugs') ;

        if(!in_array($current_slug,$whitelisted_slugs) && !in_array($current_slug,$allowed_slugs)){
          show_error('Anda tidak memiliki hak akses untuk membuka halaman ini.', 403, '403 Forbidden - Akses Ditolak');
          exit();
        }
    }
}