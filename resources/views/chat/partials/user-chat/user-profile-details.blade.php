<div>
    <div class="user-profile-sidebar"
         :class="{ 'd-block': isOpenProfileSidebar }">

        <div class="p-3 border-bottom">
            <div class="user-profile-img">
                <img src="{{ $user['profile_photo_url'] }}" class="profile-img rounded" alt="{{ $user['name'] }}">
                <div class="overlay-content rounded">
                    <div class="user-chat-nav p-2">
                        <div class="d-flex w-100">
                            <div class="flex-grow-1">
                                <button type="button" class="btn nav-btn text-white user-profile-show d-none d-lg-block"
                                        @click="isOpenProfileSidebar = ! isOpenProfileSidebar">
                                    <i class="bx bx-x"></i>
                                </button>
                                <button type="button" class="btn nav-btn text-white user-profile-show d-block d-lg-none">
                                    <i class="bx bx-left-arrow-alt"></i>
                                </button>
                            </div>
                            <div class="flex-shrink-0">
                                <div class="dropdown">
                                    <button class="btn nav-btn text-white dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                        <i class='bx bx-dots-vertical-rounded'></i>
                                    </button>
                                    <div class="dropdown-menu dropdown-menu-end">
                                        <a class="dropdown-item d-flex justify-content-between align-items-center d-lg-none user-profile-show" href="#">View Profile <i class="bx bx-user text-muted"></i></a>
                                        <a class="dropdown-item d-flex justify-content-between align-items-center d-lg-none" href="#" data-bs-toggle="modal" data-bs-target=".audiocallModal">Audio <i class="bx bxs-phone-call text-muted"></i></a>
                                        <a class="dropdown-item d-flex justify-content-between align-items-center d-lg-none" href="#" data-bs-toggle="modal" data-bs-target=".videocallModal">Video <i class="bx bx-video text-muted"></i></a>
                                        <a class="dropdown-item d-flex justify-content-between align-items-center" href="#">Archive <i class="bx bx-archive text-muted"></i></a>
                                        <a class="dropdown-item d-flex justify-content-between align-items-center" href="#">Muted <i class="bx bx-microphone-off text-muted"></i></a>
                                        <a class="dropdown-item d-flex justify-content-between align-items-center" href="#">Delete <i class="bx bx-trash text-muted"></i></a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="mt-auto p-3">
                        <h5 class="user-name mb-1 text-truncate">{{ $user['name'] }}</h5>
                        <p class="font-size-14 text-truncate mb-0"><i class="bx bxs-circle font-size-10 text-success me-1 ms-0"></i> Online</p>
                    </div>
                </div>
            </div>
        </div>
        <!-- End profile user -->

        <!-- Start user-profile-desc -->
        <div class="p-4 user-profile-desc" data-simplebar>

            <div class="text-center border-bottom">
                <div class="row">
                    <div class="col-sm col-4">
                        <div class="mb-4">
                            <button type="button" class="btn avatar-sm p-0">
                                <span class="avatar-title rounded bg-light text-body">
                                    <i class="bx bxs-message-alt-detail"></i>
                                </span>
                            </button>
                            <h5 class="font-size-11 text-uppercase text-muted mt-2">Message</h5>
                        </div>
                    </div>
                    <div class="col-sm col-4">
                        <div class="mb-4">
                            <button type="button" class="btn avatar-sm p-0 favourite-btn">
                                <span class="avatar-title rounded bg-light text-body">
                                    <i class="bx bx-heart"></i>
                                </span>
                            </button>
                            <h5 class="font-size-11 text-uppercase text-muted mt-2">Favourite</h5>
                        </div>
                    </div>
                    <div class="col-sm col-4">
                        <div class="mb-4">
                            <button type="button" class="btn avatar-sm p-0" data-bs-toggle="modal" data-bs-target=".audiocallModal">
                                <span class="avatar-title rounded bg-light text-body">
                                    <i class="bx bxs-phone-call"></i>
                                </span>
                            </button>
                            <h5 class="font-size-11 text-uppercase text-muted mt-2">Audio</h5>
                        </div>
                    </div>
                    <div class="col-sm col-4">
                        <div class="mb-4">
                            <button type="button" class="btn avatar-sm p-0" data-bs-toggle="modal" data-bs-target=".videocallModal">
                                <span class="avatar-title rounded bg-light text-body">
                                    <i class="bx bx-video"></i>
                                </span>
                            </button>
                            <h5 class="font-size-11 text-uppercase text-muted mt-2">Video</h5>
                        </div>
                    </div>
                    <div class="col-sm col-4">
                        <div class="mb-4">
                            <div class="dropdown">
                                <button class="btn avatar-sm p-0 dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                    <span class="avatar-title bg-light text-body rounded">
                                        <i class='bx bx-dots-horizontal-rounded'></i>
                                    </span>
                                </button>

                                <div class="dropdown-menu dropdown-menu-end">
                                    <a class="dropdown-item d-flex justify-content-between align-items-center" href="#">Archive <i class="bx bx-archive text-muted"></i></a>
                                    <a class="dropdown-item d-flex justify-content-between align-items-center" href="#">Muted <i class="bx bx-microphone-off text-muted"></i></a>
                                    <a class="dropdown-item d-flex justify-content-between align-items-center" href="#">Delete <i class="bx bx-trash text-muted"></i></a>
                                </div>
                            </div>
                            <h5 class="font-size-11 text-uppercase text-muted mt-2">More</h5>
                        </div>
                    </div>
                </div>
            </div>
            <div class="text-muted pt-4">
                <h5 class="font-size-11 text-uppercase">Status :</h5>
                <p class="mb-4">If several languages coalesce, the grammar of the resulting.</p>
            </div>

            <div class="pb-2">
                <h5 class="font-size-11 text-uppercase mb-2">Info :</h5>
                <div>
                    <div class="d-flex align-items-end">
                        <div class="flex-grow-1">
                            <p class="text-muted font-size-14 mb-1">Name</p>
                        </div>
                        <div class="flex-shrink-0">
                            <button type="button" class="btn btn-sm btn-soft-primary">Edit</button>
                        </div>
                    </div>
                    <h5 class="font-size-14 text-truncate">{{ $user['name'] }}</h5>
                </div>

                <div class="mt-4">
                    <p class="text-muted font-size-14 mb-1">Email</p>
                    <h5 class="font-size-14">{{ $user['email'] }}</h5>
                </div>

                <div class="mt-4">
                    <p class="text-muted font-size-14 mb-1">Location</p>
                    <h5 class="font-size-14 mb-0">California, USA</h5>
                </div>
            </div>
            <hr class="my-4">

            <div>
                <div class="d-flex">
                    <div class="flex-grow-1">
                        <h5 class="font-size-11 text-muted text-uppercase">Group in common</h5>
                    </div>
                </div>

                <ul class="list-unstyled chat-list mx-n4">
                    <li>
                        <a href="javascript: void(0);">
                            <div class="d-flex align-items-center">
                                <div class="flex-shrink-0 avatar-xs me-2">
                                    <span class="avatar-title rounded-circle bg-soft-light text-dark">
                                        #
                                    </span>
                                </div>
                                <div class="flex-grow-1 overflow-hidden">
                                    <p class="text-truncate mb-0">Landing Design</p>
                                </div>
                            </div>
                        </a>
                    </li>
                    <li>
                        <a href="javascript: void(0);">
                            <div class="d-flex align-items-center">
                                <div class="flex-shrink-0 avatar-xs me-2">
                                    <span class="avatar-title rounded-circle bg-soft-light text-dark">
                                        #
                                    </span>
                                </div>
                                <div class="flex-grow-1 overflow-hidden">
                                    <p class="text-truncate mb-0">Design Phase 2</p>
                                </div>
                            </div>
                        </a>
                    </li>
                </ul>
            </div>

            <hr class="my-4">

            <div>
                <div class="d-flex">
                    <div class="flex-grow-1">
                        <h5 class="font-size-11 text-muted text-uppercase">Media</h5>
                    </div>
                    <div class="flex-shrink-0">
                        <a href="#" class="font-size-12 d-block mb-2">Show all</a>
                    </div>
                </div>
                <div class="profile-media-img">
                    <div class="media-img-list">
                        <a href="#">
                            <img src="{{ Vite::asset('resources/assets/images/small/img-1.jpg') }}" alt="media img" class="img-fluid">
                        </a>
                    </div>
                    <div class="media-img-list">
                        <a href="#">
                            <img src="{{ Vite::asset('resources/assets/images/small/img-2.jpg') }}" alt="media img" class="img-fluid">
                        </a>
                    </div>
                    <div class="media-img-list">
                        <a href="#">
                            <img src="{{ Vite::asset('resources/assets/images/small/img-3.jpg') }}" alt="media img" class="img-fluid">
                        </a>
                    </div>
                    <div class="media-img-list">
                        <a href="#">
                            <img src="{{ Vite::asset('resources/assets/images/small/img-4.jpg') }}" alt="media img" class="img-fluid">
                            <div class="bg-overlay">+ 15</div>
                        </a>
                    </div>
                </div>
            </div>

            <hr class="my-4">

            <div>
                <div>
                    <h5 class="font-size-11 text-muted text-uppercase mb-3">Attached Files</h5>
                </div>

                <div>
                    <div class="card p-2 border mb-2">
                        <div class="d-flex align-items-center">
                            <div class="flex-shrink-0 avatar-xs ms-1 me-3">
                                <div class="avatar-title bg-soft-primary text-primary rounded-circle">
                                    <i class="bx bx-file"></i>
                                </div>
                            </div>
                            <div class="flex-grow-1 overflow-hidden">
                                <h5 class="font-size-14 text-truncate mb-1">design-phase-1-approved.pdf</h5>
                                <p class="text-muted font-size-13 mb-0">12.5 MB</p>
                            </div>

                            <div class="flex-shrink-0 ms-3">
                                <div class="d-flex gap-2">
                                    <div>
                                        <a href="#" class="text-muted px-1">
                                            <i class="bx bxs-download"></i>
                                        </a>
                                    </div>
                                    <div class="dropdown">
                                        <a class="dropdown-toggle text-muted px-1" href="#" role="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                            <i class="bx bx-dots-horizontal-rounded"></i>
                                        </a>
                                        <div class="dropdown-menu dropdown-menu-end">
                                            <a class="dropdown-item d-flex align-items-center justify-content-between" href="#">Share <i class="bx bx-share-alt ms-2 text-muted"></i></a>
                                            <a class="dropdown-item d-flex align-items-center justify-content-between" href="#">Bookmark <i class="bx bx-bookmarks text-muted ms-2"></i></a>
                                            <div class="dropdown-divider"></div>
                                            <a class="dropdown-item d-flex align-items-center justify-content-between" href="#">Delete <i class="bx bx-trash ms-2 text-muted"></i></a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="card p-2 border mb-2">
                        <div class="d-flex align-items-center">
                            <div class="flex-shrink-0 avatar-xs ms-1 me-3">
                                <div class="avatar-title bg-soft-primary text-primary rounded-circle">
                                    <i class="bx bx-image"></i>
                                </div>
                            </div>
                            <div class="flex-grow-1 overflow-hidden">
                                <h5 class="font-size-14 text-truncate mb-1">Image-1.jpg</h5>
                                <p class="text-muted font-size-13 mb-0">4.2 MB</p>
                            </div>

                            <div class="flex-shrink-0 ms-3">
                                <div class="d-flex gap-2">
                                    <div>
                                        <a href="#" class="text-muted px-1">
                                            <i class="bx bxs-download"></i>
                                        </a>
                                    </div>
                                    <div class="dropdown">
                                        <a class="dropdown-toggle text-muted px-1" href="#" role="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                            <i class="bx bx-dots-horizontal-rounded"></i>
                                        </a>
                                        <div class="dropdown-menu dropdown-menu-end">
                                            <a class="dropdown-item d-flex align-items-center justify-content-between" href="#">Share <i class="bx bx-share-alt ms-2 text-muted"></i></a>
                                            <a class="dropdown-item d-flex align-items-center justify-content-between" href="#">Bookmark <i class="bx bx-bookmarks text-muted ms-2"></i></a>
                                            <div class="dropdown-divider"></div>
                                            <a class="dropdown-item d-flex align-items-center justify-content-between" href="#">Delete <i class="bx bx-trash ms-2 text-muted"></i></a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="card p-2 border mb-2">
                        <div class="d-flex align-items-center">
                            <div class="flex-shrink-0 avatar-xs ms-1 me-3">
                                <div class="avatar-title bg-soft-primary text-primary rounded-circle">
                                    <i class="bx bx-image"></i>
                                </div>
                            </div>
                            <div class="flex-grow-1 overflow-hidden">
                                <h5 class="font-size-14 text-truncate mb-1">Image-2.jpg</h5>
                                <p class="text-muted font-size-13 mb-0">3.1 MB</p>
                            </div>

                            <div class="flex-shrink-0 ms-3">
                                <div class="d-flex gap-2">
                                    <div>
                                        <a href="#" class="text-muted px-1">
                                            <i class="bx bxs-download"></i>
                                        </a>
                                    </div>
                                    <div class="dropdown">
                                        <a class="dropdown-toggle text-muted px-1" href="#" role="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                            <i class="bx bx-dots-horizontal-rounded"></i>
                                        </a>
                                        <div class="dropdown-menu dropdown-menu-end">
                                            <a class="dropdown-item d-flex align-items-center justify-content-between" href="#">Share <i class="bx bx-share-alt ms-2 text-muted"></i></a>
                                            <a class="dropdown-item d-flex align-items-center justify-content-between" href="#">Bookmark <i class="bx bx-bookmarks text-muted ms-2"></i></a>
                                            <div class="dropdown-divider"></div>
                                            <a class="dropdown-item d-flex align-items-center justify-content-between" href="#">Delete <i class="bx bx-trash ms-2 text-muted"></i></a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="card p-2 border mb-2">
                        <div class="d-flex align-items-center">
                            <div class="flex-shrink-0 avatar-xs ms-1 me-3">
                                <div class="avatar-title bg-soft-primary text-primary rounded-circle">
                                    <i class="bx bx-file"></i>
                                </div>
                            </div>
                            <div class="flex-grow-1 overflow-hidden">
                                <h5 class="font-size-14 text-truncate mb-1">Landing-A.zip</h5>
                                <p class="text-muted font-size-13 mb-0">6.7 MB</p>
                            </div>

                            <div class="flex-shrink-0 ms-3">
                                <div class="d-flex gap-2">
                                    <div>
                                        <a href="#" class="text-muted px-1">
                                            <i class="bx bxs-download"></i>
                                        </a>
                                    </div>
                                    <div class="dropdown">
                                        <a class="dropdown-toggle text-muted px-1" href="#" role="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                            <i class="bx bx-dots-horizontal-rounded"></i>
                                        </a>
                                        <div class="dropdown-menu dropdown-menu-end">
                                            <a class="dropdown-item d-flex align-items-center justify-content-between" href="#">Share <i class="bx bx-share-alt ms-2 text-muted"></i></a>
                                            <a class="dropdown-item d-flex align-items-center justify-content-between" href="#">Bookmark <i class="bx bx-bookmarks text-muted ms-2"></i></a>
                                            <div class="dropdown-divider"></div>
                                            <a class="dropdown-item d-flex align-items-center justify-content-between" href="#">Delete <i class="bx bx-trash ms-2 text-muted"></i></a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- end user-profile-desc -->
    </div>
</div>
