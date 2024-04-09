/**
 * Share Project
 */

'use strict';
$(function () {
  const select2ShareProject = $('.share-project-select');

  var shareProject = document.getElementById('shareProject');
  shareProject.addEventListener('show.bs.modal', function (event) {
    // do something...
    if (select2ShareProject.length) {
      function renderAvatar(option) {
        if (!option.id) {
          return option.text;
        }
        var optionEle =
          '<div class="d-flex align-items-center">' +
          '<div class="avatar avatar-xs me-2 d-flex">' +
          '<img src="' +
          assetsPath +
          $(option.element).data('image') +
          '" class="rounded-circle">' +
          '</div>' +
          '<div class="name">' +
          $(option.element).data('name') +
          '</div>' +
          '</div>';
        return optionEle;
      }
      select2ShareProject.wrap('<div class="position-relative"></div>').select2({
        dropdownParent: shareProject,
        templateResult: renderAvatar,
        templateSelection: renderAvatar,
        placeholder: 'Add Project Members',
        escapeMarkup: function (es) {
          return es;
        }
      });
    }
  });
});
