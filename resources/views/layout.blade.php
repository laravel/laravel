<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="author" content="">

    <title>Admin: A Hotel</title>

    @if(!Session::has('adminData'))
        <script type="text/javascript">
            window.location.href="{{url('admin/login')}}";
        </script>
    @endif

    <!-- Custom fonts for this template-->
    <link href="{{asset('public')}}/vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">
    <link
        href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i"
        rel="stylesheet">

    <!-- Custom styles for this template-->
    <link href="{{asset('public')}}/css/sb-admin-2.min.css" rel="stylesheet">

</head>

<body id="page-top">

    <!-- Page Wrapper -->
    <div id="wrapper">

        <!-- Sidebar -->
        <ul class="navbar-nav bg-gradient-primary sidebar sidebar-dark accordion" id="accordionSidebar">

            <!-- Sidebar - Brand -->
            <a class="sidebar-brand d-flex align-items-center justify-content-center" href="index.html">
                <div class="sidebar-brand-icon">
                    <i class="fas fa-hotel"></i>
                </div>
                <div class="sidebar-brand-text mx-3">The Grand Ark Hotel</div>
            </a>

            <!-- Divider -->
            <hr class="sidebar-divider my-0">

            <!-- Nav Item - Dashboard -->
            <li class="nav-item">
                <a class="nav-link" href="{{url('dashboard')}}">
                    <i class="fas fa-fw fa-tachometer-alt"></i>
                    <span>Dashboard</span></a>
            </li>

            <!-- Divider -->
            <hr class="sidebar-divider">

            <!-- Heading -->
            <div class="sidebar-heading">
                Masters
            </div>

            <li class="nav-item">
                <a class="nav-link @if(!request()->is('admin/banner*')) collapsed @endif" href="#" data-toggle="collapse" data-target="#bannerSection"
                    aria-expanded="true" aria-controls="collapseTwo">
                    <i class="fas fa-fw fa-images"></i>
                    <span>HomePage Banners</span>
                </a>
                <div id="bannerSection" class="collapse @if(request()->is('admin/banner*')) show @endif" aria-labelledby="headingTwo" data-parent="#accordionSidebar">
                    <div class="bg-white py-2 collapse-inner rounded">
                        <a class="collapse-item" href="{{url('admin/banner/create')}}">Add New</a>
                        <a class="collapse-item" href="{{url('admin/banner')}}">View All</a>
                    </div>
                </div>
            </li>

            <!-- Nav Item - Pages Collapse Menu -->
            <li class="nav-item">
                <a class="nav-link @if(!request()->is('admin/roomtype*')) collapsed @endif" href="#" data-toggle="collapse" data-target="#collapseTwo"
                    aria-expanded="true" aria-controls="collapseTwo">
                    <i class="fas fa-fw fa-table"></i>
                    <span>RoomType</span>
                </a>
                <div id="collapseTwo" class="collapse @if(request()->is('admin/roomtype*')) show @endif" aria-labelledby="headingTwo" data-parent="#accordionSidebar">
                    <div class="bg-white py-2 collapse-inner rounded">
                        <a class="collapse-item" href="{{url('admin/roomtype/create')}}">Add New</a>
                        <a class="collapse-item" href="{{url('admin/roomtype')}}">View All</a>
                    </div>
                </div>
            </li>

            <!-- RoomMaster -->
            <li class="nav-item">
                <a class="nav-link @if(!request()->is('admin/rooms*')) collapsed @endif" href="#" data-toggle="collapse" data-target="#roomMaster"
                    aria-expanded="true" aria-controls="collapseTwo">
                    <i class="fas fa-fw fa-box"></i>
                    <span>Room</span>
                </a>
                <div id="roomMaster" class="collapse @if(request()->is('admin/rooms*')) show @endif" aria-labelledby="headingTwo" data-parent="#accordionSidebar">
                    <div class="bg-white py-2 collapse-inner rounded">
                        <a class="collapse-item" href="{{url('admin/rooms/create')}}">Add New</a>
                        <a class="collapse-item" href="{{url('admin/rooms')}}">View All</a>
                    </div>
                </div>
            </li>

            <!-- CustomerMaster -->
            <li class="nav-item">
                <a class="nav-link @if(!request()->is('admin/customer*')) collapsed @endif" href="#" data-toggle="collapse" data-target="#CustomerMaster"
                    aria-expanded="true" aria-controls="collapseTwo">
                    <i class="fas fa-fw fa-users"></i>
                    <span>Customers</span>
                </a>
                <div id="CustomerMaster" class="collapse @if(request()->is('admin/customer*')) show @endif" aria-labelledby="headingTwo" data-parent="#accordionSidebar">
                    <div class="bg-white py-2 collapse-inner rounded">
                        <a class="collapse-item" href="{{url('admin/customer/create')}}">Add New</a>
                        <a class="collapse-item" href="{{url('admin/customer')}}">View All</a>
                    </div>
                </div>
            </li>

            <!-- Department -->
            <li class="nav-item">
                <a class="nav-link @if(!request()->is('admin/department*')) collapsed @endif" href="#" data-toggle="collapse" data-target="#DepartmentMaster"
                    aria-expanded="true" aria-controls="collapseTwo">
                    <i class="fas fa-fw fa-users"></i>
                    <span>Departments</span>
                </a>
                <div id="DepartmentMaster" class="collapse @if(request()->is('admin/department*')) show @endif" aria-labelledby="headingTwo" data-parent="#accordionSidebar">
                    <div class="bg-white py-2 collapse-inner rounded">
                        <a class="collapse-item" href="{{url('admin/department/create')}}">Add New</a>
                        <a class="collapse-item" href="{{url('admin/department')}}">View All</a>
                    </div>
                </div>
            </li>

            <!-- Staff -->
            <li class="nav-item">
                <a class="nav-link @if(!request()->is('admin/staff*')) collapsed @endif" href="#" data-toggle="collapse" data-target="#StaffMaster"
                    aria-expanded="true" aria-controls="collapseTwo">
                    <i class="fas fa-fw fa-users"></i>
                    <span>Staff</span>
                </a>
                <div id="StaffMaster" class="collapse @if(request()->is('admin/staff*')) show @endif" aria-labelledby="headingTwo" data-parent="#accordionSidebar">
                    <div class="bg-white py-2 collapse-inner rounded">
                        <a class="collapse-item" href="{{url('admin/staff/create')}}">Add New</a>
                        <a class="collapse-item" href="{{url('admin/staff')}}">View All</a>
                    </div>
                </div>
            </li>

            <li class="nav-item">
                <a class="nav-link" href="{{url('admin/booking')}}">
                    <i class="fas fa-hotel"></i>
                    <span>Bookings</span></a>
            </li>

            <li class="nav-item">
                <a class="nav-link" href="{{url('admin/service')}}">
                    <i class="fas fa-table"></i>
                    <span>Services</span></a>
            </li>

            <li class="nav-item">
                <a class="nav-link" href="{{url('admin/testimonials')}}">
                    <i class="fas fa-hotel"></i>
                    <span>Customer Testimonials</span></a>
            </li>

            <!-- Nav Item - Dashboard -->
            <li class="nav-item">
                <a class="nav-link" href="{{url('admin/logout')}}">
                    <i class="fas fa-fw fa-sign-out-alt"></i>
                    <span>Logout</span></a>
            </li>

        </ul>
        <!-- End of Sidebar -->

        <!-- Content Wrapper -->
        <div id="content-wrapper" class="d-flex flex-column">

            <!-- Main Content -->
            <div id="content">

                <!-- Topbar -->
                <nav class="navbar navbar-expand navbar-light bg-white topbar mb-4 static-top shadow">

                    <!-- Sidebar Toggle (Topbar) -->
                    <button id="sidebarToggleTop" class="btn btn-link d-md-none rounded-circle mr-3">
                        <i class="fa fa-bars"></i>
                    </button>


                    <!-- Topbar Navbar -->
                    <ul class="navbar-nav ml-auto">

                        <!-- Nav Item - Search Dropdown (Visible Only XS) -->
                        <li class="nav-item dropdown no-arrow d-sm-none">
                            <a class="nav-link dropdown-toggle" href="#" id="searchDropdown" role="button"
                                data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <i class="fas fa-search fa-fw"></i>
                            </a>
                            <!-- Dropdown - Messages -->
                            <div class="dropdown-menu dropdown-menu-right p-3 shadow animated--grow-in"
                                aria-labelledby="searchDropdown">
                                <form class="form-inline mr-auto w-100 navbar-search">
                                    <div class="input-group">
                                        <input type="text" class="form-control bg-light border-0 small"
                                            placeholder="Search for..." aria-label="Search"
                                            aria-describedby="basic-addon2">
                                        <div class="input-group-append">
                                            <button class="btn btn-primary" type="button">
                                                <i class="fas fa-search fa-sm"></i>
                                            </button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </li>

                        <div class="topbar-divider d-none d-sm-block"></div>

                        <!-- Nav Item - User Information -->
                        <li class="nav-item dropdown no-arrow">
                            <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button"
                                data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <img class="img-profile rounded-circle"
                                    src="{{asset('public')}}/img/undraw_profile.svg">
                            </a>
                            <!-- Dropdown - User Information -->
                            <div class="dropdown-menu dropdown-menu-right shadow animated--grow-in"
                                aria-labelledby="userDropdown">
                                <a class="dropdown-item" href="{{url('admin/logout')}}" data-toggle="modal" data-target="#logoutModal">
                                    <i class="fas fa-sign-out-alt fa-sm fa-fw mr-2 text-gray-400"></i>
                                    Logout
                                </a>
                            </div>
                        </li>

                    </ul>

                </nav>
                <!-- End of Topbar -->

                @yield('content')

            </div>
            <!-- End of Main Content -->

            <!-- Footer -->
            <footer class="sticky-footer bg-white">
                <div class="container my-auto">
                    <div class="copyright text-center my-auto">
                        <span>Copyright &copy; Your Website 2020</span>
                    </div>
                </div>
            </footer>
            <!-- End of Footer -->

        </div>
        <!-- End of Content Wrapper -->

    </div>
    <!-- End of Page Wrapper -->

    <!-- Scroll to Top Button-->
    <a class="scroll-to-top rounded" href="#page-top">
        <i class="fas fa-angle-up"></i>
    </a>

    <!-- Logout Modal-->
    <div class="modal fade" id="logoutModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
        aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Ready to Leave?</h5>
                    <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>
                <div class="modal-body">Select "Logout" below if you are ready to end your current session.</div>
                <div class="modal-footer">
                    <button class="btn btn-secondary" type="button" data-dismiss="modal">Cancel</button>
                    <a class="btn btn-primary" href="{{url('admin/logout')}}">Logout</a>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap core JavaScript-->
    <script src="{{asset('public')}}/vendor/jquery/jquery.min.js"></script>
    <script src="{{asset('public')}}/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>

    <!-- Core plugin JavaScript-->
    <script src="{{asset('public')}}/vendor/jquery-easing/jquery.easing.min.js"></script>

    <!-- Custom scripts for all pages-->
    <script src="{{asset('public')}}/js/sb-admin-2.min.js"></script>

    @yield('scripts')

</body>

</html>