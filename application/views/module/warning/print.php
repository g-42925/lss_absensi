<?php
// ─── Helpers ────────────────────────────────────────────────────────────────
$w   = $warning;
$cmp = $company ?? [];

$levelNum   = (int)($w['level'] ?? 1);
$levelWords = ['', 'PERTAMA', 'KEDUA', 'KETIGA'];
$levelLabel = $levelWords[$levelNum] ?? 'PERTAMA';
$spCode     = 'SP-' . $levelNum;

// Indonesian month names
function tglIndo($date) {
    if (empty($date)) return '...............';
    $bulan = ['','Januari','Februari','Maret','April','Mei','Juni',
              'Juli','Agustus','September','Oktober','November','Desember'];
    $d = strtotime($date);
    return date('d', $d) . ' ' . $bulan[(int)date('m', $d)] . ' ' . date('Y', $d);
}

$dateKejadian  = tglIndo($w['date'] ?? '');
$dateCreated   = tglIndo($w['createdAt'] ?? date('Y-m-d'));
$dateStart     = !empty($w['date']) ? $w['date'] : date('Y-m-d');
$dateEnd       = tglIndo(date('Y-m-d', strtotime($dateStart . ' +6 months')));
$dateStartFmt  = tglIndo($dateStart);

// Company fields
$companyName   = htmlspecialchars($cmp['company_name']  ?? '');
$companyAddr   = htmlspecialchars($cmp['address']       ?? '');
$companyEmail  = htmlspecialchars($cmp['email']         ?? '');
$companyPhone  = htmlspecialchars($cmp['mobile']        ?? '');
$companyWeb    = htmlspecialchars($cmp['website']       ?? '');

// City extracted from address (first part before comma, if any)
$cityRaw       = '';
if (!empty($cmp['address'])) {
    $parts   = explode(',', $cmp['address']);
    $cityRaw = trim(end($parts));
}
$companyCity   = !empty($cityRaw) ? $cityRaw : '...............';

// Employee fields
$empName       = htmlspecialchars($w['nama_pegawai']    ?? '');
$empNik        = htmlspecialchars($w['nik'] ?? $w['id_pegawai'] ?? '');
$empPosition   = htmlspecialchars($w['position_name']   ?? '................................');
$empDivision   = htmlspecialchars($w['division_name']   ?? '................................');

// Warning fields
$spNumber      = htmlspecialchars($w['sp_number']       ?? '');
$spTitle       = htmlspecialchars($w['title']           ?? '');
$violation     = nl2br(htmlspecialchars($w['violation'] ?? ''));
$location      = htmlspecialchars($w['location']        ?? '................................');
$regulation    = htmlspecialchars($w['regulation']      ?? '................................');

// Auth (signer)
$signerName    = htmlspecialchars($auth['nama_lengkap'] ?? '');
$signerRole    = htmlspecialchars($auth['nama_role']    ?? '');

// Company logo
$logoPath = '';
if (!empty($cmp['company_id'])) {
    $candidates = [
        FCPATH . 'assets/company/' . $cmp['company_id'] . '.png',
        FCPATH . 'assets/company/' . $cmp['company_id'] . '.jpg',
        FCPATH . 'assets/img/logo/' . $cmp['company_id'] . '.png',
    ];
    foreach ($candidates as $c) {
        if (file_exists($c)) { $logoPath = base_url(str_replace(FCPATH, '', $c)); break; }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Surat Peringatan <?= $spCode; ?> – <?= $spTitle; ?></title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&family=EB+Garamond:ital,wght@0,400;0,600;0,700;1,400&display=swap" rel="stylesheet">
  <style>
    /* ── Reset ── */
    *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

    body {
      background: #d1d5db;
      font-family: 'EB Garamond', 'Georgia', serif;
      font-size: 13pt;
      color: #111;
    }

    /* ── Toolbar ── */
    .toolbar {
      position: fixed;
      top: 0; left: 0; right: 0;
      z-index: 200;
      background: #0f172a;
      display: flex;
      align-items: center;
      gap: 12px;
      padding: 10px 24px;
      box-shadow: 0 2px 10px rgba(0,0,0,.45);
    }
    .toolbar-title {
      flex: 1;
      color: #e2e8f0;
      font-family: 'Inter', sans-serif;
      font-size: 13px;
      font-weight: 500;
    }
    .toolbar-title span {
      color: #94a3b8;
      font-weight: 400;
    }
    .btn-back {
      display: inline-flex; align-items: center; gap: 6px;
      background: transparent;
      color: #94a3b8;
      border: 1px solid #334155;
      border-radius: 6px;
      padding: 6px 14px;
      font-size: 13px;
      font-family: 'Inter', sans-serif;
      text-decoration: none;
      transition: all .2s;
    }
    .btn-back:hover { color: #f1f5f9; border-color: #64748b; }
    .btn-print {
      display: inline-flex; align-items: center; gap: 6px;
      background: #2563eb;
      color: #fff;
      border: none;
      border-radius: 6px;
      padding: 7px 18px;
      font-size: 13px;
      font-family: 'Inter', sans-serif;
      cursor: pointer;
      transition: background .2s;
    }
    .btn-print:hover { background: #1d4ed8; }

    /* ── Page wrapper ── */
    .page-wrap {
      padding: 72px 0 60px;
      display: flex;
      justify-content: center;
    }

    /* ── A4 Paper ── */
    .sp-paper {
      width: 210mm;
      min-height: 297mm;
      background: #fff;
      padding: 18mm 20mm 24mm 28mm;
      box-shadow: 0 8px 40px rgba(0,0,0,.22);
      position: relative;
    }

    /* ── Kop Surat ── */
    .kop {
      display: flex;
      align-items: center;
      gap: 14px;
      padding-bottom: 10px;
      border-bottom: 4px double #111;
      margin-bottom: 22px;
    }
    .kop-logo {
      width: 68px; height: 68px;
      object-fit: contain;
      flex-shrink: 0;
    }
    .kop-logo-box {
      width: 68px; height: 68px;
      border: 2px dashed #ccc;
      border-radius: 4px;
      display: flex; align-items: center; justify-content: center;
      color: #bbb; font-size: 10px; text-align: center;
      line-height: 1.3; flex-shrink: 0;
    }
    .kop-text { flex: 1; }
    .kop-company {
      font-size: 18pt;
      font-weight: 700;
      line-height: 1.15;
      letter-spacing: .4px;
      text-transform: uppercase;
    }
    .kop-address {
      font-size: 9.5pt;
      color: #555;
      margin-top: 4px;
      line-height: 1.5;
    }
    .kop-contact {
      font-size: 9pt;
      color: #666;
      margin-top: 2px;
    }

    /* ── Judul Surat ── */
    .sp-title {
      text-align: center;
      margin: 4px 0 2px;
    }
    .sp-title h2 {
      display: inline-block;
      font-size: 14pt;
      font-weight: 700;
      text-transform: uppercase;
      letter-spacing: 1.2px;
      border-bottom: 2px solid #111;
      padding-bottom: 2px;
    }
    .sp-nomor {
      text-align: center;
      font-size: 11pt;
      color: #333;
      margin-bottom: 22px;
    }

    /* ── Body ── */
    .sp-body { line-height: 1.1; }
    .sp-body p { margin-bottom: 10px; text-align: justify; }

    /* identity tables */
    .id-table {
      width: 100%;
      border-collapse: collapse;
      margin: 4px 0 14px 24px;
      font-size: 12.5pt;
    }
    .id-table td { padding: 1px 0; vertical-align: top; }
    .id-table td:first-child { width: 145px; white-space: nowrap; }
    .id-table td.sep    { width: 14px; text-align: center; }

    /* violation block */
    .violation-block {
      margin: 6px 0 14px 24px;
      border-left: 3px solid #444;
      padding: 8px 16px;
      background: #f8f8f8;
      line-height: 1.7;
    }

    /* expectation list */
    .expect-list {
      margin: 6px 0 14px 40px;
      padding: 0;
      list-style: decimal;
    }
    .expect-list li { margin-bottom: 4px; }

    /* ── Signature section ── */
    .sig-date {
      text-align: right;
      margin-top: 24px;
      margin-bottom: 10px;
      font-size: 12pt;
    }
    .sig-wrap {
      display: flex;
      justify-content: space-between;
      gap: 20px;
      margin-top: 10px;
      page-break-inside: avoid;
    }
    .sig-block { flex: 1; text-align: center; }
    .sig-role {
      font-weight: 700;
      font-size: 12pt;
      margin-bottom: 6px;
    }
    .sig-note {
      font-size: 10pt;
      color: #555;
      font-style: italic;
      margin-bottom: 64px;
    }
    .sig-line {
      display: inline-block;
      min-width: 160px;
      border-top: 1px solid #333;
      padding-top: 4px;
      font-weight: 700;
      font-size: 12pt;
    }
    .sig-pos {
      display: block;
      font-size: 10.5pt;
      color: #555;
      margin-top: 2px;
    }

    /* ── Print media ── */
    @media print {
      body        { background: #fff; }
      .toolbar    { display: none !important; }
      .page-wrap  { padding: 0; }
      .sp-paper   { box-shadow: none; margin: 0; width: 100%; min-height: unset; }
    }
  </style>
</head>
<body>

<!-- ═══ Toolbar ═══ -->
<div class="toolbar">
  <a href="<?= base_url('warning'); ?>" class="btn-back">
    <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="currentColor" viewBox="0 0 24 24"><path d="M15.41 16.58L10.83 12l4.58-4.59L14 6l-6 6 6 6z"/></svg>
    Kembali
  </a>
  <p class="toolbar-title">
    Preview &mdash; <strong><?= $spCode; ?></strong>
    <span>/ <?= $spTitle; ?></span>
  </p>
  <button class="btn-print" onclick="window.print()">
    <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" fill="currentColor" viewBox="0 0 24 24">
      <path d="M6 9V2h12v7H6zm0 5H4a2 2 0 0 1-2-2V9a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v3a2 2 0 0 1-2 2h-2v6H6v-6zm2 4h8v-4H8v4zm-2-7a1 1 0 1 0 2 0 1 1 0 0 0-2 0z"/>
    </svg>
    Cetak
  </button>
</div>

<!-- ═══ Paper ═══ -->
<div class="page-wrap">
<div class="sp-paper">

  <!-- KOP SURAT -->
  <div class="kop">
    <div class="">
      <img src="<?= $company['logo'] ?>" alt="Logo" class="kop-logo">
    </div>
    <div class="kop-text">
      <div class="kop-company"><?= $companyName ?: '&nbsp;'; ?></div>
      <?php if ($companyAddr): ?>
        <div class="kop-address"><?= $companyAddr; ?></div>
      <?php endif; ?>
      <?php if ($companyPhone || $companyEmail || $companyWeb): ?>
        <div class="kop-contact">
          <?php if ($companyPhone) echo 'Telp: ' . $companyPhone; ?>
          <?php if ($companyEmail) echo ($companyPhone ? ' &nbsp;|&nbsp; ' : '') . 'Email: ' . $companyEmail; ?>
          <?php if ($companyWeb)   echo ($companyPhone || $companyEmail ? ' &nbsp;|&nbsp; ' : '') . $companyWeb; ?>
        </div>
      <?php endif; ?>
    </div>
  </div>
  <!-- /KOP SURAT -->

  <!-- JUDUL -->
  <div class="sp-title">
    <h2>Surat Peringatan <?= $levelLabel; ?> (<?= $spCode; ?>)</h2>
  </div>
  <div class="sp-nomor">Nomor: <?= $spNumber; ?></div>

  <!-- BADAN SURAT -->
  <div class="sp-body">

    <p>Yang bertanda tangan di bawah ini:</p>

    <table class="id-table">
      <tr>
        <td>Nama</td><td class="sep">:</td>
        <td><?= $signerName ?: '________________________________'; ?></td>
      </tr>
      <tr>
        <td>Jabatan</td><td class="sep">:</td>
        <td><?= $signerRole ?: $position['name'] ?></td>
      </tr>
      <tr>
        <td>Perusahaan</td><td class="sep">:</td>
        <td><?= $companyName ?: $signerName; ?></td>
      </tr>
    </table>

    <p>Dengan ini memberikan Surat Peringatan <?= $levelLabel; ?> kepada:</p>

    <table class="id-table">
      <tr>
        <td>Nama</td><td class="sep">:</td>
        <td><strong><?= $empName; ?></strong></td>
      </tr>
      <tr>
        <td>NIK</td><td class="sep">:</td>
        <td><?= $empNik; ?></td>
      </tr>
      <tr>
        <td>Jabatan</td><td class="sep">:</td>
        <td><?= $empPosition; ?></td>
      </tr>
      <tr>
        <td>Divisi</td><td class="sep">:</td>
        <td><?= $empDivision; ?></td>
      </tr>
    </table>

    <p>
      Berdasarkan hasil evaluasi dan/atau laporan yang diterima, Saudara telah melakukan pelanggaran
      terhadap ketentuan perusahaan, yaitu:
    </p>

    <div class="violation-block">
      <?= $violation; ?>
    </div>

    <table class="id-table">
      <tr>
        <td>Waktu kejadian</td><td class="sep">:</td>
        <td><?= $dateKejadian; ?></td>
      </tr>
      <tr>
        <td>Ketentuan yang dilanggar</td><td class="sep">:</td>
        <td><?= $warning['title']; ?></td>
      </tr>
    </table>

    <p>
      Sehubungan dengan hal tersebut, perusahaan memberikan Surat Peringatan <?= $levelLabel; ?>
      (<?= $spCode; ?>) sebagai bentuk pembinaan agar Saudara tidak mengulangi pelanggaran yang sama
      maupun pelanggaran lainnya.
    </p>

    <p>
      Apabila Saudara kembali melakukan pelanggaran selama masa berlaku surat ini, perusahaan berhak
      mengambil tindakan disipliner sesuai dengan ketentuan yang berlaku
    </p>

    <p>
      Demikian surat ini dibuat untuk menjadi perhatian dan dilaksanakan sebagaimana mestinya.
    </p>

    <!-- Tanggal & Tanda Tangan -->
    <div class="sig-date"><?= $companyCity; ?>, <?= $dateCreated; ?></div>

    <div class="sig-wrap">

      <!-- Pihak Perusahaan -->
      <div class="sig-block">
        <div class="sig-role">Pihak Perusahaan</div>
        <div class="sig-note">Tanda Tangan:</div>
        <span class="sig-line"><?= $signerName; ?></span>
        <span class="sig-pos"><?= $signerRole; ?></span>
      </div>

      <!-- Penerima Surat -->
      <div class="sig-block">
        <div class="sig-role">Penerima Surat</div>
        <div class="sig-note">
          Saya menyatakan telah menerima<br>dan membaca Surat Peringatan ini.
        </div>
        <span class="sig-line"><?= $empName; ?></span>
        <span class="sig-pos">Karyawan</span>
      </div>

    </div>
    <!-- /Tanda tangan -->

  </div><!-- /.sp-body -->

</div><!-- /.sp-paper -->
</div><!-- /.page-wrap -->

</body>
</html>
