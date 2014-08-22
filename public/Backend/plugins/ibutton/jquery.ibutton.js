/*!
 * iButton jQuery Plug-in
 *
 * Copyright 2011 Giva, Inc. (http://www.givainc.com/labs/) 
 * 
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 * 
 *  http://www.apache.org/licenses/LICENSE-2.0
 * 
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 *
 * Date: 2011-07-26
 * Rev:  1.0.03
 *
 *
 * MoonCake v1.3.1 - iButton Plugin JS
 *
 * This file is part of MoonCake, an Admin template build for sale at ThemeForest.
 * For questions, suggestions or support request, please mail me at maimairel@yahoo.com
 *
 * Development Started:
 * July 28, 2012
 * Last Update:
 * December 07, 2012
 *
 * This file is a modification of iButton jQuery Plug-in from Giva-labs (http://www.givainc.com/labs/ibutton_jquery_plugin.htm)
 *
 * This plugin has been updated on v1.0.1 of MoonCake, with the following changes:
 * - Removed the 'return false' on the document mouseup, mousedown event as this might affect touch events globaly
 * - Moved the widths calculation to a custom local function that also will be called when calling 'repaint()'
 *
 */

;(function ($) {
    // set default options
    $.iButton = {
        version: "1.0.03",
        setDefaults: function (options) {
            $.extend(defaults, options);
        }
    };
    $.fn.iButton = function (options) {
        var method = typeof arguments[0] == "string" && arguments[0];
        var args = method && Array.prototype.slice.call(arguments, 1) || arguments;
        // get a reference to the first iButton found
        var self = (this.length == 0) ? null : $.data(this[0], "iButton");

        // if a method is supplied, execute it for non-empty results
        if (self && method && this.length) {

            // if request a copy of the object, return it			
            if (method.toLowerCase() == "object") return self;
            // if method is defined, run it and return either it's results or the chain
            else if (self[method]) {
                // define a result variable to return to the jQuery chain
                var result;
                this.each(function (i) {
                    // apply the method to the current element
                    var r = $.data(this, "iButton")[method].apply(self, args);
                    // if first iteration we need to check if we're done processing or need to add it to the jquery chain
                    if (i == 0 && r) {
                        // if this is a jQuery item, we need to store them in a collection
                        if ( !! r.jquery) {
                            result = $([]).add(r);
                            // otherwise, just store the result and stop executing
                        } else {
                            result = r;
                            // since we're a non-jQuery item, just cancel processing further items
                            return false;
                        }
                        // keep adding jQuery objects to the results
                    } else if ( !! r && !! r.jquery) {
                        result = result.add(r);
                    }
                });

                // return either the results (which could be a jQuery object) or the original chain
                return result || this;
                // everything else, return the chain
            } else return this;
            // initializing request (only do if iButton not already initialized)
        } else {
            // create a new iButton for each object found
            return this.each(function () {
                new iButton(this, options);
            });
        };
    };

    $(function () {
        $('[data-provide="ibutton"]').each(function () {
            var $el = $(this);
            $el.iButton($el.data());
        });
    });

    // count instances	
    var counter = 0;
    // detect iPhone
    $.browser.iphone = (navigator.userAgent.toLowerCase().indexOf("iphone") > -1);

    var iButton = function (input, options) {
        var self = this,
            $input = $(input),
            id = ++counter,
            disabled = false,
            width = {}, mouse = {
                dragging: false,
                clicked: null
            }, dragStart = {
                position: null,
                offset: null,
                time: null
            }
            // make a copy of the options and use the HTML5 data attribute if provided
            ,
            options = $.extend({}, defaults, options, $input.data())
            // set valid field types
            ,
            allow = ":checkbox, :radio";

        // only do for checkboxes buttons, if matches inside that node
        if (!$input.is(allow)) return $input.find(allow).iButton(options);
        // if iButton already exists, stop processing
        else if ($.data($input[0], "iButton")) return;

        // store a reference to this marquee
        $.data($input[0], "iButton", self);

        // if the element's parent is a label, then we need to prevent click events
        if($input.parent().is('label')) {
            $input.parent().bind('click.iButton', function(e){
                e.preventDefault();
            });
        }

        // toggles the state of a button (or can turn on/off)
        this.toggle = function (t) {
            var toggle = (arguments.length > 0) ? t : !$input[0].checked;
            $input.attr("checked", toggle).trigger("change");
        };

        // disable/enable the control
        this.disable = function (t) {
            var toggle = (arguments.length > 0) ? t : !disabled;
            // mark the control disabled
            disabled = toggle;
            // mark the input disabled
            $input.attr("disabled", toggle);
            // set the diabled styles
            $container[toggle ? "addClass" : "removeClass"](options.classDisabled);
            // run callback
            if ($.isFunction(options.disable)) options.disable.apply(self, [disabled, $input, options]);
        };

        // repaint the button
        this.repaint = function () {
            calculateWidths();
            positionHandle();
        };

        // this will destroy the iButton style
        this.destroy = function () {
            // remove behaviors
            $([$input[0], $container[0]]).unbind(".iButton");
            if($input.parent().is('label')) $input.parent().unbind('.iButton');
            $(document).unbind(".iButton_" + id);
            // move the checkbox to it's original location
            $container.after($input).remove();
            // kill the reference
            $.data($input[0], "iButton", null);
            // run callback
            if ($.isFunction(options.destroy)) options.destroy.apply(self, [$input, options]);
        };

        $input
        // create the wrapper code
        .wrap('<div class="' + $.trim(options.classContainer + ' ' + options.className) + '" />')
        .after(
            '<div class="' + options.classHandle + '">' + 
                '<div class="' + options.classHandleInner + '" />' + 
            '</div>' + 
            '<div class="' + options.classLabelOff + '">' + 
                '<span><label>' + options.labelOff + '</label></span>' + 
            '</div>' + 
            '<div class="' + options.classLabelOn + '">' + 
                '<span><label>' + options.labelOn + '</label></span>' + 
            '</div>'
        );

        var $container = $input.parent(),
            $handle = $input.siblings("." + options.classHandle),
            $offlabel = $input.siblings("." + options.classLabelOff),
            $offspan = $offlabel.children("span"),
            $onlabel = $input.siblings("." + options.classLabelOn),
            $onspan = $onlabel.children("span"), 
            handleRight;

        var calculateWidths = function() {
            // get the width of the labels
            width.onspan = $onspan.outerWidth();
            width.offspan = $offspan.outerWidth();

            // set the handle width based on the smallest label width
            width.handle = Math.min(width.onspan, width.offspan);
            $handle.css("width", width.handle);

            // set the container's width based on the total widths of the labels + the largest border width of the labels
            width.container = width.onspan + width.offspan;
            $container.css("width", width.container + 
                Math.max(
                    parseInt($offlabel.css('border-left-width'), 10) + parseInt($offlabel.css('border-right-width'), 10), 
                    parseInt($onlabel.css('border-left-width'), 10) + parseInt($onlabel.css('border-right-width'), 10)
                ) );

            // adjust the off label to match the new container size
            $offlabel.css("width", width.container);

            handleRight = width.container - width.handle;
        };

        // Calculate the button element widths
        calculateWidths();

        var positionHandle = function (animate) {
            var checked = $input[0].checked,
                x = (checked) ? handleRight : 0,
                animate = (arguments.length > 0) ? arguments[0] : true;

            if (animate && options.enableFx) {
                $handle.stop().animate({
                    left: x
                }, options.duration, options.easing);
                $onlabel.stop().animate({
                    width: x
                }, options.duration, options.easing);
                $onspan.stop().animate({
                    marginLeft: x - handleRight
                }, options.duration, options.easing);
                $offspan.stop().animate({
                    marginRight: -x
                }, options.duration, options.easing);
            } else {
                $handle.css("left", x);
                $onlabel.css("width", x);
                $onspan.css("marginLeft", x - handleRight);
                $offspan.css("marginRight", - x);
            }
        };

        // place the buttons in their default location	
        positionHandle(false);

        var getDragPos = function (e) {
            return e.pageX || ((e.originalEvent.changedTouches) ? e.originalEvent.changedTouches[0].pageX : 0);
        };

        // monitor mouse clicks in the container
        $container.bind("mousedown.iButton touchstart.iButton", function (e) {
            // abort if disabled or allow clicking the input to toggle the status (if input is visible)
            if ($(e.target).is(allow) || disabled || (!options.allowRadioUncheck && $input.is(":radio:checked"))) return;

            e.preventDefault();
            mouse.clicked = $handle;
            dragStart.position = getDragPos(e);
            dragStart.offset = dragStart.position - (parseInt($handle.css("left"), 10) || 0);
            dragStart.time = (new Date()).getTime();
        });

        // make sure dragging support is enabled		
        if (options.enableDrag) {
            // monitor mouse movement on the page
            $(document).bind("mousemove.iButton_" + id + " touchmove.iButton_" + id, function (e) {
                // if we haven't clicked on the container, cancel event
                if (mouse.clicked != $handle) {
                    return;
                }
                e.preventDefault();

                var x = getDragPos(e);
                if (x != dragStart.offset) {
                    mouse.dragging = true;
                    $container.addClass(options.classHandleActive);
                }

                // make sure number is between 0 and 1			
                var pct = Math.min(1, Math.max(0, (x - dragStart.offset) / handleRight));

                $handle.css("left", pct * handleRight);
                $onlabel.css("width", pct * handleRight);
                $offspan.css("marginRight", - pct * handleRight);
                $onspan.css("marginLeft", - (1 - pct) * handleRight);
            });
        }

        // monitor when the mouse button is released
        $(document).bind("mouseup.iButton_" + id + " touchend.iButton_" + id, function (e) {
            if (mouse.clicked != $handle) {
                return;
            }
            e.preventDefault();

            // track if the value has changed			
            var changed = true;

            // if not dragging or click time under a certain millisecond, then just toggle			
            if (!mouse.dragging || (((new Date()).getTime() - dragStart.time) < options.clickOffset)) {
                var checked = $input[0].checked;
                $input.attr("checked", !checked);

                // run callback
                if ($.isFunction(options.click)) options.click.apply(self, [!checked, $input, options]);
            } else {
                var x = getDragPos(e);

                var pct = (x - dragStart.offset) / handleRight;
                var checked = (pct >= 0.5);

                // if the value is the same, don't run change event
                if ($input[0].checked == checked) changed = false;

                $input.attr("checked", checked);
            }

            // remove the active handler class			
            $container.removeClass(options.classHandleActive);
            mouse.clicked = null;
            mouse.dragging = null;
            // run any change event for the element
            if (changed) $input.trigger("change");
            // if the value didn't change, just reset the handle
            else positionHandle();
        });

        // animate when we get a change event
        $input.bind("change.iButton", function () {
            // move handle
            positionHandle();

            // if a radio element, then we must repaint the other elements in it's group to show them as not selected
            if ($input.is(":radio")) {
                var el = $input[0];

                // try to use the DOM to get the grouped elements, but if not in a form get by name attr
                var $radio = $(el.form ? el.form[el.name] : ":radio[name=" + el.name + "]");

                // repaint the radio elements that are not checked	
                $radio.filter(":not(:checked)").iButton("repaint");
            }

            // run callback
            if ($.isFunction(options.change)) options.change.apply(self, [$input, options]);
        })
        // if the element has focus, we need to highlight the container
        .bind("focus.iButton", function () {
            $container.addClass(options.classFocus);
        })
        // if the element has focus, we need to highlight the container
        .bind("blur.iButton", function () {
            $container.removeClass(options.classFocus);
        });

        // if a click event is registered, we must register on the checkbox so it's fired if triggered on the checkbox itself
        if ($.isFunction(options.click)) {
            $input.bind("click.iButton", function () {
                options.click.apply(self, [$input[0].checked, $input, options]);
            });
        }

        // if the field is disabled, mark it as such
        if ($input.is(":disabled")) this.disable(true);

        // special behaviors for IE    
        if ($.browser.msie) {
            // disable text selection in IE, other browsers are controlled via CSS
            $container.find("*").andSelf().attr("unselectable", "on");
            // IE needs to register to the "click" event to make changes immediately (the change event only occurs on blur)
            $input.bind("click.iButton", function () {
                $input.triggerHandler("change.iButton");
            });
        }

        // run the init callback
        if ($.isFunction(options.init)) options.init.apply(self, [$input, options]);
    };

    var defaults = {
        duration: 200 // the speed of the animation
        ,
        easing: "swing" // the easing animation to use
        ,
        labelOn: "ON" // the text to show when toggled on
        ,
        labelOff: "OFF" // the text to show when toggled off
        ,
        enableDrag: true // determines if we allow dragging
        ,
        enableFx: true // determines if we show animation
        ,
        allowRadioUncheck: false // determine if a radio button should be able to be unchecked
        ,
        clickOffset: 120 // if millseconds between a mousedown & mouseup event this value, then considered a mouse click

        // define the class statements
        ,
        className: "",
        classContainer: "ibutton-container",
        classDisabled: "ibutton-disabled",
        classFocus: "ibutton-focus",
        classLabelOn: "ibutton-label-on",
        classLabelOff: "ibutton-label-off",
        classHandle: "ibutton-handle",
        classHandleInner: "ibutton-handle-inner",
        classHandleActive: "ibutton-active-handle",

        // event handlers
        init: null // callback that occurs when a iButton is initialized
        ,
        change: null // callback that occurs when the button state is changed
        ,
        click: null // callback that occurs when the button is clicked
        ,
        disable: null // callback that occurs when the button is disabled/enabled
        ,
        destroy: null // callback that occurs when the button is destroyed
    };

})(jQuery);
