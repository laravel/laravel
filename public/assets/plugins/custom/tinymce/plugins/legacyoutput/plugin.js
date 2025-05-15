/**
 * Copyright (c) Tiny Technologies, Inc. All rights reserved.
 * Licensed under the LGPL or a commercial license.
 * For LGPL see License.txt in the project root for license information.
 * For commercial licenses see https://www.tiny.cloud/
 *
 * Version: 5.8.1 (2021-05-20)
 */
(function () {
    'use strict';

    var global = tinymce.util.Tools.resolve('tinymce.PluginManager');

    var global$1 = tinymce.util.Tools.resolve('tinymce.util.Tools');

    var getFontSizeFormats = function (editor) {
      return editor.getParam('fontsize_formats');
    };
    var setFontSizeFormats = function (editor, fontsize_formats) {
      editor.settings.fontsize_formats = fontsize_formats;
    };
    var getFontFormats = function (editor) {
      return editor.getParam('font_formats');
    };
    var setFontFormats = function (editor, font_formats) {
      editor.settings.font_formats = font_formats;
    };
    var getFontSizeStyleValues = function (editor) {
      return editor.getParam('font_size_style_values', 'xx-small,x-small,small,medium,large,x-large,xx-large');
    };
    var setInlineStyles = function (editor, inline_styles) {
      editor.settings.inline_styles = inline_styles;
    };

    var overrideFormats = function (editor) {
      var alignElements = 'p,h1,h2,h3,h4,h5,h6,td,th,div,ul,ol,li,table', fontSizes = global$1.explode(getFontSizeStyleValues(editor)), schema = editor.schema;
      editor.formatter.register({
        alignleft: {
          selector: alignElements,
          attributes: { align: 'left' }
        },
        aligncenter: {
          selector: alignElements,
          attributes: { align: 'center' }
        },
        alignright: {
          selector: alignElements,
          attributes: { align: 'right' }
        },
        alignjustify: {
          selector: alignElements,
          attributes: { align: 'justify' }
        },
        bold: [
          {
            inline: 'b',
            remove: 'all',
            preserve_attributes: [
              'class',
              'style'
            ]
          },
          {
            inline: 'strong',
            remove: 'all',
            preserve_attributes: [
              'class',
              'style'
            ]
          },
          {
            inline: 'span',
            styles: { fontWeight: 'bold' }
          }
        ],
        italic: [
          {
            inline: 'i',
            remove: 'all',
            preserve_attributes: [
              'class',
              'style'
            ]
          },
          {
            inline: 'em',
            remove: 'all',
            preserve_attributes: [
              'class',
              'style'
            ]
          },
          {
            inline: 'span',
            styles: { fontStyle: 'italic' }
          }
        ],
        underline: [
          {
            inline: 'u',
            remove: 'all',
            preserve_attributes: [
              'class',
              'style'
            ]
          },
          {
            inline: 'span',
            styles: { textDecoration: 'underline' },
            exact: true
          }
        ],
        strikethrough: [
          {
            inline: 'strike',
            remove: 'all',
            preserve_attributes: [
              'class',
              'style'
            ]
          },
          {
            inline: 'span',
            styles: { textDecoration: 'line-through' },
            exact: true
          }
        ],
        fontname: {
          inline: 'font',
          toggle: false,
          attributes: { face: '%value' }
        },
        fontsize: {
          inline: 'font',
          toggle: false,
          attributes: {
            size: function (vars) {
              return String(global$1.inArray(fontSizes, vars.value) + 1);
            }
          }
        },
        forecolor: {
          inline: 'font',
          attributes: { color: '%value' },
          links: true,
          remove_similar: true,
          clear_child_styles: true
        },
        hilitecolor: {
          inline: 'font',
          styles: { backgroundColor: '%value' },
          links: true,
          remove_similar: true,
          clear_child_styles: true
        }
      });
      global$1.each('b,i,u,strike'.split(','), function (name) {
        schema.addValidElements(name + '[*]');
      });
      if (!schema.getElementRule('font')) {
        schema.addValidElements('font[face|size|color|style]');
      }
      global$1.each(alignElements.split(','), function (name) {
        var rule = schema.getElementRule(name);
        if (rule) {
          if (!rule.attributes.align) {
            rule.attributes.align = {};
            rule.attributesOrder.push('align');
          }
        }
      });
    };
    var overrideSettings = function (editor) {
      var defaultFontsizeFormats = '8pt=1 10pt=2 12pt=3 14pt=4 18pt=5 24pt=6 36pt=7';
      var defaultFontsFormats = 'Andale Mono=andale mono,monospace;' + 'Arial=arial,helvetica,sans-serif;' + 'Arial Black=arial black,sans-serif;' + 'Book Antiqua=book antiqua,palatino,serif;' + 'Comic Sans MS=comic sans ms,sans-serif;' + 'Courier New=courier new,courier,monospace;' + 'Georgia=georgia,palatino,serif;' + 'Helvetica=helvetica,arial,sans-serif;' + 'Impact=impact,sans-serif;' + 'Symbol=symbol;' + 'Tahoma=tahoma,arial,helvetica,sans-serif;' + 'Terminal=terminal,monaco,monospace;' + 'Times New Roman=times new roman,times,serif;' + 'Trebuchet MS=trebuchet ms,geneva,sans-serif;' + 'Verdana=verdana,geneva,sans-serif;' + 'Webdings=webdings;' + 'Wingdings=wingdings,zapf dingbats';
      setInlineStyles(editor, false);
      if (!getFontSizeFormats(editor)) {
        setFontSizeFormats(editor, defaultFontsizeFormats);
      }
      if (!getFontFormats(editor)) {
        setFontFormats(editor, defaultFontsFormats);
      }
    };
    var setup = function (editor) {
      overrideSettings(editor);
      editor.on('PreInit', function () {
        return overrideFormats(editor);
      });
    };

    function Plugin () {
      global.add('legacyoutput', function (editor) {
        setup(editor);
      });
    }

    Plugin();

}());
