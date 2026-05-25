<?php 

defined('BASEPATH') or exit('No direct script access allowed');

class News extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->library('form_validation');
        $this->load->model('user/auth_model', 'auth');
        $this->load->model('user/newspaper_model', 'news');
    }

    public function index() {
        redirect('auth');
    }

    public function detail($id = '') {

        if($id==''){
            redirect('auth');
        }else{
            $data['htmlpagejs'] = 'auth';
            $data['htmlclasstemp'] = 'customizer-hide';
            $data['title'] = 'Mentari Islamic School';

            $data['datas']      = $this->news->get_berita_id($id);

            if($data['datas']!=null){
                $this->load->view('templates/header', $data);
                $this->load->view('module/news/detail', $data);
                $this->load->view('templates/fscript-html-end', $data);
            }else{
                redirect('auth');
            }
        }
    }
}

?>