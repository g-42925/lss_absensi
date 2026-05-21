<div class="min-h-screen bg-gray-100 p-6">
  <div class="mx-auto">
    
    <!-- Header -->
    <div class="mb-6 flex flex-col gap-2 md:flex-row">
      <div>
        <h1 class="text-2xl font-bold text-gray-800">
          Data Gaji Karyawan
        </h1>

        <p class="text-sm text-gray-500">
          Periode: Mei 2026
        </p>
      </div>
      <div class="ml-auto">
        <select onchange="filter(this.value)" class="rounded-md border border-gray-200 px-4 py-2 text-sm">
          <?php foreach($months as $month): ?>
            <option <?= $filter == $month['key'] ? 'selected' : '' ?> value="<?= $month['key'] ?>"><?= $month['month'] ?></option>
          <?php endforeach; ?>
        </select>
        <button class="rounded-md bg-black px-4 py-2 text-sm font-medium text-white shadow">
          Export Payroll
        </button>
      </div>
    </div>

    <!-- Table -->
    <div class="overflow-hidden rounded-md bg-white shadow">
      <table class="min-w-full border-collapse">
        
        <!-- Head -->
        <thead class="bg-gray-50">
          <tr class="border-b">
            <th class="px-4 py-4 text-left text-sm font-semibold text-gray-700">
              Karyawan
            </th>

            <th class="px-4 py-4 text-left text-sm font-semibold text-gray-700">
              Status
            </th>

            <th class="px-4 py-4 text-left text-sm font-semibold text-gray-700">
              Nik
            </th>

            <th class="px-4 py-4 text-left text-sm font-semibold text-gray-700">
              Alamat
            </th>

            <th class="px-4 py-4 text-left text-sm font-semibold text-gray-700">
              Masa kerja
            </th>

            <th class="px-4 py-4 text-left text-sm font-semibold text-gray-700">
              Gaji Pokok
            </th>

            <th class="px-4 py-4 text-left text-sm font-semibold text-gray-700">
              Pendapatan
            </th>

            <th class="px-4 py-4 text-left text-sm font-semibold text-gray-700">
              Potongan
            </th>

            <th class="px-4 py-4 text-left text-sm font-semibold text-gray-700">
              Total
            </th>

            <th class="px-4 py-4"></th>
          </tr>
        </thead>

        <!-- Body -->
        <tbody class="divide-y divide-gray-100">
          <?php foreach($employees as $index => $emp): ?>
            <tr>
              <td class="px-4 py-4">
                <div>
                  <p class="font-semibold text-gray-800">
                    <?= $emp['nama_pegawai'] ?>
                  </p>

                  <p class="text-sm text-gray-500">
                    ID: <?= $emp['id_pegawai'] ?>
                  </p>
                </div>
              </td>
              <td class="px-4 py-4">----</td>
              <td class="px-4 py-4">
                <?= $emp['nik'] ?>
              </td>
              <td class="px-4 py-4">
                <?= $emp['address'] ?>
              </td>
              <?php if($emp['status_pegawai'] == 'contract'): ?>
                <td class="px-4 py-4">
                  <div class="space-y-1 text-sm">
                    <div class="flex justify-between gap-6">
                      <span class="text-gray-500">Mulai</span>
                      <span><?= $emp['contract_start_date'] ?></span>
                    </div>

                    <div class="flex justify-between gap-6">
                      <span class="text-gray-500">Berakhir</span>
                      <span><?= $emp['contract_end_date'] ?></span>
                    </div>
                  </div>
                </td>                  
              <?php else: ?>
                <td class="px-4 py-4">
                  <div class="space-y-1 text-sm">
                    <div class="flex justify-between gap-6">
                      <span class="text-gray-500">Mulai</span>
                      <span>-</span>
                    </div>

                    <div class="flex justify-between gap-6">
                      <span class="text-gray-500">Berakhir</span>
                      <span>-</span>
                    </div>
                  </div>
                </td>             
              <?php endif; ?>
              <td class="px-4 py-4">
                <?= number_format($emp['salary']) ?>
              </td>
              <td class="px-4 py-4">
                <div class="space-y-1 text-sm">
                  <?php foreach($emp['plus'] as $key => $value): ?>
                    <div class="flex justify-between gap-6">
                      <span class="text-gray-500"><?= $key ?></span>
                      <span><?= number_format($value) ?></span>
                    </div>
                  <?php endforeach; ?>
                </div>
              </td>
              <td class="px-4 py-4">
                <div class="space-y-1 text-sm">
                  <?php foreach($emp['minus'] as $key => $value): ?>
                    <div class="flex justify-between gap-6">
                      <span class="text-gray-500"><?= $key ?></span>
                      <span><?= number_format($value) ?></span>
                    </div>
                  <?php endforeach; ?>
                </div>
              </td>

              <td class="px-4 py-4">
                <?= number_format(round($emp['thp'],0)) ?>
              </td>
            </tr>



          <?php endforeach; ?>
          <!-- ROW 1 -->


        </tbody>
      </table>
    </div>

  </div>
</div>

<script>
  function filter(month){
    window.location.href = "<?= base_url('salary_record/filter/') ?>" + month;
  }
</script>