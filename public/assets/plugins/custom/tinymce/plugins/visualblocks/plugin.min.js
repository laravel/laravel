/**
 * Copyright (c) Tiny Technologies, Inc. All rights reserved.
 * Licensed under the LGPL or a commercial license.
 * For LGPL see License.txt in the project root for license information.
 * For commercial licenses see https://www.tiny.cloud/
 *
 * Version: 5.8.1 (2021-05-20)
 */
!function(){"use strict";var t=tinymce.util.Tools.resolve("tinymce.PluginManager"),r=function(t,o,e){var n,i;t.dom.toggleClass(t.getBody(),"mce-visualblocks"),e.set(!e.get()),n=t,i=e.get(),n.fire("VisualBlocks",{state:i})},f=function(e,n){return function(o){o.setActive(n.get());var t=function(t){return o.setActive(t.state)};return e.on("VisualBlocks",t),function(){return e.off("VisualBlocks",t)}}};t.add("visualblocks",function(t,o){var e,n,i,s,c,u,l,a=(e=!1,{get:function(){return e},set:function(t){e=t}});i=a,(n=t).addCommand("mceVisualBlocks",function(){r(n,0,i)}),c=a,(s=t).ui.registry.addToggleButton("visualblocks",{icon:"visualblocks",tooltip:"Show blocks",onAction:function(){return s.execCommand("mceVisualBlocks")},onSetup:f(s,c)}),s.ui.registry.addToggleMenuItem("visualblocks",{text:"Show blocks",icon:"visualblocks",onAction:function(){return s.execCommand("mceVisualBlocks")},onSetup:f(s,c)}),l=a,(u=t).on("PreviewFormats AfterPreviewFormats",function(t){l.get()&&u.dom.toggleClass(u.getBody(),"mce-visualblocks","afterpreviewformats"===t.type)}),u.on("init",function(){u.getParam("visualblocks_default_state",!1,"boolean")&&r(u,0,l)})})}();