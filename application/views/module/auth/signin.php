  <body>
    
    <!-- Content -->
    <div class="container-xxl">
      <div class="authentication-wrapper authentication-basic container-p-y">
        <div class="authentication-inner py-4">
          <!-- Login -->
          <div class="card">
            <div class="card-body" style="padding: 1rem 2rem;">
              <!-- aplikasi ini dibuat oleh chandra gustiya tim carvellonic, nomor 08567354414 -->
              <!-- Logo -->
              <div class="app-brand justify-content-center_ mb-3 mt-0">
                <a href="#" class="app-brand-link gap-2">
                  <img src="<?=base_url('assets/temp/assets/logo/client/logo.png');?>" width="90">
                </a>
              </div>
              <!-- /Logo -->
              <!--<h4 class="mb-1 pt-2">Attendance App! 👋</h4>-->
              <p class="mb-4">Selamat datang, silakan masuk ke akun Anda dan mulai menggunakan Aplikasi.</p>

              <?=$this->session->flashdata('message');?>

              <form id="formAuthentication" class="mb-3" action="<?=base_url('auth');?>" method="POST">
                <div class="mb-3">
                  <label for="email" class="form-label">Alamat Email</label>
                  <input
                    type="email"
                    class="form-control"
                    id="email"
                    name="email"
                    autocomplete="off"
                    value=""
                    placeholder="Masukan email anda..."
                    autofocus />
                </div>
                <div class="mb-3 form-password-toggle">
                  <div class="d-flex justify-content-between">
                    <label class="form-label" for="password">Password</label>
                  </div>
                  <input
                    type="password"
                    id="password"
                    class="form-control"
                    name="password"
                    value=""
                    placeholder="***************"
                    aria-describedby="password" />
                </div>
                <div class="mb-3">
                  <div class="form-check">
                    <input class="form-check-input" type="checkbox" id="remember-me" />
                    <label class="form-check-label" for="remember-me"> Remember Me </label>
                  </div>
                </div>
                <div class="mb-3">
                  <button class="btn btn-primary d-grid w-100" type="submit">Login</button>
                </div>
              </form>

            </div>
          </div>
        </div>
      </div>
    </div>
    <!-- / Content -->
