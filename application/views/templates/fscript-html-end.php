    <!-- Modal -->
    <div class="modal fade" id="optiondataModalAttendance" tabindex="-1" aria-hidden="true">
      <div class="modal-dialog modal-lg modal-dialog-centered modal-add-new-role">
        <div class="modal-content p-2 p-md-3" id="content_option_modal_att"></div>
      </div>
    </div>

    <!-- Global Photo Popup Modal -->
    <div class="modal fade" id="photoViewerModal" tabindex="-1" aria-hidden="true">
      <div class="modal-dialog modal-dialog-centered" style="max-width:90vw;width:fit-content;margin:auto;">
        <div class="modal-content" style="background:transparent;border:none;box-shadow:none;">
          <div class="modal-body p-0 text-center" style="background:rgba(0,0,0,0.85);border-radius:12px;position:relative;">
            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Tutup" style="position:absolute;top:12px;right:12px;z-index:2;background:rgba(0,0,0,0.5);border-radius:50%;padding:8px;opacity:1;"></button>
            <img id="photoViewerImg" src="" alt="Foto" style="max-width:90vw;max-height:85vh;width:auto;height:auto;border-radius:12px;display:block;margin:0 auto;" />
          </div>
        </div>
      </div>
    </div>

    <div class="modal fade" id="addNewLogin" tabindex="-1" aria-hidden="true">
      <div class="modal-dialog modal-dialog-centered1 modal-simple">
        <div class="modal-content p-3 p-md-5">
          <div class="modal-body">
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            <div class="text-center mb-4">
              <h3 class="mb-2">Form Login</h3>
              <p class="text-muted">Selamat datang, silakan masuk ke akun Anda dan mulai menggunakan Aplikasi.</p>
            </div>
            <form id="formAuthentication" class="mb-3" action="<?=base_url('auth');?>" method="POST">
              <div class="mb-3">
                <label for="email" class="form-label">Alamat Email</label>
                <input
                  type="email"
                  class="form-control"
                  id="email"
                  name="email"
                  autocomplete="off"
                  value="admin@gmail.com"
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
                  value="admin100"
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

    <!-- Core JS -->
    <!-- build:js assets/vendor/js/core.js -->
    <script src="<?=base_url('assets/temp/');?>assets/vendor/libs/popper/popper.js"></script>
    <script src="<?=base_url('assets/temp/');?>assets/vendor/js/bootstrap.js"></script>
    <script src="<?=base_url('assets/temp/');?>assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.js"></script>
    <script src="<?=base_url('assets/temp/');?>assets/vendor/libs/node-waves/node-waves.js"></script>

    <script src="<?=base_url('assets/temp/');?>assets/vendor/libs/hammer/hammer.js"></script>
    <script src="<?=base_url('assets/temp/');?>assets/vendor/libs/i18n/i18n.js"></script>
    <script src="<?=base_url('assets/temp/');?>assets/vendor/libs/typeahead-js/typeahead.js"></script>

    <script src="<?=base_url('assets/temp/');?>assets/vendor/js/menu.js"></script>
    <!-- endbuild -->

    <!-- Vendors JS -->
    <script src="<?=base_url('assets/temp/');?>assets/vendor/libs/apex-charts/apexcharts.js"></script>
    <script src="<?=base_url('assets/temp/');?>assets/vendor/libs/swiper/swiper.js"></script>
    <script src="<?=base_url('assets/temp/');?>assets/vendor/libs/datatables-bs5/datatables-bootstrap5.js"></script>
    <script src="<?=base_url('assets/temp/');?>assets/vendor/libs/formvalidation/dist/js/FormValidation.min.js"></script>
    <script src="<?=base_url('assets/temp/');?>assets/vendor/libs/formvalidation/dist/js/plugins/Bootstrap5.min.js"></script>
    <script src="<?=base_url('assets/temp/');?>assets/vendor/libs/formvalidation/dist/js/plugins/AutoFocus.min.js"></script>
    <script src="<?=base_url('assets/temp/');?>assets/vendor/libs/select2/select2.js"></script>
    <script src="<?=base_url('assets/temp/');?>assets/vendor/libs/tagify/tagify.js"></script>
    <script src="<?=base_url('assets/temp/');?>assets/vendor/libs/bootstrap-select/bootstrap-select.js"></script>
    <script src="<?=base_url('assets/temp/');?>assets/vendor/libs/bloodhound/bloodhound.js"></script>

    <!-- Main JS -->
    <script src="<?=base_url('assets/temp/');?>assets/js/main.js"></script>

    <script src="<?=base_url('assets/temp/');?>assets/js/forms-selects.js"></script>
    <script src="<?=base_url('assets/temp/');?>assets/js/forms-tagify.js"></script>
    <script src="<?=base_url('assets/temp/');?>assets/js/forms-typeahead.js"></script>

    <script src="<?=base_url('assets/temp/');?>assets/vendor/libs/moment/moment.js"></script>
    <script src="<?=base_url('assets/temp/');?>assets/vendor/libs/flatpickr/flatpickr.js"></script>
    <script src="<?=base_url('assets/temp/');?>assets/vendor/libs/bootstrap-datepicker/bootstrap-datepicker.js"></script>
    <script src="<?=base_url('assets/temp/');?>assets/vendor/libs/bootstrap-daterangepicker/bootstrap-daterangepicker.js"></script>
    <script src="<?=base_url('assets/temp/');?>assets/vendor/libs/jquery-timepicker/jquery-timepicker.js"></script>
    <script src="<?=base_url('assets/temp/');?>assets/vendor/libs/pickr/pickr.js"></script>
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>


    <!-- Page JS -->
    <?php if ($htmlpagejs=='auth' || $htmlpagejs=='signup') { ?>
    <script src="<?=base_url('assets/temp/');?>assets/js/pages-auth.js"></script>
    <?php } else if ($htmlpagejs=='dashboard') { ?>
    <script src="<?=base_url('assets/temp/');?>assets/js/dashboards-analytics.js"></script>
    <?php } else if ($htmlpagejs=='shift') { ?>
    <script src="<?=base_url('assets/temp/');?>assets/vendor/libs/fullcalendar-6.1.6/dist/index.global.min.js"></script>
    <script src="<?=base_url('assets/temp/');?>assets/vendor/libs/sweetalert2/sweetalert2x.js"></script>
    <!-- <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script> -->
    <?php } ?>
    <script src="<?=base_url('assets/temp/');?>assets/js/forms-pickers.js"></script>
    <script src="<?=base_url('assets/temp/');?>tinymce/tinymce/tinymce.min.js"></script>
    <script async src="https://www.instagram.com/embed.js"></script>

    <script>
        $(document).ready(function () {

          $('[data-toggle="tooltip"]').tooltip();
          $('[data-toggle="popover"]').popover();

          $('#dataTable').DataTable({
            ordering: false,
            fixedColumns:   true,
            scrollX:        true,
            scrollCollapse: true,
            "lengthMenu": [[15, 50, 100, -1], [15, 50, 100, "All"]]
          }); // ID From dataTable 

          $('#dataTableatt').DataTable({
            ordering: false,
            fixedColumns:   true,
            scrollX:        true,
            scrollCollapse: true,
            "lengthMenu": [[50, 75, 100, -1], [50, 75, 100, "All"]]
          }); // ID From dataTable 

          $('#dataTableatt2').DataTable({
            ordering: false,
            fixedColumns:   {
              left: 1,
              right: 1
            },
            scrollX:        true,
            scrollCollapse: true,
            "lengthMenu": [[50, 75, 100, -1], [50, 75, 100, "All"]]
          }); // ID From dataTable 

          setTimeout(() => {
            $('.dataTables_filter .form-control').removeClass('form-control-sm');
            $('.dataTables_length .form-select').removeClass('form-select-sm');
          }, 200);
          

          //text editor
          var BASE_URL = "<?php echo base_url() ?>assets/temp/tinymce/"; // use your own base url
          tinymce.init({
              selector: ".editoronly textarea",
              theme: "modern",
              // width: 680,
              height: 200,
              relative_urls: false,
              remove_script_host: false,
              // document_base_url: BASE_URL,
              plugins: [
                  "advlist autolink link image lists charmap preview hr anchor pagebreak spellchecker paste",
                  "searchreplace wordcount visualblocks visualchars code fullscreen insertdatetime media nonbreaking",
                  "table contextmenu directionality template textcolor responsivefilemanager template"
              ],
              toolbar: "insertfile undo redo | template styleselect | bold italic | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent pagebreak hr | link unlink image media | forecolor backcolor | preview code",
              templates: [
                {title: 'Default', description: 'Default', content: '{{default}}'},
              ],
              image_advtab: true,
              external_filemanager_path: BASE_URL + "filemanager/",
              filemanager_title: "Media",
              external_plugins: { "filemanager": BASE_URL + "filemanager/plugin.min.js" }
          });

        });

        function showImgfile(objFileInput, cid = 'targetfileimg', width = '120') {
          if (objFileInput.files[0]) {
            var fileReader = new FileReader();
            fileReader.onload = function (e) {
              $("#"+cid).html('<img src="'+e.target.result+'" width="'+width+'" class="rounded" />');
            }
            fileReader.readAsDataURL(objFileInput.files[0]);
          }
        }

        function stringMatch(term, candidate) {
          return candidate && candidate.toLowerCase().indexOf(term.toLowerCase()) >= 0;
        }

        function matchCustom(params, data) {
          // If there are no search terms, return all of the data
          if ($.trim(params.term) === '') {
              return data;
          }
          // Do not display the item if there is no 'text' property
          if (typeof data.text === 'undefined') {
              return null;
          }
          // Match text of option
          if (stringMatch(params.term, data.text)) {
              return data;
          }
          // Match attribute "data-foo" of option
          if (stringMatch(params.term, $(data.element).attr('data-foo'))) {
              return data;
          }
          // Return `null` if the term should not be displayed
          return null;
        }

        function formatCustom(state) {
          return $(
            '<div><div>' + state.text + '</div><div class="foo">'
                + $(state.element).attr('data-foo')
                + '</div></div>'
          );
        }

        function action_data_att(a,b,c){
          $('#optiondataModalAttendance').modal('toggle');
          $('#content_option_modal_att').html('Loading...');
          $.get('<?=base_url('attendance/action/');?>'+a+'/'+b+'/'+c, function(data) {
            $('#content_option_modal_att').html(data);
          });
        }

        function showPhotoPopup(url, title) {
          title = title || 'Foto';
          $('#photoViewerModalLabel').text(title);
          
          $('#photoViewerImg').off('error').on('error', function() {
            $(this).hide();
            if ($('#photoViewerError').length === 0) {
              $(this).after('<div id="photoViewerError" style="color:white; padding:20px; font-style:italic;">Gambar tidak tersedia atau URL tidak valid</div>');
            } else {
              $('#photoViewerError').show();
            }
          }).off('load').on('load', function() {
            $(this).show();
            if ($('#photoViewerError').length > 0) {
              $('#photoViewerError').hide();
            }
          });
          
          if (!url || url === '#' || url === '') {
            $('#photoViewerImg').trigger('error');
          } else {
            $('#photoViewerImg').attr('src', '').attr('src', url);
          }
          
          $('#photoViewerModal').modal('show');
        }
    </script>

    <!-- PWA: Service Worker Registration -->
    <script>
      (function () {
        if (!('serviceWorker' in navigator)) return;

        var swUrl = '<?=base_url('sw.js');?>';

        navigator.serviceWorker.register(swUrl, { scope: '<?=base_url();?>' })
          .then(function (reg) {
            console.log('[PWA] Service Worker registered, scope:', reg.scope);
          })
          .catch(function (err) {
            console.warn('[PWA] Service Worker registration failed:', err);
          });

        // ── Install Prompt ──────────────────────────────────────────
        var deferredPrompt = null;
        var installBanner  = null;

        window.addEventListener('beforeinstallprompt', function (e) {
          e.preventDefault();
          deferredPrompt = e;

          // Tampilkan banner install jika belum pernah ditolak
          if (localStorage.getItem('pwa_install_dismissed')) return;

          installBanner = document.createElement('div');
          installBanner.id = 'pwa-install-banner';
          installBanner.innerHTML = [
            '<style>',
            '#pwa-install-banner {',
            '  position: fixed; bottom: 20px; left: 50%; transform: translateX(-50%);',
            '  background: linear-gradient(135deg,#696cff,#9155fd);',
            '  color: #fff; border-radius: 14px; padding: 14px 20px;',
            '  display: flex; align-items: center; gap: 14px;',
            '  box-shadow: 0 8px 32px rgba(105,108,255,.45);',
            '  z-index: 99999; font-family: "Public Sans",system-ui,sans-serif;',
            '  font-size: 14px; max-width: 420px; width: calc(100% - 40px);',
            '  animation: pwaSlideUp .35s ease;',
            '}',
            '@keyframes pwaSlideUp {',
            '  from { opacity:0; transform:translateX(-50%) translateY(30px); }',
            '  to   { opacity:1; transform:translateX(-50%) translateY(0); }',
            '}',
            '#pwa-install-banner .pwa-icon { font-size:28px; flex-shrink:0; }',
            '#pwa-install-banner .pwa-text { flex:1; }',
            '#pwa-install-banner .pwa-text strong { display:block; font-size:15px; }',
            '#pwa-install-banner .pwa-text span { opacity:.85; }',
            '#pwa-install-banner .pwa-btn-install {',
            '  background:#fff; color:#696cff; border:none; border-radius:8px;',
            '  padding:8px 16px; font-size:13px; font-weight:700;',
            '  cursor:pointer; white-space:nowrap; flex-shrink:0;',
            '}',
            '#pwa-install-banner .pwa-btn-close {',
            '  background:rgba(255,255,255,.2); border:none; border-radius:50%;',
            '  width:28px; height:28px; cursor:pointer; color:#fff; font-size:16px;',
            '  display:flex; align-items:center; justify-content:center; flex-shrink:0;',
            '}',
            '</style>',
            '<div class="pwa-icon">📱</div>',
            '<div class="pwa-text">',
            '  <strong>Pasang LSS Absensi</strong>',
            '  <span>Akses lebih cepat dari layar utama</span>',
            '</div>',
            '<button class="pwa-btn-install" id="pwa-install-btn">Pasang</button>',
            '<button class="pwa-btn-close" id="pwa-dismiss-btn" title="Tutup">✕</button>',
          ].join('');

          document.body.appendChild(installBanner);

          document.getElementById('pwa-install-btn').addEventListener('click', function () {
            if (deferredPrompt) {
              deferredPrompt.prompt();
              deferredPrompt.userChoice.then(function (result) {
                if (result.outcome === 'accepted') {
                  localStorage.setItem('pwa_install_dismissed', '1');
                }
                deferredPrompt = null;
              });
            }
            if (installBanner) installBanner.remove();
          });

          document.getElementById('pwa-dismiss-btn').addEventListener('click', function () {
            localStorage.setItem('pwa_install_dismissed', '1');
            if (installBanner) installBanner.remove();
          });
        });

        window.addEventListener('appinstalled', function () {
          localStorage.setItem('pwa_install_dismissed', '1');
          if (installBanner) installBanner.remove();
          console.log('[PWA] App berhasil dipasang!');
        });
      })();
    </script>

  </body>
</html>