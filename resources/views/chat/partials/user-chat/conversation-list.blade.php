<div>
    <ul class="list-unstyled chat-conversation-list" id="users-conversation">
        @foreach($messages as $message)
            <li class="chat-list @if (auth()->id() == $message['user_id']) right @else left @endif" id="{{ $message['id'] }}">
                <div class="conversation-list">
                    @if (auth()->id() != $message['user_id'])
                        <div class="chat-avatar">
                            <img src="{{ $message['user']['profile_photo_url'] }}" alt="{{ $message['user']['name'] }}">
                        </div>
                    @endif
                    <div class="user-chat-content">
                        <div class="ctext-wrap">
                            <div class="ctext-wrap-content" id="{{ $message['id'] }}">
                                <p class="mb-0 ctext-content">{!! $message['content'] !!}</p></div>
                            <div class="dropdown align-self-start message-box-drop">
                                <a class="dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                    <i class="ri-more-2-fill"></i>
                                </a>
                                <div class="dropdown-menu">
                                    <a class="dropdown-item d-flex align-items-center justify-content-between reply-message" href="#" id="reply-message-0" data-bs-toggle="collapse" data-bs-target=".replyCollapse">Reply <i class="bx bx-share ms-2 text-muted"></i></a>
                                    <a class="dropdown-item d-flex align-items-center justify-content-between" href="#" data-bs-toggle="modal" data-bs-target=".forwardModal">Forward <i class="bx bx-share-alt ms-2 text-muted"></i></a>
                                    <a class="dropdown-item d-flex align-items-center justify-content-between copy-message" href="#" id="copy-message-0">Copy <i class="bx bx-copy text-muted ms-2"></i></a>
                                    <a class="dropdown-item d-flex align-items-center justify-content-between" href="#">Bookmark <i class="bx bx-bookmarks text-muted ms-2"></i></a>
                                    @if ($message['user_id'] != auth()->id())
                                        <a class="dropdown-item d-flex align-items-center justify-content-between" href="#">Mark as Unread <i class="bx bx-message-error text-muted ms-2"></i></a>
                                    @endif
                                    @if ($message['user_id'] == auth()->id())
                                        <a class="dropdown-item d-flex align-items-center justify-content-between delete-item" href="#">Delete <i class="bx bx-trash text-muted ms-2"></i></a>
                                    @endif
                                </div>
                            </div>
                        </div>
                        <div class="conversation-name">
                            <small class="text-muted time">{{ $message['created_at'] }}</small>
                            <span class="text-success check-message-icon"><i class="bx bx-check-double"></i></span>
                        </div>
                    </div>
                </div>
            </li>
        @endforeach
    </ul>
</div>
