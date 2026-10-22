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

            <h5 class="mb-1 pt-2">Reset Username & Password 🔐</h5>
            <p class="mb-4 text-muted" style="font-size: 0.9rem;">
              Masukkan <strong>Username (Email)</strong> Anda, lalu klik <em>Request OTP</em> untuk mendapatkan kode verifikasi. Setelah itu isi OTP, (opsional) Username Baru, dan Password Baru.
            </p>

            <?=$this->session->flashdata('message');?>

            <form id="formResetPassword" class="mb-3" action="<?=base_url('reset/proccess/');?>" method="POST">

              <!-- Current Username -->
              <div class="mb-3">
                <label for="username" class="form-label">Username Saat Ini (Email)</label>
                <div class="input-group input-group-merge">
                  <span class="input-group-text"><i class="ti ti-mail"></i></span>
                  <input
                    type="email"
                    class="form-control"
                    id="username"
                    name="username"
                    autocomplete="off"
                    placeholder="Masukkan email Anda..."
                    required
                    autofocus />
                </div>
              </div>

              <!-- OTP -->
              <div class="mb-3">
                <label for="otp" class="form-label">Kode OTP</label>
                <div class="input-group input-group-merge">
                  <span class="input-group-text"><i class="ti ti-key"></i></span>
                  <input
                    type="text"
                    class="form-control"
                    id="otp"
                    name="otp"
                    autocomplete="off"
                    placeholder="Masukkan OTP yang dikirim ke email..."
                    required />
                </div>
              </div>
              
              <!-- New Username -->
              <div class="mb-3">
                <label for="new_username" class="form-label">Username Baru (Opsional)</label>
                <div class="input-group input-group-merge">
                  <span class="input-group-text"><i class="ti ti-mail-forward"></i></span>
                  <input
                    type="email"
                    class="form-control"
                    id="new_username"
                    name="new_username"
                    autocomplete="off"
                    placeholder="Kosongkan jika tidak ingin mengubah username..." />
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
                    placeholder="Masukkan password baru..." required />
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
                  Submit <i class="ti ti-check ms-1"></i>
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
      const username = document.getElementById('username').value.trim();
      if (!username) {
        alert('Harap isi Username Saat Ini (Email) terlebih dahulu.');
        document.getElementById('username').focus();
        return;
      }
      
      fetch('<?=base_url('reset/send_otp')?>', {
          method: 'POST',
          headers: {
              'Content-Type': 'application/x-www-form-urlencoded',
          },
          body: 'username=' + encodeURIComponent(username)
      })
      .then(response => response.json())
      .then(data => {
          if (data.status === 'success') {
              alert(data.message);
          } else {
              alert('Gagal: ' + data.message);
          }
      })
      .catch(error => {
          console.error('Error:', error);
          alert('Terjadi kesalahan saat mengirim permintaan OTP.');
      });
    }
  </script>
