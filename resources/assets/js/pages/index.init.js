/*
Template Name: Doot - Responsive Bootstrap 5 Chat App
Author: Themesbrand
Website: https://themesbrand.com/
Contact: themesbrand@gmail.com
File: Index init js
*/

(function () {
  var isreplyMessage = false;
  var currentChatId = "users-chat";
  var dummyImage = "assets/images/users/user-dummy-img.jpg";

  var currentSelectedChat = "users";
  var url = window.location.origin + "/assets/js/dir/";
  var usersList = "";
  var userChatId = 1;
  document.getElementById("copyClipBoard").style.display = "none";
  document.getElementById("copyClipBoardChannel").style.display = "none";

  // chat user responsive hide show
  function toggleSelected() {
    var userChatElement = document.getElementsByClassName("user-chat");
    document.querySelectorAll(".chat-user-list li a").forEach(function (item) {
      item.addEventListener("click", function (event) {
        userChatElement.forEach(function (elm) {
          elm.classList.add("user-chat-show");
        });

        // chat user list link active
        var chatUserList = document.querySelector(".chat-user-list li.active");
        if (chatUserList) chatUserList.classList.remove("active");
        this.parentNode.classList.add("active");
      });
    });

    document.querySelectorAll(".sort-contact ul li").forEach(function (item2) {
      item2.addEventListener("click", function (event) {
        userChatElement.forEach(function (elm) {
          elm.classList.add("user-chat-show");
        });
      });
    });
    // user-chat-remove
    document.querySelectorAll(".user-chat-remove").forEach(function (item) {
      item.addEventListener("click", function (event) {
        userChatElement.forEach(function (elm) {
          elm.classList.remove("user-chat-show");
        });
      });
    });
  }

  // single to channel and channel to single chat conversation
  function chatSwap() {
    document.querySelectorAll("#favourite-users li, #usersList li") &&
      document
        .querySelectorAll("#favourite-users li, #usersList li")
        .forEach(function (item) {
          item.addEventListener("click", function (event) {
            currentSelectedChat = "users";
            updateSelectedChat();
            currentChatId = "users-chat";
            var contactId = item.getAttribute("id");
            var username = item.querySelector(".text-truncate").innerHTML;

            document.querySelector(".user-profile-sidebar .user-name").innerHTML = username;
            var contactImagesWithName = document.getElementById("users-chat");
            contactImagesWithName.querySelector(".text-truncate .user-profile-show").innerHTML = username;
            document.querySelector(".user-profile-desc .text-truncate").innerHTML = username;
            document.querySelector(".audiocallModal .text-truncate").innerHTML = username;
            document.querySelector(".videocallModal .text-truncate").innerHTML = username;
            var img = document.getElementById(contactId).querySelector(".avatar-xs").getAttribute("src");

            if (img) {
              document.querySelector(".user-own-img .avatar-sm").setAttribute("src", img);
              document.querySelector(".user-profile-sidebar .profile-img").setAttribute("src", img);
              document.querySelector(".audiocallModal .img-thumbnail").setAttribute("src", img);
              document.querySelector(".videocallModal .videocallModal-bg").setAttribute("src", img);
            } else {
              document.querySelector(".user-own-img .avatar-sm").setAttribute("src", dummyImage);
              document.querySelector(".user-profile-sidebar .profile-img").setAttribute("src", dummyImage);
              document.querySelector(".audiocallModal .img-thumbnail").setAttribute("src", dummyImage);
              document.querySelector(".videocallModal .videocallModal-bg").setAttribute("src", dummyImage);
            }

            var chatImg = item.querySelector(".avatar-xs").getAttribute("src");
            var conversationImg = document.getElementById("users-conversation");
            conversationImg.querySelectorAll(".left .chat-avatar").forEach(function (item3) {
                if (chatImg) {
                  item3.querySelector("img").setAttribute("src", chatImg);
                } else {
                  item3.querySelector("img").setAttribute("src", dummyImage);
                }
              });
            window.stop();
          });
        });

    document.querySelectorAll("#channelList li").forEach(function (item) {
      item.addEventListener("click", function (event) {
        currentChatId = "channel-chat";
        currentSelectedChat = "channel";
        updateSelectedChat();
        var channelId = item.getAttribute("id");
        var channelName = item.querySelector(".text-truncate").innerHTML;
        var changeChannelName = document.getElementById("channel-chat");

        changeChannelName.querySelector(".text-truncate .user-profile-show").innerHTML = channelName;
        document.querySelector(".user-profile-desc .text-truncate").innerHTML = channelName;
        document.querySelector(".audiocallModal .text-truncate").innerHTML = channelName;
        document.querySelector(".videocallModal .text-truncate").innerHTML = channelName;
        document.querySelector(".user-profile-sidebar .user-name").innerHTML = channelName;

        var channelImg = document.getElementById(channelId).querySelector(".avatar-xs").getAttribute("src");

        if (channelImg) {
          document.querySelector(".user-own-img .avatar-sm").setAttribute("src", channelImg);
          document.querySelector(".user-profile-sidebar .profile-img").setAttribute("src", channelImg);
          document.querySelector(".audiocallModal .img-thumbnail").setAttribute("src", channelImg);
          document.querySelector(".videocallModal .videocallModal-bg").setAttribute("src", channelImg);
        } else {
          document.querySelector(".user-own-img .avatar-sm").setAttribute("src", dummyImage);
          document.querySelector(".user-profile-sidebar .profile-img").setAttribute("src", dummyImage);
          document.querySelector(".audiocallModal .img-thumbnail").setAttribute("src", dummyImage);
          document.querySelector(".videocallModal .videocallModal-bg").setAttribute("src", dummyImage);
        }
      });
    });
  }

  //user list by json
  var getJSON = function (jsonurl, callback) {
    var xhr = new XMLHttpRequest();
    xhr.open("GET", url + jsonurl, true);
    xhr.responseType = "json";
    xhr.onload = function () {
      var status = xhr.status;
      if (status === 200) {
        callback(null, xhr.response);
      } else {
        callback(status, xhr.response);
      }
    };
    xhr.send();
  };

  getJSON("users.json", function (err, data) {
    if (err !== null) {
      console.log("Something went wrong: " + err);
    } else {
      // set favourite users list
      var fav = data[0].favorites;
      fav.forEach(function (user, index) {
        var profile = user.profile
          ? '<img src="' + user.profile + '" class="rounded-circle avatar-xs" alt=""><span class="user-status"></span>'
          : '<div class="avatar-xs"><span class="avatar-title rounded-circle bg-primary text-white"><span class="username">JP</span><span class="user-status"></span></span></div>';

        var isMessageCount = user.messagecount
          ? '<div class="ms-auto"><span class="badge badge-soft-dark rounded p-1">' + user.messagecount + "</span></div>"
          : "";
        var messageCount = user.messagecount
          ? '<a href="javascript: void(0);" class="unread-msg-user">'
          : '<a href="javascript: void(0);">';

        var activeClass = user.id === 1 ? "active" : "";
        document.getElementById("favourite-users").innerHTML += '<li id="contact-id-' + user.id + '" data-name="favorite" class="' + activeClass + '">\
                  ' + messageCount + ' \
                      <div class="d-flex align-items-center">\
                          <div class="chat-user-img online align-self-center me-2 ms-0">\
                              ' + profile + '\
                          </div>\
                          <div class="overflow-hidden">\
                              <p class="text-truncate mb-0">' + user.name + "</p>\
                          </div>\
                          " + isMessageCount + "\
                      </div>\
                  </a>\
              </li>";
      });

      // set users message list
      var users = data[0].users;
      users.forEach(function (userData, index) {
        var isUserProfile = userData.profile
          ? '<img src="' + userData.profile + '" class="rounded-circle avatar-xs" alt=""><span class="user-status"></span>'
          : '<div class="avatar-xs"><span class="avatar-title rounded-circle bg-primary text-white"><span class="username">JL</span><span class="user-status"></span></span></div>';

        var isMessageCount = userData.messagecount
          ? '<div class="ms-auto"><span class="badge badge-soft-dark rounded p-1">' + userData.messagecount + "</span></div>"
          : "";
        var messageCount = userData.messagecount
          ? '<a href="javascript: void(0);" class="unread-msg-user">'
          : '<a href="javascript: void(0);">';
        document.getElementById("usersList").innerHTML +=
          '<li id="contact-id-' + userData.id + '" data-name="direct-message">\
                  ' + messageCount + ' \
                  <div class="d-flex align-items-center">\
                      <div class="chat-user-img online align-self-center me-2 ms-0">\
                          ' + isUserProfile + '\
                      </div>\
                      <div class="overflow-hidden">\
                          <p class="text-truncate mb-0">' + userData.name + "</p>\
                      </div>\
                      " + isMessageCount + "\
                  </div>\
              </a>\
          </li>";
      });

      // set channels list
      var channelsData = data[0].channels;
      channelsData.forEach(function (isChannel, index) {
        var isMessage = isChannel.messagecount
          ? '<div class="flex-shrink-0 ms-2"><span class="badge badge-soft-dark rounded p-1">' + isChannel.messagecount + "</span></div>"
          : "";
        var messageCount = isChannel.messagecount
          ? '<a href="javascript: void(0);" class="unread-msg-user">'
          : '<a href="javascript: void(0);">';
        document.getElementById("channelList").innerHTML += '<li id="contact-id-' + isChannel.id + '" data-name="channel">\
                ' + messageCount + ' \
                    <div class="d-flex align-items-center">\
                        <div class="flex-shrink-0 avatar-xs me-2">\
                            <span class="avatar-title rounded-circle bg-soft-light text-dark">#</span>\
                        </div>\
                        <div class="flex-grow-1 overflow-hidden">\
                            <p class="text-truncate mb-0">' + isChannel.name + "</p>\
                        </div>\
                        <div>" + isMessage + "</div>\
                        </div>\
                </a>\
            </li>";
      });
    }
    toggleSelected();
    chatSwap();
  });

  //CallList userDetails
  function callsList() {
    document.querySelectorAll("#callList li").forEach(function (item) {
      item.addEventListener("click", function (event) {
        var callsId = item.getAttribute("id");
        var callUser = item.querySelector(".text-truncate").innerHTML;
        document.querySelector(".videocallModal .text-truncate").innerHTML = callUser;
        document.querySelector(".audiocallModal .text-truncate").innerHTML = callUser;

        var callImg = document.getElementById(callsId).querySelector(".avatar-xs").getAttribute("src");
        if (callImg) {
          document.querySelector(".audiocallModal .img-thumbnail").setAttribute("src", callImg);
          document.querySelector(".videocallModal .videocallModal-bg").setAttribute("src", callImg);
        } else {
          document.querySelector(".audiocallModal .img-thumbnail").setAttribute("src", dummyImage);
          document.querySelector(".videocallModal .videocallModal-bg").setAttribute("src", dummyImage);
        }
      });
    });
  }

  //Call list
  getJSON("callList.json", function (err, data) {
    if (err !== null) {
      console.log("Something went wrong: " + err);
    } else {
      callList = data;
      callList.forEach(function (calls, index) {
        var callIcon =
          calls.callVideo === true
            ? '<button type="button" class="btn btn-link p-0 font-size-20 stretched-link" data-bs-toggle="modal" data-bs-target=".videocallModal"><i class="' +
            calls.callTypeIcon + '"></i></button>'
            : '<button type="button" class="btn btn-link p-0 font-size-20 stretched-link" data-bs-toggle="modal" data-bs-target=".audiocallModal"><i class="' +
            calls.callTypeIcon + '"></i></button>';

        var profile = calls.profile
          ? '<img src="' + calls.profile + '" class="rounded-circle avatar-xs" alt="">'
          : '<div class="avatar-xs"><span class="avatar-title rounded-circle bg-danger text-white">RL</span></div>';
        document.getElementById("callList").innerHTML +=
        '<li id="calls-id-' + calls.id + '" >\
        <div class="d-flex align-items-center">\
        <div class="chat-user-img flex-shrink-0 me-2">\
            ' + profile + '\
        </div>\
            <div class="flex-grow-1 overflow-hidden">\
                <p class="text-truncate mb-0">' + calls.name + '</p>\
                <div class="text-muted font-size-12 text-truncate"><i class="' + calls.callArrowType + '"></i> ' + calls.dateTime + '</div>\
            </div>\
            <div class="flex-shrink-0 ms-3">\
                <div class="d-flex align-items-center gap-3">\
                    <div>\
                        <h5 class="mb-0 font-size-12 text-muted">' + calls.callTime + "</h5>\
                    </div>\
                    <div>\
                       " + callIcon + "\
                    </div>\
                </div>\
            </div>\
        </div>\
      </li>";
      });
    }
    callsList();
  });

  //Contact List dynamic Details
  function contactList() {
    document.querySelectorAll(".sort-contact ul li").forEach(function (item) {
      item.addEventListener("click", function (event) {
        currentSelectedChat = "users";
        updateSelectedChat();
        var contactName = item.querySelector("li .font-size-14").innerHTML;
        document.querySelector(".text-truncate .user-profile-show").innerHTML = contactName;
        document.querySelector(".user-profile-desc .text-truncate").innerHTML = contactName;
        document.querySelector(".audiocallModal .text-truncate").innerHTML = contactName;
        document.querySelector(".videocallModal .text-truncate").innerHTML = contactName;
        document.querySelector(".user-profile-sidebar .user-name").innerHTML = contactName;

        var contactImg = item.querySelector("li .align-items-center").querySelector(".avatar-xs .rounded-circle").getAttribute("src");
        if (contactImg) {
          document.querySelector(".user-own-img .avatar-sm").setAttribute("src", contactImg);
          document.querySelector(".user-profile-sidebar .profile-img").setAttribute("src", contactImg);
          document.querySelector(".audiocallModal .img-thumbnail").setAttribute("src", contactImg);
          document.querySelector(".videocallModal .videocallModal-bg").setAttribute("src", contactImg);
        } else {
          document.querySelector(".user-own-img .avatar-sm").setAttribute("src", dummyImage);
          document.querySelector(".user-profile-sidebar .profile-img").setAttribute("src", dummyImage);
          document.querySelector(".audiocallModal .img-thumbnail").setAttribute("src", dummyImage);
          document.querySelector(".videocallModal .videocallModal-bg").setAttribute("src", dummyImage);
        }
        var conversationImg = document.getElementById("users-conversation");
        conversationImg.querySelectorAll(".left .chat-avatar").forEach(function (item3) {
          if (contactImg) {
            item3.querySelector("img").setAttribute("src", contactImg);
          } else {
            item3.querySelector("img").setAttribute("src", dummyImage);
          }
        });
        window.stop();
      });
    });
  }

  // get contacts list
  getJSON("contacts.json", function (err, data) {
    if (err !== null) {
      console.log("Something went wrong: " + err);
    } else {
      usersList = data;
      data.sort(function (a, b) {
        return a.name.localeCompare(b.name);
      });
      // set favourite users list
      var msgHTML = "";
      var userNameCharAt = "";

      usersList.forEach(function (user, index) {
        var profile = user.profile
          ? '<img src="' + user.profile + '" class="img-fluid rounded-circle" alt="">'
          : '<span class="avatar-title rounded-circle bg-primary font-size-10">FP</span>';

        msgHTML = '<li>\
              <div class="d-flex align-items-center">\
                  <div class="flex-shrink-0 me-2">\
                      <div class="avatar-xs">\
                          ' + profile + '\
                      </div>\
                  </div>\
                  <div class="flex-grow-1">\
                      <h5 class="font-size-14 m-0" >' + user.name + '</h5>\
                  </div>\
                  <div class="flex-shrink-0">\
                      <div class="dropdown">\
                          <a href="#" class="text-muted dropdown-toggle" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">\
                              <i class="bx bx-dots-vertical-rounded align-middle"></i>\
                          </a>\
                          <div class="dropdown-menu dropdown-menu-end">\
                              <a class="dropdown-item d-flex align-items-center justify-content-between" href="#">Edit <i class="bx bx-pencil ms-2 text-muted"></i></a>\
                              <a class="dropdown-item d-flex align-items-center justify-content-between" href="#">Block <i class="bx bx-block ms-2 text-muted"></i></a>\
                              <a class="dropdown-item d-flex align-items-center justify-content-between" href="#">Remove <i class="bx bx-trash ms-2 text-muted"></i></a>\
                          </div>\
                      </div>\
                  </div>\
              </div>\
          </li>';
        var isSortContact =
          '<div class="mt-3" >\
              <div class="contact-list-title">' +user.name.charAt(0).toUpperCase() +'\
                </div>\
          <ul id="contact-sort-' +
          user.name.charAt(0) +
          '" class="list-unstyled contact-list" >';

        if (userNameCharAt != user.name.charAt(0)) {
          document.getElementsByClassName("sort-contact")[0].innerHTML += isSortContact;
        }
        document.getElementById("contact-sort-" + user.name.charAt(0)).innerHTML =
          document.getElementById("contact-sort-" + user.name.charAt(0)).innerHTML + msgHTML;
        userNameCharAt = user.name.charAt(0);
        +"</ul>" + "</div>";
      });
    }
    contactList();
    toggleSelected();
  });

  function updateSelectedChat() {
    if (currentSelectedChat == "users") {
      document.getElementById("channel-chat").style.display = "none";
      document.getElementById("users-chat").style.display = "block";
      getChatMessages(url + "chats.json");
    } else {
      document.getElementById("channel-chat").style.display = "block";
      document.getElementById("users-chat").style.display = "none";
      getChatMessages(url + "chats.json");
    }
  }
  updateSelectedChat();

  // Profile hide/show
  var userProfileSidebar = document.querySelector(".user-profile-sidebar");

  document.querySelectorAll(".user-profile-show").forEach(function (item) {
    item.addEventListener("click", function (event) {
      userProfileSidebar.classList.toggle("d-block");
    });
  });

  // chat conversation scroll
  window.addEventListener("DOMContentLoaded", function () {
    var conversationScroll = document.querySelector("#chat-conversation .simplebar-content-wrapper");
    conversationScroll.scrollTop = conversationScroll.scrollHeight;
  });

  // body click hide collapse
  var myCollapse = document.getElementById("chatinputmorecollapse");
  document.body.addEventListener("click", function () {
    var bsCollapse = new bootstrap.Collapse(myCollapse, {
      toggle: false,
    });
    bsCollapse.hide();
  });

  // chat conversation swiper
  if (myCollapse) {
    myCollapse.addEventListener("shown.bs.collapse", function () {
      initSwiper();
    });
  }

  function initSwiper() {
    var swiper = new Swiper(".chatinput-links", {
      slidesPerView: 3,
      spaceBetween: 30,
      breakpoints: {
        768: {
          slidesPerView: 4,
        },
        1024: {
          slidesPerView: 6,
        },
      },
    });
  }

  // contact modal list
  var contactModalList = document.querySelectorAll(
    ".contact-modal-list .contact-list li"
  );
  contactModalList.forEach(function (link) {
    link.addEventListener("click", function () {
      link.classList.toggle("selected");
    });
  });

  // Change conversation bg

  //Auto Focus curser to Text Box Area

  document.getElementById("favourite-users").onclick = function () {
    document.getElementById("chat-input").focus();
  };

  document.getElementById("usersList").onclick = function () {
    document.getElementById("chat-input").focus();
  };

  document.getElementById("channelList").onclick = function () {
    document.getElementById("chat-input").focus();
  };

  // Scroll to Bottom
  function scrollToBottom(id) {
    var simpleBar = document.getElementById(id).querySelector("#chat-conversation .simplebar-content-wrapper");
    var offsetHeight = document.getElementsByClassName("chat-conversation-list")[0] ? document.getElementById(id).getElementsByClassName("chat-conversation-list")[0].scrollHeight -
      window.innerHeight +250: 0;
    if (offsetHeight)
      simpleBar.scrollTo({ top: offsetHeight, behavior: "smooth" });
  }

  //add an eventListener to the from
  var chatForm = document.querySelector("#chatinput-form");
  var chatInput = document.querySelector("#chat-input");
  var itemList = document.querySelector(".chat-conversation-list");
  var chatInputFeedback = document.querySelector(".chat-input-feedback");

  function currentTime() {
    var ampm = new Date().getHours() >= 12 ? "pm" : "am";
    var hour =
      new Date().getHours() > 12
        ? new Date().getHours() % 12
        : new Date().getHours();
    var minute =
      new Date().getMinutes() < 10
        ? "0" + new Date().getMinutes()
        : new Date().getMinutes();
    if (hour < 10) {
      return "0" + hour + ":" + minute + " " + ampm;
    } else {
      return hour + ":" + minute + " " + ampm;
    }
  }
  setInterval(currentTime, 1000);

  var messageIds = 0;

  //Audio file
  var audiofilename;
  var audiofileSize;
  var audioFile = "";
  var afiling = [];
  var fileNumberAudio = 1;
  document
    .querySelector("#audiofile-input")
    .addEventListener("change", function () {
      var preview = document.querySelector(".file_Upload");
      audioFile = document.querySelector("#audiofile-input").files[0];

      // remove-audioFile

      var reader = new FileReader();
      reader.readAsDataURL(audioFile);
      if (preview) {
        preview.classList.add("show");
      }

      reader.addEventListener(
        "load",
        function () {
          // Array.push(preview).forEach((gallery, index) => {
          var filename = audioFile.name;
          var fileSize = Math.round(audioFile.size / 1000000).toFixed(2);    

          preview.innerHTML =
            '<div class="card p-2 border mb-2 audiofile_pre d-inline-block position-relative">\
            <div class="d-flex align-items-center">\
                <div class="flex-shrink-0 avatar-xs ms-1 me-3">\
                    <div class="avatar-title bg-soft-primary text-primary rounded-circle">\
                        <i class="bx bx-headphone"></i>\
                    </div>\
                </div>\
                <div class="flex-grow-1 overflow-hidden">\
                <h5 class="font-size-14 text-truncate mb-1">' +filename +'</h5>\
                  <input type="hidden" name="downloadaudiodata" value="' +audioFile +'"/>\
                        <p class="text-muted text-truncate font-size-13 mb-0">' +fileSize +'mb</p>\
                </div>\
                <div class="flex-shrink-0 ms-3">\
                    <div class="d-flex gap-2">\
                        <div>\
                        <i class="ri-close-line text-danger audioFile-remove"  id="remove-audioFile"></i>\
                        </div>\
                    </div>\
                </div>\
            </div>\
          </div>';
          audiofilename = filename;
          audiofileSize = fileSize;
          removeAudioFile();
          afiling[fileNumberAudio] = audioFile;
        },
        false
      );
      fileNumberAudio++;
    });

  var getAudioFiles = function (chatid3, newAdioItems, newAdioItemSize) {
    var newAdioItems = audiofilename;
    var newAdioItemSize = audiofileSize;
    messageIds++;
    var chatConList3 = document.getElementById(chatid3);
    var itemList3 = chatConList3.querySelector(".chat-conversation-list");
    if (newAdioItems != null) {
      itemList3.insertAdjacentHTML(
        "beforeend",

        '<li class="chat-list right" id="chat-list-' +
        messageIds +
        '" >\
          <div class="conversation-list">\
              <div class="user-chat-content">\
                  <div class="ctext-wrap">\
                      <div class="ctext-wrap-content">\
                          <div class="p-3 border-primary border rounded-3">\
                              <div class="d-flex align-items-center attached-file">\
                                  <div class="flex-shrink-0 avatar-sm me-3 ms-0 attached-file-avatar">\
                                      <div class="avatar-title bg-soft-primary text-primary rounded-circle font-size-20"><i class="bx bx-headphone"></i></div>\
                                  </div>\
                                  <div class="flex-grow-1 overflow-hidden">\
                                      <div class="text-start">\
                                          <h5 class="font-size-14 mb-1">' +newAdioItems +'</h5>\
                                          <p class="text-muted text-truncate font-size-13 mb-0">' +newAdioItemSize +'mb</p>\
                                  </div>\
                                  </div>\
                                  <div class="flex-shrink-0 ms-4">\
                                      <div class="d-flex gap-2 font-size-20 d-flex align-items-start">\
                                          <div>\
                                          <a href="#" class="text-muted download-file" data-id="' +fileNumberAudio +'" > <i class="bx bxs-download"></i> </a>\
                                          </div>\
                                      </div>\
                                  </div>\
                              </div>\
                          </div>\
                      </div>\
                      <div class="dropdown align-self-start message-box-drop">\
                          <a class="dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false"> <i class="ri-more-2-fill"></i> </a>\
                          <div class="dropdown-menu">\
                          <a class="dropdown-item d-flex align-items-center justify-content-between" href="#" data-bs-toggle="collapse" data-bs-target=".replyCollapse">Reply <i class="bx bx-share ms-2 text-muted"></i></a>\
                          <a class="dropdown-item d-flex align-items-center justify-content-between" href="#" data-bs-toggle="modal" data-bs-target=".forwardModal">Forward <i class="bx bx-share-alt ms-2 text-muted"></i></a>\
                          <a class="dropdown-item d-flex align-items-center justify-content-between copy-message" href="#" id="copy-message-' +messageIds +'">Copy <i class="bx bx-copy text-muted ms-2"></i></a>\
                          <a class="dropdown-item d-flex align-items-center justify-content-between" href="#">Bookmark <i class="bx bx-bookmarks text-muted ms-2"></i></a>\
                          <a class="dropdown-item d-flex align-items-center justify-content-between" href="#">Mark as Unread <i class="bx bx-message-error text-muted ms-2"></i></a>\
                           <a class="dropdown-item d-flex align-items-center justify-content-between delete-item" id="delete-item-' + messageIds +'" href="#">Delete <i class="bx bx-trash text-muted ms-2"></i></a>\
                      </div>\
                      </div>\
                      </div>\
                      <div class="conversation-name">\
                          <small class="text-muted time">' +currentTime() +'</small>\
                            <span class="text-success check-message-icon"><i class="bx bx-check"></i></span>\
                          </div>\
                        </div>\
                      </div>\
                    </li>'
      );
    }
    var newChatList = document.getElementById("chat-list-" + messageIds);
    newChatList.querySelectorAll(".delete-item").forEach(function (subitem) {
      subitem.addEventListener("click", function () {
        itemList.removeChild(newChatList);
      });
    });

    document.querySelectorAll(".download-file").forEach(function (subitem2) {
      subitem2.addEventListener("click", function (event) {
        event.preventDefault();

        var audiofiledataid = subitem2.getAttribute("data-id");

        if (
          !window.File ||
          !window.FileReader ||
          !window.FileList ||
          !window.Blob
        ) {
          alert("The File APIs are not fully supported in this browser.");
          return;
        }
        var blob = new Blob([filing[audiofiledataid]], {
          type: "application/mp3",
        });
        var link = document.createElement("a");
        link.href = window.URL.createObjectURL(blob);
        link.download = afiling[audiofiledataid]["name"];
        link.click();
      });
    });
    document.querySelector(".file_Upload ").classList.remove("show");
  };

  //Attached file Append
  var filename2;
  var filesize2;

  var file = "";
  var filing = [];
  var fileNumber = 1;

  document.querySelector("#attachedfile-input").addEventListener("change", function () {
    var preview = document.querySelector(".file_Upload");
    file = document.querySelector("#attachedfile-input").files[0];

    fr = new FileReader();

    fr.readAsDataURL(file);

    if (preview) {
      preview.classList.add("show");
    }

    fr.addEventListener(
      "load",
      function () {
        var filename = file.name;
        var fileSize = Math.round(file.size / 1000000).toFixed(2);

        preview.innerHTML =
          '<div class="card p-2 border attchedfile_pre d-inline-block position-relative">\
            <div class="d-flex align-items-center">\
                <div class="flex-shrink-0 avatar-xs ms-1 me-3">\
                    <div class="avatar-title bg-soft-primary text-primary rounded-circle">\
                        <i class="ri-attachment-2"></i>\
                    </div>\
                </div>\
                <div class="flex-grow-1 overflow-hidden">\
                <a href="" id="a"></a>\
                    <h5 class="font-size-14 text-truncate mb-1">' + filename + '</h5>\
                    <input type="hidden" name="downloaddata" value="' + file + '"/>\
                    <p class="text-muted text-truncate font-size-13 mb-0">' + fileSize + 'mb</p>\
                </div>\
                <div class="flex-shrink-0 align-self-start ms-3">\
                    <div class="d-flex gap-2">\
                        <div>\
                        <i class="ri-close-line text-muted attechedFile-remove"  id="remove-attechedFile"></i>\
                        </div>\
                    </div>\
                </div>\
            </div>\
          </div>';
        filename2 = filename;
        filesize2 = fileSize;
        filing[fileNumber] = file;
        removeAttachedFile();
      },
      false
    );
    fileNumber++;
  });

  var getAttachedFiles = function (
    chatid2,
    newAttchedItems,
    newAttchedItemSize
  ) {
    var newAttchedItems = filename2;
    var newAttchedItemSize = filesize2;
    messageIds++;
    var chatConList2 = document.getElementById(chatid2);
    var itemList2 = chatConList2.querySelector(".chat-conversation-list");

    if (newAttchedItems != null) {
      itemList2.insertAdjacentHTML(
        "beforeend",
        '<li class="chat-list right" id="chat-list-' +
        messageIds +
        '" >\
          <div class="conversation-list">\
              <div class="user-chat-content">\
                  <div class="ctext-wrap">\
                      <div class="ctext-wrap-content">\
                          <div class="p-3 border-primary border rounded-3">\
                              <div class="d-flex align-items-center attached-file">\
                                  <div class="flex-shrink-0 avatar-sm me-3 ms-0 attached-file-avatar">\
                                      <div class="avatar-title bg-soft-primary text-primary rounded-circle font-size-20"><i class="ri-attachment-2"></i></div>\
                                  </div>\
                                  <div class="flex-grow-1 overflow-hidden">\
                                      <div class="text-start">\
                                          <h5 class="font-size-14 mb-1">' +newAttchedItems +'</h5>\
                                          <p class="text-muted text-truncate font-size-13 mb-0">' +newAttchedItemSize +'mb</p>\
                                          <p class="text-muted text-truncate font-size-13 mb-0"></p>\
                                      </div>\
                                  </div>\
                                  <div class="flex-shrink-0 ms-4">\
                                      <div class="d-flex gap-2 font-size-20 d-flex align-items-start">\
                                          <div>\
                                              <a href="#" class="text-muted download-file" data-id="' +fileNumber +'"> <i class="bx bxs-download"></i> </a>\
                                          </div>\
                                      </div>\
                                  </div>\
                              </div>\
                          </div>\
                      </div>\
                      <div class="dropdown align-self-start message-box-drop">\
                          <a class="dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false"> <i class="ri-more-2-fill"></i> </a>\
                          <div class="dropdown-menu">\
                          <a class="dropdown-item d-flex align-items-center justify-content-between" href="#" data-bs-toggle="collapse" data-bs-target=".replyCollapse">Reply <i class="bx bx-share ms-2 text-muted"></i></a>\
                          <a class="dropdown-item d-flex align-items-center justify-content-between" href="#" data-bs-toggle="modal" data-bs-target=".forwardModal">Forward <i class="bx bx-share-alt ms-2 text-muted"></i></a>\
                          <a class="dropdown-item d-flex align-items-center justify-content-between copy-message" href="#" id="copy-message-' +messageIds +'">Copy <i class="bx bx-copy text-muted ms-2"></i></a>\
                          <a class="dropdown-item d-flex align-items-center justify-content-between" href="#">Bookmark <i class="bx bx-bookmarks text-muted ms-2"></i></a>\
                          <a class="dropdown-item d-flex align-items-center justify-content-between" href="#">Mark as Unread <i class="bx bx-message-error text-muted ms-2"></i></a>\
                          <a class="dropdown-item d-flex align-items-center justify-content-between delete-item" id="delete-item-' + messageIds +'" href="#">Delete <i class="bx bx-trash text-muted ms-2"></i></a>\
                      </div>\
                    </div>\
                  </div>\
                <div class="conversation-name">\
                    <small class="text-muted time">' +currentTime() +'</small>\
                      <span class="text-success check-message-icon"><i class="bx bx-check"></i></span>\
                    </div>\
                </div>\
              </div>\
            </li>'
      );
    }
    // remove File
    var newChatList = document.getElementById("chat-list-" + messageIds);
    newChatList.querySelectorAll(".delete-item").forEach(function (subitem) {
      subitem.addEventListener("click", function () {
        itemList.removeChild(newChatList);
      });
    });


    //Download attached file
    newChatList.querySelectorAll(".download-file").forEach(function (subitem1) {
      subitem1.addEventListener("click", function (event) {
        event.preventDefault();
        var dataid = subitem1.getAttribute("data-id");
        if (
          !window.File ||
          !window.FileReader ||
          !window.FileList ||
          !window.Blob
        ) {
          alert("The File APIs are not fully supported in this browser.");
          return;
        }
        var blob = new Blob([filing[dataid]], { type: "application/pdf" }); // change resultByte to bytes
        var link = document.createElement("a");
        link.href = window.URL.createObjectURL(blob);
        link.download = filing[dataid]["name"];
        link.click();
      });
    });
    document.querySelector(".file_Upload ").classList.remove("show");
  };

  var imageurls = [];

  removeimg = 1;

  var indexing = 0;

  document.querySelector("#galleryfile-input").addEventListener("change", previewImages);

  function previewImages() {
    var preview = document.querySelector(".file_Upload");

    preview.insertAdjacentHTML(
      "beforeend",'<div class="profile-media-img image_pre"></div>'
    );

    var imageselector = document.querySelector(".file_Upload .profile-media-img");

    if (this.files) {
      [].forEach.call(this.files, readAndPreview);
    }

    function readAndPreview(file) {
      // Make sure `file.name` matches our extensions criteria
      if (!/\.(jpe?g|png|gif)$/i.test(file.name)) {
        return alert(file.name + " is not an image");
      } // else...

      var reader = new FileReader();

      var createImage = "";

      reader.addEventListener("load", function () {
        removeimg++;
        if (preview) {
          preview.classList.add("show");
        }

        imageurls.push(reader.result);

        createImage +=
          '<div class="media-img-list" id="remove-image-' + removeimg +'">\
          <a href="#">\
              <img src="' +this.result +'" alt="' + file.name +'" class="img-fluid">\
          </a>\
            <i class="ri-close-line image-remove" onclick="removeImage(`remove-image-' + removeimg +'`)"></i>\
          </div>';

        imageselector.insertAdjacentHTML("afterbegin", createImage);
        indexing++;
      });
      reader.readAsDataURL(file);
    }
  }

  //append images
  var getImages = function (chatid1, newImage) {
    var newImages = imageurls;

    var chatConList1 = document.getElementById(chatid1);

    var itemList1 = chatConList1.querySelector(".chat-conversation-list");

    var multiimg = "";

    newImages.forEach(function (newImage) {
      messageIds++;
      multiimg +=
        '<div class="message-img-list">\
          <div>\
            <a class="popup-img d-inline-block" href="' + newImage + '" target="_blank">\
                <img src="' + newImage + '" alt="" class="rounded border" width="200" />\
            </a>\
          </div>\
          <div class="message-img-link">\
            <ul class="list-inline mb-0">\
              <li class="list-inline-item dropdown">\
                <a class="dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">\
                    <i class="bx bx-dots-horizontal-rounded"></i>\
                </a>\
          <div class="dropdown-menu">\
            <a class="dropdown-item d-flex align-items-center justify-content-between" href="' + newImage + '" download>Download <i class="bx bx-download ms-2 text-muted"></i></a>\
            <a class="dropdown-item d-flex align-items-center justify-content-between" href="#" data-bs-toggle="collapse" data-bs-target=".replyCollapse">Reply <i class="bx bx-share ms-2 text-muted"></i></a>\
            <a class="dropdown-item d-flex align-items-center justify-content-between" href="#" data-bs-toggle="modal" data-bs-target=".forwardModal">Forward <i class="bx bx-share-alt ms-2 text-muted"></i></a>\
            <a class="dropdown-item d-flex align-items-center justify-content-between" href="#">Bookmark <i class="bx bx-bookmarks text-muted ms-2"></i></a>\
            <a class="dropdown-item d-flex align-items-center justify-content-between delete-image" id="delete-item-' +messageIds +'" href="#">Delete <i class="bx bx-trash text-muted ms-2"></i></a>\
          </div>\
        </li>\
      </ul>\
    </div>\
    </div>';
    });

    if (newImages != null) {
      itemList1.insertAdjacentHTML(
        "beforeend",
        '<li class="chat-list right" id="chat-list-' +
        messageIds +
        '" >\
        <div class="conversation-list">\
            <div class="user-chat-content">\
                <div class="ctext-wrap">\
                    <div class="ctext-wrap-content">\
                        <div class="message-img mb-0">' + multiimg + '\
                    </div>\
                    </div>\
                    </div>\
                  <div class="conversation-name">\
                    <small class="text-muted time">' +currentTime() +'</small>\
                    <span class="text-success check-message-icon"><i class="bx bx-check"></i></span>\
                </div>\
          </div>\
        </li>'
      );
      updateLightbox();
      indexing = 0;

      //Delete appended images(single user)
      var deleteImages = itemList.querySelectorAll(".chat-list");
      deleteImages.forEach(function (item) {
        item.querySelectorAll(".delete-image").forEach(function (subitem) {
          subitem.addEventListener("click", function () {
            subitem.closest(".message-img").childElementCount == 1 ? subitem.closest(".chat-list").remove() : subitem.closest(".message-img-list").remove();
          });
        });
      });

      //Delete appended images(Channel chat)
      var deleteChannelImages = channelItemList.querySelectorAll(".chat-list");
      deleteChannelImages.forEach(function (item) {
        item.querySelectorAll(".delete-image").forEach(function (subitem) {
          subitem.addEventListener("click", function () {
            subitem.closest(".message-img").childElementCount == 1 ? subitem.closest(".chat-list").remove() : subitem.closest(".message-img-list").remove();
          });
        });
      });
    }

    document.querySelector(".file_Upload").classList.remove("show");
    imageurls = [];
  };

  //Append New Message
  var getChatList = function (chatid, chatItems) {
    messageIds++;

    var chatConList = document.getElementById(chatid);
    var itemList = chatConList.querySelector(".chat-conversation-list");
    if (chatItems != null) {
      itemList.insertAdjacentHTML(
        "beforeend",
        '<li class="chat-list right" id="chat-list-' + messageIds + '" >\
                <div class="conversation-list">\
                    <div class="user-chat-content">\
                        <div class="ctext-wrap">\
                            <div class="ctext-wrap-content">\
                                <p class="mb-0 ctext-content">' + chatItems + '\</p>\
                            </div>\
                            <div class="dropdown align-self-start message-box-drop">\
                                <a class="dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">\
                                    <i class="ri-more-2-fill"></i>\
                                </a>\
                                <div class="dropdown-menu">\
                                    <a class="dropdown-item d-flex align-items-center justify-content-between reply-message" href="#" data-bs-toggle="collapse" data-bs-target=".replyCollapse">Reply <i class="bx bx-share ms-2 text-muted"></i></a>\
                                    <a class="dropdown-item d-flex align-items-center justify-content-between" href="#" data-bs-toggle="modal" data-bs-target=".forwardModal">Forward <i class="bx bx-share-alt ms-2 text-muted"></i></a>\
                                    <a class="dropdown-item d-flex align-items-center justify-content-between copy-message" href="#" id="copy-message-' + messageIds +'">Copy <i class="bx bx-copy text-muted ms-2"></i></a>\
                                    <a class="dropdown-item d-flex align-items-center justify-content-between" href="#">Bookmark <i class="bx bx-bookmarks text-muted ms-2"></i></a>\
                                    <a class="dropdown-item d-flex align-items-center justify-content-between" href="#">Mark as Unread <i class="bx bx-message-error text-muted ms-2"></i></a>\
                                    <a class="dropdown-item d-flex align-items-center justify-content-between delete-item" id="delete-item-' + messageIds +'" href="#">Delete <i class="bx bx-trash text-muted ms-2"></i></a>\
                            </div>\
                        </div>\
                    </div>\
                    <div class="conversation-name">\
                        <small class="text-muted time">' +currentTime() +'</small>\
                        <span class="text-success check-message-icon"><i class="bx bx-check"></i></span>\
                    </div>\
                </div>\
            </div>\
        </li>'
      );
    }

    // remove chat list
    var newChatList = document.getElementById("chat-list-" + messageIds);
    newChatList.querySelectorAll(".delete-item").forEach(function (subitem) {
      subitem.addEventListener("click", function () {
        itemList.removeChild(newChatList);
      });
    });

    //Copy Clipboard alert
    newChatList.querySelectorAll(".copy-message").forEach(function (subitem) {
      subitem.addEventListener("click", function () {
        document.getElementById("copyClipBoard").style.display = "block";
        document.getElementById("copyClipBoardChannel").style.display = "block";
        setTimeout(hideclipboardNew, 1000);
        function hideclipboardNew() {
          document.getElementById("copyClipBoard").style.display = "none";
          document.getElementById("copyClipBoardChannel").style.display ="none";
        }
      });
    });

    //reply Message model
    newChatList.querySelectorAll(".reply-message").forEach(function (subitem) {
      subitem.addEventListener("click", function () {
        var replyToggleOpenNew = document.querySelector(".replyCard");
        var replyToggleCloseNew = document.querySelector("#close_toggle");
        var replyMessageNew = subitem.closest(".ctext-wrap").children[0].children[0].innerText;
        var replyUserNew = document.querySelector(".user-profile-show").innerHTML;
        isreplyMessage = true;
        replyToggleOpenNew.classList.add("show");
        replyToggleCloseNew.addEventListener("click", function () {
          replyToggleOpenNew.classList.remove("show");
        });

        document.querySelector(".replyCard .replymessage-block .flex-grow-1 .mb-0").innerText = replyMessageNew;
        document.querySelector(".replyCard .replymessage-block .flex-grow-1 .conversation-name").innerText = replyUserNew;
      });
    });

    //Copy Message
    newChatList.querySelectorAll(".copy-message").forEach(function (subitem) {
      subitem.addEventListener("click", function () {
        var currentValue =newChatList.childNodes[1].firstElementChild.firstElementChild.firstElementChild.firstElementChild.innerText;
        navigator.clipboard.writeText(currentValue);
      });
    });
  };

  var messageboxcollapse = 1;

  //message with reply
  var getReplyChatList = function (chatReplyId, chatReplyItems) {
    var chatReplyUser = document.querySelector(".user-profile-show").innerHTML;
    var chatReplyMessage = document.querySelector(".replyCard .replymessage-block .flex-grow-1 .mb-0").innerText;
    messageIds++;
    var chatreplyConList = document.getElementById(chatReplyId);
    var itemReplyList = chatreplyConList.querySelector(".chat-conversation-list");
    if (chatReplyItems != null) {
      itemReplyList.insertAdjacentHTML(
        "beforeend",
        '<li class="chat-list right" id="chat-list-' + messageIds + '" >\
                <div class="conversation-list">\
                    <div class="user-chat-content">\
                        <div class="ctext-wrap">\
                            <div class="ctext-wrap-content">\
                            <div class="replymessage-block mb-0 d-flex align-items-start">\
                        <div class="flex-grow-1">\
                            <h5 class="conversation-name">' + chatReplyUser + '</h5>\
                            <p class="mb-0">' + chatReplyMessage + '</p>\
                        </div>\
                        <div class="flex-shrink-0">\
                            <button type="button" class="btn btn-sm btn-link mt-n2 me-n3 font-size-18">\
                            </button>\
                        </div>\
                    </div>\
                                <p class="mb-0 ctext-content mt-1">\
                                    ' + chatReplyItems + '\
                                </p>\
                            </div>\
                            <div class="dropdown align-self-start message-box-drop">\
                                <a class="dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">\
                                    <i class="ri-more-2-fill"></i>\
                                </a>\
                                <div class="dropdown-menu">\
                                    <a class="dropdown-item d-flex align-items-center justify-content-between reply-message" href="#" data-bs-toggle="collapse" data-bs-target=".replyCollapse">Reply <i class="bx bx-share ms-2 text-muted"></i></a>\
                                    <a class="dropdown-item d-flex align-items-center justify-content-between" href="#" data-bs-toggle="modal" data-bs-target=".forwardModal">Forward <i class="bx bx-share-alt ms-2 text-muted"></i></a>\
                                    <a class="dropdown-item d-flex align-items-center justify-content-between copy-message" href="#" id="copy-message-' +messageIds +'">Copy <i class="bx bx-copy text-muted ms-2"></i></a>\
                                    <a class="dropdown-item d-flex align-items-center justify-content-between" href="#">Bookmark <i class="bx bx-bookmarks text-muted ms-2"></i></a>\
                                    <a class="dropdown-item d-flex align-items-center justify-content-between" href="#">Mark as Unread <i class="bx bx-message-error text-muted ms-2"></i></a>\
                                    <a class="dropdown-item d-flex align-items-center justify-content-between delete-item" id="delete-item-' + messageIds +'" href="#">Delete <i class="bx bx-trash text-muted ms-2"></i></a>\
                            </div>\
                        </div>\
                    </div>\
                    <div class="conversation-name">\
                        <small class="text-muted time">' +currentTime() +'</small>\
                        <span class="text-success check-message-icon"><i class="bx bx-check"></i></span>\
                    </div>\
                </div>\
            </div>\
        </li>'
      );
      messageboxcollapse++;
    }

    // remove chat list
    var newChatList = document.getElementById("chat-list-" + messageIds);
    newChatList.querySelectorAll(".delete-item").forEach(function (subitem) {
      subitem.addEventListener("click", function () {
        itemList.removeChild(newChatList);
      });
    });

    //Copy Clipboard alert
    newChatList.querySelectorAll(".copy-message").forEach(function (subitem) {
      subitem.addEventListener("click", function () {
        document.getElementById("copyClipBoard").style.display = "block";
        document.getElementById("copyClipBoardChannel").style.display = "block";
        setTimeout(hideclipboardNew, 1000);
        function hideclipboardNew() {
          document.getElementById("copyClipBoard").style.display = "none";
          document.getElementById("copyClipBoardChannel").style.display ="none";
        }
      });
    });

    newChatList.querySelectorAll(".reply-message").forEach(function (subitem) {
      subitem.addEventListener("click", function () {
        var replyMessage = subitem.closest(".ctext-wrap").children[0].children[0].innerText;
        var replyuser = document.querySelector(".user-profile-show").innerHTML;
        document.querySelector(".replyCard .replymessage-block .flex-grow-1 .mb-0").innerText = replyMessage;
        var msgWwnerName = (subitem.closest(".chat-list")) ? subitem.closest(".chat-list").classList.contains("left") ? replyuser : 'You' : replyuser;
        document.querySelector(".replyCard .replymessage-block .flex-grow-1 .conversation-name").innerText = msgWwnerName;
      });
    });

    //Copy Message
    newChatList.querySelectorAll(".copy-message").forEach(function (subitem) {
      subitem.addEventListener("click", function () {
        newChatList.childNodes[1].children[1].firstElementChild.firstElementChild.getAttribute("id");
        isText =newChatList.childNodes[1].children[1].firstElementChild.firstElementChild.innerText;
        navigator.clipboard.writeText(isText);
      });
    });
  };

  if (chatForm) {
    //add an item to the List, including to local storage
    chatForm.addEventListener("submit", function (e) {
      e.preventDefault();
      var chatId = currentChatId;
      var chatId1 = currentChatId;
      var chatId2 = currentChatId;
      var chatId3 = currentChatId;
      var chatReplyId = currentChatId;

      var chatInputValue = chatInput.value;
      var imagepreview = document.querySelector(".image_pre");
      var attachedFilePreview = document.querySelector(".attchedfile_pre");
      var audioFilePreview = document.querySelector(".audiofile_pre");

      if (imagepreview != null) {
        getImages(chatId1, chatInputValue);
      } else if (imagepreview != null) {
        getImages(chatId1, chatInputValue);
      } else if (attachedFilePreview != null) {
        getAttachedFiles(chatId2, chatInputValue);
      } else if (audioFilePreview != null) {
        getAudioFiles(chatId3, chatInputValue);
      } else {
        if (isreplyMessage == true) {
          getReplyChatList(chatReplyId, chatInputValue);
          isreplyMessage = false;
        } else {
          getChatList(chatId, chatInputValue);
        }
      }

      scrollToBottom(chatId || chatId1 || chatId2 || chatId3 || chatReplyId);

      chatInput.value = "";      

      //Images input text area null
      document.querySelector(".image_pre")? document.querySelector(".image_pre").remove(): "";
      document.getElementById("galleryfile-input").value = "";

      //attached input text area null
      document.querySelector(".attchedfile_pre") ? document.querySelector(".attchedfile_pre").remove(): "";
      document.getElementById("attachedfile-input").value = "";

      //audio input text area null
      document.querySelector(".audiofile_pre")? document.querySelector(".audiofile_pre").remove(): "";
      document.getElementById("audiofile-input").value = "";

      //reply msg remove textarea
      document.getElementById("close_toggle").click();
    });
  }

  // remove chat list
  function deleteMessage() {
    var deleteItems = itemList.querySelectorAll(".delete-item");
    deleteItems.forEach(function (item) {
      item.addEventListener("click", function () {
        item.closest(".user-chat-content").childElementCount == 2? item.closest(".chat-list").remove(): item.closest(".ctext-wrap").remove();
      });
    });
  }

  //remove chat images
  function deleteImage() {
    var deleteImage = itemList.querySelectorAll(".chat-list");
    deleteImage.forEach(function (item) {
      item.querySelectorAll(".delete-image").forEach(function (subitem) {
        subitem.addEventListener("click", function () {
          subitem.closest(".message-img").childElementCount == 1 ? subitem.closest(".chat-list").remove() : subitem.closest(".message-img-list").remove();
        });
      });
    });
  }

  //Delete Channel Message
  var channelItemList = document.querySelector("#channel-conversation");
  function deleteChannelMessage() {
    var channelChatList = channelItemList.querySelectorAll(".delete-item");
    channelChatList.forEach(function (item) {
      item.addEventListener("click", function () {
        item.closest(".user-chat-content").childElementCount == 2 ? item.closest(".chat-list").remove(): item.closest(".ctext-wrap").remove();
      });
    });
  }

  //Copy ClipBoard Alert
  function copyClipboard() {
    var copyClipboardAlert = document.querySelectorAll(".copy-message");
    copyClipboardAlert.forEach(function (item) {
      item.addEventListener("click", function () {
        document.getElementById("copyClipBoard").style.display = "block";
        document.getElementById("copyClipBoardChannel").style.display = "block";
        setTimeout(hideclipboard, 1000);
        function hideclipboard() {
          document.getElementById("copyClipBoard").style.display = "none";
          document.getElementById("copyClipBoardChannel").style.display ="none";
        }
      });
    });
  }

  //Copy Messages
  function copyMessage() {
    var copyMessage = itemList.querySelectorAll(".copy-message");
    copyMessage.forEach(function (item) {
      item.addEventListener("click", function () {
        var isText = item.closest(".ctext-wrap").children[0]? item.closest(".ctext-wrap").children[0].children[0].innerText: "";
        navigator.clipboard.writeText(isText);
      });
    });
  }

  function copyChannelMessage() {
    var copyChannelMessage = channelItemList.querySelectorAll(".copy-message");
    copyChannelMessage.forEach(function (item) {
      item.addEventListener("click", function () {
        var isText = item.closest(".ctext-wrap").children[0] ? item.closest(".ctext-wrap").children[0].children[0].innerText : "";
        navigator.clipboard.writeText(isText);
      });
    });
  }

  //reply message
  function replyMessage() {
    var replyMessage = itemList.querySelectorAll(".reply-message");
    var replyToggleOpen = document.querySelector(".replyCard");
    var replyToggleClose = document.querySelector("#close_toggle");

    replyMessage.forEach(function (item) {
      item.addEventListener("click", function () {
        isreplyMessage = true;
        replyToggleOpen.classList.add("show");
        replyToggleClose.addEventListener("click", function () {
          replyToggleOpen.classList.remove("show");
        });

        var replyMsg = item.closest(".ctext-wrap").children[0].children[0].innerText;
        document.querySelector(".replyCard .replymessage-block .flex-grow-1 .mb-0").innerText = replyMsg;
        var replyuser = document.querySelector(".user-profile-show").innerHTML;
        var msgWwnerName = (subitem.closest(".chat-list")) ? subitem.closest(".chat-list").classList.contains("left") ? replyuser : 'You' : replyuser;
        document.querySelector(".replyCard .replymessage-block .flex-grow-1 .conversation-name").innerText = msgWwnerName;
      });
    });
  }

  //reply Channelmessage
  function replyChannelMessage() {
    var replyChannelMessage =
      channelItemList.querySelectorAll(".reply-message");
    var replyChannelToggleOpen = document.querySelector(".replyCard");
    var replyChannelToggleClose = document.querySelector("#close_toggle");

    replyChannelMessage.forEach(function (item) {
      item.addEventListener("click", function () {
        isreplyMessage = true;
        replyChannelToggleOpen.classList.add("show");
        replyChannelToggleClose.addEventListener("click", function () {
          replyChannelToggleOpen.classList.remove("show");
        });

        var replyChannelMsg =item.closest(".ctext-wrap").children[0].children[0].innerText;
        document.querySelector(".replyCard .replymessage-block .flex-grow-1 .mb-0").innerText = replyChannelMsg;
        var replyChanneluser =document.querySelector(".user-profile-show").innerHTML;
        document.querySelector(".replyCard .replymessage-block .flex-grow-1 .conversation-name").innerText = replyChanneluser;
      });
    });
  }


  //Copy Channel Messages
  function copyChannelMessage() {
    var copyChannelMessage = channelItemList.querySelectorAll(".copy-message");
    copyChannelMessage.forEach(function (item) {
      item.addEventListener("click", function () {
        var isText = item.closest(".ctext-wrap").children[0] ? item.closest(".ctext-wrap").children[0].children[0].innerText : "";
        navigator.clipboard.writeText(isText);
      });
    });
  }

  // Profile Foreground Img

  document.querySelector("#profile-foreground-img-file-input").addEventListener("change", function () {
      id;
      var preview = document.querySelector(".profile-foreground-img");
      var file = document.querySelector(".profile-foreground-img-file-input").files[0];
      var reader = new FileReader();

      reader.addEventListener("load",function () {
          preview.src = reader.result;
        },
        false
      );
      if (file) {
        reader.readAsDataURL(file);
      }
    });

  // user profile img
  document
    .querySelector("#profile-img-file-input")
    .addEventListener("change", function () {
      var preview = document.querySelector(".user-profile-image");
      var file = document.querySelector(".profile-img-file-input").files[0];
      var reader = new FileReader();

      reader.addEventListener("load",function () {
          preview.src = reader.result;
        },
        false
      );
      if (file) {
        reader.readAsDataURL(file);
      }
    });

  // favourite btn
  var favouriteBtn = document.getElementsByClassName("favourite-btn");
  for (var i = 0; i < favouriteBtn.length; i++) {
    var favouriteBtns = favouriteBtn[i];
    favouriteBtns.onclick = function () {
      favouriteBtns.classList.toggle("active");
    };
  }

  // chat emojiPicker input
  var emojiPicker = new FgEmojiPicker({
    trigger: [".emoji-btn"],
    removeOnSelection: false,
    closeButton: true,
    position: ["top", "right"],
    preFetch: true,
    dir: "assets/js/dir/json",
    insertInto: document.querySelector(".chat-input"),
  });

  // emojiPicker position
  var emojiBtn = document.getElementById("emoji-btn");
  emojiBtn.addEventListener("click", function () {
    setTimeout(function () {
      var fgEmojiPicker = document.getElementsByClassName("fg-emoji-picker")[0];
      if (fgEmojiPicker) {
        var leftEmoji = window.getComputedStyle(fgEmojiPicker)? window.getComputedStyle(fgEmojiPicker).getPropertyValue("left") : "";
        if (leftEmoji) {
          leftEmoji = leftEmoji.replace("px", "");
          leftEmoji = leftEmoji - 40 + "px";
          fgEmojiPicker.style.left = leftEmoji;
        }
      }
    }, 0);
  });

  function getJSONFile(jsonurl, callback) {
    var xhr = new XMLHttpRequest();
    xhr.open("GET", jsonurl, true);
    xhr.responseType = "json";
    xhr.onload = function () {
      var status = xhr.status;
      if (status === 200) {
        callback(null, xhr.response);
      } else {
        callback(status, xhr.response);
      }
    };
    xhr.send();
  }

  // getNextMsgCounts
  function getNextMsgCounts(chatsData, i, from_id) {
    var counts = 0;
    while (chatsData[i]) {
      if (chatsData[i + 1] && chatsData[i + 1]["from_id"] == from_id) {
        counts++;
        i++;
      } else {
        break;
      }
    }
    return counts;
  }

  //getNextMsgs
  function getNextMsgs(chatsData, i, from_id, isContinue) {
    var msgs = 0;
    while (chatsData[i]) {
      if (chatsData[i + 1] && chatsData[i + 1]["from_id"] == from_id) {
        msgs = getMsg(
          chatsData[i + 1].id,
          chatsData[i + 1].msg,
          chatsData[i + 1].has_images,
          chatsData[i + 1].has_files,
          chatsData[i + 1].has_dropDown
        );
        i++;
      } else {
        break;
      }
    }
    return msgs;
  }

  // getMsg
  function getMsg(id, msg, has_images, has_files, has_dropDown) {
    var msgHTML = '<div class="ctext-wrap">';
    if (msg != null) {
      msgHTML +=
        '<div class="ctext-wrap-content" id=' + id +'>\
        <p class="mb-0 ctext-content">' +msg +"</p></div>";
    } else if (has_images && has_images.length > 0) {
      msgHTML += '<div class="message-img mb-0">';
      for (i = 0; i < has_images.length; i++) {
        msgHTML +=
          '<div class="message-img-list">\
            <div>\
              <a class="popup-img d-inline-block" href="' + has_images[i] +'">\
                <img src="' +has_images[i] +'" alt="" class="rounded border">\
              </a>\
            </div>\
            <div class="message-img-link">\
              <ul class="list-inline mb-0">\
                <li class="list-inline-item dropdown">\
                  <a class="dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">\
                      <i class="bx bx-dots-horizontal-rounded"></i>\
                  </a>\
                <div class="dropdown-menu">\
                  <a class="dropdown-item d-flex align-items-center justify-content-between" href="' +has_images[i] +'" download>Download <i class="bx bx-download ms-2 text-muted"></i></a>\
                  <a class="dropdown-item d-flex align-items-center justify-content-between"  href="#" data-bs-toggle="collapse" data-bs-target=".replyCollapse">Reply <i class="bx bx-share ms-2 text-muted"></i></a>\
                  <a class="dropdown-item d-flex align-items-center justify-content-between" href="#" data-bs-toggle="modal" data-bs-target=".forwardModal">Forward <i class="bx bx-share-alt ms-2 text-muted"></i></a>\
                  <a class="dropdown-item d-flex align-items-center justify-content-between" href="#">Bookmark <i class="bx bx-bookmarks text-muted ms-2"></i></a>\
                  <a class="dropdown-item d-flex align-items-center justify-content-between delete-image" href="#">Delete <i class="bx bx-trash ms-2 text-muted"></i></a>\
                </div>\
              </li>\
          </ul>\
        </div>\
      </div>';
      }
      msgHTML += "</div>";
    } else if (has_files.length > 0) {
      msgHTML +=
        '<div class="ctext-wrap-content">\
            <div class="p-3 border-primary border rounded-3">\
            <div class="d-flex align-items-center attached-file">\
                <div class="flex-shrink-0 avatar-sm me-3 ms-0 attached-file-avatar">\
                    <div class="avatar-title bg-soft-primary text-primary rounded-circle font-size-20">\
                        <i class="ri-attachment-2"></i>\
                    </div>\
                </div>\
                <div class="flex-grow-1 overflow-hidden">\
                    <div class="text-start">\
                        <h5 class="font-size-14 mb-1">design-phase-1-approved.pdf</h5>\
                        <p class="text-muted text-truncate font-size-13 mb-0">12.5 MB</p>\
                    </div>\
                </div>\
                <div class="flex-shrink-0 ms-4">\
                    <div class="d-flex gap-2 font-size-20 d-flex align-items-start">\
                        <div>\
                            <a href="#" class="text-muted">\
                                <i class="bx bxs-download"></i>\
                            </a>\
                        </div>\
                    </div>\
                </div>\
             </div>\
            </div>\
            </div>\
            <div class="dropdown align-self-start message-box-drop">\
                <a class="dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">\
                    <i class="ri-more-2-fill"></i>\
                </a>\
                <div class="dropdown-menu">\
                  <a class="dropdown-item d-flex align-items-center justify-content-between"  href="' + has_files +'" download>Download <i class="bx bx-download ms-2 text-muted"></i></a>\
                  <a class="dropdown-item d-flex align-items-center justify-content-between" href="#" data-bs-toggle="collapse" data-bs-target=".replyCollapse">Reply <i class="bx bx-share ms-2 text-muted"></i></a>\
                  <a class="dropdown-item d-flex align-items-center justify-content-between" href="#" data-bs-toggle="modal" data-bs-target=".forwardModal">Forward <i class="bx bx-share-alt ms-2 text-muted"></i></a>\
                  <a class="dropdown-item d-flex align-items-center justify-content-between" href="#">Bookmark <i class="bx bx-bookmarks text-muted ms-2"></i></a>\
                  <a class="dropdown-item d-flex align-items-center justify-content-between delete-item" href="#">Delete <i class="bx bx-trash text-muted ms-2"></i></a>\
                </div>\
            </div>';
    }
    if (has_dropDown === true) {
      msgHTML +=
        '<div class="dropdown align-self-start message-box-drop">\
                <a class="dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">\
                    <i class="ri-more-2-fill"></i>\
                </a>\
                <div class="dropdown-menu">\
                    <a class="dropdown-item d-flex align-items-center justify-content-between reply-message" href="#" id="reply-message-' + messageIds +'" data-bs-toggle="collapse" data-bs-target=".replyCollapse">Reply <i class="bx bx-share ms-2 text-muted"></i></a>\
                    <a class="dropdown-item d-flex align-items-center justify-content-between" href="#" data-bs-toggle="modal" data-bs-target=".forwardModal">Forward <i class="bx bx-share-alt ms-2 text-muted"></i></a>\
                    <a class="dropdown-item d-flex align-items-center justify-content-between copy-message" href="#" id="copy-message-' + messageIds +'">Copy <i class="bx bx-copy text-muted ms-2"></i></a>\
                    <a class="dropdown-item d-flex align-items-center justify-content-between" href="#">Bookmark <i class="bx bx-bookmarks text-muted ms-2"></i></a>\
                    <a class="dropdown-item d-flex align-items-center justify-content-between" href="#">Mark as Unread <i class="bx bx-message-error text-muted ms-2"></i></a>\
                    <a class="dropdown-item d-flex align-items-center justify-content-between delete-item" href="#">Delete <i class="bx bx-trash text-muted ms-2"></i></a>\
                </div>\
            </div>';
    }
    msgHTML += "</div>";
    return msgHTML;
  }

  //Chat Message
  function getChatMessages(jsonFileUrl) {
    getJSONFile(jsonFileUrl, function (err, data) {
      if (err !== null) {
        console.log("Something went wrong: " + err);
      } else {
        var chatsData = currentSelectedChat == "users" ? data[0].chats : data[0].channel_chat;
        document.getElementById(currentSelectedChat + "-conversation").innerHTML = "";
        var isContinue = 0;
        chatsData.forEach(function (isChat, index) {
          if (isContinue > 0) {
            isContinue = isContinue - 1;
            return;
          }
          var isAlighn = isChat.from_id == userChatId ? " right" : " left";
          var user = usersList.find(function (list) {
            return list.id == isChat.from_id;
          });
          var msgHTML =
            '<li class="chat-list' +isAlighn +'" id=' + isChat.id +'>\
                        <div class="conversation-list">';
          if (userChatId != isChat.from_id)
            msgHTML +=
              '<div class="chat-avatar"><img src="' + user.profile +'" alt=""></div>';

          msgHTML += '<div class="user-chat-content">';
          msgHTML += getMsg(
            isChat.id,
            isChat.msg,
            isChat.has_images,
            isChat.has_files,
            isChat.has_dropDown
          );
          if (
            chatsData[index + 1] &&
            isChat.from_id == chatsData[index + 1]["from_id"]
          ) {
            isContinue = getNextMsgCounts(chatsData, index, isChat.from_id);
            msgHTML += getNextMsgs(
              chatsData,
              index,
              isChat.from_id,
              isContinue
            );
          }

          msgHTML +=
            '<div class="conversation-name"><small class="text-muted time">' + isChat.datetime +
            '</small> <span class="text-success check-message-icon"><i class="bx bx-check-double"></i></span></div>';
          msgHTML += "</div>\
                </div>\
            </li>";

          document.getElementById(currentSelectedChat + "-conversation").innerHTML += msgHTML;
        });
      }
      deleteMessage();
      deleteChannelMessage();
      deleteImage();
      copyMessage();
      copyChannelMessage();
      scrollToBottom("users-chat");
      updateLightbox();
      copyClipboard();
      replyMessage();
      replyChannelMessage();      
    });
  }
  // GLightbox Popup
  function updateLightbox() {
    var lightbox = GLightbox({
      selector: ".popup-img",
      title: false,
    });
  }
})();

var input, filter, ul, li, a, i, j, div;
// Search User
function searchUser() {
  input = document.getElementById("serachChatUser");
  filter = input.value.toUpperCase();
  ul = document.querySelector(".chat-room-list");
  li = ul.getElementsByTagName("li");
  for (i = 0; i < li.length; i++) {
    var item = li[i];
    var txtValue = item.querySelector("p").innerText;
    if (txtValue.toUpperCase().indexOf(filter) > -1) {
      li[i].style.display = "";
    } else {
      li[i].style.display = "none";
    }
  }
}

//Search Contacts
function searchContacts() {
  input = document.getElementById("searchContact");
  filter = input.value.toUpperCase();
  list = document.querySelector(".sort-contact");
  li = list.querySelectorAll(".mt-3 li");
  div = list.querySelectorAll(".mt-3 .contact-list-title");

  for (j = 0; j < div.length; j++) {
    var contactTitle = div[j];
    txtValue = contactTitle.innerText;
    if (txtValue.toUpperCase().indexOf(filter) > -1) {
      div[j].style.display = "";
    } else {
      div[j].style.display = "none";
    }
  }

  for (i = 0; i < li.length; i++) {
    contactName = li[i];
    txtValue = contactName.querySelector("h5").innerText;
    if (txtValue.toUpperCase().indexOf(filter) > -1) {
      li[i].style.display = "";
    } else {
      li[i].style.display = "none";
    }
  }
}

//Search contact on contactModalList
function searchContactOnModal() {
  input = document.getElementById("searchContactModal");
  filter = input.value.toUpperCase();
  list = document.querySelector(".contact-modal-list");
  li = list.querySelectorAll(".mt-3 li");
  div = list.querySelectorAll(".mt-3 .contact-list-title");

  for (j = 0; j < div.length; j++) {
    var contactTitle = div[j];
    txtValue = contactTitle.innerText;
    if (txtValue.toUpperCase().indexOf(filter) > -1) {
      div[j].style.display = "";
    } else {
      div[j].style.display = "none";
    }
  }

  for (i = 0; i < li.length; i++) {
    contactName = li[i];
    txtValue = contactName.querySelector("h5").innerText;
    if (txtValue.toUpperCase().indexOf(filter) > -1) {
      li[i].style.display = "";
    } else {
      li[i].style.display = "none";
    }
  }
}

//Location Permission
function getLocation() {
  if (navigator.geolocation) {
    navigator.geolocation.getCurrentPosition(showPosition);
  } else {
    x.innerHTML = "Geolocation is not supported by this browser.";
  }
}

function showPosition(position) {
  x.innerHTML =
    "Latitude: " +
    position.coords.latitude +
    "<br>Longitude: " +
    position.coords.longitude;
}

//Camera Permission
function cameraPermission() {
  if (navigator.mediaDevices.getUserMedia) {
    navigator.mediaDevices
      .getUserMedia({ video: true })
      .then(function (s) {
        video.srcObject = s;
      })
      .catch(function (err) {
        console.log(err);
      });
  } else {
    console.log("No");
  }
}

//Audio(Mic) Permission
function audioPermission() {
  navigator.mediaDevices.getUserMedia({ audio: true }).then(function (stream) {
    window.localStream = stream;
    window.localAudio.srcObject = stream;
    window.localAudio.autoplay = true;
  });
}

function themeColor(primaryColor) {
  var isActiveColor = window.localStorage.getItem("color");
  var isActiveImage = window.localStorage.getItem("image");

  document.querySelectorAll(".theme-img , .theme-color").forEach(function (item) {
      if (item.id == isActiveColor) {
        item.checked = true;
      }
      if (item.id == isActiveImage) {
        item.checked = true;
      }
      var colorRadioElements = document.querySelector("input[name=bgcolor-radio]:checked");

      if (colorRadioElements) {
        colorRadioElements = colorRadioElements.id;

        var elementsColor = document.getElementsByClassName(colorRadioElements);

        var color = window.getComputedStyle(elementsColor[0], null).getPropertyValue("background-color");
        var userChatOverlay = document.querySelector(".user-chat-overlay");

        if (colorRadioElements == "bgcolor-radio8") {
          color = "#4eac6d";
          userChatOverlay.style.background = null;
        }
        else {
          userChatOverlay.style.background = color;
        }
        rgbColor = color.substring(color.indexOf("(") + 1, color.indexOf(")"));

        document.documentElement.style.setProperty("--bs-primary-rgb",rgbColor);
      }

      var imageRadioElements = document.querySelector("input[name=bgimg-radio]:checked");

      if (imageRadioElements) {
        imageRadioElements = imageRadioElements.id;
        window.localStorage.setItem("image", imageRadioElements);
        var elementsImage = document.getElementsByClassName(imageRadioElements);
        if (elementsColor) {
          var image = window.getComputedStyle(elementsImage[0], null).getPropertyValue("background-image");
          var userChat = document.querySelector(".user-chat");
          userChat.style.backgroundImage = image;
        }
      }
      item.addEventListener("click", function (event) {
        if (item.id == isActiveColor) {
          item.checked = true;
        }
        if (item.id == isActiveImage) {
          item.checked = true;
        }

        // choose theme color
        var colorRadioElements = document.querySelector("input[name=bgcolor-radio]:checked");

        if (colorRadioElements) {
          colorRadioElements = colorRadioElements.id;

          var elementsColor = document.getElementsByClassName(colorRadioElements);
          if (elementsColor) {
            var color = window.getComputedStyle(elementsColor[0], null).getPropertyValue("background-color");
            var userChatOverlay = document.querySelector(".user-chat-overlay");

            if (colorRadioElements == "bgcolor-radio8") {
              color = "#4eac6d";
              userChatOverlay.style.background = null;
            } else {
              userChatOverlay.style.background = color;
            }

            rgbColor = color.substring(color.indexOf("(") + 1,color.indexOf(")"));
            document.documentElement.style.setProperty("--bs-primary-rgb",rgbColor);
            window.localStorage.setItem("color", colorRadioElements);
          }
        }

        // choose theme image
        var imageRadioElements = document.querySelector("input[name=bgimg-radio]:checked");

        if (imageRadioElements) {
          imageRadioElements = imageRadioElements.id;
          window.localStorage.setItem("image", imageRadioElements);
          var elementsImage =document.getElementsByClassName(imageRadioElements);
          if (elementsColor) {
            var image = window.getComputedStyle(elementsImage[0], null).getPropertyValue("background-image");
            var userChat = document.querySelector(".user-chat");
            userChat.style.backgroundImage = image;
          }
        }
      });
    });
}
var primaryColor = window.getComputedStyle(document.body, null).getPropertyValue("--bs-primary-rgb");
themeColor(primaryColor);

//Remove image
function removeImage(id) {
  document.querySelector("#" + id).remove();
  if (document.querySelectorAll(".image-remove").length == 0) {
    document.querySelector(".file_Upload").classList.remove("show");
  }
}

//Remove Attached Files
function removeAttachedFile() {
  if (document.getElementById("remove-attechedFile")) {
    document.getElementsByClassName("attechedFile-remove")[0];
    // Delete Upload Preview Attached Files
    document.getElementById("remove-attechedFile").addEventListener("click", function (e) {
        e.target.closest(".attchedfile_pre").remove();
      });
  }
  var removeButton = document.querySelector("#remove-attechedFile");
  removeButton.addEventListener("click", function () {
    document.querySelector(".file_Upload ").classList.remove("show");
  });
}

//Remove Audio Files
function removeAudioFile() {
  if (document.getElementById("remove-audioFile")) {
    document.getElementsByClassName("audioFile-remove")[0];
    // Delete Upload Preview Attached Files
    document.getElementById("remove-audioFile").addEventListener("click", function (e) {
        e.target.closest(".audiofile_pre").remove();
      });
  }
  var removeButton = document.querySelector("#remove-audioFile");
  removeButton.addEventListener("click", function () {
    document.querySelector(".file_Upload ").classList.remove("show");
  });
}
