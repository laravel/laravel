"use strict";
(function (root, factory) {
  if (typeof exports === 'object') {
    module.exports = factory();
  }
  else if (typeof define === 'function' && define.amd) {
    define(['jquery', 'googlemaps!'], factory);
  }
  else {
    root.GMaps = factory();
  }


}(this, function () {

  /*!
   * GMaps.js v0.4.25
   * http://hpneo.github.com/gmaps/
   *
   * Copyright 2017, Gustavo Leon
   * Released under the MIT License.
   */

  var extend_object = function (obj, new_obj) {
    var name;

    if (obj === new_obj) {
      return obj;
    }

    for (name in new_obj) {
      if (new_obj[name] !== undefined) {
        obj[name] = new_obj[name];
      }
    }

    return obj;
  };

  var replace_object = function (obj, replace) {
    var name;

    if (obj === replace) {
      return obj;
    }

    for (name in replace) {
      if (obj[name] != undefined) {
        obj[name] = replace[name];
      }
    }

    return obj;
  };

  var array_map = function (array, callback) {
    var original_callback_params = Array.prototype.slice.call(arguments, 2),
      array_return = [],
      array_length = array.length,
      i;

    if (Array.prototype.map && array.map === Array.prototype.map) {
      array_return = Array.prototype.map.call(array, function (item) {
        var callback_params = original_callback_params.slice(0);
        callback_params.splice(0, 0, item);

        return callback.apply(this, callback_params);
      });
    }
    else {
      for (i = 0; i < array_length; i++) {
        callback_params = original_callback_params;
        callback_params.splice(0, 0, array[i]);
        array_return.push(callback.apply(this, callback_params));
      }
    }

    return array_return;
  };

  var array_flat = function (array) {
    var new_array = [],
      i;

    for (i = 0; i < array.length; i++) {
      new_array = new_array.concat(array[i]);
    }

    return new_array;
  };

  var coordsToLatLngs = function (coords, useGeoJSON) {
    var first_coord = coords[0],
      second_coord = coords[1];

    if (useGeoJSON) {
      first_coord = coords[1];
      second_coord = coords[0];
    }

    return new google.maps.LatLng(first_coord, second_coord);
  };

  var arrayToLatLng = function (coords, useGeoJSON) {
    var i;

    for (i = 0; i < coords.length; i++) {
      if (!(coords[i] instanceof google.maps.LatLng)) {
        if (coords[i].length > 0 && typeof (coords[i][0]) === "object") {
          coords[i] = arrayToLatLng(coords[i], useGeoJSON);
        }
        else {
          coords[i] = coordsToLatLngs(coords[i], useGeoJSON);
        }
      }
    }

    return coords;
  };

  var getElementsByClassName = function (class_name, context) {
    var element,
      _class = class_name.replace('.', '');

    if ('jQuery' in this && context) {
      element = $("." + _class, context)[0];
    } else {
      element = document.getElementsByClassName(_class)[0];
    }
    return element;

  };

  var getElementById = function (id, context) {
    var element,
      id = id.replace('#', '');

    if ('jQuery' in window && context) {
      element = $('#' + id, context)[0];
    } else {
      element = document.getElementById(id);
    };

    return element;
  };

  var findAbsolutePosition = function (obj) {
    var curleft = 0,
      curtop = 0;

    if (obj.getBoundingClientRect) {
      var rect = obj.getBoundingClientRect();
      var sx = -(window.scrollX ? window.scrollX : window.pageXOffset);
      var sy = -(window.scrollY ? window.scrollY : window.pageYOffset);

      return [(rect.left - sx), (rect.top - sy)];
    }

    if (obj.offsetParent) {
      do {
        curleft += obj.offsetLeft;
        curtop += obj.offsetTop;
      } while (obj = obj.offsetParent);
    }

    return [curleft, curtop];
  };

  var GMaps = (function (global) {
    "use strict";

    var doc = document;
    /**
     * Creates a new GMaps instance, including a Google Maps map.
     * @class GMaps
     * @constructs
     * @param {object} options - `options` accepts all the [MapOptions](https://developers.google.com/maps/documentation/javascript/reference#MapOptions) and [events](https://developers.google.com/maps/documentation/javascript/reference#Map) listed in the Google Maps API. Also accepts:
     * * `lat` (number): Latitude of the map's center
     * * `lng` (number): Longitude of the map's center
     * * `el` (string or HTMLElement): container where the map will be rendered
     * * `markerClusterer` (function): A function to create a marker cluster. You can use MarkerClusterer or MarkerClustererPlus.
     */
    var GMaps = function (options) {

      if (!(typeof window.google === 'object' && window.google.maps)) {
        if (typeof window.console === 'object' && window.console.error) {
          console.error('Google Maps API is required. Please register the following JavaScript library https://maps.googleapis.com/maps/api/js.');
        }

        return function () { };
      }

      if (!this) return new GMaps(options);

      options.zoom = options.zoom || 15;
      options.mapType = options.mapType || 'roadmap';

      var valueOrDefault = function (value, defaultValue) {
        return value === undefined ? defaultValue : value;
      };

      var self = this,
        i,
        events_that_hide_context_menu = [
          'bounds_changed', 'center_changed', 'click', 'dblclick', 'drag',
          'dragend', 'dragstart', 'idle', 'maptypeid_changed', 'projection_changed',
          'resize', 'tilesloaded', 'zoom_changed'
        ],
        events_that_doesnt_hide_context_menu = ['mousemove', 'mouseout', 'mouseover'],
        options_to_be_deleted = ['el', 'lat', 'lng', 'mapType', 'width', 'height', 'markerClusterer', 'enableNewStyle'],
        identifier = options.el || options.div,
        markerClustererFunction = options.markerClusterer,
        mapType = google.maps.MapTypeId[options.mapType.toUpperCase()],
        map_center = new google.maps.LatLng(options.lat, options.lng),
        zoomControl = valueOrDefault(options.zoomControl, true),
        zoomControlOpt = options.zoomControlOpt || {
          style: 'DEFAULT',
          position: 'TOP_LEFT'
        },
        zoomControlStyle = zoomControlOpt.style || 'DEFAULT',
        zoomControlPosition = zoomControlOpt.position || 'TOP_LEFT',
        panControl = valueOrDefault(options.panControl, true),
        mapTypeControl = valueOrDefault(options.mapTypeControl, true),
        scaleControl = valueOrDefault(options.scaleControl, true),
        streetViewControl = valueOrDefault(options.streetViewControl, true),
        overviewMapControl = valueOrDefault(overviewMapControl, true),
        map_options = {},
        map_base_options = {
          zoom: this.zoom,
          center: map_center,
          mapTypeId: mapType
        },
        map_controls_options = {
          panControl: panControl,
          zoomControl: zoomControl,
          zoomControlOptions: {
            style: google.maps.ZoomControlStyle[zoomControlStyle],
            position: google.maps.ControlPosition[zoomControlPosition]
          },
          mapTypeControl: mapTypeControl,
          scaleControl: scaleControl,
          streetViewControl: streetViewControl,
          overviewMapControl: overviewMapControl
        };

      if (typeof (options.el) === 'string' || typeof (options.div) === 'string') {
        if (identifier.indexOf("#") > -1) {
          /**
           * Container element
           *
           * @type {HTMLElement}
           */
          this.el = getElementById(identifier, options.context);
        } else {
          this.el = getElementsByClassName.apply(this, [identifier, options.context]);
        }
      } else {
        this.el = identifier;
      }

      if (typeof (this.el) === 'undefined' || this.el === null) {
        throw 'No element defined.';
      }

      window.context_menu = window.context_menu || {};
      window.context_menu[self.el.id] = {};

      /**
       * Collection of custom controls in the map UI
       *
       * @type {array}
       */
      this.controls = [];
      /**
       * Collection of map's overlays
       *
       * @type {array}
       */
      this.overlays = [];
      /**
       * Collection of KML/GeoRSS and FusionTable layers
       *
       * @type {array}
       */
      this.layers = [];
      /**
       * Collection of data layers (See {@link GMaps#addLayer})
       *
       * @type {object}
       */
      this.singleLayers = {};
      /**
       * Collection of map's markers
       *
       * @type {array}
       */
      this.markers = [];
      /**
       * Collection of map's lines
       *
       * @type {array}
       */
      this.polylines = [];
      /**
       * Collection of map's routes requested by {@link GMaps#getRoutes}, {@link GMaps#renderRoute}, {@link GMaps#drawRoute}, {@link GMaps#travelRoute} or {@link GMaps#drawSteppedRoute}
       *
       * @type {array}
       */
      this.routes = [];
      /**
       * Collection of map's polygons
       *
       * @type {array}
       */
      this.polygons = [];
      this.infoWindow = null;
      this.overlay_el = null;
      /**
       * Current map's zoom
       *
       * @type {number}
       */
      this.zoom = options.zoom;
      this.registered_events = {};

      this.el.style.width = options.width || this.el.scrollWidth || this.el.offsetWidth;
      this.el.style.height = options.height || this.el.scrollHeight || this.el.offsetHeight;

      google.maps.visualRefresh = options.enableNewStyle;

      for (i = 0; i < options_to_be_deleted.length; i++) {
        delete options[options_to_be_deleted[i]];
      }

      if (options.disableDefaultUI != true) {
        map_base_options = extend_object(map_base_options, map_controls_options);
      }

      map_options = extend_object(map_base_options, options);

      for (i = 0; i < events_that_hide_context_menu.length; i++) {
        delete map_options[events_that_hide_context_menu[i]];
      }

      for (i = 0; i < events_that_doesnt_hide_context_menu.length; i++) {
        delete map_options[events_that_doesnt_hide_context_menu[i]];
      }

      /**
       * Google Maps map instance
       *
       * @type {google.maps.Map}
       */
      this.map = new google.maps.Map(this.el, map_options);

      if (markerClustererFunction) {
        /**
         * Marker Clusterer instance
         *
         * @type {object}
         */
        this.markerClusterer = markerClustererFunction.apply(this, [this.map]);
      }

      var buildContextMenuHTML = function (control, e) {
        var html = '',
          options = window.context_menu[self.el.id][control];

        for (var i in options) {
          if (options.hasOwnProperty(i)) {
            var option = options[i];

            html += '<li><a id="' + control + '_' + i + '" href="#">' + option.title + '</a></li>';
          }
        }

        if (!getElementById('gmaps_context_menu')) return;

        var context_menu_element = getElementById('gmaps_context_menu');

        context_menu_element.innerHTML = html;

        var context_menu_items = context_menu_element.getElementsByTagName('a'),
          context_menu_items_count = context_menu_items.length,
          i;

        for (i = 0; i < context_menu_items_count; i++) {
          var context_menu_item = context_menu_items[i];

          var assign_menu_item_action = function (ev) {
            ev.preventDefault();

            options[this.id.replace(control + '_', '')].action.apply(self, [e]);
            self.hideContextMenu();
          };

          google.maps.event.clearListeners(context_menu_item, 'click');
          google.maps.event.addDomListenerOnce(context_menu_item, 'click', assign_menu_item_action, false);
        }

        var position = findAbsolutePosition.apply(this, [self.el]),
          left = position[0] + e.pixel.x - 15,
          top = position[1] + e.pixel.y - 15;

        context_menu_element.style.left = left + "px";
        context_menu_element.style.top = top + "px";

        // context_menu_element.style.display = 'block';
      };

      this.buildContextMenu = function (control, e) {
        if (control === 'marker') {
          e.pixel = {};

          var overlay = new google.maps.OverlayView();
          overlay.setMap(self.map);

          overlay.draw = function () {
            var projection = overlay.getProjection(),
              position = e.marker.getPosition();

            e.pixel = projection.fromLatLngToContainerPixel(position);

            buildContextMenuHTML(control, e);
          };
        }
        else {
          buildContextMenuHTML(control, e);
        }

        var context_menu_element = getElementById('gmaps_context_menu');

        setTimeout(function () {
          context_menu_element.style.display = 'block';
        }, 0);
      };

      /**
       * Add a context menu for a map or a marker.
       *
       * @param {object} options - The `options` object should contain:
       * * `control` (string): Kind of control the context menu will be attached. Can be "map" or "marker".
       * * `options` (array): A collection of context menu items:
       *   * `title` (string): Item's title shown in the context menu.
       *   * `name` (string): Item's identifier.
       *   * `action` (function): Function triggered after selecting the context menu item.
       */
      this.setContextMenu = function (options) {
        window.context_menu[self.el.id][options.control] = {};

        var i,
          ul = doc.createElement('ul');

        for (i in options.options) {
          if (options.options.hasOwnProperty(i)) {
            var option = options.options[i];

            window.context_menu[self.el.id][options.control][option.name] = {
              title: option.title,
              action: option.action
            };
          }
        }

        ul.id = 'gmaps_context_menu';
        ul.style.display = 'none';
        ul.style.position = 'absolute';
        ul.style.minWidth = '100px';
        ul.style.background = 'white';
        ul.style.listStyle = 'none';
        ul.style.padding = '8px';
        ul.style.boxShadow = '2px 2px 6px #ccc';

        if (!getElementById('gmaps_context_menu')) {
          doc.body.appendChild(ul);
        }

        var context_menu_element = getElementById('gmaps_context_menu');

        google.maps.event.addDomListener(context_menu_element, 'mouseout', function (ev) {
          if (!ev.relatedTarget || !this.contains(ev.relatedTarget)) {
            window.setTimeout(function () {
              context_menu_element.style.display = 'none';
            }, 400);
          }
        }, false);
      };

      /**
       * Hide the current context menu
       */
      this.hideContextMenu = function () {
        var context_menu_element = getElementById('gmaps_context_menu');

        if (context_menu_element) {
          context_menu_element.style.display = 'none';
        }
      };

      var setupListener = function (object, name) {
        google.maps.event.addListener(object, name, function (e) {
          if (e == undefined) {
            e = this;
          }

          options[name].apply(this, [e]);

          self.hideContextMenu();
        });
      };

      //google.maps.event.addListener(this.map, 'idle', this.hideContextMenu);
      google.maps.event.addListener(this.map, 'zoom_changed', this.hideContextMenu);

      for (var ev = 0; ev < events_that_hide_context_menu.length; ev++) {
        var name = events_that_hide_context_menu[ev];

        if (name in options) {
          setupListener(this.map, name);
        }
      }

      for (var ev = 0; ev < events_that_doesnt_hide_context_menu.length; ev++) {
        var name = events_that_doesnt_hide_context_menu[ev];

        if (name in options) {
          setupListener(this.map, name);
        }
      }

      google.maps.event.addListener(this.map, 'rightclick', function (e) {
        if (options.rightclick) {
          options.rightclick.apply(this, [e]);
        }

        if (window.context_menu[self.el.id]['map'] != undefined) {
          self.buildContextMenu('map', e);
        }
      });

      /**
       * Trigger a `resize` event, useful if you need to repaint the current map (for changes in the viewport or display / hide actions).
       */
      this.refresh = function () {
        google.maps.event.trigger(this.map, 'resize');
      };

      /**
       * Adjust the map zoom to include all the markers added in the map.
       */
      this.fitZoom = function () {
        var latLngs = [],
          markers_length = this.markers.length,
          i;

        for (i = 0; i < markers_length; i++) {
          if (typeof (this.markers[i].visible) === 'boolean' && this.markers[i].visible) {
            latLngs.push(this.markers[i].getPosition());
          }
        }

        this.fitLatLngBounds(latLngs);
      };

      /**
       * Adjust the map zoom to include all the coordinates in the `latLngs` array.
       *
       * @param {array} latLngs - Collection of `google.maps.LatLng` objects.
       */
      this.fitLatLngBounds = function (latLngs) {
        var total = latLngs.length,
          bounds = new google.maps.LatLngBounds(),
          i;

        for (i = 0; i < total; i++) {
          bounds.extend(latLngs[i]);
        }

        this.map.fitBounds(bounds);
      };

      /**
       * Center the map using the `lat` and `lng` coordinates.
       *
       * @param {number} lat - Latitude of the coordinate.
       * @param {number} lng - Longitude of the coordinate.
       * @param {function} [callback] - Callback that will be executed after the map is centered.
       */
      this.setCenter = function (lat, lng, callback) {
        this.map.panTo(new google.maps.LatLng(lat, lng));

        if (callback) {
          callback();
        }
      };

      /**
       * Return the HTML element container of the map.
       *
       * @returns {HTMLElement} the element container.
       */
      this.getElement = function () {
        return this.el;
      };

      /**
       * Increase the map's zoom.
       *
       * @param {number} [magnitude] - The number of times the map will be zoomed in.
       */
      this.zoomIn = function (value) {
        value = value || 1;

        this.zoom = this.map.getZoom() + value;
        this.map.setZoom(this.zoom);
      };

      /**
       * Decrease the map's zoom.
       *
       * @param {number} [magnitude] - The number of times the map will be zoomed out.
       */
      this.zoomOut = function (value) {
        value = value || 1;

        this.zoom = this.map.getZoom() - value;
        this.map.setZoom(this.zoom);
      };

      var native_methods = [],
        method;

      for (method in this.map) {
        if (typeof (this.map[method]) == 'function' && !this[method]) {
          native_methods.push(method);
        }
      }

      for (i = 0; i < native_methods.length; i++) {
        (function (gmaps, scope, method_name) {
          gmaps[method_name] = function () {
            return scope[method_name].apply(scope, arguments);
          };
        })(this, this.map, native_methods[i]);
      }
    };

    return GMaps;
  })(this);

  GMaps.prototype.createControl = function (options) {
    var control = document.createElement('div');

    control.style.cursor = 'pointer';

    if (options.disableDefaultStyles !== true) {
      control.style.fontFamily = 'Roboto, Arial, sans-serif';
      control.style.fontSize = '11px';
      control.style.boxShadow = 'rgba(0, 0, 0, 0.298039) 0px 1px 4px -1px';
    }

    for (var option in options.style) {
      control.style[option] = options.style[option];
    }

    if (options.id) {
      control.id = options.id;
    }

    if (options.title) {
      control.title = options.title;
    }

    if (options.classes) {
      control.className = options.classes;
    }

    if (options.content) {
      if (typeof options.content === 'string') {
        control.innerHTML = options.content;
      }
      else if (options.content instanceof HTMLElement) {
        control.appendChild(options.content);
      }
    }

    if (options.position) {
      control.position = google.maps.ControlPosition[options.position.toUpperCase()];
    }

    for (var ev in options.events) {
      (function (object, name) {
        google.maps.event.addDomListener(object, name, function () {
          options.events[name].apply(this, [this]);
        });
      })(control, ev);
    }

    control.index = 1;

    return control;
  };

  /**
   * Add a custom control to the map UI.
   *
   * @param {object} options - The `options` object should contain:
   * * `style` (object): The keys and values of this object should be valid CSS properties and values.
   * * `id` (string): The HTML id for the custom control.
   * * `classes` (string): A string containing all the HTML classes for the custom control.
   * * `content` (string or HTML element): The content of the custom control.
   * * `position` (string): Any valid [`google.maps.ControlPosition`](https://developers.google.com/maps/documentation/javascript/controls#ControlPositioning) value, in lower or upper case.
   * * `events` (object): The keys of this object should be valid DOM events. The values should be functions.
   * * `disableDefaultStyles` (boolean): If false, removes the default styles for the controls like font (family and size), and box shadow.
   * @returns {HTMLElement}
   */
  GMaps.prototype.addControl = function (options) {
    var control = this.createControl(options);

    this.controls.push(control);
    this.map.controls[control.position].push(control);

    return control;
  };

  /**
   * Remove a control from the map. `control` should be a control returned by `addControl()`.
   *
   * @param {HTMLElement} control - One of the controls returned by `addControl()`.
   * @returns {HTMLElement} the removed control.
   */
  GMaps.prototype.removeControl = function (control) {
    var position = null,
      i;

    for (i = 0; i < this.controls.length; i++) {
      if (this.controls[i] == control) {
        position = this.controls[i].position;
        this.controls.splice(i, 1);
      }
    }

    if (position) {
      for (i = 0; i < this.map.controls.length; i++) {
        var controlsForPosition = this.map.controls[control.position];

        if (controlsForPosition.getAt(i) == control) {
          controlsForPosition.removeAt(i);

          break;
        }
      }
    }

    return control;
  };

  GMaps.prototype.createMarker = function (options) {
    if (options.lat == undefined && options.lng == undefined && options.position == undefined) {
      throw 'No latitude or longitude defined.';
    }

    var self = this,
      details = options.details,
      fences = options.fences,
      outside = options.outside,
      base_options = {
        position: new google.maps.LatLng(options.lat, options.lng),
        map: null
      },
      marker_options = extend_object(base_options, options);

    delete marker_options.lat;
    delete marker_options.lng;
    delete marker_options.fences;
    delete marker_options.outside;

    var marker = new google.maps.Marker(marker_options);

    marker.fences = fences;

    if (options.infoWindow) {
      marker.infoWindow = new google.maps.InfoWindow(options.infoWindow);

      var info_window_events = ['closeclick', 'content_changed', 'domready', 'position_changed', 'zindex_changed'];

      for (var ev = 0; ev < info_window_events.length; ev++) {
        (function (object, name) {
          if (options.infoWindow[name]) {
            google.maps.event.addListener(object, name, function (e) {
              options.infoWindow[name].apply(this, [e]);
            });
          }
        })(marker.infoWindow, info_window_events[ev]);
      }
    }

    var marker_events = ['animation_changed', 'clickable_changed', 'cursor_changed', 'draggable_changed', 'flat_changed', 'icon_changed', 'position_changed', 'shadow_changed', 'shape_changed', 'title_changed', 'visible_changed', 'zindex_changed'];

    var marker_events_with_mouse = ['dblclick', 'drag', 'dragend', 'dragstart', 'mousedown', 'mouseout', 'mouseover', 'mouseup'];

    for (var ev = 0; ev < marker_events.length; ev++) {
      (function (object, name) {
        if (options[name]) {
          google.maps.event.addListener(object, name, function () {
            options[name].apply(this, [this]);
          });
        }
      })(marker, marker_events[ev]);
    }

    for (var ev = 0; ev < marker_events_with_mouse.length; ev++) {
      (function (map, object, name) {
        if (options[name]) {
          google.maps.event.addListener(object, name, function (me) {
            if (!me.pixel) {
              me.pixel = map.getProjection().fromLatLngToPoint(me.latLng)
            }

            options[name].apply(this, [me]);
          });
        }
      })(this.map, marker, marker_events_with_mouse[ev]);
    }

    google.maps.event.addListener(marker, 'click', function () {
      this.details = details;

      if (options.click) {
        options.click.apply(this, [this]);
      }

      if (marker.infoWindow) {
        self.hideInfoWindows();
        marker.infoWindow.open(self.map, marker);
      }
    });

    google.maps.event.addListener(marker, 'rightclick', function (e) {
      e.marker = this;

      if (options.rightclick) {
        options.rightclick.apply(this, [e]);
      }

      if (window.context_menu[self.el.id]['marker'] != undefined) {
        self.buildContextMenu('marker', e);
      }
    });

    if (marker.fences) {
      google.maps.event.addListener(marker, 'dragend', function () {
        self.checkMarkerGeofence(marker, function (m, f) {
          outside(m, f);
        });
      });
    }

    return marker;
  };

  GMaps.prototype.addMarker = function (options) {
    var marker;
    if (options.hasOwnProperty('gm_accessors_')) {
      // Native google.maps.Marker object
      marker = options;
    }
    else {
      if ((options.hasOwnProperty('lat') && options.hasOwnProperty('lng')) || options.position) {
        marker = this.createMarker(options);
      }
      else {
        throw 'No latitude or longitude defined.';
      }
    }

    marker.setMap(this.map);

    if (this.markerClusterer) {
      this.markerClusterer.addMarker(marker);
    }

    this.markers.push(marker);

    GMaps.fire('marker_added', marker, this);

    return marker;
  };

  GMaps.prototype.addMarkers = function (array) {
    for (var i = 0, marker; marker = array[i]; i++) {
      this.addMarker(marker);
    }

    return this.markers;
  };

  GMaps.prototype.hideInfoWindows = function () {
    for (var i = 0, marker; marker = this.markers[i]; i++) {
      if (marker.infoWindow) {
        marker.infoWindow.close();
      }
    }
  };

  GMaps.prototype.removeMarker = function (marker) {
    for (var i = 0; i < this.markers.length; i++) {
      if (this.markers[i] === marker) {
        this.markers[i].setMap(null);
        this.markers.splice(i, 1);

        if (this.markerClusterer) {
          this.markerClusterer.removeMarker(marker);
        }

        GMaps.fire('marker_removed', marker, this);

        break;
      }
    }

    return marker;
  };

  GMaps.prototype.removeMarkers = function (collection) {
    var new_markers = [];

    if (typeof collection == 'undefined') {
      for (var i = 0; i < this.markers.length; i++) {
        var marker = this.markers[i];
        marker.setMap(null);

        GMaps.fire('marker_removed', marker, this);
      }

      if (this.markerClusterer && this.markerClusterer.clearMarkers) {
        this.markerClusterer.clearMarkers();
      }

      this.markers = new_markers;
    }
    else {
      for (var i = 0; i < collection.length; i++) {
        var index = this.markers.indexOf(collection[i]);

        if (index > -1) {
          var marker = this.markers[index];
          marker.setMap(null);

          if (this.markerClusterer) {
            this.markerClusterer.removeMarker(marker);
          }

          GMaps.fire('marker_removed', marker, this);
        }
      }

      for (var i = 0; i < this.markers.length; i++) {
        var marker = this.markers[i];
        if (marker.getMap() != null) {
          new_markers.push(marker);
        }
      }

      this.markers = new_markers;
    }
  };

  GMaps.prototype.drawOverlay = function (options) {
    var overlay = new google.maps.OverlayView(),
      auto_show = true;

    overlay.setMap(this.map);

    if (options.auto_show != null) {
      auto_show = options.auto_show;
    }

    overlay.onAdd = function () {
      var el = document.createElement('div');

      el.style.borderStyle = "none";
      el.style.borderWidth = "0px";
      el.style.position = "absolute";
      el.style.zIndex = 100;
      el.innerHTML = options.content;

      overlay.el = el;

      if (!options.layer) {
        options.layer = 'overlayLayer';
      }

      var panes = this.getPanes(),
        overlayLayer = panes[options.layer],
        stop_overlay_events = ['contextmenu', 'DOMMouseScroll', 'dblclick', 'mousedown'];

      overlayLayer.appendChild(el);

      for (var ev = 0; ev < stop_overlay_events.length; ev++) {
        (function (object, name) {
          google.maps.event.addDomListener(object, name, function (e) {
            if (navigator.userAgent.toLowerCase().indexOf('msie') != -1 && document.all) {
              e.cancelBubble = true;
              e.returnValue = false;
            }
            else {
              e.stopPropagation();
            }
          });
        })(el, stop_overlay_events[ev]);
      }

      if (options.click) {
        panes.overlayMouseTarget.appendChild(overlay.el);
        google.maps.event.addDomListener(overlay.el, 'click', function () {
          options.click.apply(overlay, [overlay]);
        });
      }

      google.maps.event.trigger(this, 'ready');
    };

    overlay.draw = function () {
      var projection = this.getProjection(),
        pixel = projection.fromLatLngToDivPixel(new google.maps.LatLng(options.lat, options.lng));

      options.horizontalOffset = options.horizontalOffset || 0;
      options.verticalOffset = options.verticalOffset || 0;

      var el = overlay.el,
        content = el.children[0],
        content_height = content.clientHeight,
        content_width = content.clientWidth;

      switch (options.verticalAlign) {
        case 'top':
          el.style.top = (pixel.y - content_height + options.verticalOffset) + 'px';
          break;
        default:
        case 'middle':
          el.style.top = (pixel.y - (content_height / 2) + options.verticalOffset) + 'px';
          break;
        case 'bottom':
          el.style.top = (pixel.y + options.verticalOffset) + 'px';
          break;
      }

      switch (options.horizontalAlign) {
        case 'left':
          el.style.left = (pixel.x - content_width + options.horizontalOffset) + 'px';
          break;
        default:
        case 'center':
          el.style.left = (pixel.x - (content_width / 2) + options.horizontalOffset) + 'px';
          break;
        case 'right':
          el.style.left = (pixel.x + options.horizontalOffset) + 'px';
          break;
      }

      el.style.display = auto_show ? 'block' : 'none';

      if (!auto_show) {
        options.show.apply(this, [el]);
      }
    };

    overlay.onRemove = function () {
      var el = overlay.el;

      if (options.remove) {
        options.remove.apply(this, [el]);
      }
      else {
        overlay.el.parentNode.removeChild(overlay.el);
        overlay.el = null;
      }
    };

    this.overlays.push(overlay);
    return overlay;
  };

  GMaps.prototype.removeOverlay = function (overlay) {
    for (var i = 0; i < this.overlays.length; i++) {
      if (this.overlays[i] === overlay) {
        this.overlays[i].setMap(null);
        this.overlays.splice(i, 1);

        break;
      }
    }
  };

  GMaps.prototype.removeOverlays = function () {
    for (var i = 0, item; item = this.overlays[i]; i++) {
      item.setMap(null);
    }

    this.overlays = [];
  };

  GMaps.prototype.drawPolyline = function (options) {
    var path = [],
      points = options.path;

    if (points.length) {
      if (points[0][0] === undefined) {
        path = points;
      }
      else {
        for (var i = 0, latlng; latlng = points[i]; i++) {
          path.push(new google.maps.LatLng(latlng[0], latlng[1]));
        }
      }
    }

    var polyline_options = {
      map: this.map,
      path: path,
      strokeColor: options.strokeColor,
      strokeOpacity: options.strokeOpacity,
      strokeWeight: options.strokeWeight,
      geodesic: options.geodesic,
      clickable: true,
      editable: false,
      visible: true
    };

    if (options.hasOwnProperty("clickable")) {
      polyline_options.clickable = options.clickable;
    }

    if (options.hasOwnProperty("editable")) {
      polyline_options.editable = options.editable;
    }

    if (options.hasOwnProperty("icons")) {
      polyline_options.icons = options.icons;
    }

    if (options.hasOwnProperty("zIndex")) {
      polyline_options.zIndex = options.zIndex;
    }

    var polyline = new google.maps.Polyline(polyline_options);

    var polyline_events = ['click', 'dblclick', 'mousedown', 'mousemove', 'mouseout', 'mouseover', 'mouseup', 'rightclick'];

    for (var ev = 0; ev < polyline_events.length; ev++) {
      (function (object, name) {
        if (options[name]) {
          google.maps.event.addListener(object, name, function (e) {
            options[name].apply(this, [e]);
          });
        }
      })(polyline, polyline_events[ev]);
    }

    this.polylines.push(polyline);

    GMaps.fire('polyline_added', polyline, this);

    return polyline;
  };

  GMaps.prototype.removePolyline = function (polyline) {
    for (var i = 0; i < this.polylines.length; i++) {
      if (this.polylines[i] === polyline) {
        this.polylines[i].setMap(null);
        this.polylines.splice(i, 1);

        GMaps.fire('polyline_removed', polyline, this);

        break;
      }
    }
  };

  GMaps.prototype.removePolylines = function () {
    for (var i = 0, item; item = this.polylines[i]; i++) {
      item.setMap(null);
    }

    this.polylines = [];
  };

  GMaps.prototype.drawCircle = function (options) {
    options = extend_object({
      map: this.map,
      center: new google.maps.LatLng(options.lat, options.lng)
    }, options);

    delete options.lat;
    delete options.lng;

    var polygon = new google.maps.Circle(options),
      polygon_events = ['click', 'dblclick', 'mousedown', 'mousemove', 'mouseout', 'mouseover', 'mouseup', 'rightclick'];

    for (var ev = 0; ev < polygon_events.length; ev++) {
      (function (object, name) {
        if (options[name]) {
          google.maps.event.addListener(object, name, function (e) {
            options[name].apply(this, [e]);
          });
        }
      })(polygon, polygon_events[ev]);
    }

    this.polygons.push(polygon);

    return polygon;
  };

  GMaps.prototype.drawRectangle = function (options) {
    options = extend_object({
      map: this.map
    }, options);

    var latLngBounds = new google.maps.LatLngBounds(
      new google.maps.LatLng(options.bounds[0][0], options.bounds[0][1]),
      new google.maps.LatLng(options.bounds[1][0], options.bounds[1][1])
    );

    options.bounds = latLngBounds;

    var polygon = new google.maps.Rectangle(options),
      polygon_events = ['click', 'dblclick', 'mousedown', 'mousemove', 'mouseout', 'mouseover', 'mouseup', 'rightclick'];

    for (var ev = 0; ev < polygon_events.length; ev++) {
      (function (object, name) {
        if (options[name]) {
          google.maps.event.addListener(object, name, function (e) {
            options[name].apply(this, [e]);
          });
        }
      })(polygon, polygon_events[ev]);
    }

    this.polygons.push(polygon);

    return polygon;
  };

  GMaps.prototype.drawPolygon = function (options) {
    var useGeoJSON = false;

    if (options.hasOwnProperty("useGeoJSON")) {
      useGeoJSON = options.useGeoJSON;
    }

    delete options.useGeoJSON;

    options = extend_object({
      map: this.map
    }, options);

    if (useGeoJSON == false) {
      options.paths = [options.paths.slice(0)];
    }

    if (options.paths.length > 0) {
      if (options.paths[0].length > 0) {
        options.paths = array_flat(array_map(options.paths, arrayToLatLng, useGeoJSON));
      }
    }

    var polygon = new google.maps.Polygon(options),
      polygon_events = ['click', 'dblclick', 'mousedown', 'mousemove', 'mouseout', 'mouseover', 'mouseup', 'rightclick'];

    for (var ev = 0; ev < polygon_events.length; ev++) {
      (function (object, name) {
        if (options[name]) {
          google.maps.event.addListener(object, name, function (e) {
            options[name].apply(this, [e]);
          });
        }
      })(polygon, polygon_events[ev]);
    }

    this.polygons.push(polygon);

    GMaps.fire('polygon_added', polygon, this);

    return polygon;
  };

  GMaps.prototype.removePolygon = function (polygon) {
    for (var i = 0; i < this.polygons.length; i++) {
      if (this.polygons[i] === polygon) {
        this.polygons[i].setMap(null);
        this.polygons.splice(i, 1);

        GMaps.fire('polygon_removed', polygon, this);

        break;
      }
    }
  };

  GMaps.prototype.removePolygons = function () {
    for (var i = 0, item; item = this.polygons[i]; i++) {
      item.setMap(null);
    }

    this.polygons = [];
  };

  GMaps.prototype.getFromFusionTables = function (options) {
    var events = options.events;

    delete options.events;

    var fusion_tables_options = options,
      layer = new google.maps.FusionTablesLayer(fusion_tables_options);

    for (var ev in events) {
      (function (object, name) {
        google.maps.event.addListener(object, name, function (e) {
          events[name].apply(this, [e]);
        });
      })(layer, ev);
    }

    this.layers.push(layer);

    return layer;
  };

  GMaps.prototype.loadFromFusionTables = function (options) {
    var layer = this.getFromFusionTables(options);
    layer.setMap(this.map);

    return layer;
  };

  GMaps.prototype.getFromKML = function (options) {
    var url = options.url,
      events = options.events;

    delete options.url;
    delete options.events;

    var kml_options = options,
      layer = new google.maps.KmlLayer(url, kml_options);

    for (var ev in events) {
      (function (object, name) {
        google.maps.event.addListener(object, name, function (e) {
          events[name].apply(this, [e]);
        });
      })(layer, ev);
    }

    this.layers.push(layer);

    return layer;
  };

  GMaps.prototype.loadFromKML = function (options) {
    var layer = this.getFromKML(options);
    layer.setMap(this.map);

    return layer;
  };

  GMaps.prototype.addLayer = function (layerName, options) {
    //var default_layers = ['weather', 'clouds', 'traffic', 'transit', 'bicycling', 'panoramio', 'places'];
    options = options || {};
    var layer;

    switch (layerName) {
      case 'weather': this.singleLayers.weather = layer = new google.maps.weather.WeatherLayer();
        break;
      case 'clouds': this.singleLayers.clouds = layer = new google.maps.weather.CloudLayer();
        break;
      case 'traffic': this.singleLayers.traffic = layer = new google.maps.TrafficLayer();
        break;
      case 'transit': this.singleLayers.transit = layer = new google.maps.TransitLayer();
        break;
      case 'bicycling': this.singleLayers.bicycling = layer = new google.maps.BicyclingLayer();
        break;
      case 'panoramio':
        this.singleLayers.panoramio = layer = new google.maps.panoramio.PanoramioLayer();
        layer.setTag(options.filter);
        delete options.filter;

        //click event
        if (options.click) {
          google.maps.event.addListener(layer, 'click', function (event) {
            options.click(event);
            delete options.click;
          });
        }
        break;
      case 'places':
        this.singleLayers.places = layer = new google.maps.places.PlacesService(this.map);

        //search, nearbySearch, radarSearch callback, Both are the same
        if (options.search || options.nearbySearch || options.radarSearch) {
          var placeSearchRequest = {
            bounds: options.bounds || null,
            keyword: options.keyword || null,
            location: options.location || null,
            name: options.name || null,
            radius: options.radius || null,
            rankBy: options.rankBy || null,
            types: options.types || null
          };

          if (options.radarSearch) {
            layer.radarSearch(placeSearchRequest, options.radarSearch);
          }

          if (options.search) {
            layer.search(placeSearchRequest, options.search);
          }

          if (options.nearbySearch) {
            layer.nearbySearch(placeSearchRequest, options.nearbySearch);
          }
        }

        //textSearch callback
        if (options.textSearch) {
          var textSearchRequest = {
            bounds: options.bounds || null,
            location: options.location || null,
            query: options.query || null,
            radius: options.radius || null
          };

          layer.textSearch(textSearchRequest, options.textSearch);
        }
        break;
    }

    if (layer !== undefined) {
      if (typeof layer.setOptions == 'function') {
        layer.setOptions(options);
      }
      if (typeof layer.setMap == 'function') {
        layer.setMap(this.map);
      }

      return layer;
    }
  };

  GMaps.prototype.removeLayer = function (layer) {
    if (typeof (layer) == "string" && this.singleLayers[layer] !== undefined) {
      this.singleLayers[layer].setMap(null);

      delete this.singleLayers[layer];
    }
    else {
      for (var i = 0; i < this.layers.length; i++) {
        if (this.layers[i] === layer) {
          this.layers[i].setMap(null);
          this.layers.splice(i, 1);

          break;
        }
      }
    }
  };

  var travelMode, unitSystem;

  GMaps.prototype.getRoutes = function (options) {
    switch (options.travelMode) {
      case 'bicycling':
        travelMode = google.maps.TravelMode.BICYCLING;
        break;
      case 'transit':
        travelMode = google.maps.TravelMode.TRANSIT;
        break;
      case 'driving':
        travelMode = google.maps.TravelMode.DRIVING;
        break;
      default:
        travelMode = google.maps.TravelMode.WALKING;
        break;
    }

    if (options.unitSystem === 'imperial') {
      unitSystem = google.maps.UnitSystem.IMPERIAL;
    }
    else {
      unitSystem = google.maps.UnitSystem.METRIC;
    }

    var base_options = {
      avoidHighways: false,
      avoidTolls: false,
      optimizeWaypoints: false,
      waypoints: []
    },
      request_options = extend_object(base_options, options);

    request_options.origin = /string/.test(typeof options.origin) ? options.origin : new google.maps.LatLng(options.origin[0], options.origin[1]);
    request_options.destination = /string/.test(typeof options.destination) ? options.destination : new google.maps.LatLng(options.destination[0], options.destination[1]);
    request_options.travelMode = travelMode;
    request_options.unitSystem = unitSystem;

    delete request_options.callback;
    delete request_options.error;

    var self = this,
      routes = [],
      service = new google.maps.DirectionsService();

    service.route(request_options, function (result, status) {
      if (status === google.maps.DirectionsStatus.OK) {
        for (var r in result.routes) {
          if (result.routes.hasOwnProperty(r)) {
            routes.push(result.routes[r]);
          }
        }

        if (options.callback) {
          options.callback(routes, result, status);
        }
      }
      else {
        if (options.error) {
          options.error(result, status);
        }
      }
    });
  };

  GMaps.prototype.removeRoutes = function () {
    this.routes.length = 0;
  };

  GMaps.prototype.getElevations = function (options) {
    options = extend_object({
      locations: [],
      path: false,
      samples: 256
    }, options);

    if (options.locations.length > 0) {
      if (options.locations[0].length > 0) {
        options.locations = array_flat(array_map([options.locations], arrayToLatLng, false));
      }
    }

    var callback = options.callback;
    delete options.callback;

    var service = new google.maps.ElevationService();

    //location request
    if (!options.path) {
      delete options.path;
      delete options.samples;

      service.getElevationForLocations(options, function (result, status) {
        if (callback && typeof (callback) === "function") {
          callback(result, status);
        }
      });
      //path request
    } else {
      var pathRequest = {
        path: options.locations,
        samples: options.samples
      };

      service.getElevationAlongPath(pathRequest, function (result, status) {
        if (callback && typeof (callback) === "function") {
          callback(result, status);
        }
      });
    }
  };

  GMaps.prototype.cleanRoute = GMaps.prototype.removePolylines;

  GMaps.prototype.renderRoute = function (options, renderOptions) {
    var self = this,
      panel = ((typeof renderOptions.panel === 'string') ? document.getElementById(renderOptions.panel.replace('#', '')) : renderOptions.panel),
      display;

    renderOptions.panel = panel;
    renderOptions = extend_object({
      map: this.map
    }, renderOptions);
    display = new google.maps.DirectionsRenderer(renderOptions);

    this.getRoutes({
      origin: options.origin,
      destination: options.destination,
      travelMode: options.travelMode,
      waypoints: options.waypoints,
      unitSystem: options.unitSystem,
      error: options.error,
      avoidHighways: options.avoidHighways,
      avoidTolls: options.avoidTolls,
      optimizeWaypoints: options.optimizeWaypoints,
      callback: function (routes, response, status) {
        if (status === google.maps.DirectionsStatus.OK) {
          display.setDirections(response);
        }
      }
    });
  };

  GMaps.prototype.drawRoute = function (options) {
    var self = this;

    this.getRoutes({
      origin: options.origin,
      destination: options.destination,
      travelMode: options.travelMode,
      waypoints: options.waypoints,
      unitSystem: options.unitSystem,
      error: options.error,
      avoidHighways: options.avoidHighways,
      avoidTolls: options.avoidTolls,
      optimizeWaypoints: options.optimizeWaypoints,
      callback: function (routes) {
        if (routes.length > 0) {
          var polyline_options = {
            path: routes[routes.length - 1].overview_path,
            strokeColor: options.strokeColor,
            strokeOpacity: options.strokeOpacity,
            strokeWeight: options.strokeWeight
          };

          if (options.hasOwnProperty("icons")) {
            polyline_options.icons = options.icons;
          }

          self.drawPolyline(polyline_options);

          if (options.callback) {
            options.callback(routes[routes.length - 1]);
          }
        }
      }
    });
  };

  GMaps.prototype.travelRoute = function (options) {
    if (options.origin && options.destination) {
      this.getRoutes({
        origin: options.origin,
        destination: options.destination,
        travelMode: options.travelMode,
        waypoints: options.waypoints,
        unitSystem: options.unitSystem,
        error: options.error,
        callback: function (e) {
          //start callback
          if (e.length > 0 && options.start) {
            options.start(e[e.length - 1]);
          }

          //step callback
          if (e.length > 0 && options.step) {
            var route = e[e.length - 1];
            if (route.legs.length > 0) {
              var steps = route.legs[0].steps;
              for (var i = 0, step; step = steps[i]; i++) {
                step.step_number = i;
                options.step(step, (route.legs[0].steps.length - 1));
              }
            }
          }

          //end callback
          if (e.length > 0 && options.end) {
            options.end(e[e.length - 1]);
          }
        }
      });
    }
    else if (options.route) {
      if (options.route.legs.length > 0) {
        var steps = options.route.legs[0].steps;
        for (var i = 0, step; step = steps[i]; i++) {
          step.step_number = i;
          options.step(step);
        }
      }
    }
  };

  GMaps.prototype.drawSteppedRoute = function (options) {
    var self = this;

    if (options.origin && options.destination) {
      this.getRoutes({
        origin: options.origin,
        destination: options.destination,
        travelMode: options.travelMode,
        waypoints: options.waypoints,
        error: options.error,
        callback: function (e) {
          //start callback
          if (e.length > 0 && options.start) {
            options.start(e[e.length - 1]);
          }

          //step callback
          if (e.length > 0 && options.step) {
            var route = e[e.length - 1];
            if (route.legs.length > 0) {
              var steps = route.legs[0].steps;
              for (var i = 0, step; step = steps[i]; i++) {
                step.step_number = i;
                var polyline_options = {
                  path: step.path,
                  strokeColor: options.strokeColor,
                  strokeOpacity: options.strokeOpacity,
                  strokeWeight: options.strokeWeight
                };

                if (options.hasOwnProperty("icons")) {
                  polyline_options.icons = options.icons;
                }

                self.drawPolyline(polyline_options);
                options.step(step, (route.legs[0].steps.length - 1));
              }
            }
          }

          //end callback
          if (e.length > 0 && options.end) {
            options.end(e[e.length - 1]);
          }
        }
      });
    }
    else if (options.route) {
      if (options.route.legs.length > 0) {
        var steps = options.route.legs[0].steps;
        for (var i = 0, step; step = steps[i]; i++) {
          step.step_number = i;
          var polyline_options = {
            path: step.path,
            strokeColor: options.strokeColor,
            strokeOpacity: options.strokeOpacity,
            strokeWeight: options.strokeWeight
          };

          if (options.hasOwnProperty("icons")) {
            polyline_options.icons = options.icons;
          }

          self.drawPolyline(polyline_options);
          options.step(step);
        }
      }
    }
  };

  GMaps.Route = function (options) {
    this.origin = options.origin;
    this.destination = options.destination;
    this.waypoints = options.waypoints;

    this.map = options.map;
    this.route = options.route;
    this.step_count = 0;
    this.steps = this.route.legs[0].steps;
    this.steps_length = this.steps.length;

    var polyline_options = {
      path: new google.maps.MVCArray(),
      strokeColor: options.strokeColor,
      strokeOpacity: options.strokeOpacity,
      strokeWeight: options.strokeWeight
    };

    if (options.hasOwnProperty("icons")) {
      polyline_options.icons = options.icons;
    }

    this.polyline = this.map.drawPolyline(polyline_options).getPath();
  };

  GMaps.Route.prototype.getRoute = function (options) {
    var self = this;

    this.map.getRoutes({
      origin: this.origin,
      destination: this.destination,
      travelMode: options.travelMode,
      waypoints: this.waypoints || [],
      error: options.error,
      callback: function () {
        self.route = e[0];

        if (options.callback) {
          options.callback.call(self);
        }
      }
    });
  };

  GMaps.Route.prototype.back = function () {
    if (this.step_count > 0) {
      this.step_count--;
      var path = this.route.legs[0].steps[this.step_count].path;

      for (var p in path) {
        if (path.hasOwnProperty(p)) {
          this.polyline.pop();
        }
      }
    }
  };

  GMaps.Route.prototype.forward = function () {
    if (this.step_count < this.steps_length) {
      var path = this.route.legs[0].steps[this.step_count].path;

      for (var p in path) {
        if (path.hasOwnProperty(p)) {
          this.polyline.push(path[p]);
        }
      }
      this.step_count++;
    }
  };

  GMaps.prototype.checkGeofence = function (lat, lng, fence) {
    return fence.containsLatLng(new google.maps.LatLng(lat, lng));
  };

  GMaps.prototype.checkMarkerGeofence = function (marker, outside_callback) {
    if (marker.fences) {
      for (var i = 0, fence; fence = marker.fences[i]; i++) {
        var pos = marker.getPosition();
        if (!this.checkGeofence(pos.lat(), pos.lng(), fence)) {
          outside_callback(marker, fence);
        }
      }
    }
  };

  GMaps.prototype.toImage = function (options) {
    var options = options || {},
      static_map_options = {};

    static_map_options['size'] = options['size'] || [this.el.clientWidth, this.el.clientHeight];
    static_map_options['lat'] = this.getCenter().lat();
    static_map_options['lng'] = this.getCenter().lng();

    if (this.markers.length > 0) {
      static_map_options['markers'] = [];

      for (var i = 0; i < this.markers.length; i++) {
        static_map_options['markers'].push({
          lat: this.markers[i].getPosition().lat(),
          lng: this.markers[i].getPosition().lng()
        });
      }
    }

    if (this.polylines.length > 0) {
      var polyline = this.polylines[0];

      static_map_options['polyline'] = {};
      static_map_options['polyline']['path'] = google.maps.geometry.encoding.encodePath(polyline.getPath());
      static_map_options['polyline']['strokeColor'] = polyline.strokeColor
      static_map_options['polyline']['strokeOpacity'] = polyline.strokeOpacity
      static_map_options['polyline']['strokeWeight'] = polyline.strokeWeight
    }

    return GMaps.staticMapURL(static_map_options);
  };

  GMaps.staticMapURL = function (options) {
    var parameters = [],
      data,
      static_root = (location.protocol === 'file:' ? 'http:' : location.protocol) + '//maps.googleapis.com/maps/api/staticmap';

    if (options.url) {
      static_root = options.url;
      delete options.url;
    }

    static_root += '?';

    var markers = options.markers;

    delete options.markers;

    if (!markers && options.marker) {
      markers = [options.marker];
      delete options.marker;
    }

    var styles = options.styles;

    delete options.styles;

    var polyline = options.polyline;
    delete options.polyline;

    /** Map options **/
    if (options.center) {
      parameters.push('center=' + options.center);
      delete options.center;
    }
    else if (options.address) {
      parameters.push('center=' + options.address);
      delete options.address;
    }
    else if (options.lat) {
      parameters.push(['center=', options.lat, ',', options.lng].join(''));
      delete options.lat;
      delete options.lng;
    }
    else if (options.visible) {
      var visible = encodeURI(options.visible.join('|'));
      parameters.push('visible=' + visible);
    }

    var size = options.size;
    if (size) {
      if (size.join) {
        size = size.join('x');
      }
      delete options.size;
    }
    else {
      size = '630x300';
    }
    parameters.push('size=' + size);

    if (!options.zoom && options.zoom !== false) {
      options.zoom = 15;
    }

    var sensor = options.hasOwnProperty('sensor') ? !!options.sensor : true;
    delete options.sensor;
    parameters.push('sensor=' + sensor);

    for (var param in options) {
      if (options.hasOwnProperty(param)) {
        parameters.push(param + '=' + options[param]);
      }
    }

    /** Markers **/
    if (markers) {
      var marker, loc;

      for (var i = 0; data = markers[i]; i++) {
        marker = [];

        if (data.size && data.size !== 'normal') {
          marker.push('size:' + data.size);
          delete data.size;
        }
        else if (data.icon) {
          marker.push('icon:' + encodeURI(data.icon));
          delete data.icon;
        }

        if (data.color) {
          marker.push('color:' + data.color.replace('#', '0x'));
          delete data.color;
        }

        if (data.label) {
          marker.push('label:' + data.label[0].toUpperCase());
          delete data.label;
        }

        loc = (data.address ? data.address : data.lat + ',' + data.lng);
        delete data.address;
        delete data.lat;
        delete data.lng;

        for (var param in data) {
          if (data.hasOwnProperty(param)) {
            marker.push(param + ':' + data[param]);
          }
        }

        if (marker.length || i === 0) {
          marker.push(loc);
          marker = marker.join('|');
          parameters.push('markers=' + encodeURI(marker));
        }
        // New marker without styles
        else {
          marker = parameters.pop() + encodeURI('|' + loc);
          parameters.push(marker);
        }
      }
    }

    /** Map Styles **/
    if (styles) {
      for (var i = 0; i < styles.length; i++) {
        var styleRule = [];
        if (styles[i].featureType) {
          styleRule.push('feature:' + styles[i].featureType.toLowerCase());
        }

        if (styles[i].elementType) {
          styleRule.push('element:' + styles[i].elementType.toLowerCase());
        }

        for (var j = 0; j < styles[i].stylers.length; j++) {
          for (var p in styles[i].stylers[j]) {
            var ruleArg = styles[i].stylers[j][p];
            if (p == 'hue' || p == 'color') {
              ruleArg = '0x' + ruleArg.substring(1);
            }
            styleRule.push(p + ':' + ruleArg);
          }
        }

        var rule = styleRule.join('|');
        if (rule != '') {
          parameters.push('style=' + rule);
        }
      }
    }

    /** Polylines **/
    function parseColor(color, opacity) {
      if (color[0] === '#') {
        color = color.replace('#', '0x');

        if (opacity) {
          opacity = parseFloat(opacity);
          opacity = Math.min(1, Math.max(opacity, 0));
          if (opacity === 0) {
            return '0x00000000';
          }
          opacity = (opacity * 255).toString(16);
          if (opacity.length === 1) {
            opacity += opacity;
          }

          color = color.slice(0, 8) + opacity;
        }
      }
      return color;
    }

    if (polyline) {
      data = polyline;
      polyline = [];

      if (data.strokeWeight) {
        polyline.push('weight:' + parseInt(data.strokeWeight, 10));
      }

      if (data.strokeColor) {
        var color = parseColor(data.strokeColor, data.strokeOpacity);
        polyline.push('color:' + color);
      }

      if (data.fillColor) {
        var fillcolor = parseColor(data.fillColor, data.fillOpacity);
        polyline.push('fillcolor:' + fillcolor);
      }

      var path = data.path;
      if (path.join) {
        for (var j = 0, pos; pos = path[j]; j++) {
          polyline.push(pos.join(','));
        }
      }
      else {
        polyline.push('enc:' + path);
      }

      polyline = polyline.join('|');
      parameters.push('path=' + encodeURI(polyline));
    }

    /** Retina support **/
    var dpi = window.devicePixelRatio || 1;
    parameters.push('scale=' + dpi);

    parameters = parameters.join('&');
    return static_root + parameters;
  };

  GMaps.prototype.addMapType = function (mapTypeId, options) {
    if (options.hasOwnProperty("getTileUrl") && typeof (options["getTileUrl"]) == "function") {
      options.tileSize = options.tileSize || new google.maps.Size(256, 256);

      var mapType = new google.maps.ImageMapType(options);

      this.map.mapTypes.set(mapTypeId, mapType);
    }
    else {
      throw "'getTileUrl' function required.";
    }
  };

  GMaps.prototype.addOverlayMapType = function (options) {
    if (options.hasOwnProperty("getTile") && typeof (options["getTile"]) == "function") {
      var overlayMapTypeIndex = options.index;

      delete options.index;

      this.map.overlayMapTypes.insertAt(overlayMapTypeIndex, options);
    }
    else {
      throw "'getTile' function required.";
    }
  };

  GMaps.prototype.removeOverlayMapType = function (overlayMapTypeIndex) {
    this.map.overlayMapTypes.removeAt(overlayMapTypeIndex);
  };

  GMaps.prototype.addStyle = function (options) {
    var styledMapType = new google.maps.StyledMapType(options.styles, { name: options.styledMapName });

    this.map.mapTypes.set(options.mapTypeId, styledMapType);
  };

  GMaps.prototype.setStyle = function (mapTypeId) {
    this.map.setMapTypeId(mapTypeId);
  };

  GMaps.prototype.createPanorama = function (streetview_options) {
    if (!streetview_options.hasOwnProperty('lat') || !streetview_options.hasOwnProperty('lng')) {
      streetview_options.lat = this.getCenter().lat();
      streetview_options.lng = this.getCenter().lng();
    }

    this.panorama = GMaps.createPanorama(streetview_options);

    this.map.setStreetView(this.panorama);

    return this.panorama;
  };

  GMaps.createPanorama = function (options) {
    var el = getElementById(options.el, options.context);

    options.position = new google.maps.LatLng(options.lat, options.lng);

    delete options.el;
    delete options.context;
    delete options.lat;
    delete options.lng;

    var streetview_events = ['closeclick', 'links_changed', 'pano_changed', 'position_changed', 'pov_changed', 'resize', 'visible_changed'],
      streetview_options = extend_object({ visible: true }, options);

    for (var i = 0; i < streetview_events.length; i++) {
      delete streetview_options[streetview_events[i]];
    }

    var panorama = new google.maps.StreetViewPanorama(el, streetview_options);

    for (var i = 0; i < streetview_events.length; i++) {
      (function (object, name) {
        if (options[name]) {
          google.maps.event.addListener(object, name, function () {
            options[name].apply(this);
          });
        }
      })(panorama, streetview_events[i]);
    }

    return panorama;
  };

  GMaps.prototype.on = function (event_name, handler) {
    return GMaps.on(event_name, this, handler);
  };

  GMaps.prototype.off = function (event_name) {
    GMaps.off(event_name, this);
  };

  GMaps.prototype.once = function (event_name, handler) {
    return GMaps.once(event_name, this, handler);
  };

  GMaps.custom_events = ['marker_added', 'marker_removed', 'polyline_added', 'polyline_removed', 'polygon_added', 'polygon_removed', 'geolocated', 'geolocation_failed'];

  GMaps.on = function (event_name, object, handler) {
    if (GMaps.custom_events.indexOf(event_name) == -1) {
      if (object instanceof GMaps) object = object.map;
      return google.maps.event.addListener(object, event_name, handler);
    }
    else {
      var registered_event = {
        handler: handler,
        eventName: event_name
      };

      object.registered_events[event_name] = object.registered_events[event_name] || [];
      object.registered_events[event_name].push(registered_event);

      return registered_event;
    }
  };

  GMaps.off = function (event_name, object) {
    if (GMaps.custom_events.indexOf(event_name) == -1) {
      if (object instanceof GMaps) object = object.map;
      google.maps.event.clearListeners(object, event_name);
    }
    else {
      object.registered_events[event_name] = [];
    }
  };

  GMaps.once = function (event_name, object, handler) {
    if (GMaps.custom_events.indexOf(event_name) == -1) {
      if (object instanceof GMaps) object = object.map;
      return google.maps.event.addListenerOnce(object, event_name, handler);
    }
  };

  GMaps.fire = function (event_name, object, scope) {
    if (GMaps.custom_events.indexOf(event_name) == -1) {
      google.maps.event.trigger(object, event_name, Array.prototype.slice.apply(arguments).slice(2));
    }
    else {
      if (event_name in scope.registered_events) {
        var firing_events = scope.registered_events[event_name];

        for (var i = 0; i < firing_events.length; i++) {
          (function (handler, scope, object) {
            handler.apply(scope, [object]);
          })(firing_events[i]['handler'], scope, object);
        }
      }
    }
  };

  GMaps.geolocate = function (options) {
    var complete_callback = options.always || options.complete;

    if (navigator.geolocation) {
      navigator.geolocation.getCurrentPosition(function (position) {
        options.success(position);

        if (complete_callback) {
          complete_callback();
        }
      }, function (error) {
        options.error(error);

        if (complete_callback) {
          complete_callback();
        }
      }, options.options);
    }
    else {
      options.not_supported();

      if (complete_callback) {
        complete_callback();
      }
    }
  };

  GMaps.geocode = function (options) {
    this.geocoder = new google.maps.Geocoder();
    var callback = options.callback;
    if (options.hasOwnProperty('lat') && options.hasOwnProperty('lng')) {
      options.latLng = new google.maps.LatLng(options.lat, options.lng);
    }

    delete options.lat;
    delete options.lng;
    delete options.callback;

    this.geocoder.geocode(options, function (results, status) {
      callback(results, status);
    });
  };

  if (typeof window.google === 'object' && window.google.maps) {
    //==========================
    // Polygon containsLatLng
    // https://github.com/tparkin/Google-Maps-Point-in-Polygon
    // Poygon getBounds extension - google-maps-extensions
    // http://code.google.com/p/google-maps-extensions/source/browse/google.maps.Polygon.getBounds.js
    if (!google.maps.Polygon.prototype.getBounds) {
      google.maps.Polygon.prototype.getBounds = function (latLng) {
        var bounds = new google.maps.LatLngBounds();
        var paths = this.getPaths();
        var path;

        for (var p = 0; p < paths.getLength(); p++) {
          path = paths.getAt(p);
          for (var i = 0; i < path.getLength(); i++) {
            bounds.extend(path.getAt(i));
          }
        }

        return bounds;
      };
    }

    if (!google.maps.Polygon.prototype.containsLatLng) {
      // Polygon containsLatLng - method to determine if a latLng is within a polygon
      google.maps.Polygon.prototype.containsLatLng = function (latLng) {
        // Exclude points outside of bounds as there is no way they are in the poly
        var bounds = this.getBounds();

        if (bounds !== null && !bounds.contains(latLng)) {
          return false;
        }

        // Raycast point in polygon method
        var inPoly = false;

        var numPaths = this.getPaths().getLength();
        for (var p = 0; p < numPaths; p++) {
          var path = this.getPaths().getAt(p);
          var numPoints = path.getLength();
          var j = numPoints - 1;

          for (var i = 0; i < numPoints; i++) {
            var vertex1 = path.getAt(i);
            var vertex2 = path.getAt(j);

            if (vertex1.lng() < latLng.lng() && vertex2.lng() >= latLng.lng() || vertex2.lng() < latLng.lng() && vertex1.lng() >= latLng.lng()) {
              if (vertex1.lat() + (latLng.lng() - vertex1.lng()) / (vertex2.lng() - vertex1.lng()) * (vertex2.lat() - vertex1.lat()) < latLng.lat()) {
                inPoly = !inPoly;
              }
            }

            j = i;
          }
        }

        return inPoly;
      };
    }

    if (!google.maps.Circle.prototype.containsLatLng) {
      google.maps.Circle.prototype.containsLatLng = function (latLng) {
        if (google.maps.geometry) {
          return google.maps.geometry.spherical.computeDistanceBetween(this.getCenter(), latLng) <= this.getRadius();
        }
        else {
          return true;
        }
      };
    }

    google.maps.Rectangle.prototype.containsLatLng = function (latLng) {
      return this.getBounds().contains(latLng);
    };

    google.maps.LatLngBounds.prototype.containsLatLng = function (latLng) {
      return this.contains(latLng);
    };

    google.maps.Marker.prototype.setFences = function (fences) {
      this.fences = fences;
    };

    google.maps.Marker.prototype.addFence = function (fence) {
      this.fences.push(fence);
    };

    google.maps.Marker.prototype.getId = function () {
      return this['__gm_id'];
    };
  }

  //==========================
  // Array indexOf
  // https://developer.mozilla.org/en-US/docs/JavaScript/Reference/Global_Objects/Array/indexOf
  if (!Array.prototype.indexOf) {
    Array.prototype.indexOf = function (searchElement /*, fromIndex */) {
      "use strict";
      if (this == null) {
        throw new TypeError();
      }
      var t = Object(this);
      var len = t.length >>> 0;
      if (len === 0) {
        return -1;
      }
      var n = 0;
      if (arguments.length > 1) {
        n = Number(arguments[1]);
        if (n != n) { // shortcut for verifying if it's NaN
          n = 0;
        } else if (n != 0 && n != Infinity && n != -Infinity) {
          n = (n > 0 || -1) * Math.floor(Math.abs(n));
        }
      }
      if (n >= len) {
        return -1;
      }
      var k = n >= 0 ? n : Math.max(len - Math.abs(n), 0);
      for (; k < len; k++) {
        if (k in t && t[k] === searchElement) {
          return k;
        }
      }
      return -1;
    }
  }

  return GMaps;
}));