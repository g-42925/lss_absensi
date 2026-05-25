<?php 

$nama_file = 'izin-'.$datap['nama_pegawai'];

header("Content-type: application/vnd-ms-excel");
header("Content-Disposition: attachment; filename=".$nama_file.".xls");

$atextlamat = 'Jln. Kapiten Purba No. 14c, kelurahan Mangga, kecamatan medan tuntungan, kota Medan, kode pos 20141';

if ($datar['is_status']==0) {
    $status = 'Pending';
}else if ($datar['is_status']==1) {
  $status = 'Approved';
}else if ($datar['is_status']==2) {
  $status = 'Reject';
}else{
  $status = 'Unknown';
}

$totgl = '';
if ($datap['tanggal_request_end']!='') {
    $totgl = ' s/d '.indo($datap['tanggal_request_end']);
}

?>

<style>
    table {
        width: 100%;
        border-collapse: collapse;
    }
    th, td {
        border: 1px solid black;
        padding: 8px;
        text-align: center;
        width: auto;
        word-wrap: break-word; /* Memastikan kata yang panjang terputus */
        vertical-align: middle !important;
    }
    .header, .footer {
        border: none;
    }
    .no-border {
        border: none;
    }
    .center {
        width: 100%;
        text-align: center !important;
        vertical-align: middle !important;
    }
    .center img {
        display: block !important;
        margin: 0 auto !important;
        vertical-align: middle !important;
        text-align: center;
    }
</style>

<table>
    <tr class="header">
        <td colspan="7" class="no-border"></td> <!-- Kolom Kosong -->
    </tr>
    <tr class="">
        <td class="no-border"></td> <!-- Kolom Kosong -->
        <td colspan="2" class="center" style="border-right:0px;">
            <img src="<?=base_url('assets/temp/');?>assets/logo/client/ic_xlcc.png" alt="Logo" width="95" style="margin-left:25px;">
        </td>
        <td colspan="4" style="border-left:0px;">
            <h2><?=$setting['nama_perusahaan'];?></h2>
            <?=$setting['alamat_perusahaan'];?>
            <br>
            <br>
        </td>
    </tr>
    <tr>
        <td class="no-border"></td> <!-- Kolom Kosong -->
        <td colspan="6" style="vertical-align: middle !important;""><h2>Form Cuti & Izin</h2></td>
    </tr>
    <tr>
        <th class="no-border"></th> <!-- Kolom Kosong -->
        <th>Nama Karyawan</th>
        <th>Status</th>
        <th>Alasan Cuti</th>
        <th>Tanggal</th>
        <th>Total (hari)</th>
        <th>Sisa Cuti</th>
    </tr>
    <tr>
        <td class="no-border"></td> <!-- Kolom Kosong -->
        <td><?=$datap['nama_pegawai'];?></td>
        <td><?=$status;?></td>
        <td><?=$datap['catatan_awal'];?></td>
        <td><?=indo($datap['tanggal_request']).$totgl;?></td>
        <td><?=$datap['jumlah_cuti'];?></td>
        <td>-</td>
    </tr>
    <tr class="footer">
        <td class="no-border" colspan="7"></td> <!-- Kolom Kosong -->
    </tr>
    <tr class="footer">
        <td class="no-border"></td> <!-- Kolom Kosong -->
        <!-- <td colspan="3" class="no-border" style="text-align: left;">
            Dibuat Oleh,
        </td> -->
        <td colspan="6" class="no-border" style="text-align: right;">
            DiApproved Oleh,<br>
            HRD / Admin
        </td>
    </tr>
</table>