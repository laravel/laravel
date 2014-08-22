/*!
 * jQuery MsgBox - for jQuery 1.3+
 * http://codecanyon.net/item/jquery-msgbox/92626?ref=aeroalquimia
 *
 * Copyright 2010, Eduardo Daniel Sada
 * You need to buy a license if you want use this script.
 * http://codecanyon.net/wiki/buying/howto-buying/licensing/
 *
 * Version: 1.3.5 (Aug 19 2012)
 *
 * Includes jQuery Easing v1.1.2
 * http://gsgd.co.uk/sandbox/jquery.easIng.php
 * Copyright (c) 2007 George Smith
 * Released under the MIT License.
 */

(function($) {
  
  var ie6 = (jQuery.browser.msie && parseInt(jQuery.browser.version, 10) < 7 && parseInt(jQuery.browser.version, 10) > 4);
  
  if ($.proxy === undefined)
  {
    $.extend({
      proxy: function( fn, thisObject ) {
        if ( fn )
        {
          proxy = function() { return fn.apply( thisObject || this, arguments ); };
        };
        return proxy;
      }
    });
  };

  $.extend( jQuery.easing,
  {
    easeOutBack: function (x, t, b, c, d, s) {
      if (s == undefined) s = 1.70158;
      return c*((t=t/d-1)*t*((s+1)*t + s) + 1) + b;
    }
  });

  $.extend($.expr[':'], {
    value: function(a) {
      return $(a).val();
    }
  });

  $.extend({
    MsgBoxObject: {
      defaults    : {
                      name            : 'jquery-msgbox',
                      zIndex          : 10000,
                      width           : 420,
                      height          : 'auto',
                      background      : '#FFFFFF',
                      modal           : true,
                      overlay         : {
                                        'background-color'  : '#000000',
                                        'opacity'           : 0.5
                                        },
                      showDuration    : 200,
                      closeDuration   : 100,
                      moveDuration    : 500,
                      shake           : {
                                        'distance'   : 10,
                                        'duration'   : 100,
                                        'transition' : 'easeOutBack',
                                        'loops'      : 2
                                      },
                      form            : {
                                        'active'  : false,
                                        'action'  : '#',
                                        'method'  : 'post'
                                        },
                      emergefrom      : 'top'
                    },
      options     : {},
      esqueleto   : {
                      msgbox  : [],
                      wrapper : [],
                      form    : [],
                      buttons : [],
                      inputs  : []
                    },
      visible     : false,
      i           : 0,
      animation   : false,
      
      config : function(options) {
        this.options = $.extend(true, this.options, options);
        this.overlay.element.css(this.options.overlay);
        this.overlay.options.hideOnClick = !this.options.modal;
        this.esqueleto.msgbox.css({'width':this.options.width, 'height':this.options.height, 'background-color': this.options.background});
        this.moveBox();
      },

      overlay : {
        create: function(options) {
          this.options = options;
          this.element = $('<div id="'+new Date().getTime()+'"></div>');
          this.element.css($.extend({}, {
            'position'  : 'fixed',
            'top'       : 0,
            'left'      : 0,
            'opacity'   : 0,
            'display'   : 'none',
            'z-index'   : this.options.zIndex
          }, this.options.style));

          this.element.click( $.proxy(function(event) {
            if (this.options.hideOnClick)
            {
              if ($.isFunction(this.options.callback))
              {
                this.options.callback();
              }
              else
              {
                this.hide();
              }
            }
            event.preventDefault();
          }, this));
          
          this.hidden = true;
          this.inject();
          return this;
        },

        inject: function() {
          this.target = $(document.body);
          this.target.append(this.element);

          if(ie6)
          {
            this.element.css({'position': 'absolute'});
            var zIndex = parseInt(this.element.css('zIndex'));
            if (!zIndex)
            {
              zIndex = 1;
              var pos = this.element.css('position');
              if (pos == 'static' || !pos)
              {
                this.element.css({'position': 'relative'});
              }
              this.element.css({'zIndex': zIndex});
            }
            zIndex = (!!(this.options.zIndex || this.options.zIndex === 0) && zIndex > this.options.zIndex) ? this.options.zIndex : zIndex - 1;
            if (zIndex < 0)
            {
              zIndex = 1;
            }
            this.shim = $('<iframe id="IF_'+new Date().getTime()+'" scrolling="no" frameborder=0 src=""></div>');
            this.shim.css({
              zIndex    : zIndex,
              position  : 'absolute',
              top       : 0,
              left      : 0,
              border    : 'none',
              width     : 0,
              height    : 0,
              opacity   : 0
            });
            this.shim.insertAfter(this.element);
            $('html, body').css({
              'height'      : '100%',
              'width'       : '100%',
              'margin-left' : 0,
              'margin-right': 0
            });
          }
        },

        resize: function(x, y) {
          this.element.css({ 'height': 0, 'width': 0 });
          if (this.shim) this.shim.css({ 'height': 0, 'width': 0 });

          var win = { x: $(document).width(), y: $(document).height() };
          
          this.element.css({
            'width'   : '100%',
            'height'  : y ? y : win.y
          });

          if (this.shim)
          {
            this.shim.css({ 'height': 0, 'width': 0 });
            this.shim.css({
              'position': 'absolute',
              'left'    : 0,
              'top'     : 0,
              'width'   : this.element.width(),
              'height'  : y ? y : win.y
            });
          }
          return this;
        },

        show: function() {
          if (!this.hidden) return this;
          if (this.transition) this.transition.stop();
          this.target.bind('resize', $.proxy(this.resize, this));
          this.resize();
          if (this.shim) this.shim.css({'display': 'block'});
          this.hidden = false;

          this.transition = this.element.fadeIn(this.options.showDuration, $.proxy(function(){
            this.element.trigger('show');
          }, this));
          
          return this;
        },

        hide: function() {
          if (this.hidden) return this;
          if (this.transition) this.transition.stop();
          this.target.unbind('resize');
          if (this.shim) this.shim.css({'display': 'none'});
          this.hidden = true;

          this.transition = this.element.fadeOut(this.options.closeDuration, $.proxy(function(){
            this.element.trigger('hide');
            this.element.css({ 'height': 0, 'width': 0 });
          }, this));

          return this;
        }
      },

      create: function() {
        this.options = $.extend(true, this.defaults, this.options);

        this.overlay.create({
          style         : this.options.overlay,
          hideOnClick   : !this.options.modal,
          zIndex        : this.options.zIndex-1,
          showDuration  : this.options.showDuration,
          closeDuration : this.options.closeDuration
        });
                
        this.esqueleto.msgbox = $('<div class="'+this.options.name+'"></div>');
        this.esqueleto.msgbox.css({
          'display'   : 'none',
          'position'  : 'absolute',
          'top'       : 0,
          'left'      : 0,
          'width'     : this.options.width,
          'height'    : this.options.height,
          'z-index'   : this.options.zIndex,
          'word-wrap' : 'break-word',
          '-moz-box-shadow'         : '0 0 15px rgba(0, 0, 0, 0.5)',
          '-webkit-box-shadow'      : '0 0 15px rgba(0, 0, 0, 0.5)',
          'box-shadow'              : '0 0 15px rgba(0, 0, 0, 0.5)',
          '-moz-border-radius'      : '6px',
          '-webkit-border-radius'   : '6px',
          'border-radius'           : '6px',
          'background-color'        : this.options.background
        });
        
        this.esqueleto.wrapper = $('<div class="'+this.options.name+'-wrapper"></div>');
        this.esqueleto.msgbox.append(this.esqueleto.wrapper);
        
        this.esqueleto.form = $('<form action="'+this.options.formaction+'" method="post"></form>');
        this.esqueleto.wrapper.append(this.esqueleto.form);


        this.esqueleto.wrapper.css({
          height       : (ie6 ? 80 : 'auto'),
          'min-height' : 80,
          'zoom'       : 1
        });
        
        $('body').append(this.esqueleto.msgbox);

        this.addevents();
        return this.esqueleto.msgbox;
      },
      
      addevents: function() {
        $(window).bind('resize', $.proxy(function() {
          if (this.visible)
          {
            this.overlay.resize();
            this.moveBox();
          }
        }, this));

        $(window).bind('scroll', $.proxy(function() {
          if (this.visible)
          {
            this.moveBox();
          }
        }, this));

        this.esqueleto.msgbox.bind('keydown', $.proxy(function(event) {
          if (event.keyCode == 27)
          {
            this.close(false);
          }
        }, this));
        
        this.esqueleto.form.bind('submit', $.proxy(function(event) {
          $('input[type=submit]:first, button[type=submit]:first, button:first', this.esqueleto.form).trigger('click');
          if (!options.form.active) {
            event.preventDefault();
          }
        }, this));

        // heredamos los eventos, desde el overlay:
        this.overlay.element.bind('show', $.proxy(function() { $(this).triggerHandler('show'); }, this));
        this.overlay.element.bind('hide', $.proxy(function() { $(this).triggerHandler('close'); }, this));

      },

      show: function(txt, options, callback) {
        var types = ['alert', 'info', 'error', 'prompt', 'confirm'];
      
        this.esqueleto.msgbox.queue(this.options.name, $.proxy(function( next ) {
        
          options = $.extend(true, {
            type  : 'alert',
            form  : {
                      'active' : false
                    }
          }, options || {});
          
          if (typeof options.buttons === "undefined")
          {
            if (options.type == 'confirm' || options.type == 'prompt')
            {
              var buttons = [
                {type: 'submit', value: 'Accept'},
                {type: 'cancel', value: 'Cancel'}
              ];
            }
            else
            {
              var buttons = [
                {type: 'submit', value: 'Accept'}
              ];
            };
          }
          else
          {
            var buttons = options.buttons;
          };
          
          if (typeof options.inputs === "undefined" && options.type == 'prompt')
          {
            var inputs = [
              {type: 'text', name: 'prompt', value: ''}
            ];
          }
          else
          {
            var inputs = options.inputs;
          };
          
          this.callback = $.isFunction(callback) ? callback : function(e) {};
          
          if (typeof inputs !== "undefined")
          {
            this.esqueleto.inputs = $('<div class="'+this.options.name+'-inputs"></div>');
            this.esqueleto.form.append(this.esqueleto.inputs);

            $.each(inputs, $.proxy(function(i, input) {
              if (input.type == 'checkbox')
              {
                iLabel = input.label ? '<label class="'+this.options.name+'-label">' : '';
                fLabel = input.label ? input.label+'</label>' : '';
                input.value = input.value === undefined ? '1' : input.value;
                iName  = input.name === undefined ? this.options.name+'-label-'+i : input.name;
                this.esqueleto.inputs.append($(iLabel+'<input type="'+input.type+'" style="display:inline; width:auto;" name="'+iName+'" value="'+input.value+'" autocomplete="off"/> '+fLabel));
              }
              else
              {
                iLabel = input.label ? '<label class="'+this.options.name+'-label">'+input.label : '';
                fLabel = input.label ? '</label>' : '';
                input.value = input.value === undefined ? '' : input.value;
                iRequired   = input.required === undefined || input.required == false ? '' : 'required="true"';
                iName  = input.name === undefined ? this.options.name+'-label-'+i : input.name;
                this.esqueleto.inputs.append($(iLabel+'<input type="'+input.type+'" name="'+iName+'" value="'+input.value+'" autocomplete="off" '+iRequired+'/>'+fLabel));
              }
            }, this));
          }

          this.esqueleto.buttons = $('<div class="'+this.options.name+'-buttons"></div>');
          this.esqueleto.form.append(this.esqueleto.buttons);
          
          if (options.form.active) {
            this.esqueleto.form.attr('action', options.form.action === undefined ? '#' : options.form.action);
            this.esqueleto.form.attr('method', options.form.method === undefined ? 'post' : options.form.method);
            this.options.form.active = true;
          } else {
            this.esqueleto.form.attr('action', '#');
            this.esqueleto.form.attr('method', 'post');
            this.options.form.active = false;
          }
          
          if (options.type != 'prompt')
          {
            $.each(buttons, $.proxy(function(i, button) {
              if (button.type == 'submit')
              {
                this.esqueleto.buttons.append($('<button type="submit" class="'+this.options.name+'-button-submit '+(button["class"] || "")+'">'+button.value+'</button>').bind('click', $.proxy(function(e) { this.close(button.value); e.preventDefault(); }, this)));
              }
              else if (button.type == 'cancel')
              {
                this.esqueleto.buttons.append($('<button type="button" class="'+this.options.name+'-button-cancel '+(button["class"] || "")+'">'+button.value+'</button>').bind('click', $.proxy(function(e) { this.close(false); e.preventDefault(); }, this)));
              }
            }, this));
          }
          else if (options.type == 'prompt')
          {
            $.each(buttons, $.proxy(function(i, button) {
              if (button.type == 'submit')
              {
                this.esqueleto.buttons.append($('<button type="submit" class="'+this.options.name+'-button-submit '+(button["class"] || "")+'">'+button.value+'</button>').bind('click', $.proxy(function(e) {
                  if ($('input[required="true"]:not(:value)').length>0)
                  {
                    $('input[required="true"]:not(:value):first').focus();
                    this.shake();
                  }
                  else if (this.options.form.active)
                  {
                    return true;
                  }
                  else
                  {
                    this.close(this.toArguments($('input', this.esqueleto.inputs)));
                  }

                  e.preventDefault();
                }, this)));
              }
              else if (button.type == 'cancel')
              {
                this.esqueleto.buttons.append($('<button type="button" class="'+this.options.name+'-button-cancel '+(button["class"] || "")+'">'+button.value+'</button>').bind('click', $.proxy(function(e) { this.close(false); e.preventDefault(); }, this)));
              };
            }, this));
          };

          this.esqueleto.form.prepend(txt);
          
          $.each(types, $.proxy(function(i, e) {
            this.esqueleto.wrapper.removeClass(this.options.name+'-'+e);
          }, this));
          this.esqueleto.wrapper.addClass(this.options.name+'-'+options.type);

          this.moveBox(); // set initial position

          this.visible = true;
          this.overlay.show();

          this.esqueleto.msgbox.css({
            display : 'block',
            left    : ( ($(document).width() - this.options.width) / 2)
          });

          this.moveBox();

          setTimeout($.proxy(function() { var b = $('input, button', this.esqueleto.msgbox); if (b.length) { b.get(0).focus();} }, this), this.options.moveDuration);
        }, this));


        this.i++;
        
        if (this.i==1)
        {
          this.esqueleto.msgbox.dequeue(this.options.name);
        }

      },
      
      toArguments: function(array) {
        return $.map(array, function(a) {
          return $(a).val();
        });
      },
      
      moveBox: function() {
        var size   = { x: $(window).width(),      y: $(window).height() };
        var scroll = { x: $(window).scrollLeft(), y: $(window).scrollTop() };
        var height = this.esqueleto.msgbox.outerHeight();
        var y      = 0;
        var x      = 0;

        // vertically center
        y = scroll.x + ((size.x - this.options.width) / 2);
        
        if (this.options.emergefrom == "bottom")
        {
          x = (scroll.y + size.y + 80);
        }
        else // top
        {
          x = (scroll.y - height) - 80;
        }

        if (this.visible)
        {

          if (this.animation)
          {
            this.animation.stop;
          }

          this.animation = this.esqueleto.msgbox.animate({
            left  : y,
            top   : scroll.y + ((size.y - height) / 2)
          }, {
            duration  : this.options.moveDuration,
            queue     : false,
            easing    : 'easeOutBack'
          });

        }
        else
        {
          this.esqueleto.msgbox.css({
            top     : x,
            left    : y
          });
        }
      },
      
      close: function(param) {
        this.esqueleto.msgbox.css({
          display : 'none',
          top     : 0
        });
        
        this.visible = false;
        
        if ($.isFunction(this.callback))
        {
          this.callback.apply(this, $.makeArray(param));
        }
        
        setTimeout($.proxy(function() {
          this.i--;
          this.esqueleto.msgbox.dequeue(this.options.name);
        }, this), this.options.closeDuration);
        
        if (this.i==1) 
        {
          this.overlay.hide();
        }
        
        this.moveBox();
        
        this.esqueleto.form.empty();
      },

      shake: function() {
        var x = this.options.shake.distance;
        var d = this.options.shake.duration;
        var t = this.options.shake.transition;
        var o = this.options.shake.loops;
        var l = this.esqueleto.msgbox.position().left;
        var e = this.esqueleto.msgbox;

        for (i=0; i<o; i++)
        {
         e.animate({left: l+x}, d, t);
         e.animate({left: l-x}, d, t);
        };

        e.animate({left: l+x}, d, t);
        e.animate({left: l},   d, t);
      }

    },
    
    msgbox: function(txt, options, callback) {
      if (typeof txt == "object")
      {
        $.MsgBoxObject.config(txt);
      }
      else
      {
        return $.MsgBoxObject.show(txt, options, callback);
      }
    }
    
  });
  
  $(function() {
    if (parseFloat($.fn.jquery) > 1.2) {
      $.MsgBoxObject.create();
    } else {
      throw "The jQuery version that was loaded is too old. MsgBox requires jQuery 1.3+";
    }
  });
})(jQuery);