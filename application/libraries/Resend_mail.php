<?php
defined('BASEPATH') OR exit('No direct script access allowed');

require_once FCPATH . 'vendor/autoload.php';

class Resend_mail {
  private $client;

  public function __construct() {
    $r = $_ENV['RESEND_API_KEY'] ?? getenv('RESEND_API_KEY');

    if (!$r) throw new RuntimeException('RESEND_API_KEY belum dikonfigurasi');

    $this->client = Resend::client($r);
  }

  public function send($to, $subject, $html) {
    return $this->client->emails->send([
      'from' => 'Leryn <noreply@lerynsoftware.com>',
      'to' => [$to],
      'subject' => $subject,
      'html' => $html,
    ]);
  }
}