<?php
defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Kpi_absensi
 *
 * Controller untuk modul Employee Monthly KPI berbasis absensi.
 *
 * Routes:
 *   GET  kpi_absensi/              → index (daftar karyawan + KPI bulan lalu)
 *   GET  kpi_absensi/index         → sama dengan di atas
 *   POST kpi_absensi/index         → filter periode (bulan/tahun)
 *   GET  kpi_absensi/detail/{pid}/{bulan}/{tahun}  → detail satu karyawan
 *   POST kpi_absensi/generate      → generate/simpan KPI semua karyawan periode tertentu
 *   GET  kpi_absensi/generate_one/{pid}/{bulan}/{tahun} → generate satu karyawan
 *   GET  kpi_absensi/export/{bulan}/{tahun} → download CSV semua karyawan
 */
class Kpi_absensi extends CI_Controller
{
    public $email;
    public $session;
    public $form_validation;
    public $upload;
    public $pagination;
    public $other;
    public $menu;
    public $rp;
    public $kpi_m;
    

    public function __construct()
    {
        parent::__construct();
        is_logged_in();
        $this->load->model('user/menu_model', 'menu');
        $this->load->model('user/kpi_absensi_model', 'kpi_m');
    }

    // =========================================================
    // Helpers & Shared Data
    // =========================================================

    /**
     * Periode default: bulan sebelum bulan berjalan.
     * Contoh: sekarang Agustus 2026 → kembalikan [7, 2026]
     */
    private function _periode_default()
    {
        $now   = new DateTime();
        $first = new DateTime('first day of this month');
        $prev  = (clone $first)->modify('-1 month');
        return [
            'bulan' => intval($prev->format('n')),
            'tahun' => intval($prev->format('Y')),
        ];
    }

    private function _validate_periode($bulan, $tahun)
    {
        $bulan = intval($bulan);
        $tahun = intval($tahun);

        // Tidak boleh menggunakan bulan berjalan atau masa depan
        $now      = new DateTime();
        $sekarang = intval($now->format('Ym'));
        $pilihan  = $tahun * 100 + $bulan;

        if ($bulan < 1 || $bulan > 12 || $tahun < 2020 || $pilihan >= $sekarang) {
            return $this->_periode_default();
        }
        return ['bulan' => $bulan, 'tahun' => $tahun];
    }

    private function _nama_bulan($n){
        $months = [
            1 => 'Januari',    2 => 'Februari', 3 => 'Maret',
            4 => 'April',      5 => 'Mei',       6 => 'Juni',
            7 => 'Juli',       8 => 'Agustus',   9 => 'September',
            10 => 'Oktober',  11 => 'November', 12 => 'Desember',
        ];
        return $months[$n] ?? '-';
    }

    // =========================================================
    // INDEX – Daftar semua karyawan + KPI bulan terpilih
    // =========================================================

    public function index(){
        $companyId = $this->session->userdata('company_id');
        $divisions = $this->db->query("select * from divisions where company_id = ?",[$companyId])->result();
        
        $data = [
            'htmlpagejs'  => 'none',
            'nmenu'       => 'KPI Absensi',
            'title'       => 'KPI Absensi Bulanan',
            'namalabel'   => 'KPI Karyawan Berbasis Absensi',
            'auth'        => authUser(),
        ];

        // Periode dari POST filter atau default
        if ($this->input->post('bulan') && $this->input->post('tahun')) {
            $periode = $this->_validate_periode(
                $this->input->post('bulan'),
                $this->input->post('tahun')
            );
        } 
        else {
            $periode = $this->_periode_default();
        }

        $bulan = $periode['bulan'];
        $tahun = $periode['tahun'];
        $division = $this->input->post('division');
        $keyword = $this->input->post('keyword');
        $division = $division == '' ? 'all' : $division;
        $keyword = $keyword == '' ? 'all' : $keyword;
       
        // Ambil KPI tersimpan
        $kpi_list = $this->kpi_m->get_all_kpi($companyId, $bulan, $tahun, $division, $keyword);

        // Ambil semua karyawan (untuk mengetahui siapa yang belum di-generate)
        $semua_pegawai = $this->kpi_m->get_all_pegawai($companyId,$division,$keyword);

        // Map KPI tersimpan ke pegawai_id
        $kpi_map = [];
        foreach ($kpi_list as $k) {
            $kpi_map[$k['pegawai_id']] = $k;
        }

        // Gabungkan
        $datas = [];
        foreach ($semua_pegawai as $p) {
            $kpi = $kpi_map[$p['pegawai_id']] ?? null;
            $datas[] = array_merge($p, [
                'kpi'          => $kpi,
                'is_generated' => $kpi !== null,
            ]);
        }

        $data = [
            'htmlpagejs'      => 'none',
            'nmenu'           => 'KPI Absensi',
            'title'           => 'KPI Absensi Bulanan',
            'namalabel'       => 'KPI Karyawan Berbasis Absensi',
            'auth'            => authUser(),
            'datas'           => $datas,
            'bulan'           => $bulan,
            'tahun'           => $tahun,
            'division'        => $division,
            'keyword'         => $keyword,
            'nama_bulan'      => $this->_nama_bulan($bulan),
            'total_generated' => count($kpi_list),
            'total_pegawai'   => count($semua_pegawai),
            'divisions'       => $divisions
        ];

        $this->load->view('templates/header', $data);
        $this->load->view('templates/sidemenu', $data);
        $this->load->view('templates/sidenav', $data);
        $this->load->view('module/kpi_absensi/index', $data);
        $this->load->view('templates/footer', $data);
        $this->load->view('templates/fscript-html-end', $data);
    }

    // =========================================================
    // DETAIL – KPI + breakdown harian satu karyawan
    // =========================================================

    public function detail($pegawai_id = null, $bulan = null, $tahun = null){
        if (!$pegawai_id) { redirect('kpi_absensi'); }

        $companyId = $this->session->userdata('company_id');

        // Validasi pegawai
        $pegawai = $this->db->query(
            "SELECT p.*, d.division_name, pos.name AS position_name
             FROM m_pegawai p
             LEFT JOIN divisions d ON p.division_id = d.id
             LEFT JOIN position pos ON p.position_id = pos.id
             WHERE p.pegawai_id = ? AND p.company_id = ? AND p.is_del = 'n' LIMIT 1",
            [$pegawai_id, $companyId]
        )->row_array();

        if (!$pegawai) { redirect('kpi_absensi'); }

        $periode = $this->_validate_periode($bulan, $tahun);
        $bulan   = $periode['bulan'];
        $tahun   = $periode['tahun'];

        // Hitung KPI real-time untuk breakdown harian
        $kpi = $this->kpi_m->calculate_kpi($pegawai_id, $bulan, $tahun);

        // Ambil snapshot tersimpan (untuk status "sudah di-generate")
        $snapshot = $this->kpi_m->get_kpi_one($companyId, $pegawai_id, $bulan, $tahun);

        $data = [
            'htmlpagejs'  => 'none',
            'nmenu'       => 'KPI Absensi',
            'title'       => 'Detail KPI – ' . $pegawai['nama_pegawai'],
            'namalabel'   => 'Detail KPI Absensi',
            'auth'        => authUser(),
            'pegawai'     => $pegawai,
            'kpi'         => $kpi,
            'snapshot'    => $snapshot,
            'bulan'       => $bulan,
            'tahun'       => $tahun,
            'nama_bulan'  => $this->_nama_bulan($bulan),
        ];

        $this->load->view('templates/header', $data);
        $this->load->view('templates/sidemenu', $data);
        $this->load->view('templates/sidenav', $data);
        $this->load->view('module/kpi_absensi/detail', $data);
        $this->load->view('templates/footer', $data);
        $this->load->view('templates/fscript-html-end', $data);
    }

    // =========================================================
    // GENERATE – Simpan KPI semua karyawan (bulk) ke DB
    // =========================================================

    public function generate()
    {
        isEditable();

        $bulan = $this->input->post('bulan');
        $tahun = $this->input->post('tahun');

        $periode = $this->_validate_periode($bulan, $tahun);
        $bulan   = $periode['bulan'];
        $tahun   = $periode['tahun'];

        $companyId = $this->session->userdata('company_id');
        $pegawais  = $this->kpi_m->get_all_pegawai($companyId);

        $success = 0;
        $failed  = 0;

        foreach ($pegawais as $p) {
            try {
                $kpi = $this->kpi_m->calculate_kpi($p['pegawai_id'], $bulan, $tahun);
                $saved = $this->kpi_m->save_kpi($companyId, $p['pegawai_id'], $bulan, $tahun, $kpi);
                if ($saved) { $success++; } else { $failed++; }
            } catch (Exception $e) {
                $failed++;
            }
        }

        $nama_bulan = $this->_nama_bulan($bulan);

        if ($failed === 0) {
            $this->session->set_flashdata('message',
                '<div class="alert alert-success p-cg">✅ Generate KPI berhasil untuk <strong>'
                . $success . ' karyawan</strong> periode ' . $nama_bulan . ' ' . $tahun . '.</div>'
            );
        } else {
            $this->session->set_flashdata('message',
                '<div class="alert alert-warning p-cg">⚠️ Generate selesai: <strong>'
                . $success . ' berhasil</strong>, ' . $failed . ' gagal.</div>'
            );
        }

        redirect('kpi_absensi?bulan=' . $bulan . '&tahun=' . $tahun);
    }

    // =========================================================
    // GENERATE ONE – Simpan KPI satu karyawan ke DB
    // =========================================================

    public function generate_one($pegawai_id = null, $bulan = null, $tahun = null)
    {
        isEditable();
        if (!$pegawai_id) { redirect('kpi_absensi'); }

        $companyId = $this->session->userdata('company_id');
        $periode   = $this->_validate_periode($bulan, $tahun);
        $bulan     = $periode['bulan'];
        $tahun     = $periode['tahun'];

        $kpi   = $this->kpi_m->calculate_kpi($pegawai_id, $bulan, $tahun);
        $saved = $this->kpi_m->save_kpi($companyId, $pegawai_id, $bulan, $tahun, $kpi);

        if ($saved) {
            $this->session->set_flashdata('message',
                '<div class="alert alert-success p-cg">✅ KPI berhasil disimpan.</div>'
            );
        } else {
            $this->session->set_flashdata('message',
                '<div class="alert alert-danger p-cg">❌ Gagal menyimpan KPI.</div>'
            );
        }

        redirect('kpi_absensi/detail/' . $pegawai_id . '/' . $bulan . '/' . $tahun);
    }

    // =========================================================
    // EXPORT – Download CSV semua karyawan periode tertentu
    // =========================================================

    public function export($bulan = null, $tahun = null)
    {
        $companyId = $this->session->userdata('company_id');
        $periode   = $this->_validate_periode($bulan, $tahun);
        $bulan     = $periode['bulan'];
        $tahun     = $periode['tahun'];

        $nama_bulan = $this->_nama_bulan($bulan);
        $filename   = 'KPI_Absensi_' . $nama_bulan . '_' . $tahun . '_' . date('Ymd_His') . '.csv';

        header('Content-Type: text/csv; charset=UTF-8');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        header('Pragma: no-cache');
        header('Expires: 0');

        $output = fopen('php://output', 'w');

        // BOM untuk Excel agar karakter UTF-8 terbaca
        fputs($output, "\xEF\xBB\xBF");

        fputcsv($output, [
            'No', 'NIK', 'Nama Karyawan', 'Divisi', 'Jabatan',
            'Hari Kerja Efektif', 'Hari Hadir', 'Hari Izin', 'Hari Sakit',
            'Hari Cuti', 'Hari Alpha', '% Kehadiran',
            'Jml Terlambat', 'Total Menit Terlambat', 'Rata-rata Menit Terlambat',
            '% Tepat Waktu Masuk', '% Tepat Waktu Pulang', 'Total Jam Kerja',
            'Jumlah SP', 'KPI Score',
        ]);

        $kpi_list = $this->kpi_m->get_all_kpi($companyId, $bulan, $tahun);

        $no = 1;
        foreach ($kpi_list as $k) {
            fputcsv($output, [
                $no++,
                $k['nik'],
                $k['nama_pegawai'],
                $k['division_name'] ?? '-',
                $k['position_name'] ?? '-',
                $k['hari_kerja_efektif'],
                $k['hari_hadir'],
                $k['hari_izin'],
                $k['hari_sakit'],
                $k['hari_cuti'],
                $k['hari_alpha'],
                number_format($k['persen_kehadiran'], 2) . '%',
                $k['jumlah_terlambat'],
                $k['total_menit_terlambat'],
                number_format($k['rata_menit_terlambat'], 2),
                number_format($k['persen_tepat_waktu_masuk'], 2) . '%',
                number_format($k['persen_tepat_waktu_pulang'], 2) . '%',
                number_format($k['total_jam_kerja'], 2) . ' jam',
                $k['jumlah_sp'],
                number_format($k['kpi_score'], 2),
            ]);
        }

        fclose($output);
        exit;
    }
}
