<div>
    <div class="chat-content d-lg-flex"
         x-data="{ isOpenProfileSidebar: false }">
        <!-- start chat conversation section -->
        <div class="w-100 overflow-hidden position-relative">
            @if ($group)
                <div x-init="Echo.private('chat.{{ $group->id }}')
                    .listen('MessageSent', (e) => {
                        // @this.call('incomingMessage', e)
                        window.livewire.emitTo('user-chat-conversation-list', 'messageSent', e)
                    })"></div>

                <!-- conversation user -->
                @includeWhen($group->isUserType(), 'chat.partials.user-chat.conversation-user', compact('group', 'user'))

                <!-- conversation group -->
                @includeWhen($group->isGroupType(), 'chat.partials.user-chat.conversation-group', compact('group'))

                <!-- start chat input section -->
                <div class="position-relative">
                    <div class="chat-input-section p-3 p-lg-4">

                        @livewire('user-chat-input', compact('group'), key('group-'.$group['id']))
                        <div class="chat-input-collapse chat-input-collapse1 collapse" id="chatinputmorecollapse">
                            <div class="card mb-0">
                                <div class="card-body py-3">
                                    <!-- Swiper -->
                                    <div class="swiper chatinput-links">
                                        <div class="swiper-wrapper">
                                            <div class="swiper-slide">
                                                <div class="text-center px-2 position-relative">
                                                    <div>
                                                        <input id="attachedfile-input" type="file" class="d-none" accept=".zip,.rar,.7zip,.pdf" multiple>
                                                        <label for="attachedfile-input" class="avatar-sm mx-auto stretched-link">
                                            <span class="avatar-title font-size-18 bg-soft-primary text-primary rounded-circle">
                                                <i class="bx bx-paperclip"></i>
                                            </span>
                                                        </label>
                                                    </div>
                                                    <h5 class="font-size-11 text-uppercase mt-3 mb-0 text-body text-truncate">Attached</h5>
                                                </div>
                                            </div>
                                            <div class="swiper-slide">
                                                <div class="text-center px-2">
                                                    <div class="avatar-sm mx-auto">
                                                        <div class="avatar-title font-size-18 bg-soft-primary text-primary rounded-circle">
                                                            <i class="bx bxs-camera"></i>
                                                        </div>
                                                    </div>
                                                    <h5 class="font-size-11 text-uppercase text-truncate mt-3 mb-0"><a href="#" class="text-body stretched-link" onclick="cameraPermission()">Camera</a></h5>
                                                </div>
                                            </div>
                                            <div class="swiper-slide">
                                                <div class="text-center px-2 position-relative">
                                                    <div>
                                                        <input id="galleryfile-input" type="file" class="d-none" accept="image/png, image/gif, image/jpeg" multiple>
                                                        <label for="galleryfile-input" class="avatar-sm mx-auto stretched-link">
                                            <span class="avatar-title font-size-18 bg-soft-primary text-primary rounded-circle">
                                                <i class="bx bx-images"></i>
                                            </span>
                                                        </label>
                                                    </div>
                                                    <h5 class="font-size-11 text-uppercase text-truncate mt-3 mb-0">Gallery</h5>
                                                </div>
                                            </div>
                                            <div class="swiper-slide">
                                                <div class="text-center px-2">
                                                    <div>
                                                        <input id="audiofile-input" type="file" class="d-none" accept="audio/*" multiple>
                                                        <label for="audiofile-input" class="avatar-sm mx-auto stretched-link">
                                            <span class="avatar-title font-size-18 bg-soft-primary text-primary rounded-circle">
                                                <i class="bx bx-headphone"></i>
                                            </span>
                                                        </label>
                                                    </div>
                                                    <h5 class="font-size-11 text-uppercase text-truncate mt-3 mb-0">Audio</h5>
                                                </div>
                                            </div>
                                            <div class="swiper-slide">
                                                <div class="text-center px-2">
                                                    <div class="avatar-sm mx-auto">
                                                        <div class="avatar-title font-size-18 bg-soft-primary text-primary rounded-circle">
                                                            <i class="bx bx-current-location"></i>
                                                        </div>
                                                    </div>

                                                    <h5 class="font-size-11 text-uppercase text-truncate mt-3 mb-0"><a href="#" class="text-body stretched-link" onclick="getLocation()">Location</a></h5>
                                                </div>
                                            </div>
                                            <div class="swiper-slide">
                                                <div class="text-center px-2">
                                                    <div class="avatar-sm mx-auto">
                                                        <div class="avatar-title font-size-18 bg-soft-primary text-primary rounded-circle">
                                                            <i class="bx bxs-user-circle"></i>
                                                        </div>
                                                    </div>
                                                    <h5 class="font-size-11 text-uppercase text-truncate mt-3 mb-0"><a href="#" class="text-body stretched-link" data-bs-toggle="modal" data-bs-target=".contactModal">Contacts</a></h5>
                                                </div>
                                            </div>

                                            <div class="swiper-slide d-block d-sm-none">
                                                <div class="text-center px-2">
                                                    <div class="avatar-sm mx-auto">
                                                        <div class="avatar-title font-size-18 bg-soft-primary text-primary rounded-circle">
                                                            <i class="bx bx-microphone"></i>
                                                        </div>
                                                    </div>
                                                    <h5 class="font-size-11 text-uppercase text-truncate mt-3 mb-0"><a href="#" class="text-body stretched-link">Audio</a></h5>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="replyCard">
                        <div class="card mb-0">
                            <div class="card-body py-3">
                                <div class="replymessage-block mb-0 d-flex align-items-start">
                                    <div class="flex-grow-1">
                                        <h5 class="conversation-name"></h5>
                                        <p class="mb-0"></p>
                                    </div>
                                    <div class="flex-shrink-0">
                                        <button type="button" id="close_toggle" class="btn btn-sm btn-link mt-n2 me-n3 font-size-18">
                                            <i class="bx bx-x align-middle"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- end chat input section -->
           @endif
        </div>
        <!-- end chat conversation section -->

        <!-- start User profile detail sidebar -->
        @if ($group)
            @if ($group->isUserType())
                @livewire('user-chat-user-profile-details', compact('user'), key('user-'.$user['id']))
            @endif

            @if ($group->isGroupType())
                @livewire('user-chat-group-profile-details', compact('group'), key('group-'.$group['id']))
            @endif
        @endif
        <!-- end User profile detail sidebar -->
    </div>
    <!-- end user chat content -->
</div>
