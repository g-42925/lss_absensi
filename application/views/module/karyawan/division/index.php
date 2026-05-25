<div class="container-xxl flex-grow-1 container-p-y m-3">
  <div class="card">
    <div class="card-header border-bottom">
      <h5 class="card-title">Data Divisi</h5>
      <div class="text-start">
        <a href="<?=base_url('karyawan/division/add?failed=false');?>" class="btn btn-secondary btn-primary btn-sm"><i class="ti ti-plus me-md-1"></i> Tambah Divisi</a>
      </div>
    </div>
    <div class="card-datatable table-responsive">
      <div class="table-responsive">
  <table class="table table-bordered">
    <tbody>
      <?php 
      $fields = [
        'Divisi' => fn($r)=> $r['division_name'],
        'Pola kerja' => fn($r)=> $r['work_system'],
        'Nominal penalty terlambat' => fn($r)=> number_format($r['penalty_nominal'],2),
        'Nominal penalty alpha' => fn($r)=> is_int($r['alpha_penalty_value']) ? "Rp ".number_format($r['alpha_penalty_value'],2) : $r['alpha_penalty_value'],
        'Nominal penalty istirahat' => fn($r)=> is_int($r['after_break_late_penalty_value']) ? number_format($r['after_break_late_penalty_value'],2) : $r['after_break_late_penalty_value'],
        'Restriksi' => fn($r) => $r['restriction']." Minute",
        'Restriksi absen pulang' => fn($r) => $r['clockout_restriction']." Minute",
        'Konsekuensi alpha' => fn($r) => $r['alpha_consequence'],
        'Upah lembur' => fn($r) => number_format($r['overwork_fee'],2),
        'Aksi' => fn($r) => "
          <a href='".base_url()."karyawan/division/edit/".$r['id']."?failed=false' class='btn p-1' title='Edit Divisi'>
            <i class='ti ti-edit'></i>
          </a>
        ",
      ];
      
      foreach($fields as $label => $func): ?>
      <tr>
        <th><?= $label ?></th>
        <?php foreach($datas as $row): ?>
          <td><?= $func($row) ?></td>
        <?php endforeach; ?>
      </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
</div>
    </div>
  </div>
</div>