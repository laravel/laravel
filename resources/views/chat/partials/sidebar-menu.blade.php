<div class="side-menu flex-lg-column">
    <!-- LOGO -->
    <div class="navbar-brand-box">
        <a href="/" class="logo logo-dark">
            <span class="logo-sm">
                <svg xmlns="http://www.w3.org/2000/svg" width="30" height="30" viewBox="0 0 24 24"><path d="M8.5,18l3.5,4l3.5-4H19c1.103,0,2-0.897,2-2V4c0-1.103-0.897-2-2-2H5C3.897,2,3,2.897,3,4v12c0,1.103,0.897,2,2,2H8.5z M7,7h10v2H7V7z M7,11h7v2H7V11z"/></svg>
            </span>
        </a>

        <a href="/" class="logo logo-light">
            <span class="logo-sm">
                <svg xmlns="http://www.w3.org/2000/svg" width="30" height="30" viewBox="0 0 24 24"><path d="M8.5,18l3.5,4l3.5-4H19c1.103,0,2-0.897,2-2V4c0-1.103-0.897-2-2-2H5C3.897,2,3,2.897,3,4v12c0,1.103,0.897,2,2,2H8.5z M7,7h10v2H7V7z M7,11h7v2H7V11z"/></svg>
            </span>
        </a>
    </div>
    <!-- end navbar-brand-box -->

    <!-- Start side-menu nav -->
    <div class="flex-lg-column my-0 sidemenu-navigation">
        <ul class="nav nav-pills side-menu-nav" role="tablist">
            <li class="nav-item d-none d-lg-block" data-bs-toggle="tooltip" data-bs-placement="right" data-bs-trigger="hover" data-bs-container=".sidemenu-navigation" title="Profile">
                <a class="nav-link" id="pills-user-tab" data-bs-toggle="pill" href="#pills-user" role="tab">
                    <i class='bx bx-user-circle'></i>
                </a>
            </li>
            <li class="nav-item" data-bs-toggle="tooltip" data-bs-placement="right" data-bs-trigger="hover" data-bs-container=".sidemenu-navigation" title="Chats">
                <a class="nav-link active" id="pills-chat-tab" data-bs-toggle="pill" href="#pills-chat" role="tab">
                    <i class='bx bx-conversation'></i>
                </a>
            </li>
            <li class="nav-item" data-bs-toggle="tooltip" data-bs-placement="right" data-bs-trigger="hover" data-bs-container=".sidemenu-navigation" title="Contacts">
                <a class="nav-link" id="pills-contacts-tab" data-bs-toggle="pill" href="#pills-contacts" role="tab">
                    <i class='bx bxs-user-detail'></i>
                </a>
            </li>
            <li class="nav-item" data-bs-toggle="tooltip" data-bs-placement="right" data-bs-trigger="hover" data-bs-container=".sidemenu-navigation" title="Calls">
                <a class="nav-link" id="pills-calls-tab" data-bs-toggle="pill" href="#pills-calls" role="tab">
                    <i class='bx bx-phone-call'></i>
                </a>
            </li>
            <li class="nav-item" data-bs-toggle="tooltip" data-bs-placement="right" data-bs-trigger="hover" data-bs-container=".sidemenu-navigation" title="Bookmark">
                <a class="nav-link" id="pills-bookmark-tab" data-bs-toggle="pill" href="#pills-bookmark" role="tab">
                    <i class='bx bx-bookmarks'></i>
                </a>
            </li>
            <li class="nav-item d-none d-lg-block" data-bs-toggle="tooltip" data-bs-placement="right" data-bs-container=".sidemenu-navigation" data-bs-trigger="hover" title="Settings">
                <a class="nav-link" id="pills-setting-tab" data-bs-toggle="pill" href="#pills-setting" role="tab">
                    <i class='bx bx-cog'></i>
                </a>
            </li>
            <li class="nav-item mt-auto">
                <a class="nav-link light-dark" href="#" data-bs-toggle="tooltip" data-bs-trigger="hover" data-bs-placement="right" data-bs-container=".sidemenu-navigation" data-bs-html="true" title="<span class='light-mode'>Light</span> <span class='dark-mode'>Dark</span> Mode">
                    <i class='bx bx-moon'></i>
                </a>
            </li>
            <li class="nav-item dropdown profile-user-dropdown">
                <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    <img src="{{ auth()->user()->profile_photo_url }}" alt="{{ auth()->user()->name }}" class="profile-user rounded-circle">
                </a>
                <div class="dropdown-menu">
                    <a class="dropdown-item d-flex align-items-center justify-content-between" id="pills-user-tab" data-bs-toggle="pill" href="#pills-user" role="tab">Profile <i class="bx bx-user-circle text-muted ms-1"></i></a>
                    <a class="dropdown-item d-flex align-items-center justify-content-between" id="pills-setting-tab" data-bs-toggle="pill" href="#pills-setting" role="tab">Setting <i class="bx bx-cog text-muted ms-1"></i></a>
                    <a class="dropdown-item d-flex align-items-center justify-content-between" href="#">Change Password <i class="bx bx-lock-open text-muted ms-1"></i></a>
                    <div class="dropdown-divider"></div>
                    <form method="POST" action="{{ route('logout') }}" x-data>
                        @csrf
                        <a class="dropdown-item d-flex align-items-center justify-content-between" href="{{ route('logout') }}"
                           @click.prevent="$root.submit();">{{ __('Log out') }} <i class="bx bx-log-out-circle text-muted ms-1"></i></a>
                    </form>
                </div>
            </li>
        </ul>
    </div>
    <!-- end side-menu nav -->
</div>
