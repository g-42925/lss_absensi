<?php
defined('BASEPATH') or exit('No direct script access allowed');

$active_group = 'default';
$query_builder = TRUE;

$host = isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : '';

// 1. Opsi Coolify / Docker Container (Menggunakan Environment Variable atau Domain sslip.io)
if (getenv('DB_HOST') !== false || strpos($host, 'sslip.io') !== false) {
  $db['default'] = array(
    'dsn'     => '',
    'hostname' => getenv('DB_HOST') ?: 'yb1m4buiisdlav5jyqhis2d0', // Fallback ke nama service MySQL Coolify
    'username' => getenv('DB_USER') ?: 'root',
    'password' => getenv('DB_PASS') ?: 'iupm5OPBV7LYKWBsJiA5V68p3vDLLy54quMFAlzEGF0LI5mslBEL5kGW327GFwg9',
    'database' => getenv('DB_NAME') ?: 'db_erp',
    'dbdriver' => 'mysqli',
    'dbprefix' => '',
    'pconnect' => FALSE,
    'db_debug' => (ENVIRONMENT !== 'production'),
    'cache_on' => FALSE,
    'cachedir' => '',
    'char_set' => 'utf8mb4',
    'dbcollat' => 'utf8mb4_general_ci',
    'swap_pre' => '',
    'encrypt'  => FALSE,
    'compress' => FALSE,
    'stricton'  => FALSE,
    'failover' => array(),
    'save_queries' => TRUE
  );
}
// 2. Localhost Development
elseif (strpos($host, 'localhost') !== false || strpos($host, '127.0.0.1') !== false) {
  $db['default'] = array(
    'dsn' => '',
    'hostname' => 'localhost',
    'username' => 'root',
    'password' => '',
    'database' => 'db_erp',
    'dbdriver' => 'mysqli',
    'dbprefix' => '',
    'pconnect' => FALSE,
    'db_debug' => (ENVIRONMENT !== 'production'),
    'cache_on' => FALSE,
    'cachedir' => '',
    'char_set' => 'utf8mb4',
    'dbcollat' => 'utf8_general_ci',
    'swap_pre' => '',
    'encrypt' => FALSE,
    'compress' => FALSE,
    'stricton' => FALSE,
    'failover' => array(),
    'save_queries' => TRUE
  );
}
// 3. Jaringan Lokal IP 192.168.1.x
elseif (strpos($host, '192.168.1') !== false) {
  $db['default'] = array(
    'dsn' => '',
    'hostname' => 'localhost',
    'username' => 'root',
    'password' => '',
    'database' => 'db_erp',
    'dbdriver' => 'mysqli',
    'dbprefix' => '',
    'pconnect' => FALSE,
    'db_debug' => (ENVIRONMENT !== 'production'),
    'cache_on' => FALSE,
    'cachedir' => '',
    'char_set' => 'utf8mb4',
    'dbcollat' => 'utf8_general_ci',
    'swap_pre' => '',
    'encrypt' => FALSE,
    'compress' => FALSE,
    'stricton' => FALSE,
    'failover' => array(),
    'save_queries' => TRUE
  );
}
// 4. DOMCloud
elseif (strpos($host, 'dom.my.id') !== false) {
  $db['default'] = array(
    'dsn'      => '',
    'hostname' => 'localhost',
    'username' => 'bowed_associate_wib',
    'password' => 'P5t41RH8FAh(y9vs--',
    'database' => 'bowed_associate_wib_db',
    'dbdriver' => 'mysqli',
    'dbprefix' => '',
    'pconnect' => FALSE,
    'db_debug' => (ENVIRONMENT !== 'production'),
    'cache_on' => FALSE,
    'cachedir' => '',
    'char_set' => 'utf8mb4',
    'dbcollat' => 'utf8_general_ci',
    'swap_pre' => '',
    'encrypt'  => FALSE,
    'compress' => FALSE,
    'stricton'  => FALSE,
    'failover' => array(),
    'save_queries' => TRUE
  );
}
// 5. Hostinger (Default Fallback untuk domain production utama)
else {
  $db['default'] = array(
    'dsn' => '',
    'hostname' => 'localhost',
    'username' => 'u279313339_iqbal',
    'password' => '9Pb&v^k+a#!',
    'database' => 'u279313339_db_erp',
    'dbdriver' => 'mysqli',
    'dbprefix' => '',
    'pconnect' => FALSE,
    'db_debug' => (ENVIRONMENT !== 'production'),
    'cache_on' => FALSE,
    'cachedir' => '',
    'char_set' => 'utf8mb4',
    'dbcollat' => 'utf8_general_ci',
    'swap_pre' => '',
    'encrypt' => FALSE,
    'compress' => FALSE,
    'stricton' => FALSE,
    'failover' => array(),
    'save_queries' => TRUE
  );
}