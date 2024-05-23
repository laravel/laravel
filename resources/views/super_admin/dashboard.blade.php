<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Super Admin Dashboard</title>
    <link href="../assets/css/nucleo-svg.css" rel="stylesheet">
    <link href="../assets/css/nucleo-icons.css" rel="stylesheet">
    <link rel="stylesheet" type="text/css" href="https://fonts.googleapis.com/css?family=Roboto:300,400,500,700,900|Roboto+Slab:400,700">
    <link id="pagestyle" href="../assets/css/material-dashboard.css?v=3.1.0" rel="stylesheet">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons+Round" rel="stylesheet">
    <script src="https://kit.fontawesome.com/42d5adcbca.js" crossorigin="anonymous"></script>
    <style media="all" id="fa-v4-font-face">/*!
 * Font Awesome Free 5.15.4 by @fontawesome - https://fontawesome.com
 * License - https://fontawesome.com/license/free (Icons: CC BY 4.0, Fonts: SIL OFL 1.1, Code: MIT License)
 */@font-face{font-family:"FontAwesome";font-display:block;src:url(https://ka-f.fontawesome.com/releases/v5.15.4/webfonts/free-fa-solid-900.eot);src:url(https://ka-f.fontawesome.com/releases/v5.15.4/webfonts/free-fa-solid-900.eot?#iefix) format("embedded-opentype"),url(https://ka-f.fontawesome.com/releases/v5.15.4/webfonts/free-fa-solid-900.woff2) format("woff2"),url(https://ka-f.fontawesome.com/releases/v5.15.4/webfonts/free-fa-solid-900.woff) format("woff"),url(https://ka-f.fontawesome.com/releases/v5.15.4/webfonts/free-fa-solid-900.ttf) format("truetype"),url(https://ka-f.fontawesome.com/releases/v5.15.4/webfonts/free-fa-solid-900.svg#fontawesome) format("svg")}@font-face{font-family:"FontAwesome";font-display:block;src:url(https://ka-f.fontawesome.com/releases/v5.15.4/webfonts/free-fa-brands-400.eot);src:url(https://ka-f.fontawesome.com/releases/v5.15.4/webfonts/free-fa-brands-400.eot?#iefix) format("embedded-opentype"),url(https://ka-f.fontawesome.com/releases/v5.15.4/webfonts/free-fa-brands-400.woff2) format("woff2"),url(https://ka-f.fontawesome.com/releases/v5.15.4/webfonts/free-fa-brands-400.woff) format("woff"),url(https://ka-f.fontawesome.com/releases/v5.15.4/webfonts/free-fa-brands-400.ttf) format("truetype"),url(https://ka-f.fontawesome.com/releases/v5.15.4/webfonts/free-fa-brands-400.svg#fontawesome) format("svg")}@font-face{font-family:"FontAwesome";font-display:block;src:url(https://ka-f.fontawesome.com/releases/v5.15.4/webfonts/free-fa-regular-400.eot);src:url(https://ka-f.fontawesome.com/releases/v5.15.4/webfonts/free-fa-regular-400.eot?#iefix) format("embedded-opentype"),url(https://ka-f.fontawesome.com/releases/v5.15.4/webfonts/free-fa-regular-400.woff2) format("woff2"),url(https://ka-f.fontawesome.com/releases/v5.15.4/webfonts/free-fa-regular-400.woff) format("woff"),url(https://ka-f.fontawesome.com/releases/v5.15.4/webfonts/free-fa-regular-400.ttf) format("truetype"),url(https://ka-f.fontawesome.com/releases/v5.15.4/webfonts/free-fa-regular-400.svg#fontawesome) format("svg");unicode-range:U+f004-f005,U+f007,U+f017,U+f022,U+f024,U+f02e,U+f03e,U+f044,U+f057-f059,U+f06e,U+f070,U+f075,U+f07b-f07c,U+f080,U+f086,U+f089,U+f094,U+f09d,U+f0a0,U+f0a4-f0a7,U+f0c5,U+f0c7-f0c8,U+f0e0,U+f0eb,U+f0f3,U+f0f8,U+f0fe,U+f111,U+f118-f11a,U+f11c,U+f133,U+f144,U+f146,U+f14a,U+f14d-f14e,U+f150-f152,U+f15b-f15c,U+f164-f165,U+f185-f186,U+f191-f192,U+f1ad,U+f1c1-f1c9,U+f1cd,U+f1d8,U+f1e3,U+f1ea,U+f1f6,U+f1f9,U+f20a,U+f247-f249,U+f24d,U+f254-f25b,U+f25d,U+f271-f274,U+f279,U+f28b,U+f28d,U+f2b5-f2b6,U+f2b9,U+f2bb,U+f2bd,U+f2c1-f2c2,U+f2d0,U+f2d2,U+f2dc,U+f2ed,U+f3a5,U+f3d1,U+f410}@font-face{font-family:"FontAwesome";font-display:block;src:url(https://ka-f.fontawesome.com/releases/v5.15.4/webfonts/free-fa-v4deprecations.eot);src:url(https://ka-f.fontawesome.com/releases/v5.15.4/webfonts/free-fa-v4deprecations.eot?#iefix) format("embedded-opentype"),url(https://ka-f.fontawesome.com/releases/v5.15.4/webfonts/free-fa-v4deprecations.woff2) format("woff2"),url(https://ka-f.fontawesome.com/releases/v5.15.4/webfonts/free-fa-v4deprecations.woff) format("woff"),url(https://ka-f.fontawesome.com/releases/v5.15.4/webfonts/free-fa-v4deprecations.ttf) format("truetype"),url(https://ka-f.fontawesome.com/releases/v5.15.4/webfonts/free-fa-v4deprecations.svg#fontawesome) format("svg");unicode-range:U+f003,U+f006,U+f014,U+f016,U+f01a-f01b,U+f01d,U+f040,U+f045-f047,U+f05c-f05d,U+f07d-f07e,U+f087-f088,U+f08a-f08b,U+f08e,U+f090,U+f096-f097,U+f0a2,U+f0e4-f0e6,U+f0ec-f0ee,U+f0f5-f0f7,U+f10c,U+f112,U+f114-f115,U+f11d,U+f123,U+f132,U+f145,U+f147-f149,U+f14c,U+f166,U+f16a,U+f172,U+f175-f178,U+f18e,U+f190,U+f196,U+f1b1,U+f1d9,U+f1db,U+f1f7,U+f20c,U+f219,U+f230,U+f24a,U+f250,U+f278,U+f27b,U+f283,U+f28c,U+f28e,U+f29b-f29c,U+f2b7,U+f2ba,U+f2bc,U+f2be,U+f2c0,U+f2c3,U+f2d3-f2d4}</style>
 
</head>
<style>
    .super_admin_dashboard .card-version-1{
        width: 30%;
        display: inline-block;
        margin-right: 10px;
        text-align: center;
        /* background: linear-gradient(135deg, rgb(174 174 174 / 80%), rgb(156 156 156 / 80%), rgb(69 69 69 / 80%)); */
    }
    .super_admin_dashboard .card-version-1 .count-non-super .count{
        font-size: 50px;
        color: black;
        font-weight: 600;
    }
    .super_admin_dashboard .card-version-1 .count-non-super .count-head{
        font-size: 18px;
        color: black;
        font-weight: 400;
        text-transform: uppercase;
        letter-spacing:  0.0525rem;
    }
    .super_admin_dashboard .card-version-1 .row .row{
        margin: auto;
    }
    .themewise-cstm-head{
      font-size: 30px;
      font-weight: 500;
      text-transform: uppercase;
      text-align: center;
      margin-top: 50px;
      color: black;
    }
    .theme-wise-cstm-row.super_admin_dashboard .card-version-1 .count-non-super .count-head{
      font-size: 16px;
      color: black;
      font-weight: 400;
      text-transform: uppercase;
      letter-spacing: 0.02rem;
    }
    .theme-wise-cstm-row.super_admin_dashboard .card-version-1{
      min-height: 200px;
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
        
        <li class="nav-item">
          <a class="nav-link text-dark active bg-gradient-primary" href="http://127.0.0.1:8000/super_admin/dashboard">
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
          <a class="nav-link text-dark" href="http://127.0.0.1:8000/super_admin/abstractreview">
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
    <div class="container-fluid px-2 px-md-4 super_admin_dashboard">
      
      <!-- <div class="card card-body mt-4">
        <div class="row">
          <div class="row">
            <div class="col-12 col-xl-12">
              <div class="card card-plain h-100">
                <div class="card-body p-3">
                </div>
              </div>
            </div>
          </div>
        </div>
      </div> -->
      <div class="card card-body mt-4 card-version-1">
        <div class="row">
          <div class="row">
            <div class="col-12 col-xl-12">
              <div class="card card-plain h-100">
                <div class="card-body p-3">
                    <div class="count-non-super">
                        <div class="count">{{ $nonSuperAdminCount }}</div>
                        <div class="count-head">Users Registered</div>
                    </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
      <div class="card card-body mt-4 card-version-1">
        <div class="row">
          <div class="row">
            <div class="col-12 col-xl-12">
              <div class="card card-plain h-100">
                <div class="card-body p-3">
                    <div class="count-non-super">
                        <div class="count">{{ $totalAbstractCount }}</div>
                        <div class="count-head">Total Submissions</div>
                    </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
      <!-- @foreach ($themeCounts as $theme => $count)
          <div class="card card-body mt-4 card-version-1">
              <div class="row">
                  <div class="col-12 col-xl-12">
                      <div class="card card-plain h-100">
                          <div class="card-body p-3">
                              <div class="count-non-super">
                                  <div class="count">{{ $count }}</div>
                                  <div class="count-head">{{ $theme }} - Abstracts Submitted</div>
                              </div>
                          </div>
                      </div>
                  </div>
              </div>
          </div>
      @endforeach -->
      
    </div>
    <div class="theme-wise-cstm-row container-fluid px-2 px-md-4 super_admin_dashboard">
      <div class="themewise-cstm-head">
        Abstracts Submitted Theme Wise
      </div>
      <div class="card card-body mt-4 card-version-1">
        <div class="row">
          <div class="row">
            <div class="col-12 col-xl-12">
              <div class="card card-plain h-100">
                <div class="card-body p-3">
                    <div class="count-non-super">
                        <div class="count">{{ isset($themeCounts['MPR']) ? $themeCounts['MPR'] : 0 }}</div>
                        <div class="count-head">Mineral Processing</div>
                    </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div><!--one-->
      <div class="card card-body mt-4 card-version-1">
        <div class="row">
          <div class="row">
            <div class="col-12 col-xl-12">
              <div class="card card-plain h-100">
                <div class="card-body p-3">
                    <div class="count-non-super">
                        <div class="count">{{ isset($themeCounts['ISM']) ? $themeCounts['ISM'] : 0 }}</div>
                        <div class="count-head">Iron Making and Steel Making</div>
                    </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div><!--two-->
      <div class="card card-body mt-4 card-version-1">
        <div class="row">
          <div class="row">
            <div class="col-12 col-xl-12">
              <div class="card card-plain h-100">
                <div class="card-body p-3">
                    <div class="count-non-super">
                        <div class="count">{{ isset($themeCounts['NFM']) ? $themeCounts['NFM'] : 0 }}</div>
                        <div class="count-head">Non-ferrous Metal Processing</div>
                    </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div><!--three-->
      <div class="card card-body mt-4 card-version-1">
        <div class="row">
          <div class="row">
            <div class="col-12 col-xl-12">
              <div class="card card-plain h-100">
                <div class="card-body p-3">
                    <div class="count-non-super">
                        <div class="count">{{ isset($themeCounts['SCA']) ? $themeCounts['SCA'] : 0 }}</div>
                        <div class="count-head">Solidification and Casting</div>
                    </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div><!--four-->
      <div class="card card-body mt-4 card-version-1">
        <div class="row">
          <div class="row">
            <div class="col-12 col-xl-12">
              <div class="card card-plain h-100">
                <div class="card-body p-3">
                    <div class="count-non-super">
                        <div class="count">{{ isset($themeCounts['MJN']) ? $themeCounts['MJN'] : 0 }}</div>
                        <div class="count-head">Metal Joining</div>
                    </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div><!--five-->
      <div class="card card-body mt-4 card-version-1">
        <div class="row">
          <div class="row">
            <div class="col-12 col-xl-12">
              <div class="card card-plain h-100">
                <div class="card-body p-3">
                    <div class="count-non-super">
                        <div class="count">{{ isset($themeCounts['PMA']) ? $themeCounts['PMA'] : 0 }}</div>
                        <div class="count-head">Powder Metallurgy and Additive Manufacturing</div>
                    </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div><!--six-->
      <div class="card card-body mt-4 card-version-1">
        <div class="row">
          <div class="row">
            <div class="col-12 col-xl-12">
              <div class="card card-plain h-100">
                <div class="card-body p-3">
                    <div class="count-non-super">
                        <div class="count">{{ isset($themeCounts['BSF']) ? $themeCounts['BSF'] : 0 }}</div>
                        <div class="count-head">Bio-Materials</div>
                    </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div><!--seven-->
      <div class="card card-body mt-4 card-version-1">
        <div class="row">
          <div class="row">
            <div class="col-12 col-xl-12">
              <div class="card card-plain h-100">
                <div class="card-body p-3">
                    <div class="count-non-super">
                        <div class="count">{{ isset($themeCounts['CMS']) ? $themeCounts['CMS'] : 0 }}</div>
                        <div class="count-head">Integrated Computational Materials Engineering</div>
                    </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div><!--eight-->
      <div class="card card-body mt-4 card-version-1">
        <div class="row">
          <div class="row">
            <div class="col-12 col-xl-12">
              <div class="card card-plain h-100">
                <div class="card-body p-3">
                    <div class="count-non-super">
                        <div class="count">{{ isset($themeCounts['DIN']) ? $themeCounts['DIN'] : 0 }}</div>
                        <div class="count-head">Digitalization and Industry 4.0</div>
                    </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div><!--nine-->
      <div class="card card-body mt-4 card-version-1">
        <div class="row">
          <div class="row">
            <div class="col-12 col-xl-12">
              <div class="card card-plain h-100">
                <div class="card-body p-3">
                    <div class="count-non-super">
                        <div class="count">{{ isset($themeCounts['SPC']) ? $themeCounts['SPC'] : 0 }}</div>
                        <div class="count-head">Structure Property Correlation</div>
                    </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div><!--ten-->
      <div class="card card-body mt-4 card-version-1">
        <div class="row">
          <div class="row">
            <div class="col-12 col-xl-12">
              <div class="card card-plain h-100">
                <div class="card-body p-3">
                    <div class="count-non-super">
                        <div class="count">{{ isset($themeCounts['FAN']) ? $themeCounts['FAN'] : 0 }}</div>
                        <div class="count-head">Failure Analysis</div>
                    </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div><!--eleven-->
      <div class="card card-body mt-4 card-version-1">
        <div class="row">
          <div class="row">
            <div class="col-12 col-xl-12">
              <div class="card card-plain h-100">
                <div class="card-body p-3">
                    <div class="count-non-super">
                        <div class="count">{{ isset($themeCounts['ESU']) ? $themeCounts['ESU'] : 0 }}</div>
                        <div class="count-head">Environment and Sustainability</div>
                    </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div><!--twelve-->
      <div class="card card-body mt-4 card-version-1">
        <div class="row">
          <div class="row">
            <div class="col-12 col-xl-12">
              <div class="card card-plain h-100">
                <div class="card-body p-3">
                    <div class="count-non-super">
                        <div class="count">{{ isset($themeCounts['RCC']) ? $themeCounts['RCC'] : 0 }}</div>
                        <div class="count-head">Refractories, Ceramics and Composites</div>
                    </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div><!--thirteen-->
      <div class="card card-body mt-4 card-version-1">
        <div class="row">
          <div class="row">
            <div class="col-12 col-xl-12">
              <div class="card card-plain h-100">
                <div class="card-body p-3">
                    <div class="count-non-super">
                        <div class="count">{{ isset($themeCounts['CEB']) ? $themeCounts['CEB'] : 0 }}</div>
                        <div class="count-head">Corrosion, Electrochemistry, Batteries and Fuel Cells</div>
                    </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div><!--fourteen-->
      <div class="card card-body mt-4 card-version-1">
        <div class="row">
          <div class="row">
            <div class="col-12 col-xl-12">
              <div class="card card-plain h-100">
                <div class="card-body p-3">
                    <div class="count-non-super">
                        <div class="count">{{ isset($themeCounts['AME']) ? $themeCounts['AME'] : 0 }}</div>
                        <div class="count-head">Archaeo-metallurgy</div>
                    </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div><!--fifteen-->
      <div class="card card-body mt-4 card-version-1">
        <div class="row">
          <div class="row">
            <div class="col-12 col-xl-12">
              <div class="card card-plain h-100">
                <div class="card-body p-3">
                    <div class="count-non-super">
                        <div class="count">{{ isset($themeCounts['MFR']) ? $themeCounts['MFR'] : 0 }}</div>
                        <div class="count-head">Metal Forming</div>
                    </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div><!--sixteen-->
      <div class="card card-body mt-4 card-version-1">
        <div class="row">
          <div class="row">
            <div class="col-12 col-xl-12">
              <div class="card card-plain h-100">
                <div class="card-body p-3">
                    <div class="count-non-super">
                        <div class="count">{{ isset($themeCounts['MSD']) ? $themeCounts['MSD'] : 0 }}</div>
                        <div class="count-head">Materials for Strategic Sectors</div>
                    </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div><!--seventeen-->
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
</body>
</html>