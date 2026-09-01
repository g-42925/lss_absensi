<body>
  <!-- Content -->
  <div class="container-xxl">
    <div class="authentication-wrapper authentication-basic container-p-y">
      <div class="authentication-inner py-4">

        <!-- Reset Password Card -->
        <div class="card">
          <div class="card-body" style="padding: 1rem 2rem;">

            <!-- Logo -->
            <div class="app-brand justify-content-center mb-3 mt-0 d-flex align-items-center">
              <a href="#" class="app-brand-link gap-2 d-flex align-items-center">
                <img src="<?=base_url('assets/temp/assets/logo/client/logo_4.png');?>" width="90">
                <span style="font-size: 28px; font-weight: bold; color: #4A4A4A; letter-spacing: 1px;">
                  Leryn Absensi
                </span>
              </a>
            </div>
            <!-- /Logo -->

            <h5 class="mb-1 pt-2">Reset Password 🔐</h5>
            <p class="mb-4 text-muted" style="font-size: 0.9rem;">
              Masukkan <strong>Secret Key</strong> dan <strong>Password Baru</strong> Anda, lalu klik <em>Request OTP</em> untuk mendapatkan kode verifikasi.
            </p>

            <?=$this->session->flashdata('message');?>

            <form id="formResetPassword" class="mb-3" action="<?=base_url('reset/proccess/');?>" method="POST">

              <!-- Secret Key -->
              <div class="mb-3">
                <label for="secret_key" class="form-label">Secret Key</label>
                <div class="input-group input-group-merge">
                  <span class="input-group-text"><i class="ti ti-key"></i></span>
                  <input
                    type="text"
                    class="form-control"
                    id="secret_key"
                    name="secret_key"
                    autocomplete="off"
                    placeholder="Masukkan secret key Anda..."
                    autofocus />
                </div>
              </div>

              <div class="mb-4">
                <label for="new_password" class="form-label">Password Baru</label>
                <div class="input-group input-group-merge">
                  <span class="input-group-text"><i class="ti ti-lock"></i></span>
                  <input
                    type="password"
                    class="form-control"
                    id="new_password"
                    name="new_password"
                    placeholder="Masukkan password baru..." />
                  <span class="input-group-text" style="cursor:pointer;" onclick="togglePassword()">
                    <i class="ti ti-eye" id="toggleIcon"></i>
                  </span>
                </div>
              </div>
              
              <!-- New Password -->
              <div class="mb-4">
                <label for="new_password" class="form-label">Password Baru</label>
                <div class="input-group input-group-merge">
                  <span class="input-group-text"><i class="ti ti-lock"></i></span>
                  <input
                    type="password"
                    class="form-control"
                    id="new_password"
                    name="new_password"
                    placeholder="Masukkan password baru..." />
                  <span class="input-group-text" style="cursor:pointer;" onclick="togglePassword()">
                    <i class="ti ti-eye" id="toggleIcon"></i>
                  </span>
                </div>
              </div>

              <!-- Buttons -->
              <div class="d-flex gap-2 mb-3">
                <button
                  type="button"
                  id="btnRequestOtp"
                  class="btn btn-outline-primary w-50"
                  onclick="requestOtp()">
                  <i class="ti ti-mail-forward me-1"></i> Request OTP
                </button>
                <button
                  type="submit"
                  id="btnNext"
                  class="btn btn-primary w-50">
                  Next <i class="ti ti-arrow-right ms-1"></i>
                </button>
              </div>

              <div class="text-center">
                <a href="<?=base_url('auth');?>" class="text-muted" style="font-size: 0.85rem;">
                  <i class="ti ti-arrow-left me-1"></i> Kembali ke Login
                </a>
              </div>

            </form>

          </div>
        </div>
        <!-- /Reset Password Card -->

      </div>
    </div>
  </div>
  <!-- / Content -->

  <script>
    function togglePassword() {
      const input = document.getElementById('new_password');
      const icon  = document.getElementById('toggleIcon');
      if (input.type === 'password') {
        input.type = 'text';
        icon.classList.replace('ti-eye', 'ti-eye-off');
      } else {
        input.type = 'password';
        icon.classList.replace('ti-eye-off', 'ti-eye');
      }
    }

    function requestOtp() {
      const secretKey = document.getElementById('secret_key').value.trim();
      if (!secretKey) {
        alert('Harap isi Secret Key terlebih dahulu.');
        document.getElementById('secret_key').focus();
        return;
      }
      // TODO: implementasi AJAX request OTP ke endpoint yang sesuai
      alert('Permintaan OTP telah dikirim ke email terdaftar.');
    }
  </script>
