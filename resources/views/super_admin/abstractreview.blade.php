<!--
=========================================================
* Material Dashboard 2 - v3.1.0
=========================================================

* Product Page: https://www.creative-tim.com/product/material-dashboard
* Copyright 2023 Creative Tim (https://www.creative-tim.com)
* Licensed under MIT (https://www.creative-tim.com/license)

* Coded by Creative Tim

=========================================================

* The above copyright notice and this permission notice shall be included in all copies or substantial portions of the Software.
-->
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <meta name="csrf-token" content="{{ csrf_token() }}">

  <link rel="apple-touch-icon" sizes="76x76" href="../assets/img/apple-icon.png">
  <link rel="icon" type="image/png" href="../assets/img/favicon.png">
  <title>
    Profile
  </title>
  <!--     Fonts and icons     -->
  <link rel="stylesheet" type="text/css" href="https://fonts.googleapis.com/css?family=Roboto:300,400,500,700,900|Roboto+Slab:400,700" />
  <!-- Nucleo Icons -->
  <link href="../assets/css/nucleo-icons.css" rel="stylesheet" />
  <link href="../assets/css/nucleo-svg.css" rel="stylesheet" />
  <!-- Font Awesome Icons -->
  <script src="https://kit.fontawesome.com/42d5adcbca.js" crossorigin="anonymous"></script>
  <!-- Material Icons -->
  <link href="https://fonts.googleapis.com/icon?family=Material+Icons+Round" rel="stylesheet">
  <!-- CSS Files -->
  <link id="pagestyle" href="../assets/css/material-dashboard.css?v=3.1.0" rel="stylesheet" />
  <!-- Style.css file -->
  <link rel="stylesheet" href="../assets/css/style.css">
  <!-- Nepcha Analytics (nepcha.com) -->
  <!-- Nepcha is a easy-to-use web analytics. No cookies and fully compliant with GDPR, CCPA and PECR. -->
  <script defer data-site="YOUR_DOMAIN_HERE" src="https://api.nepcha.com/js/nepcha-analytics.js"></script>
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
  <!-- Bootstrap CSS -->
  <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">


</head>

<style>
    .abstract-edit-del-table-profile {
        margin: 30px 0px;
    }
    .abstract-edit-del-table-profile .tabl table{
        width: 100%;
    }
    .abstract-edit-del-table-profile .tabl table, td, th{
        border: 2px solid black;
        text-align: center;
    }
    .abstract-edit-del-table-profile .tabl table th{
        background-color: #090441;
        padding: 5px 10px;
        color: white;
        font-weight: 400;
        text-transform: uppercase;
    }
    .abstract-edit-del-table-profile .tabl table td{
        font-size: 14px;
        color: black;
        padding: 5px 10px;
    }
    .btn-download-abstracts{
      background-image: linear-gradient(195deg, #FFA726 0%, #FB8C00 100%);
      margin-top: 40px;
      margin-bottom: -30px;
      margin-right: 10px;
    }
    .floating-alert {
        position: fixed;
        top: 20px;
        right: 20px;
        z-index: 9999;
    }
</style>

<body class="g-sidenav-show bg-gray-200">
  <!-- <aside class="sidenav navbar navbar-vertical navbar-expand-xs border-0 border-radius-xl my-3 fixed-start ms-3   bg-gradient-dark" id="sidenav-main"> -->
  <aside class="sidenav navbar navbar-vertical navbar-expand-xs border-0 border-radius-xl my-3 fixed-start ms-3   bg-white" id="sidenav-main">
    <div class="sidenav-header">
      <i class="fas fa-times p-3 cursor-pointer text-dark opacity-5 position-absolute end-0 top-0 d-none d-xl-none" aria-hidden="true" id="iconSidenav"></i>
      <a class="navbar-brand m-0" href="#" target="">
        <span class="ms-1 font-weight-bold text-dark">{{$superAdminName}}</span>
        <p class="ms-1 text-dark" style="font-size: 12px;">Reviewer</p>
        <span class="ms-1 font-weight-bold text-dark"></span>
        <p class="ms-1 text-dark" style="font-size: 12px;"></p>
      </a>
    </div>
    <hr class="horizontal light mt-0 mb-2">
    <div class="collapse navbar-collapse  w-auto " id="sidenav-collapse-main">
      <ul class="navbar-nav">
        <!-- <li class="nav-item">
          <a class="nav-link text-dark" href="../pages/profile.html">
            <div class="text-dark text-center me-2 d-flex align-items-center justify-content-center">
              <i class="material-icons opacity-10">person</i>
            </div>
            <span class="nav-link-text ms-1">Profile</span>
          </a>
        </li> -->
        <li class="nav-item">
          <a class="nav-link text-dark" href="http://127.0.0.1:8000/super_admin/dashboard">
            <div class="text-dark text-center me-2 d-flex align-items-center justify-content-center">
              <i class="material-icons opacity-10">dashboard</i>
            </div>
            <span class="nav-link-text ms-1">Dashboard</span>
          </a>
        </li>
        <li class="nav-item">
          <a class="nav-link text-dark" href="http://127.0.0.1:8000/super_admin/userlist">
            <div class="text-dark text-center me-2 d-flex align-items-center justify-content-center">
              <i class="material-icons opacity-10">person</i>
            </div>
            <span class="nav-link-text ms-1">User List</span>
          </a>
        </li>
        <li class="nav-item">
          <a class="nav-link text-dark active bg-gradient-primary" href="http://127.0.0.1:8000/super_admin/abstractreview">
            <div class="text-dark text-center me-2 d-flex align-items-center justify-content-center">
              <i class="material-icons opacity-10">dashboard</i>
            </div>
            <span class="nav-link-text ms-1">Abstract Review</span>
          </a>
        </li>
        <!-- <li class="nav-item">
           <a class="nav-link text-dark " href="../pages/billing.html">
          <a class="nav-link text-dark " href="#">
            <div class="text-dark text-center me-2 d-flex align-items-center justify-content-center">
              <i class="material-icons opacity-10">table_view</i>
            </div>
            <span class="nav-link-text ms-1">Payments</span>
          </a>
        </li> -->
        <!-- <li class="nav-item">
          <a class="nav-link text-dark " href="#">
            <div class="text-dark text-center me-2 d-flex align-items-center justify-content-center">
              <i class="material-icons opacity-10">receipt_long</i>
            </div>
            <span class="nav-link-text ms-1">My Invoice</span>
          </a>
        </li> -->
        <li class="nav-item">
          <a class="nav-link text-dark " href="#">
            <div class="text-dark text-center me-2 d-flex align-items-center justify-content-center">
              <i class="material-icons opacity-10">logout</i>
            </div>
            <span class="nav-link-text ms-1">Logout</span>
          </a>
        </li>
        
      </ul>
    </div>
    
  </aside>
  <div class="main-content position-relative max-height-vh-100 h-100">
    <div class="container mt-4">
      <div class="internaltext">
        <h3 style="text-align: center;">
          IIM-ATM 2024 Bengaluru
        </h3>
        <div style="text-align: end; margin-inline: 20px;">
          <img style="width: 150px; text-align: end; margin-top: -70px; " src="../assets/img/logo.gif" alt="">
        </div>
      </div>
    </div>
    
    <!-- End Navbar -->
    <div class="container-fluid px-2 px-md-4">
      
      <div class="card card-body mt-4">
        <div class="row">
          <div class="row">
            <div class="col-12 col-xl-12">
              <div class="card card-plain h-100">
                <div class="card-body p-3">
                  <a href="{{ route('download.abstracts') }}" class="btn btn-primary btn-download-abstracts">Download All Abstracts</a>
                </div>
                
                <form action="{{ route('export.abstracts') }}" method="get">
                    <button type="submit">Export Abstracts</button>
                </form>

                  <button type="button" class="btn btn-primary download-btn-css" data-toggle="modal" data-target="#changePasswordModal">
                      Bulk Status Change
                  </button>
                  <!-- Modal -->
                  <div class="modal fade" id="changePasswordModal" tabindex="-1" role="dialog" aria-labelledby="changePasswordModalLabel" aria-hidden="true">
                      <div class="modal-dialog" role="document">
                          <div class="modal-content">
                              <div class="modal-header">
                                  <h5 class="modal-title" id="changePasswordModalLabel" style="font-size: 24px;text-transform: uppercase;font-weight: 700;">Change Password</h5>
                                  <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                      <span aria-hidden="true" style="border: 1px solid;padding: 0px  7px 3px 7px;">&times;</span>
                                  </button>
                              </div>
                              <div class="modal-body">
                                <form action="{{ route('import.abstracts') }}" method="post" enctype="multipart/form-data">
                                    @csrf
                                    <input type="file" name="excel_file">
                                    <button type="submit">Import Abstract Statuses</button>
                                </form>
                              </div>
                          </div>
                      </div>
                  </div>

              </div>
            </div>
          </div>
          <!-- abstract edit and delete -->
          <div class="abstract-edit-del-table-profile">
            <div class="tabl" style="padding: 0px 50px;">
                <table>
                    <tr>
                        <th>URN</th>
                        <th>Abstract ID</th>
                        <th>Name</th>
                        <th>Organization</th>
                        <th>Theme Selected</th>
                        <th>File Uploaded</th>
                        <th>Status Update</th>
                    </tr>
                    @if ($abstractUploads)
                    @foreach ($abstractUploads as $abstractUpload)
                    <tr>
                      <td>IIMATM2024_{{ $abstractUpload->user_id }}</td>
                      <td>{{ $abstractUpload->abstract_upload_id }}</td>
                      <td>{{ $abstractUpload->name }}</td>
                      <td>{{ $abstractUpload->organization_name }}</td>
                      <td>{{ $abstractUpload->theme }}</td>
                      <td>{{ $abstractUpload->file_path }}
                            <a href="{{ asset('storage/abstracts/' . basename($abstractUpload->file_path)) }}" target="_blank">
                                {{ basename($abstractUpload->file_path) }}
                            </a>
                      </td>
                      <td>
                        <select name="approval-sel-review" class="approval-sel-review" data-abstract-id="{{ $abstractUpload->id }}">
                            <option value="" disabled selected>Update Status</option>
                            <option value="Oral" {{ $abstractUpload->status === 'Oral' ? 'selected' : '' }}>Oral</option>
                            <option value="Poster" {{ $abstractUpload->status === 'Poster' ? 'selected' : '' }}>Poster</option>
                            <option value="Not Accepted" {{ $abstractUpload->status === 'Not Accepted' ? 'selected' : '' }}>Not Accepted</option>
                        </select>
                      </td>
                    </tr>
                    @endforeach
                    @else
                    <tr>
                      <td>Not Uploaded</td>
                      <td>Not Uploaded</td>
                      <td>Not Uploaded</td>
                    </tr>
                    @endif
                </table>
            </div>
          </div>
        </div>
      </div>
    </div>
    <footer class="footer py-4  ">
      <div class="container-fluid">
        <div class="row align-items-center justify-content-lg-between">
          <div class="col-lg-6 mb-lg-0 mb-4">
            <div class="copyright text-center text-sm text-muted text-lg-start ms-4">
              © IIM | <script>
                document.write(new Date().getFullYear())
              </script>,
              <!-- made with <i class="fa fa-heart"></i>  -->
              by
              <a href="#" class="font-weight-bold" target="_blank">JSW </a>
              <!-- for a better web. -->
            </div>
          </div>
          
        </div>
      </div>
    </footer>
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show floating-alert" role="alert" style="color:white;">
            {{ session('success') }}
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show floating-alert" role="alert" style="color:white;">
            {{ session('error') }}
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif
  </div>
  
  <!--   Core JS Files   -->
  <script src="{{asset('assets/js/core/popper.min.js')}}"></script>
  <script src="{{asset('assets/js/core/bootstrap.min.js')}}"></script>
  <script src="{{asset('assets/js/plugins/perfect-scrollbar.min.js')}}"></script>
  <script src="{{asset('assets/js/plugins/smooth-scrollbar.min.js')}}"></script>
  <script>
    var win = navigator.platform.indexOf('Win') > -1;
    if (win && document.querySelector('#sidenav-scrollbar')) {
      var options = {
        damping: '0.5'
      }
      Scrollbar.init(document.querySelector('#sidenav-scrollbar'), options);
    }
  </script>
  <!-- close the alert in 5sec -->
  <script>
      document.addEventListener('DOMContentLoaded', function() {
          setTimeout(function() {
              // Using plain JavaScript to hide the alert
              var alerts = document.querySelectorAll('.floating-alert');
              alerts.forEach(function(alert) {
                  alert.style.transition = "opacity 1s";
                  alert.style.opacity = "0";
                  setTimeout(function() {
                      alert.remove();
                  }, 1000); // Matches the transition duration
              });
          }, 5000); // 5 seconds
      });
  </script>
  <!-- Github buttons -->
  <script async defer src="https://buttons.github.io/buttons.js"></script>
  <!-- Control Center for Material Dashboard: parallax effects, scripts for the example pages etc -->
  <script src="../assets/js/material-dashboard.min.js?v=3.1.0"></script>
  <script>
    $(document).ready(function () {
        // Set up CSRF token for all AJAX requests
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        $('.approval-sel-review').change(function () {
            var abstractId = $(this).data('abstract-id');
            var status = $(this).val();

            // If no status is selected, default to "Pending"
            if (!status) {
                status = 'Pending';
            }

            // Send AJAX request to update status
            $.ajax({
                url: '/super_admin/update-abstract-status',
                method: 'POST',
                data: {
                    abstract_id: abstractId,
                    status: status
                },
                success: function (response) {
                    // Handle success response
                },
                error: function (xhr, status, error) {
                    // Handle error
                }
            });
        });
    });
</script>
<!-- Bootstrap JavaScript -->
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.bundle.min.js"></script>
<!-- jQuery -->
<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>

</body>

</html>