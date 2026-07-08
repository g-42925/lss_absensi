<?php
// Helpers
$w   = $warning;
$cmp = $company ?? [];

$levelNum   = (int)($w['level'] ?? 1);
$levelWords = ['', 'PERTAMA', 'KEDUA', 'KETIGA'];
$levelLabel = $levelWords[$levelNum] ?? 'PERTAMA';
$spCode     = 'SP-' . $levelNum;

// Dates
$dateKejadian  = !empty($w['date'])      ? date('d F Y', strtotime($w['date']))      : '...';
$dateCreated   = !empty($w['createdAt']) ? date('d F Y', strtotime($w['createdAt'])) : date('d F Y');

// SP berlaku 6 bulan sejak tanggal kejadian
$dateStart     = !empty($w['date']) ? $w['date'] : date('Y-m-d');
$dateEnd       = date('d F Y', strtotime($dateStart . ' +6 months'));
$dateStartFmt  = date('d F Y', strtotime($dateStart));

$companyName   = htmlspecialchars($cmp['company_name'] ?? '................................');
$companyCity   = '................................'; // tidak ada kolom kota, bisa dikembangkan

$empName       = htmlspecialchars($w['nama_pegawai']  ?? '................................');
$empNik        = htmlspecialchars($w['nik'] ?? $w['id_pegawai'] ?? '................................');
$empPosition   = htmlspecialchars($w['position_name'] ?? '................................');
$empDivision   = htmlspecialchars($w['division_name'] ?? '................................');

$spNumber      = htmlspecialchars($w['sp_number'] ?? '................................');
$violation     = nl2br(htmlspecialchars($w['violation'] ?? ''));
$spTitle       = htmlspecialchars($w['title'] ?? '');
?>
<style>
  @import url('https://fonts.googleapis.com/css2?family=Times+New+Roman&family=EB+Garamond:ital,wght@0,400;0,600;0,700;1,400&display=swap');

  body.print-body {
    background: #e8e8e8;
    font-family: 'EB Garamond', 'Times New Roman', Georgia, serif;
    font-size: 13pt;
    color: #111;
    margin: 0;
    padding: 0;
  }

  .print-toolbar {
    position: fixed;
    top: 0; left: 0; right: 0;
    z-index: 100;
    background: #1e293b;
    padding: 10px 24px;
    display: flex;
    align-items: center;
    gap: 12px;
    box-shadow: 0 2px 8px rgba(0,0,0,.4);
  }
  .print-toolbar h6 {
    color: #f1f5f9;
    margin: 0;
    flex: 1;
    font-family: 'Inter', sans-serif;
    font-size: 13px;
    font-weight: 500;
  }
  .btn-print {
    background: #3b82f6;
    color: #fff;
    border: none;
    border-radius: 6px;
    padding: 7px 18px;
    font-size: 13px;
    cursor: pointer;
    font-family: 'Inter', sans-serif;
    display: flex; align-items: center; gap: 6px;
  }
  .btn-print:hover { background: #2563eb; }
  .btn-back {
    background: transparent;
    color: #94a3b8;
    border: 1px solid #475569;
    border-radius: 6px;
    padding: 6px 14px;
    font-size: 13px;
    cursor: pointer;
    font-family: 'Inter', sans-serif;
    text-decoration: none;
  }
  .btn-back:hover { color: #fff; border-color: #94a3b8; }

  .page-wrap {
    padding: 80px 0 40px;
    display: flex;
    justify-content: center;
  }

  /* -- Paper -- */
  .sp-paper {
    width: 210mm;
    min-height: 297mm;
    background: #fff;
    padding: 20mm 22mm 22mm 28mm; /* left wider for binding */
    box-shadow: 0 4px 32px rgba(0,0,0,.18);
    position: relative;
    box-sizing: border-box;
  }

  /* Kop Surat */
  .kop {
    border-bottom: 3px double #111;
    padding-bottom: 10px;
    margin-bottom: 18px;
    display: flex;
    align-items: center;
    gap: 16px;
  }
  .kop .kop-logo {
    width: 60px; height: 60px;
    border: 2px solid #111;
    border-radius: 4px;
    object-fit: contain;
    flex-shrink: 0;
  }
  .kop .kop-logo-placeholder {
    width: 60px; height: 60px;
    border: 2px solid #bbb;
    border-radius: 4px;
    display: flex; align-items: center; justify-content: center;
    color: #bbb; font-size: 11px; text-align: center; line-height: 1.2;
  }
  .kop-info { flex: 1; }
  .kop-info .company { font-size: 17pt; font-weight: 700; line-height: 1.2; letter-spacing: .3px; }
  .kop-info .address { font-size: 9.5pt; color: #444; margin-top: 3px; }

  /* Judul SP */
  .sp-heading {
    text-align: center;
    margin: 12px 0 6px;
  }
  .sp-heading h2 {
    font-size: 14pt;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 1px;
    margin: 0 0 2px;
    text-decoration: underline;
  }
  .sp-nomor {
    text-align: center;
    font-size: 10.5pt;
    color: #333;
    margin-bottom: 18px;
  }

  /* Body text */
  .sp-body { line-height: 1.8; }

  .sp-table {
    width: 100%;
    border-collapse: collapse;
    margin: 6px 0 12px 20px;
    font-size: 12.5pt;
  }
  .sp-table td { padding: 1px 0; vertical-align: top; }
  .sp-table td:first-child { width: 130px; white-space: nowrap; }
  .sp-table td:nth-child(2) { width: 16px; text-align: center; }

  .sp-violation {
    border-left: 3px solid #333;
    padding: 8px 14px;
    margin: 8px 0 8px 20px;
    background: #f9f9f9;
    font-style: italic;
    line-height: 1.7;
  }

  .sp-list {
    margin: 6px 0 10px 36px;
    padding: 0;
  }
  .sp-list li { margin-bottom: 4px; }

  .sp-closing { margin-top: 20px; }

  /* Tanda tangan */
  .ttd-wrap {
    display: flex;
    gap: 30px;
    margin-top: 36px;
    page-break-inside: avoid;
  }
  .ttd-block {
    flex: 1;
    text-align: center;
  }
  .ttd-label { font-weight: 700; margin-bottom: 4px; font-size: 12pt; }
  .ttd-desc  { font-size: 10.5pt; color: #444; margin-bottom: 60px; font-style: italic; }
  .ttd-name  { border-top: 1px solid #333; padding-top: 4px; font-weight: 700; min-width: 140px; display: inline-block; font-size: 12pt; }
  .ttd-pos   { font-size: 10.5pt; color: #444; }

  /* Print media */
  @media print {
    body.print-body { background: #fff; }
    .print-toolbar  { display: none !important; }
    .page-wrap      { padding: 0; }
    .sp-paper       { box-shadow: none; margin: 0; width: 100%; min-height: unset; }
  }
</style>

<body class="print-body">

<!-- Toolbar (hidden on print) -->
<div class="print-toolbar">
  <a href="<?= base_url('warning'); ?>" class="btn-back">&#8592; Kembali</a>
  <h6>Preview — Surat Peringatan <?= $spCode; ?> &middot; <?= $spTitle; ?></h6>
  <button class="btn-print" onclick="window.print()">
    <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" fill="currentColor" viewBox="0 0 24 24"><path d="M6 9V2h12v7H6zm0 5H4a2 2 0 0 1-2-2V9a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v3a2 2 0 0 1-2 2h-2v6H6v-6zm2 4h8v-4H8v4zm-2-7a1 1 0 1 0 2 0 1 1 0 0 0-2 0z"/></svg>
    Cetak
  </button>
</div>

<div class="page-wrap">
<div class="sp-paper">

  <!-- ===== JUDUL ===== -->
  <div class="sp-heading">
    <h2>Surat Peringatan <?= $levelLabel; ?> (<?= $spCode; ?>)</h2>
  </div>
  <div class="sp-nomor">Nomor: <?= $spNumber; ?></div>

  <!-- ===== BADAN SURAT ===== -->
  <div class="sp-body">

    <p>Yang bertanda tangan di bawah ini:</p>

    <table class="sp-table">
      <tr>
        <td>Nama</td><td>:</td>
        <td><?= htmlspecialchars($auth['nama_lengkap'] ?? '................................'); ?></td>
      </tr>
      <tr>
        <td>Jabatan</td><td>:</td>
        <td><?= htmlspecialchars($auth['nama_role'] ?? '................................'); ?></td>
      </tr>
      <tr>
        <td>Perusahaan</td><td>:</td>
        <td><?= $companyName; ?></td>
      </tr>
    </table>

    <p>Dengan ini memberikan Surat Peringatan <?= $levelLabel; ?> kepada:</p>

    <table class="sp-table">
      <tr>
        <td>Nama</td><td>:</td>
        <td><strong><?= $empName; ?></strong></td>
      </tr>
      <tr>
        <td>NIK</td><td>:</td>
        <td><?= $empNik; ?></td>
      </tr>
      <tr>
        <td>Jabatan</td><td>:</td>
        <td><?= $empPosition; ?></td>
      </tr>
      <tr>
        <td>Divisi</td><td>:</td>
        <td><?= $empDivision; ?></td>
      </tr>
    </table>

    <p>Berdasarkan hasil evaluasi dan/atau laporan yang diterima, Saudara telah melakukan pelanggaran terhadap ketentuan perusahaan, yaitu:</p>

    <table class="sp-table">
      <tr>
        <td>Pelanggaran</td><td>:</td>
        <td><strong><?= $spTitle; ?></strong></td>
      </tr>
      <tr>
        <td>Waktu Kejadian</td><td>:</td>
        <td><?= $dateKejadian; ?></td>
      </tr>
    </table>

    <div class="sp-violation">
      <?= $violation; ?>
    </div>

    <p>
      Sehubungan dengan hal tersebut, perusahaan memberikan Surat Peringatan <?= $levelLabel; ?> (<?= $spCode; ?>)
      sebagai bentuk pembinaan agar Saudara tidak mengulangi pelanggaran yang sama maupun pelanggaran lainnya.
    </p>

    <p>
      Apabila Saudara kembali melakukan pelanggaran selama masa berlaku surat ini, perusahaan berhak mengambil
      tindakan disipliner sesuai dengan ketentuan yang berlaku, termasuk pemberian Surat Peringatan berikutnya
      atau tindakan lain sesuai peraturan perusahaan dan peraturan perundang-undangan.
    </p>


    <div class="sp-closing">
      <p>Demikian surat ini dibuat untuk menjadi perhatian dan dilaksanakan sebagaimana mestinya.</p>
    </div>

  </div><!-- /.sp-body -->

</div><!-- /.sp-paper -->
</div><!-- /.page-wrap -->

</body>
