    <!-- Modal -->
    <div class="modal fade" id="optiondataModalAttendance" tabindex="-1" aria-hidden="true">
      <div class="modal-dialog modal-lg modal-dialog-centered modal-add-new-role">
        <div class="modal-content p-2 p-md-3" id="content_option_modal_att"></div>
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
    </script>
  
  </body>
</html>