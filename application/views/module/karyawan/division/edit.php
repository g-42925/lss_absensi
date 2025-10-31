<div class="container-xxl flex-grow-1 container-p-y">
  <div class="card">  
    <!-- Kontainer utama -->
    <div class="min-h-screen flex flex-col">

        <!-- Header/Breadcrumbs -->
        

        <!-- Kontainer konten utama -->
        <form class="card-body flex-grow p-6 md:p-10" action="<?= base_url().'karyawan/division/edit_proses/'.$current['id'] ?>" method="post">
            <?php if ($failed == 1): ?>
              <?=$this->session->flashdata('message');?>
            <?php endif; ?>
            <div class="max-w-6xl mx-auto bg-white rounded-lg shadow-md p-6 md:p-8">
                <!-- Judul dan deskripsi formulir -->
                <h2 class="text-xl md:text-2xl font-semibold text-gray-800 mb-6">
                    Edit Division
                </h2>

                <!-- Bagian Formulir -->
                <div class="space-y-6">

                    <!-- Field: Work System -->
                    

                    <!-- Field: Division Name -->
                    <div class="flex flex-col md:flex-row md:items-center">
                        <label for="division-name" class="block text-gray-600 font-medium md:w-1/3">
                            Division Name <span class="text-red-500">*</span>
                        </label>
                        <div class="mt-1 md:mt-0 md:w-2/3">
                            <input type="text" value="<?= $current['division_name'] ?>" name="divisionName" id="division-name" class="w-full px-4 py-2 border rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                        </div>
                    </div>

                    <div class="flex flex-col md:flex-row md:items-center">
                        <label for="penalty-nominal" class="block text-gray-600 font-medium md:w-1/3">
                            Late Penalty Value <span class="text-red-500">*</span>
                        </label>
                        <div class="mt-1 md:mt-0 md:w-2/3">
                            <input type="number" value="<?= $current['penalty_nominal'] ?>" name="penaltyNominal" id="penaltyNominal" class="w-full px-4 py-2 border rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                        </div>
                    </div>

                    <div class="flex flex-col md:flex-row md:items-center">
                        <label for="penalty-nominal" class="block text-gray-600 font-medium md:w-1/3">
                            Alpha Penalty Value <span class="text-red-500">*</span>
                        </label>
                        <div class="mt-1 md:mt-0 md:w-2/3">
                            <input type="number" value="<?= $current['alpha_penalty_value'] ?>" name="alphaPenaltyValue" id="penaltyNominal" class="w-full px-4 py-2 border rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                        </div>
                    </div>

                     <div class="flex flex-col md:flex-row md:items-center">
                        <label for="penalty-nominal" class="block text-gray-600 font-medium md:w-1/3">
                            After Break Late Penalty Value <span class="text-red-500">*</span>
                        </label>
                        <div class="mt-1 md:mt-0 md:w-2/3">
                            <input type="number" value="<?= $current['after_break_late_penalty_value'] ?>" name="afterBreakLatePenaltyValue" id="abLatePenaltyNominal" class="w-full px-4 py-2 border rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                        </div>
                    </div>

                    <div class="flex flex-col md:flex-row md:items-center hidden">
                        <label for="penalty-nominal" class="block text-gray-600 font-medium md:w-1/3">
                            Work System <span class="text-red-500">*</span>
                        </label>
                        <div class="mt-1 md:mt-0 md:w-2/3">
                            <input type="text" id="pattern" value="<?= $current['work_system'] ?>" name="pattern" class="w-full px-4 py-2 border rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                        </div>
                    </div>

                    <div class="flex flex-col md:flex-row md:items-center">
                        <label for="penalty-nominal" class="block text-gray-600 font-medium md:w-1/3">
                            Restriction After Tolerance <span class="text-red-500">*</span>
                        </label>
                        <div class="mt-1 md:mt-0 md:w-2/3">
                            <input type="text" value="<?= $current['restriction'] ?>" name="restriction" class="w-full px-4 py-2 border rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                        </div>
                    </div>

                     <div class="flex flex-col md:flex-row md:items-center">
                        <label for="penalty-nominal" class="block text-gray-600 font-medium md:w-1/3">
                            Restriction After Clockout <span class="text-red-500">*</span>
                        </label>
                        <div class="mt-1 md:mt-0 md:w-2/3">
                            <input type="text" value="<?= $current['clockout_restriction'] ?>" name="clockoutRestriction" class="w-full px-4 py-2 border rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                        </div>
                    </div>

                     <div class="flex flex-col md:flex-row md:items-center">
                        <label for="penalty-nominal" class="block text-gray-600 font-medium md:w-1/3">
                            Overwork fee <span class="text-red-500">*</span>
                        </label>
                        <div class="mt-1 md:mt-0 md:w-2/3">
                            <input type="text" value="<?= $current['overwork_fee'] ?>" name="overworkFee" class="w-full px-4 py-2 border rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                        </div>
                    </div>

                   

                    <!-- Field: Late Penalty -->
                    <div class="flex flex-col md:flex-row md:items-center">
                        <label class="block text-gray-600 font-medium md:w-1/3">
                            Late Penalty <span class="text-red-500">*</span>
                        </label>
                        <div class="mt-1 md:mt-0 md:w-2/3 flex items-center space-x-6">
                            <label class="inline-flex items-center">
                                <input <?= $current['late_penalty'] == 1 ? 'checked':'' ?> type="radio" name="latePenalty" value="1" class="form-radio text-blue-600">
                                <span class="ml-2 text-gray-700">Yes</span>
                            </label>
                            <label class="inline-flex items-center">
                                <input <?= $current['late_penalty'] == 0 ? 'checked':'' ?> type="radio" name="latePenalty" value="0" class="form-radio text-blue-600">
                                <span class="ml-2 text-gray-700">No</span>
                            </label>
                        </div>
                    </div>

                    <div class="flex flex-col md:flex-row md:items-center">
                        <label class="block text-gray-600 font-medium md:w-1/3">
                            Clockout Late Penalty <span class="text-red-500">*</span>
                        </label>
                        <div class="mt-1 md:mt-0 md:w-2/3 flex items-center space-x-6">
                            <label class="inline-flex items-center">
                                <input <?= $current['clockout_penalty'] == 1 ? 'checked':'' ?> type="radio" name="clockoutPenalty" value="1" class="form-radio text-blue-600">
                                <span class="ml-2 text-gray-700">Yes</span>
                            </label>
                            <label class="inline-flex items-center">
                                <input <?= $current['clockout_penalty'] == 0 ? 'checked':'' ?> type="radio" name="clockoutPenalty" value="0" class="form-radio text-blue-600">
                                <span class="ml-2 text-gray-700">No</span>
                            </label>
                        </div>
                    </div>

                    <div class="flex flex-col md:flex-row md:items-center">
                        <label class="block text-gray-600 font-medium md:w-1/3">
                            Alpha Penalty <span class="text-red-500">*</span>
                        </label>
                        <div class="mt-1 md:mt-0 md:w-2/3 flex items-center space-x-6">
                            <label class="inline-flex items-center">
                                <input <?= $current['alpha_penalty'] == 1 ? 'checked':'' ?> type="radio" name="alphaPenalty" value="1" class="form-radio text-blue-600">
                                <span class="ml-2 text-gray-700">Yes</span>
                            </label>
                            <label class="inline-flex items-center">
                                <input <?= $current['alpha_penalty'] == 0 ? 'checked':'' ?> type="radio" name="alphaPenalty" value="0" class="form-radio text-blue-600">
                                <span class="ml-2 text-gray-700">No</span>
                            </label>
                        </div>
                    </div>
                    
                    <div class="flex flex-col md:flex-row md:items-center">
                        <label class="block text-gray-600 font-medium md:w-1/3">
                            Alpha Penalty On Holiday Date <span class="text-red-500">*</span>
                        </label>
                        <div class="mt-1 md:mt-0 md:w-2/3 flex items-center space-x-6">
                            <label class="inline-flex items-center">
                                <input <?= $current['alpha_penalty_on_holiday_date'] == 1 ? 'checked':'' ?> type="radio" name="alphaPenaltyOnHolidayDate" value="1" class="form-radio text-blue-600">
                                <span class="ml-2 text-gray-700">Yes</span>
                            </label>
                            <label class="inline-flex items-center">
                                <input <?= $current['alpha_penalty_on_holiday_date'] == 0 ? 'checked':'' ?> type="radio" name="alphaPenaltyOnHolidayDate" value="0" class="form-radio text-blue-600">
                                <span class="ml-2 text-gray-700">No</span>
                            </label>
                        </div>
                    </div>

                    <div class="flex flex-col md:flex-row md:items-center">
                        <label class="block text-gray-600 font-medium md:w-1/3">
                            Alpha Consequence <span class="text-red-500">*</span>
                        </label>
                        <div class="mt-1 md:mt-0 md:w-2/3 flex items-center space-x-6">
                            <label class="inline-flex items-center">
                                <input <?= $current['alpha_consequence'] == '1' ? 'checked':'' ?> type="radio" name="alphaConsequence" value="1" class="form-radio text-blue-600">
                                <span class="ml-2 text-gray-700">Salary Deduction</span>
                            </label>
                            <label class="inline-flex items-center">
                                <input <?= $current['alpha_consequence'] == '2' ? 'checked':'' ?> type="radio" name="alphaConsequence" value="2" class="form-radio text-blue-600">
                                <span class="ml-2 text-gray-700">Offdays Deduction</span>
                            </label>
                        </div>
                    </div>

                    <div class="flex flex-col md:flex-row md:items-center">
                        <label class="block text-gray-600 font-medium md:w-1/3">
                            Alpha Penalty Type <span class="text-red-500">*</span>
                        </label>
                        <div class="mt-1 md:mt-0 md:w-2/3 flex items-center space-x-6">
                            <label class="inline-flex items-center">
                                <input <?= $current['alpha_penalty_type'] == "custom" ? 'checked':'' ?> type="radio" name="alphaPenaltyType" value="custom" class="form-radio text-blue-600">
                                <span class="ml-2 text-gray-700">Custom</span>
                            </label>
                            <label class="inline-flex items-center">
                                <input <?= $current['alpha_penalty_type'] == "percent" ? 'checked':'' ?> type="radio" name="alphaPenaltyType" value="percent" class="form-radio text-blue-600">
                                <span class="ml-2 text-gray-700">Percent</span>
                            </label>
                            <label class="inline-flex items-center">
                                <input <?= $current['alpha_penalty_type'] == "no" ? 'checked':'' ?> type="radio" name="alphaPenaltyType" value="no" class="form-radio text-blue-600">
                                <span class="ml-2 text-gray-700">No</span>
                            </label>
                        </div>
                    </div>

                    <div class="flex flex-col md:flex-row md:items-center">
                        <label class="block text-gray-600 font-medium md:w-1/3">
                            After Break Late Penalty Type <span class="text-red-500">*</span>
                        </label>
                        <div class="mt-1 md:mt-0 md:w-2/3 flex items-center space-x-6">
                            <label class="inline-flex items-center">
                                <input <?= $current['after_break_late_penalty_type'] == "fixed" ? 'checked':'' ?> type="radio" name="afterBreakLatePenaltyType" value="fixed" class="form-radio text-blue-600">
                                <span class="ml-2 text-gray-700">Fixed</span>
                            </label>
                            <label class="inline-flex items-center">
                                <input <?= $current['after_break_late_penalty_type'] == "minute" ? 'checked':'' ?> type="radio" name="afterBreakLatePenaltyType" value="minute" class="form-radio text-blue-600">
                                <span class="ml-2 text-gray-700">Minute</span>
                            </label>
                            <label class="inline-flex items-center">
                                <input <?= $current['after_break_late_penalty_type'] == "no" ? 'checked':'' ?> type="radio" name="afterBreakLatePenaltyType" value="no" class="form-radio text-blue-600">
                                <span class="ml-2 text-gray-700">No</span>
                            </label>
                        </div>
                    </div>

                    <div class="flex flex-col md:flex-row md:items-center">
                        <label class="block text-gray-600 font-medium md:w-1/3">
                            After Break Late Penalty <span class="text-red-500">*</span>
                        </label>
                        <div class="mt-1 md:mt-0 md:w-2/3 flex items-center space-x-6">
                            <label class="inline-flex items-center">
                                <input <?= $current['after_break_late_penalty'] == 1 ? 'checked':'' ?> type="radio" name="afterBreakLatePenalty" value="1" class="form-radio text-blue-600">
                                <span class="ml-2 text-gray-700">Yes</span>
                            </label>
                            <label class="inline-flex items-center">
                                <input <?= $current['after_break_late_penalty'] == 0 ? 'checked':'' ?> type="radio" name="afterBreakLatePenalty" value="0" class="form-radio text-blue-600">
                                <span class="ml-2 text-gray-700">No</span>
                            </label>
                        </div>
                    </div>

                    <div class="flex flex-col md:flex-row md:items-center">
                        <label class="block text-gray-600 font-medium md:w-1/3">
                            Work System <span class="text-red-500">*</span>
                        </label>
                        <div class="mt-1 md:mt-0 md:w-2/3 flex items-center space-x-6">
                            <label class="inline-flex items-center">
                                <input <?= explode('-',$current['work_system'])[0] == "wd" ? 'checked':'' ?> type="radio" onclick="showWeeklyList()" class="form-radio text-blue-600">
                                <span class="ml-2 text-gray-700">Work Day</span>
                            </label>
                            <label class="inline-flex items-center">
                                <input <?= explode('-',$current['work_system'])[0] == "s" ? 'checked':'' ?> type="radio" onclick="showShiftList()" class="form-radio text-blue-600">
                                <span class="ml-2 text-gray-700">Shift Day</span>
                            </label>
                        </div>
                    </div>

                    <div class="w-2/3 ml-auto hidden" id="weeklyList">
                        <label class="form-label" for="multicol-country">Pilih Jadwal Mingguan<i class="text-danger">*</i></label>
                        <select class="form-select" required="" onchange="test(this.value)">
                          <?php foreach ($weekly as $w): ?>
                            <option value="wd-<?= $w['pola_kerja_id'] ?>"><?= $w['nama_pola'] ?></option>
                          <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="flex flex-col md:flex-row md:items-center">
                        <label class="block text-gray-600 font-medium md:w-1/3">
                            FFO Check In Allowed <span class="text-red-500">*</span>
                        </label>
                        <div class="mt-1 md:mt-0 md:w-2/3 flex items-center space-x-6">
                            <label class="inline-flex items-center">
                                <input <?= $current['ffo_check_in_allowed'] == 1 ? 'checked':'' ?> type="radio" value="1" name="ffocia" class="form-radio text-blue-600">
                                <span class="ml-2 text-gray-700">yes</span>
                            </label>
                            <label class="inline-flex items-center">
                                <input <?= $current['ffo_check_in_allowed'] == 0 ? 'checked':'' ?> type="radio" value="0" name="ffocia" class="form-radio text-blue-600">
                                <span class="ml-2 text-gray-700">no</span>
                            </label>
                        </div>
                    </div>

                    <div class="w-2/3 ml-auto hidden" id="shiftList">
                        <label class="form-label" for="multicol-country">Pilih Shift<i class="text-danger">*</i></label>
                        <select class="form-select" required="" onchange="test(this.value)">
                          <?php foreach ($shift as $s): ?>
                            <option value="s-<?= $s['id'] ?>"><?= $s['name'] ?></option>
                          <?php endforeach; ?>
                        </select>
                    </div>

                </div>

                <!-- Footer button -->
                <div class="flex justify-end space-x-4 mt-8 pt-6 border-t border-gray-200">
                    <button class="px-6 py-2 rounded-md font-medium text-gray-700 border border-gray-300 hover:bg-gray-100 transition-colors duration-200">
                        Cancel
                    </button>
                    <button class="px-6 py-2 rounded-md font-medium text-white bg-blue-600 hover:bg-blue-700 transition-colors duration-200">
                        Save
                    </button>
                </div>
            </div>
        </form>
    </div>
  </div>
</div>

<script type="text/javascript">
    function showWeeklyList(){
      var weeklyList = document.getElementById("weeklyList")
      var shiftList = document.getElementById("shiftList")
      var pattern = document.getElementById("pattern")


      weeklyList.classList.remove("hidden")
      shiftList.classList.add("hidden")
      pattern.value = "wd-23"

    }

    function showShiftList(){
      var weeklyList = document.getElementById("weeklyList")
      var shiftList = document.getElementById("shiftList")
      var pattern = document.getElementById("pattern")
      
      weeklyList.classList.add("hidden")
      shiftList.classList.remove("hidden")
      pattern.value = "s-1756196909598"
    }

    function test(id){
      var pattern = document.getElementById("pattern")
      pattern.value = id;
    }


</script>