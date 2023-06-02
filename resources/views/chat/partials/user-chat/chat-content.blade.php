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

                        @livewire('user-chat-input', compact('group'), key('input-group-'.$group['id']))
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
