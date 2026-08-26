<?php

defined('BASEPATH') or exit('No direct script access allowed');

class MY_Controller extends CI_Controller {

    public function __construct() {
        parent::__construct();

        $allowed_slugs = [];

        $directory      = $this->router->directory;
        $class          = $this->router->class;
        $method         = $this->router->method;
        $reflection     = new ReflectionMethod($this, $method);
        $skipPermission = !empty($reflection->getAttributes(SkipPermission::class));

        if($this->session->userdata('role_id') == '1') return;

        if ($skipPermission) return;

        if (!$this->session->userdata('u_id')) redirect('auth');

        $roleId = $this->session->userdata('role_id');

        $actions = $this->db->query("select * from role_actions where role_id = ?",[$roleId])->result_array();

        foreach($actions as $act) $allowed_slugs[] = $act['slug'];
        

        $current_slug = $directory.$class.'/'.$method;

        $whitelisted_slugs = [
          'dashboard/index',
          'auth/logout'
        ];

        if(!in_array($current_slug,$whitelisted_slugs) && !in_array($current_slug,$allowed_slugs)){
          show_error('Anda tidak memiliki hak akses untuk membuka halaman ini.', 403, '403 Forbidden - Akses Ditolak');
          exit();
        }
    }
}