<!doctype html>
<html lang="en" class="light-theme">

<head>
  <link rel="icon" href="/assets/images/logo/logo-no-text-removebg-preview.png" type="image/x-icon" />
  <!-- Required meta tags -->
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">

  <!-- loader-->
  <link href="/assets/css/pace.min.css" rel="stylesheet" />
  <script src="/assets/js/pace.min.js"></script>

  <!--plugins-->
  <link href="/assets/plugins/simplebar/css/simplebar.css" rel="stylesheet" />
  <link href="/assets/plugins/perfect-scrollbar/css/perfect-scrollbar.css" rel="stylesheet" />
  <link href="/assets/plugins/datatable/css/dataTables.bootstrap5.min.css" rel="stylesheet" />
  <link href="/assets/plugins/select2/css/select2.min.css" rel="stylesheet" />
    <link href="/assets/plugins/select2/css/select2-bootstrap4.css" rel="stylesheet" />

  <!-- CSS Files -->
  <link href="/assets/css/bootstrap.min.css" rel="stylesheet">
  <link href="/assets/css/bootstrap-extended.css" rel="stylesheet">
  <link href="/assets/css/style.css" rel="stylesheet">
  <link href="/assets/css/icons.css" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500&display=swap" rel="stylesheet">

  <!--Theme Styles-->
  <link href="/assets/css/dark-theme.css" rel="stylesheet" />
  <link href="/assets/css/semi-dark.css" rel="stylesheet" />
  <link href="/assets/css/header-colors.css" rel="stylesheet" />

  {{-- bootstrap icons --}}
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.3/font/bootstrap-icons.css">

  <title>Dashboard</title>
</head>

<body>


  <!--start wrapper-->
  <div class="wrapper">

    <!--start sidebar -->
    @include('layouts.sidebar')
    <!--end sidebar -->

    <!--start top header-->
    @include('layouts.header')
    <!--end top header-->


    <!-- start page content wrapper-->
    <div class="page-content-wrapper">
      <!-- start page content-->
      <div class="page-content">
          @yield('container')
      </div>
      <!-- end page content-->
    </div>
    <!--end page content wrapper-->

    <!--Start Back To Top Button-->
    <a href="javaScript:;" class="back-to-top">
      <ion-icon name="arrow-up-outline"></ion-icon>
    </a>
    <!--End Back To Top Button-->

  </div>
  <!--end wrapper-->


  <!-- JS Files-->
  <script src="/assets/js/jquery.min.js"></script>
  <script src="/assets/plugins/simplebar/js/simplebar.min.js"></script>
  <script src="/assets/plugins/metismenu/js/metisMenu.min.js"></script>
  <script src="/assets/js/bootstrap.bundle.min.js"></script>
  <script type="module" src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.esm.js"></script>
  <!--plugins-->
  <script src="/assets/plugins/perfect-scrollbar/js/perfect-scrollbar.js"></script>
  <script src="/assets/plugins/datatable/js/jquery.dataTables.min.js"></script>
  <script src="/assets/plugins/datatable/js/dataTables.bootstrap5.min.js"></script>
  <script src="/assets/js/table-datatable.js"></script>
  <script src="/assets/plugins/select2/js/select2.min.js"></script>
  <script src="/assets/js/form-select2.js"></script>
  <!-- Main JS-->
  <script src="/assets/js/main.js"></script>
  <script type="text/javascript">
    var btnfs = document.getElementById("fullscreen-btn");
    btnfs.style.cursor = "pointer";
    btnfs.addEventListener("click", function() {
      if ((document.fullScreenElement && document.fullScreenElement !== null) ||
        (!document.mozFullScreen && !document.webkitIsFullScreen)) {
        if (document.documentElement.requestFullScreen) {
          btnfs.classList.remove('bx-fullscreen');
          btnfs.classList.add('bx-exit-fullscreen');
          document.documentElement.requestFullScreen();
        } else if (document.documentElement.mozRequestFullScreen) {
          btnfs.classList.remove('bx-fullscreen');
          btnfs.classList.add('bx-exit-fullscreen');
          document.documentElement.mozRequestFullScreen();
        } else if (document.documentElement.webkitRequestFullScreen) {
          btnfs.classList.remove('bx-fullscreen');
          btnfs.classList.add('bx-exit-fullscreen');
          document.documentElement.webkitRequestFullScreen(Element.ALLOW_KEYBOARD_INPUT);
        }
      } else {
        if (document.cancelFullScreen) {
          btnfs.classList.remove('bx-exit-fullscreen');
          btnfs.classList.add('bx-fullscreen');
          document.cancelFullScreen();
        } else if (document.mozCancelFullScreen) {
          btnfs.classList.remove('bx-exit-fullscreen');
          btnfs.classList.add('bx-fullscreen');
          document.mozCancelFullScreen();
        } else if (document.webkitCancelFullScreen) {
          btnfs.classList.remove('bx-exit-fullscreen');
          btnfs.classList.add('bx-fullscreen');
          document.webkitCancelFullScreen();
        }
      }
    });
  </script>
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  @yield('script')


</body>

</html>