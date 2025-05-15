/**
 * Copyright (c) Tiny Technologies, Inc. All rights reserved.
 * Licensed under the LGPL or a commercial license.
 * For LGPL see License.txt in the project root for license information.
 * For commercial licenses see https://www.tiny.cloud/
 *
 * Version: 5.8.1 (2021-05-20)
 */
!function(){"use strict";var e=tinymce.util.Tools.resolve("tinymce.PluginManager"),o=function(o){var e=o.getContent({source_view:!0});o.windowManager.open({title:"Source Code",size:"large",body:{type:"panel",items:[{type:"textarea",name:"code"}]},buttons:[{type:"cancel",name:"cancel",text:"Cancel"},{type:"submit",name:"save",text:"Save",primary:!0}],initialData:{code:e},onSubmit:function(e){var t,n;t=o,n=e.getData().code,t.focus(),t.undoManager.transact(function(){t.setContent(n)}),t.selection.setCursorLocation(),t.nodeChanged(),e.close()}})};e.add("code",function(e){var t,n;return(t=e).addCommand("mceCodeEditor",function(){o(t)}),(n=e).ui.registry.addButton("code",{icon:"sourcecode",tooltip:"Source code",onAction:function(){return o(n)}}),n.ui.registry.addMenuItem("code",{icon:"sourcecode",text:"Source code",onAction:function(){return o(n)}}),{}})}();