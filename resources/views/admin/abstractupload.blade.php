
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
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
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
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
</style>

<body class="g-sidenav-show bg-gray-200">
  <!-- <aside class="sidenav navbar navbar-vertical navbar-expand-xs border-0 border-radius-xl my-3 fixed-start ms-3   bg-gradient-dark" id="sidenav-main"> -->
  <aside class="sidenav navbar navbar-vertical navbar-expand-xs border-0 border-radius-xl my-3 fixed-start ms-3   bg-white" id="sidenav-main">
    <div class="sidenav-header">
      <i class="fas fa-times p-3 cursor-pointer text-dark opacity-5 position-absolute end-0 top-0 d-none d-xl-none" aria-hidden="true" id="iconSidenav"></i>
      <a class="navbar-brand m-0" href="#" target="">
        <!-- <img src="../assets/img/logo-ct.png" class="navbar-brand-img h-100" alt="main_logo"> -->
        <!-- <span class="ms-1 font-weight-bold text-white">Material Dashboard 2</span> -->
        <span class="ms-1 font-weight-bold text-dark">{{ $profile->name }}</span>
        <p class="ms-1 text-dark" style="font-size: 12px;">{{ $profile->designation }}</p>
        <span class="ms-1 font-weight-bold text-dark"></span>
        <p class="ms-1 text-dark" style="font-size: 12px;"></p>
      </a>
    </div>
    <hr class="horizontal light mt-0 mb-2">
    <div class="collapse navbar-collapse  w-auto " id="sidenav-collapse-main">
      <ul class="navbar-nav">
        <li class="nav-item">
          <a class="nav-link text-dark" href="http://127.0.0.1:8000/profile">
            <div class="text-dark text-center me-2 d-flex align-items-center justify-content-center">
              <i class="material-icons opacity-10">person</i>
            </div>
            <span class="nav-link-text ms-1">Profile</span>
          </a>
        </li>
        <li class="nav-item">
          <a class="nav-link text-dark active bg-gradient-primary" href="http://127.0.0.1:8000/admin/abstract">
            <div class="text-dark text-center me-2 d-flex align-items-center justify-content-center">
              <i class="material-icons opacity-10">dashboard</i>
            </div>
            <span class="nav-link-text ms-1">Abstract</span>
          </a>
        </li>
        <li class="nav-item">
          <!-- <a class="nav-link text-dark " href="../pages/billing.html"> -->
          <a class="nav-link text-dark " href="#">
            <div class="text-dark text-center me-2 d-flex align-items-center justify-content-center">
              <i class="material-icons opacity-10">table_view</i>
            </div>
            <span class="nav-link-text ms-1">Payments</span>
          </a>
        </li>
        <li class="nav-item">
          <a class="nav-link text-dark " href="#">
            <div class="text-dark text-center me-2 d-flex align-items-center justify-content-center">
              <i class="material-icons opacity-10">receipt_long</i>
            </div>
            <span class="nav-link-text ms-1">My Invoice</span>
          </a>
        </li>
        <li class="nav-item">
          <a class="nav-link text-dark " href="#">
            <div class="text-dark text-center me-2 d-flex align-items-center justify-content-center">
              <i class="material-icons opacity-10">logout</i>
            </div>
            <span class="nav-link-text ms-1">Logout</span>
          </a>
        </li>
        <!-- <li class="nav-item">
          <a class="nav-link text-dark " href="../pages/sign-up.html">
            <div class="text-dark text-center me-2 d-flex align-items-center justify-content-center">
              <i class="material-icons opacity-10">assignment</i>
            </div>
            <span class="nav-link-text ms-1">Sign Up</span>
          </a>
        </li> -->
      </ul>
    </div>
    <!-- <div class="sidenav-footer position-absolute w-100 bottom-0 ">
      <div class="mx-3">
        <a class="btn btn-outline-primary mt-4 w-100" href="https://www.creative-tim.com/learning-lab/bootstrap/overview/material-dashboard?ref=sidebarfree" type="button">Documentation</a>
        <a class="btn bg-gradient-primary w-100" href="https://www.creative-tim.com/product/material-dashboard-pro?ref=sidebarfree" type="button">Upgrade to pro</a>
      </div>
    </div> -->
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
      <!-- <div class="page-header min-height-300 border-radius-xl mt-4" style="background-image: url('https://images.unsplash.com/photo-1531512073830-ba890ca4eba2?ixid=MnwxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8&ixlib=rb-1.2.1&auto=format&fit=crop&w=1920&q=80');">
        <span class="mask  bg-gradient-primary  opacity-6"></span>
      </div> -->
      <!-- <div class="card card-body mx-3 mx-md-4 mt-4"> -->
      <div class="card card-body mt-4">
        <!-- <div class="col-auto">
            <div class="avatar avatar-my position-relative">
              <img src="../assets/img/profile-demo.jpg" alt="profile_image" class="w-100 border-radius-lg shadow-sm">
            </div>
          </div>
          <div class="col-auto my-auto">
            <div class="h-100">
              <h5 class="mb-1">
              {{$profile->name}}
              </h5>
              <p class="mb-0 font-weight-normal text-sm">
              {{$profile->designation}}
              </p>
              <ul class="list-group mt-4">
                    <li style="list-style: none; margin-block: 3px;" class=" border-0 ps-0 text-sm"><strong class="text-dark">Mobile Number:</strong> &nbsp; {{$profile->phone}}</li>
                    <li style="list-style: none; margin-block: 3px;" class=" border-0 ps-0 text-sm"><strong class="text-dark">Official Mail:</strong> &nbsp; {{$profile->email}}</li>
                    <li style="list-style: none; margin-block: 3px;" class=" border-0 ps-0 text-sm"><strong class="text-dark">Organization:</strong> &nbsp; {{$profile->organization_name}}</li>
                  </ul>
            </div>
        </div> -->
        
        <div class="row">
          <div class="row">
            
            <div class="col-12 col-xl-12">
              <div class="card card-plain h-100">
                
                <div class="card-body p-3">
                  
                </div>
              </div>
            </div>
            
          </div>
          <div class="banner" style="margin-bottom:20px;">
            <div class="border-radius-xl">
              
              <!-- abstract upload start -->
              <div class="abst-upload-main" style="width: 95%;display: inline-block;text-align: center;">
                <h3 class="" style="text-transform:uppercase;margin-bottom:-5px;">
                    Upload Abstract
                </h3>
                <div class="abst-upload-form" style="padding: 10px 30px 20px 30px;border: 0px solid #e7e7e7;display: inline-block;border-radius: 10px;margin-top: 20px;box-shadow: 0px 0px 7px 2px #f0f0f0;text-align:left;">
                    <form action="{{ route('abstract-upload.store') }}" method="post" enctype="multipart/form-data">
                        @csrf
                        <!-- <div style="font-size: 16px;text-transform: uppercase;color: black;font-weight: 500;">
                            <label style="text-transform: uppercase;font-weight: 600;color: black;margin-right: 10px;font-size: 18px;margin-left: 0;display: block;margin-bottom: 5px;margin-top: 20px;">Select Type:</label>
                            <div style="cursor:pointer"><input type="radio" name="type" value="oral" id="oraltype" required><label for="oraltype" style="font-size: 16px;text-transform: uppercase;color: black;font-weight: 500;cursor:pointer;">Oral</label></div>
                            <div style="cursor:pointer"><input type="radio" name="type" value="poster" required id="postertype"><label for="postertype"  style="font-size: 16px;text-transform: uppercase;color: black;font-weight: 500;cursor:pointer;">Poster</label></div>
                        </div> -->
                        <div>
                        <label style="text-transform: uppercase;font-weight: 600;color: black;margin-right: 10px;font-size: 18px;margin-left: 0;display: block;margin-bottom: 5px;margin-top: 20px;">Select Theme:</label>
                            <select name="theme" required style="padding: 8px 20px;border: 0px;box-shadow: 0px 0px 5px 1px #e1e1e1;border-radius: 6px;">
                                <option value="MPR">Mineral Processing - Ferrous, Non-ferrous and Coal</option>
                                <option value="ISM">Iron Making and Steel making</option>
                                <option value="NFM">Non-ferrous Metal Processing</option>
                                <option value="SCA">Solidification and Casting</option>
                                <option value="MFR">Metal Forming - Hot Rolling, Cold Rolling, Forging and Drawing</option>
                                <option value="MJN">Metal Joining - Ferrous and Non-ferrous</option>
                                <option value="PMA">Powder Metallurgy and Additive Manufacturing</option>
                                <option value="BSF">Bio-Materials, Smart Materials and Functional Materials</option>
                                <option value="CMS">Integrated Computational Materials Engineering (ICME), Modeling and Simulation</option>
                                <option value="DIN">Digitalization and Industry 4.0</option>
                                <option value="SPC">Structure Property Correlation</option>
                                <option value="FAN">Failure Analysis</option>
                                <option value="ESU">Environment and Sustainability</option>
                                <option value="RCC">Refractories, Ceramics and Composites</option>
                                <option value="MSD">Materials for Strategic Sectors - Defence, Nuclear and Aerospace</option>
                                <option value="CEB">Corrosion, Electrochemistry, Batteries and Fuel Cells</option>
                                <option value="AME">Archaeo-metallurgy</option>
                                <!-- Add more options IF Needed -->
                            </select>
                        </div>
                        <div>
                        <label style="text-transform: uppercase;font-weight: 600;color: black;margin-right: 10px;font-size: 18px;margin-left: 0;display: block;margin-bottom: 5px;margin-top: 20px;">Upload Your Abstract File:</label>
                            <input type="file" name="file" required style="box-shadow: 0px 0px 5px 1px #e1e1e1;padding: 8px;border-radius: 5px;">
                        </div>
                        <button type="submit" style="background-image: linear-gradient(195deg, #FFA726 0%, #FB8C00 100%);border: none;padding: 8px 20px;margin-top: 20px;border-radius: 5px;color: white;text-transform: uppercase;">Submit</button>
                    </form>
                    @if(session('error'))
                        <div class="alert alert-danger" style="background-image: linear-gradient(195deg, #FFA726 0%, #FB8C00 100%);color:white;margin-top:20px;">
                            {{ session('error') }}
                        </div>
                    @endif
                </div>
                
              </div>
              <!-- Right hand side process image -->
              <!-- <div class="abst-right-process-image" style="width: 48%;display: inline-block;text-align: center;vertical-align:top;">
                    <div class="img-header">
                    <h3 class="" style="text-transform:uppercase;">
                        How Abstract Submission Works
                    </h3></div>
                    <div class="imag"><img src="{{asset('assets/img/demo-work-img.png')}}" alt="" width="50%" height="100%"></div>
              </div> -->
            </div>
          </div>
          <!-- abstract edit and delete -->
          <div class="abstract-edit-del-table-profile">
            <div class="tabl" style="padding: 0px 50px;">
                <table>
                    <tr>
                        <th>Abstract ID</th>
                        <th>Theme Selected</th>
                        <th>File Uploaded</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                    @if ($abstracts)
                    @foreach ($abstracts as $abstract)
                    <tr>
                      <td>{{ $abstract->abstract_upload_id }}</td>
                      <td>{{ $abstract->theme }}</td>
                      <td>{{ $abstract->file_path }}
                            <a href="#" class="file-preview-link" data-file-url="{{ Storage::disk('abstracts')->url($abstract->file_path) }}">
                                {{ basename($abstract->file_path) }}
                            </a>
                            <!-- <a href="{{ asset('storage/abstracts/' . basename($abstract->file_path)) }}" target="_blank">
                                {{ basename($abstract->file_path) }}
                            </a> -->
                      </td>
                      <td>{{ $abstract->status }}</td>
                      <td>
                        <form id="delete-form-{{ $abstract->id }}" action="{{ route('abstract-upload.destroy', $abstract->id) }}" method="POST">
                            @csrf
                            @method('DELETE')
                            <button type="button" onclick="confirmDelete({{ $abstract->id }})" style="border: none;background: transparent;"><img src="{{asset('assets/img/bin.png')}}" alt="delete" width="20px" height="20px"></button>
                        </form>
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
                <!-- Modal for file preview -->
                <div class="modal fade" id="filePreviewModal" tabindex="-1" role="dialog" aria-labelledby="filePreviewModalLabel" aria-hidden="true">
                    <div class="modal-dialog modal-lg" role="document">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="filePreviewModalLabel">File Preview</h5>
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                            <div class="modal-body">
                                <iframe id="filePreviewIframe" src="" width="100%" height="600px"></iframe>
                            </div>
                        </div>
                    </div>
                </div>
                <!--Modal Ends-->
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
  </div>
  
  <!--   Core JS Files   -->
  <script src="../assets/js/core/popper.min.js"></script>
  <script src="../assets/js/core/bootstrap.min.js"></script>
  <script src="../assets/js/plugins/perfect-scrollbar.min.js"></script>
  <script src="../assets/js/plugins/smooth-scrollbar.min.js"></script>
  <script>
    var win = navigator.platform.indexOf('Win') > -1;
    if (win && document.querySelector('#sidenav-scrollbar')) {
      var options = {
        damping: '0.5'
      }
      Scrollbar.init(document.querySelector('#sidenav-scrollbar'), options);
    }
  </script>
  <!-- Github buttons -->
  <script async defer src="https://buttons.github.io/buttons.js"></script>
  <!-- Control Center for Material Dashboard: parallax effects, scripts for the example pages etc -->
  <script src="../assets/js/material-dashboard.min.js?v=3.1.0"></script>
  <script>
      function confirmDelete(id) {
          if (confirm("Are you sure you want to delete this abstract?")) {
              document.getElementById('delete-form-' + id).submit();
          }
      }
  </script>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        var fileLinks = document.querySelectorAll('.file-preview-link');
        var modal = document.getElementById('filePreviewModal');
        var iframe = document.getElementById('filePreviewIframe');

        fileLinks.forEach(function(link) {
            link.addEventListener('click', function(e) {
                e.preventDefault();
                var fileUrl = this.getAttribute('data-file-url');
                iframe.src = fileUrl;
                $('#filePreviewModal').modal('show');
            });
        });

        $('#filePreviewModal').on('hidden.bs.modal', function () {
            iframe.src = "";
        });
    });
</script>

<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.3/dist/umd/popper.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>


</body>

</html>