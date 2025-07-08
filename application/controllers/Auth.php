<?php 

defined('BASEPATH') or exit('No direct script access allowed');

class Auth extends CI_Controller {
    public $email;
    public $session;
    public $form_validation;
    public $upload;
    public $pagination;
    public $auth;
    public $news;
    public $slid;

    public function __construct() {
        parent::__construct();
        $this->load->library('form_validation');
        $this->load->model('user/auth_model', 'auth');
        $this->load->model('user/newspaper_model', 'news');
        $this->load->model('user/sliders_model', 'slid');
    }

    // public function index() {
    //     if ($this->session->userdata('u_id')){
    //         redirect('dashboard');
    //     }
    //     $this->form_validation->set_rules('email', 'Email', 'trim|required|xss_clean|htmlspecialchars');
    //     $this->form_validation->set_rules('password', 'Password', 'trim|required|xss_clean|htmlspecialchars');

    //     if ($this->form_validation->run() == false) {
    //         $data['htmlpagejs'] = 'auth';
    //         $data['htmlclasstemp'] = 'customizer-hide';
    //         $data['title'] = 'Mentari Islamic School';

    //         $data['datas'] = $this->news->get_berita();
    //         $data['sliders'] = $this->slid->get_sliders();

    //         $this->load->view('templates/header', $data);
    //         $this->load->view('module/auth/signin', $data);
    //         $this->load->view('templates/fscript-html-end', $data);
    //     } else {
    //         $this->_login();
    //     }
    // }

    public function index() {
        if ($this->session->userdata('u_id')){
            redirect('dashboard');
        }
        $this->form_validation->set_rules('email', 'Email', 'trim|required|xss_clean|htmlspecialchars');
        $this->form_validation->set_rules('password', 'Password', 'trim|required|xss_clean|htmlspecialchars');

        if ($this->form_validation->run() == false) {
            $data['htmlpagejs'] = 'auth';
            $data['htmlclasstemp'] = 'customizer-hide';
            $data['title'] = 'Halaman Login';
            $this->load->view('templates/header', $data);
            $this->load->view('module/auth/signin', $data);
            $this->load->view('templates/fscript-html-end', $data);
        } else {
            $this->_login();
        }
    }

    private function _login() {
        $email = $this->input->post('email');
        $password = $this->input->post('password');
        $user = $this->auth->proses_login($email);

        if ($user != null) {
            if ($user['is_status']=='y') {
                if (password_verify($password, $user['password'])) {
                    $data = [
                        'u_id'          => $user['user_id'],
                        'role_id'       => $user['role_id']
                    ];
                    $this->session->set_userdata($data);
                    redirect('auth');
                } else {
                    $this->session->set_flashdata('message', '<div class="alert alert-danger" role="alert">Kata sandi tidak sesuai. "'.$password.'" </div>');
                    redirect('auth');
                }
            } else {
                $this->session->set_flashdata('message', '<div class="alert alert-warning" role="alert">"'.$email.'" tidak aktif, hubungi tim support untuk informasi lebih lanjut.</div>');
                redirect('auth');
            }
        } else {
            $this->session->set_flashdata('message', '<div class="alert alert-danger" role="alert">Email "'.$email.'" tidak terdaftar.</div>');
            redirect('auth');
        }
    }

    public function logout() {
        $this->session->unset_userdata('u_id');
        $this->session->unset_userdata('role_id');
        $this->session->set_flashdata('message', '<div class="alert alert-primary" role="alert">Telah keluar.</div>');
        redirect('auth');
    }
}

?>