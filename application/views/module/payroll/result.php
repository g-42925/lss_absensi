<style>
@media print {
  body * {
    visibility: hidden; /* Sembunyikan semua elemen */
  }

  #printArea, #printArea * {
    visibility: visible; /* Kecuali elemen yang ingin diprint */
  }

  #printArea {
    position: absolute;
    left: 0;
    top: 0;
    width: 100%;
    padding: 20px;
  }

  .no-print {
    display: none !important; /* Hilangkan tombol print */
  }
}
</style>

<div class="container-xxl flex-grow-1 container-p-y">
  <div class="max-w-3xl mx-auto">
    <div class="flex justify-end mb-4 no-print">
      <button onclick="window.print()"
          class="px-4 py-2 bg-blue-600 text-white rounded shadow hover:bg-blue-700">
        Cetak / Print
      </button>
    </div>

    <div class="slip bg-white rounded-2xl shadow p-6" id="printArea">
      <!-- Header -->
      <div class="flex items-center justify-between border-b pb-4 mb-4">
        <div>
          <h1 class="text-2xl font-semibold"><?= $final['company']['company_name'] ?></h1>
          <p class="text-sm text-gray-600"><?= $final['company']['address'] ?></p>
        </div>
        <div class="text-right">
          <p class="text-sm text-gray-500">Periode: <span class="font-medium"><?= $from ?> - <?= $to ?> </span></p>
        </div>
      </div>

      <!-- Employee Info -->
      <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6 text-sm">
        <div>
          <p class="text-gray-500">Nama</p>
          <p class="font-medium"><?= $final['emp']['nama_pegawai'] ?></p>
        </div>
        <div>
          <p class="text-gray-500">Jabatan</p>
          <p class="font-medium"><?= $final['emp']['position'] ?></p>
        </div>
        <div>
          <p class="text-gray-500">NIK</p>
          <p class="font-medium"><?= $final['emp']['nik'] ?></p>
        </div>
      </div>

      <!-- Earnings & Deductions -->
      <div class="md:flex md:space-x-6">
        <!-- Earnings -->
        <div class="md:w-1/2">
          <h2 class="text-sm font-semibold text-gray-700 mb-2">Penghasilan</h2>
          <table class="w-full text-sm">
            <tbody>
              <tr class="border-t">
                <td class="py-2">Gaji Pokok</td>
                <td class="py-2 text-right font-medium"><?= number_format($final['salary'], 2) ?></td>
              </tr>

              <?php foreach ($final['allowance'] as $i): ?>
                <tr class="border-t">
                  <td class="py-2"><?= $i['name'] ?></td>
                  <td class="py-2 text-right"><?= number_format($i['value'], 2) ?></td>
                </tr>          
              <?php endforeach; ?>

            </tbody>
          </table>
        </div>

        <!-- Deductions -->
        <div class="md:w-1/2 mt-4 md:mt-0">
          <h2 class="text-sm font-semibold text-gray-700 mb-2">Potongan</h2>
          <table class="w-full text-sm">
              <?php foreach ($final['penalty'] as $i): ?>
                <tr class="border-t">
                  <td class="py-2"><?= $i['name'] ?></td>
                  <td class="py-2 text-right"><?= number_format($i['value'],2) ?></td>
                </tr>          
              <?php endforeach; ?>
              <?php foreach ($final['benefit'] as $b): ?>
                <tr class="border-t">
                  <td class="py-2"><?= $b['name'] ?></td>
                  <td class="py-2 text-right"><?= number_format($b['value'],2) ?></td>
                </tr>          
              <?php endforeach; ?>
            </tbody>
          </table>
        </div>
      </div>

      <!-- Summary Totals -->
      <div class="mt-6 border-t pt-4">
        <div class="md:flex md:justify-end">
          <div class="w-full md:w-1/2">
            <div class="flex justify-between py-1">
              <div class="text-sm text-gray-600">Total Penghasilan</div>
              <div class="font-medium"><?= number_format($final['totalIncome'],2) ?></div>
            </div>
            <div class="flex justify-between py-1">
              <div class="text-sm text-gray-600">Total Potongan</div>
              <div class="font-medium"><?= $final['totalBenefit'] ?></div>
            </div>
            <div class="flex justify-between pt-3 border-t mt-3">
              <div class="text-base font-semibold">Take Home Pay</div>
              <div class="text-base font-semibold"><?= number_format($final['thp'],2) ?></div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>


