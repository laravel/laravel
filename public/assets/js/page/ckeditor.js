'use strict';
$(function () {
  //CKEditor
  CKEDITOR.replace('ckeditor');
  CKEDITOR.config.height = 300;

  if (window.CodeMirror) {
    $(".codeeditor").each(function () {
      let editor = CodeMirror.fromTextArea(this, {
        lineNumbers: true,
        theme: "duotone-dark",
        mode: 'javascript',
        height: 200
      });
      editor.setSize("100%", 200);
    });
  }


});

