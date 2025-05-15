<div id="kt_header" style="" class="header align-items-stretch">
    <!--begin::Container-->
    <div class="container-fluid d-flex align-items-stretch justify-content-between">
        <!--begin::Aside mobile toggle-->
        <div class="d-flex align-items-center d-lg-none ms-n3 me-1" title="Show aside menu">
            <div class="btn btn-icon btn-active-light-primary w-30px h-30px w-md-40px h-md-40px"
                id="kt_aside_mobile_toggle">
                <!--begin::Svg Icon | path: icons/duotone/Text/Menu.svg-->
                <span class="svg-icon svg-icon-2x mt-1">
                    <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink"
                        width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
                        <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                            <rect x="0" y="0" width="24" height="24" />
                            <rect fill="#000000" x="4" y="5" width="16" height="3" rx="1.5" />
                            <path
                                d="M5.5,15 L18.5,15 C19.3284271,15 20,15.6715729 20,16.5 C20,17.3284271 19.3284271,18 18.5,18 L5.5,18 C4.67157288,18 4,17.3284271 4,16.5 C4,15.6715729 4.67157288,15 5.5,15 Z M5.5,10 L18.5,10 C19.3284271,10 20,10.6715729 20,11.5 C20,12.3284271 19.3284271,13 18.5,13 L5.5,13 C4.67157288,13 4,12.3284271 4,11.5 C4,10.6715729 4.67157288,10 5.5,10 Z"
                                fill="#000000" opacity="0.3" />
                        </g>
                    </svg>
                </span>
                <!--end::Svg Icon-->
            </div>
        </div>
        <!--end::Aside mobile toggle-->
        <!--begin::Mobile logo-->
        {{-- <div class="d-flex align-items-center flex-grow-1 flex-lg-grow-0">
            <a href="index.html" class="d-lg-none">
                <img alt="Logo" src="assets/media/logos/logo-3.svg" class="h-30px" />
            </a>
        </div> --}}
        <!--end::Mobile logo-->
        <!--begin::Wrapper-->
        <div class="d-flex align-items-stretch justify-content-between flex-lg-grow-1">
            <!--begin::Navbar-->
            <div class="d-flex align-items-stretch" id="kt_header_nav">
                <!--begin::Menu wrapper-->
                <div class="header-menu align-items-stretch" data-kt-drawer="true"
                    data-kt-drawer-name="header-menu"
                    data-kt-drawer-activate="{default: true, lg: false}" data-kt-drawer-overlay="true"
                    data-kt-drawer-width="{default:'200px', '300px': '250px'}"
                    data-kt-drawer-direction="end" data-kt-drawer-toggle="#kt_header_menu_mobile_toggle"
                    data-kt-swapper="true" data-kt-swapper-mode="prepend"
                    data-kt-swapper-parent="{default: '#kt_body', lg: '#kt_header_nav'}">
                    <!--begin::Menu-->
                    <div class="menu menu-lg-rounded menu-column menu-lg-row menu-state-bg menu-title-gray-700 menu-state-title-primary menu-state-icon-primary menu-state-bullet-primary menu-arrow-gray-400 fw-bold my-5 my-lg-0 align-items-stretch"
                        id="#kt_header_menu" data-kt-menu="true">
                        {{-- <div class="menu-item me-lg-1">
                            <a class="menu-link active py-3" href="index.html">
                                <span class="menu-title">Dashboard</span>
                            </a>
                        </div> --}}
                    </div>
                    <!--end::Menu-->
                </div>
                <!--end::Menu wrapper-->
            </div>
            <!--end::Navbar-->
            <!--begin::Topbar-->
            <div class="d-flex align-items-stretch flex-shrink-0">
                <!--begin::Toolbar wrapper-->
                <div class="d-flex align-items-stretch flex-shrink-0">
                    <!--begin::Search-->
                    <!--begin::Chat-->
                <div class="d-flex align-items-center ms-1 ms-lg-3">
                    <!--begin::Menu- wrapper-->
                    <a id="notif_page">

                        <div class="btn btn-icon btn-active-light-primary position-relative w-30px h-30px w-md-40px h-md-40px menu-dropdown"
                            data-kt-menu-trigger="click" data-kt-menu-attach="parent" data-kt-menu-placement="bottom-end"
                            data-kt-menu-flip="bottom">
                            <span class="svg-icon svg-icon-1">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-bell" viewBox="0 0 16 16">
                                    <path d="M8 16a2 2 0 0 0 2-2H6a2 2 0 0 0 2 2zM8 1.918l-.797.161A4.002 4.002 0 0 0 4 6c0 .628-.134 2.197-.459 3.742-.16.767-.376 1.566-.663 2.258h10.244c-.287-.692-.502-1.49-.663-2.258C12.134 8.197 12 6.628 12 6a4.002 4.002 0 0 0-3.203-3.92L8 1.917zM14.22 12c.223.447.481.801.78 1H1c.299-.199.557-.553.78-1C2.68 10.2 3 6.88 3 6c0-2.42 1.72-4.44 4.005-4.901a1 1 0 1 1 1.99 0A5.002 5.002 0 0 1 13 6c0 .88.32 4.2 1.22 6z"/>
                                  </svg>
                            </span>
                            <span id="notifikasi_id" class=""></span>
                            <!--end::Svg Icon-->
                        </div>
                    </a>
                    <!--begin::Menu-->
                    <div class="menu menu-sub menu-sub-dropdown menu-column w-350px w-lg-375px" data-kt-menu="true"
                        style="z-index: 105; position: fixed; inset: 0px auto auto 0px; margin: 0px; transform: translate(777px, 65px);"
                        data-popper-placement="bottom-end">
                        <!--begin::Heading-->
                        {{-- <div class="d-flex flex-column bgi-no-repeat rounded-top bg-primary">
                            <!--begin::Title-->
                            <h3 class="text-white fw-bold px-9 mt-10 mb-6">Notifikasi
                                <span class="fs-8 opacity-75 ps-3">24</span></h3>
                            <ul class="nav nav-line-tabs nav-line-tabs-2x nav-stretch fw-bold px-9">
                                <li class="nav-item">
                                    <a class="nav-link text-white opacity-75 opacity-state-100 pb-4"
                                        data-bs-toggle="tab" href="#kt_topbar_notifications_1"></a>
                                </li>
                            </ul>
                        </div> --}}
                        <div class="tab-content">
                            <!--begin::Tab panel-->
                            <div class="tab-pane fade active show" id="kt_topbar_notifications_1" role="tabpanel">
                                <!--begin::Items-->
                                {{-- <div class="scroll-y mh-325px px-5">
                                    <div class="d-flex flex-stack py-4">
                                        <div class="d-flex align-items-center">
                                            <div class="mb-0 me-2">
                                                <p
                                                    class="fs-6 text-gray-800 text-hover-primary fw-bolder">Nama pemohon</p>
                                                <div class="text-gray-400 fs-7">Telah mengirimkan permohonan (nama layanan)</div>
                                            </div>
                                        </div>
                                        <span class="badge badge-light fs-8">Waktu</span>
                                    </div>
                                    <a href=""> <span class="badge badge-primary fs-8">Tandai Telah di baca</span></a>
                                </div>
                                <hr> --}}
                            </div>
                            <!--end::Tab panel-->
                        </div>
                        <!--end::Tab content-->
                    </div>
                    <!--end::Menu-->
                    <!--end::Menu wrapper-->
                </div>
                    <!--end::Chat-->

                    <!--begin::User-->
                    <div class="d-flex align-items-center ms-1 ms-lg-3" id="kt_header_user_menu_toggle">
                        <!--begin::Menu wrapper-->
                        <div class="cursor-pointer symbol symbol-30px symbol-md-40px"
                            data-kt-menu-trigger="click" data-kt-menu-attach="parent"
                            data-kt-menu-placement="bottom-end" data-kt-menu-flip="bottom">
                            <img src="{{ asset('assets/img/logo.jpg') }}" alt="name" />
                        </div>
                        <!--begin::Menu-->
                        <div class="menu menu-sub menu-sub-dropdown menu-column menu-rounded menu-gray-600 menu-state-bg menu-state-primary fw-bold py-4 fs-6 w-275px"
                            data-kt-menu="true">
                            <!--begin::Menu item-->
                            <div class="menu-item px-3">
                                <div class="menu-content d-flex align-items-center px-3">
                                    <!--begin::Avatar-->
                                    <div class="symbol symbol-50px me-5">
                                        <img alt="Logo" src="{{ asset('assets/img/logo.jpg') }}" />
                                    </div>
                                    <!--end::Avatar-->
                                    <!--begin::Username-->
                                    <div class="d-flex flex-column">
                                        
                                        <div class="fw-bolder d-flex align-items-center fs-5">{{ Auth::user()->name }}</div>
                                        <a href="javascript:void()" class="fw-bold text-muted text-hover-primary fs-7">{{ Auth::user()->email }}</a>
                                    </div>
                                    <!--end::Username-->
                                </div>
                            </div>
                            <!--end::Menu item-->
                            <div class="menu-item px-5">
                                <a href="" class="menu-link px-5">Profile</a>
                                <a href="javascript:void()" data-toggle="pill" data-bs-toggle="modal" data-bs-target="#logoutModal"
                                    class="menu-link px-5">Sign Out</a>
                            </div>
                            <!--end::Menu item-->
                        </div>
                        <!--end::Menu-->
                        <!--end::Menu wrapper-->
                    </div>
                    <div class="toolbar" id="kt_toolbar">
                        <!--begin::Container-->
                        <div id="kt_toolbar_container" class="container-fluid d-flex flex-stack">
                            <!--begin::Page title-->
                            <div data-kt-swapper="true" data-kt-swapper-mode="prepend" data-kt-swapper-parent="{default: '#kt_content_container', 'lg': '#kt_toolbar_container'}" class="page-title d-flex align-items-center flex-wrap me-3 mb-5 mb-lg-0">
                                <!--begin::Title-->
                                <h1 class="d-flex align-items-center text-dark fw-bolder my-1 fs-3">@yield('title')</h1>
                                <!--end::Title-->
                                <!--begin::Separator-->
                                <span class="h-20px border-gray-200 border-start mx-4"></span>
                                <!--end::Separator-->
                                <!--begin::Breadcrumb-->
                                <ul class="breadcrumb breadcrumb-separatorless fw-bold fs-7 my-1">
                                    <!--begin::Item-->
                                    <li class="breadcrumb-item text-muted">
                                        <a href="" class="text-muted text-hover-primary">@yield('breadcumb-1')</a>
                                    </li>
                                    <!--end::Item-->
                                    <!--begin::Item-->
                                    <li class="breadcrumb-item">
                                        <span class="bullet bg-gray-200 w-5px h-2px"></span>
                                    </li>
                                    <!--end::Item-->
                                    <!--begin::Item-->
                                    <li class="breadcrumb-item text-muted">@yield('breadcumb-2')</li>
                                    <!--end::Item-->
                                    <!--begin::Item-->
                                    <li class="breadcrumb-item">
                                        <span class="bullet bg-gray-200 w-5px h-2px"></span>
                                    </li>
                                    <!--end::Item-->
                                    <!--begin::Item-->
                                    <li class="breadcrumb-item text-dark">@yield('breadcumb-3')</li>
                                    <!--end::Item-->
                                </ul>
                                <!--end::Breadcrumb-->
                            </div>
                        </div>
                        <!--end::Container-->
                    </div>
                    <!--end::User -->
                </div>
                <!--end::Toolbar wrapper-->
            </div>
            <!--end::Topbar-->
        </div>
        <!--end::Wrapper-->
    </div>
    <!--end::Container-->
</div>

<!-- Logout Modal-->
<div class="modal fade" id="logoutModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Yakin ingin keluar?</h5>
                {{-- <button type="button" class="btn-close btn-light text-white" data-bs-dismiss="modal"></button> --}}
            </div>
            <div class="modal-body">Klik tombol 'Logout' dibawah ini untuk keluar dari sesi</div>
            <div class="modal-footer">
                <button class="btn btn-secondary" type="button" data-bs-dismiss="modal">Cancel</button>
                <a class="btn btn-primary bg-hover-danger" href="{{ route('logout') }}" onclick="event.preventDefault();
                document.getElementById('logout-form').submit();">Logout</a>
                <form id="logout-form" action="{{ route('logout') }}" method="GET" class="d-none">
                    @csrf
                </form>
            </div>
        </div>
    </div>
</div>
@push('script')




@endpush