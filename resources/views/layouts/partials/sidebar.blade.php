<ul class="navbar-nav bg-gradient-primary sidebar sidebar-dark accordion" id="accordionSidebar">

    <!-- Sidebar - Brand -->
    <a class="sidebar-brand d-flex align-items-center justify-content-center" href="#">
        <div class="sidebar-brand-icon">
            <h3>Menu</h3>
            {{-- <img src="{{ asset('assets/img/header_logo.svg') }}" alt="" width="40%"> --}}
        </div>
    </a>

    <!-- Divider -->
    <hr class="sidebar-divider my-0">

    <!-- Nav Item - Dashboard -->
  


    <!-- Nav Item - Pages Collapse Menu -->

    @role('admin')
    <li class="nav-item active">
        <a class="nav-link" href="{{ route('home') }}">
            <i class="fas fa-fw fa-tachometer-alt"></i>
            <span>Dashboard</span></a>
    </li>

    <!-- Divider -->
    <hr class="sidebar-divider">

    <li class="nav-item">
        <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapseTwo"
            aria-expanded="true" aria-controls="collapseTwo">
            <i class="fas fa-fw fa-user "></i>
            <span>User</span>
        </a>
        <div id="collapseTwo" class="collapse" aria-labelledby="headingTwo" data-parent="#accordionSidebar">
            <div class="bg-white py-2 collapse-inner rounded">
                {{-- <h6 class="collapse-header">Custom Components:</h6> --}}
                <a class="collapse-item" href="{{ route('users.create') }}">Create</a>
                <a class="collapse-item" href="{{ route('users.index') }}">List</a>
            </div>
        </div>
    </li>

     <li class="nav-item">
        <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapseThree"
            aria-expanded="true" aria-controls="collapseThree">
            <i class="fas fa-fw fa-cog"></i>
            <span>Role</span>
        </a>
        <div id="collapseThree" class="collapse" aria-labelledby="headingTHree" data-parent="#accordionSidebar">
            <div class="bg-white py-2 collapse-inner rounded">
                <a class="collapse-item" href="{{ route('roles.create') }}">Create</a>
                <a class="collapse-item" href="{{ route('roles.index') }}">List</a>
            </div>
        </div>
    </li> 

    <!-- Nav Item - Utilities Collapse Menu -->
    <li class="nav-item">
        <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapseUtilities"
            aria-expanded="true" aria-controls="collapseUtilities">
            <i class="fas fa-fw fa-wrench"></i>
            <span>Games</span>
        </a>
        <div id="collapseUtilities" class="collapse" aria-labelledby="headingUtilities"
            data-parent="#accordionSidebar">
            <div class="bg-white py-2 collapse-inner rounded">
                {{-- <h6 class="collapse-header">Custom Utilities:</h6> --}}
                <a class="collapse-item" href="{{ route('games.create') }}">Create</a>
                <a class="collapse-item" href="{{ route('games.index') }}">List</a>
            </div>
        </div>
    </li>
    @endrole
   
    @role('user')
    <li class="nav-item">
        <a class="nav-link" href="{{ route('user.games.index') }}">
            <i class="fas fa-fw fa-gamepad"></i>
            <span>Games</span></a>
    </li>

    <li class="nav-item">
        <a class="nav-link" href="tables.html">
            <i class="fas fa-fw fa-book"></i>
            <span>News</span></a>
    </li>    
   
    <li class="nav-item">
        <a class="nav-link" href="tables.html">
            <i class="fas fa-fw fa-eye"></i>
            <span>Live Matching</span></a>
    </li>

    <li class="nav-item">
        <a class="nav-link" href="tables.html">
            <i class="fas fa-fw fa-table"></i>
            <span>Athletes</span></a>
    </li>
    @endrole



    <!-- Nav Item - Tables -->
    



</ul>