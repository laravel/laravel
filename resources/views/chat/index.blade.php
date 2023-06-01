@section('title', 'Chat')

@push('styles')
    <!-- glightbox css -->
    @vite('resources/assets/libs/glightbox/glightbox.css')

    <!-- swiper css -->
    @vite('resources/assets/libs/swiper/swiper-bundle.css')

    <!-- Styles -->
    @livewireStyles
@endpush

@push('scripts')
    <!-- glightbox js -->
    @vite('resources/assets/libs/glightbox/glightbox.js')

    <!-- Swiper JS -->
    @vite('resources/assets/libs/swiper/swiper-bundle.js')

    <!-- fg-emoji-picker JS -->
    @vite('resources/assets/libs/fg-emoji-picker/fgEmojiPicker.js')

    <!-- page init -->
    @vite('resources/assets/js/pages/index.init.js')

    <!-- Scripts -->
    @vite('resources/js/app.js')

    @livewireScripts
@endpush

<x-chat-layout>
    <div class="layout-wrapper d-lg-flex"
         x-data="{ showUserChat: false }">

        <!-- Start left sidebar-menu -->
        @include('chat.partials.sidebar-menu')
        <!-- end left sidebar-menu -->

        <!-- start chat-leftsidebar -->
        <div class="chat-leftsidebar">

            <div class="tab-content">
                <!-- Start Profile tab-pane -->
                <div class="tab-pane" id="pills-user" role="tabpanel" aria-labelledby="pills-user-tab">
                    @livewire('tabpane-profile')
                </div>
                <!-- End Profile tab-pane -->

                <!-- Start chats tab-pane -->
                <div class="tab-pane show active" id="pills-chat" role="tabpanel" aria-labelledby="pills-chat-tab">
                    @livewire('tabpane-chats')
                </div>
                <!-- End chats tab-pane -->

                <!-- Start contacts tab-pane -->
                <div class="tab-pane" id="pills-contacts" role="tabpanel" aria-labelledby="pills-contacts-tab">
                    @livewire('tabpane-contacts')
                </div>
                <!-- End contacts tab-pane -->

                <!-- Start calls tab-pane -->
                <div class="tab-pane" id="pills-calls" role="tabpanel" aria-labelledby="pills-calls-tab">
                    @livewire('tabpane-calls')
                </div>
                <!-- End calls tab-pane -->

                <!-- Start bookmark tab-pane -->
                <div class="tab-pane" id="pills-bookmark" role="tabpanel" aria-labelledby="pills-bookmark-tab">
                    @livewire('tabpane-bookmark')
                </div>
                <!-- End bookmark tab-pane -->

                <!-- Start settings tab-pane -->
                <div class="tab-pane" id="pills-setting" role="tabpanel" aria-labelledby="pills-setting-tab">
                    @livewire('tabpane-settings')
                </div>
                <!-- End settings tab-pane -->
            </div>
            <!-- end tab content -->
        </div>
        <!-- end chat-leftsidebar -->

        <!-- Start User chat -->
        <div class="user-chat w-100 overflow-hidden"
             :class="{ 'user-chat-show': showUserChat }">
            <div class="user-chat-overlay"></div>

            @livewire('user-chat-content')
        </div>
        <!-- End User chat -->

        @include('chat.partials.modals')
        <!-- <video id="video" autoplay="true">

        </video>     -->
    </div>
    <!-- end  layout wrapper -->
</x-chat-layout>
