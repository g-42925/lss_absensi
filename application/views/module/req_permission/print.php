<!doctype html>
<html lang="id">
<head>
<meta charset="utf-8" />
<meta name="viewport" content="width=device-width,initial-scale=1" />
<title>Formulir Pengajuan Cuti - Muhammad Iqbal</title>
<style>
  /* Reset & basic */
  body { font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial; margin: 0; padding: 20px; color: #111; background: #fff; }
  .page { max-width: 900px; margin: 0 auto; padding: 24px; border: 1px solid #e6e6e6; box-shadow: 0 2px 6px rgba(0,0,0,0.04); }
  header { display:flex; gap:16px; align-items:center; margin-bottom: 18px; }
  .logo { width:84px; height:84px; object-fit:cover; border-radius:8px; border:1px solid #ddd; }
  h1 { font-size:18px; margin:0; }
  .meta { margin-left:auto; text-align:right; font-size:13px; color:#444; }
  .meta div { line-height:1.25; }

  /* Info table */
  .section { margin-top:18px; }
  table.info { width:100%; border-collapse:collapse; }
  table.info td, table.info th { padding:8px 10px; vertical-align:top; border-bottom: 1px solid #f0f0f0; }
  table.info th { text-align:left; width:240px; color:#333; font-weight:600; background:transparent; }
  .note { font-size:13px; color:#555; margin-top:8px; }

  /* Highlight for warning values */
  .warn { color: #b33; font-weight:700; }

  /* Signatures */
  .sigs { display:flex; gap:24px; margin-top:28px; }
  .sig { flex:1; text-align:center; }
  .sig-box { height:84px; border-bottom:1px dotted #bbb; margin-bottom:8px; }

  /* Print button */
  .actions { margin-top:18px; display:flex; gap:8px; }
  button { background:#0b74de; color:white; border:none; padding:8px 12px; border-radius:6px; cursor:pointer; }
  button.secondary { background:#777; }

  /* Print styling */
  @media print {
    body { padding:0; }
    .page { border:none; box-shadow:none; margin:0; max-width:100%; }
    .actions { display:none; }
    header .meta { font-size:12px; }
  }

  /* small screens */
  @media (max-width:600px) {
    header { flex-direction:column; align-items:flex-start; gap:10px; }
    .meta { margin-left:0; text-align:left; width:100%; }
    table.info th { width: 40%; }
  }
</style>
</head>
<body>
<div class="page" id="print-area">
  <header>
    <img class="logo" src="assets/uploaded/users/default-logo.png" alt="Foto Pegawai" onerror="this.style.display='none'"/>
    <div>
      <h1>Formulir Pengajuan Izin / Cuti</h1>
    </div>

    <div class="meta">
      <div>Diajukan: <strong><?= $data['created_at'] ?></strong></div>
    </div>
  </header>

  <section class="section">
    <table class="info" role="table" aria-label="detail pengajuan">
      <tr>
        <th>Nama Pegawai</th>
        <td><strong><?= $data['nama_pegawai'] ?></strong> (ID: <em><?= $data['id_pegawai'] ?></em>)</td>
      </tr>

      <tr>
        <th>NIK / No. Pegawai / Email</th>
        <td><?= $data['nik'] ?> / <?= $data['nomor_pegawai'] ?> / <?= $data['email_pegawai'] ?></td>
      </tr>

      <tr>
        <th>Posisi / Divisi</th>
        <td><?= $data['name'] ?> / <?= $data['division_name'] ?></td>
      </tr>

      <tr>
        <th>Tanggal Request</th>
        <td>
          Dari <?= $data['tanggal_request'] ?> Sampai <?= $data['tanggal_request_end'] ?>
        </td>
      </tr>

      <tr>
        <th>Alasan / Catatan</th>
        <td>
          <?= $data['catatan_awal'] ?>
        </td>
      </tr>

      <tr>
        <th>Disetujui Oleh</th>
        <td><?= $this->session->userdata('nama_lengkap') ?></td>
      </tr>

    </table>
  </section>

  <section class="section sigs" aria-label="tanda tangan">
    <div class="sig">
      <div class="sig-box"></div>
      <div>Nama Pegawai</div>
      <div style="font-weight:600; margin-top:6px;"><?= $data['nama_pegawai'] ?></div>
    </div>

    <div class="sig">
      <div class="sig-box"></div>
      <div>Disetujui Oleh</div>
      <div style="font-weight:600; margin-top:6px;"><?= $this->session->userdata('nama_lengkap') ?></div>
    </div>

    <div class="sig">
      <div class="sig-box"></div>
      <div>HR / Admin</div>
      <div style="font-weight:600; margin-top:6px;">(<?= $this->session->userdata('nama_lengkap') ?>)</div>
    </div>
  </section>
</div>

<script>
  // Fill status if empty (from data is_status => empty). Keep dash if not available.
  (function(){
    var status = ""; // data 'is_status' kosong
    document.getElementById('status-field').textContent = status ? status : '-';
  })();

  // Provide a simple "download HTML" function so user can save file locally
  function downloadAsHtml(){
    var doc = document.documentElement.outerHTML;
    var blob = new Blob([doc], {type: 'text/html'});
    var url = URL.createObjectURL(blob);
    var a = document.createElement('a');
    a.href = url;
    a.download = 'pengajuan_cuti_133_muhammad_iqbal.html';
    document.body.appendChild(a);
    a.click();
    a.remove();
    URL.revokeObjectURL(url);
  }
</script>
</body>
</html>
