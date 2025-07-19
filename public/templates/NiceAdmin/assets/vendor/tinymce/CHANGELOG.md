# Changelog
All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## 6.3.2 - 2023-02-22

### Fixed
- Removed a workaround for ensuring stylesheets are loaded in an outdated version of webkit. #TINY-9433

## 6.3.1 - 2022-12-06

### Fixed
- HTML in messages for the `WindowManager.alert` and `WindowManager.confirm` APIs were not properly sanitized. #TINY-3548

## 6.3.0 - 2022-11-23

### Added
- New `expand` function added to `tinymce.selection` which expands the selection around the nearest word. #TINY-9001
- New `expand` function added to `tinymce.dom.RangeUtils` to return a new range expanded around the nearest word. #TINY-9001
- New `color_map_background` and `color_map_foreground` options which set the base colors used in the `backcolor` and `forecolor` toolbar buttons and menu items. #TINY-9184
- Added optional `storageKey` property to `colorinput` component and `colorswatch` fancy menu item. #TINY-9184
- New `addView` function added to `editor.ui.registry` which makes it possible to register custom editor views. #TINY-9210
- New `ToggleView` command which makes it possible to hide or show registered custom views. #TINY-9210
- New `color_default_foreground` and `color_default_background` options to set the initial default color for the `forecolor` and `backcolor` toolbar buttons and menu items. #TINY-9183
- New `getTransparentElements` function added to `tinymce.html.Schema` to return a map object of transparent HTML elements. #TINY-9172
- Added `ToggleToolbarDrawer` event to subscribe to toolbar’s opening and closing. #TINY-9271

### Changed
- Transparent elements, like anchors, are now allowed in the root of the editor body if they contain blocks. #TINY-9172
- Colorswatch keyboard navigation now starts on currently selected color if present in the colorswatch. #TINY-9283
- `setContent` is now allowed to accept any custom keys and values as a second options argument. #TINY-9143

### Improved
- Transparent elements, like anchors, can now contain block elements. #TINY-9172
- Colorswatch now displays a checkmark for selected color. #TINY-9283
- Color picker dialog now starts on the appropriate color for the cursor position. #TINY-9213

### Fixed
- Parsing media content would cause a memory leak, which for example occurred when using the `getContent` API. #TINY-9186
- Dragging a noneditable element toward the bottom edge would cause the page to scroll up. #TINY-9025
- Range expanding capabilities would behave inconsistently depending on where the cursor was placed. #TINY-9029
- Compilation errors were thrown when using TypeScript 4.8. #TINY-9161
- Line separator scrolling in floating toolbars. #TINY-8948
- A double bottom border appeared on inline mode editor for the `tinymce-5` skin. #TINY-9108
- The editor header showed up even with no menubar and toolbar configured. #TINY-8819
- Inline text pattern no longer triggers if it matches only the end but not the start. #TINY-8947
- Matches of inline text patterns that are similar are now managed correctly. #TINY-8949
- Using `editor.selection.getContent({ format: 'text' })` or `editor.getContent({ format: 'text' })` would sometimes deselect selected radio buttons. #TINY-9213
- The context toolbar prevented the user from placing the cursor at the edges of the editor. #TINY-8890
- The Quick Insert context toolbar provided by the `quickbars` plugin showed when the cursor was in a fake block caret. #TINY-9190
- The `editor.selection.getRng()` API was not returning a proper range on hidden editors in Firefox. #TINY-9259
- The `editor.selection.getBookmark()` API was not returning a proper bookmark on hidden editors in Firefox. #TINY-9259
- Dragging a noneditable element before or after another noneditable element now works correctly. #TINY-9253
- The restored selection after a redo or undo action was not scrolled into view. #TINY-9222
- A newline could not be inserted when the selection was restored from a bookmark after an inline element with a `contenteditable="false"` attribute. #TINY-9194
- The global `tinymce.dom.styleSheetLoader` was not affected by the `content_css_cors` option. #TINY-6037
- The caret was moved to the previous line when a text pattern executed a `mceInsertContent` command on Enter key when running on Firefox. #TINY-9193

## 6.2.0 - 2022-09-08

### Added
- New `text_patterns_lookup` option to provide additional text patterns dynamically. #TINY-8778
- New promotion element has been added to the default UI. It can be disabled using the new `promotion` option. #TINY-8840
- New `format_noneditable_selector` option to specify the `contenteditable="false"` elements that can be wrapped in a format. #TINY-8905
- Added `allow` as a valid attribute for the `iframe` element in the editor schema. #TINY-8939
- New `search` field in the `MenuButton` that shows a search field at the top of the menu, and refetches items when the search field updates. #TINY-8952

### Improved
- The formatter can now apply a format to a `contenteditable="false"` element by wrapping it. Configurable using the `format_noneditable_selector` option. #TINY-8905
- The autocompleter now supports a multiple character trigger using the new `trigger` configuration. #TINY-8887
- The formatter now applies some inline formats, such as color and font size, to list item elements when the entire item content is selected. #TINY-8961
- The installed and available plugin lists in the Help dialog are now sorted alphabetically. #TINY-9019
- Alignment can now be applied to more types of embedded media elements. #TINY-8687

### Changed
- The `@menubar-row-separator-color` oxide variable no longer affects the divider between the Menubar and Toolbar. It only controls the color of the separator lines drawn in multiline Menubars. #TINY-8632
- The `@toolbar-separator-color` oxide variable now affects the color of the separator between the Menubar and Toolbar only. #TINY-8632
- Available Premium plugins, which are listed by name in the Help dialog, are no longer translated. #TINY-9019

### Fixed
- The Autolink plugin did not work when text nodes in the content were fragmented. #TINY-3723
- Fixed multiple incorrect types on public APIs found while enabling TypeScript strict mode. #TINY-8806
- The number of blank lines returned from `editor.getContent({format: 'text'})` differed between browsers. #TINY-8579
- The editor focused via the `auto_focus` option was not scrolled into the viewport. #TINY-8785
- Adding spaces immediately after a `contenteditable="false"` block did not work properly in some circumstances. #TINY-8814
- Elements with only `data-*` custom attributes were sometimes removed when they should not be removed. #TINY-8755
- Selecting a figure with `class="image"` incorrectly highlighted the link toolbar button. #TINY-8832
- Specifying a single, non-default list style for the `advlist_bullet_styles` and `advlist_number_styles` options was not respected. #TINY-8721
- Fixed multiple issues that occurred when formatting `contenteditable` elements. #TINY-8905
- Spaces could be incorrectly added to `urlinput` dialog components (commonly but not exclusively presented in the *Insert/Edit Link* dialog) in certain cases. #TINY-8775
- The text patterns logic threw an error when there were fragmented text nodes in a paragraph. #TINY-8779
- Dragging a `contentEditable=false` element towards a document’s edge did not cause scrolling. #TINY-8874
- Parsing large documents no longer throws a `Maximum call stack size exceeded` exception. #TINY-6945
- DomParser filter matching was not checked between filters, which could lead to an exception in the parser. #TINY-8888
- `contenteditable="false"` lists can no longer be toggled; and `contenteditable="true"` list elements within these lists can no longer be indented, split into another list element, or appended to the previous list element by deletion. #TINY-8920
- Removed extra bottom padding in the context toolbar of the `tinymce-5` skin. #TINY-8980
- Fixed a regression where pressing **Enter** added or deleted content outside the selection. #TINY-9101
- Fixed a bug where pressing **Enter** deleted selected `contenteditable="false"` `<pre>` elements. #TINY-9101
- The `editor.insertContent()` API did not respect the `no_events` argument. #TINY-9140

### Deprecated
- The autocompleter configuration property, `ch`, has been deprecated. It will be removed in the next major release. Use the `trigger` property instead. #TINY-8887

## 6.1.2 - 2022-07-29

### Fixed
- Reverted the undo level fix in the `autolink` plugin as it caused duplicated content in some edge cases. #TINY-8936

## 6.1.1 - 2022-07-27

### Fixed
- Invalid special elements were not cleaned up correctly during sanitization. #TINY-8780
- An exception was thrown when deleting all content if the start or end of the document had a `contenteditable="false"` element. #TINY-8877
- When a sidebar was opened using the `sidebar_show` option, its associated toolbar button was not highlighted. #TINY-8873
- When converting a URL to a link, the `autolink` plugin did not fire an `ExecCommand` event, nor did it create an undo level. #TINY-8896
- Worked around a Firefox bug which resulted in cookies not being available inside the editor content. #TINY-8916
- `<pre>` content pasted into a `<pre>` block that had inline styles or was `noneditable` now merges correctly with the surrounding content. #TINY-8860
- After a `codesample` was pasted, the insertion point was placed incorrectly. #TINY-8861

## 6.1.0 - 2022-06-29

### Added
- New `sidebar_show` option to show the specified sidebar on initialization. #TINY-8710
- New `newline_behavior` option controls what happens when the Return or Enter key is pressed or the `mceInsertNewLine` command is used. #TINY-8458
- New `iframe_template_callback` option in the Media plugin. Patch provided by Namstel. #TINY-8684
- New `transparent` property for `iframe` dialog component. #TINY-8534
- New `removeAttributeFilter` and `removeNodeFilter` functions added to the DomParser and DOM Serializer APIs. #TINY-7847
- New `dispatchChange` function added to the UndoManager API to fire the change with current editor status as level and current undoManager layer as lastLevel. #TINY-8641

### Improved
- Clearer focus states for buttons while navigating with a keyboard. #TINY-8557
- Support annotating certain block elements directly when using the editor's Annotation API. #TINY-8698
- The `mceLink` command can now take the value `{ dialog: true }` to always open the link dialog. #TINY-8057
- All help dialog links to `https://www.tiny.cloud` now include `rel="noopener"` to avoid potential security issues. #TINY-8834

### Changed
- The `end_container_on_empty_block` option can now take a string of blocks, allowing the exiting of a blockquote element by pressing Enter or Return twice. #TINY-6559
- The default value for `end_container_on_empty_block` option has been changed to `'blockquote'`. #TINY-6559
- Link menu and toolbar buttons now always execute the `mceLink` command. #TINY-8057
- Toggling fullscreen mode when using the Fullscreen plugin now also fires the `ResizeEditor` event. #TINY-8701
- Getting the editor's text content now returns newlines instead of an empty string if more than one empty paragraph exists. #TINY-8578
- Custom elements are now treated as non-empty elements by the schema. #TINY-4784
- The autocompleter's menu HTML element is now positioned instead of the wrapper. #TINY-6476
- Choice menu items will now use the `'menuitemradio'` aria role to better reflect that only a single item can be active. #TINY-8602

### Fixed
- Some Template plugin option values were not escaped properly when doing replacement lookups with Regular Expressions. #TINY-7433
- Copy events were not dispatched in readonly mode. #TINY-6800
- `<pre>` tags were not preserved when copying and pasting. #TINY-7719
- The URL detection used for autolink and smart paste did not work if a path segment contained valid characters such as `!` and `:`. #TINY-8069
- In some cases pressing the Backspace or Delete key would incorrectly step into tables rather than remain outside. #TINY-8592
- Links opened when Alt+Enter or Option+Return was typed even when `preventDefault()` was called on the keydown event. #TINY-8661
- Inconsistent visual behavior between choosing Edit -> Select All and typing Ctrl+A or Cmd+A when a document contained an image. #TINY-4550
- Ctrl+Shift+Home/End or Cmd+Shift+Up-arrow/Down-arrow did not expand the selection to a `contenteditable="false"` element if the element was at the beginning or end of a document. #TINY-7795
- Triple-clicking did not select a paragraph in Google Chrome in some circumstances. #TINY-8215
- Images were not showing as selected when selected along with other content. #TINY-5947
- Selection direction was not stored or restored when getting or setting selection bookmarks. #TINY-8599
- When text within an inline boundary element was selected and the right-arrow key was pressed, the insertion point incorrectly moved to the left. #TINY-8601
- In some versions of Safari, the `editor.selection.isForward()` API could throw an exception due to an invalid selection. #TINY-8686
- The selection is no longer incorrectly moved inside a comment by the `editor.selection.normalize()` API. #TINY-7817
- The `InsertParagraph` or `mceInsertNewLine` commands did not delete the current selection like the native command does. #TINY-8606
- The `InsertLineBreak` command did not replace selected content. #TINY-8458
- If selected content straddled a parent and nested list, cutting the selection did not always set the list style to `'none'` on the parent list. #TINY-8078
- Delete operations could behave incorrectly if the selection contains a `contenteditable="false"` element located at the edge of content. #TINY-8729
- Spaces were not added correctly on some browsers when the insertion point was immediately before or after a `contenteditable="false"` block element. #TINY-8588
- Images that used a Data URI were corrupted when the data wasn't base64 encoded. #TINY-8337
- `uploadImages` no longer triggers two change events if there is a removal of images on upload. #TINY-8641
- Preview and Insert Template dialogs now display the correct content background color when using dark skins. #TINY-8534
- Dialogs no longer exceed window height on smaller screens. #TINY-8146
- UI components, such as dialogs, would in some cases cause the Esc keyup event to incorrectly trigger inside the editor. #TINY-7005
- Fixed incorrect word breaks in menus when the menu presented with a scrollbar. #TINY-8572
- Notifications did not properly reposition when toggling fullscreen mode. #TINY-8701
- Text alignments, such as flush left and centered, could not be applied to `<pre>` elements. #TINY-7715
- Indenting or outdenting list items inside a block element that was inside another list item did not work. #TINY-7209
- Changing the list type of a list within another block element altered the parent element that contained that list. #TINY-8068
- Pasting columns in tables could, in some circumstances, result in an invalid table. #TINY-8040
- Copying columns in tables could sometimes result in an invalid copy. #TINY-8040
- Changing table properties with the `table_style_by_css` option set to `false` would sometimes reset the table width. #TINY-8758
- Custom elements added to otherwise blank lines were removed during serialization. #TINY-4784
- The editor's autocompleter was not triggered at the start of nested list items. #TINY-8759
- Some function types in the TreeWalker API missed that it could return `undefined`. #TINY-8592
- Nuget packages for .NET and .NET Core are now configured to copy TinyMCE into `/wwwroot/lib/` when TinyMCE is installed into a project. #TINY-8611

## 6.0.3 - 2022-05-25

### Fixed
- Could not remove values when multiple cells were selected with the cell properties dialog. #TINY-8625
- Could not remove values when multiple rows were selected with the row properties dialog. #TINY-8625
- Empty lines that were formatted in a ranged selection using the `format_empty_lines` option were not kept in the serialized content. #TINY-8639
- The `s` element was missing from the default schema text inline elements. #TINY-8639
- Some text inline elements specified via the schema were not removed when empty by default. #TINY-8639

## 6.0.2 - 2022-04-27

### Fixed
- Some media elements wouldn't update when changing the source URL. #TINY-8660
- Inline toolbars flickered when switching between editors. #TINY-8594
- Multiple inline toolbars were shown if focused too quickly. #TINY-8503
- Added background and additional spacing for the text labeled buttons in the toolbar to improve visual clarity. #TINY-8617
- Toolbar split buttons with text used an incorrect width on touch devices. #TINY-8647

## 6.0.1 - 2022-03-23

### Fixed
- Fixed the dev ZIP missing the required `bin` scripts to build from the source. #TINY-8542
- Fixed a regression whereby text patterns couldn't be updated at runtime. #TINY-8540
- Fixed an issue where tables with colgroups could be copied incorrectly in some cases. #TINY-8568
- Naked buttons better adapt to various background colors, improved text contrast in notifications. #TINY-8533
- The autocompleter would not fire the `AutocompleterStart` event nor close the menu in some cases. #TINY-8552
- It wasn't possible to select text right after an inline noneditable element. #TINY-8567
- Fixed a double border showing for the `tinymce-5` skin when using `toolbar_location: 'bottom'`. #TINY-8564
- Clipboard content was not generated correctly when cutting and copying `contenteditable="false"` elements. #TINY-8563
- Fixed the box-shadow getting clipped in autocompletor popups. #TINY-8573
- The `buttonType` property did not work for dialog footer buttons. #TINY-8582
- Fix contrast ratio for error messages. #TINY-8586

## 6.0.0 - 2022-03-03

### Added
- New `editor.options` API to replace the old `editor.settings` and `editor.getParam` APIs. #TINY-8206
- New `editor.annotator.removeAll` API to remove all annotations by name. #TINY-8195
- New `Resource.unload` API to make it possible to unload resources. #TINY-8431
- New `FakeClipboard` API on the `tinymce` global. #TINY-8353
- New `dispatch()` function to replace the now deprecated `fire()` function in various APIs. #TINY-8102
- New `AutocompleterStart`, `AutocompleterUpdate` and `AutocompleterEnd` events. #TINY-8279
- New `mceAutocompleterClose`, `mceAutocompleterReload` commands. #TINY-8279
- New `mceInsertTableDialog` command to open the insert table dialog. #TINY-8273
- New `slider` dialog component. #TINY-8304
- New `imagepreview` dialog component, allowing preview and zoom of any image URL. #TINY-8333
- New `buttonType` property on dialog button components, supporting `toolbar` style in addition to `primary` and `secondary`. #TINY-8304
- The `tabindex` attribute is now copied from the target element to the iframe. #TINY-8315

### Improved
- New default theme styling for TinyMCE 6 facelift with old skin available as `tinymce-5` and `tinymce-5-dark`. #TINY-8373
- The default height of editor has been increased from `200px` to `400px` to improve the usability of the editor. #TINY-6860
- The upload results returned from the `editor.uploadImages()` API now includes a `removed` flag, reflecting if the image was removed after a failed upload. #TINY-7735
- The `ScriptLoader`, `StyleSheetLoader`, `AddOnManager`, `PluginManager` and `ThemeManager` APIs will now return a `Promise` when loading resources instead of using callbacks. #TINY-8325
- A `ThemeLoadError` event is now fired if the theme fails to load. #TINY-8325
- The `BeforeSetContent` event will now include the actual serialized content when passing in an `AstNode` to the `editor.setContent` API. #TINY-7996
- Improved support for placing the caret before or after noneditable elements within the editor. #TINY-8169
- Calls to `editor.selection.setRng` now update the caret position bookmark used when focus is returned to the editor. #TINY-8450
- The `emoticon` plugin dialog, toolbar and menu item has been updated to use the more accurate `Emojis` term. #TINY-7631
- The dialog `redial` API will now only rerender the changed components instead of the whole dialog. #TINY-8334
- The dialog API `setData` method now uses a deep merge algorithm to support partial nested objects. #TINY-8333
- The dialog spec `initialData` type is now `Partial<T>` to match the underlying implementation details. #TINY-8334
- Notifications no longer require a timeout to disable the close button. #TINY-6679
- The editor theme is now fetched in parallel with the icons, language pack and plugins. #TINY-8453

### Changed
- TinyMCE is now MIT licensed. #TINY-2316
- Moved the `paste` plugin's functionality to TinyMCE core. #TINY-8310
- The `paste_data_images` option now defaults to `true`. #TINY-8310
- Moved the `noneditable` plugin to TinyMCE core. #TINY-8311
- Renamed the `noneditable_noneditable_class` option to `noneditable_class`. #TINY-8311
- Renamed the `noneditable_editable_class` option to `editable_class`. #TINY-8311
- Moved the `textpattern` plugin to TinyMCE core. #TINY-8312
- Renamed the `textpattern_patterns` option to `text_patterns`. #TINY-8312
- Moved the `hr` plugin's functionality to TinyMCE core. #TINY-8313
- Moved the `print` plugin's functionality to TinyMCE core. #TINY-8314
- Moved non-UI table functionality to core. #TINY-8273
- The `DomParser` API no longer uses a custom parser internally and instead uses the native `DOMParser` API. #TINY-4627
- The `editor.getContent()` API can provide custom content by preventing and overriding `content` in the `BeforeGetContent` event. This makes it consistent with the `editor.selection.getContent()` API. #TINY-8018
- The `editor.setContent()` API can now be prevented using the `BeforeSetContent` event. This makes it consistent with the `editor.selection.setContent()` API. #TINY-8018
- Add-ons such as plugins and themes are no longer constructed using the `new` operator. #TINY-8256
- A number of APIs that were not proper classes, are no longer constructed using the `new` operator. #TINY-8322
- The Editor commands APIs will no longer fallback to executing the browsers native command functionality. #TINY-7829
- The Editor query command APIs will now return `false` or an empty string on removed editors. #TINY-7829
- The `mceAddEditor` and `mceToggleEditor` commands now take an object as their value to specify the id and editor options. #TINY-8138
- The `mceInsertTable` command can no longer open the insert table dialog. Use the `mceInsertTableDialog` command instead. #TINY-8273
- The `plugins` option now returns a `string` array instead of a space separated string. #TINY-8455
- The `media` plugin no longer treats `iframe`, `video`, `audio` or `object` elements as "special" and will validate the contents against the schema. #TINY-8382
- The `images_upload_handler` option is no longer passed a `success` or `failure` callback and instead requires a `Promise` to be returned with the upload result. #TINY-8325
- The `tinymce.settings` global property is no longer set upon initialization. #TINY-7359
- The `change` event is no longer fired on first modification. #TINY-6920
- The `GetContent` event will now always pass a `string` for the `content` property. #TINY-7996
- Changed the default tag for the strikethrough format to the `s` tag when using a html 5 schema. #TINY-8262
- The `strike` tag is automatically converted to the `s` tag when using a html 5 schema. #TINY-8262
- Aligning a table to the left or right will now use margin styling instead of float styling. #TINY-6558
- The `:` control character has been changed to `~` for the schema `valid_elements` and `extended_valid_elements` options. #TINY-6726
- The `primary` property on dialog buttons has been deprecated. Use the new `buttonType` property instead. #TINY-8304
- Changed the default statusbar element path delimiter from `»` to `›`. #TINY-8372
- Replaced the `Powered by Tiny` branding text with the Tiny logo. #TINY-8371
- The default minimum height of editor has been changed to 100px to prevent the UI disappearing while resizing. #TINY-6860
- RGB colors are no longer converted to hex values when parsing or serializing content. #TINY-8163
- Replaced the `isDisabled()` function with an `isEnabled()` function for various APIs. #TINY-8101
- Replaced the `enable()` and `disable()` functions with a `setEnabled(state)` function in various APIs. #TINY-8101
- Replaced the `disabled` property with an `enabled` property in various APIs. #TINY-8101
- Replaced the `disable(name)` and `enable(name)` functions with a `setEnabled(name, state)` function in the Dialog APIs. #TINY-8101
- Renamed the `tinymce.Env.os.isOSX` API to `tinymce.Env.os.isMacOS`. #TINY-8175
- Renamed the `tinymce.Env.browser.isChrome` API to `tinymce.Env.browser.isChromium` to better reflect its functionality. #TINY-8300
- Renamed the `getShortEndedElements` Schema API to `getVoidElements`. #TINY-8344
- Renamed the `font_formats` option to `font_family_formats`. #TINY-8328
- Renamed the `fontselect` toolbar button and `fontformats` menu item to `fontfamily`. #TINY-8328
- Renamed the `fontsize_formats` option to `font_size_formats`. #TINY-8328
- Renamed the `fontsizeselect` toolbar button and `fontsizes` menu item to `fontsize`. #TINY-8328
- Renamed the `formatselect` toolbar button and `blockformats` menu item to `blocks`. #TINY-8328
- Renamed the `styleselect` toolbar button and `formats` menu item to `styles`. #TINY-8328
- Renamed the `lineheight_formats` option to `line_height_formats`. #TINY-8328
- Renamed the `getWhiteSpaceElements()` function to `getWhitespaceElements()` in the `Schema` API. #TINY-8102
- Renamed the `mceInsertClipboardContent` command `content` property to `html` to better reflect what data is passed. #TINY-8310
- Renamed the `default_link_target` option to `link_default_target` for both `link` and `autolink` plugins. #TINY-4603
- Renamed the `rel_list` option to `link_rel_list` for the `link` plugin. #TINY-4603
- Renamed the `target_list` option to `link_target_list` for the `link` plugin. #TINY-4603
- The default value for the `link_default_protocol` option has been changed to `https` instead of `http`. #TINY-7824
- The default value for the `element_format` option has been changed to `html`. #TINY-8263
- The default value for the `schema` option has been changed to `html5`. #TINY-8261
- The default value for the `table_style_by_css` option has been changed to `true`. #TINY-8259
- The default value for the `table_use_colgroups` option has been changed to `true`. #TINY-8259

### Fixed
- The object returned from the `editor.fire()` API was incorrect if the editor had been removed. #TINY-8018
- The `editor.selection.getContent()` API did not respect the `no_events` argument. #TINY-8018
- The `editor.annotator.remove` API did not keep selection when removing the annotation. #TINY-8195
- The `GetContent` event was not fired when getting `tree` or `text` formats using the `editor.selection.getContent()` API. #TINY-8018
- The `beforeinput` and `input` events would sometimes not fire as expected when deleting content. #TINY-8168 #TINY-8329
- The `table` plugin would sometimes not correctly handle headers in the `tfoot` section. #TINY-8104
- The `silver` theme UI was incorrectly rendered before plugins had initialized. #TINY-8288
- The aria labels for the color picker dialog were not translated. #TINY-8381
- Fixed sub-menu items not read by screen readers. Patch contributed by westonkd. #TINY-8417
- Dialog labels and other text-based UI properties did not escape HTML markup. #TINY-7524
- Anchor elements would render incorrectly when using the `allow_html_in_named_anchor` option. #TINY-3799
- The `AstNode` HTML serializer did not serialize `pre` or `textarea` elements correctly when they contained newlines. #TINY-8446
- Fixed sub-menu items not read by screen readers. Patch contributed by westonkd. #TINY-8417
- The Home or End keys would move out of a editable element contained within a noneditable element. #TINY-8201
- Dialogs could not be opened in inline mode before the editor had been rendered. #TINY-8397
- Clicking on menu items could cause an unexpected console warning if the `onAction` function caused the menu to close. #TINY-8513
- Fixed various color and contrast issues for the dark skins. #TINY-8527

### Removed
- Removed support for Microsoft Internet Explorer 11. #TINY-8194 #TINY-8241
- Removed support for Microsoft Word from the opensource paste functionality. #TINY-7493
- Removed support for the `plugins` option allowing a mixture of a string array and of space separated strings. #TINY-8399
- Removed support for the deprecated `false` value for the `forced_root_block` option. #TINY-8260
- Removed the jQuery integration. #TINY-4519
- Removed the `imagetools` plugin, which is now classified as a Premium plugin. #TINY-8209
- Removed the `imagetools` dialog component. #TINY-8333
- Removed the `toc` plugin, which is now classified as a Premium plugin. #TINY-8250
- Removed the `tabfocus` plugin. #TINY-8315
- Removed the `textpattern` plugin's API as part of moving it to core. #TINY-8312
- Removed the `table` plugin's API. #TINY-8273
- Removed the callback for the `EditorUpload` API. #TINY-8325
- Removed the legacy browser detection properties from the `Env` API. #TINY-8162
- Removed the `filterNode` method from the `DomParser` API. #TINY-8249
- Removed the `SaxParser` API. #TINY-8218
- Removed the `tinymce.utils.Promise` API. #TINY-8241
- Removed the `toHex` function for the `DOMUtils` and `Styles` APIs. #TINY-8163
- Removed the `execCommand` handler function from the plugin and theme interfaces. #TINY-7829
- Removed the `editor.settings` property as it has been replaced by the new Options API. #TINY-8236
- Removed the `shortEnded` and `fixed` properties on `tinymce.html.Node` class. #TINY-8205
- Removed the `mceInsertRawHTML` command. #TINY-8214
- Removed the style field from the `image` plugin dialog advanced tab. #TINY-3422
- Removed the `paste_filter_drop` option as native drag and drop handling is no longer supported. #TINY-8511
- Removed the legacy `mobile` theme. #TINY-7832
- Removed the deprecated `$`, `Class`, `DomQuery` and `Sizzle` APIs. #TINY-4520 #TINY-8326
- Removed the deprecated `Color`, `JSON`, `JSONP` and `JSONRequest`. #TINY-8162
- Removed the deprecated `XHR` API. #TINY-8164
- Removed the deprecated `setIconStroke` Split Toolbar Button API. #TINY-8162
- Removed the deprecated `editors` property from `EditorManager`. #TINY-8162
- Removed the deprecated `execCallback` and `setMode` APIs from `Editor`. #TINY-8162
- Removed the deprecated `addComponents` and `dependencies` APIs from `AddOnManager`. #TINY-8162
- Removed the deprecated `clearInterval`, `clearTimeout`, `debounce`, `requestAnimationFrame`, `setInterval`, `setTimeout` and `throttle` APIs from `Delay`. #TINY-8162
- Removed the deprecated `Schema` options. #TINY-7821
- Removed the deprecated `file_browser_callback_types`, `force_hex_style_colors` and `images_dataimg_filter` options. #TINY-7823
- Removed the deprecated `filepicker_validator_handler`, `force_p_newlines`, `gecko_spellcheck`, `tab_focus`, `table_responsive_width` and `toolbar_drawer` options. #TINY-7820
- Removed the deprecated `media_scripts` option in the `media` plugin. #TINY-8421
- Removed the deprecated `editor_deselector`, `editor_selector`, `elements`, `mode` and `types` legacy TinyMCE init options. #TINY-7822
- Removed the deprecated `content_editable_state` and `padd_empty_with_br` options. #TINY-8400
- Removed the deprecated `autoresize_on_init` option from the `autoresize` plugin. #TINY-8400
- Removed the deprecated `fullpage`, `spellchecker`, `bbcode`, `legacyoutput`, `colorpicker`, `contextmenu` and `textcolor` plugins. #TINY-8192
- Removed the undocumented `editor.editorCommands.hasCustomCommand` API. #TINY-7829
- Removed the undocumented `mceResetDesignMode`, `mceRepaint` and `mceBeginUndoLevel` commands. #TINY-7829

### Deprecated
- The dialog button component's `primary` property has been deprecated and will be removed in the next major release. Use the new `buttonType` property instead. #TINY-8304
- The `fire()` function of `tinymce.Editor`, `tinymce.dom.EventUtils`, `tinymce.dom.DOMUtils`, `tinymce.util.Observable` and `tinymce.util.EventDispatcher` has been deprecated and will be removed in the next major release. Use the `dispatch()` function instead. #TINY-8102
- The `content` property on the `SetContent` event has been deprecated and will be removed in the next major release. #TINY-8457
- The return value of the `editor.setContent` API has been deprecated and will be removed in the next major release. #TINY-8457

## 5.10.3 - 2022-02-09

### Fixed
- Alignment would sometimes be removed on parent elements when changing alignment on certain inline nodes, such as images. #TINY-8308
- The `fullscreen` plugin would reset the scroll position when exiting fullscreen mode. #TINY-8418

## 5.10.2 - 2021-11-17

### Fixed
- Internal selectors were appearing in the style list when using the `importcss` plugin. #TINY-8238

## 5.10.1 - 2021-11-03

### Fixed
- The iframe aria help text was not read by some screen readers. #TINY-8171
- Clicking the `forecolor` or `backcolor` toolbar buttons would do nothing until selecting a color. #TINY-7836
- Crop functionality did not work in the `imagetools` plugin when the editor was rendered in a shadow root. #TINY-6387
- Fixed an exception thrown on Safari when closing the `searchreplace` plugin dialog. #TINY-8166
- The `autolink` plugin did not convert URLs to links when starting with a bracket. #TINY-8091
- The `autolink` plugin incorrectly created nested links in some cases. #TINY-8091
- Tables could have an incorrect height set on rows when rendered outside of the editor. #TINY-7699
- In certain circumstances, the table of contents plugin would incorrectly add an extra empty list item. #TINY-4636
- The insert table grid menu displayed an incorrect size when re-opening the grid. #TINY-6532
- The word count plugin was treating the zero width space character (`&#8203;`) as a word. #TINY-7484

## 5.10.0 - 2021-10-11

### Added
- Added a new `URI.isDomSafe(uri)` API to check if a URI is considered safe to be inserted into the DOM. #TINY-7998
- Added the `ESC` key code constant to the `VK` API. #TINY-7917
- Added a new `deprecation_warnings` setting for turning off deprecation console warning messages. #TINY-8049

### Improved
- The `element` argument of the `editor.selection.scrollIntoView()` API is now optional, and if it is not provided the current selection will be scrolled into view. #TINY-7291

### Changed
- The deprecated `scope` attribute is no longer added to `td` cells when converting a row to a header row. #TINY-7731
- The number of `col` elements is normalized to match the number of columns in a table after a table action. #TINY-8011

### Fixed
- Fixed a regression that caused block wrapper formats to apply and remove incorrectly when using a collapsed selection with multiple words. #TINY-8036
- Resizing table columns in some scenarios would resize the column to an incorrect position. #TINY-7731
- Inserting a table where the parent element had padding would cause the table width to be incorrect. #TINY-7991
- The resize backdrop element did not have the `data-mce-bogus="all"` attribute set to prevent it being included in output. #TINY-7854
- Resize handles appeared on top of dialogs and menus when using an inline editor. #TINY-3263
- Fixed the `autoresize` plugin incorrectly scrolling to the top of the editor content in some cases when changing content. #TINY-7291
- Fixed the `editor.selection.scrollIntoView()` type signature, as it incorrectly required an `Element` instead of `HTMLElement`. #TINY-7291
- Table cells that were both row and column headers did not retain the correct state when converting back to a regular row or column. #TINY-7709
- Clicking beside a non-editable element could cause the editor to incorrectly scroll to the top of the content. #TINY-7062
- Clicking in a table cell, with a non-editable element in an adjacent cell, incorrectly caused the non-editable element to be selected. #TINY-7736
- Split toolbar buttons incorrectly had nested `tabindex="-1"` attributes. #TINY-7879
- Fixed notifications rendering in the wrong place initially and when the page was scrolled. #TINY-7894
- Fixed an exception getting thrown when the number of `col` elements didn't match the number of columns in a table. #TINY-7041 #TINY-8011
- The table selection state could become incorrect after selecting a noneditable table cell. #TINY-8053
- As of Mozilla Firefox 91, toggling fullscreen mode with `toolbar_sticky` enabled would cause the toolbar to disappear. #TINY-7873
- Fixed URLs not cleaned correctly in some cases in the `link` and `image` plugins. #TINY-7998
- Fixed the `image` and `media` toolbar buttons incorrectly appearing to be in an inactive state in some cases. #TINY-3463
- Fixed the `editor.selection.selectorChanged` API not firing if the selector matched the current selection when registered in some cases. #TINY-3463
- Inserting content into a `contenteditable="true"` element that was contained within a `contenteditable="false"` element would move the selection to an incorrect location. #TINY-7842
- Dragging and dropping `contenteditable="false"` elements could result in the element being placed in an unexpected location. #TINY-7917
- Pressing the Escape key would not cancel a drag action that started on a `contenteditable="false"` element within the editor. #TINY-7917
- `video` and `audio` elements were unable to be played when the `media` plugin live embeds were enabled in some cases. #TINY-7674
- Pasting images would throw an exception if the clipboard `items` were not files (for example, screenshots taken from gnome-software). Patch contributed by cedric-anne. #TINY-8079

### Deprecated
- Several APIs have been deprecated. See the release notes section for information. #TINY-8023 #TINY-8063
- Several Editor settings have been deprecated. See the release notes section for information. #TINY-8086
- The Table of Contents and Image Tools plugins will be classified as Premium plugins in the next major release. #TINY-8087
- Word support in the `paste` plugin has been deprecated and will be removed in the next major release. #TINY-8087

## 5.9.2 - 2021-09-08

### Fixed
- Fixed an exception getting thrown when disabling events and setting content. #TINY-7956
- Delete operations could behave incorrectly if the selection crossed a table boundary. #TINY-7596

## 5.9.1 - 2021-08-27

### Fixed
- Published TinyMCE types failed to compile in strict mode. #TINY-7915
- The `TableModified` event sometimes didn't fire when performing certain table actions. #TINY-7916

## 5.9.0 - 2021-08-26

### Added
- Added a new `mceFocus` command that focuses the editor. Equivalent to using `editor.focus()`. #TINY-7373
- Added a new `mceTableToggleClass` command which toggles the provided class on the currently selected table. #TINY-7476
- Added a new `mceTableCellToggleClass` command which toggles the provided class on the currently selected table cells. #TINY-7476
- Added a new `tablecellvalign` toolbar button and menu item for vertical table cell alignment. #TINY-7477
- Added a new `tablecellborderwidth` toolbar button and menu item to change table cell border width. #TINY-7478
- Added a new `tablecellborderstyle` toolbar button and menu item to change table cell border style. #TINY-7478
- Added a new `tablecaption` toolbar button and menu item to toggle captions on tables. #TINY-7479
- Added a new `mceTableToggleCaption` command that toggles captions on a selected table. #TINY-7479
- Added a new `tablerowheader` toolbar button and menu item to toggle the header state of row cells. #TINY-7478
- Added a new `tablecolheader` toolbar button and menu item to toggle the header state of column cells. #TINY-7482
- Added a new `tablecellbordercolor` toolbar button and menu item to select table cell border colors, with an accompanying setting `table_border_color_map` to customize the available values. #TINY-7480
- Added a new `tablecellbackgroundcolor` toolbar button and menu item to select table cell background colors, with an accompanying setting `table_background_color_map` to customize the available values. #TINY-7480
- Added a new `language` menu item and toolbar button to add `lang` attributes to content, with an accompanying `content_langs` setting to specify the languages available. #TINY-6149
- A new `lang` format is now available that can be used with `editor.formatter`, or applied with the `Lang` editor command. #TINY-6149
- Added a new `language` icon for the `language` toolbar button. #TINY-7670
- Added a new `table-row-numbering` icon. #TINY-7327
- Added new plugin commands: `mceEmoticons` (Emoticons), `mceWordCount` (Word Count), and `mceTemplate` (Template). #TINY-7619
- Added a new `iframe_aria_text` setting to set the iframe title attribute. #TINY-1264
- Added a new DomParser `Node.children()` API to return all the children of a `Node`. #TINY-7756

### Improved
- Sticky toolbars can now be offset from the top of the page using the new `toolbar_sticky_offset` setting. #TINY-7337
- Fancy menu items now accept an `initData` property to allow custom initialization data. #TINY-7480
- Improved the load time of the `fullpage` plugin by using the existing editor schema rather than creating a new one. #TINY-6504
- Improved the performance when UI components are rendered. #TINY-7572
- The context toolbar no longer unnecessarily repositions to the top of large elements when scrolling. #TINY-7545
- The context toolbar will now move out of the way when it overlaps with the selection, such as in table cells. #TINY-7192
- The context toolbar now uses a short animation when transitioning between different locations. #TINY-7740
- `Env.browser` now uses the User-Agent Client Hints API where it is available. #TINY-7785
- Icons with a `-rtl` suffix in their name will now automatically be used when the UI is rendered in right-to-left mode. #TINY-7782
- The `formatter.match` API now accepts an optional `similar` parameter to check if the format partially matches. #TINY-7712
- The `formatter.formatChanged` API now supports providing format variables when listening for changes. #TINY-7713
- The formatter will now fire `FormatApply` and `FormatRemove` events for the relevant actions. #TINY-7713
- The `autolink` plugin link detection now permits custom protocols. #TINY-7714
- The `autolink` plugin valid link detection has been improved. #TINY-7714

### Changed
- Changed the load order so content CSS is loaded before the editor is populated with content. #TINY-7249
- Changed the `emoticons`, `wordcount`, `code`, `codesample`, and `template` plugins to open dialogs using commands. #TINY-7619
- The context toolbar will no longer show an arrow when it overlaps the content, such as in table cells. #TINY-7665
- The context toolbar will no longer overlap the statusbar for toolbars using `node` or `selection` positions. #TINY-7666

### Fixed
- The `editor.fire` API was incorrectly mutating the original `args` provided. #TINY-3254
- Unbinding an event handler did not take effect immediately while the event was firing. #TINY-7436
- Binding an event handler incorrectly took effect immediately while the event was firing. #TINY-7436
- Unbinding a native event handler inside the `remove` event caused an exception that blocked editor removal. #TINY-7730
- The `SetContent` event contained the incorrect `content` when using the `editor.selection.setContent()` API. #TINY-3254
- The editor content could be edited after calling `setProgressState(true)` in iframe mode. #TINY-7373
- Tabbing out of the editor after calling `setProgressState(true)` behaved inconsistently in iframe mode. #TINY-7373
- Flash of unstyled content while loading the editor because the content CSS was loaded after the editor content was rendered. #TINY-7249
- Partially transparent RGBA values provided in the `color_map` setting were given the wrong hex value. #TINY-7163
- HTML comments with mismatched quotes were parsed incorrectly under certain circumstances. #TINY-7589
- The editor could crash when inserting certain HTML content. #TINY-7756
- Inserting certain HTML content into the editor could result in invalid HTML once parsed. #TINY-7756
- Links in notification text did not show the correct mouse pointer. #TINY-7661
- Using the Tab key to navigate into the editor on Microsoft Internet Explorer 11 would incorrectly focus the toolbar. #TINY-3707
- The editor selection could be placed in an incorrect location when undoing or redoing changes in a document containing `contenteditable="false"` elements. #TINY-7663
- Menus and context menus were not closed when clicking into a different editor. #TINY-7399
- Context menus on Android were not displayed when more than one HTML element was selected. #TINY-7688
- Disabled nested menu items could still be opened. #TINY-7700
- The nested menu item chevron icon was not fading when the menu item was disabled. #TINY-7700
- `imagetools` buttons were incorrectly enabled for remote images without `imagetools_proxy` set. #TINY-7772
- Only table content would be deleted when partially selecting a table and content outside the table. #TINY-6044
- The table cell selection handling was incorrect in some cases when dealing with nested tables. #TINY-6298
- Removing a table row or column could result in the cursor getting placed in an invalid location. #TINY-7695
- Pressing the Tab key to navigate through table cells did not skip noneditable cells. #TINY-7705
- Clicking on a noneditable table cell did not show a visual selection like other noneditable elements. #TINY-7724
- Some table operations would incorrectly cause table row attributes and styles to be lost. #TINY-6666
- The selection was incorrectly lost when using the `mceTableCellType` and `mceTableRowType` commands. #TINY-6666
- The `mceTableRowType` was reversing the order of the rows when converting multiple header rows back to body rows. #TINY-6666
- The table dialog did not always respect the `table_style_with_css` option. #TINY-4926
- Pasting into a table with multiple cells selected could cause the content to be pasted in the wrong location. #TINY-7485
- The `TableModified` event was not fired when pasting cells into a table. #TINY-6939
- The table paste column before and after icons were not flipped in RTL mode. #TINY-7851
- Fixed table corruption when deleting a `contenteditable="false"` cell. #TINY-7891
- The `dir` attribute was being incorrectly applied to list items. #TINY-4589
- Applying selector formats would sometimes not apply the format correctly to elements in a list. #TINY-7393
- For formats that specify an attribute or style that should be removed, the formatter `match` API incorrectly returned `false`. #TINY-6149
- The type signature on the `formatter.matchNode` API had the wrong return type (was `boolean` but should have been `Formatter | undefined`). #TINY-6149
- The `formatter.formatChanged` API would ignore the `similar` parameter if another callback had already been registered for the same format. #TINY-7713
- The `formatter.formatChanged` API would sometimes not run the callback the first time the format was removed. #TINY-7713
- Base64 encoded images with spaces or line breaks in the data URI were not displayed correctly. Patch contributed by RoboBurned.

### Deprecated
- The `bbcode`, `fullpage`, `legacyoutput`, and `spellchecker` plugins have been deprecated and marked for removal in the next major release. #TINY-7260

## 5.8.2 - 2021-06-23

### Fixed
- Fixed an issue when pasting cells from tables containing `colgroup`s into tables without `colgroup`s. #TINY-6675
- Fixed an issue that could cause an invalid toolbar button state when multiple inline editors were on a single page. #TINY-6297

## 5.8.1 - 2021-05-20

### Fixed
- An unexpected exception was thrown when switching to readonly mode and adjusting the editor width. #TINY-6383
- Content could be lost when the `pagebreak_split_block` setting was enabled. #TINY-3388
- The `list-style-type: none;` style on nested list items was incorrectly removed when clearing formatting. #TINY-6264
- URLs were not always detected when pasting over a selection. Patch contributed by jwcooper. #TINY-6997
- Properties on the `OpenNotification` event were incorrectly namespaced. #TINY-7486

## 5.8.0 - 2021-05-06

### Added
- Added the `PAGE_UP` and `PAGE_DOWN` key code constants to the `VK` API. #TINY-4612
- The editor resize handle can now be controlled using the keyboard. #TINY-4823
- Added a new `fixed_toolbar_container_target` setting which renders the toolbar in the specified `HTMLElement`. Patch contributed by pvrobays.

### Improved
- The `inline_boundaries` feature now supports the `home`, `end`, `pageup`, and `pagedown` keys. #TINY-4612
- Updated the `formatter.matchFormat` API to support matching formats with variables in the `classes` property. #TINY-7227
- Added HTML5 `audio` and `video` elements to the default alignment formats. #TINY-6633
- Added support for alpha list numbering to the list properties dialog. #TINY-6891

### Changed
- Updated the `image` dialog to display the class list dropdown as full-width if the caption checkbox is not present. #TINY-6400
- Renamed the "H Align" and "V Align" input labels in the Table Cell Properties dialog to "Horizontal align" and "Vertical align" respectively. #TINY-7285

### Deprecated
- The undocumented `setIconStroke` Split Toolbar Button API has been deprecated and will be removed in a future release. #TINY-3551

### Fixed
- Fixed a bug where it wasn't possible to align nested list items. #TINY-6567
- The RGB fields in the color picker dialog were not staying in sync with the color palette and hue slider. #TINY-6952
- The color preview box in the color picker dialog was not correctly displaying the saturation and value of the chosen color. #TINY-6952
- The color picker dialog will now show an alert if it is submitted with an invalid hex color code. #TINY-2814
- Fixed a bug where the `TableModified` event was not fired when adding a table row with the Tab key. #TINY-7006
- Added missing `images_file_types` setting to the exported TypeScript types. #GH-6607
- Fixed a bug where lists pasted from Word with Roman numeral markers were not displayed correctly. Patch contributed by aautio. #GH-6620
- The `editor.insertContent` API was incorrectly handling nested `span` elements with matching styles. #TINY-6263
- The HTML5 `small` element could not be removed when clearing text formatting. #TINY-6633
- The Oxide button text transform variable was incorrectly using `capitalize` instead of `none`. Patch contributed by dakur. #GH-6341
- Fix dialog button text that was using title-style capitalization. #TINY-6816
- Table plugin could perform operations on tables containing the inline editor. #TINY-6625
- Fixed Tab key navigation inside table cells with a ranged selection. #TINY-6638
- The foreground and background toolbar button color indicator is no longer blurry. #TINY-3551
- Fixed a regression in the `tinymce.create()` API that caused issues when multiple objects were created. #TINY-7358
- Fixed the `LineHeight` command causing the `change` event to be fired inconsistently. #TINY-7048

## 5.7.1 - 2021-03-17

### Fixed
- Fixed the `help` dialog incorrectly linking to the changelog of TinyMCE 4 instead of TinyMCE 5. #TINY-7031
- Fixed a bug where error messages were displayed incorrectly in the image dialog. #TINY-7099
- Fixed an issue where URLs were not correctly filtered in some cases. #TINY-7025
- Fixed a bug where context menu items with names that contained uppercase characters were not displayed. #TINY-7072
- Fixed context menu items lacking support for the `disabled` and `shortcut` properties. #TINY-7073
- Fixed a regression where the width and height were incorrectly set when embedding content using the `media` dialog. #TINY-7074

## 5.7.0 - 2021-02-10

### Added
- Added IPv6 address support to the URI API. Patch contributed by dev7355608. #GH-4409
- Added new `structure` and `style` properties to the `TableModified` event to indicate what kinds of modifications were made. #TINY-6643
- Added `video` and `audio` live embed support for the `media` plugin. #TINY-6229
- Added the ability to resize `video` and `iframe` media elements. #TINY-6229
- Added a new `font_css` setting for adding fonts to both the editor and the parent document. #TINY-6199
- Added a new `ImageUploader` API to simplify uploading image data to the configured `images_upload_url` or `images_upload_handler`. #TINY-4601
- Added an Oxide variable to define the container background color in fullscreen mode. #TINY-6903
- Added Oxide variables for setting the toolbar background colors for inline and sticky toolbars. #TINY-6009
- Added a new `AfterProgressState` event that is fired after `editor.setProgressState` calls complete. #TINY-6686
- Added support for `table_column_resizing` when inserting or deleting columns. #TINY-6711

### Changed
- Changed table and table column copy behavior to retain an appropriate width when pasted. #TINY-6664
- Changed the `lists` plugin to apply list styles to all text blocks within a selection. #TINY-3755
- Changed the `advlist` plugin to log a console error message when the `list` plugin isn't enabled. #TINY-6585
- Changed the z-index of the `setProgressState(true)` throbber so it does not hide notifications. #TINY-6686
- Changed the type signature for `editor.selection.getRng()` incorrectly returning `null`. #TINY-6843
- Changed some `SaxParser` regular expressions to improve performance. #TINY-6823
- Changed `editor.setProgressState(true)` to close any open popups. #TINY-6686

### Fixed
- Fixed `codesample` highlighting performance issues for some languages. #TINY-6996
- Fixed an issue where cell widths were lost when merging table cells. #TINY-6901
- Fixed `col` elements incorrectly transformed to `th` elements when converting columns to header columns. #TINY-6715
- Fixed a number of table operations not working when selecting 2 table cells on Mozilla Firefox. #TINY-3897
- Fixed a memory leak by backporting an upstream Sizzle fix. #TINY-6859
- Fixed table `width` style was removed when copying. #TINY-6664
- Fixed focus lost while typing in the `charmap` or `emoticons` dialogs when the editor is rendered in a shadow root. #TINY-6904
- Fixed corruption of base64 URLs used in style attributes when parsing HTML. #TINY-6828
- Fixed the order of CSS precedence of `content_style` and `content_css` in the `preview` and `template` plugins. `content_style` now has precedence. #TINY-6529
- Fixed an issue where the image dialog tried to calculate image dimensions for an empty image URL. #TINY-6611
- Fixed an issue where `scope` attributes on table cells would not change as expected when merging or unmerging cells. #TINY-6486
- Fixed the plugin documentation links in the `help` plugin. #DOC-703
- Fixed events bound using `DOMUtils` not returning the correct result for `isDefaultPrevented` in some cases. #TINY-6834
- Fixed the "Dropped file type is not supported" notification incorrectly showing when using an inline editor. #TINY-6834
- Fixed an issue with external styles bleeding into TinyMCE. #TINY-6735
- Fixed an issue where parsing malformed comments could cause an infinite loop. #TINY-6864
- Fixed incorrect return types on `editor.selection.moveToBookmark`. #TINY-6504
- Fixed the type signature for `editor.selection.setCursorLocation()` incorrectly allowing a node with no `offset`. #TINY-6843
- Fixed incorrect behavior when editor is destroyed while loading stylesheets. #INT-2282
- Fixed figure elements incorrectly splitting from a valid parent element when editing the image within. #TINY-6592
- Fixed inserting multiple rows or columns in a table cloning from the incorrect source row or column. #TINY-6906
- Fixed an issue where new lines were not scrolled into view when pressing Shift+Enter or Shift+Return. #TINY-6964
- Fixed an issue where list elements would not be removed when outdenting using the Enter or Return key. #TINY-5974
- Fixed an issue where file extensions with uppercase characters were treated as invalid. #TINY-6940
- Fixed dialog block messages were not passed through TinyMCE's translation system. #TINY-6971

## 5.6.2 - 2020-12-08

### Fixed
- Fixed a UI rendering regression when the document body is using `display: flex`. #TINY-6783

## 5.6.1 - 2020-11-25

### Fixed
- Fixed the `mceTableRowType` and `mceTableCellType` commands were not firing the `newCell` event. #TINY-6692
- Fixed the HTML5 `s` element was not recognized when editing or clearing text formatting. #TINY-6681
- Fixed an issue where copying and pasting table columns resulted in invalid HTML when using colgroups. #TINY-6684
- Fixed an issue where the toolbar would render with the wrong width for inline editors in some situations. #TINY-6683

## 5.6.0 - 2020-11-18

### Added
- Added new `BeforeOpenNotification` and `OpenNotification` events which allow internal notifications to be captured and modified before display. #TINY-6528
- Added support for `block` and `unblock` methods on inline dialogs. #TINY-6487
- Added new `TableModified` event which is fired whenever changes are made to a table. #TINY-6629
- Added new `images_file_types` setting to determine which image file formats will be automatically processed into `img` tags on paste when using the `paste` plugin. #TINY-6306
- Added support for `images_file_types` setting in the image file uploader to determine which image file extensions are valid for upload. #TINY-6224
- Added new `format_empty_lines` setting to control if empty lines are formatted in a ranged selection. #TINY-6483
- Added template support to the `autocompleter` for customizing the autocompleter items. #TINY-6505
- Added new user interface `enable`, `disable`, and `isDisabled` methods. #TINY-6397
- Added new `closest` formatter API to get the closest matching selection format from a set of formats. #TINY-6479
- Added new `emojiimages` emoticons database that uses the twemoji CDN by default. #TINY-6021
- Added new `emoticons_database` setting to configure which emoji database to use. #TINY-6021
- Added new `name` field to the `style_formats` setting object to enable specifying a name for the format. #TINY-4239

### Changed
- Changed `readonly` mode to allow hyperlinks to be clickable. #TINY-6248

### Fixed
- Fixed the `change` event not firing after a successful image upload. #TINY-6586
- Fixed the type signature for the `entity_encoding` setting not accepting delimited lists. #TINY-6648
- Fixed layout issues when empty `tr` elements were incorrectly removed from tables. #TINY-4679
- Fixed image file extensions lost when uploading an image with an alternative extension, such as `.jfif`. #TINY-6622
- Fixed a security issue where URLs in attributes weren't correctly sanitized. #TINY-6518
- Fixed `DOMUtils.getParents` incorrectly including the shadow root in the array of elements returned. #TINY-6540
- Fixed an issue where the root document could be scrolled while an editor dialog was open inside a shadow root. #TINY-6363
- Fixed `getContent` with text format returning a new line when the editor is empty. #TINY-6281
- Fixed table column and row resizers not respecting the `data-mce-resize` attribute. #TINY-6600
- Fixed inserting a table via the `mceInsertTable` command incorrectly creating 2 undo levels. #TINY-6656
- Fixed nested tables with `colgroup` elements incorrectly always resizing the inner table. #TINY-6623
- Fixed the `visualchars` plugin causing the editor to steal focus when initialized. #TINY-6282
- Fixed `fullpage` plugin altering text content in `editor.getContent()`. #TINY-6541
- Fixed `fullscreen` plugin not working correctly with multiple editors and shadow DOM. #TINY-6280
- Fixed font size keywords such as `medium` not displaying correctly in font size menus. #TINY-6291
- Fixed an issue where some attributes in table cells were not copied over to new rows or columns. #TINY-6485
- Fixed incorrectly removing formatting on adjacent spaces when removing formatting on a ranged selection. #TINY-6268
- Fixed the `Cut` menu item not working in the latest version of Mozilla Firefox. #TINY-6615
- Fixed some incorrect types in the new TypeScript declaration file. #TINY-6413
- Fixed a regression where a fake offscreen selection element was incorrectly created for the editor root node. #TINY-6555
- Fixed an issue where menus would incorrectly collapse in small containers. #TINY-3321
- Fixed an issue where only one table column at a time could be converted to a header. #TINY-6326
- Fixed some minor memory leaks that prevented garbage collection for editor instances. #TINY-6570
- Fixed resizing a `responsive` table not working when using the column resize handles. #TINY-6601
- Fixed incorrectly calculating table `col` widths when resizing responsive tables. #TINY-6646
- Fixed an issue where spaces were not preserved in pre-blocks when getting text content. #TINY-6448
- Fixed a regression that caused the selection to be difficult to see in tables with backgrounds. #TINY-6495
- Fixed content pasted multiple times in the editor when using Microsoft Internet Explorer 11. Patch contributed by mattford. #GH-4905

## 5.5.1 - 2020-10-01

### Fixed
- Fixed pressing the down key near the end of a document incorrectly raising an exception. #TINY-6471
- Fixed incorrect Typescript types for the `Tools` API. #TINY-6475

## 5.5.0 - 2020-09-29

### Added
- Added a TypeScript declaration file to the bundle output for TinyMCE core. #TINY-3785
- Added new `table_column_resizing` setting to control how table columns are resized when using the resize bars. #TINY-6001
- Added the ability to remove images on a failed upload using the `images_upload_handler` failure callback. #TINY-6011
- Added `hasPlugin` function to the editor API to determine if a plugin exists or not. #TINY-766
- Added new `ToggleToolbarDrawer` command and query state handler to allow the toolbar drawer to be programmatically toggled and the toggle state to be checked. #TINY-6032
- Added the ability to use `colgroup` elements in tables. #TINY-6050
- Added a new setting `table_use_colgroups` for toggling whether colgroups are used in new tables. #TINY-6050
- Added the ability to delete and navigate HTML media elements without the `media` plugin. #TINY-4211
- Added `fullscreen_native` setting to the `fullscreen` plugin to enable use of the entire monitor. #TINY-6284
- Added table related oxide variables to the Style API for more granular control over table cell selection appearance. #TINY-6311
- Added new `toolbar_persist` setting to control the visibility of the inline toolbar. #TINY-4847
- Added new APIs to allow for programmatic control of the inline toolbar visibility. #TINY-4847
- Added the `origin` property to the `ObjectResized` and `ObjectResizeStart` events, to specify which handle the resize was performed on. #TINY-6242
- Added new StyleSheetLoader `unload` and `unloadAll` APIs to allow loaded stylesheets to be removed. #TINY-3926
- Added the `LineHeight` query command and action to the editor. #TINY-4843
- Added the `lineheight` toolbar and menu items, and added `lineheight` to the default format menu. #TINY-4843
- Added a new `contextmenu_avoid_overlap` setting to allow context menus to avoid overlapping matched nodes. #TINY-6036
- Added new listbox dialog UI component for rendering a dropdown that allows nested options. #TINY-2236
- Added back the ability to use nested items in the `image_class_list`, `link_class_list`, `link_list`, `table_class_list`, `table_cell_class_list`, and `table_row_class_list` settings. #TINY-2236

### Changed
- Changed how CSS manipulates table cells when selecting multiple cells to achieve a semi-transparent selection. #TINY-6311
- Changed the `target` property on fired events to use the native event target. The original target for an open shadow root can be obtained using `event.getComposedPath()`. #TINY-6128
- Changed the editor to clean-up loaded CSS stylesheets when all editors using the stylesheet have been removed. #TINY-3926
- Changed `imagetools` context menu icon for accessing the `image` dialog to use the `image` icon. #TINY-4141
- Changed the `editor.insertContent()` and `editor.selection.setContent()` APIs to retain leading and trailing whitespace. #TINY-5966
- Changed the `table` plugin `Column` menu to include the cut, copy and paste column menu items. #TINY-6374
- Changed the default table styles in the content CSS files to better support the styling options available in the `table` dialog. #TINY-6179

### Deprecated
- Deprecated the `Env.experimentalShadowDom` flag. #TINY-6128

### Fixed
- Fixed tables with no borders displaying with the default border styles in the `preview` dialog. #TINY-6179
- Fixed loss of whitespace when inserting content after a non-breaking space. #TINY-5966
- Fixed the `event.getComposedPath()` function throwing an exception for events fired from the editor. #TINY-6128
- Fixed notifications not appearing when the editor is within a ShadowRoot. #TINY-6354
- Fixed focus issues with inline dialogs when the editor is within a ShadowRoot. #TINY-6360
- Fixed the `template` plugin previews missing some content styles. #TINY-6115
- Fixed the `media` plugin not saving the alternative source url in some situations. #TINY-4113
- Fixed an issue where column resizing using the resize bars was inconsistent between fixed and relative table widths. #TINY-6001
- Fixed an issue where dragging and dropping within a table would select table cells. #TINY-5950
- Fixed up and down keyboard navigation not working for inline `contenteditable="false"` elements. #TINY-6226
- Fixed dialog not retrieving `close` icon from icon pack. #TINY-6445
- Fixed the `unlink` toolbar button not working when selecting multiple links. #TINY-4867
- Fixed the `link` dialog not showing the "Text to display" field in some valid cases. #TINY-5205
- Fixed the `DOMUtils.split()` API incorrectly removing some content. #TINY-6294
- Fixed pressing the escape key not focusing the editor when using multiple toolbars. #TINY-6230
- Fixed the `dirty` flag not being correctly set during an `AddUndo` event. #TINY-4707
- Fixed `editor.selection.setCursorLocation` incorrectly placing the cursor outside `pre` elements in some circumstances. #TINY-4058
- Fixed an exception being thrown when pressing the enter key inside pre elements while `br_in_pre` setting is false. #TINY-4058

## 5.4.2 - 2020-08-17

### Fixed
- Fixed the editor not resizing when resizing the browser window in fullscreen mode. #TINY-3511
- Fixed clicking on notifications causing inline editors to hide. #TINY-6058
- Fixed an issue where link URLs could not be deleted or edited in the link dialog in some cases. #TINY-4706
- Fixed a regression where setting the `anchor_top` or `anchor_bottom` options to `false` was not working. #TINY-6256
- Fixed the `anchor` plugin not supporting the `allow_html_in_named_anchor` option. #TINY-6236
- Fixed an exception thrown when removing inline formats that contained additional styles or classes. #TINY-6288
- Fixed an exception thrown when positioning the context toolbar on Internet Explorer 11 in some edge cases. #TINY-6271
- Fixed inline formats not removed when more than one `removeformat` format rule existed. #TINY-6216
- Fixed an issue where spaces were sometimes removed when removing formating on nearby text. #TINY-6251
- Fixed the list toolbar buttons not showing as active when a list is selected. #TINY-6286
- Fixed an issue where the UI would sometimes not be shown or hidden when calling the show or hide API methods on the editor. #TINY-6048
- Fixed the list type style not retained when copying list items. #TINY-6289
- Fixed the Paste plugin converting tabs in plain text to a single space character. A `paste_tab_spaces` option has been included for setting the number of spaces used to replace a tab character. #TINY-6237

## 5.4.1 - 2020-07-08

### Fixed
- Fixed the Search and Replace plugin incorrectly including zero-width caret characters in search results. #TINY-4599
- Fixed dragging and dropping unsupported files navigating the browser away from the editor. #TINY-6027
- Fixed undo levels not created on browser handled drop or paste events. #TINY-6027
- Fixed content in an iframe element parsing as DOM elements instead of text content. #TINY-5943
- Fixed Oxide checklist styles not showing when printing. #TINY-5139
- Fixed bug with `scope` attribute not being added to the cells of header rows. #TINY-6206

## 5.4.0 - 2020-06-30

### Added
- Added keyboard navigation support to menus and toolbars when the editor is in a ShadowRoot. #TINY-6152
- Added the ability for menus to be clicked when the editor is in an open shadow root. #TINY-6091
- Added the `Editor.ui.styleSheetLoader` API for loading stylesheets within the Document or ShadowRoot containing the editor UI. #TINY-6089
- Added the `StyleSheetLoader` module to the public API. #TINY-6100
- Added Oxide variables for styling the `select` element and headings in dialog content. #TINY-6070
- Added icons for `table` column and row cut, copy, and paste toolbar buttons. #TINY-6062
- Added all `table` menu items to the UI registry, so they can be used by name in other menus. #TINY-4866
- Added new `mceTableApplyCellStyle` command to the `table` plugin. #TINY-6004
- Added new `table` cut, copy, and paste column editor commands and menu items. #TINY-6006
- Added font related Oxide variables for secondary buttons, allowing for custom styling. #TINY-6061
- Added new `table_header_type` setting to control how table header rows are structured. #TINY-6007
- Added new `table_sizing_mode` setting to replace the `table_responsive_width` setting, which has now been deprecated. #TINY-6051
- Added new `mceTableSizingMode` command for changing the sizing mode of a table. #TINY-6000
- Added new `mceTableRowType`, `mceTableColType`, and `mceTableCellType` commands and value queries. #TINY-6150

### Changed
- Changed `advlist` toolbar buttons to only show a dropdown list if there is more than one option. #TINY-3194
- Changed `mceInsertTable` command and `insertTable` API method to take optional header rows and columns arguments. #TINY-6012
- Changed stylesheet loading, so that UI skin stylesheets can load in a ShadowRoot if required. #TINY-6089
- Changed the DOM location of menus so that they display correctly when the editor is in a ShadowRoot. #TINY-6093
- Changed the table plugin to correctly detect all valid header row structures. #TINY-6007

### Fixed
- Fixed tables with no defined width being converted to a `fixed` width table when modifying the table. #TINY-6051
- Fixed the `autosave` `isEmpty` API incorrectly detecting non-empty content as empty. #TINY-5953
- Fixed table `Paste row after` and `Paste row before` menu items not disabled when nothing was available to paste. #TINY-6006
- Fixed a selection performance issue with large tables on Microsoft Internet Explorer and Edge. #TINY-6057
- Fixed filters for screening commands from the undo stack to be case-insensitive. #TINY-5946
- Fixed `fullscreen` plugin now removes all classes when the editor is closed. #TINY-4048
- Fixed handling of mixed-case icon identifiers (names) for UI elements. #TINY-3854
- Fixed leading and trailing spaces lost when using `editor.selection.getContent({ format: 'text' })`. #TINY-5986
- Fixed an issue where changing the URL with the quicklink toolbar caused unexpected undo behavior. #TINY-5952
- Fixed an issue where removing formatting within a table cell would cause Internet Explorer 11 to scroll to the end of the table. #TINY-6049
- Fixed an issue where the `allow_html_data_urls` setting was not correctly applied. #TINY-5951
- Fixed the `autolink` feature so that it no longer treats a string with multiple "@" characters as an email address. #TINY-4773
- Fixed an issue where removing the editor would leave unexpected attributes on the target element. #TINY-4001
- Fixed the `link` plugin now suggest `mailto:` when the text contains an '@' and no slashes (`/`). #TINY-5941
- Fixed the `valid_children` check of custom elements now allows a wider range of characters in names. #TINY-5971

## 5.3.2 - 2020-06-10

### Fixed
- Fixed a regression introduced in 5.3.0, where `images_dataimg_filter` was no-longer called. #TINY-6086

## 5.3.1 - 2020-05-27

### Fixed
- Fixed the image upload error alert also incorrectly closing the image dialog. #TINY-6020
- Fixed editor content scrolling incorrectly on focus in Firefox by reverting default content CSS html and body heights added in 5.3.0. #TINY-6019

## 5.3.0 - 2020-05-21

### Added
- Added html and body height styles to the default oxide content CSS. #TINY-5978
- Added `uploadUri` and `blobInfo` to the data returned by `editor.uploadImages()`. #TINY-4579
- Added a new function to the `BlobCache` API to lookup a blob based on the base64 data and mime type. #TINY-5988
- Added the ability to search and replace within a selection. #TINY-4549
- Added the ability to set the list start position for ordered lists and added new `lists` context menu item. #TINY-3915
- Added `icon` as an optional config option to the toggle menu item API. #TINY-3345
- Added `auto` mode for `toolbar_location` which positions the toolbar and menu bar at the bottom if there is no space at the top. #TINY-3161

### Changed
- Changed the default `toolbar_location` to `auto`. #TINY-3161
- Changed toggle menu items and choice menu items to have a dedicated icon with the checkmark displayed on the far right side of the menu item. #TINY-3345
- Changed the `link`, `image`, and `paste` plugins to use Promises to reduce the bundle size. #TINY-4710
- Changed the default icons to be lazy loaded during initialization. #TINY-4729
- Changed the parsing of content so base64 encoded urls are converted to blob urls. #TINY-4727
- Changed context toolbars so they concatenate when more than one is suitable for the current selection. #TINY-4495
- Changed inline style element formats (strong, b, em, i, u, strike) to convert to a span on format removal if a `style` or `class` attribute is present. #TINY-4741

### Fixed
- Fixed the `selection.setContent()` API not running parser filters. #TINY-4002
- Fixed formats incorrectly applied or removed when table cells were selected. #TINY-4709
- Fixed the `quickimage` button not restricting the file types to images. #TINY-4715
- Fixed search and replace ignoring text in nested contenteditable elements. #TINY-5967
- Fixed resize handlers displaying in the wrong location sometimes for remote images. #TINY-4732
- Fixed table picker breaking in Firefox on low zoom levels. #TINY-4728
- Fixed issue with loading or pasting contents with large base64 encoded images on Safari. #TINY-4715
- Fixed supplementary special characters being truncated when inserted into the editor. Patch contributed by mlitwin. #TINY-4791
- Fixed toolbar buttons not set to disabled when the editor is in readonly mode. #TINY-4592
- Fixed the editor selection incorrectly changing when removing caret format containers. #TINY-3438
- Fixed bug where title, width, and height would be set to empty string values when updating an image and removing those attributes using the image dialog. #TINY-4786
- Fixed `ObjectResized` event firing when an object wasn't resized. #TINY-4161
- Fixed `ObjectResized` and `ObjectResizeStart` events incorrectly fired when adding or removing table rows and columns. #TINY-4829
- Fixed the placeholder not hiding when pasting content into the editor. #TINY-4828
- Fixed an issue where the editor would fail to load if local storage was disabled. #TINY-5935
- Fixed an issue where an uploaded image would reuse a cached image with a different mime type. #TINY-5988
- Fixed bug where toolbars and dialogs would not show if the body element was replaced (e.g. with Turbolinks). Patch contributed by spohlenz. #GH-5653
- Fixed an issue where multiple formats would be removed when removing a single format at the end of lines or on empty lines. #TINY-1170
- Fixed zero-width spaces incorrectly included in the `wordcount` plugin character count. #TINY-5991
- Fixed a regression introduced in 5.2.0 whereby the desktop `toolbar_mode` setting would incorrectly override the mobile default setting. #TINY-5998
- Fixed an issue where deleting all content in a single cell table would delete the entire table. #TINY-1044

## 5.2.2 - 2020-04-23

### Fixed
- Fixed an issue where anchors could not be inserted on empty lines. #TINY-2788
- Fixed text decorations (underline, strikethrough) not consistently inheriting the text color. #TINY-4757
- Fixed `format` menu alignment buttons inconsistently applying to images. #TINY-4057
- Fixed the floating toolbar drawer height collapsing when the editor is rendered in modal dialogs or floating containers. #TINY-4837
- Fixed `media` embed content not processing safely in some cases. #TINY-4857

## 5.2.1 - 2020-03-25

### Fixed
- Fixed the "is decorative" checkbox in the image dialog clearing after certain dialog events. #FOAM-11
- Fixed possible uncaught exception when a `style` attribute is removed using a content filter on `setContent`. #TINY-4742
- Fixed the table selection not functioning correctly in Microsoft Edge 44 or higher. #TINY-3862
- Fixed the table resize handles not functioning correctly in Microsoft Edge 44 or higher. #TINY-4160
- Fixed the floating toolbar drawer disconnecting from the toolbar when adding content in inline mode. #TINY-4725 #TINY-4765
- Fixed `readonly` mode not returning the appropriate boolean value. #TINY-3948
- Fixed the `forced_root_block_attrs` setting not applying attributes to new blocks consistently. #TINY-4564
- Fixed the editor incorrectly stealing focus during initialization in Microsoft Internet Explorer. #TINY-4697
- Fixed dialogs stealing focus when opening an alert or confirm dialog using an `onAction` callback. #TINY-4014
- Fixed inline dialogs incorrectly closing when clicking on an opened alert or confirm dialog. #TINY-4012
- Fixed the context toolbar overlapping the menu bar and toolbar. #TINY-4586
- Fixed notification and inline dialog positioning issues when using `toolbar_location: 'bottom'`. #TINY-4586
- Fixed the `colorinput` popup appearing offscreen on mobile devices. #TINY-4711
- Fixed special characters not being found when searching by "whole words only". #TINY-4522
- Fixed an issue where dragging images could cause them to be duplicated. #TINY-4195
- Fixed context toolbars activating without the editor having focus. #TINY-4754
- Fixed an issue where removing the background color of text did not always work. #TINY-4770
- Fixed an issue where new rows and columns in a table did not retain the style of the previous row or column. #TINY-4788

## 5.2.0 - 2020-02-13

### Added
- Added the ability to apply formats to spaces. #TINY-4200
- Added new `toolbar_location` setting to allow for positioning the menu and toolbar at the bottom of the editor. #TINY-4210
- Added new `toolbar_groups` setting to allow a custom floating toolbar group to be added to the toolbar when using `floating` toolbar mode. #TINY-4229
- Added new `link_default_protocol` setting to `link` and `autolink` plugin to allow a protocol to be used by default. #TINY-3328
- Added new `placeholder` setting to allow a placeholder to be shown when the editor is empty. #TINY-3917
- Added new `tinymce.dom.TextSeeker` API to allow searching text across different DOM nodes. #TINY-4200
- Added a drop shadow below the toolbar while in sticky mode and introduced Oxide variables to customize it when creating a custom skin. #TINY-4343
- Added `quickbars_image_toolbar` setting to allow for the image quickbar to be turned off. #TINY-4398
- Added iframe and img `loading` attribute to the default schema. Patch contributed by ataylor32. #GH-5112
- Added new `getNodeFilters`/`getAttributeFilters` functions to the `editor.serializer` instance. #TINY-4344
- Added new `a11y_advanced_options` setting to allow additional accessibility options to be added. #FOAM-11
- Added new accessibility options and behaviours to the image dialog using `a11y_advanced_options`. #FOAM-11
- Added the ability to use the window `PrismJS` instance for the `codesample` plugin instead of the bundled version to allow for styling custom languages. #TINY-4504
- Added error message events that fire when a resource loading error occurs. #TINY-4509

### Changed
- Changed the default schema to disallow `onchange` for select elements. #TINY-4614
- Changed default `toolbar_mode` value from false to `wrap`. The value false has been deprecated. #TINY-4617
- Changed `toolbar_drawer` setting to `toolbar_mode`. `toolbar_drawer` has been deprecated. #TINY-4416
- Changed iframe mode to set selection on content init if selection doesn't exist. #TINY-4139
- Changed table related icons to align them with the visual style of the other icons. #TINY-4341
- Changed and improved the visual appearance of the color input field. #TINY-2917
- Changed fake caret container to use `forced_root_block` when possible. #TINY-4190
- Changed the `requireLangPack` API to wait until the plugin has been loaded before loading the language pack. #TINY-3716
- Changed the formatter so `style_formats` are registered before the initial content is loaded into the editor. #TINY-4238
- Changed media plugin to use https protocol for media urls by default. #TINY-4577
- Changed the parser to treat CDATA nodes as bogus HTML comments to match the HTML parsing spec. A new `preserve_cdata` setting has been added to preserve CDATA nodes if required. #TINY-4625

### Fixed
- Fixed incorrect parsing of malformed/bogus HTML comments. #TINY-4625
- Fixed `quickbars` selection toolbar appearing on non-editable elements. #TINY-4359
- Fixed bug with alignment toolbar buttons sometimes not changing state correctly. #TINY-4139
- Fixed the `codesample` toolbar button not toggling when selecting code samples other than HTML. #TINY-4504
- Fixed content incorrectly scrolling to the top or bottom when pressing enter if when the content was already in view. #TINY-4162
- Fixed `scrollIntoView` potentially hiding elements behind the toolbar. #TINY-4162
- Fixed editor not respecting the `resize_img_proportional` setting due to legacy code. #TINY-4236
- Fixed flickering floating toolbar drawer in inline mode. #TINY-4210
- Fixed an issue where the template plugin dialog would be indefinitely blocked on a failed template load. #TINY-2766
- Fixed the `mscontrolselect` event not being unbound on IE/Edge. #TINY-4196
- Fixed Confirm dialog footer buttons so only the "Yes" button is highlighted. #TINY-4310
- Fixed `file_picker_callback` functionality for Image, Link and Media plugins. #TINY-4163
- Fixed issue where floating toolbar drawer sometimes would break if the editor is resized while the drawer is open. #TINY-4439
- Fixed incorrect `external_plugins` loading error message. #TINY-4503
- Fixed resize handler was not hidden for ARIA purposes. Patch contributed by Parent5446. #GH-5195
- Fixed an issue where content could be lost if a misspelled word was selected and spellchecking was disabled. #TINY-3899
- Fixed validation errors in the CSS where certain properties had the wrong default value. #TINY-4491
- Fixed an issue where forced root block attributes were not applied when removing a list. #TINY-4272
- Fixed an issue where the element path isn't being cleared when there are no parents. #TINY-4412
- Fixed an issue where width and height in svg icons containing `rect` elements were overridden by the CSS reset. #TINY-4408
- Fixed an issue where uploading images with `images_reuse_filename` enabled and that included a query parameter would generate an invalid URL. #TINY-4638
- Fixed the `closeButton` property not working when opening notifications. #TINY-4674
- Fixed keyboard flicker when opening a context menu on mobile. #TINY-4540
- Fixed issue where plus icon svg contained strokes. #TINY-4681

## 5.1.6 - 2020-01-28

### Fixed
- Fixed `readonly` mode not blocking all clicked links. #TINY-4572
- Fixed legacy font sizes being calculated inconsistently for the `FontSize` query command value. #TINY-4555
- Fixed changing a tables row from `Header` to `Body` incorrectly moving the row to the bottom of the table. #TINY-4593
- Fixed the context menu not showing in certain cases with hybrid devices. #TINY-4569
- Fixed the context menu opening in the wrong location when the target is the editor body. #TINY-4568
- Fixed the `image` plugin not respecting the `automatic_uploads` setting when uploading local images. #TINY-4287
- Fixed security issue related to parsing HTML comments and CDATA. #TINY-4544

## 5.1.5 - 2019-12-19

### Fixed
- Fixed the UI not working with hybrid devices that accept both touch and mouse events. #TNY-4521
- Fixed the `charmap` dialog initially focusing the first tab of the dialog instead of the search input field. #TINY-4342
- Fixed an exception being raised when inserting content if the caret was directly before or after a `contenteditable="false"` element. #TINY-4528
- Fixed a bug with pasting image URLs when paste as text is enabled. #TINY-4523

## 5.1.4 - 2019-12-11

### Fixed
- Fixed dialog contents disappearing when clicking a checkbox for right-to-left languages. #TINY-4518
- Fixed the `legacyoutput` plugin registering legacy formats after editor initialization, causing legacy content to be stripped on the initial load. #TINY-4447
- Fixed search and replace not cycling through results when searching using special characters. #TINY-4506
- Fixed the `visualchars` plugin converting HTML-like text to DOM elements in certain cases. #TINY-4507
- Fixed an issue with the `paste` plugin not sanitizing content in some cases. #TINY-4510
- Fixed HTML comments incorrectly being parsed in certain cases. #TINY-4511

## 5.1.3 - 2019-12-04

### Fixed
- Fixed sticky toolbar not undocking when fullscreen mode is activated. #TINY-4390
- Fixed the "Current Window" target not applying when updating links using the link dialog. #TINY-4063
- Fixed disabled menu items not highlighting when focused. #TINY-4339
- Fixed touch events passing through dialog collection items to the content underneath on Android devices. #TINY-4431
- Fixed keyboard navigation of the Help dialog's Keyboard Navigation tab. #TINY-4391
- Fixed search and replace dialog disappearing when finding offscreen matches on iOS devices. #TINY-4350
- Fixed performance issues where sticky toolbar was jumping while scrolling on slower browsers. #TINY-4475

## 5.1.2 - 2019-11-19

### Fixed
- Fixed desktop touch devices using `mobile` configuration overrides. #TINY-4345
- Fixed unable to disable the new scrolling toolbar feature. #TINY-4345
- Fixed touch events passing through any pop-up items to the content underneath on Android devices. #TINY-4367
- Fixed the table selector handles throwing JavaScript exceptions for non-table selections. #TINY-4338
- Fixed `cut` operations not removing selected content on Android devices when the `paste` plugin is enabled. #TINY-4362
- Fixed inline toolbar not constrained to the window width by default. #TINY-4314
- Fixed context toolbar split button chevrons pointing right when they should be pointing down. #TINY-4257
- Fixed unable to access the dialog footer in tabbed dialogs on small screens. #TINY-4360
- Fixed mobile table selectors were hard to select with touch by increasing the size. #TINY-4366
- Fixed mobile table selectors moving when moving outside the editor. #TINY-4366
- Fixed inline toolbars collapsing when using sliding toolbars. #TINY-4389
- Fixed block textpatterns not treating NBSPs as spaces. #TINY-4378
- Fixed backspace not merging blocks when the last element in the preceding block was a `contenteditable="false"` element. #TINY-4235
- Fixed toolbar buttons that only contain text labels overlapping on mobile devices. #TINY-4395
- Fixed quickbars quickimage picker not working on mobile. #TINY-4377
- Fixed fullscreen not resizing in an iOS WKWebView component. #TINY-4413

## 5.1.1 - 2019-10-28

### Fixed
- Fixed font formats containing spaces being wrapped in `&quot;` entities instead of single quotes. #TINY-4275
- Fixed alert and confirm dialogs losing focus when clicked. #TINY-4248
- Fixed clicking outside a modal dialog focusing on the document body. #TINY-4249
- Fixed the context toolbar not hiding when scrolled out of view. #TINY-4265

## 5.1.0 - 2019-10-17

### Added
- Added touch selector handles for table selections on touch devices. #TINY-4097
- Added border width field to Table Cell dialog. #TINY-4028
- Added touch event listener to media plugin to make embeds playable. #TINY-4093
- Added oxide styling options to notifications and tweaked the default variables. #TINY-4153
- Added additional padding to split button chevrons on touch devices, to make them easier to interact with. #TINY-4223
- Added new platform detection functions to `Env` and deprecated older detection properties. #TINY-4184
- Added `inputMode` config field to specify inputmode attribute of `input` dialog components. #TINY-4062
- Added new `inputMode` property to relevant plugins/dialogs. #TINY-4102
- Added new `toolbar_sticky` setting to allow the iframe menubar/toolbar to stick to the top of the window when scrolling. #TINY-3982

### Changed
- Changed default setting for `toolbar_drawer` to `floating`. #TINY-3634
- Changed mobile phones to use the `silver` theme by default. #TINY-3634
- Changed some editor settings to default to `false` on touch devices:
  - `menubar`(phones only). #TINY-4077
  - `table_grid`. #TINY-4075
  - `resize`. #TINY-4157
  - `object_resizing`. #TINY-4157
- Changed toolbars and context toolbars to sidescroll on mobile. #TINY-3894 #TINY-4107
- Changed context menus to render as horizontal menus on touch devices. #TINY-4107
- Changed the editor to use the `VisualViewport` API of the browser where possible. #TINY-4078
- Changed visualblocks toolbar button icon and renamed `paragraph` icon to `visualchars`. #TINY-4074
- Changed Oxide default for `@toolbar-button-chevron-color` to follow toolbar button icon color. #TINY-4153
- Changed the `urlinput` dialog component to use the `url` type attribute. #TINY-4102

### Fixed
- Fixed Safari desktop visual viewport fires resize on fullscreen breaking the restore function. #TINY-3976
- Fixed scroll issues on mobile devices. #TINY-3976
- Fixed context toolbar unable to refresh position on iOS12. #TINY-4107
- Fixed ctrl+left click not opening links on readonly mode and the preview dialog. #TINY-4138
- Fixed Slider UI component not firing `onChange` event on touch devices. #TINY-4092
- Fixed notifications overlapping instead of stacking. #TINY-3478
- Fixed inline dialogs positioning incorrectly when the page is scrolled. #TINY-4018
- Fixed inline dialogs and menus not repositioning when resizing. #TINY-3227
- Fixed inline toolbar incorrectly stretching to the full width when a width value was provided. #TINY-4066
- Fixed menu chevrons color to follow the menu text color. #TINY-4153
- Fixed table menu selection grid from staying black when using dark skins, now follows border color. #TINY-4153
- Fixed Oxide using the wrong text color variable for menubar button focused state. #TINY-4146
- Fixed the autoresize plugin not keeping the selection in view when resizing. #TINY-4094
- Fixed textpattern plugin throwing exceptions when using `forced_root_block: false`. #TINY-4172
- Fixed missing CSS fill styles for toolbar button icon active state. #TINY-4147
- Fixed an issue where the editor selection could end up inside a short ended element (such as `br`). #TINY-3999
- Fixed browser selection being lost in inline mode when opening split dropdowns. #TINY-4197
- Fixed backspace throwing an exception when using `forced_root_block: false`. #TINY-4099
- Fixed floating toolbar drawer expanding outside the bounds of the editor. #TINY-3941
- Fixed the autocompleter not activating immediately after a `br` or `contenteditable=false` element. #TINY-4194
- Fixed an issue where the autocompleter would incorrectly close on IE 11 in certain edge cases. #TINY-4205

## 5.0.16 - 2019-09-24

### Added
- Added new `referrer_policy` setting to add the `referrerpolicy` attribute when loading scripts or stylesheets. #TINY-3978
- Added a slight background color to dialog tab links when focused to aid keyboard navigation. #TINY-3877

### Fixed
- Fixed media poster value not updating on change. #TINY-4013
- Fixed openlink was not registered as a toolbar button. #TINY-4024
- Fixed failing to initialize if a script tag was used inside a SVG. #TINY-4087
- Fixed double top border showing on toolbar without menubar when toolbar_drawer is enabled. #TINY-4118
- Fixed unable to drag inline dialogs to the bottom of the screen when scrolled. #TINY-4154
- Fixed notifications appearing on top of the toolbar when scrolled in inline mode. #TINY-4159
- Fixed notifications displaying incorrectly on IE 11. #TINY-4169

## 5.0.15 - 2019-09-02

### Added
- Added a dark `content_css` skin to go with the dark UI skin. #TINY-3743

### Changed
- Changed the enabled state on toolbar buttons so they don't get the hover effect. #TINY-3974

### Fixed
- Fixed missing CSS active state on toolbar buttons. #TINY-3966
- Fixed `onChange` callback not firing for the colorinput dialog component. #TINY-3968
- Fixed context toolbars not showing in fullscreen mode. #TINY-4023

## 5.0.14 - 2019-08-19

### Added
- Added an API to reload the autocompleter menu with additional fetch metadata #MENTIONS-17

### Fixed
- Fixed missing toolbar button border styling options. #TINY-3965
- Fixed image upload progress notification closing before the upload is complete. #TINY-3963
- Fixed inline dialogs not closing on escape when no dialog component is in focus. #TINY-3936
- Fixed plugins not being filtered when defaulting to mobile on phones. #TINY-3537
- Fixed toolbar more drawer showing the content behind it when transitioning between opened and closed states. #TINY-3878
- Fixed focus not returning to the dialog after pressing the "Replace all" button in the search and replace dialog. #TINY-3961

### Removed
- Removed Oxide variable `@menubar-select-disabled-border-color` and replaced it with `@menubar-select-disabled-border`. #TINY-3965

## 5.0.13 - 2019-08-06

### Changed
- Changed modal dialogs to prevent dragging by default and added new `draggable_modal` setting to restore dragging. #TINY-3873
- Changed the nonbreaking plugin to insert nbsp characters wrapped in spans to aid in filtering. This can be disabled using the `nonbreaking_wrap` setting. #TINY-3647
- Changed backspace behaviour in lists to outdent nested list items when the cursor is at the start of the list item. #TINY-3651

### Fixed
- Fixed sidebar growing beyond editor bounds in IE 11. #TINY-3937
- Fixed issue with being unable to keyboard navigate disabled toolbar buttons. #TINY-3350
- Fixed issues with backspace and delete in nested contenteditable true and false elements. #TINY-3868
- Fixed issue with losing keyboard navigation in dialogs due to disabled buttons. #TINY-3914
- Fixed `MouseEvent.mozPressure is deprecated` warning in Firefox. #TINY-3919
- Fixed `default_link_target` not being respected when `target_list` is disabled. #TINY-3757
- Fixed mobile plugin filter to only apply to the mobile theme, rather than all mobile platforms. #TINY-3405
- Fixed focus switching to another editor during mode changes. #TINY-3852
- Fixed an exception being thrown when clicking on an uninitialized inline editor. #TINY-3925
- Fixed unable to keyboard navigate to dialog menu buttons. #TINY-3933
- Fixed dialogs being able to be dragged outside the window viewport. #TINY-3787
- Fixed inline dialogs appearing above modal dialogs. #TINY-3932

## 5.0.12 - 2019-07-18

### Added
- Added ability to utilize UI dialog panels inside other panels. #TINY-3305
- Added help dialog tab explaining keyboard navigation of the editor. #TINY-3603

### Changed
- Changed the "Find and Replace" design to an inline dialog. #TINY-3054

### Fixed
- Fixed issue where autolink spacebar event was not being fired on Edge. #TINY-3891
- Fixed table selection missing the background color. #TINY-3892
- Fixed removing shortcuts not working for function keys. #TINY-3871
- Fixed non-descriptive UI component type names. #TINY-3349
- Fixed UI registry components rendering as the wrong type when manually specifying a different type. #TINY-3385
- Fixed an issue where dialog checkbox, input, selectbox, textarea and urlinput components couldn't be disabled. #TINY-3708
- Fixed the context toolbar not using viable screen space in inline/distraction free mode. #TINY-3717
- Fixed the context toolbar overlapping the toolbar in various conditions. #TINY-3205
- Fixed IE11 edge case where items were being inserted into the wrong location. #TINY-3884

## 5.0.11 - 2019-07-04

### Fixed
- Fixed packaging errors caused by a rollup treeshaking bug (https://github.com/rollup/rollup/issues/2970). #TINY-3866
- Fixed the customeditor component not able to get data from the dialog api. #TINY-3866
- Fixed collection component tooltips not being translated. #TINY-3855

## 5.0.10 - 2019-07-02

### Added
- Added support for all HTML color formats in `color_map` setting. #TINY-3837

### Changed
- Changed backspace key handling to outdent content in appropriate circumstances. #TINY-3685
- Changed default palette for forecolor and backcolor to include some lighter colors suitable for highlights. #TINY-2865
- Changed the search and replace plugin to cycle through results. #TINY-3800

### Fixed
- Fixed inconsistent types causing some properties to be unable to be used in dialog components. #TINY-3778
- Fixed an issue in the Oxide skin where dialog content like outlines and shadows were clipped because of overflow hidden. #TINY-3566
- Fixed the search and replace plugin not resetting state when changing the search query. #TINY-3800
- Fixed backspace in lists not creating an undo level. #TINY-3814
- Fixed the editor to cancel loading in quirks mode where the UI is not supported. #TINY-3391
- Fixed applying fonts not working when the name contained spaces and numbers. #TINY-3801
- Fixed so that initial content is retained when initializing on list items. #TINY-3796
- Fixed inefficient font name and font size current value lookup during rendering. #TINY-3813
- Fixed mobile font copied into the wrong folder for the oxide-dark skin. #TINY-3816
- Fixed an issue where resizing the width of tables would produce inaccurate results. #TINY-3827
- Fixed a memory leak in the Silver theme. #TINY-3797
- Fixed alert and confirm dialogs using incorrect markup causing inconsistent padding. #TINY-3835
- Fixed an issue in the Table plugin with `table_responsive_width` not enforcing units when resizing. #TINY-3790
- Fixed leading, trailing and sequential spaces being lost when pasting plain text. #TINY-3726
- Fixed exception being thrown when creating relative URIs. #TINY-3851
- Fixed focus is no longer set to the editor content during mode changes unless the editor already had focus. #TINY-3852

## 5.0.9 - 2019-06-26

### Fixed
- Fixed print plugin not working in Firefox. #TINY-3834

## 5.0.8 - 2019-06-18

### Added
- Added back support for multiple toolbars. #TINY-2195
- Added support for .m4a files to the media plugin. #TINY-3750
- Added new base_url and suffix editor init options. #TINY-3681

### Fixed
- Fixed incorrect padding for select boxes with visible values. #TINY-3780
- Fixed selection incorrectly changing when programmatically setting selection on contenteditable false elements. #TINY-3766
- Fixed sidebar background being transparent. #TINY-3727
- Fixed the build to remove duplicate iife wrappers. #TINY-3689
- Fixed bogus autocompleter span appearing in content when the autocompleter menu is shown. #TINY-3752
- Fixed toolbar font size select not working with legacyoutput plugin. #TINY-2921
- Fixed the legacyoutput plugin incorrectly aligning images. #TINY-3660
- Fixed remove color not working when using the legacyoutput plugin. #TINY-3756
- Fixed the font size menu applying incorrect sizes when using the legacyoutput plugin. #TINY-3773
- Fixed scrollIntoView not working when the parent window was out of view. #TINY-3663
- Fixed the print plugin printing from the wrong window in IE11. #TINY-3762
- Fixed content CSS loaded over CORS not loading in the preview plugin with content_css_cors enabled. #TINY-3769
- Fixed the link plugin missing the default "None" option for link list. #TINY-3738
- Fixed small dot visible with menubar and toolbar disabled in inline mode. #TINY-3623
- Fixed space key properly inserts a nbsp before/after block elements. #TINY-3745
- Fixed native context menu not showing with images in IE11. #TINY-3392
- Fixed inconsistent browser context menu image selection. #TINY-3789

## 5.0.7 - 2019-06-05

### Added
- Added new toolbar button and menu item for inserting tables via dialog. #TINY-3636
- Added new API for adding/removing/changing tabs in the Help dialog. #TINY-3535
- Added highlighting of matched text in autocompleter items. #TINY-3687
- Added the ability for autocompleters to work with matches that include spaces. #TINY-3704
- Added new `imagetools_fetch_image` callback to allow custom implementations for cors loading of images. #TINY-3658
- Added `'http'` and `https` options to `link_assume_external_targets` to prepend `http://` or `https://` prefixes when URL does not contain a protocol prefix. Patch contributed by francoisfreitag. #GH-4335

### Changed
- Changed annotations navigation to work the same as inline boundaries. #TINY-3396
- Changed tabpanel API by adding a `name` field and changing relevant methods to use it. #TINY-3535

### Fixed
- Fixed text color not updating all color buttons when choosing a color. #TINY-3602
- Fixed the autocompleter not working with fragmented text. #TINY-3459
- Fixed the autosave plugin no longer overwrites window.onbeforeunload. #TINY-3688
- Fixed infinite loop in the paste plugin when IE11 takes a long time to process paste events. Patch contributed by lRawd. #GH-4987
- Fixed image handle locations when using `fixed_toolbar_container`. Patch contributed by t00. #GH-4966
- Fixed the autoresize plugin not firing `ResizeEditor` events. #TINY-3587
- Fixed editor in fullscreen mode not extending to the bottom of the screen. #TINY-3701
- Fixed list removal when pressing backspace after the start of the list item. #TINY-3697
- Fixed autocomplete not triggering from compositionend events. #TINY-3711
- Fixed `file_picker_callback` could not set the caption field on the insert image dialog. #TINY-3172
- Fixed the autocompleter menu showing up after a selection had been made. #TINY-3718
- Fixed an exception being thrown when a file or number input has focus during initialization. Patch contributed by t00. #GH-2194

## 5.0.6 - 2019-05-22

### Added
- Added `icons_url` editor settings to enable icon packs to be loaded from a custom url. #TINY-3585
- Added `image_uploadtab` editor setting to control the visibility of the upload tab in the image dialog. #TINY-3606
- Added new api endpoints to the wordcount plugin and improved character count logic. #TINY-3578

### Changed
- Changed plugin, language and icon loading errors to log in the console instead of a notification. #TINY-3585

### Fixed
- Fixed the textpattern plugin not working with fragmented text. #TINY-3089
- Fixed various toolbar drawer accessibility issues and added an animation. #TINY-3554
- Fixed issues with selection and ui components when toggling readonly mode. #TINY-3592
- Fixed so readonly mode works with inline editors. #TINY-3592
- Fixed docked inline toolbar positioning when scrolled. #TINY-3621
- Fixed initial value not being set on bespoke select in quickbars and toolbar drawer. #TINY-3591
- Fixed so that nbsp entities aren't trimmed in white-space: pre-line elements. #TINY-3642
- Fixed `mceInsertLink` command inserting spaces instead of url encoded characters. #GH-4990
- Fixed text content floating on top of dialogs in IE11. #TINY-3640

## 5.0.5 - 2019-05-09

### Added
- Added menu items to match the forecolor/backcolor toolbar buttons. #TINY-2878
- Added default directionality based on the configured language. #TINY-2621
- Added styles, icons and tests for rtl mode. #TINY-2621

### Fixed
- Fixed autoresize not working with floating elements or when media elements finished loading. #TINY-3545
- Fixed incorrect vertical caret positioning in IE 11. #TINY-3188
- Fixed submenu anchoring hiding overflowed content. #TINY-3564

### Removed
- Removed unused and hidden validation icons to avoid displaying phantom tooltips. #TINY-2329

## 5.0.4 - 2019-04-23

### Added
- Added back URL dialog functionality, which is now available via `editor.windowManager.openUrl()`. #TINY-3382
- Added the missing throbber functionality when calling `editor.setProgressState(true)`. #TINY-3453
- Added function to reset the editor content and undo/dirty state via `editor.resetContent()`. #TINY-3435
- Added the ability to set menu buttons as active. #TINY-3274
- Added `editor.mode` API, featuring a custom editor mode API. #TINY-3406
- Added better styling to floating toolbar drawer. #TINY-3479
- Added the new premium plugins to the Help dialog plugins tab. #TINY-3496
- Added the linkchecker context menu items to the default configuration. #TINY-3543

### Fixed
- Fixed image context menu items showing on placeholder images. #TINY-3280
- Fixed dialog labels and text color contrast within notifications/alert banners to satisfy WCAG 4.5:1 contrast ratio for accessibility. #TINY-3351
- Fixed selectbox and colorpicker items not being translated. #TINY-3546
- Fixed toolbar drawer sliding mode to correctly focus the editor when tabbing via keyboard navigation. #TINY-3533
- Fixed positioning of the styleselect menu in iOS while using the mobile theme. #TINY-3505
- Fixed the menubutton `onSetup` callback to be correctly executed when rendering the menu buttons. #TINY-3547
- Fixed `default_link_target` setting to be correctly utilized when creating a link. #TINY-3508
- Fixed colorpicker floating marginally outside its container. #TINY-3026
- Fixed disabled menu items displaying as active when hovered. #TINY-3027

### Removed
- Removed redundant mobile wrapper. #TINY-3480

## 5.0.3 - 2019-03-19

### Changed
- Changed empty nested-menu items within the style formats menu to be disabled or hidden if the value of `style_formats_autohide` is `true`. #TINY-3310
- Changed the entire phrase 'Powered by Tiny' in the status bar to be a link instead of just the word 'Tiny'. #TINY-3366
- Changed `formatselect`, `styleselect` and `align` menus to use the `mceToggleFormat` command internally. #TINY-3428

### Fixed
- Fixed toolbar keyboard navigation to work as expected when `toolbar_drawer` is configured. #TINY-3432
- Fixed text direction buttons to display the correct pressed state in selections that have no explicit `dir` property. #TINY-3138
- Fixed the mobile editor to clean up properly when removed. #TINY-3445
- Fixed quickbar toolbars to add an empty box to the screen when it is set to `false`. #TINY-3439
- Fixed an issue where pressing the **Delete/Backspace** key at the edge of tables was creating incorrect selections. #TINY-3371
- Fixed an issue where dialog collection items (emoticon and special character dialogs) couldn't be selected with touch devices. #TINY-3444
- Fixed a type error introduced in TinyMCE version 5.0.2 when calling `editor.getContent()` with nested bookmarks. #TINY-3400
- Fixed an issue that prevented default icons from being overridden. #TINY-3449
- Fixed an issue where **Home/End** keys wouldn't move the caret correctly before or after `contenteditable=false` inline elements. #TINY-2995
- Fixed styles to be preserved in IE 11 when editing via the `fullpage` plugin. #TINY-3464
- Fixed the `link` plugin context toolbar missing the open link button. #TINY-3461
- Fixed inconsistent dialog component spacing. #TINY-3436

## 5.0.2 - 2019-03-05

### Added
- Added presentation and document presets to `htmlpanel` dialog component. #TINY-2694
- Added missing fixed_toolbar_container setting has been reimplemented in the Silver theme. #TINY-2712
- Added a new toolbar setting `toolbar_drawer` that moves toolbar groups which overflow the editor width into either a `sliding` or `floating` toolbar section. #TINY-2874

### Changed
- Updated the build process to include package lock files in the dev distribution archive. #TINY-2870

### Fixed
- Fixed inline dialogs did not have aria attributes. #TINY-2694
- Fixed default icons are now available in the UI registry, allowing use outside of toolbar buttons. #TINY-3307
- Fixed a memory leak related to select toolbar items. #TINY-2874
- Fixed a memory leak due to format changed listeners that were never unbound. #TINY-3191
- Fixed an issue where content may have been lost when using permanent bookmarks. #TINY-3400
- Fixed the quicklink toolbar button not rendering in the quickbars plugin. #TINY-3125
- Fixed an issue where menus were generating invalid HTML in some cases. #TINY-3323
- Fixed an issue that could cause the mobile theme to show a blank white screen when the editor was inside an `overflow:hidden` element. #TINY-3407
- Fixed mobile theme using a transparent background and not taking up the full width on iOS. #TINY-3414
- Fixed the template plugin dialog missing the description field. #TINY-3337
- Fixed input dialog components using an invalid default type attribute. #TINY-3424
- Fixed an issue where backspace/delete keys after/before pagebreak elements wouldn't move the caret. #TINY-3097
- Fixed an issue in the table plugin where menu items and toolbar buttons weren't showing correctly based on the selection. #TINY-3423
- Fixed inconsistent button focus styles in Firefox. #TINY-3377
- Fixed the resize icon floating left when all status bar elements were disabled. #TINY-3340
- Fixed the resize handle to not show in fullscreen mode. #TINY-3404

## 5.0.1 - 2019-02-21

### Added
- Added H1-H6 toggle button registration to the silver theme. #TINY-3070
- Added code sample toolbar button will now toggle on when the cursor is in a code section. #TINY-3040
- Added new settings to the emoticons plugin to allow additional emoticons to be added. #TINY-3088

### Fixed
- Fixed an issue where adding links to images would replace the image with text. #TINY-3356
- Fixed an issue where the inline editor could use fractional pixels for positioning. #TINY-3202
- Fixed an issue where uploading non-image files in the Image Plugin upload tab threw an error. #TINY-3244
- Fixed an issue in the media plugin that was causing the source url and height/width to be lost in certain circumstances. #TINY-2858
- Fixed an issue with the Context Toolbar not being removed when clicking outside of the editor. #TINY-2804
- Fixed an issue where clicking 'Remove link' wouldn't remove the link in certain circumstances. #TINY-3199
- Fixed an issue where the media plugin would fail when parsing dialog data. #TINY-3218
- Fixed an issue where retrieving the selected content as text didn't create newlines. #TINY-3197
- Fixed incorrect keyboard shortcuts in the Help dialog for Windows. #TINY-3292
- Fixed an issue where JSON serialization could produce invalid JSON. #TINY-3281
- Fixed production CSS including references to source maps. #TINY-3920
- Fixed development CSS was not included in the development zip. #TINY-3920
- Fixed the autocompleter matches predicate not matching on the start of words by default. #TINY-3306
- Fixed an issue where the page could be scrolled with modal dialogs open. #TINY-2252
- Fixed an issue where autocomplete menus would show an icon margin when no items had icons. #TINY-3329
- Fixed an issue in the quickbars plugin where images incorrectly showed the text selection toolbar. #TINY-3338
- Fixed an issue that caused the inline editor to fail to render when the target element already had focus. #TINY-3353

### Removed
- Removed paste as text notification banner and paste_plaintext_inform setting. #POW-102

## 5.0.0 - 2019-02-04

Full documentation for the version 5 features and changes is available at https://www.tiny.cloud/docs/tinymce/5/release-notes/release-notes50/

### Added
- Added links and registered names with * to denote premium plugins in Plugins tab of Help dialog. #TINY-3223

### Changed
- Changed Tiny 5 mobile skin to look more uniform with desktop. #TINY-2650
- Blacklisted table, th and td as inline editor target. #TINY-717

### Fixed
- Fixed an issue where tab panel heights weren't sizing properly on smaller screens and weren't updating on resize. #TINY-3242
- Fixed image tools not having any padding between the label and slider. #TINY-3220
- Fixed context toolbar toggle buttons not showing the correct state. #TINY-3022
- Fixed missing separators in the spellchecker context menu between the suggestions and actions. #TINY-3217
- Fixed notification icon positioning in alert banners. #TINY-2196
- Fixed a typo in the word count plugin name. #TINY-3062
- Fixed charmap and emoticons dialogs not having a primary button. #TINY-3233
- Fixed an issue where resizing wouldn't work correctly depending on the box-sizing model. #TINY-3278

## 5.0.0-rc-2 - 2019-01-22

### Added
- Added screen reader accessibility for sidebar and statusbar. #TINY-2699

### Changed
- Changed formatting menus so they are registered and made the align toolbar button use an icon instead of text. #TINY-2880
- Changed checkboxes to use a boolean for its state, instead of a string. #TINY-2848
- Updated the textpattern plugin to properly support nested patterns and to allow running a command with a value for a pattern with a start and an end. #TINY-2991
- Updated Emoticons and Charmap dialogs to be screen reader accessible. #TINY-2693

### Fixed
- Fixed the link dialog such that it will now retain class attributes when updating links. #TINY-2825
- Fixed "Find and replace" not showing in the "Edit" menu by default. #TINY-3061
- Fixed dropdown buttons missing the 'type' attribute, which could cause forms to be incorrectly submitted. #TINY-2826
- Fixed emoticon and charmap search not returning expected results in certain cases. #TINY-3084
- Fixed blank rel_list values throwing an exception in the link plugin. #TINY-3149

### Removed
- Removed unnecessary 'flex' and unused 'colspan' properties from the new dialog APIs. #TINY-2973

## 5.0.0-rc-1 - 2019-01-08

### Added
- Added editor settings functionality to specify title attributes for toolbar groups. #TINY-2690
- Added icons instead of button text to improve Search and Replace dialog footer appearance. #TINY-2654
- Added `tox-dialog__table` instead of `mce-table-striped` class to enhance Help dialog appearance. #TINY-2360
- Added title attribute to iframes so, screen readers can announce iframe labels. #TINY-2692
- Added a wordcount menu item, that defaults to appearing in the tools menu. #TINY-2877

### Changed
- Updated the font select dropdown logic to try to detect the system font stack and show "System Font" as the font name. #TINY-2710
- Updated the autocompleter to only show when it has matched items. #TINY-2350
- Updated SizeInput labels to "Height" and "Width" instead of Dimensions. #TINY-2833
- Updated the build process to minify and generate ASCII only output for the emoticons database. #TINY-2744

### Fixed
- Fixed readonly mode not fully disabling editing content. #TINY-2287
- Fixed accessibility issues with the font select, font size, style select and format select toolbar dropdowns. #TINY-2713
- Fixed accessibility issues with split dropdowns. #TINY-2697
- Fixed the legacyoutput plugin to be compatible with TinyMCE 5.0. #TINY-2301
- Fixed icons not showing correctly in the autocompleter popup. #TINY-3029
- Fixed an issue where preview wouldn't show anything in Edge under certain circumstances. #TINY-3035
- Fixed the height being incorrectly calculated for the autoresize plugin. #TINY-2807

## 5.0.0-beta-1 - 2018-11-30

### Added
- Added a new `addNestedMenuItem()` UI registry function and changed all nested menu items to use the new registry functions. #TINY-2230
- Added title attribute to color swatch colors. #TINY-2669
- Added anchorbar component to anchor inline toolbar dialogs to instead of the toolbar. #TINY-2040
- Added support for toolbar<n> and toolbar array config options to be squashed into a single toolbar and not create multiple toolbars. #TINY-2195
- Added error handling for when forced_root_block config option is set to true. #TINY-2261
- Added functionality for the removed_menuitems config option. #TINY-2184
- Added the ability to use a string to reference menu items in menu buttons and submenu items. #TINY-2253

### Changed
- Changed the name of the "inlite" plugin to "quickbars". #TINY-2831
- Changed the background color icon to highlight background icon. #TINY-2258
- Changed Help dialog to be accessible to screen readers. #TINY-2687
- Changed the color swatch to save selected custom colors to local storage for use across sessions. #TINY-2722
- Changed `WindowManager` API - methods `getParams`, `setParams` and `getWindows`, and the legacy `windows` property, have been removed. `alert` and `confirm` dialogs are no longer tracked in the window list. #TINY-2603

### Fixed
- Fixed an inline mode issue where the save plugin upon saving can cause content loss. #TINY-2659
- Fixed an issue in IE 11 where calling selection.getContent() would return an empty string when the editor didn't have focus. #TINY-2325

### Removed
- Removed compat3x plugin. #TINY-2815

## 5.0.0-preview-4 - 2018-11-12

### Added
- Added width and height placeholder text to image and media dialog dimensions input. #AP-296
- Added the ability to keyboard navigate through menus, toolbars, sidebar and the status bar sequentially. #AP-381
- Added translation capability back to the editor's UI. #AP-282
- Added `label` component type for dialogs to group components under a label.

### Changed
- Changed the editor resize handle so that it should be disabled when the autoresize plugin is turned on. #AP-424
- Changed UI text for microcopy improvements. #TINY-2281

### Fixed
- Fixed distraction free plugin. #AP-470
- Fixed contents of the input field being selected on focus instead of just recieving an outline highlight. #AP-464
- Fixed styling issues with dialogs and menus in IE 11. #AP-456
- Fixed custom style format control not honoring custom formats. #AP-393
- Fixed context menu not appearing when clicking an image with a caption. #AP-382
- Fixed directionality of UI when using an RTL language. #AP-423
- Fixed page responsiveness with multiple inline editors. #AP-430
- Fixed empty toolbar groups appearing through invalid configuration of the `toolbar` property. #AP-450
- Fixed text not being retained when updating links through the link dialog. #AP-293
- Fixed edit image context menu, context toolbar and toolbar items being incorrectly enabled when selecting invalid images. #AP-323
- Fixed emoji type ahead being shown when typing URLs. #AP-366
- Fixed toolbar configuration properties incorrectly expecting string arrays instead of strings. #AP-342
- Fixed the block formatting toolbar item not showing a "Formatting" title when there is no selection. #AP-321
- Fixed clicking disabled toolbar buttons hiding the toolbar in inline mode. #AP-380
- Fixed `EditorResize` event not being fired upon editor resize. #AP-327
- Fixed tables losing styles when updating through the dialog. #AP-368
- Fixed context toolbar positioning to be more consistent near the edges of the editor. #AP-318
- Fixed table of contents plugin now works with v5 toolbar APIs correctly. #AP-347
- Fixed the `link_context_toolbar` configuration not disabling the context toolbar. #AP-458
- Fixed the link context toolbar showing incorrect relative links. #AP-435
- Fixed the alignment of the icon in alert banner dialog components. #TINY-2220
- Fixed the visual blocks and visual char menu options not displaying their toggled state. #TINY-2238
- Fixed the editor not displaying as fullscreen when toggled. #TINY-2237

### Removed
- Removed the tox-custom-editor class that was added to the wrapping element of codemirror. #TINY-2211

## 5.0.0-preview-3 - 2018-10-18

### Changed
- Changed editor layout to use modern CSS properties over manually calculating dimensions. #AP-324
- Changed `autoresize_min_height` and `autoresize_max_height` configurations to `min_height` and `max_height`. #AP-324
- Changed `Whole word` label in Search and Replace dialog to `Find whole words only`. #AP-387

### Fixed
- Fixed bugs with editor width jumping when resizing and the iframe not resizing to smaller than 150px in height. #AP-324
- Fixed mobile theme bug that prevented the editor from loading. #AP-404
- Fixed long toolbar groups extending outside of the editor instead of wrapping.
- Fixed dialog titles so they are now proper case. #AP-384
- Fixed color picker default to be #000000 instead of #ff00ff. #AP-216
- Fixed "match case" option on the Find and Replace dialog is no longer selected by default. #AP-298
- Fixed vertical alignment of toolbar icons. #DES-134
- Fixed toolbar icons not appearing on IE11. #DES-133

## 5.0.0-preview-2 - 2018-10-10

### Added
- Added swatch is now shown for colorinput fields, instead of the colorpicker directly. #AP-328
- Added fontformats and fontsizes menu items. #AP-390

### Changed
- Changed configuration of color options has been simplified to `color_map`, `color_cols`, and `custom_colors`. #AP-328
- Changed `height` configuration to apply to the editor frame (including menubar, toolbar, status bar) instead of the content area. #AP-324

### Fixed
- Fixed styleselect not updating the displayed item as the cursor moved. #AP-388
- Fixed preview iframe not expanding to the dialog size. #AP-252
- Fixed 'meta' shortcuts not translated into platform-specific text. #AP-270
- Fixed tabbed dialogs (Charmap and Emoticons) shrinking when no search results returned.
- Fixed a bug where alert banner icons were not retrieved from icon pack. #AP-330
- Fixed component styles to flex so they fill large dialogs. #AP-252
- Fixed editor flashing unstyled during load (still in progress). #AP-349

### Removed
- Removed `colorpicker` plugin, it is now in the theme. #AP-328
- Removed `textcolor` plugin, it is now in the theme. #AP-328

## 5.0.0-preview-1 - 2018-10-01

Developer preview 1.

Initial list of features and changes is available at https://www.tiny.cloud/docs/tinymce/5/release-notes/release-notes50/.

## 4.9.11 - 2020-07-13

### Fixed
- Fixed the `selection.setContent()` API not running parser filters. #TINY-4002
- Fixed content in an iframe element parsing as DOM elements instead of text content. #TINY-5943
- Fixed up and down keyboard navigation not working for inline `contenteditable="false"` elements. #TINY-6226

## 4.9.10 - 2020-04-23

### Fixed
- Fixed an issue where the editor selection could end up inside a short ended element (eg br). #TINY-3999
- Fixed a security issue related to CDATA sanitization during parsing. #TINY-4669
- Fixed `media` embed content not processing safely in some cases. #TINY-4857

## 4.9.9 - 2020-03-25

### Fixed
- Fixed the table selection not functioning correctly in Microsoft Edge 44 or higher. #TINY-3862
- Fixed the table resize handles not functioning correctly in Microsoft Edge 44 or higher. #TINY-4160
- Fixed the `forced_root_block_attrs` setting not applying attributes to new blocks consistently. #TINY-4564
- Fixed the editor failing to initialize if a script tag was used inside an SVG. #TINY-4087

## 4.9.8 - 2020-01-28

### Fixed
- Fixed the `mobile` theme failing to load due to a bundling issue. #TINY-4613
- Fixed security issue related to parsing HTML comments and CDATA. #TINY-4544

## 4.9.7 - 2019-12-19

### Fixed
- Fixed the `visualchars` plugin converting HTML-like text to DOM elements in certain cases. #TINY-4507
- Fixed an issue with the `paste` plugin not sanitizing content in some cases. #TINY-4510
- Fixed HTML comments incorrectly being parsed in certain cases. #TINY-4511

## 4.9.6 - 2019-09-02

### Fixed
- Fixed image browse button sometimes displaying the browse window twice. #TINY-3959

## 4.9.5 - 2019-07-02

### Changed
- Changed annotations navigation to work the same as inline boundaries. #TINY-3396

### Fixed
- Fixed the print plugin printing from the wrong window in IE11. #TINY-3762
- Fixed an exception being thrown when a file or number input has focus during initialization. Patch contributed by t00. #GH-2194
- Fixed positioning of the styleselect menu in iOS while using the mobile theme. #TINY-3505
- Fixed native context menu not showing with images in IE11. #TINY-3392
- Fixed selection incorrectly changing when programmatically setting selection on contenteditable false elements. #TINY-3766
- Fixed image browse button not working on touch devices. #TINY-3751
- Fixed so that nbsp entities aren't trimmed in white-space: pre-line elements. #TINY-3642
- Fixed space key properly inserts a nbsp before/after block elements. #TINY-3745
- Fixed infinite loop in the paste plugin when IE11 takes a long time to process paste events. Patch contributed by lRawd. #GH-4987

## 4.9.4 - 2019-03-20

### Fixed
- Fixed an issue where **Home/End** keys wouldn't move the caret correctly before or after `contenteditable=false` inline elements. #TINY-2995
- Fixed an issue where content may have been lost when using permanent bookmarks. #TINY-3400
- Fixed the mobile editor to clean up properly when removed. #TINY-3445
- Fixed an issue where retrieving the selected content as text didn't create newlines. #TINY-3197
- Fixed an issue where typing space between images would cause issues with nbsp not being inserted. #TINY-3346

## 4.9.3 - 2019-01-31

### Added
- Added a visualchars_default_state setting to the Visualchars Plugin. Patch contributed by mat3e.

### Fixed
- Fixed a bug where scrolling on a page with more than one editor would cause a ResizeWindow event to fire. #TINY-3247
- Fixed a bug where if a plugin threw an error during initialisation the whole editor would fail to load. #TINY-3243
- Fixed a bug where getContent would include bogus elements when valid_elements setting was set up in a specific way. #TINY-3213
- Fixed a bug where only a few function key names could be used when creating keyboard shortcuts. #TINY-3146
- Fixed a bug where it wasn't possible to enter spaces into an editor after pressing shift+enter. #TINY-3099
- Fixed a bug where no caret would be rendered after backspacing to a contenteditable false element. #TINY-2998
- Fixed a bug where deletion to/from indented lists would leave list fragments in the editor. #TINY-2981

## 4.9.2 - 2018-12-17

### Fixed
- Fixed a bug with pressing the space key on IE 11 would result in nbsp characters being inserted between words at the end of a block. #TINY-2996
- Fixed a bug where character composition using quote and space on US International keyboards would produce a space instead of a quote. #TINY-2999
- Fixed a bug where remove format wouldn't remove the inner most inline element in some situations. #TINY-2982
- Fixed a bug where outdenting an list item would affect attributes on other list items within the same list. #TINY-2971
- Fixed a bug where the DomParser filters wouldn't be applied for elements created when parsing invalid html. #TINY-2978
- Fixed a bug where setProgressState wouldn't automatically close floating ui elements like menus. #TINY-2896
- Fixed a bug where it wasn't possible to navigate out of a figcaption element using the arrow keys. #TINY-2894
- Fixed a bug where enter key before an image inside a link would remove the image. #TINY-2780

## 4.9.1 - 2018-12-04

### Added
- Added functionality to insert html to the replacement feature of the Textpattern Plugin. #TINY-2839

### Fixed
- Fixed a bug where `editor.selection.getContent({format: 'text'})` didn't work as expected in IE11 on an unfocused editor. #TINY-2862
- Fixed a bug in the Textpattern Plugin where the editor would get an incorrect selection after inserting a text pattern on Safari. #TINY-2838
- Fixed a bug where the space bar didn't work correctly in editors with the forced_root_block setting set to false. #TINY-2816

## 4.9.0 - 2018-11-27

### Added
- Added a replace feature to the Textpattern Plugin. #TINY-1908
- Added functionality to the Lists Plugin that improves the indentation logic. #TINY-1790

### Fixed
- Fixed a bug where it wasn't possible to delete/backspace when the caret was between a contentEditable=false element and a BR. #TINY-2372
- Fixed a bug where copying table cells without a text selection would fail to copy anything. #TINY-1789
- Implemented missing `autosave_restore_when_empty` functionality in the Autosave Plugin. Patch contributed by gzzo. #GH-4447
- Reduced insertion of unnecessary nonbreaking spaces in the editor. #TINY-1879

## 4.8.5 - 2018-10-30

### Added
- Added a content_css_cors setting to the editor that adds the crossorigin="anonymous" attribute to link tags added by the StyleSheetLoader. #TINY-1909

### Fixed
- Fixed a bug where trying to remove formatting with a collapsed selection range would throw an exception. #GH-4636
- Fixed a bug in the image plugin that caused updating figures to split contenteditable elements. #GH-4563
- Fixed a bug that was causing incorrect viewport calculations for fixed position UI elements. #TINY-1897
- Fixed a bug where inline formatting would cause the delete key to do nothing. #TINY-1900

## 4.8.4 - 2018-10-23

### Added
- Added support for the HTML5 `main` element. #TINY-1877

### Changed
- Changed the keyboard shortcut to move focus to contextual toolbars to Ctrl+F9. #TINY-1812

### Fixed
- Fixed a bug where content css could not be loaded from another domain. #TINY-1891
- Fixed a bug on FireFox where the cursor would get stuck between two contenteditable false inline elements located inside of the same block element divided by a BR. #TINY-1878
- Fixed a bug with the insertContent method where nonbreaking spaces would be inserted incorrectly. #TINY-1868
- Fixed a bug where the toolbar of the inline editor would not be visible in some scenarios. #TINY-1862
- Fixed a bug where removing the editor while more than one notification was open would throw an error. #TINY-1845
- Fixed a bug where the menubutton would be rendered on top of the menu if the viewport didn't have enough height. #TINY-1678
- Fixed a bug with the annotations api where annotating collapsed selections caused problems. #TBS-2449
- Fixed a bug where wbr elements were being transformed into whitespace when using the Paste Plugin's paste as text setting. #GH-4638
- Fixed a bug where the Search and Replace didn't replace spaces correctly. #GH-4632
- Fixed a bug with sublist items not persisting selection. #GH-4628
- Fixed a bug with mceInsertRawHTML command not working as expected. #GH-4625

## 4.8.3 - 2018-09-13

### Fixed
- Fixed a bug where the Wordcount Plugin didn't correctly count words within tables on IE11. #TINY-1770
- Fixed a bug where it wasn't possible to move the caret out of a table on IE11 and Firefox. #TINY-1682
- Fixed a bug where merging empty blocks didn't work as expected, sometimes causing content to be deleted. #TINY-1781
- Fixed a bug where the Textcolor Plugin didn't show the correct current color. #TINY-1810
- Fixed a bug where clear formatting with a collapsed selection would sometimes clear formatting from more content than expected. #TINY-1813 #TINY-1821
- Fixed a bug with the Table Plugin where it wasn't possible to keyboard navigate to the caption. #TINY-1818

## 4.8.2 - 2018-08-09

### Changed
- Moved annotator from "experimental" to "annotator" object on editor. #TBS-2398
- Improved the multiclick normalization across browsers. #TINY-1788

### Fixed
- Fixed a bug where running getSelectedBlocks with a collapsed selection between block elements would produce incorrect results. #TINY-1787
- Fixed a bug where the ScriptLoaders loadScript method would not work as expected in FireFox when loaded on the same page as a ShadowDOM polyfill. #TINY-1786
- Removed reference to ShadowDOM event.path as Blink based browsers now support event.composedPath. #TINY-1785
- Fixed a bug where a reference to localStorage would throw an "access denied" error in IE11 with strict security settings. #TINY-1782
- Fixed a bug where pasting using the toolbar button on an inline editor in IE11 would cause a looping behaviour. #TINY-1768

## 4.8.1 - 2018-07-26

### Fixed
- Fixed a bug where the content of inline editors was being cleaned on every call of `editor.save()`. #TINY-1783
- Fixed a bug where the arrow of the Inlite Theme toolbar was being rendered incorrectly in RTL mode. #TINY-1776
- Fixed a bug with the Paste Plugin where pasting after inline contenteditable false elements moved the caret to the end of the line. #TINY-1758

## 4.8.0 - 2018-06-27

### Added
- Added new "experimental" object in editor, with initial Annotator API. #TBS-2374

### Fixed
- Fixed a bug where deleting paragraphs inside of table cells would delete the whole table cell. #TINY-1759
- Fixed a bug in the Table Plugin where removing row height set on the row properties dialog did not update the table. #TINY-1730
- Fixed a bug with the font select toolbar item didn't update correctly. #TINY-1683
- Fixed a bug where all bogus elements would not be deleted when removing an inline editor. #TINY-1669

## 4.7.13 - 2018-05-16

### Added
- Added missing code menu item from the default menu config. #TINY-1648
- Added new align button for combining the separate align buttons into a menu button. #TINY-1652

### Fixed
- Fixed a bug where Edge 17 wouldn't be able to select images or tables. #TINY-1679
- Fixed issue where whitespace wasn't preserved when the editor was initialized on pre elements. #TINY-1649
- Fixed a bug with the fontselect dropdowns throwing an error if the editor was hidden in Firefox. #TINY-1664
- Fixed a bug where it wasn't possible to merge table cells on IE 11. #TINY-1671
- Fixed a bug where textcolor wasn't applying properly on IE 11 in some situations. #TINY-1663
- Fixed a bug where the justifyfull command state wasn't working correctly. #TINY-1677
- Fixed a bug where the styles wasn't updated correctly when resizing some tables. #TINY-1668

## 4.7.12 - 2018-05-03

### Added
- Added an option to filter out image svg data urls.
- Added support for html5 details and summary elements.

### Changed
- Changed so the mce-abs-layout-item css rule targets html instead of body. Patch contributed by nazar-pc.

### Fixed
- Fixed a bug where the "read" step on the mobile theme was still present on android mobile browsers.
- Fixed a bug where all images in the editor document would reload on any editor change.
- Fixed a bug with the Table Plugin where ObjectResized event wasn't being triggered on column resize.
- Fixed so the selection is set to the first suitable caret position after editor.setContent called.
- Fixed so links with xlink:href attributes are filtered correctly to prevent XSS.
- Fixed a bug on IE11 where pasting content into an inline editor initialized on a heading element would create new editable elements.
- Fixed a bug where readonly mode would not work as expected when the editor contained contentEditable=true elements.
- Fixed a bug where the Link Plugin would throw an error when used together with the webcomponents polyfill. Patch contributed by 4esnog.
- Fixed a bug where the "Powered by TinyMCE" branding link would break on XHTML pages. Patch contributed by tistre.
- Fixed a bug where the same id would be used in the blobcache for all pasted images. Patch contributed by thorn0.

## 4.7.11 - 2018-04-11

### Added
- Added a new imagetools_credentials_hosts option to the Imagetools Plugin.

### Fixed
- Fixed a bug where toggling a list containing empty LIs would throw an error. Patch contributed by bradleyke.
- Fixed a bug where applying block styles to a text with the caret at the end of the paragraph would select all text in the paragraph.
- Fixed a bug where toggling on the Spellchecker Plugin would trigger isDirty on the editor.
- Fixed a bug where it was possible to enter content into selection bookmark spans.
- Fixed a bug where if a non paragraph block was configured in forced_root_block the editor.getContent method would return incorrect values with an empty editor.
- Fixed a bug where dropdown menu panels stayed open and fixed in position when dragging dialog windows.
- Fixed a bug where it wasn't possible to extend table cells with the space button in Safari.
- Fixed a bug where the setupeditor event would thrown an error when using the Compat3x Plugin.
- Fixed a bug where an error was thrown in FontInfo when called on a detached element.

## 4.7.10 - 2018-04-03

### Added
- Added normalization of triple clicks across browsers in the editor.
- Added a `hasFocus` method to the editor that checks if the editor has focus.
- Added correct icon to the Nonbreaking Plugin menu item.

### Fixed
- Fixed so the `getContent`/`setContent` methods work even if the editor is not initialized.
- Fixed a bug with the Media Plugin where query strings were being stripped from youtube links.
- Fixed a bug where image styles were changed/removed when opening and closing the Image Plugin dialog.
- Fixed a bug in the Table Plugin where some table cell styles were not correctly added to the content html.
- Fixed a bug in the Spellchecker Plugin where it wasn't possible to change the spellchecker language.
- Fixed so the the unlink action in the Link Plugin has a menu item and can be added to the contextmenu.
- Fixed a bug where it wasn't possible to keyboard navigate to the start of an inline element on a new line within the same block element.
- Fixed a bug with the Text Color Plugin where if used with an inline editor located at the bottom of the screen the colorpicker could appear off screen.
- Fixed a bug with the UndoManager where undo levels were being added for nbzwsp characters.
- Fixed a bug with the Table Plugin where the caret would sometimes be lost when keyboard navigating up through a table.
- Fixed a bug where FontInfo.getFontFamily would throw an error when called on a removed editor.
- Fixed a bug in Firefox where undo levels were not being added correctly for some specific operations.
- Fixed a bug where initializing an inline editor inside of a table would make the whole table resizeable.
- Fixed a bug where the fake cursor that appears next to tables on Firefox was positioned incorrectly when switching to fullscreen.
- Fixed a bug where zwsp's weren't trimmed from the output from `editor.getContent({ format: 'text' })`.
- Fixed a bug where the fontsizeselect/fontselect toolbar items showed the body info rather than the first possible caret position info on init.
- Fixed a bug where it wasn't possible to select all content if the editor only contained an inline boundary element.
- Fixed a bug where `content_css` urls with query strings wasn't working.
- Fixed a bug in the Table Plugin where some table row styles were removed when changing other styles in the row properties dialog.

### Removed
- Removed the "read" step from the mobile theme.

## 4.7.9 - 2018-02-27

### Fixed
- Fixed a bug where the editor target element didn't get the correct style when removing the editor.

## 4.7.8 - 2018-02-26

### Fixed
- Fixed an issue with the Help Plugin where the menuitem name wasn't lowercase.
- Fixed an issue on MacOS where text and bold text did not have the same line-height in the autocomplete dropdown in the Link Plugin dialog.
- Fixed a bug where the "paste as text" option in the Paste Plugin didn't work.
- Fixed a bug where dialog list boxes didn't get positioned correctly in documents with scroll.
- Fixed a bug where the Inlite Theme didn't use the Table Plugin api to insert correct tables.
- Fixed a bug where the Inlite Theme panel didn't hide on blur in a correct way.
- Fixed a bug where placing the cursor before a table in Firefox would scroll to the bottom of the table.
- Fixed a bug where selecting partial text in table cells with rowspans and deleting would produce faulty tables.
- Fixed a bug where the Preview Plugin didn't work on Safari due to sandbox security.
- Fixed a bug where table cell selection using the keyboard threw an error.
- Fixed so the font size and font family doesn't toggle the text but only sets the selected format on the selected text.
- Fixed so the built-in spellchecking on Chrome and Safari creates an undo level when replacing words.

## 4.7.7 - 2018-02-19

### Added
- Added a border style selector to the advanced tab of the Image Plugin.
- Added better controls for default table inserted by the Table Plugin.
- Added new `table_responsive_width` option to the Table Plugin that controls whether to use pixel or percentage widths.

### Fixed
- Fixed a bug where the Link Plugin text didn't update when a URL was pasted using the context menu.
- Fixed a bug with the Spellchecker Plugin where using "Add to dictionary" in the context menu threw an error.
- Fixed a bug in the Media Plugin where the preview node for iframes got default width and height attributes that interfered with width/height styles.
- Fixed a bug where backslashes were being added to some font family names in Firefox in the fontselect toolbar item.
- Fixed a bug where errors would be thrown when trying to remove an editor that had not yet been fully initialized.
- Fixed a bug where the Imagetools Plugin didn't update the images atomically.
- Fixed a bug where the Fullscreen Plugin was throwing errors when being used on an inline editor.
- Fixed a bug where drop down menus weren't positioned correctly in inline editors on scroll.
- Fixed a bug with a semicolon missing at the end of the bundled javascript files.
- Fixed a bug in the Table Plugin with cursor navigation inside of tables where the cursor would sometimes jump into an incorrect table cells.
- Fixed a bug where indenting a table that is a list item using the "Increase indent" button would create a nested table.
- Fixed a bug where text nodes containing only whitespace were being wrapped by paragraph elements.
- Fixed a bug where whitespace was being inserted after br tags inside of paragraph tags.
- Fixed a bug where converting an indented paragraph to a list item would cause the list item to have extra padding.
- Fixed a bug where Copy/Paste in an editor with a lot of content would cause the editor to scroll to the top of the content in IE11.
- Fixed a bug with a memory leak in the DragHelper. Path contributed by ben-mckernan.
- Fixed a bug where the advanced tab in the Media Plugin was being shown even if it didn't contain anything. Patch contributed by gabrieeel.
- Fixed an outdated eventname in the EventUtils. Patch contributed by nazar-pc.
- Fixed an issue where the Json.parse function would throw an error when being used on a page with strict CSP settings.
- Fixed so you can place the curser before and after table elements within the editor in Firefox and Edge/IE.

## 4.7.6 - 2018-01-29

### Fixed
- Fixed a bug in the jquery integration where it threw an error saying that "global is not defined".
- Fixed a bug where deleting a table cell whose previous sibling was set to contenteditable false would create a corrupted table.
- Fixed a bug where highlighting text in an unfocused editor did not work correctly in IE11/Edge.
- Fixed a bug where the table resize handles were not being repositioned when activating the Fullscreen Plugin.
- Fixed a bug where the Imagetools Plugin dialog didn't honor editor RTL settings.
- Fixed a bug where block elements weren't being merged correctly if you deleted from after a contenteditable false element to the beginning of another block element.
- Fixed a bug where TinyMCE didn't work with module loaders like webpack.

## 4.7.5 - 2018-01-22

### Fixed
- Fixed bug with the Codesample Plugin where it wasn't possible to edit codesamples when the editor was in inline mode.
- Fixed bug where focusing on the status bar broke the keyboard navigation functionality.
- Fixed bug where an error would be thrown on Edge by the Table Plugin when pasting using the PowerPaste Plugin.
- Fixed bug in the Table Plugin where selecting row border style from the dropdown menu in advanced row properties would throw an error.
- Fixed bug with icons being rendered incorrectly on Chrome on Mac OS.
- Fixed bug in the Textcolor Plugin where the font color and background color buttons wouldn't trigger an ExecCommand event.
- Fixed bug in the Link Plugin where the url field wasn't forced LTR.
- Fixed bug where the Nonbreaking Plugin incorrectly inserted spaces into tables.
- Fixed bug with the inline theme where the toolbar wasn't repositioned on window resize.

## 4.7.4 - 2017-12-05

### Fixed
- Fixed bug in the Nonbreaking Plugin where the nonbreaking_force_tab setting was being ignored.
- Fixed bug in the Table Plugin where changing row height incorrectly converted column widths to pixels.
- Fixed bug in the Table Plugin on Edge and IE11 where resizing the last column after resizing the table would cause invalid column heights.
- Fixed bug in the Table Plugin where keyboard navigation was not normalized between browsers.
- Fixed bug in the Table Plugin where the colorpicker button would show even without defining the colorpicker_callback.
- Fixed bug in the Table Plugin where it wasn't possible to set the cell background color.
- Fixed bug where Firefox would throw an error when intialising an editor on an element that is hidden or not yet added to the DOM.
- Fixed bug where Firefox would throw an error when intialising an editor inside of a hidden iframe.

## 4.7.3 - 2017-11-23

### Added
- Added functionality to open the Codesample Plugin dialog when double clicking on a codesample. Patch contributed by dakuzen.

### Fixed
- Fixed bug where undo/redo didn't work correctly with some formats and caret positions.
- Fixed bug where the color picker didn't show up in Table Plugin dialogs.
- Fixed bug where it wasn't possible to change the width of a table through the Table Plugin dialog.
- Fixed bug where the Charmap Plugin couldn't insert some special characters.
- Fixed bug where editing a newly inserted link would not actually edit the link but insert a new link next to it.
- Fixed bug where deleting all content in a table cell made it impossible to place the caret into it.
- Fixed bug where the vertical alignment field in the Table Plugin cell properties dialog didn't do anything.
- Fixed bug where an image with a caption showed two sets of resize handles in IE11.
- Fixed bug where pressing the enter button inside of an h1 with contenteditable set to true would sometimes produce a p tag.
- Fixed bug with backspace not working as expected before a noneditable element.
- Fixed bug where operating on tables with invalid rowspans would cause an error to be thrown.
- Fixed so a real base64 representation of the image is available on the blobInfo that the images_upload_handler gets called with.
- Fixed so the image upload tab is available when the images_upload_handler is defined (and not only when the images_upload_url is defined).

## 4.7.2 - 2017-11-07

### Added
- Added newly rewritten Table Plugin.
- Added support for attributes with colon in valid_elements and addValidElements.
- Added support for dailymotion short url in the Media Plugin. Patch contributed by maat8.
- Added support for converting to half pt when converting font size from px to pt. Patch contributed by danny6514.
- Added support for location hash to the Autosave plugin to make it work better with SPAs using hash routing.
- Added support for merging table cells when pasting a table into another table.

### Changed
- Changed so the language packs are only loaded once. Patch contributed by 0xor1.
- Simplified the css for inline boundaries selection by switching to an attribute selector.

### Fixed
- Fixed bug where an error would be thrown on editor initialization if the window.getSelection() returned null.
- Fixed bug where holding down control or alt keys made the keyboard navigation inside an inline boundary not work as expected.
- Fixed bug where applying formats in IE11 produced extra, empty paragraphs in the editor.
- Fixed bug where the Word Count Plugin didn't count some mathematical operators correctly.
- Fixed bug where removing an inline editor removed the element that the editor had been initialized on.
- Fixed bug where setting the selection to the end of an editable container caused some formatting problems.
- Fixed bug where an error would be thrown sometimes when an editor was removed because of the selection bookmark was being stored asynchronously.
- Fixed a bug where an editor initialized on an empty list did not contain any valid cursor positions.
- Fixed a bug with the Context Menu Plugin and webkit browsers on Mac where right-clicking inside a table would produce an incorrect selection.
- Fixed bug where the Image Plugin constrain proportions setting wasn't working as expected.
- Fixed bug where deleting the last character in a span with decorations produced an incorrect element when typing.
- Fixed bug where focusing on inline editors made the toolbar flicker when moving between elements quickly.
- Fixed bug where the selection would be stored incorrectly in inline editors when the mouseup event was fired outside the editor body.
- Fixed bug where toggling bold at the end of an inline boundary would toggle off the whole word.
- Fixed bug where setting the skin to false would not stop the loading of some skin css files.
- Fixed bug in mobile theme where pinch-to-zoom would break after exiting the editor.
- Fixed bug where sublists of a fully selected list would not be switched correctly when changing list style.
- Fixed bug where inserting media by source would break the UndoManager.
- Fixed bug where inserting some content into the editor with a specific selection would replace some content incorrectly.
- Fixed bug where selecting all content with ctrl+a in IE11 caused problems with untoggling some formatting.
- Fixed bug where the Search and Replace Plugin left some marker spans in the editor when undoing and redoing after replacing some content.
- Fixed bug where the editor would not get a scrollbar when using the Fullscreen and Autoresize plugins together.
- Fixed bug where the font selector would stop working correctly after selecting fonts three times.
- Fixed so pressing the enter key inside of an inline boundary inserts a br after the inline boundary element.
- Fixed a bug where it wasn't possible to use tab navigation inside of a table that was inside of a list.
- Fixed bug where end_container_on_empty_block would incorrectly remove elements.
- Fixed bug where content_styles weren't added to the Preview Plugin iframe.
- Fixed so the beforeSetContent/beforeGetContent events are preventable.
- Fixed bug where changing height value in Table Plugin advanced tab didn't do anything.
- Fixed bug where it wasn't possible to remove formatting from content in beginning of table cell.

## 4.7.1 - 2017-10-09

### Fixed
- Fixed bug where theme set to false on an inline editor produced an extra div element after the target element.
- Fixed bug where the editor drag icon was misaligned with the branding set to false.
- Fixed bug where doubled menu items were not being removed as expected with the removed_menuitems setting.
- Fixed bug where the Table of contents plugin threw an error when initialized.
- Fixed bug where it wasn't possible to add inline formats to text selected right to left.
- Fixed bug where the paste from plain text mode did not work as expected.
- Fixed so the style previews do not set color and background color when selected.
- Fixed bug where the Autolink plugin didn't work as expected with some formats applied on an empty editor.
- Fixed bug where the Textpattern plugin were throwing errors on some patterns.
- Fixed bug where the Save plugin saved all editors instead of only the active editor. Patch contributed by dannoe.

## 4.7.0 - 2017-10-03

### Added
- Added new mobile ui that is specifically designed for mobile devices.

### Changed
- Updated the default skin to be more modern and white since white is preferred by most implementations.
- Restructured the default menus to be more similar to common office suites like Google Docs.

### Fixed
- Fixed so theme can be set to false on both inline and iframe editor modes.
- Fixed bug where inline editor would add/remove the visualblocks css multiple times.
- Fixed bug where selection wouldn't be properly restored when editor lost focus and commands where invoked.
- Fixed bug where toc plugin would generate id:s for headers even though a toc wasn't inserted into the content.
- Fixed bug where is wasn't possible to drag/drop contents within the editor if paste_data_images where set to true.
- Fixed bug where getParam and close in WindowManager would get the first opened window instead of the last opened window.
- Fixed bug where delete would delete between cells inside a table in Firefox.

## 4.6.7 - 2017-09-18

### Added
- Added some missing translations to Image, Link and Help plugins.

### Fixed
- Fixed bug where paste wasn't working in IOS.
- Fixed bug where the Word Count Plugin didn't count some mathematical operators correctly.
- Fixed bug where inserting a list in a table caused the cell to expand in height.
- Fixed bug where pressing enter in a list located inside of a table deleted list items instead of inserting new list item.
- Fixed bug where copy and pasting table cells produced inconsistent results.
- Fixed bug where initializing an editor with an ID of 'length' would throw an exception.
- Fixed bug where it was possible to split a non merged table cell.
- Fixed bug where copy and pasting a list with a very specific selection into another list would produce a nested list.
- Fixed bug where copy and pasting ordered lists sometimes produced unordered lists.
- Fixed bug where padded elements inside other elements would be treated as empty.
- Fixed so you can resize images inside a figure element.
- Fixed bug where an inline TinyMCE editor initialized on a table did not set selection on load in Chrome.
- Fixed the positioning of the inlite toolbar when the target element wasn't big enough to fit the toolbar.

## 4.6.6 - 2017-08-30

### Fixed
- Fixed so that notifications wrap long text content instead of bleeding outside the notification element.
- Fixed so the content_style css is added after the skin and custom stylesheets.
- Fixed bug where it wasn't possible to remove a table with the Cut button.
- Fixed bug where the center format wasn't getting the same font size as the other formats in the format preview.
- Fixed bug where the wordcount plugin wasn't counting hyphenated words correctly.
- Fixed bug where all content pasted into the editor was added to the end of the editor.
- Fixed bug where enter keydown on list item selection only deleted content and didn't create a new line.
- Fixed bug where destroying the editor while the content css was still loading caused error notifications on Firefox.
- Fixed bug where undoing cut operation in IE11 left some unwanted html in the editor content.
- Fixed bug where enter keydown would throw an error in IE11.
- Fixed bug where duplicate instances of an editor were added to the editors array when using the createEditor API.
- Fixed bug where the formatter applied formats on the wrong content when spellchecker was activated.
- Fixed bug where switching formats would reset font size on child nodes.
- Fixed bug where the table caption element weren't always the first descendant to the table tag.
- Fixed bug where pasting some content into the editor on chrome some newlines were removed.
- Fixed bug where it wasn't possible to remove a list if a list item was a table element.
- Fixed bug where copy/pasting partial selections of tables wouldn't produce a proper table.
- Fixed bug where the searchreplace plugin could not find consecutive spaces.
- Fixed bug where background color wasn't applied correctly on some partially selected contents.

## 4.6.5 - 2017-08-02

### Added
- Added new inline_boundaries_selector that allows you to specify the elements that should have boundaries.
- Added new local upload feature this allows the user to upload images directly from the image dialog.
- Added a new api for providing meta data for plugins. It will show up in the help dialog if it's provided.

### Fixed
- Fixed so that the notifications created by the notification manager are more screen reader accessible.
- Fixed bug where changing the list format on multiple selected lists didn't change all of the lists.
- Fixed bug where the nonbreaking plugin would insert multiple undo levels when pressing the tab key.
- Fixed bug where delete/backspace wouldn't render a caret when all editor contents where deleted.
- Fixed bug where delete/backspace wouldn't render a caret if the deleted element was a single contentEditable false element.
- Fixed bug where the wordcount plugin wouldn't count words correctly if word where typed after applying a style format.
- Fixed bug where the wordcount plugin would count mathematical formulas as multiple words for example 1+1=2.
- Fixed bug where formatting of triple clicked blocks on Chrome/Safari would result in styles being added outside the visual selection.
- Fixed bug where paste would add the contents to the end of the editor area when inline mode was used.
- Fixed bug where toggling off bold formatting on text entered in a new paragraph would add an extra line break.
- Fixed bug where autolink plugin would only produce a link on every other consecutive link on Firefox.
- Fixed bug where it wasn't possible to select all contents if the content only had one pre element.
- Fixed bug where sizzle would produce lagging behavior on some sites due to repaints caused by feature detection.
- Fixed bug where toggling off inline formats wouldn't include the space on selected contents with leading or trailing spaces.
- Fixed bug where the cut operation in UI wouldn't work in Chrome.
- Fixed bug where some legacy editor initialization logic would throw exceptions about editor settings not being defined.
- Fixed bug where it wasn't possible to apply text color to links if they where part of a non collapsed selection.
- Fixed bug where an exception would be thrown if the user selected a video element and then moved the focus outside the editor.
- Fixed bug where list operations didn't work if there where block elements inside the list items.
- Fixed bug where applying block formats to lists wrapped in block elements would apply to all elements in that wrapped block.

## 4.6.4 - 2017-06-13

### Fixed
- Fixed bug where the editor would move the caret when clicking on the scrollbar next to a content editable false block.
- Fixed bug where the text color select dropdowns wasn't placed correctly when they didn't fit the width of the screen.
- Fixed bug where the default editor line height wasn't working for mixed font size contents.
- Fixed bug where the content css files for inline editors were loaded multiple times for multiple editor instances.
- Fixed bug where the initial value of the font size/font family dropdowns wasn't displayed.
- Fixed bug where the I18n api was not supporting arrays as the translation replacement values.
- Fixed bug where chrome would display "The given range isn't in document." errors for invalid ranges passed to setRng.
- Fixed bug where the compat3x plugin wasn't working since the global tinymce references wasn't resolved correctly.
- Fixed bug where the preview plugin wasn't encoding the base url passed into the iframe contents producing a xss bug.
- Fixed bug where the dom parser/serializer wasn't handling some special elements like noframes, title and xmp.
- Fixed bug where the dom parser/serializer wasn't handling cdata sections with comments inside.
- Fixed bug where the editor would scroll to the top of the editable area if a dialog was closed in inline mode.
- Fixed bug where the link dialog would not display the right rel value if rel_list was configured.
- Fixed bug where the context menu would select images on some platforms but not others.
- Fixed bug where the filenames of images were not retained on dragged and drop into the editor from the desktop.
- Fixed bug where the paste plugin would misrepresent newlines when pasting plain text and having forced_root_block configured.
- Fixed so that the error messages for the imagetools plugin is more human readable.
- Fixed so the internal validate setting for the parser/serializer can't be set from editor initialization settings.

## 4.6.3 - 2017-05-30

### Fixed
- Fixed bug where the arrow keys didn't work correctly when navigating on nested inline boundary elements.
- Fixed bug where delete/backspace didn't work correctly on nested inline boundary elements.
- Fixed bug where image editing didn't work on subsequent edits of the same image.
- Fixed bug where charmap descriptions wouldn't properly wrap if they exceeded the width of the box.
- Fixed bug where the default image upload handler only accepted 200 as a valid http status code.
- Fixed so rel on target=_blank links gets forced with only noopener instead of both noopener and noreferrer.

## 4.6.2 - 2017-05-23

### Fixed
- Fixed bug where the SaxParser would run out of memory on very large documents.
- Fixed bug with formatting like font size wasn't applied to del elements.
- Fixed bug where various api calls would be throwing exceptions if they where invoked on a removed editor instance.
- Fixed bug where the branding position would be incorrect if the editor was inside a hidden tab and then later showed.
- Fixed bug where the color levels feature in the imagetools dialog wasn't working properly.
- Fixed bug where imagetools dialog wouldn't pre-load images from CORS domains, before trying to prepare them for editing.
- Fixed bug where the tab key would move the caret to the next table cell if being pressed inside a list inside a table.
- Fixed bug where the cut/copy operations would loose parent context like the current format etc.
- Fixed bug with format preview not working on invalid elements excluded by valid_elements.
- Fixed bug where blocks would be merged in incorrect order on backspace/delete.
- Fixed bug where zero length text nodes would cause issues with the undo logic if there where iframes present.
- Fixed bug where the font size/family select lists would throw errors if the first node was a comment.
- Fixed bug with csp having to allow local script evaluation since it was used to detect global scope.
- Fixed bug where CSP required a relaxed option for javascript: URLs in unsupported legacy browsers.
- Fixed bug where a fake caret would be rendered for td with the contenteditable=false.
- Fixed bug where typing would be blocked on IE 11 when within a nested contenteditable=true/false structure.

## 4.6.1 - 2017-05-10

### Added
- Added configuration option to list plugin to disable tab indentation.

### Fixed
- Fixed bug where format change on very specific content could cause the selection to change.
- Fixed bug where TinyMCE could not be lazyloaded through jquery integration.
- Fixed bug where entities in style attributes weren't decoded correctly on paste in webkit.
- Fixed bug where fontsize_formats option had been renamed incorrectly.
- Fixed bug with broken backspace/delete behaviour between contenteditable=false blocks.
- Fixed bug where it wasn't possible to backspace to the previous line with the inline boundaries functionality turned on.
- Fixed bug where is wasn't possible to move caret left and right around a linked image with the inline boundaries functionality turned on.
- Fixed bug where pressing enter after/before hr element threw exception. Patch contributed bradleyke.
- Fixed so the CSS in the visualblocks plugin doesn't overwrite background color. Patch contributed by Christian Rank.
- Fixed bug where multibyte characters weren't encoded correctly. Patch contributed by James Tarkenton.
- Fixed bug where shift-click to select within contenteditable=true fields wasn't working.

## 4.6.0 - 2017-05-04

### Added
- Added an inline boundary caret position feature that makes it easier to type at the beginning/end of links/code elements.
- Added a help plugin that adds a button and a dialog showing the editor shortcuts and loaded plugins.
- Added an inline_boundaries option that allows you to disable the inline boundary feature if it's not desired.
- Added a new ScrollIntoView event that allows you to override the default scroll to element behavior.
- Added role and aria- attributes as valid elements in the default valid elements config.
- Added new internal flag for PastePreProcess/PastePostProcess this is useful to know if the paste was coming from an external source.
- Added new ignore function to UndoManager this works similar to transact except that it doesn't add an undo level by default.

### Fixed
- Fixed so that urls gets retained for images when being edited. This url is then passed on to the upload handler.
- Fixed so that the editors would be initialized on readyState interactive instead of complete.
- Fixed so that the init event of the editor gets fired once all contentCSS files have been properly loaded.
- Fixed so that width/height of the editor gets taken from the textarea element if it's explicitly specified in styles.
- Fixed so that keep_styles set to false no longer clones class/style from the previous paragraph on enter.
- Fixed so that the default line-height is 1.2em to avoid zwnbsp characters from producing text rendering glitches on Windows.
- Fixed so that loading errors of content css gets presented by a notification message.
- Fixed so figure image elements can be linked when selected this wraps the figure image in a anchor element.
- Fixed bug where it wasn't possible to copy/paste rows with colspans by using the table copy/paste feature.
- Fixed bug where the protect setting wasn't properly applied to header/footer parts when using the fullpage plugin.
- Fixed bug where custom formats that specified upper case element names where not applied correctly.
- Fixed bug where some screen readers weren't reading buttons due to an aria specific fix for IE 8.
- Fixed bug where cut wasn't working correctly on iOS due to it's clipboard API not working correctly.
- Fixed bug where Edge would paste div elements instead of paragraphs when pasting plain text.
- Fixed bug where the textpattern plugin wasn't dealing with trailing punctuations correctly.
- Fixed bug where image editing would some times change the image format from jpg to png.
- Fixed bug where some UI elements could be inserted into the toolbar even if they where not registered.
- Fixed bug where it was possible to click the TD instead of the character in the character map and that caused an exception.
- Fixed bug where the font size/font family dropdowns would sometimes show an incorrect value due to css not being loaded in time.
- Fixed bug with the media plugin inserting undefined instead of retaining size when media_dimensions was set to false.
- Fixed bug with deleting images when forced_root_blocks where set to false.
- Fixed bug where input focus wasn't properly handled on nested content editable elements.
- Fixed bug where Chrome/Firefox would throw an exception when selecting images due to recent change of setBaseAndExtent support.
- Fixed bug where malformed blobs would throw exceptions now they are simply ignored.
- Fixed bug where backspace/delete wouldn't work properly in some cases where all contents was selected in WebKit.
- Fixed bug with Angular producing errors since it was expecting events objects to be patched with their custom properties.
- Fixed bug where the formatter would apply formatting to spellchecker errors now all bogus elements are excluded.
- Fixed bug with backspace/delete inside table caption elements wouldn't behave properly on IE 11.
- Fixed bug where typing after a contenteditable false inline element could move the caret to the end of that element.
- Fixed bug where backspace before/after contenteditable false blocks wouldn't properly remove the right element.
- Fixed bug where backspace before/after contenteditable false inline elements wouldn't properly empty the current block element.
- Fixed bug where vertical caret navigation with a custom line-height would sometimes match incorrect positions.
- Fixed bug with paste on Edge where character encoding wasn't handled properly due to a browser bug.
- Fixed bug with paste on Edge where extra fragment data was inserted into the contents when pasting.
- Fixed bug with pasting contents when having a whole block element selected on WebKit could cause WebKit spans to appear.
- Fixed bug where the visualchars plugin wasn't working correctly showing invisible nbsp characters.
- Fixed bug where browsers would hang if you tried to load some malformed html contents.
- Fixed bug where the init call promise wouldn't resolve if the specified selector didn't find any matching elements.
- Fixed bug where the Schema isValidChild function was case sensitive.

### Removed
- Dropped support for IE 8-10 due to market share and lack of support from Microsoft. See tinymce docs for details.

## 4.5.3 - 2017-02-01

### Added
- Added keyboard navigation for menu buttons when the menu is in focus.
- Added api to the list plugin for setting custom classes/attributes on lists.
- Added validation for the anchor plugin input field according to W3C id naming specifications.

### Fixed
- Fixed bug where media placeholders were removed after resize with the forced_root_block setting set to false.
- Fixed bug where deleting selections with similar sibling nodes sometimes deleted the whole document.
- Fixed bug with inlite theme where several toolbars would appear scrolling when more than one instance of the editor was in use.
- Fixed bug where the editor would throw error with the fontselect plugin on hidden editor instances in Firefox.
- Fixed bug where the background color would not stretch to the font size.
- Fixed bug where font size would be removed when changing background color.
- Fixed bug where the undomanager trimmed away whitespace between nodes on undo/redo.
- Fixed bug where media_dimensions=false in media plugin caused the editor to throw an error.
- Fixed bug where IE was producing font/u elements within links on paste.
- Fixed bug where some button tooltips were broken when compat3x was in use.
- Fixed bug where backspace/delete/typeover would remove the caption element.
- Fixed bug where powerspell failed to function when compat3x was enabled.
- Fixed bug where it wasn't possible to apply sub/sup on text with large font size.
- Fixed bug where pre tags with spaces weren't treated as content.
- Fixed bug where Meta+A would select the entire document instead of all contents in nested ce=true elements.

## 4.5.2 - 2017-01-04

### Fixed
- Added missing keyboard shortcut description for the underline menu item in the format menu.
- Fixed bug where external blob urls wasn't properly handled by editor upload logic. Patch contributed by David Oviedo.
- Fixed bug where urls wasn't treated as a single word by the wordcount plugin.
- Fixed bug where nbsp characters wasn't treated as word delimiters by the wordcount plugin.
- Fixed bug where editor instance wasn't properly passed to the format preview logic. Patch contributed by NullQuery.
- Fixed bug where the fake caret wasn't hidden when you moved selection to a cE=false element.
- Fixed bug where it wasn't possible to edit existing code sample blocks.
- Fixed bug where it wasn't possible to delete editor contents if the selection included an empty block.
- Fixed bug where the formatter wasn't expanding words on some international characters. Patch contributed by Martin Larochelle.
- Fixed bug where the open link feature wasn't working correctly on IE 11.
- Fixed bug where enter before/after a cE=false block wouldn't properly padd the paragraph with an br element.
- Fixed so font size and font family select boxes always displays a value by using the runtime style as a fallback.
- Fixed so missing plugins will be logged to console as warnings rather than halting the initialization of the editor.
- Fixed so splitbuttons become normal buttons in advlist plugin if styles are empty. Patch contributed by René Schleusner.
- Fixed so you can multi insert rows/cols by selecting table cells and using insert rows/columns.

## 4.5.1 - 2016-12-07

### Fixed
- Fixed bug where the lists plugin wouldn't initialize without the advlist plugins if served from cdn.
- Fixed bug where selectors with "*" would cause the style format preview to throw an error.
- Fixed bug with toggling lists off on lists with empty list items would throw an error.
- Fixed bug where editing images would produce non existing blob uris.
- Fixed bug where the offscreen toc selection would be treated as the real toc element.
- Fixed bug where the aria level attribute for element path would have an incorrect start index.
- Fixed bug where the offscreen selection of cE=false that where very wide would be shown onscreen. Patch contributed by Steven Bufton.
- Fixed so the default_link_target gets applied to links created by the autolink plugin.
- Fixed so that the name attribute gets removed by the anchor plugin if editing anchors.

## 4.5.0 - 2016-11-23

### Added
- Added new toc plugin allows you to insert table of contents based on editor headings.
- Added new auto complete menu to all url fields. Adds history, link to anchors etc.
- Added new sidebar api that allows you to add custom sidebar panels and buttons to toggle these.
- Added new insert menu button that allows you to have multiple insert functions under the same menu button.
- Added new open link feature to ctrl+click, alt+enter and context menu.
- Added new media_embed_handler option to allow the media plugin to be populated with custom embeds.
- Added new support for editing transparent images using the image tools dialog.
- Added new images_reuse_filename option to allow filenames of images to be retained for upload.
- Added new security feature where links with target="_blank" will by default get rel="noopener noreferrer".
- Added new allow_unsafe_link_target to allow you to opt-out of the target="_blank" security feature.
- Added new style_formats_autohide option to automatically hide styles based on context.
- Added new codesample_content_css option to specify where the code sample prism css is loaded from.
- Added new support for Japanese/Chinese word count following the unicode standards on this.
- Added new fragmented undo levels this dramatically reduces flicker on contents with iframes.
- Added new live previews for complex elements like table or lists.

### Fixed
- Fixed bug where it wasn't possible to properly tab between controls in a dialog with a disabled form item control.
- Fixed bug where firefox would generate a rectangle on elements produced after/before a cE=false elements.
- Fixed bug with advlist plugin not switching list element format properly in some edge cases.
- Fixed bug where col/rowspans wasn't correctly computed by the table plugin in some cases.
- Fixed bug where the table plugin would thrown an error if object_resizing was disabled.
- Fixed bug where some invalid markup would cause issues when running in XHTML mode. Patch contributed by Charles Bourasseau.
- Fixed bug where the fullscreen class wouldn't be removed properly when closing dialogs.
- Fixed bug where the PastePlainTextToggle event wasn't fired by the paste plugin when the state changed.
- Fixed bug where table the row type wasn't properly updated in table row dialog. Patch contributed by Matthias Balmer.
- Fixed bug where select all and cut wouldn't place caret focus back to the editor in WebKit. Patch contributed by Daniel Jalkut.
- Fixed bug where applying cell/row properties to multiple cells/rows would reset other unchanged properties.
- Fixed bug where some elements in the schema would have redundant/incorrect children.
- Fixed bug where selector and target options would cause issues if used together.
- Fixed bug where drag/drop of images from desktop on chrome would thrown an error.
- Fixed bug where cut on WebKit/Blink wouldn't add an undo level.
- Fixed bug where IE 11 would scroll to the cE=false elements when they where selected.
- Fixed bug where keys like F5 wouldn't work when a cE=false element was selected.
- Fixed bug where the undo manager wouldn't stop the typing state when commands where executed.
- Fixed bug where unlink on wrapped links wouldn't work properly.
- Fixed bug with drag/drop of images on WebKit where the image would be deleted form the source editor.
- Fixed bug where the visual characters mode would be disabled when contents was extracted from the editor.
- Fixed bug where some browsers would toggle of formats applied to the caret when clicking in the editor toolbar.
- Fixed bug where the custom theme function wasn't working correctly.
- Fixed bug where image option for custom buttons required you to have icon specified as well.
- Fixed bug where the context menu and contextual toolbars would be visible at the same time and sometimes overlapping.
- Fixed bug where the noneditable plugin would double wrap elements when using the noneditable_regexp option.
- Fixed bug where tables would get padding instead of margin when you used the indent button.
- Fixed bug where the charmap plugin wouldn't properly insert non breaking spaces.
- Fixed bug where the color previews in color input boxes wasn't properly updated.
- Fixed bug where the list items of previous lists wasn't merged in the right order.
- Fixed bug where it wasn't possible to drag/drop inline-block cE=false elements on IE 11.
- Fixed bug where some table cell merges would produce incorrect rowspan/colspan.
- Fixed so the font size of the editor defaults to 14px instead of 11px this can be overridden by custom css.
- Fixed so wordcount is debounced to reduce cpu hogging on larger texts.
- Fixed so tinymce global gets properly exported as a module when used with some module bundlers.
- Fixed so it's possible to specify what css properties you want to preview on specific formats.
- Fixed so anchors are contentEditable=false while within the editor.
- Fixed so selected contents gets wrapped in a inline code element by the codesample plugin.
- Fixed so conditional comments gets properly stripped independent of case. Patch contributed by Georgii Dolzhykov.
- Fixed so some escaped css sequences gets properly handled. Patch contributed by Georgii Dolzhykov.
- Fixed so notifications with the same message doesn't get displayed at the same time.
- Fixed so F10 can be used as an alternative key to focus to the toolbar.
- Fixed various api documentation issues and typos.

### Removed
- Removed layer plugin since it wasn't really ported from 3.x and there doesn't seem to be much use for it.
- Removed moxieplayer.swf from the media plugin since it wasn't used by the media plugin.
- Removed format state from the advlist plugin to be more consistent with common word processors.

## 4.4.3 - 2016-09-01

### Fixed
- Fixed bug where copy would produce an exception on Chrome.
- Fixed bug where deleting lists on IE 11 would merge in correct text nodes.
- Fixed bug where deleting partial lists with indentation wouldn't cause proper normalization.

## 4.4.2 - 2016-08-25

### Added
- Added new importcss_exclusive option to disable unique selectors per group.
- Added new group specific selector_converter option to importcss plugin.
- Added new codesample_languages option to apply custom languages to codesample plugin.
- Added new codesample_dialog_width/codesample_dialog_height options.

### Fixed
- Fixed bug where fullscreen button had an incorrect keyboard shortcut.
- Fixed bug where backspace/delete wouldn't work correctly from a block to a cE=false element.
- Fixed bug where smartpaste wasn't detecting links with special characters in them like tilde.
- Fixed bug where the editor wouldn't get proper focus if you clicked on a cE=false element.
- Fixed bug where it wasn't possible to copy/paste table rows that had merged cells.
- Fixed bug where merging cells could some times produce invalid col/rowspan attibute values.
- Fixed bug where getBody would sometimes thrown an exception now it just returns null if the iframe is clobbered.
- Fixed bug where drag/drop of cE=false element wasn't properly constrained to viewport.
- Fixed bug where contextmenu on Mac would collapse any selection to a caret.
- Fixed bug where rtl mode wasn't rendered properly when loading a language pack with the rtl flag.
- Fixed bug where Kamer word bounderies would be stripped from contents.
- Fixed bug where lists would sometimes render two dots or numbers on the same line.
- Fixed bug where the skin_url wasn't used by the inlite theme.
- Fixed so data attributes are ignored when comparing formats in the formatter.
- Fixed so it's possible to disable inline toolbars in the inlite theme.
- Fixed so template dialog gets resized if it doesn't fit the window viewport.

## 4.4.1 - 2016-07-26

### Added
- Added smart_paste option to paste plugin to allow disabling the paste behavior if needed.

### Fixed
- Fixed bug where png urls wasn't properly detected by the smart paste logic.
- Fixed bug where the element path wasn't working properly when multiple editor instances where used.
- Fixed bug with creating lists out of multiple paragraphs would just create one list item instead of multiple.
- Fixed bug where scroll position wasn't properly handled by the inlite theme to place the toolbar properly.
- Fixed bug where multiple instances of the editor using the inlite theme didn't render the toolbar properly.
- Fixed bug where the shortcut label for fullscreen mode didn't match the actual shortcut key.
- Fixed bug where it wasn't possible to select cE=false blocks using touch devices on for example iOS.
- Fixed bug where it was possible to select the child image within a cE=false on IE 11.
- Fixed so inserts of html containing lists doesn't merge with any existing lists unless it's a paste operation.

## 4.4.0 - 2016-06-30

### Added
- Added new inlite theme this is a more lightweight inline UI.
- Added smarter paste logic that auto detects urls in the clipboard and inserts images/links based on that.
- Added a better image resize algorithm for better image quality in the imagetools plugin.

### Fixed
- Fixed bug where it wasn't possible to drag/dropping cE=false elements on FF.
- Fixed bug where backspace/delete before/after a cE=false block would produce a new paragraph.
- Fixed bug where list style type css property wasn't preserved when indenting lists.
- Fixed bug where merging of lists where done even if the list style type was different.
- Fixed bug where the image_dataimg_filter function wasn't used when pasting images.
- Fixed bug where nested editable within a non editable element would cause scroll on focus in Chrome.
- Fixed so invalid targets for inline mode is blocked on initialization. We only support elements that can have children.

## 4.3.13 - 2016-06-08

### Added
- Added characters with a diacritical mark to charmap plugin. Patch contributed by Dominik Schilling.
- Added better error handling if the image proxy service would produce errors.

### Fixed
- Fixed issue with pasting list items into list items would produce nested list rather than a merged list.
- Fixed bug where table selection could get stuck in selection mode for inline editors.
- Fixed bug where it was possible to place the caret inside the resize grid elements.
- Fixed bug where it wasn't possible to place in elements horizontally adjacent cE=false blocks.
- Fixed bug where multiple notifications wouldn't be properly placed on screen.
- Fixed bug where multiple editor instance of the same id could be produces in some specific integrations.

## 4.3.12 - 2016-05-10

### Fixed
- Fixed bug where focus calls couldn't be made inside the editors PostRender event handler.
- Fixed bug where some translations wouldn't work as expected due to a bug in editor.translate.
- Fixed bug where the node change event could fire with a node out side the root of the editor.
- Fixed bug where Chrome wouldn't properly present the keyboard paste clipboard details when paste was clicked.
- Fixed bug where merged cells in tables couldn't be selected from right to left.
- Fixed bug where insert row wouldn't properly update a merged cells rowspan property.
- Fixed bug where the color input boxes preview field wasn't properly set on initialization.
- Fixed bug where IME composition inside table cells wouldn't work as expected on IE 11.
- Fixed so all shadow dom support is under and experimental flag due to flaky browser support.

## 4.3.11 - 2016-04-25

### Fixed
- Fixed bug where it wasn't possible to insert empty blocks though the API unless they where padded.
- Fixed bug where you couldn't type the Euro character on Windows.
- Fixed bug where backspace/delete from a cE=false element to a text block didn't work properly.
- Fixed bug where the text color default grid would render incorrectly.
- Fixed bug where the codesample plugin wouldn't load the css in the editor for multiple editors.
- Fixed so the codesample plugin textarea gets focused by default.

## 4.3.10 - 2016-04-12

### Fixed
- Fixed bug where the key "y" on WebKit couldn't be entered due to conflict with keycode for F10 on keypress.

## 4.3.9 - 2016-04-12

### Added
- Added support for focusing the contextual toolbars using keyboard.
- Added keyboard support for slider UI controls. You can no increase/decrease using arrow keys.
- Added url pattern matching for Dailymotion to media plugin. Patch contributed by Bertrand Darbon.
- Added body_class to template plugin preview. Patch contributed by Milen Petrinski.
- Added options to better override textcolor pickers with custom colors. Patch contributed by Xavier Boubert.
- Added visual arrows to inline contextual toolbars so that they point to the element being active.

### Changed
- Changed the Meta+Shift+F shortcut to Ctrl+Shift+F since Czech, Slovak, Polish languages used the first one for input.

### Fixed
- Fixed so toolbars for tables or other larger elements get better positioned below the scrollable viewport.
- Fixed bug where it was possible to click links inside cE=false blocks.
- Fixed bug where event targets wasn't properly handled in Safari Technical Preview.
- Fixed bug where drag/drop text in FF 45 would make the editor caret invisible.
- Fixed bug where the remove state wasn't properly set on editor instances when detected as clobbered.
- Fixed bug where offscreen selection of some cE=false elements would render onscreen. Patch contributed by Steven Bufton
- Fixed bug where enter would clone styles out side the root on editors inside a span. Patch contributed by ChristophKaser.
- Fixed bug where drag/drop of images into the editor didn't work correctly in FF.
- Fixed so the first item in panels for the imagetools dialog gets proper keyboard focus.

## 4.3.8 - 2016-03-15

### Fixed
- Fixed bug where inserting HR at the end of a block element would produce an extra empty block.
- Fixed bug where links would be clickable when readonly mode was enabled.
- Fixed bug where the formatter would normalize to the wrong node on very specific content.
- Fixed bug where some nested list items couldn't be indented properly.
- Fixed bug where links where clickable in the preview dialog.
- Fixed so the alt attribute doesn't get padded with an empty value by default.
- Fixed so nested alignment works more correctly. You will now alter the alignment to the closest block parent.

## 4.3.7 - 2016-03-02

### Fixed
- Fixed bug where incorrect icons would be rendered for imagetools edit and color levels.
- Fixed bug where navigation using arrow keys inside a SelectBox didn't move up/down.
- Fixed bug where the visualblocks plugin would render borders round internal UI elements.

## 4.3.6 - 2016-03-01

### Added
- Added new paste_remember_plaintext_info option to allow a global disable of the plain text mode notification.
- Added new PastePlainTextToggle event that fires when plain text mode toggles on/off.

### Fixed
- Fixed bug where it wasn't possible to select media elements since the drag logic would snap it to mouse cursor.
- Fixed bug where it was hard to place the caret inside nested cE=true elements when the outer cE=false element was focused.
- Fixed bug where editors wouldn't properly initialize if both selector and mode where used.
- Fixed bug where IME input inside table cells would switch the IME off.
- Fixed bug where selection inside the first table cell would cause the whole table cell to get selected.
- Fixed bug where error handling of images being uploaded wouldn't properly handle faulty statuses.
- Fixed bug where inserting contents before a HR would cause an exception to be thrown.
- Fixed bug where copy/paste of Excel data would be inserted as an image.
- Fixed caret position issues with copy/paste of inline block cE=false elements.
- Fixed issues with various menu item focus bugs in Chrome. Where the focused menu bar item wasn't properly blurred.
- Fixed so the notifications have a solid background since it would be hard to read if there where text under it.
- Fixed so notifications gets animated similar to the ones used by dialogs.
- Fixed so larger images that gets pasted is handled better.
- Fixed so the window close button is more uniform on various platform and also increased it's hit area.

## 4.3.5 - 2016-02-11

Npm version bump due to package not being fully updated.

## 4.3.4 - 2016-02-11

### Added
- Added new OpenWindow/CloseWindow events that gets fired when windows open/close.
- Added new NewCell/NewRow events that gets fired when table cells/rows are created.
- Added new Promise return value to tinymce.init makes it easier to handle initialization.

### Fixed
- Fixed various bugs with drag/drop of contentEditable:false elements.
- Fixed bug where deleting of very specific nested list items would result in an odd list.
- Fixed bug where lists would get merged with adjacent lists outside the editable inline root.
- Fixed bug where MS Edge would crash when closing a dialog then clicking a menu item.
- Fixed bug where table cell selection would add undo levels.
- Fixed bug where table cell selection wasn't removed when inline editor where removed.
- Fixed bug where table cell selection wouldn't work properly on nested tables.
- Fixed bug where table merge menu would be available when merging between thead and tbody.
- Fixed bug where table row/column resize wouldn't get properly removed when the editor was removed.
- Fixed bug where Chrome would scroll to the editor if there where a empty hash value in document url.
- Fixed bug where the cache suffix wouldn't work correctly with the importcss plugin.
- Fixed bug where selection wouldn't work properly on MS Edge on Windows Phone 10.
- Fixed so adjacent pre blocks gets joined into one pre block since that seems like the user intent.
- Fixed so events gets properly dispatched in shadow dom. Patch provided by Nazar Mokrynskyi.

### Removed
- Removed the jQuery version the jQuery plugin is now moved into the main package.
- Removed jscs from build process since eslint can now handle code style checking.

## 4.3.3 - 2016-01-14

### Added
- Added new table_resize_bars configuration setting.  This setting allows you to disable the table resize bars.
- Added new beforeInitialize event to tinymce.util.XHR lets you modify XHR properties before open. Patch contributed by Brent Clintel.
- Added new autolink_pattern setting to autolink plugin. Enables you to override the default autolink formats. Patch contributed by Ben Tiedt.
- Added new charmap option that lets you override the default charmap of the charmap plugin.
- Added new charmap_append option that lets you add new characters to the default charmap of the charmap plugin.
- Added new insertCustomChar event that gets fired when a character is inserted by the charmap plugin.

### Fixed
- Fixed bug where table cells started with a superfluous &nbsp; in IE10+.
- Fixed bug where table plugin would retain all BR tags when cells were merged.
- Fixed bug where media plugin would strip underscores from youtube urls.
- Fixed bug where IME input would fail on IE 11 if you typed within a table.
- Fixed bug where double click selection of a word would remove the space before the word on insert contents.
- Fixed bug where table plugin would produce exceptions when hovering tables with invalid structure.
- Fixed bug where fullscreen wouldn't scroll back to it's original position when untoggled.
- Fixed so the template plugins templates setting can be a function that gets a callback that can provide templates.

## 4.3.2 - 2015-12-14

### Fixed
- Fixed bug where the resize bars for table cells were not affected by the object_resizing property.
- Fixed bug where the contextual table toolbar would appear incorrectly if TinyMCE was initialized inline inside a table.
- Fixed bug where resizing table cells did not fire a node change event or add an undo level.
- Fixed bug where double click selection of text on IE 11 wouldn't work properly.
- Fixed bug where codesample plugin would incorrectly produce br elements inside code elements.
- Fixed bug where media plugin would strip dashes from youtube urls.
- Fixed bug where it was possible to move the caret into the table resize bars.
- Fixed bug where drag/drop into a cE=false element was possible on IE.

## 4.3.1 - 2015-11-30

### Fixed
- Fixed so it's possible to disable the table inline toolbar by setting it to false or an empty string.
- Fixed bug where it wasn't possible to resize some tables using the drag handles.
- Fixed bug where unique id:s would clash for multiple editor instances and cE=false selections.
- Fixed bug where the same plugin could be initialized multiple times.
- Fixed bug where the table inline toolbars would be displayed at the same time as the image toolbars.
- Fixed bug where the table selection rect wouldn't be removed when selecting another control element.

## 4.3.0 - 2015-11-23

### Added
- Added new table column/row resize support. Makes it a lot more easy to resize the columns/rows in a table.
- Added new table inline toolbar. Makes it easier to for example add new rows or columns to a table.
- Added new notification API. Lets you display floating notifications to the end user.
- Added new codesample plugin that lets you insert syntax highlighted pre elements into the editor.
- Added new image_caption to images. Lets you create images with captions using a HTML5 figure/figcaption elements.
- Added new live previews of embeded videos. Lets you play the video right inside the editor.
- Added new setDirty method and "dirty" event to the editor. Makes it easier to track the dirty state change.
- Added new setMode method to Editor instances that lets you dynamically switch between design/readonly.
- Added new core support for contentEditable=false elements within the editor overrides the browsers broken behavior.

### Changed
- Rewrote the noneditable plugin to use the new contentEditable false core logic.

### Fixed
- Fixed so the dirty state doesn't set to false automatically when the undo index is set to 0.
- Fixed the Selection.placeCaretAt so it works better on IE when the coordinate is between paragraphs.
- Fixed bug where data-mce-bogus="all" element contents where counted by the word count plugin.
- Fixed bug where contentEditable=false elements would be indented by the indent buttons.
- Fixed bug where images within contentEditable=false would be selected in WebKit on mouse click.
- Fixed bug in DOMUntils split method where the replacement parameter wouldn't work on specific cases.
- Fixed bug where the importcss plugin would import classes from the skin content css file.
- Fixed so all button variants have a wrapping span for it's text to make it easier to skin.
- Fixed so it's easier to exit pre block using the arrow keys.
- Fixed bug where listboxes with fix widths didn't render correctly.

## 4.2.8 - 2015-11-13

### Fixed
- Fixed bug where it was possible to delete tables as the inline root element if all columns where selected.
- Fixed bug where the UI buttons active state wasn't properly updated due to recent refactoring of that logic.

## 4.2.7 - 2015-10-27

### Fixed
- Fixed bug where backspace/delete would remove all formats on the last paragraph character in WebKit/Blink.
- Fixed bug where backspace within a inline format element with a bogus caret container would move the caret.
- Fixed bug where backspace/delete on selected table cells wouldn't add an undo level.
- Fixed bug where script tags embedded within the editor could sometimes get a mce- prefix prepended to them
- Fixed bug where validate: false option could produce an error to be thrown from the Serialization step.
- Fixed bug where inline editing of a table as the root element could let the user delete that table.
- Fixed bug where inline editing of a table as the root element wouldn't properly handle enter key.
- Fixed bug where inline editing of a table as the root element would normalize the selection incorrectly.
- Fixed bug where inline editing of a list as the root element could let the user delete that list.
- Fixed bug where inline editing of a list as the root element could let the user split that list.
- Fixed bug where resize handles would be rendered on editable root elements such as table.

## 4.2.6 - 2015-09-28

### Added
- Added capability to set request headers when using XHRs.
- Added capability to upload local images automatically default delay is set to 30 seconds after editing images.
- Added commands ids mceEditImage, mceAchor and mceMedia to be avaiable from execCommand.
- Added Edge browser to saucelabs grunt task. Patch contributed by John-David Dalton.

### Fixed
- Fixed bug where blob uris not produced by tinymce would produce HTML invalid markup.
- Fixed bug where selection of contents of a nearly empty editor in Edge would sometimes fail.
- Fixed bug where color styles woudln't be retained on copy/paste in Blink/Webkit.
- Fixed bug where the table plugin would throw an error when inserting rows after a child table.
- Fixed bug where the template plugin wouldn't handle functions as variable replacements.
- Fixed bug where undo/redo sometimes wouldn't work properly when applying formatting collapsed ranges.
- Fixed bug where shift+delete wouldn't do a cut operation on Blink/WebKit.
- Fixed bug where cut action wouldn't properly store the before selection bookmark for the undo level.
- Fixed bug where backspace in side an empty list element on IE would loose editor focus.
- Fixed bug where the save plugin wouldn't enable the buttons when a change occurred.
- Fixed bug where Edge wouldn't initialize the editor if a document.domain was specified.
- Fixed bug where enter key before nested images would sometimes not properly expand the previous block.
- Fixed bug where the inline toolbars wouldn't get properly hidden when blurring the editor instance.
- Fixed bug where Edge would paste Chinese characters on some Windows 10 installations.
- Fixed bug where IME would loose focus on IE 11 due to the double trailing br bug fix.
- Fixed bug where the proxy url in imagetools was incorrect. Patch contributed by Wong Ho Wang.

## 4.2.5 - 2015-08-31

### Added
- Added fullscreen capability to embedded youtube and vimeo videos.

### Fixed
- Fixed bug where the uploadImages call didn't work on IE 10.
- Fixed bug where image place holders would be uploaded by uploadImages call.
- Fixed bug where images marked with bogus would be uploaded by the uploadImages call.
- Fixed bug where multiple calls to uploadImages would result in decreased performance.
- Fixed bug where pagebreaks were editable to imagetools patch contributed by Rasmus Wallin.
- Fixed bug where the element path could cause too much recursion exception.
- Fixed bug for domains containing ".min". Patch contributed by Loïc Février.
- Fixed so validation of external links to accept a number after www. Patch contributed by Victor Carvalho.
- Fixed so the charmap is exposed though execCommand. Patch contributed by Matthew Will.
- Fixed so that the image uploads are concurrent for improved performance.
- Fixed various grammar problems in inline documentation. Patches provided by nikolas.

## 4.2.4 - 2015-08-17

### Added
- Added picture as a valid element to the HTML 5 schema. Patch contributed by Adam Taylor.

### Fixed
- Fixed bug where contents would be duplicated on drag/drop within the same editor.
- Fixed bug where floating/alignment of images on Edge wouldn't work properly.
- Fixed bug where it wasn't possible to drag images on IE 11.
- Fixed bug where image selection on Edge would sometimes fail.
- Fixed bug where contextual toolbars icons wasn't rendered properly when using the toolbar_items_size.
- Fixed bug where searchreplace dialog doesn't get prefilled with the selected text.
- Fixed bug where fragmented matches wouldn't get properly replaced by the searchreplace plugin.
- Fixed bug where enter key wouldn't place the caret if was after a trailing space within an inline element.
- Fixed bug where the autolink plugin could produce multiple links for the same text on Gecko.
- Fixed bug where EditorUpload could sometimes throw an exception if the blob wasn't found.
- Fixed xss issues with media plugin not properly filtering out some script attributes.

## 4.2.3 - 2015-07-30

### Fixed
- Fixed bug where image selection wasn't possible on Edge due to incompatible setBaseAndExtend API.
- Fixed bug where image blobs urls where not properly destroyed by the imagetools plugin.
- Fixed bug where keyboard shortcuts wasn't working correctly on IE 8.
- Fixed skin issue where the borders of panels where not visible on IE 8.

## 4.2.2 - 2015-07-22

### Fixed
- Fixed bug where float panels were not being hidden on inline editor blur when fixed_toolbar_container config option was in use.
- Fixed bug where combobox states wasn't properly updated if contents where updated without keyboard.
- Fixed bug where pasting into textbox or combobox would move the caret to the end of text.
- Fixed bug where removal of bogus span elements before block elements would remove whitespace between nodes.
- Fixed bug where repositioning of inline toolbars where async and producing errors if the editor was removed from DOM to early. Patch by iseulde.
- Fixed bug where element path wasn't working correctly. Patch contributed by iseulde.
- Fixed bug where menus wasn't rendered correctly when custom images where added to a menu. Patch contributed by Naim Hammadi.

## 4.2.1 - 2015-06-29

### Fixed
- Fixed bug where back/forward buttons in the browser would render blob images as broken images.
- Fixed bug where Firefox would throw regexp to big error when replacing huge base64 chunks.
- Fixed bug rendering issues with resize and context toolbars not being placed properly until next animation frame.
- Fixed bug where the rendering of the image while cropping would some times not be centered correctly.
- Fixed bug where listbox items with submenus would me selected as active.
- Fixed bug where context menu where throwing an error when rendering.
- Fixed bug where resize both option wasn't working due to resent addClass API change. Patch contributed by Jogai.
- Fixed bug where a hideAll call for container rendered inline toolbars would throw an error.
- Fixed bug where onclick event handler on combobox could cause issues if element.id was a function by some polluting libraries.
- Fixed bug where listboxes wouldn't get proper selected sub menu item when using link_list or image_list.
- Fixed so the UI controls are as wide as 4.1.x to avoid wrapping controls in toolbars.
- Fixed so the imagetools dialog is adaptive for smaller screen sizes.

## 4.2.0 - 2015-06-25

### Added
- Added new flat default skin to make the UI more modern.
- Added new imagetools plugin, lets you crop/resize and apply filters to images.
- Added new contextual toolbars support to the API lets you add floating toolbars for specific CSS selectors.
- Added new promise feature fill as tinymce.util.Promise.
- Added new built in image upload feature lets you upload any base64 encoded image within the editor as files.

### Fixed
- Fixed bug where resize handles would appear in the right position in the wrong editor when switching between resizable content in different inline editors.
- Fixed bug where tables would not be inserted in inline mode due to previous float panel fix.
- Fixed bug where floating panels would remain open when focus was lost on inline editors.
- Fixed bug where cut command on Chrome would thrown a browser security exception.
- Fixed bug where IE 11 sometimes would report an incorrect size for images in the image dialog.
- Fixed bug where it wasn't possible to remove inline formatting at the end of block elements.
- Fixed bug where it wasn't possible to delete table cell contents when cell selection was vertical.
- Fixed bug where table cell wasn't emptied from block elements if delete/backspace where pressed in empty cell.
- Fixed bug where cmd+shift+arrow didn't work correctly on Firefox mac when selecting to start/end of line.
- Fixed bug where removal of bogus elements would sometimes remove whitespace between nodes.
- Fixed bug where the resize handles wasn't updated when the main window was resized.
- Fixed so script elements gets removed by default to prevent possible XSS issues in default config implementations.
- Fixed so the UI doesn't need manual reflows when using non native layout managers.
- Fixed so base64 encoded images doesn't slow down the editor on modern browsers while editing.
- Fixed so all UI elements uses touch events to improve mobile device support.
- Removed the touch click quirks patch for iOS since it did more harm than good.
- Removed the non proportional resize handles since. Unproportional resize can still be done by holding the shift key.

## 4.1.10 - 2015-05-05

### Fixed
- Fixed bug where plugins loaded with compat3x would sometimes throw errors when loading using the jQuery version.
- Fixed bug where extra empty paragraphs would get deleted in WebKit/Blink due to recent Quriks fix.
- Fixed bug where the editor wouldn't work properly on IE 12 due to some required browser sniffing.
- Fixed bug where formatting shortcut keys where interfering with Mac OS X screenshot keys.
- Fixed bug where the caret wouldn't move to the next/previous line boundary on Cmd+Left/Right on Gecko.
- Fixed bug where it wasn't possible to remove formats from very specific nested contents.
- Fixed bug where undo levels wasn't produced when typing letters using the shift or alt+ctrl modifiers.
- Fixed bug where the dirty state wasn't properly updated when typing using the shift or alt+ctrl modifiers.
- Fixed bug where an error would be thrown if an autofocused editor was destroyed quickly after its initialization. Patch provided by thorn0.
- Fixed issue with dirty state not being properly updated on redo operation.
- Fixed issue with entity decoder not handling incorrectly written numeric entities.
- Fixed issue where some PI element values wouldn't be properly encoded.

## 4.1.9 - 2015-03-10

### Fixed
- Fixed bug where indentation wouldn't work properly for non list elements.
- Fixed bug with image plugin not pulling the image dimensions out correctly if a custom document_base_url was used.
- Fixed bug where ctrl+alt+[1-9] would conflict with the AltGr+[1-9] on Windows. New shortcuts is ctrl+shift+[1-9].
- Fixed bug with removing formatting on nodes in inline mode would sometimes include nodes outside the editor body.
- Fixed bug where extra nbsp:s would be inserted when you replaced a word surrounded by spaces using insertContent.
- Fixed bug with pasting from Google Docs would produce extra strong elements and line feeds.

## 4.1.8 - 2015-03-05

### Added
- Added new html5 sizes attribute to img elements used together with srcset.
- Added new elementpath option that makes it possible to disable the element path but keep the statusbar.
- Added new option table_style_by_css for the table plugin to set table styling with css rather than table attributes.
- Added new link_assume_external_targets option to prompt the user to prepend http:// prefix if the supplied link does not contain a protocol prefix.
- Added new image_prepend_url option to allow a custom base path/url to be added to images.
- Added new table_appearance_options option to make it possible to disable some options.
- Added new image_title option to make it possible to alter the title of the image, disabled by default.

### Fixed
- Fixed bug where selection starting from out side of the body wouldn't produce a proper selection range on IE 11.
- Fixed bug where pressing enter twice before a table moves the cursor in the table and causes a javascript error.
- Fixed bug where advanced image styles were not respected.
- Fixed bug where the less common Shift+Delete didn't produce a proper cut operation on WebKit browsers.
- Fixed bug where image/media size constrain logic would produce NaN when handling non number values.
- Fixed bug where internal classes where removed by the removeformat command.
- Fixed bug with creating links table cell contents with a specific selection would throw a exceptions on WebKit/Blink.
- Fixed bug where valid_classes option didn't work as expected according to docs. Patch provided by thorn0.
- Fixed bug where jQuery plugin would patch the internal methods multiple times. Patch provided by Drew Martin.
- Fixed bug where backspace key wouldn't delete the current selection of newly formatted content.
- Fixed bug where type over of inline formatting elements wouldn't properly keep the format on WebKit/Blink.
- Fixed bug where selection needed to be properly normalized on modern IE versions.
- Fixed bug where Command+Backspace didn't properly delete the whole line of text but the previous word.
- Fixed bug where UI active states wheren't properly updated on IE if you placed caret within the current range.
- Fixed bug where delete/backspace on WebKit/Blink would remove span elements created by the user.
- Fixed bug where delete/backspace would produce incorrect results when deleting between two text blocks with br elements.
- Fixed bug where captions where removed when pasting from MS Office.
- Fixed bug where lists plugin wouldn't properly remove fully selected nested lists.
- Fixed bug where the ttf font used for icons would throw an warning message on Gecko on Mac OS X.
- Fixed a bug where applying a color to text did not update the undo/redo history.
- Fixed so shy entities gets displayed when using the visualchars plugin.
- Fixed so removeformat removes ins/del by default since these might be used for strikethough.
- Fixed so multiple language packs can be loaded and added to the global I18n data structure.
- Fixed so transparent color selection gets treated as a normal color selection. Patch contributed by Alexander Hofbauer.
- Fixed so it's possible to disable autoresize_overflow_padding, autoresize_bottom_margin options by setting them to false.
- Fixed so the charmap plugin shows the description of the character in the dialog. Patch contributed by Jelle Hissink.
- Removed address from the default list of block formats since it tends to be missused.
- Fixed so the pre block format is called preformatted to make it more verbose.
- Fixed so it's possible to context scope translation strings this isn't needed most of the time.
- Fixed so the max length of the width/height input fields of the media dialog is 5 instead of 3.
- Fixed so drag/dropped contents gets properly processed by paste plugin since it's basically a paste. Patch contributed by Greg Fairbanks.
- Fixed so shortcut keys for headers is ctrl+alt+[1-9] instead of ctrl+[1-9] since these are for switching tabs in the browsers.
- Fixed so "u" doesn't get converted into a span element by the legacy input filter. Since this is now a valid HTML5 element.
- Fixed font families in order to provide appropriate web-safe fonts.

## 4.1.7 - 2014-11-27

### Added
- Added HTML5 schema support for srcset, source and picture. Patch contributed by mattheu.
- Added new cache_suffix setting to enable cache busting by producing unique urls.
- Added new paste_convert_word_fake_lists option to enable users to disable the fake lists convert logic.

### Fixed
- Fixed so advlist style changes adds undo levels for each change.
- Fixed bug where WebKit would sometimes produce an exception when the autolink plugin where looking for URLs.
- Fixed bug where IE 7 wouldn't be rendered properly due to aggressive css compression.
- Fixed bug where DomQuery wouldn't accept window as constructor element.
- Fixed bug where the color picker in 3.x dialogs wouldn't work properly. Patch contributed by Callidior.
- Fixed bug where the image plugin wouldn't respect the document_base_url.
- Fixed bug where the jQuery plugin would fail to append to elements named array prototype names.

## 4.1.6 - 2014-10-08

### Changed
- Replaced jake with grunt since it is more mainstream and has better plugin support.

### Fixed
- Fixed bug with clicking on the scrollbar of the iframe would cause a JS error to be thrown.
- Fixed bug where null would produce an exception if you passed it to selection.setRng.
- Fixed bug where Ctrl/Cmd+Tab would indent the current list item if you switched tabs in the browser.
- Fixed bug where pasting empty cells from Excel would result in a broken table.
- Fixed bug where it wasn't possible to switch back to default list style type.
- Fixed issue where the select all quirk fix would fire for other modifiers than Ctrl/Cmd combinations.


## 4.1.5 - 2014-09-09

### Fixed
- Fixed bug where sometimes the resize rectangles wouldn't properly render on images on WebKit/Blink.
- Fixed bug in list plugin where delete/backspace would merge empty LI elements in lists incorrectly.
- Fixed bug where empty list elements would result in empty LI elements without it's parent container.
- Fixed bug where backspace in empty caret formatted element could produce an type error exception of Gecko.
- Fixed bug where lists pasted from word with a custom start index above 9 wouldn't be properly handled.
- Fixed bug where tabfocus plugin would tab out of the editor instance even if the default action was prevented.
- Fixed bug where tabfocus wouldn't tab properly to other adjacent editor instances.
- Fixed bug where the DOMUtils setStyles wouldn't properly removed or update the data-mce-style attribute.
- Fixed bug where dialog select boxes would be placed incorrectly if document.body wasn't statically positioned.
- Fixed bug where pasting would sometimes scroll to the top of page if the user was using the autoresize plugin.
- Fixed bug where caret wouldn't be properly rendered by Chrome when clicking on the iframes documentElement.
- Fixed so custom images for menubutton/splitbutton can be provided. Patch contributed by Naim Hammadi.
- Fixed so the default action of windows closing can be prevented by blocking the default action of the close event.
- Fixed so nodeChange and focus of the editor isn't automatically performed when opening sub dialogs.

## 4.1.4 - 2014-08-21

### Added
- Added new media_filter_html option to media plugin that blocks any conditional comments, scripts etc within a video element.
- Added new content_security_policy option allows you to set custom policy for iframe contents. Patch contributed by Francois Chagnon.

### Fixed
- Fixed bug where activate/deactivate events wasn't firing properly when switching between editors.
- Fixed bug where placing the caret on iOS was difficult due to a WebKit bug with touch events.
- Fixed bug where the resize helper wouldn't render properly on older IE versions.
- Fixed bug where resizing images inside tables on older IE versions would sometimes fail depending mouse position.
- Fixed bug where editor.insertContent would produce an exception when inserting select/option elements.
- Fixed bug where extra empty paragraphs would be produced if block elements where inserted inside span elements.
- Fixed bug where the spellchecker menu item wouldn't be properly checked if spell checking was started before it was rendered.
- Fixed bug where the DomQuery filter function wouldn't remove non elements from collection.
- Fixed bug where document with custom document.domain wouldn't properly render the editor.
- Fixed bug where IE 8 would throw exception when trying to enter invalid color values into colorboxes.
- Fixed bug where undo manager could incorrectly add an extra undo level when custom resize handles was removed.
- Fixed bug where it wouldn't be possible to alter cell properties properly on table cells on IE 8.
- Fixed so the color picker button in table dialog isn't shown unless you include the colorpicker plugin or add your own custom color picker.
- Fixed so activate/deactivate events fire when windowManager opens a window since.
- Fixed so the table advtab options isn't separated by an underscore to normalize naming with image_advtab option.
- Fixed so the table cell dialog has proper padding when the advanced tab in disabled.

## 4.1.3 - 2014-07-29

### Added
- Added event binding logic to tinymce.util.XHR making it possible to override headers and settings before any request is made.

### Fixed
- Fixed bug where drag events wasn't fireing properly on older IE versions since the event handlers where bound to document.
- Fixed bug where drag/dropping contents within the editor on IE would force the contents into plain text mode even if it was internal content.
- Fixed bug where IE 7 wouldn't open menus properly due to a resize bug in the browser auto closing them immediately.
- Fixed bug where the DOMUtils getPos logic wouldn't produce a valid coordinate inside the body if the body was positioned non static.
- Fixed bug where the element path and format state wasn't properly updated if you had the wordcount plugin enabled.
- Fixed bug where a comment at the beginning of source would produce an exception in the formatter logic.
- Fixed bug where setAttrib/getAttrib on null would throw exception together with any hooked attributes like style.
- Fixed bug where table sizes wasn't properly retained when copy/pasting on WebKit/Blink.
- Fixed bug where WebKit/Blink would produce colors in RGB format instead of the forced HEX format when deleting contents.
- Fixed bug where the width attribute wasn't updated on tables if you changed the size inside the table dialog.
- Fixed bug where control selection wasn't properly handled when the caret was placed directly after an image.
- Fixed bug where selecting the contents of table cells using the selection.select method wouldn't place the caret properly.
- Fixed bug where the selection state for images wasn't removed when placing the caret right after an image on WebKit/Blink.
- Fixed bug where all events wasn't properly unbound when and editor instance was removed or destroyed by some external innerHTML call.
- Fixed bug where it wasn't possible or very hard to select images on iOS when the onscreen keyboard was visible.
- Fixed so auto_focus can take a boolean argument this will auto focus the last initialized editor might be useful for single inits.
- Fixed so word auto detect lists logic works better for faked lists that doesn't have specific markup.
- Fixed so nodeChange gets fired on mouseup as it used to before 4.1.1 we optimized that event to fire less often.

### Removed
- Removed the finish menu item from spellchecker menu since it's redundant you can stop spellchecking by toggling menu item or button.

## 4.1.2 - 2014-07-15

### Added
- Added offset/grep to DomQuery class works basically the same as it's jQuery equivalent.

### Fixed
- Fixed bug where backspace/delete or setContent with an empty string would remove header data when using the fullpage plugin.
- Fixed bug where tinymce.remove with a selector not matching any editors would remove all editors.
- Fixed bug where resizing of the editor didn't work since the theme was calling setStyles instead of setStyle.
- Fixed bug where IE 7 would fail to append html fragments to iframe document when using DomQuery.
- Fixed bug where the getStyle DOMUtils method would produce an exception if it was called with null as it's element.
- Fixed bug where the paste plugin would remove the element if the none of the paste_webkit_styles rules matched the current style.
- Fixed bug where contextmenu table items wouldn't work properly on IE since it would some times fire an incorrect selection change.
- Fixed bug where the padding/border values wasn't used in the size calculation for the body size when using autoresize. Patch contributed by Matt Whelan.
- Fixed bug where conditional word comments wouldn't be properly removed when pasting plain text.
- Fixed bug where resizing would sometime fail on IE 11 when the mouseup occurred inside the resizable element.
- Fixed so the iframe gets initialized without any inline event handlers for better CSP support. Patch contributed by Matt Whelan.
- Fixed so the tinymce.dom.Sizzle is the latest version of sizzle this resolves the document context bug.

## 4.1.1 - 2014-07-08

### Fixed
- Fixed bug where pasting plain text on some WebKit versions would result in an empty line.
- Fixed bug where resizing images inside tables on IE 11 wouldn't work properly.
- Fixed bug where IE 11 would sometimes throw "Invalid argument" exception when editor contents was set to an empty string.
- Fixed bug where document.activeElement would throw exceptions on IE 9 when that element was hidden or removed from dom.
- Fixed bug where WebKit/Blink sometimes produced br elements with the Apple-interchange-newline class.
- Fixed bug where table cell selection wasn't properly removed when copy/pasting table cells.
- Fixed bug where pasting nested list items from Word wouldn't produce proper semantic nested lists.
- Fixed bug where right clicking using the contextmenu plugin on WebKit/Blink on Mac OS X would select the target current word or line.
- Fixed bug where it wasn't possible to alter table cell properties on IE 8 using the context menu.
- Fixed bug where the resize helper wouldn't be correctly positioned on older IE versions.
- Fixed bug where fullpage plugin would produce an error if you didn't specify a doctype encoding.
- Fixed bug where anchor plugin would get the name/id of the current element even if it wasn't anchor element.
- Fixed bug where visual aids for tables wouldn't be properly disabled when changing the border size.
- Fixed bug where some control selection events wasn't properly fired on older IE versions.
- Fixed bug where table cell selection on older IE versions would prevent resizing of images.
- Fixed bug with paste_data_images paste option not working properly on modern IE versions.
- Fixed bug where custom elements with underscores in the name wasn't properly parsed/serialized.
- Fixed bug where applying inline formats to nested list elements would produce an incorrect formatting result.
- Fixed so it's possible to hide items from elements path by using preventDefault/stopPropagation.
- Fixed so inline mode toolbar gets rendered right aligned if the editable element positioned to the documents right edge.
- Fixed so empty inline elements inside empty block elements doesn't get removed if configured to be kept intact.
- Fixed so DomQuery parentsUntil/prevUntil/nextUntil supports selectors/elements/filters etc.
- Fixed so legacyoutput plugin overrides fontselect and fontsizeselect controls and handles font elements properly.

## 4.1.0 - 2014-06-18

### Added
- Added new file_picker_callback option to replace the old file_browser_callback the latter will still work though.
- Added new custom colors to textcolor plugin will be displayed if a color picker is provided also shows the latest colors.
- Added new color_picker_callback option to enable you to add custom color pickers to the editor.
- Added new advanced tabs to table/cell/row dialogs to enable you to select colors for border/background.
- Added new colorpicker plugin that lets you select colors from a hsv color picker.
- Added new tinymce.util.Color class to handle color parsing and converting.
- Added new colorpicker UI widget element lets you add a hsv color picker to any form/window.
- Added new textpattern plugin that allows you to use markdown like text patterns to format contents.
- Added new resize helper element that shows the current width & height while resizing.
- Added new "once" method to Editor and EventDispatcher enables since callback execution events.
- Added new jQuery like class under tinymce.dom.DomQuery it's exposed on editor instances (editor.$) and globally under (tinymce.$).

### Fixed
- Fixed so the default resize method for images are proportional shift/ctrl can be used to make an unproportional size.
- Fixed bug where the image_dimensions option of the image plugin would cause exceptions when it tried to update the size.
- Fixed bug where table cell dialog class field wasn't properly updated when editing an a table cell with an existing class.
- Fixed bug where Safari on Mac would produce webkit-fake-url for pasted images so these are now removed.
- Fixed bug where the nodeChange event would get fired before the selection was changed when clicking inside the current selection range.
- Fixed bug where valid_classes option would cause exception when it removed internal prefixed classes like mce-item-.
- Fixed bug where backspace would cause navigation in IE 8 on an inline element and after a caret formatting was applied.
- Fixed so placeholder images produced by the media plugin gets selected when inserted/edited.
- Fixed so it's possible to drag in images when the paste_data_images option is enabled. Might be useful for mail clients.
- Fixed so images doesn't get a width/height applied if the image_dimensions option is set to false useful for responsive contents.
- Fixed so it's possible to pass in an optional arguments object for the nodeChanged function to be passed to all nodechange event listeners.
- Fixed bug where media plugin embed code didn't update correctly.
