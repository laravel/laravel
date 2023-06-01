<div>
    <form id="chatinput-form" enctype="multipart/form-data"
          wire:submit.prevent="sendMessage">
        <div class="row g-0 align-items-center">
            <div class="file_Upload"></div>
            <div class="col-auto">
                <div class="chat-input-links me-md-2">
                    <div class="links-list-item" data-bs-toggle="tooltip" data-bs-trigger="hover" data-bs-placement="top" title="More">
                        <button type="button" class="btn btn-link text-decoration-none btn-lg waves-effect" data-bs-toggle="collapse" data-bs-target="#chatinputmorecollapse" aria-expanded="false" aria-controls="chatinputmorecollapse">
                            <i class="bx bx-dots-horizontal-rounded align-middle"></i>
                        </button>
                    </div>
                    <div class="links-list-item" data-bs-toggle="tooltip" data-bs-trigger="hover" data-bs-placement="top" title="Emoji">
                        <button type="button" class="btn btn-link text-decoration-none btn-lg waves-effect emoji-btn" id="emoji-btn">
                            <i class="bx bx-smile align-middle"></i>
                        </button>
                    </div>
                </div>
            </div>
            <div class="col">
                <div class="position-relative">
                    <div class="chat-input-feedback">
                        Please Enter a Message
                    </div>
                    <input autocomplete="off" type="text" class="form-control form-control-lg chat-input" autofocus id="chat-input" placeholder="Type your message..."
                           wire:model.defer="content">
                </div>
            </div>
            <div class="col-auto">
                <div class="chat-input-links ms-2 gap-md-1">
                    <div class="links-list-item d-none d-sm-block"  data-bs-container=".chat-input-links" data-bs-toggle="popover" data-bs-trigger="focus" data-bs-html="true"  data-bs-placement="top"
                         data-bs-content="<div class='loader-line'><div class='line'></div><div class='line'></div><div class='line'></div><div class='line'></div><div class='line'></div></div>">
                        <button type="button" class="btn btn-link text-decoration-none btn-lg waves-effect" onclick="audioPermission()">
                            <i class="bx bx-microphone align-middle"></i>
                        </button>
                    </div>
                    <div class="links-list-item">
                        <button type="submit" class="btn btn-primary btn-lg chat-send waves-effect waves-light"  data-bs-toggle="collapse" data-bs-target=".chat-input-collapse1.show">
                            <i class="bx bxs-send align-middle" id="submit-btn"></i>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>
