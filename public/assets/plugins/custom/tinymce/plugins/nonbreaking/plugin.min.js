/**
 * Copyright (c) Tiny Technologies, Inc. All rights reserved.
 * Licensed under the LGPL or a commercial license.
 * For LGPL see License.txt in the project root for license information.
 * For commercial licenses see https://www.tiny.cloud/
 *
 * Version: 5.8.1 (2021-05-20)
 */
!function(){"use strict";var n=tinymce.util.Tools.resolve("tinymce.PluginManager"),i=function(n,e){for(var a="",o=0;o<e;o++)a+=n;return a},r=function(n,e){var a,o=n.getParam("nonbreaking_wrap",!0,"boolean")||n.plugins.visualchars?'<span class="'+((a=n).plugins.visualchars&&a.plugins.visualchars.isEnabled()?"mce-nbsp-wrap mce-nbsp":"mce-nbsp-wrap")+'" contenteditable="false">'+i("&nbsp;",e)+"</span>":i("&nbsp;",e);n.undoManager.transact(function(){return n.insertContent(o)})},c=tinymce.util.Tools.resolve("tinymce.util.VK");n.add("nonbreaking",function(n){var e,a,o,i,t;(e=n).addCommand("mceNonBreaking",function(){r(e,1)}),(a=n).ui.registry.addButton("nonbreaking",{icon:"non-breaking",tooltip:"Nonbreaking space",onAction:function(){return a.execCommand("mceNonBreaking")}}),a.ui.registry.addMenuItem("nonbreaking",{icon:"non-breaking",text:"Nonbreaking space",onAction:function(){return a.execCommand("mceNonBreaking")}}),0<(t="boolean"==typeof(i=(o=n).getParam("nonbreaking_force_tab",0))?!0===i?3:0:i)&&o.on("keydown",function(n){if(n.keyCode===c.TAB&&!n.isDefaultPrevented()){if(n.shiftKey)return;n.preventDefault(),n.stopImmediatePropagation(),r(o,t)}})})}();