/**
 * Copyright (c) Tiny Technologies, Inc. All rights reserved.
 * Licensed under the LGPL or a commercial license.
 * For LGPL see License.txt in the project root for license information.
 * For commercial licenses see https://www.tiny.cloud/
 *
 * Version: 5.8.1 (2021-05-20)
 */
!function(){"use strict";var n=tinymce.util.Tools.resolve("tinymce.PluginManager"),e=tinymce.util.Tools.resolve("tinymce.Env");n.add("print",function(n){var t,i;(t=n).addCommand("mcePrint",function(){e.browser.isIE()?t.getDoc().execCommand("print",!1,null):t.getWin().print()}),(i=n).ui.registry.addButton("print",{icon:"print",tooltip:"Print",onAction:function(){return i.execCommand("mcePrint")}}),i.ui.registry.addMenuItem("print",{text:"Print...",icon:"print",onAction:function(){return i.execCommand("mcePrint")}}),n.addShortcut("Meta+P","","mcePrint")})}();