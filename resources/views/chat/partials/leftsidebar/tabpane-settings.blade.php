<div>
    <!-- Start Settings content -->
    <div>
        <div class="user-profile-img">
            <img src="{{ Vite::asset('resources/assets/images/small/img-4.jpg') }}" class="profile-img profile-foreground-img" style="height: 160px;" alt="">
            <div class="overlay-content">
                <div>
                    <div class="user-chat-nav p-3">

                        <div class="d-flex w-100 align-items-center">
                            <div class="flex-grow-1">
                                <h5 class="text-white mb-0">Settings</h5>
                            </div>
                            <div class="flex-shrink-0">
                                <div class="avatar-xs p-0 rounded-circle profile-photo-edit" data-bs-toggle="tooltip" data-bs-trigger="hover" data-bs-placement="bottom" title="Change Background">
                                    <input id="profile-foreground-img-file-input" type="file" class="profile-foreground-img-file-input" >
                                    <label for="profile-foreground-img-file-input" class="profile-photo-edit avatar-xs">
                                        <span class="avatar-title rounded-circle bg-light text-body">
                                            <i class="bx bxs-pencil"></i>
                                        </span>
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="text-center p-3 p-lg-4 border-bottom pt-2 pt-lg-2 mt-n5 position-relative">
            <div class="mb-3 profile-user">
                <img src="{{ auth()->user()->profile_photo_url }}" class="rounded-circle avatar-lg img-thumbnail user-profile-image" alt="user-profile-image">
                <div class="avatar-xs p-0 rounded-circle profile-photo-edit">
                    <input id="profile-img-file-input" type="file" class="profile-img-file-input" >
                    <label for="profile-img-file-input" class="profile-photo-edit avatar-xs">
                        <span class="avatar-title rounded-circle bg-light text-body">
                            <i class="bx bxs-camera"></i>
                        </span>
                    </label>
                </div>
            </div>

            <h5 class="font-size-16 mb-1 text-truncate"></h5>

            <div class="dropdown d-inline-block">
                <a class="text-muted dropdown-toggle d-block" href="#" role="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    <i class="bx bxs-circle {{ $statuses[auth()->user()->status]['class'] }} font-size-10 align-middle"></i> {{ $statuses[auth()->user()->status]['name'] }} <i class="mdi mdi-chevron-down"></i>
                </a>

                <div class="dropdown-menu">
                    @foreach($statuses as $key => $status)
                        <a class="dropdown-item" href="#"
                           wire:click="setUserStatus({{ $key }})"><i class="bx bxs-circle {{ $status['class'] }} font-size-10 me-1 align-middle"></i> {{ $status['name'] }}</a>
                    @endforeach
                </div>
            </div>


        </div>
        <!-- End profile user -->

        <!-- Start User profile description -->
        <div class="user-setting" data-simplebar>
            <div id="settingprofile" class="accordion accordion-flush">
                <div class="accordion-item">
                    <div class="accordion-header" id="headerpersonalinfo">
                        <button class="accordion-button font-size-14 fw-medium" type="button" data-bs-toggle="collapse" data-bs-target="#personalinfo" aria-expanded="true" aria-controls="personalinfo">
                            <i class="bx bxs-user text-muted me-3"></i> Personal Info
                        </button>
                    </div>
                    <div id="personalinfo" class="accordion-collapse collapse show" aria-labelledby="headerpersonalinfo" data-bs-parent="#settingprofile">
                        <div class="accordion-body">
                            <div class="float-end">
                                <button type="button" class="btn btn-soft-primary btn-sm"><i class="bx bxs-pencil align-middle"></i></button>
                            </div>

                            <div>
                                <p class="text-muted mb-1">Name</p>
                                <h5 class="font-size-14">{{ auth()->user()->name }}</h5>
                            </div>

                            <div class="mt-4">
                                <p class="text-muted mb-1">Email</p>
                                <h5 class="font-size-14">{{ auth()->user()->email }}</h5>
                            </div>

                            <div class="mt-4">
                                <p class="text-muted mb-1">Location</p>
                                <h5 class="font-size-14 mb-0">California, USA</h5>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- end personal info card -->

                <div class="accordion-item">
                    <div class="accordion-header" id="headerthemes">
                        <button class="accordion-button font-size-14 fw-medium collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapsethemes" aria-expanded="false" aria-controls="collapsethemes">
                            <i class="bx bxs-adjust-alt text-muted me-3"></i> Themes
                        </button>
                    </div>
                    <div id="collapsethemes" class="accordion-collapse collapse" aria-labelledby="headerthemes" data-bs-parent="#settingprofile">
                        <div class="accordion-body">
                            <div>
                                <h5 class="mb-3 font-size-11 text-muted text-uppercase">Choose Theme Color :</h5>
                                <div class="d-flex align-items-center flex-wrap gap-2 theme-btn-list theme-color-list">
                                    <div class="form-check">
                                        <input class="form-check-input theme-color" type="radio" value="0" name="bgcolor-radio" id="bgcolor-radio1" >
                                        <label class="form-check-label avatar-xs" for="bgcolor-radio1">
                                            <span class="avatar-title bg-primary-custom rounded-circle theme-btn bgcolor-radio1"></span>
                                        </label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input theme-color" type="radio" value="1" name="bgcolor-radio" id="bgcolor-radio2">
                                        <label class="form-check-label avatar-xs" for="bgcolor-radio2">
                                            <span class="avatar-title bg-info rounded-circle theme-btn bgcolor-radio2"></span>
                                        </label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input theme-color" type="radio" value="2" name="bgcolor-radio" id="bgcolor-radio4">
                                        <label class="form-check-label avatar-xs" for="bgcolor-radio4">
                                            <span class="avatar-title bg-purple rounded-circle theme-btn bgcolor-radio4"></span>
                                        </label>
                                    </div>

                                    <div class="form-check">
                                        <input class="form-check-input theme-color" type="radio" value="3" name="bgcolor-radio" id="bgcolor-radio5">
                                        <label class="form-check-label avatar-xs" for="bgcolor-radio5">
                                            <span class="avatar-title bg-pink rounded-circle theme-btn bgcolor-radio5"></span>
                                        </label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input theme-color" type="radio" value="4" name="bgcolor-radio" id="bgcolor-radio6">
                                        <label class="form-check-label avatar-xs" for="bgcolor-radio6">
                                            <span class="avatar-title bg-danger rounded-circle theme-btn bgcolor-radio6"></span>
                                        </label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input theme-color" type="radio" value="5" name="bgcolor-radio" id="bgcolor-radio7">
                                        <label class="form-check-label avatar-xs" for="bgcolor-radio7">
                                            <span class="avatar-title bg-secondary rounded-circle theme-btn bgcolor-radio7"></span>
                                        </label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input theme-color" type="radio" value="6" name="bgcolor-radio" id="bgcolor-radio8" checked>
                                        <label class="form-check-label avatar-xs light-background" for="bgcolor-radio8">
                                            <span class="avatar-title bg-light rounded-circle theme-btn bgcolor-radio8"></span>
                                        </label>
                                    </div>
                                </div>
                            </div>

                            <div class="mt-4 pt-2">
                                <h5 class="mb-3 font-size-11 text-muted text-uppercase">Choose Theme Image :</h5>
                                <div class="d-flex align-items-center flex-wrap gap-2 theme-btn-list theme-btn-list-img">
                                    <div class="form-check">
                                        <input class="form-check-input theme-img" type="radio" name="bgimg-radio" id="bgimg-radio1">
                                        <label class="form-check-label avatar-xs" for="bgimg-radio1">
                                            <span class="avatar-title bg-pattern-1 rounded-circle theme-btn bgimg-radio1"></span>
                                        </label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input theme-img" type="radio" name="bgimg-radio" id="bgimg-radio2">
                                        <label class="form-check-label avatar-xs" for="bgimg-radio2">
                                            <span class="avatar-title bg-pattern-2 rounded-circle theme-btn bgimg-radio2"></span>
                                        </label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input theme-img" type="radio" name="bgimg-radio" id="bgimg-radio3">
                                        <label class="form-check-label avatar-xs" for="bgimg-radio3">
                                            <span class="avatar-title bg-pattern-3 rounded-circle theme-btn bgimg-radio3"></span>
                                        </label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input theme-img" type="radio" name="bgimg-radio" id="bgimg-radio4">
                                        <label class="form-check-label avatar-xs" for="bgimg-radio4">
                                            <span class="avatar-title bg-pattern-4 rounded-circle theme-btn bgimg-radio4"></span>
                                        </label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input theme-img" type="radio" name="bgimg-radio" id="bgimg-radio5" checked>
                                        <label class="form-check-label avatar-xs" for="bgimg-radio5">
                                            <span class="avatar-title bg-pattern-5 rounded-circle theme-btn bgimg-radio5"></span>
                                        </label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input theme-img" type="radio" name="bgimg-radio" id="bgimg-radio6">
                                        <label class="form-check-label avatar-xs" for="bgimg-radio6">
                                            <span class="avatar-title bg-pattern-6 rounded-circle theme-btn bgimg-radio6"></span>
                                        </label>
                                    </div>

                                    <div class="form-check">
                                        <input class="form-check-input theme-img" type="radio" name="bgimg-radio" id="bgimg-radio7">
                                        <label class="form-check-label avatar-xs" for="bgimg-radio7">
                                            <span class="avatar-title bg-pattern-7 rounded-circle theme-btn bgimg-radio7"></span>
                                        </label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input theme-img" type="radio" name="bgimg-radio" id="bgimg-radio8">
                                        <label class="form-check-label avatar-xs" for="bgimg-radio8">
                                            <span class="avatar-title bg-pattern-8 rounded-circle theme-btn bgimg-radio8"></span>
                                        </label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input theme-img" type="radio" name="bgimg-radio" id="bgimg-radio9">
                                        <label class="form-check-label avatar-xs" for="bgimg-radio9">
                                            <span class="avatar-title bg-pattern-9 rounded-circle theme-btn bgimg-radio9"></span>
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="accordion-item">
                    <div class="accordion-header" id="privacy1">
                        <button class="accordion-button font-size-14 fw-medium collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#privacy" aria-expanded="false" aria-controls="privacy">
                            <i class="bx bxs-lock text-muted me-3"></i>Privacy
                        </button>
                    </div>
                    <div id="privacy" class="accordion-collapse collapse" aria-labelledby="privacy1" data-bs-parent="#settingprofile">
                        <div class="accordion-body">
                            <ul class="list-group list-group-flush">
                                <li class="list-group-item py-3 px-0 pt-0">
                                    <div class="d-flex align-items-center">
                                        <div class="flex-grow-1 overflow-hidden">
                                            <h5 class="font-size-13 mb-0 text-truncate">Profile photo</h5>
                                        </div>
                                        <div class="flex-shrink-0 ms-2">
                                            <select class="form-select form-select-sm">
                                                <option value="Everyone" selected>Everyone</option>
                                                <option value="Selected">Selected</option>
                                                <option value="Nobody">Nobody</option>
                                            </select>
                                        </div>
                                    </div>
                                </li>
                                <li class="list-group-item py-3 px-0">
                                    <div class="d-flex align-items-center">
                                        <div class="flex-grow-1 overflow-hidden">
                                            <h5 class="font-size-13 mb-0 text-truncate">Last seen</h5>

                                        </div>
                                        <div class="flex-shrink-0 ms-2">
                                            <div class="form-check form-switch">
                                                <input type="checkbox" class="form-check-input" id="privacy-lastseenSwitch" checked>
                                                <label class="form-check-label" for="privacy-lastseenSwitch"></label>
                                            </div>
                                        </div>
                                    </div>
                                </li>
                                <li class="list-group-item py-3 px-0">
                                    <div class="d-flex align-items-center">
                                        <div class="flex-grow-1 overflow-hidden">
                                            <h5 class="font-size-13 mb-0 text-truncate">Status</h5>
                                        </div>
                                        <div class="flex-shrink-0 ms-2">
                                            <select class="form-select form-select-sm">
                                                <option value="Everyone" selected>Everyone</option>
                                                <option value="Selected">Selected</option>
                                                <option value="Nobody">Nobody</option>
                                            </select>
                                        </div>
                                    </div>
                                </li>
                                <li class="list-group-item py-3 px-0">
                                    <div class="d-flex align-items-center">
                                        <div class="flex-grow-1 overflow-hidden">
                                            <h5 class="font-size-13 mb-0 text-truncate">Read receipts</h5>
                                        </div>
                                        <div class="flex-shrink-0 ms-2">
                                            <div class="form-check form-switch">
                                                <input type="checkbox" class="form-check-input" id="privacy-readreceiptSwitch" checked>
                                                <label class="form-check-label" for="privacy-readreceiptSwitch"></label>
                                            </div>
                                        </div>
                                    </div>
                                </li>
                                <li class="list-group-item py-3 px-0 pb-0">
                                    <div class="d-flex align-items-center">
                                        <div class="flex-grow-1 overflow-hidden">
                                            <h5 class="font-size-13 mb-0 text-truncate">Groups</h5>

                                        </div>
                                        <div class="flex-shrink-0 ms-2">
                                            <select class="form-select form-select-sm">
                                                <option value="Everyone" selected>Everyone</option>
                                                <option value="Selected">Selected</option>
                                                <option value="Nobody">Nobody</option>
                                            </select>
                                        </div>
                                    </div>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
                <!-- end privacy card -->

                <div class="accordion-item">
                    <div class="accordion-header" id="headersecurity">
                        <button class="accordion-button font-size-14 fw-medium collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapsesecurity" aria-expanded="false" aria-controls="collapsesecurity">
                            <i class="bx bxs-check-shield text-muted me-3"></i> Security
                        </button>
                    </div>
                    <div id="collapsesecurity" class="accordion-collapse collapse" aria-labelledby="headersecurity" data-bs-parent="#settingprofile">
                        <div class="accordion-body">
                            <ul class="list-group list-group-flush">
                                <li class="list-group-item p-0">
                                    <div class="d-flex align-items-center">
                                        <div class="flex-grow-1 overflow-hidden">
                                            <h5 class="font-size-13 mb-0 text-truncate">Show security notification</h5>

                                        </div>
                                        <div class="flex-shrink-0 ms-2">
                                            <div class="form-check form-switch">
                                                <input type="checkbox" class="form-check-input" id="security-notificationswitch">
                                                <label class="form-check-label" for="security-notificationswitch"></label>
                                            </div>
                                        </div>
                                    </div>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
                <!-- end security card -->



                <div class="accordion-item">
                    <div class="accordion-header" id="headerhelp">
                        <button class="accordion-button font-size-14 fw-medium collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapsehelp" aria-expanded="false" aria-controls="collapsehelp">
                            <i class="bx bxs-help-circle text-muted me-3"></i> Help
                        </button>
                    </div>
                    <div id="collapsehelp" class="accordion-collapse collapse" aria-labelledby="headerhelp" data-bs-parent="#settingprofile">
                        <div class="accordion-body">
                            <ul class="list-group list-group-flush">
                                <li class="list-group-item py-3 px-0 pt-0">
                                    <h5 class="font-size-13 mb-0"><a href="#" class="text-body d-block">FAQs</a></h5>
                                </li>
                                <li class="list-group-item py-3 px-0">
                                    <h5 class="font-size-13 mb-0"><a href="#" class="text-body d-block">Contact</a></h5>
                                </li>
                                <li class="list-group-item py-3 px-0 pb-0">
                                    <h5 class="font-size-13 mb-0"><a href="#" class="text-body d-block">Terms & Privacy policy</a></h5>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
            <!-- end profile-setting-accordion -->
        </div>
        <!-- End User profile description -->
    </div>
    <!-- Start Settings content -->
</div>
