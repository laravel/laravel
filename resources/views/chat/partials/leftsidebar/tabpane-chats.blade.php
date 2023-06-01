<div>
    <!-- Start chats content -->
    <div>
        <div class="px-4 pt-4">
            <div class="d-flex align-items-start">
                <div class="flex-grow-1">
                    <h4 class="mb-4">Chats</h4>
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
                <div class="input-group mb-3">
                    <input type="text" class="form-control bg-light border-0 pe-0" id="serachChatUser" onkeyup="searchUser()" placeholder="Search here.."
                    aria-label="Example text with button addon" aria-describedby="searchbtn-addon" autocomplete="off">
                    <button class="btn btn-light" type="button" id="searchbtn-addon"><i class='bx bx-search align-middle'></i></button>
                </div>
            </form>

        </div> <!-- .p-4 -->

        <div class="chat-room-list" data-simplebar>
            <!-- Start chat-message-list -->
            <h5 class="mb-3 px-4 mt-4 font-size-11 text-muted text-uppercase">Favourites</h5>

            <div class="chat-message-list">

                <ul class="list-unstyled chat-list chat-user-list" id="favourite-users">
                </ul>
            </div>

            <div class="d-flex align-items-center px-4 mt-5 mb-2">
                <div class="flex-grow-1">
                    <h4 class="mb-0 font-size-11 text-muted text-uppercase">Direct Messages</h4>
                </div>
                <div class="flex-shrink-0">
                    <div data-bs-toggle="tooltip" data-bs-trigger="hover" data-bs-placement="bottom" title="New Message">

                        <!-- Button trigger modal -->
                        <button type="button" class="btn btn-soft-primary btn-sm" data-bs-toggle="modal" data-bs-target=".contactModal">
                            <i class="bx bx-plus"></i>
                        </button>
                    </div>
                </div>
            </div>

            <div class="chat-message-list">

                <ul class="list-unstyled chat-list chat-user-list" id="usersList"
                    x-data="{ selectedUser: 0 }">
                    @foreach($users as $user)
                        @php($countUnread = $user->countUnread())
                        <li id="contact-id-{{ $user['id'] }}" data-name="favorite" :class="{ 'active': selectedUser == {{ $user['id'] }} }">
                            <a wire:click="$emitTo('user-chat-content', 'userSelected', {{ $user }})"
                               @click="selectedUser = {{ $user['id'] }}; showUserChat = true"
                               href="javascript: void(0);" class="@if($countUnread > 0) unread-msg-user @endif">
                                <div class="d-flex align-items-center">
                                    <div class="chat-user-img online align-self-center me-2 ms-0">
                                        <img src="{{ $user['profile_photo_url'] }}" class="rounded-circle avatar-xs" alt="{{ $user['name'] }}">
                                        @if ($user['status'] != \App\Models\User::STATUS_INVISIBLE)
                                            <span class="user-status {{ $user->getBGColor() }}"></span>
                                        @endif
                                    </div>
                                    <div class="overflow-hidden">
                                        <p class="text-truncate mb-0">{{ $user['name'] }}</p>
                                    </div>
                                    @if ($countUnread > 0)
                                        <div class="ms-auto"><span class="badge badge-soft-dark rounded p-1">{{ $countUnread }}</span></div>
                                    @endif
                                </div>
                            </a>
                        </li>
                    @endforeach
                </ul>
            </div>

            <div class="d-flex align-items-center px-4 mt-5 mb-2">
                <div class="flex-grow-1">
                    <h4 class="mb-0 font-size-11 text-muted text-uppercase">Channels</h4>
                </div>
                <div class="flex-shrink-0">
                    <div data-bs-toggle="tooltip" data-bs-trigger="hover" data-bs-placement="bottom" title="Create group">

                        <!-- Button trigger modal -->
                        <button type="button" class="btn btn-soft-primary btn-sm" data-bs-toggle="modal" data-bs-target="#addgroup-exampleModal">
                            <i class="bx bx-plus"></i>
                        </button>
                    </div>
                </div>
            </div>

            <div class="chat-message-list">

                <ul class="list-unstyled chat-list chat-user-list mb-3" id="channelList">

                </ul>
            </div>
            <!-- End chat-message-list -->
        </div>

    </div>
    <!-- Start chats content -->

    <!-- Start add group Modal -->
    <div class="modal fade" id="addgroup-exampleModal" tabindex="-1" role="dialog" aria-labelledby="addgroup-exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
            <div class="modal-content modal-header-colored shadow-lg border-0">
                <div class="modal-header">
                    <h5 class="modal-title text-white font-size-16" id="addgroup-exampleModalLabel">Create New Group</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close">
                    </button>
                </div>
                <div class="modal-body p-4">
                    <form>
                        <div class="mb-4">
                            <label for="addgroupname-input" class="form-label">Group Name</label>
                            <input type="text" class="form-control" id="addgroupname-input" placeholder="Enter Group Name">
                        </div>
                        <div class="mb-4">
                            <label class="form-label">Group Members</label>
                            <div class="mb-3">
                                <button class="btn btn-light btn-sm" type="button" data-bs-toggle="collapse" data-bs-target="#groupmembercollapse" aria-expanded="false" aria-controls="groupmembercollapse">
                                    Select Members
                                </button>
                            </div>

                            <div class="collapse" id="groupmembercollapse">
                                <div class="card border">
                                    <div class="card-header">
                                        <h5 class="font-size-15 mb-0">Contacts</h5>
                                    </div>
                                    <div class="card-body p-2">
                                        <div data-simplebar style="max-height: 150px;">
                                            <div>
                                                <div class="contact-list-title">
                                                    A
                                                </div>

                                                <ul class="list-unstyled contact-list">
                                                    <li>
                                                        <div class="form-check">
                                                            <input type="checkbox" class="form-check-input" id="memberCheck1" checked>
                                                            <label class="form-check-label" for="memberCheck1">Albert Rodarte</label>
                                                        </div>
                                                    </li>

                                                    <li>
                                                        <div class="form-check">
                                                            <input type="checkbox" class="form-check-input" id="memberCheck2">
                                                            <label class="form-check-label" for="memberCheck2">Allison Etter</label>
                                                        </div>
                                                    </li>
                                                </ul>
                                            </div>

                                            <div>
                                                <div class="contact-list-title">
                                                    C
                                                </div>

                                                <ul class="list-unstyled contact-list">
                                                    <li>
                                                        <div class="form-check">
                                                            <input type="checkbox" class="form-check-input" id="memberCheck3">
                                                            <label class="form-check-label" for="memberCheck3">Craig Smiley</label>
                                                        </div>
                                                    </li>

                                                </ul>
                                            </div>

                                            <div>
                                                <div class="contact-list-title">
                                                    D
                                                </div>

                                                <ul class="list-unstyled contact-list">
                                                    <li>
                                                        <div class="form-check">
                                                            <input type="checkbox" class="form-check-input" id="memberCheck4">
                                                            <label class="form-check-label" for="memberCheck4">Daniel Clay</label>
                                                        </div>
                                                    </li>

                                                </ul>
                                            </div>

                                            <div>
                                                <div class="contact-list-title">
                                                    I
                                                </div>

                                                <ul class="list-unstyled contact-list">
                                                    <li>
                                                        <div class="form-check">
                                                            <input type="checkbox" class="form-check-input" id="memberCheck5">
                                                            <label class="form-check-label" for="memberCheck5">Iris Wells</label>
                                                        </div>
                                                    </li>

                                                </ul>
                                            </div>

                                            <div>
                                                <div class="contact-list-title">
                                                    J
                                                </div>

                                                <ul class="list-unstyled contact-list">
                                                    <li>
                                                        <div class="form-check">
                                                            <input type="checkbox" class="form-check-input" id="memberCheck6">
                                                            <label class="form-check-label" for="memberCheck6">Juan Flakes</label>
                                                        </div>
                                                    </li>

                                                    <li>
                                                        <div class="form-check">
                                                            <input type="checkbox" class="form-check-input" id="memberCheck7">
                                                            <label class="form-check-label" for="memberCheck7">John Hall</label>
                                                        </div>
                                                    </li>

                                                    <li>
                                                        <div class="form-check">
                                                            <input type="checkbox" class="form-check-input" id="memberCheck8">
                                                            <label class="form-check-label" for="memberCheck8">Joy Southern</label>
                                                        </div>
                                                    </li>

                                                </ul>
                                            </div>

                                            <div>
                                                <div class="contact-list-title">
                                                    M
                                                </div>

                                                <ul class="list-unstyled contact-list">
                                                    <li>
                                                        <div class="form-check">
                                                            <input type="checkbox" class="form-check-input" id="memberCheck9">
                                                            <label class="form-check-label" for="memberCheck9">Michael Hinton</label>
                                                        </div>
                                                    </li>

                                                    <li>
                                                        <div class="form-check">
                                                            <input type="checkbox" class="form-check-input" id="memberCheck10">
                                                            <label class="form-check-label" for="memberCheck10">Mary Farmer</label>
                                                        </div>
                                                    </li>

                                                </ul>
                                            </div>

                                            <div>
                                                <div class="contact-list-title">
                                                    P
                                                </div>

                                                <ul class="list-unstyled contact-list">
                                                    <li>
                                                        <div class="form-check">
                                                            <input type="checkbox" class="form-check-input" id="memberCheck11">
                                                            <label class="form-check-label" for="memberCheck11">Phillis Griffin</label>
                                                        </div>
                                                    </li>

                                                </ul>
                                            </div>

                                            <div>
                                                <div class="contact-list-title">
                                                    R
                                                </div>

                                                <ul class="list-unstyled contact-list">
                                                    <li>
                                                        <div class="form-check">
                                                            <input type="checkbox" class="form-check-input" id="memberCheck12">
                                                            <label class="form-check-label" for="memberCheck12">Rocky Jackson</label>
                                                        </div>
                                                    </li>

                                                </ul>
                                            </div>

                                            <div>
                                                <div class="contact-list-title">
                                                    S
                                                </div>

                                                <ul class="list-unstyled contact-list">
                                                    <li>
                                                        <div class="form-check">
                                                            <input type="checkbox" class="form-check-input" id="memberCheck13">
                                                            <label class="form-check-label" for="memberCheck13">Simon Velez</label>
                                                        </div>
                                                    </li>

                                                </ul>
                                            </div>
                                        </div>
                                    </div>

                                </div>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="addgroupdescription-input" class="form-label">Description</label>
                            <textarea class="form-control" id="addgroupdescription-input" rows="3" placeholder="Enter Description"></textarea>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-link" data-bs-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary">Create Groups</button>
                </div>
            </div>
        </div>
    </div>
    <!-- End add group Modal -->
</div>
