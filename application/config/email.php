<?php

defined('BASEPATH') OR exit('No direct script access allowed');

$config['protocol']    = 'smtp';
$config['smtp_host']   = 'ssl://smtp.googlemail.com'; // atau 'tls://smtp.googlemail.com'
$config['smtp_port']   = 465;                        // 465 untuk SSL, 587 untuk TLS
$config['smtp_user']   = 'mkzbdfgh@gmail.com';     // Alamat Gmail Anda
$config['smtp_pass']   = 'fmdkbycpvjoonumy';        // Password Aplikasi (App Password)
$config['mailtype']    = 'html';                     // 'text' atau 'html'
$config['charset']     = 'utf-8';
$config['newline']     = "\r\n";
$config['crlf']        = "\r\n";
$config['wordwrap']    = TRUE;