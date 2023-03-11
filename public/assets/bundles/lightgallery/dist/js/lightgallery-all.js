/*! lightgallery - v1.6.11 - 2018-05-22
* http://sachinchoolur.github.io/lightGallery/
* Copyright (c) 2018 Sachin N; Licensed GPLv3 */
/*! lightgallery - v1.6.11 - 2018-05-22
* http://sachinchoolur.github.io/lightGallery/
* Copyright (c) 2018 Sachin N; Licensed GPLv3 */
(function (root, factory) {
    if (typeof define === 'function' && define.amd) {
        // AMD. Register as an anonymous module unless amdModuleId is set
        define(['jquery'], function (a0) {
            return (factory(a0));
        });
    } else if (typeof module === 'object' && module.exports) {
        // Node. Does not work with strict CommonJS, but
        // only CommonJS-like environments that support module.exports,
        // like Node.
        module.exports = factory(require('jquery'));
    } else {
        factory(root["jQuery"]);
    }
}(this, function ($) {

    (function () {
        'use strict';

        var defaults = {

            mode: 'lg-slide',

            // Ex : 'ease'
            cssEasing: 'ease',

            //'for jquery animation'
            easing: 'linear',
            speed: 600,
            height: '100%',
            width: '100%',
            addClass: '',
            startClass: 'lg-start-zoom',
            backdropDuration: 150,
            hideBarsDelay: 6000,

            useLeft: false,

            closable: true,
            loop: true,
            escKey: true,
            keyPress: true,
            controls: true,
            slideEndAnimatoin: true,
            hideControlOnEnd: false,
            mousewheel: true,

            getCaptionFromTitleOrAlt: true,

            // .lg-item || '.lg-sub-html'
            appendSubHtmlTo: '.lg-sub-html',

            subHtmlSelectorRelative: false,

            /**
             * @desc number of preload slides
             * will exicute only after the current slide is fully loaded.
             *
             * @ex you clicked on 4th image and if preload = 1 then 3rd slide and 5th
             * slide will be loaded in the background after the 4th slide is fully loaded..
             * if preload is 2 then 2nd 3rd 5th 6th slides will be preloaded.. ... ...
             *
             */
            preload: 1,
            showAfterLoad: true,
            selector: '',
            selectWithin: '',
            nextHtml: '',
            prevHtml: '',

            // 0, 1
            index: false,

            iframeMaxWidth: '100%',

            download: true,
            counter: true,
            appendCounterTo: '.lg-toolbar',

            swipeThreshold: 50,
            enableSwipe: true,
            enableDrag: true,

            dynamic: false,
            dynamicEl: [],
            galleryId: 1
        };

        function Plugin(element, options) {

            // Current lightGallery element
            this.el = element;

            // Current jquery element
            this.$el = $(element);

            // lightGallery settings
            this.s = $.extend({}, defaults, options);

            // When using dynamic mode, ensure dynamicEl is an array
            if (this.s.dynamic && this.s.dynamicEl !== 'undefined' && this.s.dynamicEl.constructor === Array && !this.s.dynamicEl.length) {
                throw ('When using dynamic mode, you must also define dynamicEl as an Array.');
            }

            // lightGallery modules
            this.modules = {};

            // false when lightgallery complete first slide;
            this.lGalleryOn = false;

            this.lgBusy = false;

            // Timeout function for hiding controls;
            this.hideBartimeout = false;

            // To determine browser supports for touch events;
            this.isTouch = ('ontouchstart' in document.documentElement);

            // Disable hideControlOnEnd if sildeEndAnimation is true
            if (this.s.slideEndAnimatoin) {
                this.s.hideControlOnEnd = false;
            }

            // Gallery items
            if (this.s.dynamic) {
                this.$items = this.s.dynamicEl;
            } else {
                if (this.s.selector === 'this') {
                    this.$items = this.$el;
                } else if (this.s.selector !== '') {
                    if (this.s.selectWithin) {
                        this.$items = $(this.s.selectWithin).find(this.s.selector);
                    } else {
                        this.$items = this.$el.find($(this.s.selector));
                    }
                } else {
                    this.$items = this.$el.children();
                }
            }

            // .lg-item
            this.$slide = '';

            // .lg-outer
            this.$outer = '';

            this.init();

            return this;
        }

        Plugin.prototype.init = function () {

            var _this = this;

            // s.preload should not be more than $item.length
            if (_this.s.preload > _this.$items.length) {
                _this.s.preload = _this.$items.length;
            }

            // if dynamic option is enabled execute immediately
            var _hash = window.location.hash;
            if (_hash.indexOf('lg=' + this.s.galleryId) > 0) {

                _this.index = parseInt(_hash.split('&slide=')[1], 10);

                $('body').addClass('lg-from-hash');
                if (!$('body').hasClass('lg-on')) {
                    setTimeout(function () {
                        _this.build(_this.index);
                    });

                    $('body').addClass('lg-on');
                }
            }

            if (_this.s.dynamic) {

                _this.$el.trigger('onBeforeOpen.lg');

                _this.index = _this.s.index || 0;

                // prevent accidental double execution
                if (!$('body').hasClass('lg-on')) {
                    setTimeout(function () {
                        _this.build(_this.index);
                        $('body').addClass('lg-on');
                    });
                }
            } else {

                // Using different namespace for click because click event should not unbind if selector is same object('this')
                _this.$items.on('click.lgcustom', function (event) {

                    // For IE8
                    try {
                        event.preventDefault();
                        event.preventDefault();
                    } catch (er) {
                        event.returnValue = false;
                    }

                    _this.$el.trigger('onBeforeOpen.lg');

                    _this.index = _this.s.index || _this.$items.index(this);

                    // prevent accidental double execution
                    if (!$('body').hasClass('lg-on')) {
                        _this.build(_this.index);
                        $('body').addClass('lg-on');
                    }
                });
            }

        };

        Plugin.prototype.build = function (index) {

            var _this = this;

            _this.structure();

            // module constructor
            $.each($.fn.lightGallery.modules, function (key) {
                _this.modules[key] = new $.fn.lightGallery.modules[key](_this.el);
            });

            // initiate slide function
            _this.slide(index, false, false, false);

            if (_this.s.keyPress) {
                _this.keyPress();
            }

            if (_this.$items.length > 1) {

                _this.arrow();

                setTimeout(function () {
                    _this.enableDrag();
                    _this.enableSwipe();
                }, 50);

                if (_this.s.mousewheel) {
                    _this.mousewheel();
                }
            } else {
                _this.$slide.on('click.lg', function () {
                    _this.$el.trigger('onSlideClick.lg');
                });
            }

            _this.counter();

            _this.closeGallery();

            _this.$el.trigger('onAfterOpen.lg');

            // Hide controllers if mouse doesn't move for some period
            _this.$outer.on('mousemove.lg click.lg touchstart.lg', function () {

                _this.$outer.removeClass('lg-hide-items');

                clearTimeout(_this.hideBartimeout);

                // Timeout will be cleared on each slide movement also
                _this.hideBartimeout = setTimeout(function () {
                    _this.$outer.addClass('lg-hide-items');
                }, _this.s.hideBarsDelay);

            });

            _this.$outer.trigger('mousemove.lg');

        };

        Plugin.prototype.structure = function () {
            var list = '';
            var controls = '';
            var i = 0;
            var subHtmlCont = '';
            var template;
            var _this = this;

            $('body').append('<div class="lg-backdrop"></div>');
            $('.lg-backdrop').css('transition-duration', this.s.backdropDuration + 'ms');

            // Create gallery items
            for (i = 0; i < this.$items.length; i++) {
                list += '<div class="lg-item"></div>';
            }

            // Create controlls
            if (this.s.controls && this.$items.length > 1) {
                controls = '<div class="lg-actions">' +
                    '<button class="lg-prev lg-icon">' + this.s.prevHtml + '</button>' +
                    '<button class="lg-next lg-icon">' + this.s.nextHtml + '</button>' +
                    '</div>';
            }

            if (this.s.appendSubHtmlTo === '.lg-sub-html') {
                subHtmlCont = '<div class="lg-sub-html"></div>';
            }

            template = '<div class="lg-outer ' + this.s.addClass + ' ' + this.s.startClass + '">' +
                '<div class="lg" style="width:' + this.s.width + '; height:' + this.s.height + '">' +
                '<div class="lg-inner">' + list + '</div>' +
                '<div class="lg-toolbar lg-group">' +
                '<span class="lg-close lg-icon"></span>' +
                '</div>' +
                controls +
                subHtmlCont +
                '</div>' +
                '</div>';

            $('body').append(template);
            this.$outer = $('.lg-outer');
            this.$slide = this.$outer.find('.lg-item');

            if (this.s.useLeft) {
                this.$outer.addClass('lg-use-left');

                // Set mode lg-slide if use left is true;
                this.s.mode = 'lg-slide';
            } else {
                this.$outer.addClass('lg-use-css3');
            }

            // For fixed height gallery
            _this.setTop();
            $(window).on('resize.lg orientationchange.lg', function () {
                setTimeout(function () {
                    _this.setTop();
                }, 100);
            });

            // add class lg-current to remove initial transition
            this.$slide.eq(this.index).addClass('lg-current');

            // add Class for css support and transition mode
            if (this.doCss()) {
                this.$outer.addClass('lg-css3');
            } else {
                this.$outer.addClass('lg-css');

                // Set speed 0 because no animation will happen if browser doesn't support css3
                this.s.speed = 0;
            }

            this.$outer.addClass(this.s.mode);

            if (this.s.enableDrag && this.$items.length > 1) {
                this.$outer.addClass('lg-grab');
            }

            if (this.s.showAfterLoad) {
                this.$outer.addClass('lg-show-after-load');
            }

            if (this.doCss()) {
                var $inner = this.$outer.find('.lg-inner');
                $inner.css('transition-timing-function', this.s.cssEasing);
                $inner.css('transition-duration', this.s.speed + 'ms');
            }

            setTimeout(function () {
                $('.lg-backdrop').addClass('in');
            });

            setTimeout(function () {
                _this.$outer.addClass('lg-visible');
            }, this.s.backdropDuration);

            if (this.s.download) {
                this.$outer.find('.lg-toolbar').append('<a id="lg-download" target="_blank" download class="lg-download lg-icon"></a>');
            }

            // Store the current scroll top value to scroll back after closing the gallery..
            this.prevScrollTop = $(window).scrollTop();

        };

        // For fixed height gallery
        Plugin.prototype.setTop = function () {
            if (this.s.height !== '100%') {
                var wH = $(window).height();
                var top = (wH - parseInt(this.s.height, 10)) / 2;
                var $lGallery = this.$outer.find('.lg');
                if (wH >= parseInt(this.s.height, 10)) {
                    $lGallery.css('top', top + 'px');
                } else {
                    $lGallery.css('top', '0px');
                }
            }
        };

        // Find css3 support
        Plugin.prototype.doCss = function () {
            // check for css animation support
            var support = function () {
                var transition = ['transition', 'MozTransition', 'WebkitTransition', 'OTransition', 'msTransition', 'KhtmlTransition'];
                var root = document.documentElement;
                var i = 0;
                for (i = 0; i < transition.length; i++) {
                    if (transition[i] in root.style) {
                        return true;
                    }
                }
            };

            if (support()) {
                return true;
            }

            return false;
        };

        /**
         *  @desc Check the given src is video
         *  @param {String} src
         *  @return {Object} video type
         *  Ex:{ youtube  :  ["//www.youtube.com/watch?v=c0asJgSyxcY", "c0asJgSyxcY"] }
         */
        Plugin.prototype.isVideo = function (src, index) {

            var html;
            if (this.s.dynamic) {
                html = this.s.dynamicEl[index].html;
            } else {
                html = this.$items.eq(index).attr('data-html');
            }

            if (!src) {
                if (html) {
                    return {
                        html5: true
                    };
                } else {
                    console.error('lightGallery :- data-src is not pvovided on slide item ' + (index + 1) + '. Please make sure the selector property is properly configured. More info - http://sachinchoolur.github.io/lightGallery/demos/html-markup.html');
                    return false;
                }
            }

            var youtube = src.match(/\/\/(?:www\.)?youtu(?:\.be|be\.com|be-nocookie\.com)\/(?:watch\?v=|embed\/)?([a-z0-9\-\_\%]+)/i);
            var vimeo = src.match(/\/\/(?:www\.)?vimeo.com\/([0-9a-z\-_]+)/i);
            var dailymotion = src.match(/\/\/(?:www\.)?dai.ly\/([0-9a-z\-_]+)/i);
            var vk = src.match(/\/\/(?:www\.)?(?:vk\.com|vkontakte\.ru)\/(?:video_ext\.php\?)(.*)/i);

            if (youtube) {
                return {
                    youtube: youtube
                };
            } else if (vimeo) {
                return {
                    vimeo: vimeo
                };
            } else if (dailymotion) {
                return {
                    dailymotion: dailymotion
                };
            } else if (vk) {
                return {
                    vk: vk
                };
            }
        };

        /**
         *  @desc Create image counter
         *  Ex: 1/10
         */
        Plugin.prototype.counter = function () {
            if (this.s.counter) {
                $(this.s.appendCounterTo).append('<div id="lg-counter"><span id="lg-counter-current">' + (parseInt(this.index, 10) + 1) + '</span> / <span id="lg-counter-all">' + this.$items.length + '</span></div>');
            }
        };

        /**
         *  @desc add sub-html into the slide
         *  @param {Number} index - index of the slide
         */
        Plugin.prototype.addHtml = function (index) {
            var subHtml = null;
            var subHtmlUrl;
            var $currentEle;
            if (this.s.dynamic) {
                if (this.s.dynamicEl[index].subHtmlUrl) {
                    subHtmlUrl = this.s.dynamicEl[index].subHtmlUrl;
                } else {
                    subHtml = this.s.dynamicEl[index].subHtml;
                }
            } else {
                $currentEle = this.$items.eq(index);
                if ($currentEle.attr('data-sub-html-url')) {
                    subHtmlUrl = $currentEle.attr('data-sub-html-url');
                } else {
                    subHtml = $currentEle.attr('data-sub-html');
                    if (this.s.getCaptionFromTitleOrAlt && !subHtml) {
                        subHtml = $currentEle.attr('title') || $currentEle.find('img').first().attr('alt');
                    }
                }
            }

            if (!subHtmlUrl) {
                if (typeof subHtml !== 'undefined' && subHtml !== null) {

                    // get first letter of subhtml
                    // if first letter starts with . or # get the html form the jQuery object
                    var fL = subHtml.substring(0, 1);
                    if (fL === '.' || fL === '#') {
                        if (this.s.subHtmlSelectorRelative && !this.s.dynamic) {
                            subHtml = $currentEle.find(subHtml).html();
                        } else {
                            subHtml = $(subHtml).html();
                        }
                    }
                } else {
                    subHtml = '';
                }
            }

            if (this.s.appendSubHtmlTo === '.lg-sub-html') {

                if (subHtmlUrl) {
                    this.$outer.find(this.s.appendSubHtmlTo).load(subHtmlUrl);
                } else {
                    this.$outer.find(this.s.appendSubHtmlTo).html(subHtml);
                }

            } else {

                if (subHtmlUrl) {
                    this.$slide.eq(index).load(subHtmlUrl);
                } else {
                    this.$slide.eq(index).append(subHtml);
                }
            }

            // Add lg-empty-html class if title doesn't exist
            if (typeof subHtml !== 'undefined' && subHtml !== null) {
                if (subHtml === '') {
                    this.$outer.find(this.s.appendSubHtmlTo).addClass('lg-empty-html');
                } else {
                    this.$outer.find(this.s.appendSubHtmlTo).removeClass('lg-empty-html');
                }
            }

            this.$el.trigger('onAfterAppendSubHtml.lg', [index]);
        };

        /**
         *  @desc Preload slides
         *  @param {Number} index - index of the slide
         */
        Plugin.prototype.preload = function (index) {
            var i = 1;
            var j = 1;
            for (i = 1; i <= this.s.preload; i++) {
                if (i >= this.$items.length - index) {
                    break;
                }

                this.loadContent(index + i, false, 0);
            }

            for (j = 1; j <= this.s.preload; j++) {
                if (index - j < 0) {
                    break;
                }

                this.loadContent(index - j, false, 0);
            }
        };

        /**
         *  @desc Load slide content into slide.
         *  @param {Number} index - index of the slide.
         *  @param {Boolean} rec - if true call loadcontent() function again.
         *  @param {Boolean} delay - delay for adding complete class. it is 0 except first time.
         */
        Plugin.prototype.loadContent = function (index, rec, delay) {

            var _this = this;
            var _hasPoster = false;
            var _$img;
            var _src;
            var _poster;
            var _srcset;
            var _sizes;
            var _html;
            var getResponsiveSrc = function (srcItms) {
                var rsWidth = [];
                var rsSrc = [];
                for (var i = 0; i < srcItms.length; i++) {
                    var __src = srcItms[i].split(' ');

                    // Manage empty space
                    if (__src[0] === '') {
                        __src.splice(0, 1);
                    }

                    rsSrc.push(__src[0]);
                    rsWidth.push(__src[1]);
                }

                var wWidth = $(window).width();
                for (var j = 0; j < rsWidth.length; j++) {
                    if (parseInt(rsWidth[j], 10) > wWidth) {
                        _src = rsSrc[j];
                        break;
                    }
                }
            };

            if (_this.s.dynamic) {

                if (_this.s.dynamicEl[index].poster) {
                    _hasPoster = true;
                    _poster = _this.s.dynamicEl[index].poster;
                }

                _html = _this.s.dynamicEl[index].html;
                _src = _this.s.dynamicEl[index].src;

                if (_this.s.dynamicEl[index].responsive) {
                    var srcDyItms = _this.s.dynamicEl[index].responsive.split(',');
                    getResponsiveSrc(srcDyItms);
                }

                _srcset = _this.s.dynamicEl[index].srcset;
                _sizes = _this.s.dynamicEl[index].sizes;

            } else {

                if (_this.$items.eq(index).attr('data-poster')) {
                    _hasPoster = true;
                    _poster = _this.$items.eq(index).attr('data-poster');
                }

                _html = _this.$items.eq(index).attr('data-html');
                _src = _this.$items.eq(index).attr('href') || _this.$items.eq(index).attr('data-src');

                if (_this.$items.eq(index).attr('data-responsive')) {
                    var srcItms = _this.$items.eq(index).attr('data-responsive').split(',');
                    getResponsiveSrc(srcItms);
                }

                _srcset = _this.$items.eq(index).attr('data-srcset');
                _sizes = _this.$items.eq(index).attr('data-sizes');

            }

            //if (_src || _srcset || _sizes || _poster) {

            var iframe = false;
            if (_this.s.dynamic) {
                if (_this.s.dynamicEl[index].iframe) {
                    iframe = true;
                }
            } else {
                if (_this.$items.eq(index).attr('data-iframe') === 'true') {
                    iframe = true;
                }
            }

            var _isVideo = _this.isVideo(_src, index);
            if (!_this.$slide.eq(index).hasClass('lg-loaded')) {
                if (iframe) {
                    _this.$slide.eq(index).prepend('<div class="lg-video-cont lg-has-iframe" style="max-width:' + _this.s.iframeMaxWidth + '"><div class="lg-video"><iframe class="lg-object" frameborder="0" src="' + _src + '"  allowfullscreen="true"></iframe></div></div>');
                } else if (_hasPoster) {
                    var videoClass = '';
                    if (_isVideo && _isVideo.youtube) {
                        videoClass = 'lg-has-youtube';
                    } else if (_isVideo && _isVideo.vimeo) {
                        videoClass = 'lg-has-vimeo';
                    } else {
                        videoClass = 'lg-has-html5';
                    }

                    _this.$slide.eq(index).prepend('<div class="lg-video-cont ' + videoClass + ' "><div class="lg-video"><span class="lg-video-play"></span><img class="lg-object lg-has-poster" src="' + _poster + '" /></div></div>');

                } else if (_isVideo) {
                    _this.$slide.eq(index).prepend('<div class="lg-video-cont "><div class="lg-video"></div></div>');
                    _this.$el.trigger('hasVideo.lg', [index, _src, _html]);
                } else {
                    _this.$slide.eq(index).prepend('<div class="lg-img-wrap"><img class="lg-object lg-image" src="' + _src + '" /></div>');
                }

                _this.$el.trigger('onAferAppendSlide.lg', [index]);

                _$img = _this.$slide.eq(index).find('.lg-object');
                if (_sizes) {
                    _$img.attr('sizes', _sizes);
                }

                if (_srcset) {
                    _$img.attr('srcset', _srcset);
                    try {
                        picturefill({
                            elements: [_$img[0]]
                        });
                    } catch (e) {
                        console.warn('lightGallery :- If you want srcset to be supported for older browser please include picturefil version 2 javascript library in your document.');
                    }
                }

                if (this.s.appendSubHtmlTo !== '.lg-sub-html') {
                    _this.addHtml(index);
                }

                _this.$slide.eq(index).addClass('lg-loaded');
            }

            _this.$slide.eq(index).find('.lg-object').on('load.lg error.lg', function () {

                // For first time add some delay for displaying the start animation.
                var _speed = 0;

                // Do not change the delay value because it is required for zoom plugin.
                // If gallery opened from direct url (hash) speed value should be 0
                if (delay && !$('body').hasClass('lg-from-hash')) {
                    _speed = delay;
                }

                setTimeout(function () {
                    _this.$slide.eq(index).addClass('lg-complete');
                    _this.$el.trigger('onSlideItemLoad.lg', [index, delay || 0]);
                }, _speed);

            });

            // @todo check load state for html5 videos
            if (_isVideo && _isVideo.html5 && !_hasPoster) {
                _this.$slide.eq(index).addClass('lg-complete');
            }

            if (rec === true) {
                if (!_this.$slide.eq(index).hasClass('lg-complete')) {
                    _this.$slide.eq(index).find('.lg-object').on('load.lg error.lg', function () {
                        _this.preload(index);
                    });
                } else {
                    _this.preload(index);
                }
            }

            //}
        };

        /**
        *   @desc slide function for lightgallery
            ** Slide() gets call on start
            ** ** Set lg.on true once slide() function gets called.
            ** Call loadContent() on slide() function inside setTimeout
            ** ** On first slide we do not want any animation like slide of fade
            ** ** So on first slide( if lg.on if false that is first slide) loadContent() should start loading immediately
            ** ** Else loadContent() should wait for the transition to complete.
            ** ** So set timeout s.speed + 50
        <=> ** loadContent() will load slide content in to the particular slide
            ** ** It has recursion (rec) parameter. if rec === true loadContent() will call preload() function.
            ** ** preload will execute only when the previous slide is fully loaded (images iframe)
            ** ** avoid simultaneous image load
        <=> ** Preload() will check for s.preload value and call loadContent() again accoring to preload value
            ** loadContent()  <====> Preload();
    
        *   @param {Number} index - index of the slide
        *   @param {Boolean} fromTouch - true if slide function called via touch event or mouse drag
        *   @param {Boolean} fromThumb - true if slide function called via thumbnail click
        *   @param {String} direction - Direction of the slide(next/prev)
        */
        Plugin.prototype.slide = function (index, fromTouch, fromThumb, direction) {

            var _prevIndex = this.$outer.find('.lg-current').index();
            var _this = this;

            // Prevent if multiple call
            // Required for hsh plugin
            if (_this.lGalleryOn && (_prevIndex === index)) {
                return;
            }

            var _length = this.$slide.length;
            var _time = _this.lGalleryOn ? this.s.speed : 0;

            if (!_this.lgBusy) {

                if (this.s.download) {
                    var _src;
                    if (_this.s.dynamic) {
                        _src = _this.s.dynamicEl[index].downloadUrl !== false && (_this.s.dynamicEl[index].downloadUrl || _this.s.dynamicEl[index].src);
                    } else {
                        _src = _this.$items.eq(index).attr('data-download-url') !== 'false' && (_this.$items.eq(index).attr('data-download-url') || _this.$items.eq(index).attr('href') || _this.$items.eq(index).attr('data-src'));

                    }

                    if (_src) {
                        $('#lg-download').attr('href', _src);
                        _this.$outer.removeClass('lg-hide-download');
                    } else {
                        _this.$outer.addClass('lg-hide-download');
                    }
                }

                this.$el.trigger('onBeforeSlide.lg', [_prevIndex, index, fromTouch, fromThumb]);

                _this.lgBusy = true;

                clearTimeout(_this.hideBartimeout);

                // Add title if this.s.appendSubHtmlTo === lg-sub-html
                if (this.s.appendSubHtmlTo === '.lg-sub-html') {

                    // wait for slide animation to complete
                    setTimeout(function () {
                        _this.addHtml(index);
                    }, _time);
                }

                this.arrowDisable(index);

                if (!direction) {
                    if (index < _prevIndex) {
                        direction = 'prev';
                    } else if (index > _prevIndex) {
                        direction = 'next';
                    }
                }

                if (!fromTouch) {

                    // remove all transitions
                    _this.$outer.addClass('lg-no-trans');

                    this.$slide.removeClass('lg-prev-slide lg-next-slide');

                    if (direction === 'prev') {

                        //prevslide
                        this.$slide.eq(index).addClass('lg-prev-slide');
                        this.$slide.eq(_prevIndex).addClass('lg-next-slide');
                    } else {

                        // next slide
                        this.$slide.eq(index).addClass('lg-next-slide');
                        this.$slide.eq(_prevIndex).addClass('lg-prev-slide');
                    }

                    // give 50 ms for browser to add/remove class
                    setTimeout(function () {
                        _this.$slide.removeClass('lg-current');

                        //_this.$slide.eq(_prevIndex).removeClass('lg-current');
                        _this.$slide.eq(index).addClass('lg-current');

                        // reset all transitions
                        _this.$outer.removeClass('lg-no-trans');
                    }, 50);
                } else {

                    this.$slide.removeClass('lg-prev-slide lg-current lg-next-slide');
                    var touchPrev;
                    var touchNext;
                    if (_length > 2) {
                        touchPrev = index - 1;
                        touchNext = index + 1;

                        if ((index === 0) && (_prevIndex === _length - 1)) {

                            // next slide
                            touchNext = 0;
                            touchPrev = _length - 1;
                        } else if ((index === _length - 1) && (_prevIndex === 0)) {

                            // prev slide
                            touchNext = 0;
                            touchPrev = _length - 1;
                        }

                    } else {
                        touchPrev = 0;
                        touchNext = 1;
                    }

                    if (direction === 'prev') {
                        _this.$slide.eq(touchNext).addClass('lg-next-slide');
                    } else {
                        _this.$slide.eq(touchPrev).addClass('lg-prev-slide');
                    }

                    _this.$slide.eq(index).addClass('lg-current');
                }

                if (_this.lGalleryOn) {
                    setTimeout(function () {
                        _this.loadContent(index, true, 0);
                    }, this.s.speed + 50);

                    setTimeout(function () {
                        _this.lgBusy = false;
                        _this.$el.trigger('onAfterSlide.lg', [_prevIndex, index, fromTouch, fromThumb]);
                    }, this.s.speed);

                } else {
                    _this.loadContent(index, true, _this.s.backdropDuration);

                    _this.lgBusy = false;
                    _this.$el.trigger('onAfterSlide.lg', [_prevIndex, index, fromTouch, fromThumb]);
                }

                _this.lGalleryOn = true;

                if (this.s.counter) {
                    $('#lg-counter-current').text(index + 1);
                }

            }
            _this.index = index;

        };

        /**
         *  @desc Go to next slide
         *  @param {Boolean} fromTouch - true if slide function called via touch event
         */
        Plugin.prototype.goToNextSlide = function (fromTouch) {
            var _this = this;
            var _loop = _this.s.loop;
            if (fromTouch && _this.$slide.length < 3) {
                _loop = false;
            }

            if (!_this.lgBusy) {
                if ((_this.index + 1) < _this.$slide.length) {
                    _this.index++;
                    _this.$el.trigger('onBeforeNextSlide.lg', [_this.index]);
                    _this.slide(_this.index, fromTouch, false, 'next');
                } else {
                    if (_loop) {
                        _this.index = 0;
                        _this.$el.trigger('onBeforeNextSlide.lg', [_this.index]);
                        _this.slide(_this.index, fromTouch, false, 'next');
                    } else if (_this.s.slideEndAnimatoin && !fromTouch) {
                        _this.$outer.addClass('lg-right-end');
                        setTimeout(function () {
                            _this.$outer.removeClass('lg-right-end');
                        }, 400);
                    }
                }
            }
        };

        /**
         *  @desc Go to previous slide
         *  @param {Boolean} fromTouch - true if slide function called via touch event
         */
        Plugin.prototype.goToPrevSlide = function (fromTouch) {
            var _this = this;
            var _loop = _this.s.loop;
            if (fromTouch && _this.$slide.length < 3) {
                _loop = false;
            }

            if (!_this.lgBusy) {
                if (_this.index > 0) {
                    _this.index--;
                    _this.$el.trigger('onBeforePrevSlide.lg', [_this.index, fromTouch]);
                    _this.slide(_this.index, fromTouch, false, 'prev');
                } else {
                    if (_loop) {
                        _this.index = _this.$items.length - 1;
                        _this.$el.trigger('onBeforePrevSlide.lg', [_this.index, fromTouch]);
                        _this.slide(_this.index, fromTouch, false, 'prev');
                    } else if (_this.s.slideEndAnimatoin && !fromTouch) {
                        _this.$outer.addClass('lg-left-end');
                        setTimeout(function () {
                            _this.$outer.removeClass('lg-left-end');
                        }, 400);
                    }
                }
            }
        };

        Plugin.prototype.keyPress = function () {
            var _this = this;
            if (this.$items.length > 1) {
                $(window).on('keyup.lg', function (e) {
                    if (_this.$items.length > 1) {
                        if (e.keyCode === 37) {
                            e.preventDefault();
                            _this.goToPrevSlide();
                        }

                        if (e.keyCode === 39) {
                            e.preventDefault();
                            _this.goToNextSlide();
                        }
                    }
                });
            }

            $(window).on('keydown.lg', function (e) {
                if (_this.s.escKey === true && e.keyCode === 27) {
                    e.preventDefault();
                    if (!_this.$outer.hasClass('lg-thumb-open')) {
                        _this.destroy();
                    } else {
                        _this.$outer.removeClass('lg-thumb-open');
                    }
                }
            });
        };

        Plugin.prototype.arrow = function () {
            var _this = this;
            this.$outer.find('.lg-prev').on('click.lg', function () {
                _this.goToPrevSlide();
            });

            this.$outer.find('.lg-next').on('click.lg', function () {
                _this.goToNextSlide();
            });
        };

        Plugin.prototype.arrowDisable = function (index) {

            // Disable arrows if s.hideControlOnEnd is true
            if (!this.s.loop && this.s.hideControlOnEnd) {
                if ((index + 1) < this.$slide.length) {
                    this.$outer.find('.lg-next').removeAttr('disabled').removeClass('disabled');
                } else {
                    this.$outer.find('.lg-next').attr('disabled', 'disabled').addClass('disabled');
                }

                if (index > 0) {
                    this.$outer.find('.lg-prev').removeAttr('disabled').removeClass('disabled');
                } else {
                    this.$outer.find('.lg-prev').attr('disabled', 'disabled').addClass('disabled');
                }
            }
        };

        Plugin.prototype.setTranslate = function ($el, xValue, yValue) {
            // jQuery supports Automatic CSS prefixing since jQuery 1.8.0
            if (this.s.useLeft) {
                $el.css('left', xValue);
            } else {
                $el.css({
                    transform: 'translate3d(' + (xValue) + 'px, ' + yValue + 'px, 0px)'
                });
            }
        };

        Plugin.prototype.touchMove = function (startCoords, endCoords) {

            var distance = endCoords - startCoords;

            if (Math.abs(distance) > 15) {
                // reset opacity and transition duration
                this.$outer.addClass('lg-dragging');

                // move current slide
                this.setTranslate(this.$slide.eq(this.index), distance, 0);

                // move next and prev slide with current slide
                this.setTranslate($('.lg-prev-slide'), -this.$slide.eq(this.index).width() + distance, 0);
                this.setTranslate($('.lg-next-slide'), this.$slide.eq(this.index).width() + distance, 0);
            }
        };

        Plugin.prototype.touchEnd = function (distance) {
            var _this = this;

            // keep slide animation for any mode while dragg/swipe
            if (_this.s.mode !== 'lg-slide') {
                _this.$outer.addClass('lg-slide');
            }

            this.$slide.not('.lg-current, .lg-prev-slide, .lg-next-slide').css('opacity', '0');

            // set transition duration
            setTimeout(function () {
                _this.$outer.removeClass('lg-dragging');
                if ((distance < 0) && (Math.abs(distance) > _this.s.swipeThreshold)) {
                    _this.goToNextSlide(true);
                } else if ((distance > 0) && (Math.abs(distance) > _this.s.swipeThreshold)) {
                    _this.goToPrevSlide(true);
                } else if (Math.abs(distance) < 5) {

                    // Trigger click if distance is less than 5 pix
                    _this.$el.trigger('onSlideClick.lg');
                }

                _this.$slide.removeAttr('style');
            });

            // remove slide class once drag/swipe is completed if mode is not slide
            setTimeout(function () {
                if (!_this.$outer.hasClass('lg-dragging') && _this.s.mode !== 'lg-slide') {
                    _this.$outer.removeClass('lg-slide');
                }
            }, _this.s.speed + 100);

        };

        Plugin.prototype.enableSwipe = function () {
            var _this = this;
            var startCoords = 0;
            var endCoords = 0;
            var isMoved = false;

            if (_this.s.enableSwipe && _this.doCss()) {

                _this.$slide.on('touchstart.lg', function (e) {
                    if (!_this.$outer.hasClass('lg-zoomed') && !_this.lgBusy) {
                        e.preventDefault();
                        _this.manageSwipeClass();
                        startCoords = e.originalEvent.targetTouches[0].pageX;
                    }
                });

                _this.$slide.on('touchmove.lg', function (e) {
                    if (!_this.$outer.hasClass('lg-zoomed')) {
                        e.preventDefault();
                        endCoords = e.originalEvent.targetTouches[0].pageX;
                        _this.touchMove(startCoords, endCoords);
                        isMoved = true;
                    }
                });

                _this.$slide.on('touchend.lg', function () {
                    if (!_this.$outer.hasClass('lg-zoomed')) {
                        if (isMoved) {
                            isMoved = false;
                            _this.touchEnd(endCoords - startCoords);
                        } else {
                            _this.$el.trigger('onSlideClick.lg');
                        }
                    }
                });
            }

        };

        Plugin.prototype.enableDrag = function () {
            var _this = this;
            var startCoords = 0;
            var endCoords = 0;
            var isDraging = false;
            var isMoved = false;
            if (_this.s.enableDrag && _this.doCss()) {
                _this.$slide.on('mousedown.lg', function (e) {
                    if (!_this.$outer.hasClass('lg-zoomed') && !_this.lgBusy && !$(e.target).text().trim()) {
                        e.preventDefault();
                        _this.manageSwipeClass();
                        startCoords = e.pageX;
                        isDraging = true;

                        // ** Fix for webkit cursor issue https://code.google.com/p/chromium/issues/detail?id=26723
                        _this.$outer.scrollLeft += 1;
                        _this.$outer.scrollLeft -= 1;

                        // *

                        _this.$outer.removeClass('lg-grab').addClass('lg-grabbing');

                        _this.$el.trigger('onDragstart.lg');
                    }
                });

                $(window).on('mousemove.lg', function (e) {
                    if (isDraging) {
                        isMoved = true;
                        endCoords = e.pageX;
                        _this.touchMove(startCoords, endCoords);
                        _this.$el.trigger('onDragmove.lg');
                    }
                });

                $(window).on('mouseup.lg', function (e) {
                    if (isMoved) {
                        isMoved = false;
                        _this.touchEnd(endCoords - startCoords);
                        _this.$el.trigger('onDragend.lg');
                    } else if ($(e.target).hasClass('lg-object') || $(e.target).hasClass('lg-video-play')) {
                        _this.$el.trigger('onSlideClick.lg');
                    }

                    // Prevent execution on click
                    if (isDraging) {
                        isDraging = false;
                        _this.$outer.removeClass('lg-grabbing').addClass('lg-grab');
                    }
                });

            }
        };

        Plugin.prototype.manageSwipeClass = function () {
            var _touchNext = this.index + 1;
            var _touchPrev = this.index - 1;
            if (this.s.loop && this.$slide.length > 2) {
                if (this.index === 0) {
                    _touchPrev = this.$slide.length - 1;
                } else if (this.index === this.$slide.length - 1) {
                    _touchNext = 0;
                }
            }

            this.$slide.removeClass('lg-next-slide lg-prev-slide');
            if (_touchPrev > -1) {
                this.$slide.eq(_touchPrev).addClass('lg-prev-slide');
            }

            this.$slide.eq(_touchNext).addClass('lg-next-slide');
        };

        Plugin.prototype.mousewheel = function () {
            var _this = this;
            _this.$outer.on('mousewheel.lg', function (e) {

                if (!e.deltaY) {
                    return;
                }

                if (e.deltaY > 0) {
                    _this.goToPrevSlide();
                } else {
                    _this.goToNextSlide();
                }

                e.preventDefault();
            });

        };

        Plugin.prototype.closeGallery = function () {

            var _this = this;
            var mousedown = false;
            this.$outer.find('.lg-close').on('click.lg', function () {
                _this.destroy();
            });

            if (_this.s.closable) {

                // If you drag the slide and release outside gallery gets close on chrome
                // for preventing this check mousedown and mouseup happened on .lg-item or lg-outer
                _this.$outer.on('mousedown.lg', function (e) {

                    if ($(e.target).is('.lg-outer') || $(e.target).is('.lg-item ') || $(e.target).is('.lg-img-wrap')) {
                        mousedown = true;
                    } else {
                        mousedown = false;
                    }

                });

                _this.$outer.on('mousemove.lg', function () {
                    mousedown = false;
                });

                _this.$outer.on('mouseup.lg', function (e) {

                    if ($(e.target).is('.lg-outer') || $(e.target).is('.lg-item ') || $(e.target).is('.lg-img-wrap') && mousedown) {
                        if (!_this.$outer.hasClass('lg-dragging')) {
                            _this.destroy();
                        }
                    }

                });

            }

        };

        Plugin.prototype.destroy = function (d) {

            var _this = this;

            if (!d) {
                _this.$el.trigger('onBeforeClose.lg');
                $(window).scrollTop(_this.prevScrollTop);
            }


            /**
             * if d is false or undefined destroy will only close the gallery
             * plugins instance remains with the element
             *
             * if d is true destroy will completely remove the plugin
             */

            if (d) {
                if (!_this.s.dynamic) {
                    // only when not using dynamic mode is $items a jquery collection
                    this.$items.off('click.lg click.lgcustom');
                }

                $.removeData(_this.el, 'lightGallery');
            }

            // Unbind all events added by lightGallery
            this.$el.off('.lg.tm');

            // Distroy all lightGallery modules
            $.each($.fn.lightGallery.modules, function (key) {
                if (_this.modules[key]) {
                    _this.modules[key].destroy();
                }
            });

            this.lGalleryOn = false;

            clearTimeout(_this.hideBartimeout);
            this.hideBartimeout = false;
            $(window).off('.lg');
            $('body').removeClass('lg-on lg-from-hash');

            if (_this.$outer) {
                _this.$outer.removeClass('lg-visible');
            }

            $('.lg-backdrop').removeClass('in');

            setTimeout(function () {
                if (_this.$outer) {
                    _this.$outer.remove();
                }

                $('.lg-backdrop').remove();

                if (!d) {
                    _this.$el.trigger('onCloseAfter.lg');
                }

            }, _this.s.backdropDuration + 50);
        };

        $.fn.lightGallery = function (options) {
            return this.each(function () {
                if (!$.data(this, 'lightGallery')) {
                    $.data(this, 'lightGallery', new Plugin(this, options));
                } else {
                    try {
                        $(this).data('lightGallery').init();
                    } catch (err) {
                        console.error('lightGallery has not initiated properly');
                    }
                }
            });
        };

        $.fn.lightGallery.modules = {};

    })();


}));

/*! lg-autoplay - v1.0.4 - 2017-03-28
* http://sachinchoolur.github.io/lightGallery
* Copyright (c) 2017 Sachin N; Licensed GPLv3 */

(function (root, factory) {
    if (typeof define === 'function' && define.amd) {
        // AMD. Register as an anonymous module unless amdModuleId is set
        define(['jquery'], function (a0) {
            return (factory(a0));
        });
    } else if (typeof exports === 'object') {
        // Node. Does not work with strict CommonJS, but
        // only CommonJS-like environments that support module.exports,
        // like Node.
        module.exports = factory(require('jquery'));
    } else {
        factory(jQuery);
    }
}(this, function ($) {


    (function () {

        'use strict';

        var defaults = {
            autoplay: false,
            pause: 5000,
            progressBar: true,
            fourceAutoplay: false,
            autoplayControls: true,
            appendAutoplayControlsTo: '.lg-toolbar'
        };

        /**
         * Creates the autoplay plugin.
         * @param {object} element - lightGallery element
         */
        var Autoplay = function (element) {

            this.core = $(element).data('lightGallery');

            this.$el = $(element);

            // Execute only if items are above 1
            if (this.core.$items.length < 2) {
                return false;
            }

            this.core.s = $.extend({}, defaults, this.core.s);
            this.interval = false;

            // Identify if slide happened from autoplay
            this.fromAuto = true;

            // Identify if autoplay canceled from touch/drag
            this.canceledOnTouch = false;

            // save fourceautoplay value
            this.fourceAutoplayTemp = this.core.s.fourceAutoplay;

            // do not allow progress bar if browser does not support css3 transitions
            if (!this.core.doCss()) {
                this.core.s.progressBar = false;
            }

            this.init();

            return this;
        };

        Autoplay.prototype.init = function () {
            var _this = this;

            // append autoplay controls
            if (_this.core.s.autoplayControls) {
                _this.controls();
            }

            // Create progress bar
            if (_this.core.s.progressBar) {
                _this.core.$outer.find('.lg').append('<div class="lg-progress-bar"><div class="lg-progress"></div></div>');
            }

            // set progress
            _this.progress();

            // Start autoplay
            if (_this.core.s.autoplay) {
                _this.$el.one('onSlideItemLoad.lg.tm', function () {
                    _this.startlAuto();
                });
            }

            // cancel interval on touchstart and dragstart
            _this.$el.on('onDragstart.lg.tm touchstart.lg.tm', function () {
                if (_this.interval) {
                    _this.cancelAuto();
                    _this.canceledOnTouch = true;
                }
            });

            // restore autoplay if autoplay canceled from touchstart / dragstart
            _this.$el.on('onDragend.lg.tm touchend.lg.tm onSlideClick.lg.tm', function () {
                if (!_this.interval && _this.canceledOnTouch) {
                    _this.startlAuto();
                    _this.canceledOnTouch = false;
                }
            });

        };

        Autoplay.prototype.progress = function () {

            var _this = this;
            var _$progressBar;
            var _$progress;

            _this.$el.on('onBeforeSlide.lg.tm', function () {

                // start progress bar animation
                if (_this.core.s.progressBar && _this.fromAuto) {
                    _$progressBar = _this.core.$outer.find('.lg-progress-bar');
                    _$progress = _this.core.$outer.find('.lg-progress');
                    if (_this.interval) {
                        _$progress.removeAttr('style');
                        _$progressBar.removeClass('lg-start');
                        setTimeout(function () {
                            _$progress.css('transition', 'width ' + (_this.core.s.speed + _this.core.s.pause) + 'ms ease 0s');
                            _$progressBar.addClass('lg-start');
                        }, 20);
                    }
                }

                // Remove setinterval if slide is triggered manually and fourceautoplay is false
                if (!_this.fromAuto && !_this.core.s.fourceAutoplay) {
                    _this.cancelAuto();
                }

                _this.fromAuto = false;

            });
        };

        // Manage autoplay via play/stop buttons
        Autoplay.prototype.controls = function () {
            var _this = this;
            var _html = '<span class="lg-autoplay-button lg-icon"></span>';

            // Append autoplay controls
            $(this.core.s.appendAutoplayControlsTo).append(_html);

            _this.core.$outer.find('.lg-autoplay-button').on('click.lg', function () {
                if ($(_this.core.$outer).hasClass('lg-show-autoplay')) {
                    _this.cancelAuto();
                    _this.core.s.fourceAutoplay = false;
                } else {
                    if (!_this.interval) {
                        _this.startlAuto();
                        _this.core.s.fourceAutoplay = _this.fourceAutoplayTemp;
                    }
                }
            });
        };

        // Autostart gallery
        Autoplay.prototype.startlAuto = function () {
            var _this = this;

            _this.core.$outer.find('.lg-progress').css('transition', 'width ' + (_this.core.s.speed + _this.core.s.pause) + 'ms ease 0s');
            _this.core.$outer.addClass('lg-show-autoplay');
            _this.core.$outer.find('.lg-progress-bar').addClass('lg-start');

            _this.interval = setInterval(function () {
                if (_this.core.index + 1 < _this.core.$items.length) {
                    _this.core.index++;
                } else {
                    _this.core.index = 0;
                }

                _this.fromAuto = true;
                _this.core.slide(_this.core.index, false, false, 'next');
            }, _this.core.s.speed + _this.core.s.pause);
        };

        // cancel Autostart
        Autoplay.prototype.cancelAuto = function () {
            clearInterval(this.interval);
            this.interval = false;
            this.core.$outer.find('.lg-progress').removeAttr('style');
            this.core.$outer.removeClass('lg-show-autoplay');
            this.core.$outer.find('.lg-progress-bar').removeClass('lg-start');
        };

        Autoplay.prototype.destroy = function () {

            this.cancelAuto();
            this.core.$outer.find('.lg-progress-bar').remove();
        };

        $.fn.lightGallery.modules.autoplay = Autoplay;

    })();


}));

/*! lg-fullscreen - v1.0.1 - 2016-09-30
* http://sachinchoolur.github.io/lightGallery
* Copyright (c) 2016 Sachin N; Licensed GPLv3 */

(function (root, factory) {
    if (typeof define === 'function' && define.amd) {
        // AMD. Register as an anonymous module unless amdModuleId is set
        define(['jquery'], function (a0) {
            return (factory(a0));
        });
    } else if (typeof exports === 'object') {
        // Node. Does not work with strict CommonJS, but
        // only CommonJS-like environments that support module.exports,
        // like Node.
        module.exports = factory(require('jquery'));
    } else {
        factory(jQuery);
    }
}(this, function ($) {

    (function () {

        'use strict';

        var defaults = {
            fullScreen: true
        };

        var Fullscreen = function (element) {

            // get lightGallery core plugin data
            this.core = $(element).data('lightGallery');

            this.$el = $(element);

            // extend module defalut settings with lightGallery core settings
            this.core.s = $.extend({}, defaults, this.core.s);

            this.init();

            return this;
        };

        Fullscreen.prototype.init = function () {
            var fullScreen = '';
            if (this.core.s.fullScreen) {

                // check for fullscreen browser support
                if (!document.fullscreenEnabled && !document.webkitFullscreenEnabled &&
                    !document.mozFullScreenEnabled && !document.msFullscreenEnabled) {
                    return;
                } else {
                    fullScreen = '<span class="lg-fullscreen lg-icon"></span>';
                    this.core.$outer.find('.lg-toolbar').append(fullScreen);
                    this.fullScreen();
                }
            }
        };

        Fullscreen.prototype.requestFullscreen = function () {
            var el = document.documentElement;
            if (el.requestFullscreen) {
                el.requestFullscreen();
            } else if (el.msRequestFullscreen) {
                el.msRequestFullscreen();
            } else if (el.mozRequestFullScreen) {
                el.mozRequestFullScreen();
            } else if (el.webkitRequestFullscreen) {
                el.webkitRequestFullscreen();
            }
        };

        Fullscreen.prototype.exitFullscreen = function () {
            if (document.exitFullscreen) {
                document.exitFullscreen();
            } else if (document.msExitFullscreen) {
                document.msExitFullscreen();
            } else if (document.mozCancelFullScreen) {
                document.mozCancelFullScreen();
            } else if (document.webkitExitFullscreen) {
                document.webkitExitFullscreen();
            }
        };

        // https://developer.mozilla.org/en-US/docs/Web/Guide/API/DOM/Using_full_screen_mode
        Fullscreen.prototype.fullScreen = function () {
            var _this = this;

            $(document).on('fullscreenchange.lg webkitfullscreenchange.lg mozfullscreenchange.lg MSFullscreenChange.lg', function () {
                _this.core.$outer.toggleClass('lg-fullscreen-on');
            });

            this.core.$outer.find('.lg-fullscreen').on('click.lg', function () {
                if (!document.fullscreenElement &&
                    !document.mozFullScreenElement && !document.webkitFullscreenElement && !document.msFullscreenElement) {
                    _this.requestFullscreen();
                } else {
                    _this.exitFullscreen();
                }
            });

        };

        Fullscreen.prototype.destroy = function () {

            // exit from fullscreen if activated
            this.exitFullscreen();

            $(document).off('fullscreenchange.lg webkitfullscreenchange.lg mozfullscreenchange.lg MSFullscreenChange.lg');
        };

        $.fn.lightGallery.modules.fullscreen = Fullscreen;

    })();

}));

/*! lg-pager - v1.0.2 - 2017-01-22
* http://sachinchoolur.github.io/lightGallery
* Copyright (c) 2017 Sachin N; Licensed GPLv3 */

(function (root, factory) {
    if (typeof define === 'function' && define.amd) {
        // AMD. Register as an anonymous module unless amdModuleId is set
        define(['jquery'], function (a0) {
            return (factory(a0));
        });
    } else if (typeof exports === 'object') {
        // Node. Does not work with strict CommonJS, but
        // only CommonJS-like environments that support module.exports,
        // like Node.
        module.exports = factory(require('jquery'));
    } else {
        factory(jQuery);
    }
}(this, function ($) {

    (function () {

        'use strict';

        var defaults = {
            pager: false
        };

        var Pager = function (element) {

            this.core = $(element).data('lightGallery');

            this.$el = $(element);
            this.core.s = $.extend({}, defaults, this.core.s);
            if (this.core.s.pager && this.core.$items.length > 1) {
                this.init();
            }

            return this;
        };

        Pager.prototype.init = function () {
            var _this = this;
            var pagerList = '';
            var $pagerCont;
            var $pagerOuter;
            var timeout;

            _this.core.$outer.find('.lg').append('<div class="lg-pager-outer"></div>');

            if (_this.core.s.dynamic) {
                for (var i = 0; i < _this.core.s.dynamicEl.length; i++) {
                    pagerList += '<span class="lg-pager-cont"> <span class="lg-pager"></span><div class="lg-pager-thumb-cont"><span class="lg-caret"></span> <img src="' + _this.core.s.dynamicEl[i].thumb + '" /></div></span>';
                }
            } else {
                _this.core.$items.each(function () {

                    if (!_this.core.s.exThumbImage) {
                        pagerList += '<span class="lg-pager-cont"> <span class="lg-pager"></span><div class="lg-pager-thumb-cont"><span class="lg-caret"></span> <img src="' + $(this).find('img').attr('src') + '" /></div></span>';
                    } else {
                        pagerList += '<span class="lg-pager-cont"> <span class="lg-pager"></span><div class="lg-pager-thumb-cont"><span class="lg-caret"></span> <img src="' + $(this).attr(_this.core.s.exThumbImage) + '" /></div></span>';
                    }

                });
            }

            $pagerOuter = _this.core.$outer.find('.lg-pager-outer');

            $pagerOuter.html(pagerList);

            $pagerCont = _this.core.$outer.find('.lg-pager-cont');
            $pagerCont.on('click.lg touchend.lg', function () {
                var _$this = $(this);
                _this.core.index = _$this.index();
                _this.core.slide(_this.core.index, false, true, false);
            });

            $pagerOuter.on('mouseover.lg', function () {
                clearTimeout(timeout);
                $pagerOuter.addClass('lg-pager-hover');
            });

            $pagerOuter.on('mouseout.lg', function () {
                timeout = setTimeout(function () {
                    $pagerOuter.removeClass('lg-pager-hover');
                });
            });

            _this.core.$el.on('onBeforeSlide.lg.tm', function (e, prevIndex, index) {
                $pagerCont.removeClass('lg-pager-active');
                $pagerCont.eq(index).addClass('lg-pager-active');
            });

        };

        Pager.prototype.destroy = function () {

        };

        $.fn.lightGallery.modules.pager = Pager;

    })();


}));

/*! lg-thumbnail - v1.1.0 - 2017-08-08
* http://sachinchoolur.github.io/lightGallery
* Copyright (c) 2017 Sachin N; Licensed GPLv3 */

(function (root, factory) {
    if (typeof define === 'function' && define.amd) {
        // AMD. Register as an anonymous module unless amdModuleId is set
        define(['jquery'], function (a0) {
            return (factory(a0));
        });
    } else if (typeof exports === 'object') {
        // Node. Does not work with strict CommonJS, but
        // only CommonJS-like environments that support module.exports,
        // like Node.
        module.exports = factory(require('jquery'));
    } else {
        factory(jQuery);
    }
}(this, function ($) {

    (function () {

        'use strict';

        var defaults = {
            thumbnail: true,

            animateThumb: true,
            currentPagerPosition: 'middle',

            thumbWidth: 100,
            thumbHeight: '80px',
            thumbContHeight: 100,
            thumbMargin: 5,

            exThumbImage: false,
            showThumbByDefault: true,
            toogleThumb: true,
            pullCaptionUp: true,

            enableThumbDrag: true,
            enableThumbSwipe: true,
            swipeThreshold: 50,

            loadYoutubeThumbnail: true,
            youtubeThumbSize: 1,

            loadVimeoThumbnail: true,
            vimeoThumbSize: 'thumbnail_small',

            loadDailymotionThumbnail: true
        };

        var Thumbnail = function (element) {

            // get lightGallery core plugin data
            this.core = $(element).data('lightGallery');

            // extend module default settings with lightGallery core settings
            this.core.s = $.extend({}, defaults, this.core.s);

            this.$el = $(element);
            this.$thumbOuter = null;
            this.thumbOuterWidth = 0;
            this.thumbTotalWidth = (this.core.$items.length * (this.core.s.thumbWidth + this.core.s.thumbMargin));
            this.thumbIndex = this.core.index;

            if (this.core.s.animateThumb) {
                this.core.s.thumbHeight = '100%';
            }

            // Thumbnail animation value
            this.left = 0;

            this.init();

            return this;
        };

        Thumbnail.prototype.init = function () {
            var _this = this;
            if (this.core.s.thumbnail && this.core.$items.length > 1) {
                if (this.core.s.showThumbByDefault) {
                    setTimeout(function () {
                        _this.core.$outer.addClass('lg-thumb-open');
                    }, 700);
                }

                if (this.core.s.pullCaptionUp) {
                    this.core.$outer.addClass('lg-pull-caption-up');
                }

                this.build();
                if (this.core.s.animateThumb && this.core.doCss()) {
                    if (this.core.s.enableThumbDrag) {
                        this.enableThumbDrag();
                    }

                    if (this.core.s.enableThumbSwipe) {
                        this.enableThumbSwipe();
                    }

                    this.thumbClickable = false;
                } else {
                    this.thumbClickable = true;
                }

                this.toogle();
                this.thumbkeyPress();
            }
        };

        Thumbnail.prototype.build = function () {
            var _this = this;
            var thumbList = '';
            var vimeoErrorThumbSize = '';
            var $thumb;
            var html = '<div class="lg-thumb-outer">' +
                '<div class="lg-thumb lg-group">' +
                '</div>' +
                '</div>';

            switch (this.core.s.vimeoThumbSize) {
                case 'thumbnail_large':
                    vimeoErrorThumbSize = '640';
                    break;
                case 'thumbnail_medium':
                    vimeoErrorThumbSize = '200x150';
                    break;
                case 'thumbnail_small':
                    vimeoErrorThumbSize = '100x75';
            }

            _this.core.$outer.addClass('lg-has-thumb');

            _this.core.$outer.find('.lg').append(html);

            _this.$thumbOuter = _this.core.$outer.find('.lg-thumb-outer');
            _this.thumbOuterWidth = _this.$thumbOuter.width();

            if (_this.core.s.animateThumb) {
                _this.core.$outer.find('.lg-thumb').css({
                    width: _this.thumbTotalWidth + 'px',
                    position: 'relative'
                });
            }

            if (this.core.s.animateThumb) {
                _this.$thumbOuter.css('height', _this.core.s.thumbContHeight + 'px');
            }

            function getThumb(src, thumb, index) {
                var isVideo = _this.core.isVideo(src, index) || {};
                var thumbImg;
                var vimeoId = '';

                if (isVideo.youtube || isVideo.vimeo || isVideo.dailymotion) {
                    if (isVideo.youtube) {
                        if (_this.core.s.loadYoutubeThumbnail) {
                            thumbImg = '//img.youtube.com/vi/' + isVideo.youtube[1] + '/' + _this.core.s.youtubeThumbSize + '.jpg';
                        } else {
                            thumbImg = thumb;
                        }
                    } else if (isVideo.vimeo) {
                        if (_this.core.s.loadVimeoThumbnail) {
                            thumbImg = '//i.vimeocdn.com/video/error_' + vimeoErrorThumbSize + '.jpg';
                            vimeoId = isVideo.vimeo[1];
                        } else {
                            thumbImg = thumb;
                        }
                    } else if (isVideo.dailymotion) {
                        if (_this.core.s.loadDailymotionThumbnail) {
                            thumbImg = '//www.dailymotion.com/thumbnail/video/' + isVideo.dailymotion[1];
                        } else {
                            thumbImg = thumb;
                        }
                    }
                } else {
                    thumbImg = thumb;
                }

                thumbList += '<div data-vimeo-id="' + vimeoId + '" class="lg-thumb-item" style="width:' + _this.core.s.thumbWidth + 'px; height: ' + _this.core.s.thumbHeight + '; margin-right: ' + _this.core.s.thumbMargin + 'px"><img src="' + thumbImg + '" /></div>';
                vimeoId = '';
            }

            if (_this.core.s.dynamic) {
                for (var i = 0; i < _this.core.s.dynamicEl.length; i++) {
                    getThumb(_this.core.s.dynamicEl[i].src, _this.core.s.dynamicEl[i].thumb, i);
                }
            } else {
                _this.core.$items.each(function (i) {

                    if (!_this.core.s.exThumbImage) {
                        getThumb($(this).attr('href') || $(this).attr('data-src'), $(this).find('img').attr('src'), i);
                    } else {
                        getThumb($(this).attr('href') || $(this).attr('data-src'), $(this).attr(_this.core.s.exThumbImage), i);
                    }

                });
            }

            _this.core.$outer.find('.lg-thumb').html(thumbList);

            $thumb = _this.core.$outer.find('.lg-thumb-item');

            // Load vimeo thumbnails
            $thumb.each(function () {
                var $this = $(this);
                var vimeoVideoId = $this.attr('data-vimeo-id');

                if (vimeoVideoId) {
                    $.getJSON('//www.vimeo.com/api/v2/video/' + vimeoVideoId + '.json?callback=?', {
                        format: 'json'
                    }, function (data) {
                        $this.find('img').attr('src', data[0][_this.core.s.vimeoThumbSize]);
                    });
                }
            });

            // manage active class for thumbnail
            $thumb.eq(_this.core.index).addClass('active');
            _this.core.$el.on('onBeforeSlide.lg.tm', function () {
                $thumb.removeClass('active');
                $thumb.eq(_this.core.index).addClass('active');
            });

            $thumb.on('click.lg touchend.lg', function () {
                var _$this = $(this);
                setTimeout(function () {

                    // In IE9 and bellow touch does not support
                    // Go to slide if browser does not support css transitions
                    if ((_this.thumbClickable && !_this.core.lgBusy) || !_this.core.doCss()) {
                        _this.core.index = _$this.index();
                        _this.core.slide(_this.core.index, false, true, false);
                    }
                }, 50);
            });

            _this.core.$el.on('onBeforeSlide.lg.tm', function () {
                _this.animateThumb(_this.core.index);
            });

            $(window).on('resize.lg.thumb orientationchange.lg.thumb', function () {
                setTimeout(function () {
                    _this.animateThumb(_this.core.index);
                    _this.thumbOuterWidth = _this.$thumbOuter.width();
                }, 200);
            });

        };

        Thumbnail.prototype.setTranslate = function (value) {
            // jQuery supports Automatic CSS prefixing since jQuery 1.8.0
            this.core.$outer.find('.lg-thumb').css({
                transform: 'translate3d(-' + (value) + 'px, 0px, 0px)'
            });
        };

        Thumbnail.prototype.animateThumb = function (index) {
            var $thumb = this.core.$outer.find('.lg-thumb');
            if (this.core.s.animateThumb) {
                var position;
                switch (this.core.s.currentPagerPosition) {
                    case 'left':
                        position = 0;
                        break;
                    case 'middle':
                        position = (this.thumbOuterWidth / 2) - (this.core.s.thumbWidth / 2);
                        break;
                    case 'right':
                        position = this.thumbOuterWidth - this.core.s.thumbWidth;
                }
                this.left = ((this.core.s.thumbWidth + this.core.s.thumbMargin) * index - 1) - position;
                if (this.left > (this.thumbTotalWidth - this.thumbOuterWidth)) {
                    this.left = this.thumbTotalWidth - this.thumbOuterWidth;
                }

                if (this.left < 0) {
                    this.left = 0;
                }

                if (this.core.lGalleryOn) {
                    if (!$thumb.hasClass('on')) {
                        this.core.$outer.find('.lg-thumb').css('transition-duration', this.core.s.speed + 'ms');
                    }

                    if (!this.core.doCss()) {
                        $thumb.animate({
                            left: -this.left + 'px'
                        }, this.core.s.speed);
                    }
                } else {
                    if (!this.core.doCss()) {
                        $thumb.css('left', -this.left + 'px');
                    }
                }

                this.setTranslate(this.left);

            }
        };

        // Enable thumbnail dragging and swiping
        Thumbnail.prototype.enableThumbDrag = function () {

            var _this = this;
            var startCoords = 0;
            var endCoords = 0;
            var isDraging = false;
            var isMoved = false;
            var tempLeft = 0;

            _this.$thumbOuter.addClass('lg-grab');

            _this.core.$outer.find('.lg-thumb').on('mousedown.lg.thumb', function (e) {
                if (_this.thumbTotalWidth > _this.thumbOuterWidth) {
                    // execute only on .lg-object
                    e.preventDefault();
                    startCoords = e.pageX;
                    isDraging = true;

                    // ** Fix for webkit cursor issue https://code.google.com/p/chromium/issues/detail?id=26723
                    _this.core.$outer.scrollLeft += 1;
                    _this.core.$outer.scrollLeft -= 1;

                    // *
                    _this.thumbClickable = false;
                    _this.$thumbOuter.removeClass('lg-grab').addClass('lg-grabbing');
                }
            });

            $(window).on('mousemove.lg.thumb', function (e) {
                if (isDraging) {
                    tempLeft = _this.left;
                    isMoved = true;
                    endCoords = e.pageX;

                    _this.$thumbOuter.addClass('lg-dragging');

                    tempLeft = tempLeft - (endCoords - startCoords);

                    if (tempLeft > (_this.thumbTotalWidth - _this.thumbOuterWidth)) {
                        tempLeft = _this.thumbTotalWidth - _this.thumbOuterWidth;
                    }

                    if (tempLeft < 0) {
                        tempLeft = 0;
                    }

                    // move current slide
                    _this.setTranslate(tempLeft);

                }
            });

            $(window).on('mouseup.lg.thumb', function () {
                if (isMoved) {
                    isMoved = false;
                    _this.$thumbOuter.removeClass('lg-dragging');

                    _this.left = tempLeft;

                    if (Math.abs(endCoords - startCoords) < _this.core.s.swipeThreshold) {
                        _this.thumbClickable = true;
                    }

                } else {
                    _this.thumbClickable = true;
                }

                if (isDraging) {
                    isDraging = false;
                    _this.$thumbOuter.removeClass('lg-grabbing').addClass('lg-grab');
                }
            });

        };

        Thumbnail.prototype.enableThumbSwipe = function () {
            var _this = this;
            var startCoords = 0;
            var endCoords = 0;
            var isMoved = false;
            var tempLeft = 0;

            _this.core.$outer.find('.lg-thumb').on('touchstart.lg', function (e) {
                if (_this.thumbTotalWidth > _this.thumbOuterWidth) {
                    e.preventDefault();
                    startCoords = e.originalEvent.targetTouches[0].pageX;
                    _this.thumbClickable = false;
                }
            });

            _this.core.$outer.find('.lg-thumb').on('touchmove.lg', function (e) {
                if (_this.thumbTotalWidth > _this.thumbOuterWidth) {
                    e.preventDefault();
                    endCoords = e.originalEvent.targetTouches[0].pageX;
                    isMoved = true;

                    _this.$thumbOuter.addClass('lg-dragging');

                    tempLeft = _this.left;

                    tempLeft = tempLeft - (endCoords - startCoords);

                    if (tempLeft > (_this.thumbTotalWidth - _this.thumbOuterWidth)) {
                        tempLeft = _this.thumbTotalWidth - _this.thumbOuterWidth;
                    }

                    if (tempLeft < 0) {
                        tempLeft = 0;
                    }

                    // move current slide
                    _this.setTranslate(tempLeft);

                }
            });

            _this.core.$outer.find('.lg-thumb').on('touchend.lg', function () {
                if (_this.thumbTotalWidth > _this.thumbOuterWidth) {

                    if (isMoved) {
                        isMoved = false;
                        _this.$thumbOuter.removeClass('lg-dragging');
                        if (Math.abs(endCoords - startCoords) < _this.core.s.swipeThreshold) {
                            _this.thumbClickable = true;
                        }

                        _this.left = tempLeft;
                    } else {
                        _this.thumbClickable = true;
                    }
                } else {
                    _this.thumbClickable = true;
                }
            });

        };

        Thumbnail.prototype.toogle = function () {
            var _this = this;
            if (_this.core.s.toogleThumb) {
                _this.core.$outer.addClass('lg-can-toggle');
                _this.$thumbOuter.append('<span class="lg-toogle-thumb lg-icon"></span>');
                _this.core.$outer.find('.lg-toogle-thumb').on('click.lg', function () {
                    _this.core.$outer.toggleClass('lg-thumb-open');
                });
            }
        };

        Thumbnail.prototype.thumbkeyPress = function () {
            var _this = this;
            $(window).on('keydown.lg.thumb', function (e) {
                if (e.keyCode === 38) {
                    e.preventDefault();
                    _this.core.$outer.addClass('lg-thumb-open');
                } else if (e.keyCode === 40) {
                    e.preventDefault();
                    _this.core.$outer.removeClass('lg-thumb-open');
                }
            });
        };

        Thumbnail.prototype.destroy = function () {
            if (this.core.s.thumbnail && this.core.$items.length > 1) {
                $(window).off('resize.lg.thumb orientationchange.lg.thumb keydown.lg.thumb');
                this.$thumbOuter.remove();
                this.core.$outer.removeClass('lg-has-thumb');
            }
        };

        $.fn.lightGallery.modules.Thumbnail = Thumbnail;

    })();

}));

/*! lg-video - v1.2.2 - 2018-05-01
* http://sachinchoolur.github.io/lightGallery
* Copyright (c) 2018 Sachin N; Licensed GPLv3 */

(function (root, factory) {
    if (typeof define === 'function' && define.amd) {
        // AMD. Register as an anonymous module unless amdModuleId is set
        define(['jquery'], function (a0) {
            return (factory(a0));
        });
    } else if (typeof module === 'object' && module.exports) {
        // Node. Does not work with strict CommonJS, but
        // only CommonJS-like environments that support module.exports,
        // like Node.
        module.exports = factory(require('jquery'));
    } else {
        factory(root["jQuery"]);
    }
}(this, function ($) {

    (function () {

        'use strict';

        var defaults = {
            videoMaxWidth: '855px',

            autoplayFirstVideo: true,

            youtubePlayerParams: false,
            vimeoPlayerParams: false,
            dailymotionPlayerParams: false,
            vkPlayerParams: false,

            videojs: false,
            videojsOptions: {}
        };

        var Video = function (element) {

            this.core = $(element).data('lightGallery');

            this.$el = $(element);
            this.core.s = $.extend({}, defaults, this.core.s);
            this.videoLoaded = false;

            this.init();

            return this;
        };

        Video.prototype.init = function () {
            var _this = this;

            // Event triggered when video url found without poster
            _this.core.$el.on('hasVideo.lg.tm', onHasVideo.bind(this));

            // Set max width for video
            _this.core.$el.on('onAferAppendSlide.lg.tm', onAferAppendSlide.bind(this));

            if (_this.core.doCss() && (_this.core.$items.length > 1) && (_this.core.s.enableSwipe || _this.core.s.enableDrag)) {
                _this.core.$el.on('onSlideClick.lg.tm', function () {
                    var $el = _this.core.$slide.eq(_this.core.index);
                    _this.loadVideoOnclick($el);
                });
            } else {

                // For IE 9 and bellow
                _this.core.$slide.on('click.lg', function () {
                    _this.loadVideoOnclick($(this));
                });
            }

            _this.core.$el.on('onBeforeSlide.lg.tm', onBeforeSlide.bind(this));

            _this.core.$el.on('onAfterSlide.lg.tm', function (event, prevIndex) {
                _this.core.$slide.eq(prevIndex).removeClass('lg-video-playing');
            });

            if (_this.core.s.autoplayFirstVideo) {
                _this.core.$el.on('onAferAppendSlide.lg.tm', function (e, index) {
                    if (!_this.core.lGalleryOn) {
                        var $el = _this.core.$slide.eq(index);
                        setTimeout(function () {
                            _this.loadVideoOnclick($el);
                        }, 100);
                    }
                });
            }
        };

        Video.prototype.loadVideo = function (src, addClass, noPoster, index, html) {
            var video = '';
            var autoplay = 1;
            var a = '';
            var isVideo = this.core.isVideo(src, index) || {};

            // Enable autoplay based on setting for first video if poster doesn't exist
            if (noPoster) {
                if (this.videoLoaded) {
                    autoplay = 0;
                } else {
                    autoplay = this.core.s.autoplayFirstVideo ? 1 : 0;
                }
            }

            if (isVideo.youtube) {

                a = '?wmode=opaque&autoplay=' + autoplay + '&enablejsapi=1';
                if (this.core.s.youtubePlayerParams) {
                    a = a + '&' + $.param(this.core.s.youtubePlayerParams);
                }

                video = '<iframe class="lg-video-object lg-youtube ' + addClass + '" width="560" height="315" src="//www.youtube.com/embed/' + isVideo.youtube[1] + a + '" frameborder="0" allowfullscreen></iframe>';

            } else if (isVideo.vimeo) {

                a = '?autoplay=' + autoplay + '&api=1';
                if (this.core.s.vimeoPlayerParams) {
                    a = a + '&' + $.param(this.core.s.vimeoPlayerParams);
                }

                video = '<iframe class="lg-video-object lg-vimeo ' + addClass + '" width="560" height="315"  src="//player.vimeo.com/video/' + isVideo.vimeo[1] + a + '" frameborder="0" webkitAllowFullScreen mozallowfullscreen allowFullScreen></iframe>';

            } else if (isVideo.dailymotion) {

                a = '?wmode=opaque&autoplay=' + autoplay + '&api=postMessage';
                if (this.core.s.dailymotionPlayerParams) {
                    a = a + '&' + $.param(this.core.s.dailymotionPlayerParams);
                }

                video = '<iframe class="lg-video-object lg-dailymotion ' + addClass + '" width="560" height="315" src="//www.dailymotion.com/embed/video/' + isVideo.dailymotion[1] + a + '" frameborder="0" allowfullscreen></iframe>';

            } else if (isVideo.html5) {
                var fL = html.substring(0, 1);
                if (fL === '.' || fL === '#') {
                    html = $(html).html();
                }

                video = html;

            } else if (isVideo.vk) {

                a = '&autoplay=' + autoplay;
                if (this.core.s.vkPlayerParams) {
                    a = a + '&' + $.param(this.core.s.vkPlayerParams);
                }

                video = '<iframe class="lg-video-object lg-vk ' + addClass + '" width="560" height="315" src="//vk.com/video_ext.php?' + isVideo.vk[1] + a + '" frameborder="0" allowfullscreen></iframe>';

            }

            return video;
        };

        Video.prototype.loadVideoOnclick = function ($el) {

            var _this = this;
            // check slide has poster
            if ($el.find('.lg-object').hasClass('lg-has-poster') && $el.find('.lg-object').is(':visible')) {

                // check already video element present
                if (!$el.hasClass('lg-has-video')) {

                    $el.addClass('lg-video-playing lg-has-video');

                    var _src;
                    var _html;
                    var _loadVideo = function (_src, _html) {

                        $el.find('.lg-video').append(_this.loadVideo(_src, '', false, _this.core.index, _html));

                        if (_html) {
                            if (_this.core.s.videojs) {
                                try {
                                    videojs(_this.core.$slide.eq(_this.core.index).find('.lg-html5').get(0), _this.core.s.videojsOptions, function () {
                                        this.play();
                                    });
                                } catch (e) {
                                    console.error('Make sure you have included videojs');
                                }
                            } else {
                                _this.core.$slide.eq(_this.core.index).find('.lg-html5').get(0).play();
                            }
                        }

                    };

                    if (_this.core.s.dynamic) {

                        _src = _this.core.s.dynamicEl[_this.core.index].src;
                        _html = _this.core.s.dynamicEl[_this.core.index].html;

                        _loadVideo(_src, _html);

                    } else {

                        _src = _this.core.$items.eq(_this.core.index).attr('href') || _this.core.$items.eq(_this.core.index).attr('data-src');
                        _html = _this.core.$items.eq(_this.core.index).attr('data-html');

                        _loadVideo(_src, _html);

                    }

                    var $tempImg = $el.find('.lg-object');
                    $el.find('.lg-video').append($tempImg);

                    // @todo loading icon for html5 videos also
                    // for showing the loading indicator while loading video
                    if (!$el.find('.lg-video-object').hasClass('lg-html5')) {
                        $el.removeClass('lg-complete');
                        $el.find('.lg-video-object').on('load.lg error.lg', function () {
                            $el.addClass('lg-complete');
                        });
                    }

                } else {

                    var youtubePlayer = $el.find('.lg-youtube').get(0);
                    var vimeoPlayer = $el.find('.lg-vimeo').get(0);
                    var dailymotionPlayer = $el.find('.lg-dailymotion').get(0);
                    var html5Player = $el.find('.lg-html5').get(0);
                    if (youtubePlayer) {
                        youtubePlayer.contentWindow.postMessage('{"event":"command","func":"playVideo","args":""}', '*');
                    } else if (vimeoPlayer) {
                        try {
                            $f(vimeoPlayer).api('play');
                        } catch (e) {
                            console.error('Make sure you have included froogaloop2 js');
                        }
                    } else if (dailymotionPlayer) {
                        dailymotionPlayer.contentWindow.postMessage('play', '*');

                    } else if (html5Player) {
                        if (_this.core.s.videojs) {
                            try {
                                videojs(html5Player).play();
                            } catch (e) {
                                console.error('Make sure you have included videojs');
                            }
                        } else {
                            html5Player.play();
                        }
                    }

                    $el.addClass('lg-video-playing');

                }
            }
        };

        Video.prototype.destroy = function () {
            this.videoLoaded = false;
        };

        function onHasVideo(event, index, src, html) {
            /*jshint validthis:true */
            var _this = this;
            _this.core.$slide.eq(index).find('.lg-video').append(_this.loadVideo(src, 'lg-object', true, index, html));
            if (html) {
                if (_this.core.s.videojs) {
                    try {
                        videojs(_this.core.$slide.eq(index).find('.lg-html5').get(0), _this.core.s.videojsOptions, function () {
                            if (!_this.videoLoaded && _this.core.s.autoplayFirstVideo) {
                                this.play();
                            }
                        });
                    } catch (e) {
                        console.error('Make sure you have included videojs');
                    }
                } else {
                    if (!_this.videoLoaded && _this.core.s.autoplayFirstVideo) {
                        _this.core.$slide.eq(index).find('.lg-html5').get(0).play();
                    }
                }
            }
        }

        function onAferAppendSlide(event, index) {
            /*jshint validthis:true */
            var $videoCont = this.core.$slide.eq(index).find('.lg-video-cont');
            if (!$videoCont.hasClass('lg-has-iframe')) {
                $videoCont.css('max-width', this.core.s.videoMaxWidth);
                this.videoLoaded = true;
            }
        }

        function onBeforeSlide(event, prevIndex, index) {
            /*jshint validthis:true */
            var _this = this;

            var $videoSlide = _this.core.$slide.eq(prevIndex);
            var youtubePlayer = $videoSlide.find('.lg-youtube').get(0);
            var vimeoPlayer = $videoSlide.find('.lg-vimeo').get(0);
            var dailymotionPlayer = $videoSlide.find('.lg-dailymotion').get(0);
            var vkPlayer = $videoSlide.find('.lg-vk').get(0);
            var html5Player = $videoSlide.find('.lg-html5').get(0);
            if (youtubePlayer) {
                youtubePlayer.contentWindow.postMessage('{"event":"command","func":"pauseVideo","args":""}', '*');
            } else if (vimeoPlayer) {
                try {
                    $f(vimeoPlayer).api('pause');
                } catch (e) {
                    console.error('Make sure you have included froogaloop2 js');
                }
            } else if (dailymotionPlayer) {
                dailymotionPlayer.contentWindow.postMessage('pause', '*');

            } else if (html5Player) {
                if (_this.core.s.videojs) {
                    try {
                        videojs(html5Player).pause();
                    } catch (e) {
                        console.error('Make sure you have included videojs');
                    }
                } else {
                    html5Player.pause();
                }
            } if (vkPlayer) {
                $(vkPlayer).attr('src', $(vkPlayer).attr('src').replace('&autoplay', '&noplay'));
            }

            var _src;
            if (_this.core.s.dynamic) {
                _src = _this.core.s.dynamicEl[index].src;
            } else {
                _src = _this.core.$items.eq(index).attr('href') || _this.core.$items.eq(index).attr('data-src');

            }

            var _isVideo = _this.core.isVideo(_src, index) || {};
            if (_isVideo.youtube || _isVideo.vimeo || _isVideo.dailymotion || _isVideo.vk) {
                _this.core.$outer.addClass('lg-hide-download');
            }

        }

        $.fn.lightGallery.modules.video = Video;

    })();

}));

/*! lg-zoom - v1.1.0 - 2017-08-08
* http://sachinchoolur.github.io/lightGallery
* Copyright (c) 2017 Sachin N; Licensed GPLv3 */

(function (root, factory) {
    if (typeof define === 'function' && define.amd) {
        // AMD. Register as an anonymous module unless amdModuleId is set
        define(['jquery'], function (a0) {
            return (factory(a0));
        });
    } else if (typeof exports === 'object') {
        // Node. Does not work with strict CommonJS, but
        // only CommonJS-like environments that support module.exports,
        // like Node.
        module.exports = factory(require('jquery'));
    } else {
        factory(jQuery);
    }
}(this, function ($) {

    (function () {

        'use strict';

        var getUseLeft = function () {
            var useLeft = false;
            var isChrome = navigator.userAgent.match(/Chrom(e|ium)\/([0-9]+)\./);
            if (isChrome && parseInt(isChrome[2], 10) < 54) {
                useLeft = true;
            }

            return useLeft;
        };

        var defaults = {
            scale: 1,
            zoom: true,
            actualSize: true,
            enableZoomAfter: 300,
            useLeftForZoom: getUseLeft()
        };

        var Zoom = function (element) {

            this.core = $(element).data('lightGallery');

            this.core.s = $.extend({}, defaults, this.core.s);

            if (this.core.s.zoom && this.core.doCss()) {
                this.init();

                // Store the zoomable timeout value just to clear it while closing
                this.zoomabletimeout = false;

                // Set the initial value center
                this.pageX = $(window).width() / 2;
                this.pageY = ($(window).height() / 2) + $(window).scrollTop();
            }

            return this;
        };

        Zoom.prototype.init = function () {

            var _this = this;
            var zoomIcons = '<span id="lg-zoom-in" class="lg-icon"></span><span id="lg-zoom-out" class="lg-icon"></span>';

            if (_this.core.s.actualSize) {
                zoomIcons += '<span id="lg-actual-size" class="lg-icon"></span>';
            }

            if (_this.core.s.useLeftForZoom) {
                _this.core.$outer.addClass('lg-use-left-for-zoom');
            } else {
                _this.core.$outer.addClass('lg-use-transition-for-zoom');
            }

            this.core.$outer.find('.lg-toolbar').append(zoomIcons);

            // Add zoomable class
            _this.core.$el.on('onSlideItemLoad.lg.tm.zoom', function (event, index, delay) {

                // delay will be 0 except first time
                var _speed = _this.core.s.enableZoomAfter + delay;

                // set _speed value 0 if gallery opened from direct url and if it is first slide
                if ($('body').hasClass('lg-from-hash') && delay) {

                    // will execute only once
                    _speed = 0;
                } else {

                    // Remove lg-from-hash to enable starting animation.
                    $('body').removeClass('lg-from-hash');
                }

                _this.zoomabletimeout = setTimeout(function () {
                    _this.core.$slide.eq(index).addClass('lg-zoomable');
                }, _speed + 30);
            });

            var scale = 1;
            /**
             * @desc Image zoom
             * Translate the wrap and scale the image to get better user experience
             *
             * @param {String} scaleVal - Zoom decrement/increment value
             */
            var zoom = function (scaleVal) {

                var $image = _this.core.$outer.find('.lg-current .lg-image');
                var _x;
                var _y;

                // Find offset manually to avoid issue after zoom
                var offsetX = ($(window).width() - $image.prop('offsetWidth')) / 2;
                var offsetY = (($(window).height() - $image.prop('offsetHeight')) / 2) + $(window).scrollTop();

                _x = _this.pageX - offsetX;
                _y = _this.pageY - offsetY;

                var x = (scaleVal - 1) * (_x);
                var y = (scaleVal - 1) * (_y);

                $image.css('transform', 'scale3d(' + scaleVal + ', ' + scaleVal + ', 1)').attr('data-scale', scaleVal);

                if (_this.core.s.useLeftForZoom) {
                    $image.parent().css({
                        left: -x + 'px',
                        top: -y + 'px'
                    }).attr('data-x', x).attr('data-y', y);
                } else {
                    $image.parent().css('transform', 'translate3d(-' + x + 'px, -' + y + 'px, 0)').attr('data-x', x).attr('data-y', y);
                }
            };

            var callScale = function () {
                if (scale > 1) {
                    _this.core.$outer.addClass('lg-zoomed');
                } else {
                    _this.resetZoom();
                }

                if (scale < 1) {
                    scale = 1;
                }

                zoom(scale);
            };

            var actualSize = function (event, $image, index, fromIcon) {
                var w = $image.prop('offsetWidth');
                var nw;
                if (_this.core.s.dynamic) {
                    nw = _this.core.s.dynamicEl[index].width || $image[0].naturalWidth || w;
                } else {
                    nw = _this.core.$items.eq(index).attr('data-width') || $image[0].naturalWidth || w;
                }

                var _scale;

                if (_this.core.$outer.hasClass('lg-zoomed')) {
                    scale = 1;
                } else {
                    if (nw > w) {
                        _scale = nw / w;
                        scale = _scale || 2;
                    }
                }

                if (fromIcon) {
                    _this.pageX = $(window).width() / 2;
                    _this.pageY = ($(window).height() / 2) + $(window).scrollTop();
                } else {
                    _this.pageX = event.pageX || event.originalEvent.targetTouches[0].pageX;
                    _this.pageY = event.pageY || event.originalEvent.targetTouches[0].pageY;
                }

                callScale();
                setTimeout(function () {
                    _this.core.$outer.removeClass('lg-grabbing').addClass('lg-grab');
                }, 10);
            };

            var tapped = false;

            // event triggered after appending slide content
            _this.core.$el.on('onAferAppendSlide.lg.tm.zoom', function (event, index) {

                // Get the current element
                var $image = _this.core.$slide.eq(index).find('.lg-image');

                $image.on('dblclick', function (event) {
                    actualSize(event, $image, index);
                });

                $image.on('touchstart', function (event) {
                    if (!tapped) {
                        tapped = setTimeout(function () {
                            tapped = null;
                        }, 300);
                    } else {
                        clearTimeout(tapped);
                        tapped = null;
                        actualSize(event, $image, index);
                    }

                    event.preventDefault();
                });

            });

            // Update zoom on resize and orientationchange
            $(window).on('resize.lg.zoom scroll.lg.zoom orientationchange.lg.zoom', function () {
                _this.pageX = $(window).width() / 2;
                _this.pageY = ($(window).height() / 2) + $(window).scrollTop();
                zoom(scale);
            });

            $('#lg-zoom-out').on('click.lg', function () {
                if (_this.core.$outer.find('.lg-current .lg-image').length) {
                    scale -= _this.core.s.scale;
                    callScale();
                }
            });

            $('#lg-zoom-in').on('click.lg', function () {
                if (_this.core.$outer.find('.lg-current .lg-image').length) {
                    scale += _this.core.s.scale;
                    callScale();
                }
            });

            $('#lg-actual-size').on('click.lg', function (event) {
                actualSize(event, _this.core.$slide.eq(_this.core.index).find('.lg-image'), _this.core.index, true);
            });

            // Reset zoom on slide change
            _this.core.$el.on('onBeforeSlide.lg.tm', function () {
                scale = 1;
                _this.resetZoom();
            });

            // Drag option after zoom
            _this.zoomDrag();

            _this.zoomSwipe();

        };

        // Reset zoom effect
        Zoom.prototype.resetZoom = function () {
            this.core.$outer.removeClass('lg-zoomed');
            this.core.$slide.find('.lg-img-wrap').removeAttr('style data-x data-y');
            this.core.$slide.find('.lg-image').removeAttr('style data-scale');

            // Reset pagx pagy values to center
            this.pageX = $(window).width() / 2;
            this.pageY = ($(window).height() / 2) + $(window).scrollTop();
        };

        Zoom.prototype.zoomSwipe = function () {
            var _this = this;
            var startCoords = {};
            var endCoords = {};
            var isMoved = false;

            // Allow x direction drag
            var allowX = false;

            // Allow Y direction drag
            var allowY = false;

            _this.core.$slide.on('touchstart.lg', function (e) {

                if (_this.core.$outer.hasClass('lg-zoomed')) {
                    var $image = _this.core.$slide.eq(_this.core.index).find('.lg-object');

                    allowY = $image.prop('offsetHeight') * $image.attr('data-scale') > _this.core.$outer.find('.lg').height();
                    allowX = $image.prop('offsetWidth') * $image.attr('data-scale') > _this.core.$outer.find('.lg').width();
                    if ((allowX || allowY)) {
                        e.preventDefault();
                        startCoords = {
                            x: e.originalEvent.targetTouches[0].pageX,
                            y: e.originalEvent.targetTouches[0].pageY
                        };
                    }
                }

            });

            _this.core.$slide.on('touchmove.lg', function (e) {

                if (_this.core.$outer.hasClass('lg-zoomed')) {

                    var _$el = _this.core.$slide.eq(_this.core.index).find('.lg-img-wrap');
                    var distanceX;
                    var distanceY;

                    e.preventDefault();
                    isMoved = true;

                    endCoords = {
                        x: e.originalEvent.targetTouches[0].pageX,
                        y: e.originalEvent.targetTouches[0].pageY
                    };

                    // reset opacity and transition duration
                    _this.core.$outer.addClass('lg-zoom-dragging');

                    if (allowY) {
                        distanceY = (-Math.abs(_$el.attr('data-y'))) + (endCoords.y - startCoords.y);
                    } else {
                        distanceY = -Math.abs(_$el.attr('data-y'));
                    }

                    if (allowX) {
                        distanceX = (-Math.abs(_$el.attr('data-x'))) + (endCoords.x - startCoords.x);
                    } else {
                        distanceX = -Math.abs(_$el.attr('data-x'));
                    }

                    if ((Math.abs(endCoords.x - startCoords.x) > 15) || (Math.abs(endCoords.y - startCoords.y) > 15)) {

                        if (_this.core.s.useLeftForZoom) {
                            _$el.css({
                                left: distanceX + 'px',
                                top: distanceY + 'px'
                            });
                        } else {
                            _$el.css('transform', 'translate3d(' + distanceX + 'px, ' + distanceY + 'px, 0)');
                        }
                    }

                }

            });

            _this.core.$slide.on('touchend.lg', function () {
                if (_this.core.$outer.hasClass('lg-zoomed')) {
                    if (isMoved) {
                        isMoved = false;
                        _this.core.$outer.removeClass('lg-zoom-dragging');
                        _this.touchendZoom(startCoords, endCoords, allowX, allowY);

                    }
                }
            });

        };

        Zoom.prototype.zoomDrag = function () {

            var _this = this;
            var startCoords = {};
            var endCoords = {};
            var isDraging = false;
            var isMoved = false;

            // Allow x direction drag
            var allowX = false;

            // Allow Y direction drag
            var allowY = false;

            _this.core.$slide.on('mousedown.lg.zoom', function (e) {

                // execute only on .lg-object
                var $image = _this.core.$slide.eq(_this.core.index).find('.lg-object');

                allowY = $image.prop('offsetHeight') * $image.attr('data-scale') > _this.core.$outer.find('.lg').height();
                allowX = $image.prop('offsetWidth') * $image.attr('data-scale') > _this.core.$outer.find('.lg').width();

                if (_this.core.$outer.hasClass('lg-zoomed')) {
                    if ($(e.target).hasClass('lg-object') && (allowX || allowY)) {
                        e.preventDefault();
                        startCoords = {
                            x: e.pageX,
                            y: e.pageY
                        };

                        isDraging = true;

                        // ** Fix for webkit cursor issue https://code.google.com/p/chromium/issues/detail?id=26723
                        _this.core.$outer.scrollLeft += 1;
                        _this.core.$outer.scrollLeft -= 1;

                        _this.core.$outer.removeClass('lg-grab').addClass('lg-grabbing');
                    }
                }
            });

            $(window).on('mousemove.lg.zoom', function (e) {
                if (isDraging) {
                    var _$el = _this.core.$slide.eq(_this.core.index).find('.lg-img-wrap');
                    var distanceX;
                    var distanceY;

                    isMoved = true;
                    endCoords = {
                        x: e.pageX,
                        y: e.pageY
                    };

                    // reset opacity and transition duration
                    _this.core.$outer.addClass('lg-zoom-dragging');

                    if (allowY) {
                        distanceY = (-Math.abs(_$el.attr('data-y'))) + (endCoords.y - startCoords.y);
                    } else {
                        distanceY = -Math.abs(_$el.attr('data-y'));
                    }

                    if (allowX) {
                        distanceX = (-Math.abs(_$el.attr('data-x'))) + (endCoords.x - startCoords.x);
                    } else {
                        distanceX = -Math.abs(_$el.attr('data-x'));
                    }

                    if (_this.core.s.useLeftForZoom) {
                        _$el.css({
                            left: distanceX + 'px',
                            top: distanceY + 'px'
                        });
                    } else {
                        _$el.css('transform', 'translate3d(' + distanceX + 'px, ' + distanceY + 'px, 0)');
                    }
                }
            });

            $(window).on('mouseup.lg.zoom', function (e) {

                if (isDraging) {
                    isDraging = false;
                    _this.core.$outer.removeClass('lg-zoom-dragging');

                    // Fix for chrome mouse move on click
                    if (isMoved && ((startCoords.x !== endCoords.x) || (startCoords.y !== endCoords.y))) {
                        endCoords = {
                            x: e.pageX,
                            y: e.pageY
                        };
                        _this.touchendZoom(startCoords, endCoords, allowX, allowY);

                    }

                    isMoved = false;
                }

                _this.core.$outer.removeClass('lg-grabbing').addClass('lg-grab');

            });
        };

        Zoom.prototype.touchendZoom = function (startCoords, endCoords, allowX, allowY) {

            var _this = this;
            var _$el = _this.core.$slide.eq(_this.core.index).find('.lg-img-wrap');
            var $image = _this.core.$slide.eq(_this.core.index).find('.lg-object');
            var distanceX = (-Math.abs(_$el.attr('data-x'))) + (endCoords.x - startCoords.x);
            var distanceY = (-Math.abs(_$el.attr('data-y'))) + (endCoords.y - startCoords.y);
            var minY = (_this.core.$outer.find('.lg').height() - $image.prop('offsetHeight')) / 2;
            var maxY = Math.abs(($image.prop('offsetHeight') * Math.abs($image.attr('data-scale'))) - _this.core.$outer.find('.lg').height() + minY);
            var minX = (_this.core.$outer.find('.lg').width() - $image.prop('offsetWidth')) / 2;
            var maxX = Math.abs(($image.prop('offsetWidth') * Math.abs($image.attr('data-scale'))) - _this.core.$outer.find('.lg').width() + minX);

            if ((Math.abs(endCoords.x - startCoords.x) > 15) || (Math.abs(endCoords.y - startCoords.y) > 15)) {
                if (allowY) {
                    if (distanceY <= -maxY) {
                        distanceY = -maxY;
                    } else if (distanceY >= -minY) {
                        distanceY = -minY;
                    }
                }

                if (allowX) {
                    if (distanceX <= -maxX) {
                        distanceX = -maxX;
                    } else if (distanceX >= -minX) {
                        distanceX = -minX;
                    }
                }

                if (allowY) {
                    _$el.attr('data-y', Math.abs(distanceY));
                } else {
                    distanceY = -Math.abs(_$el.attr('data-y'));
                }

                if (allowX) {
                    _$el.attr('data-x', Math.abs(distanceX));
                } else {
                    distanceX = -Math.abs(_$el.attr('data-x'));
                }

                if (_this.core.s.useLeftForZoom) {
                    _$el.css({
                        left: distanceX + 'px',
                        top: distanceY + 'px'
                    });
                } else {
                    _$el.css('transform', 'translate3d(' + distanceX + 'px, ' + distanceY + 'px, 0)');
                }

            }
        };

        Zoom.prototype.destroy = function () {

            var _this = this;

            // Unbind all events added by lightGallery zoom plugin
            _this.core.$el.off('.lg.zoom');
            $(window).off('.lg.zoom');
            _this.core.$slide.off('.lg.zoom');
            _this.core.$el.off('.lg.tm.zoom');
            _this.resetZoom();
            clearTimeout(_this.zoomabletimeout);
            _this.zoomabletimeout = false;
        };

        $.fn.lightGallery.modules.zoom = Zoom;

    })();


}));

/*! lg-hash - v1.0.4 - 2017-12-20
* http://sachinchoolur.github.io/lightGallery
* Copyright (c) 2017 Sachin N; Licensed GPLv3 */

(function (root, factory) {
    if (typeof define === 'function' && define.amd) {
        // AMD. Register as an anonymous module unless amdModuleId is set
        define(['jquery'], function (a0) {
            return (factory(a0));
        });
    } else if (typeof exports === 'object') {
        // Node. Does not work with strict CommonJS, but
        // only CommonJS-like environments that support module.exports,
        // like Node.
        module.exports = factory(require('jquery'));
    } else {
        factory(jQuery);
    }
}(this, function ($) {

    (function () {

        'use strict';

        var defaults = {
            hash: true
        };

        var Hash = function (element) {

            this.core = $(element).data('lightGallery');

            this.core.s = $.extend({}, defaults, this.core.s);

            if (this.core.s.hash) {
                this.oldHash = window.location.hash;
                this.init();
            }

            return this;
        };

        Hash.prototype.init = function () {
            var _this = this;
            var _hash;

            // Change hash value on after each slide transition
            _this.core.$el.on('onAfterSlide.lg.tm', function (event, prevIndex, index) {
                if (history.replaceState) {
                    history.replaceState(null, null, window.location.pathname + window.location.search + '#lg=' + _this.core.s.galleryId + '&slide=' + index);
                } else {
                    window.location.hash = 'lg=' + _this.core.s.galleryId + '&slide=' + index;
                }
            });

            // Listen hash change and change the slide according to slide value
            $(window).on('hashchange.lg.hash', function () {
                _hash = window.location.hash;
                var _idx = parseInt(_hash.split('&slide=')[1], 10);

                // it galleryId doesn't exist in the url close the gallery
                if ((_hash.indexOf('lg=' + _this.core.s.galleryId) > -1)) {
                    _this.core.slide(_idx, false, false);
                } else if (_this.core.lGalleryOn) {
                    _this.core.destroy();
                }

            });
        };

        Hash.prototype.destroy = function () {

            if (!this.core.s.hash) {
                return;
            }

            // Reset to old hash value
            if (this.oldHash && this.oldHash.indexOf('lg=' + this.core.s.galleryId) < 0) {
                if (history.replaceState) {
                    history.replaceState(null, null, this.oldHash);
                } else {
                    window.location.hash = this.oldHash;
                }
            } else {
                if (history.replaceState) {
                    history.replaceState(null, document.title, window.location.pathname + window.location.search);
                } else {
                    window.location.hash = '';
                }
            }

            this.core.$el.off('.lg.hash');

        };

        $.fn.lightGallery.modules.hash = Hash;

    })();


}));

/*! lg-share - v1.1.0 - 2017-10-03
* http://sachinchoolur.github.io/lightGallery
* Copyright (c) 2017 Sachin N; Licensed GPLv3 */

(function (root, factory) {
    if (typeof define === 'function' && define.amd) {
        // AMD. Register as an anonymous module unless amdModuleId is set
        define(['jquery'], function (a0) {
            return (factory(a0));
        });
    } else if (typeof exports === 'object') {
        // Node. Does not work with strict CommonJS, but
        // only CommonJS-like environments that support module.exports,
        // like Node.
        module.exports = factory(require('jquery'));
    } else {
        factory(jQuery);
    }
}(this, function ($) {

    (function () {

        'use strict';

        var defaults = {
            share: true,
            facebook: true,
            facebookDropdownText: 'Facebook',
            twitter: true,
            twitterDropdownText: 'Twitter',
            googlePlus: true,
            googlePlusDropdownText: 'GooglePlus',
            pinterest: true,
            pinterestDropdownText: 'Pinterest'
        };

        var Share = function (element) {

            this.core = $(element).data('lightGallery');

            this.core.s = $.extend({}, defaults, this.core.s);
            if (this.core.s.share) {
                this.init();
            }

            return this;
        };

        Share.prototype.init = function () {
            var _this = this;
            var shareHtml = '<span id="lg-share" class="lg-icon">' +
                '<ul class="lg-dropdown" style="position: absolute;">';
            shareHtml += _this.core.s.facebook ? '<li><a id="lg-share-facebook" target="_blank"><span class="lg-icon"></span><span class="lg-dropdown-text">' + this.core.s.facebookDropdownText + '</span></a></li>' : '';
            shareHtml += _this.core.s.twitter ? '<li><a id="lg-share-twitter" target="_blank"><span class="lg-icon"></span><span class="lg-dropdown-text">' + this.core.s.twitterDropdownText + '</span></a></li>' : '';
            shareHtml += _this.core.s.googlePlus ? '<li><a id="lg-share-googleplus" target="_blank"><span class="lg-icon"></span><span class="lg-dropdown-text">' + this.core.s.googlePlusDropdownText + '</span></a></li>' : '';
            shareHtml += _this.core.s.pinterest ? '<li><a id="lg-share-pinterest" target="_blank"><span class="lg-icon"></span><span class="lg-dropdown-text">' + this.core.s.pinterestDropdownText + '</span></a></li>' : '';
            shareHtml += '</ul></span>';

            this.core.$outer.find('.lg-toolbar').append(shareHtml);
            this.core.$outer.find('.lg').append('<div id="lg-dropdown-overlay"></div>');
            $('#lg-share').on('click.lg', function () {
                _this.core.$outer.toggleClass('lg-dropdown-active');
            });

            $('#lg-dropdown-overlay').on('click.lg', function () {
                _this.core.$outer.removeClass('lg-dropdown-active');
            });

            _this.core.$el.on('onAfterSlide.lg.tm', function (event, prevIndex, index) {

                setTimeout(function () {

                    $('#lg-share-facebook').attr('href', 'https://www.facebook.com/sharer/sharer.php?u=' + (encodeURIComponent(_this.getSahreProps(index, 'facebookShareUrl') || window.location.href)));

                    $('#lg-share-twitter').attr('href', 'https://twitter.com/intent/tweet?text=' + _this.getSahreProps(index, 'tweetText') + '&url=' + (encodeURIComponent(_this.getSahreProps(index, 'twitterShareUrl') || window.location.href)));

                    $('#lg-share-googleplus').attr('href', 'https://plus.google.com/share?url=' + (encodeURIComponent(_this.getSahreProps(index, 'googleplusShareUrl') || window.location.href)));

                    $('#lg-share-pinterest').attr('href', 'http://www.pinterest.com/pin/create/button/?url=' + (encodeURIComponent(_this.getSahreProps(index, 'pinterestShareUrl') || window.location.href)) + '&media=' + encodeURIComponent(_this.getSahreProps(index, 'src')) + '&description=' + _this.getSahreProps(index, 'pinterestText'));

                }, 100);
            });
        };

        Share.prototype.getSahreProps = function (index, prop) {
            var shareProp = '';
            if (this.core.s.dynamic) {
                shareProp = this.core.s.dynamicEl[index][prop];
            } else {
                var _href = this.core.$items.eq(index).attr('href');
                var _prop = this.core.$items.eq(index).data(prop);
                shareProp = prop === 'src' ? _href || _prop : _prop;
            }
            return shareProp;
        };

        Share.prototype.destroy = function () {

        };

        $.fn.lightGallery.modules.share = Share;

    })();



}));
