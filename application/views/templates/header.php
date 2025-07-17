<!DOCTYPE html>
<?php 
  if (isset($htmlclasstemp)) {
    $htmlclasstemp = $htmlclasstemp;
  } else {
    $htmlclasstemp = 'layout-navbar-fixed layout-menu-fixed';
  }
?>
<html
  lang="en"
  class="light-style <?=$htmlclasstemp;?>"
  dir="ltr"
  data-theme="theme-default"
  data-assets-path="<?=base_url('assets/temp/');?>assets/"
  data-template="vertical-menu-template">
  <head>
    <meta charset="utf-8" />
    <meta
      name="viewport"
      content="width=device-width, initial-scale=1.0, user-scalable=no, minimum-scale=1.0, maximum-scale=1.0" />
    <meta http-equiv="Content-Security-Policy" content="upgrade-insecure-requests" />
    <title><?=$title;?></title>

    <meta name="author" content="https://carvellonic.com">
    <meta name="description" content="" />
    <link href="https://cdn.tailwindcss.com" rel="stylesheet">
    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="<?=base_url('assets/temp/');?>assets/logo/favicon.png" />

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link
      href="https://fonts.googleapis.com/css2?family=Public+Sans:ital,wght@0,300;0,400;0,500;0,600;0,700;1,300;1,400;1,500;1,600;1,700&display=swap"
      rel="stylesheet" />

    <!-- Icons -->
    <link rel="stylesheet" href="<?=base_url('assets/temp/');?>assets/vendor/fonts/fontawesome.css" />
    <link rel="stylesheet" href="<?=base_url('assets/temp/');?>assets/vendor/fonts/tabler-icons.css" />
    <link rel="stylesheet" href="<?=base_url('assets/temp/');?>assets/vendor/fonts/flag-icons.css" />

    <!-- Core CSS -->
    <link rel="stylesheet" href="<?=base_url('assets/temp/');?>assets/vendor/css/rtl/custom.css" />
    <link rel="stylesheet" href="<?=base_url('assets/temp/');?>assets/vendor/css/rtl/core.css" class="template-customizer-core-css" />
    <link rel="stylesheet" href="<?=base_url('assets/temp/');?>assets/vendor/css/rtl/theme-default.css" class="template-customizer-theme-css" />
    <link rel="stylesheet" href="<?=base_url('assets/temp/');?>assets/css/demo.css" />

    <!-- Vendors CSS -->
    <link rel="stylesheet" href="<?=base_url('assets/temp/');?>assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.css" />
    <link rel="stylesheet" href="<?=base_url('assets/temp/');?>assets/vendor/libs/node-waves/node-waves.css" />
    <link rel="stylesheet" href="<?=base_url('assets/temp/');?>assets/vendor/libs/typeahead-js/typeahead.css" />
    <link rel="stylesheet" href="<?=base_url('assets/temp/');?>assets/vendor/libs/apex-charts/apex-charts.css" />
    <link rel="stylesheet" href="<?=base_url('assets/temp/');?>assets/vendor/libs/swiper/swiper.css" />
    <link rel="stylesheet" href="<?=base_url('assets/temp/');?>assets/vendor/libs/datatables-bs5/datatables.bootstrap5.css" />
    <link rel="stylesheet" href="<?=base_url('assets/temp/');?>assets/vendor/libs/datatables-responsive-bs5/responsive.bootstrap5.css" />
    <link rel="stylesheet" href="<?=base_url('assets/temp/');?>assets/vendor/libs/datatables-checkboxes-jquery/datatables.checkboxes.css" />
    <link rel="stylesheet" href="<?=base_url('assets/temp/');?>assets/vendor/libs/datatables-buttons-bs5/buttons.bootstrap5.css" />
    <link rel="stylesheet" href="<?=base_url('assets/temp/');?>assets/vendor/libs/formvalidation/dist/css/formValidation.min.css" />
    <link rel="stylesheet" href="<?=base_url('assets/temp/');?>assets/vendor/libs/select2/select2.css" />
    <link rel="stylesheet" href="<?=base_url('assets/temp/');?>assets/vendor/libs/tagify/tagify.css" />
    <link rel="stylesheet" href="<?=base_url('assets/temp/');?>assets/vendor/libs/bootstrap-select/bootstrap-select.css" />
    <link rel="stylesheet" href="<?=base_url('assets/temp/');?>assets/vendor/libs/datatables-select-bs5/select.bootstrap5.css" />
    <link rel="stylesheet" href="<?=base_url('assets/temp/');?>assets/vendor/libs/datatables-fixedcolumns-bs5/fixedcolumns.bootstrap5.css" />
    <link rel="stylesheet" href="<?=base_url('assets/temp/');?>assets/vendor/libs/datatables-fixedheader-bs5/fixedheader.bootstrap5.css" />

    <link rel="stylesheet" href="<?=base_url('assets/temp/');?>assets/vendor/libs/flatpickr/flatpickr.css" />
    <link rel="stylesheet" href="<?=base_url('assets/temp/');?>assets/vendor/libs/bootstrap-datepicker/bootstrap-datepicker.css" />
    <link rel="stylesheet" href="<?=base_url('assets/temp/');?>assets/vendor/libs/bootstrap-daterangepicker/bootstrap-daterangepicker.css" />
    <link rel="stylesheet" href="<?=base_url('assets/temp/');?>assets/vendor/libs/jquery-timepicker/jquery-timepicker.css" />
    <link rel="stylesheet" href="<?=base_url('assets/temp/');?>assets/vendor/libs/pickr/pickr-themes.css" />

    <!-- Page CSS -->
    <link rel="stylesheet" href="<?=base_url('assets/temp/');?>assets/vendor/libs/fullcalendar/fullcalendar.css" />
    <link rel="stylesheet" href="<?=base_url('assets/temp/');?>assets/vendor/css/pages/app-calendar.css" />
    <link rel="stylesheet" href="<?=base_url('assets/temp/');?>assets/vendor/css/pages/page-auth.css" />
    <link rel="stylesheet" href="<?=base_url('assets/temp/');?>assets/vendor/css/pages/cards-advance.css" />
    <!-- Helpers -->
    <script src="<?=base_url('assets/temp/');?>assets/vendor/js/helpers.js"></script>

    <!--! Template customizer & Theme config files MUST be included after core stylesheets and helpers.js in the <head> section -->
    <!--? Template customizer: To hide customizer set displayCustomizer value false in config.js.  -->
    <!-- <script src="<?=base_url('assets/temp/');?>assets/vendor/js/template-customizer.js"></script> -->
    <!--? Config:  Mandatory theme config file contain global vars & default theme options, Set your preferred theme option in this file.  -->
    <script src="<?=base_url('assets/temp/');?>assets/vendor/libs/jquery/jquery.js"></script>
    <script src="<?=base_url('assets/temp/');?>assets/js/config.js"></script>

    <style type="text/css">
      .customselectinput .select2-selection.select2-selection--single{
        border-radius: 0.375rem 0px 0px 0.375rem !important;
      }
      .customselectinput .bootstrap-select.noperex {
        width: 75px !important;
      }
      .customselectinput button {
        height: 38px !important;
        line-height: 22px !important;
        border-radius: 0.375rem 0px 0px 0.375rem !important;
      }

      .text-overflow-ellips {
        text-overflow: ellipsis;  
        white-space: nowrap;
        overflow: hidden;
      }

      .inner-htmlinfo,
      .inner-html-det {
        width: 100% !important;
      }

      .inner-htmlinfo img,
      .inner-html-det img {
        border-radius: 5px !important;
        width: 100%;
      }

      .inner-htmlinfo,
      .inner-html-det {
        margin-bottom: 10px !important;
        color: #333;
      }

      .inner-htmlinfo {
        max-height: 100px;
        overflow: hidden;
      }

      .inner-htmlinfo p {
        margin-bottom: 10px !important;
      }

      .inner-html-det p,
      .inner-html-det ul,
      .inner-html-det ol {
        font-weight: 500;
        color: #333;
      }

      .inner-html-det h1,
      .inner-html-det h1 strong,
      .inner-html-det strong h1 {
        font-size: 22px;
        font-weight: 600;
      }

      .inner-html-det h2,
      .inner-html-det h2 strong,
      .inner-html-det strong h2 {
        font-size: 20px;
        font-weight: 600;
      }

      .inner-html-det h3,
      .inner-html-det h3 strong,
      .inner-html-det strong h3 {
        font-size: 18px;
        font-weight: 600;
      }

      .inner-html-det h4,
      .inner-html-det h5,
      .inner-html-det h6,
      .inner-html-det h4 strong,
      .inner-html-det h5 strong,
      .inner-html-det h6 strong,
      .inner-html-det strong h4,
      .inner-html-det strong h5,
      .inner-html-det strong h6 {
        font-size: 16px;
        font-weight: 600;
      }

      .inner-html-det p,
      .inner-html-det h1,
      .inner-html-det h2,
      .inner-html-det h3,
      .inner-html-det h4,
      .inner-html-det h5,
      .inner-html-det h6,
      .inner-html-det ul,
      .inner-html-det ol {  
        margin-bottom: 10px !important;
      }

      .inner-htmlinfo table {
        width: 100% !important;
        margin-top:5px !important;
        margin-bottom:5px !important;
      }

      .inner-html-det table {
        width: 100% !important;
        margin-top:5px !important;
        margin-bottom:10px !important;
      }

      .inner-htmlinfo td,
      .inner-htmlinfo th,
      .inner-htmlinfo tr,
      .inner-html-det td,
      .inner-html-det th,
      .inner-html-det tr {
        padding-right:5px !important;
        padding-left:5px !important;
      }

      .inner-htmlinfo ol,
      .inner-html-det ol {
        padding-left: 25px !important;
      }


      .inner-htmlinfo ol li ul,
      .inner-html-det ol li ul {
        padding-left: 15px !important;
      }

      .inner-htmlinfo table tr td,
      .inner-html-det table tr td {
        padding: 5px !important;
      }
    </style>
    
  </head>
