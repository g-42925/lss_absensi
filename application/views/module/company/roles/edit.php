<!-- Content -->
<div class="container-xxl flex-grow-1 container-p-y">

  <div class="role-page">

    <!-- Page Header -->
    <div class="page-header">

      <div class="page-kicker">
        ROLE MANAGEMENT
      </div>

      <h4 class="page-title">
        Edit Jabatan
      </h4>

      <p class="page-description">
        Perbarui informasi jabatan dan hak akses yang dimilikinya.
      </p>

    </div>


    <form
      action="<?= base_url('company/roles/edit_proses/' . $edit['role_id']); ?>"
      method="POST"
    >

      <?= $this->session->flashdata('message'); ?>


      <!-- Informasi Jabatan -->
      <section class="content-section">

        <div class="section-heading">

          <div>
            <h6 class="section-title">
              Informasi Jabatan
            </h6>

            <p class="section-description">
              Informasi dasar mengenai jabatan ini.
            </p>
          </div>

        </div>


        <div class="information-panel">

          <div class="row g-4">

            <div class="col-lg-8">

              <label class="modern-label">
                Nama Jabatan
              </label>

              <input
                type="text"
                class="form-control modern-input"
                name="nama"
                value="<?= $edit['nama_role']; ?>"
                placeholder="Contoh: Manager"
                required
              >

              <div class="input-hint">
                Gunakan nama yang mudah dikenali oleh pengguna.
              </div>

            </div>


            <div class="col-lg-4">

              <label class="modern-label">
                Status
              </label>

              <select
                class="select2 form-select modern-input"
                name="status"
                required
              >

                <option
                  value="y"
                  <?= $edit['is_status'] == 'y' ? 'selected' : ''; ?>
                >
                  Aktif
                </option>

                <option
                  value="n"
                  <?= $edit['is_status'] == 'n' ? 'selected' : ''; ?>
                >
                  Tidak Aktif
                </option>

              </select>

            </div>

          </div>

        </div>

      </section>


      <!-- Hak Akses -->
      <section class="content-section">

        <div class="section-heading permission-heading">

          <div>
            <h6 class="section-title">
              Hak Akses
            </h6>

            <p class="section-description">
              Tentukan fitur yang dapat digunakan oleh jabatan ini.
            </p>
          </div>


          <!-- Global Select All -->
          <button
            type="button"
            class="select-all-button"
            id="select-all-permissions"
          >
            <i class="bx bx-check-square"></i>

            <span>
              Pilih semua
            </span>
          </button>

        </div>


        <div class="permission-grid">

          <?php foreach ($actions as $act): ?>

            <?php
              $directory  = $act[0]['directory'] ?? '';
              $group_name = $directory ?: 'Umum';
            ?>


            <!-- Permission Group -->
            <div class="permission-group">

              <div class="group-header">

                <div class="group-title">

                  <span class="group-name">
                    <?= $group_name; ?>
                  </span>

                  <span class="group-count">
                    <?= count($act); ?>
                  </span>

                </div>


                <!-- Section Select All -->
                <button
                  type="button"
                  class="group-select-all"
                >
                  <i class="bx bx-check-square"></i>

                  <span>
                    Pilih semua
                  </span>
                </button>

              </div>


              <!-- Permission List -->
              <div class="permission-list">

                <?php foreach ($act as $x): ?>

                  <?php
                    $slug = $x['directory']
                      ? $x['directory'] . '/' . $x['class'] . '/' . $x['method']
                      : $x['class'] . '/' . $x['method'];

                    $id = 'role-' . md5($slug);

                    $checked = in_array($slug, $slugs);
                  ?>


                  <label
                    class="permission-item"
                    for="<?= $id; ?>"
                  >

                    <input
                      type="checkbox"
                      class="permission-checkbox"
                      name="roles[]"
                      value="<?= $slug; ?>"
                      id="<?= $id; ?>"
                      <?= $checked ? 'checked' : ''; ?>
                    >


                    <span class="permission-indicator">
                      <i class="bx bx-check"></i>
                    </span>


                    <span class="permission-text">
                      <?= $x['description']; ?>
                    </span>

                  </label>

                <?php endforeach; ?>

              </div>

            </div>

          <?php endforeach; ?>

        </div>

      </section>


      <!-- Actions -->
      <div class="form-actions">

        <a
          href="javascript:window.history.back();"
          class="cancel-button"
        >
          Batal
        </a>

        <button
          type="submit"
          class="save-button"
        >
          <i class="bx bx-check"></i>
          Simpan Perubahan
        </button>

      </div>

    </form>

  </div>

</div>
<!-- / Content -->


<style>

  /* =========================================================
     PAGE
  ========================================================= */

  .role-page {
    max-width: 1450px;
    margin: 0 auto;
  }


  /* =========================================================
     PAGE HEADER
  ========================================================= */

  .page-header {
    margin-bottom: 2rem;
  }

  .page-kicker {
    margin-bottom: .35rem;
    font-size: .65rem;
    font-weight: 600;
    letter-spacing: .1em;
    color: #696cff;
  }

  .page-title {
    margin: 0 0 .35rem;
    font-size: 1.5rem;
    font-weight: 600;
    letter-spacing: -.02em;
    color: #2b2c40;
  }

  .page-description {
    margin: 0;
    font-size: .825rem;
    color: #8592a3;
  }


  /* =========================================================
     SECTION
  ========================================================= */

  .content-section {
    margin-bottom: 2.5rem;
  }

  .section-heading {
    margin-bottom: 1rem;
  }

  .permission-heading {
    display: flex;
    align-items: flex-end;
    justify-content: space-between;
    gap: 1rem;
  }

  .section-title {
    margin: 0 0 .25rem;
    font-size: .9rem;
    font-weight: 600;
    color: #2b2c40;
  }

  .section-description {
    margin: 0;
    font-size: .75rem;
    color: #a1acb8;
  }


  /* =========================================================
     INFORMATION
  ========================================================= */

  .information-panel {
    padding: 1.35rem;
    border: 1px solid #ebecef;
    border-radius: 10px;
    background: #fff;
  }

  .modern-label {
    display: block;
    margin-bottom: .5rem;
    font-size: .75rem;
    font-weight: 600;
    color: #566a7f;
  }

  .modern-input {
    min-height: 43px;
    border: 1px solid #e1e3e6;
    border-radius: 7px;
    background: #fff;
    font-size: .825rem;
  }

  .modern-input:focus {
    border-color: #696cff;
    box-shadow: 0 0 0 3px rgba(105, 108, 255, .07);
  }

  .input-hint {
    margin-top: .4rem;
    font-size: .68rem;
    color: #a1acb8;
  }


  /* =========================================================
     SELECT ALL
  ========================================================= */

  .select-all-button,
  .group-select-all {
    display: inline-flex;
    align-items: center;
    gap: .35rem;
    cursor: pointer;
    font-size: .7rem;
    font-weight: 500;
    transition:
      background .15s ease,
      border-color .15s ease,
      color .15s ease;
  }

  .select-all-button {
    padding: .4rem .65rem;
    border: 1px solid #e3e4ea;
    border-radius: 6px;
    background: #fff;
    color: #697a8d;
  }

  .select-all-button:hover {
    border-color: #696cff;
    background: #f7f7ff;
    color: #696cff;
  }

  .select-all-button i {
    font-size: .85rem;
  }

  .group-select-all {
    flex-shrink: 0;
    padding: .25rem .4rem;
    border: 0;
    border-radius: 5px;
    background: transparent;
    color: #8a94a6;
    font-size: .64rem;
  }

  .group-select-all:hover {
    background: #f4f4ff;
    color: #696cff;
  }

  .group-select-all i {
    font-size: .78rem;
  }


  /* =========================================================
     PERMISSION GRID
  ========================================================= */

  .permission-grid {
    column-count: 4;
    column-gap: 2.25rem;
  }

  .permission-group {
    display: inline-block;
    width: 100%;
    margin-bottom: 2rem;
    vertical-align: top;
    break-inside: avoid;
    page-break-inside: avoid;
  }


  /* =========================================================
     GROUP HEADER
  ========================================================= */

  .group-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: .75rem;
    padding-bottom: .55rem;
    margin-bottom: .3rem;
    border-bottom: 1px solid #eceef1;
  }

  .group-title {
    display: flex;
    align-items: center;
    gap: .45rem;
    min-width: 0;
  }

  .group-name {
    font-size: .72rem;
    font-weight: 600;
    color: #566a7f;
  }

  .group-count {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    min-width: 19px;
    height: 19px;
    padding: 0 .3rem;
    border-radius: 5px;
    background: #f4f5f7;
    font-size: .6rem;
    font-weight: 600;
    color: #8592a3;
  }


  /* =========================================================
     PERMISSION LIST
  ========================================================= */

  .permission-list {
    display: flex;
    flex-direction: column;
    gap: 1px;
  }

  .permission-item {
    position: relative;
    display: flex;
    align-items: center;
    gap: .6rem;
    padding: .5rem .55rem;
    margin: 0;
    border-radius: 6px;
    cursor: pointer;
    transition: background .12s ease;
  }

  .permission-item:hover {
    background: #f7f7f9;
  }

  .permission-text {
    font-size: .78rem;
    line-height: 1.35;
    color: #566a7f;
  }


  /* =========================================================
     CHECKBOX
  ========================================================= */

  .permission-checkbox {
    position: absolute;
    opacity: 0;
    pointer-events: none;
  }

  .permission-indicator {
    display: flex;
    align-items: center;
    justify-content: center;
    width: 17px;
    height: 17px;
    flex: 0 0 17px;
    border: 1.5px solid #d1d4da;
    border-radius: 4px;
    background: #fff;
    color: transparent;
    transition:
      border-color .12s ease,
      background .12s ease,
      color .12s ease;
  }

  .permission-indicator i {
    font-size: .68rem;
  }

  .permission-checkbox:checked + .permission-indicator {
    border-color: #696cff;
    background: #696cff;
    color: #fff;
  }

  .permission-checkbox:checked ~ .permission-text {
    font-weight: 500;
    color: #5558c9;
  }

  .permission-item:has(.permission-checkbox:checked) {
    background: #f8f8ff;
  }


  /* =========================================================
     FORM ACTIONS
  ========================================================= */

  .form-actions {
    display: flex;
    align-items: center;
    justify-content: flex-end;
    gap: .5rem;
    padding-top: 1.25rem;
    border-top: 1px solid #eceef1;
  }

  .cancel-button {
    padding: .6rem .9rem;
    border-radius: 7px;
    color: #8592a3;
    font-size: .78rem;
    font-weight: 500;
    text-decoration: none;
    transition:
      background .15s ease,
      color .15s ease;
  }

  .cancel-button:hover {
    background: #f5f5f7;
    color: #566a7f;
  }

  .save-button {
    display: inline-flex;
    align-items: center;
    gap: .35rem;
    padding: .6rem .9rem;
    border: 0;
    border-radius: 7px;
    background: #696cff;
    color: #fff;
    font-size: .78rem;
    font-weight: 500;
    cursor: pointer;
    transition:
      background .15s ease,
      box-shadow .15s ease;
  }

  .save-button:hover {
    background: #5f61e6;
    box-shadow: 0 4px 10px rgba(105, 108, 255, .18);
  }


  /* =========================================================
     RESPONSIVE
  ========================================================= */

  @media (max-width: 1199.98px) {

    .permission-grid {
      column-count: 3;
    }

  }

  @media (max-width: 991.98px) {

    .permission-grid {
      column-count: 2;
    }

  }

  @media (max-width: 575.98px) {

    .page-title {
      font-size: 1.35rem;
    }

    .information-panel {
      padding: 1rem;
    }

    .permission-grid {
      column-count: 1;
    }

    .permission-heading {
      align-items: flex-start;
    }

    .form-actions {
      padding-top: 1rem;
    }

  }

</style>


<script>

  const allCheckboxes = [
    ...document.querySelectorAll('.permission-checkbox')
  ];

  const globalButton =
    document.querySelector('#select-all-permissions');

  const groupButtons =
    document.querySelectorAll('.group-select-all');


  /*
   * Ambil checkbox dalam group.
   */
  const getGroupCheckboxes = button => {

    const group =
      button.closest('.permission-group');

    return [
      ...group.querySelectorAll('.permission-checkbox')
    ];

  };


  /*
   * Update label tombol.
   */
  const updateButton = (button, items) => {

    const selected =
      items.filter(item => item.checked).length;

    const allSelected =
      items.length > 0 &&
      selected === items.length;


    button.querySelector('span').textContent =
      allSelected
        ? 'Batal semua'
        : 'Pilih semua';

  };


  /*
   * Update tombol global.
   */
  const updateGlobalButton = () => {

    updateButton(
      globalButton,
      allCheckboxes
    );

  };


  /*
   * Global pilih semua.
   */
  globalButton.addEventListener('click', () => {

    const shouldSelect =
      allCheckboxes.some(
        checkbox => !checkbox.checked
      );


    allCheckboxes.forEach(checkbox => {
      checkbox.checked = shouldSelect;
    });


    groupButtons.forEach(button => {

      updateButton(
        button,
        getGroupCheckboxes(button)
      );

    });


    updateGlobalButton();

  });


  /*
   * Pilih semua per group.
   */
  groupButtons.forEach(button => {

    button.addEventListener('click', () => {

      const items =
        getGroupCheckboxes(button);


      const shouldSelect =
        items.some(
          checkbox => !checkbox.checked
        );


      items.forEach(checkbox => {
        checkbox.checked = shouldSelect;
      });


      updateButton(
        button,
        items
      );


      updateGlobalButton();

    });

  });


  /*
   * Checkbox manual.
   */
  allCheckboxes.forEach(checkbox => {

    checkbox.addEventListener('change', () => {

      updateGlobalButton();


      groupButtons.forEach(button => {

        updateButton(
          button,
          getGroupCheckboxes(button)
        );

      });

    });

  });


  /*
   * Kondisi awal.
   */
  updateGlobalButton();


  groupButtons.forEach(button => {

    updateButton(
      button,
      getGroupCheckboxes(button)
    );

  });

</script>