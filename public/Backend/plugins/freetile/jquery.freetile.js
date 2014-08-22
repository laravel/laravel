//  Freetile.js
//  v0.2.11
//
//  A dynamic layout plugin for jQuery.
//
//  Copyright (c) 2010-2012, Ioannis (Yannis) Chatzikonstantinou, All rights reserved.
//  http://www.yconst.com
//  http://www.volatileprototypes.com
// 
//  Redistribution and use in source and binary forms, with or without modification, 
//  are permitted provided that the following conditions are met:
//      - Redistributions of source code must retain the above copyright 
//  notice, this list of conditions and the following disclaimer.
//      - Redistributions in binary form must reproduce the above copyright 
//  notice, this list of conditions and the following disclaimer in the documentation 
//  and/or other materials provided with the distribution.
//  
//  THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS IS" AND ANY 
//  EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE IMPLIED 
//  WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE ARE DISCLAIMED. 
//  IN NO EVENT SHALL THE COPYRIGHT HOLDER OR CONTRIBUTORS BE LIABLE FOR ANY DIRECT, 
//  INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING, BUT 
//  NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES; LOSS OF USE, DATA, OR 
//  PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND ON ANY THEORY OF LIABILITY, 
//  WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE) 
//  ARISING IN ANY WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY 
//  OF SUCH DAMAGE.

(function( $ ){
    
    "use strict";

    //
    // Entry Point
    // _________________________________________________________
    
    $.fn.freetile = function( method ) 
    {
        // Method calling logic
        if ( typeof Freetile[ method ] === 'function' ) 
        {
          return Freetile[ method ].apply( this, Array.prototype.slice.call( arguments, 1 ));
        } 
        else if ( typeof method === 'object' || ! method ) 
        {
          return Freetile.init.apply( this, arguments );
        } 
        else 
        {
          $.error( 'Method ' +  method + ' does not exist on jQuery.Freetile' );
        }
        
        return this;
    };
    
    var Freetile = 
    {
        //
        // "Public" Methods
        // _________________________________________________________
        
        // Method 1.
        // Smart and generic method that selects between
        // initialize, re-layout or append.
        init : function(options) 
        {
            var container = this,
                o = Freetile.setupOptions(container, options),
                c = Freetile.newContent(o.contentToAppend);
            
            // Setup container bindings for resize and custom events
            if (!o.tiled) Freetile.setupContainerBindings(container, o);
            
            // If there is content to append and container has been already
            // tiled, continue in append mode.
            if (o.tiled && c) 
            {
                container.append(c);
                c.filter(o.selector || '*').imagesLoaded(function() 
                {
                    Freetile.positionAll(container, o);
                });
            // Otherwise continue by first just positioning the elements
            // and then doing a re-layout if any images are not yet loaded.
            } 
            else 
            {
                container.imagesLoaded(function() 
                {
                    Freetile.positionAll(container, o);
                });
            }
            return container;
        },
        
        // Method 2.
        // Similar to method 1 but only does something if there is
        // content to append.
        append : function(options) 
        {
            var container = this,
                o = Freetile.setupOptions(container, options),
                c = Freetile.newContent(o.contentToAppend);
            
            // If there is content to append and container has been already
            // tiled, continue in append mode.
            if (o.tiled && c) 
            {
                container.append(c);
                c.imagesLoaded(function() 
                {
                    Freetile.positionAll(container, o);
                });
            }
            return container;
        },
        
        // Method 3.
        // Layout all elements just once. Single shot. Nothing else
        // is done.
        layout : function(options) 
        {
            var container = this,
                o = Freetile.setupOptions(container, options);
            
            // Position Elements
            Freetile.positionAll(container, o);
            return container;
        },
        
        //
        // "Internal" Methods
        // _________________________________________________________
        
        // Setup Options Object
        // _________________________________________________________

        setupOptions : function(container, options) 
        {
            // Get the data object from the container. If it doesn't exist it probably means
            // it's the first time Freetile is called..
            var containerData = container.data('FreetileData');

            // Generate the options object.
            var newOptions = $.extend(true,
                {},
                this.defaults,
                containerData,
                this.reset,
                options
            );
            
            // At this point we have a nice options object which is a special blend
            // of user-defined options, stored preferences and defaults. Let's save it.
            container.data('FreetileData', newOptions);

            // Temporary variable to denote whether the container has already been
            // processed before.
            newOptions.tiled = (containerData !== undefined);
            
            // The real 'animate' property is dependent, apart from user preference,
            // on whether this is the first time that Freetile is being called (should
            // be false) and whether we are appending content (should be false too).
            // !! animate and _animate are different variables!
            // _animate is an internal variable that indicates whether animation is
            // POSSIBLE & REQUESTED !!
            newOptions._animate = newOptions.animate && newOptions.tiled && $.isEmptyObject(newOptions.contentToAppend);
            this.reset.callback = newOptions.persistentCallback && newOptions.callback ? newOptions.callback : function() {};
            return newOptions;
        },
        
        // Setup bindings to resize and custom events.
        // _________________________________________________________

        setupContainerBindings : function(container, o) 
        {
            // Bind to window resize.
            if (o.containerResize) 
            {
                var win = $(window),
                    curWidth = win.width(),
                    curHeight = win.height();

                win.resize(function() 
                {
                    clearTimeout(container.data("FreetileTimeout"));
                    container.data("FreetileTimeout", setTimeout(function() 
                    {
                        var newWidth = win.width(),
                        newHeight = win.height();
                        //Call function only if the window *actually* changes size!
                        if (newWidth != curWidth || newHeight != curHeight) 
                        {
                            curWidth = newWidth,
                            curHeight = newHeight;
                            container.freetile('layout');
                        }
                    }, 400) );
                });
            }
            // Bind to custom events.
            if (o.customEvents) 
            {
                container.bind(o.customEvents, function() 
                {
                    clearTimeout(container.data("FreetileTimeout"));
                    container.data("FreetileTimeout", setTimeout(function() { container.freetile('layout'); }, 400) );
                });
            }
            return container;
        },

        // Get content to be appended.
        // _________________________________________________________

        newContent : function(content) 
        {
            if ( (typeof content === 'object' && !$.isEmptyObject(content)) 
                || (typeof content === 'string' && $(content).length ) ) 
            {
                return $(content);
            }
            return false;
        },
        
        // Position a single element.
        // _________________________________________________________

        calculatePositions : function(container, elements, o) // Container, elements, options
        { 
            // Position index:
            // |    |   |       Old columns
            // |      |         New column
            // ^        ^
            // Start    End

            elements.each(function(i) 
            {
                // Variable declaration.
                var $this = $(this),
                    j = 0;

                o.ElementWidth = $this.outerWidth(true);
                o.ElementHeight = $this.outerHeight(true);
                o.ElementTop = 0;
                o.ElementIndex = i;
                o.IndexStart = 0; 
                o.IndexEnd = 0;
                o.BestScore = 0;
                o.TestedTop = 0;
                o.TestedLeft = 0;


                // 1.   Determine Element Position
                // ___________________________________________________

                // Find out the true top position of element
                // for position 0 (in case it spans multiple elements)
                o.TestedTop = o.currentPos[0].top;
                for (j = 1; j < o.currentPos.length && o.currentPos[j].left < o.ElementWidth; j++)
                {
                    o.TestedTop = Math.max(o.TestedTop, o.currentPos[j].top);
                }
                o.ElementTop = o.TestedTop;
                o.IndexEnd = j;
                o.BestScore = o.scoreFunction(o);

                // Element is now successfully placed at position 0.
                // As a next step, investigate the rest of available positions
                // as to whether they are better.
                for (var i = 1; (i < o.currentPos.length) && (o.currentPos[i].left + o.ElementWidth <= o.containerWidth); i++)
                {
                    o.TestedLeft = o.currentPos[i].left;
                    o.TestedTop = o.currentPos[i].top;
                    for (j = i + 1; (j < o.currentPos.length) && (o.currentPos[j].left - o.currentPos[i].left < o.ElementWidth); j++)
                    {
                        o.TestedTop = Math.max(o.TestedTop, o.currentPos[j].top);
                    }
                    var NewScore = o.scoreFunction(o);
                    if (NewScore > o.BestScore) 
                    {
                        o.IndexStart = i;
                        o.IndexEnd = j;
                        o.ElementTop = o.TestedTop;
                        o.BestScore = NewScore;
                    }
                }
                // At this point 1 <= o.IndexEnd <= Len.
                

                // 2.   Apply Element Position
                // ___________________________________________________

                // Current Position
                var curpos = $this.position(),

                // New Position
                    pos = 
                    {
                        left: o.currentPos[o.IndexStart].left + o.xPadding,
                        top: o.ElementTop + o.yPadding
                    };

                // Position the element only if it's position actually changes.
                // This check is useful when we are re-arranging an already packed arrangement.
                // Some elements may still need to be in the same positions.
                if (curpos.top != pos.top || curpos.left != pos.left) 
                {
                    var aniObj = {el : $this, f : 'css', d : 0};

                    if (o._animate && !$this.hasClass('noanim')) 
                    {
                        // Current offset
                        var curoffset = $this.offset(),

                        // Easily find new offset without lots of calculations
                        offset = 
                        {
                            left: pos.left + (curoffset.left - curpos.left),
                            top: pos.top + (curoffset.top - curpos.top)
                        };

                        // Animate only if:
                        // 1. Animate option is possible and enabled 
                        // 2. The element is allowed to animate (doesn't have the 'noanim' class)
                        // 3. The y-offset position of the element is within the viewport.
                        // Callback counter will be
                        // updated on animation end.
                        if ((curoffset.top + o.ElementHeight > o.viewportY && curoffset.top < o.viewportYH ||
                             offset.top + o.ElementHeight > o.viewportY && offset.top < o.viewportYH)) 
                        {

                            aniObj.f = 'animate'
                            aniObj.d = o.currentDelay;

                            ++(o.iteration);
                            // Increase the animation delay value.
                            o.currentDelay += o.elementDelay;
                        } 
                    } 
                    aniObj.style = pos;
                    o.styleQueue.push(aniObj);
                } 
                // Update the callback counter.
                 --(o.iteration);

                // 3.   Reconstruct Columns
                // ___________________________________________________

                // a.   Store the height of the last element of the span.
                //      If a new insertion point is to be inserted at the end of the
                //      new element span, it should have this height.
                var LastSpanTop = o.currentPos[o.IndexEnd - 1].top,
                    LastSpanRight = o.currentPos[o.IndexEnd]? o.currentPos[o.IndexEnd].left : o.containerWidth,
                    ElementRight = o.currentPos[o.IndexStart].left + o.ElementWidth;

                // b.   Update height in insertion column.
                o.currentPos[o.IndexStart].top = o.ElementTop + o.ElementHeight;

                // c. If there are columns after the insertion point,
                //    remove them, up until the last occupied column.
                //    also: If there is leftover (i.e. ElementLeft + ElementWidth < ContainerWidth)
                //    add an insertion point at X: ElementLeft + ElementWidth, Y:LastSpanTop
                if (ElementRight < LastSpanRight) 
                {
                    o.currentPos.splice(o.IndexStart + 1, o.IndexEnd - o.IndexStart - 1, {left: ElementRight, top: LastSpanTop} );
                } 
                else 
                {
                    o.currentPos.splice(o.IndexStart + 1, o.IndexEnd - o.IndexStart - 1);
                }
            });
        },

        // Process styleQueue
        // This is set up like jQuery masonry, rather than directly applying
        // styles in the positioning loop. For some reason, it yields a performance gain
        // of about 7x (in Safari 5.1.4)!!
        // _________________________________________________________

        applyStyles : function(o) {
            var obj;
            for (var i=0, len = o.styleQueue.length; i < len; i++) 
            {
                obj = o.styleQueue[i];
                if (obj.f == 'animate') 
                {
                    obj.el.delay(obj.d).animate(obj.style, $.extend( true, {}, o.animationOptions));
                } 
                else 
                {
                    obj.el.css(obj.style);
                }
            }
        },
        
        // Main layout algorithm
        // _________________________________________________________

        positionAll : function(container, o) 
        {
            //
            // 1. Initialize
            //

            // Get elements
            if ($.isEmptyObject(o.contentToAppend))
            {
                var Elements = o.selector ? container.children(o.selector) : container.children();
            }
            else
            {
                var Elements = o.selector ? o.contentToAppend.filter(o.selector) : o.contentToAppend;
            }
            
            // Count elements.
            o.ElementsCount = Elements.length;

            if (!o.ElementsCount) return(false);
            
            // Store the container's visibility properties.
            var disp = container.css('display') || '';
            var vis = container.css('visibility') || '';
            
            // Temporarily show the container...
            container.css({ display: 'block', width: '', visibility: 'hidden' });

            // Calculate container width
            o.containerWidth = container.width();
            
            // Get saved positions of elements if they exist and if we are appending
            // new content.
            var savedPos = container.data("FreetilePos");
            o.currentPos = !$.isEmptyObject(o.contentToAppend) && savedPos ? savedPos : [{left: 0, top: 0}];
        
            // Calculate container padding for correct element positioning
            o.xPadding = parseInt(container.css("padding-left"), 10);
            o.yPadding = parseInt(container.css("padding-top"), 10);
            
            // Set viewport y-offset and height
            o.viewportY = $(window).scrollTop();
            o.viewportYH = o.viewportY + $(window).height();
            
            // Initialize some variables in the options object
            o.iteration = Elements.length;
            o.currentDelay = 0;
            o.styleQueue = [];

            // Set Callback. (will be cleared on next run)
            o.animationOptions.complete = function() { if (--(o.iteration) <= 0) o.callback(o); } ;
            
            //
            // 2. Position Elements and apply styles.
            //

            // Set elements position to absolute first. http://bit.ly/hpo7Nv
            Elements.css({position: 'absolute'});
            Freetile.calculatePositions(container, Elements, o);
            Freetile.applyStyles(o);
            
            //
            // 3. Finalize
            //

            // Define container-specific CSS and force width if forceWidth is true,
            // taking into account containerWidthStep if present, to specify a different
            // width-stepping than the width of the elements.
            // Also restore original position information.
            var CalculatedCSS = {};

            if (disp) CalculatedCSS.display = disp;
            if (vis) CalculatedCSS.visibility = vis;
            
            // If container position is static make it relative to properly position elements.
            if (container.css('position') == 'static') 
            {
                CalculatedCSS.position = 'relative';
            }
            
            // If forceWidth is true, apply new width to the container
            // using step specified in containerWidthStep.
            if (o.forceWidth && o.containerWidthStep > 0) 
            {
                CalculatedCSS.width = o.containerWidthStep * (parseInt(container.width() / o.containerWidthStep, 10))
            }
            
            // Apply initial CSS properties.
            container.css(CalculatedCSS);
            
            // Re-use CalculatedCSS to apply height.
            var Tops = $.map(o.currentPos, function(n, i) {return n.top;});
            CalculatedCSS = {height: Math.max.apply(Math, Tops)};
            
            // Apply or animate.
            if (o._animate && o.containerAnimate) 
            {
                container.stop().animate(CalculatedCSS, $.extend( true, {}, o.animationOptions)); 
            }
            else 
            {
                container.css(CalculatedCSS);
            }

            // Callback
            if (o.iteration <= 0) o.callback(o);

            // Save current positions
            container.data("FreetilePos", o.currentPos);

            // Mark elements as tiled.
            Elements.addClass("tiled");
            return container;
        },
        
        // Defaults
        defaults : 
        {
            selector : '*',
            animate : false,
            elementDelay : 0,
            containerResize : true,
            containerAnimate : false, 
            customEvents : '',
            persistentCallback : false,
            forceWidth : false,
            containerWidthStep : 1,
            scoreFunction: function(o) 
            {
                // Minimum Available Variable set
                // o.IndexStart, o.IndexEnd, o.TestedLeft, o.TestedTop, o.ElementWidth, o.ElementHeight

                // The following rule would add a bit of bias to the left.
                //return -(o.TestedTop) * 8 - (o.TestedLeft);

                // Simple least-height heuristic rule (default)
                return -(o.TestedTop);
            }
        },
        
        // Overriding options.
        reset : 
        {
            animationOptions : { complete: function() {} },
            callback : function() {},
            contentToAppend : {}
        }
    };
    
    //
    // Helper Methods
    // _________________________________________________________
    
    $.fn.imagesLoaded = function( callback ) 
    {
        var $this = this,
            $images = $this.find('img:not(.load-complete)').add( $this.filter('img:not(.load-complete)') ),
            len = $images.length,
            current = len,
            images_src = [],
            blank = 'data:image/gif;base64,R0lGODlhAQABAIAAAAAAAP///ywAAAAAAQABAAACAUwAOw==';

        function triggerCallback() 
        {
            callback.call( $this, $images );
        }

        function triggerStep() 
        {
            if ($.fn.imagesLoaded.step) $.fn.imagesLoaded.step.call( $this, current, len, $images );
        }

        function imgLoadedI( event ) 
        {
            if ( --current <= 0)
            {
                current = len;
                $images.unbind( 'load error', imgLoadedI )
                .bind( 'load error', imgLoadedII )
                .each(function() 
                {
                    this.src = images_src.shift();
                });
            }
        }

        function imgLoadedII( event ) 
        {
            setTimeout( triggerStep );
            if ( --current <= 0 && event.target.src !== blank )
            {
                setTimeout( triggerCallback );
                $images.unbind( 'load error', imgLoadedII ).addClass('load-complete');
            }
        }

        if ( !len ) 
        {
            triggerCallback();
        }

        $images.bind( 'load error',  imgLoadedI ).each( function() 
        {
            images_src.push( this.src );
            // webkit hack from http://groups.google.com/group/jquery-dev/browse_thread/thread/eee6ab7b2da50e1f
            // data uri bypasses webkit log warning (thx doug jones)
            this.src = "data:image/gif;base64,R0lGODlhAQABAIAAAAAAAP///ywAAAAAAQABAAACAUwAOw==";
        });

        return $this;
    };
})( jQuery );