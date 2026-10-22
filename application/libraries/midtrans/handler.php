<?php
  require_once "veritrans.php";
  require_once "../module.php";

  Veritrans_Config::$isProduction = true;
  Veritrans_Config::$serverKey = $rest_sistem['result']['midtrans_serverkey'];

  $notif = new Veritrans_Notification();

  $transaction = $notif->transaction_status;
  $type = $notif->payment_type;
  $order_id = $notif->order_id;
  $fraud = $notif->fraud_status;

  $filt_order_id = substr($order_id,0,5);

  if ($filt_order_id=='TOPUP') {
    $linkrest = 'proses_update_topup_midtrans/';
  }else{
    $linkrest = 'proses_update_transaksi_midtrans/';
  }

  if ($transaction == 'capture') {
    // For credit card transaction, we need to check whether transaction is challenge by FDS or not
    if ($type == 'credit_card'){
      if($fraud == 'challenge'){
        // TODO set payment status in merchant's database to 'Challenge by FDS'
        // TODO merchant should decide whether this transaction is authorized or not in MAP
        $arr = array('status' => 'b', 'idtrx' => $order_id);
        $rest_kategori = loadData('rest_proses/'.$linkrest, $arr);
      } else {
        $arr = array('status' => 'y', 'idtrx' => $order_id);
        $rest_kategori = loadData('rest_proses/'.$linkrest, $arr);
      }
    }
  } else if ($transaction == 'settlement'){
      $arr = array('status' => 'y', 'idtrx' => $order_id);
      $rest_kategori = loadData('rest_proses/'.$linkrest, $arr);
  } else if($transaction == 'pending'){
    // sudah otomatis pending
  } else if ($transaction == 'deny') {
      $arr = array('status' => 'b', 'idtrx' => $order_id);
      $rest_kategori = loadData('rest_proses/'.$linkrest, $arr);
  } else if ($transaction == 'expire') {
      $arr = array('status' => 'b', 'idtrx' => $order_id);
      $rest_kategori = loadData('rest_proses/'.$linkrest, $arr);
  } else if ($transaction == 'cancel') {
      $arr = array('status' => 'b', 'idtrx' => $order_id);
      $rest_kategori = loadData('rest_proses/'.$linkrest, $arr);
  }
?>