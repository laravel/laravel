<div>
    <!-- Start Contact content -->
    <div>
        <div class="px-4 pt-4">
            <div class="d-flex align-items-start">
                <div class="flex-grow-1">
                    <h4 class="mb-4">Contacts</h4>
                </div>
                <div class="flex-shrink-0">
                    <div data-bs-toggle="tooltip" data-bs-trigger="hover" data-bs-placement="bottom" title="Add Contact">

                        <!-- Button trigger modal -->
                        <button type="button" class="btn btn-soft-primary btn-sm" data-bs-toggle="modal" data-bs-target="#addContact-exampleModal">
                            <i class="bx bx-plus"></i>
                        </button>
                    </div>
                </div>
            </div>

            <form>
                <div class="input-group mb-4">
                    <input type="text" class="form-control bg-light border-0 pe-0" id="searchContact" onkeyup="searchContacts()" placeholder="Search Contacts.." aria-label="Search Contacts..."
                    aria-describedby="button-searchcontactsaddon" autocomplete="off">
                    <button class="btn btn-light" type="button" id="button-searchcontactsaddon"><i class='bx bx-search align-middle'></i></button>
                </div>
            </form>
        </div>
        <!-- end p-4 -->

        <div class="chat-message-list chat-group-list" data-simplebar >
            <div class="sort-contact">
                @foreach($groups as $group => $users)
                    <div class="mt-3">
                        <div class="contact-list-title">{{ $group }}</div>
                        <ul id="contact-sort-A" class="list-unstyled contact-list">
                            @foreach($users as $user)
                                <li>
                                    <div class="d-flex align-items-center">
                                        <div wire:click="$emitTo('user-chat-conversation-list', 'userSelected', {{ json_encode($user) }})" class="flex-shrink-0 me-2">
                                            <div class="avatar-xs">
                                                <img src="{{ $user['profile_photo_url'] }}" class="img-fluid rounded-circle" alt="{{ $user['name'] }}">
                                            </div>
                                        </div>
                                        <div wire:click="$emitTo('user-chat-conversation-list', 'userSelected', {{ json_encode($user) }})" class="flex-grow-1">
                                            <h5 class="font-size-14 m-0">{{ $user['name'] }}</h5>
                                        </div>
                                        <div class="flex-shrink-0">
                                            <div class="dropdown">
                                                <a href="#" class="text-muted dropdown-toggle" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                    <i class="bx bx-dots-vertical-rounded align-middle"></i>
                                                </a>
                                                <div class="dropdown-menu dropdown-menu-end">
                                                    <a class="dropdown-item d-flex align-items-center justify-content-between" href="#">Edit <i class="bx bx-pencil ms-2 text-muted"></i></a>
                                                    <a class="dropdown-item d-flex align-items-center justify-content-between" href="#">Block <i class="bx bx-block ms-2 text-muted"></i></a>
                                                    <a class="dropdown-item d-flex align-items-center justify-content-between" href="#">Remove <i class="bx bx-trash ms-2 text-muted"></i></a>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                @endforeach
            </div>
        </div>
        <!-- end contact lists -->
    </div>
    <!-- Start Contact content -->
</div>
