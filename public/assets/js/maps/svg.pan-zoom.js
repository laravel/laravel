/*!
 * SVG.js Pan Zoom Plugin
 * ======================
 *
 * A JavaScript library for pan and zoom SVG things.
 * Created with <3 and JavaScript by the jillix developers.
 *
 * svg.pan-zoom.js 2.8.0
 * Licensed under the MIT license.
 * */
;(function() {

    var container = null
      , markers = null
      , mousewheel = "onwheel" in document.createElement("div")
      ? "wheel"
      : document.onmousewheel !== undefined
      ? "mousewheel"
      : "DOMMouseScroll"
      ;

    /**
     * panZoom
     * The pan-zoom contructor.
     *
     * @name panZoom
     * @function
     * @param {Object|undefined} opt_options An optional object containing the following fields:
     *
     *  - `zoom` (Array): An array of two float values: the minimum and maximum zoom values (default: `undefined`).
     *  - `zoomSpeed` (Number): The zoom speed (default: `-1`). By changing the sign the zoom is reversed.
     *
     * @return {PanZoom} The PanZoom object containing the following fields:
     *
     *  - `elm` (SVG): The selected element.
     *  - `pan` (Object): An object containing pan values.
     *  - `transform` (Object): An object containing the transform data (`scaleX`, `scaleY`, `x` and `y`).
     *
     */
    function panZoom(opt_options) {

        // Selected element
        var self = this;

        /**
         * setPosition
         * Sets the graph position programatically.
         *
         * @name setPosition
         * @function
         * @param {Number} x The relative position to the svg document (on x axis).
         * @param {Number} y The relative position to the svg document (on y axis).
         * @param {Number} z The zoom value which will be handled as `scale` internally.
         * @return {PanZoom} The `PanZoom` instance.
         */
        function setPosition(x, y, z) {
            pz.pan.iPos = pz.pan.fPos;
            pz.transform = self.transform();
            pz.transform.x = x;
            pz.transform.y = y;
            if (typeof z === "number") {
                pz.zoom(z);
            } else {
                updateMatrix();
            }
            return pz;
        }

        /**
         * zoom
         * Zooms in/out the graph programatically.
         *
         * @name zoom
         * @function
         * @param {Number} z The zoom value which will be handled as `scale` internally.
         * @param {Number} oX An optional origin point `x` coordinate.
         * @param {Number} oY An optional origin point `y` coordinate.
         * @param {Event} ev An optional event object. If provided, the `zoom` event will be triggered.
         * @return {PanZoom} The `PanZoom` instance.
         */
        function zoom(z, oX, oY, ev) {

            // Handle zoom restrictions
            if (z > opt_options.zoom[1] || z < opt_options.zoom[0]) {
                return;
            }

            if (arguments.length === 1) {
                pz.transform = pz.transform || self.transform();
                pz.transform.scaleY = pz.transform.scaleX = z;
                updateMatrix();
                return pz;
            }

            var tr = pz.transform || self.transform()

                // Get the current x, y
              , currentX = tr.x
              , currentY = tr.y
              , scaleD = z / tr.scaleX

                // Compute the final x, y
              , x = scaleD * (currentX - oX) + oX
              , y = scaleD * (currentY - oY) + oY
              ;

            setPosition(x, y, z);

            if (ev) {
                self.node.dispatchEvent(new CustomEvent("zoom", { detail: { e: ev, tr: tr } }));
            }
        }

        // Pan zoom object
        var pz = {
            pan: {}
          , elm: self
          , setPosition: setPosition
          , zoom: zoom
        };

        // Set options
        opt_options = Object(opt_options);
        opt_options.zoom = opt_options.zoom || [];
        opt_options.zoomSpeed = typeof opt_options.zoomSpeed === "number" ? opt_options.zoomSpeed : -1;

        // Get the svg document
        var svg = self;
        while ((svg = self.parent()).node.tagName.toUpperCase() !== "SVG") {}

        // Create the rectangle
        var rect = new SVG(document.createDocumentFragment()).rect().attr({
            width: svg.width()
          , height: svg.height()
          , fill: "none"
        }).style("pointer-events", "all");

        // Insert the rectangle
        self.parent().node.insertBefore(rect.node, self.node);

        /*!
         * updateMatrix
         * An internal function called to update the svg matrix.
         *
         * @name updateMatrix
         * @function
         * @return {undefined}
         */
        function updateMatrix() {
            self.attr("transform", "matrix(" + [
                pz.transform.scaleX
              , 0, 0
              , pz.transform.scaleY
              , pz.transform.x
              , pz.transform.y
            ].join(",")+ ")");
        }

        /*!
         * pan
         * The internal function called for panning.
         *
         * @name pan
         * @function
         * @param {Event} e The internal listener event.
         * @return {undefined}
         */
        function pan(e) {
            if (!pz.pan.mousedown) {
                return;
            }
            var tr = pz.transform = self.transform();
            var diffX = pz.pan.fPos.x - pz.pan.iPos.x;
            var diffY = pz.pan.fPos.y - pz.pan.iPos.y;
            pz.setPosition(tr.x + diffX, tr.y + diffY);
            self.node.dispatchEvent(new CustomEvent("pan", { detail: { e: e, tr: tr } }));
        }

        /*!
         * mousePos
         * Returns the mouse point coordinates.
         *
         * @name mousePos
         * @function
         * @param {Event} e The mouse event.
         * @param {Boolean} rel If `true`, the relative coordinates will be returned instead.
         * @return {Object} An object containing the relative coordinates.
         */
        function mousePos(e, rel) {
            var bbox = self.parent().node.getBoundingClientRect()
              , abs = {
                    x: e.clientX || e.touches[0].pageX
                  , y: e.clientY || e.touches[0].pageY
                }
              ;
            if (!rel) { return abs; }
            return {
                x: abs.x - bbox.left
              , y: abs.y - bbox.top
            };
        }

        /*!
         * zoom
         * The internal function called for zooming.
         *
         * @name zoom
         * @function
         * @param {Event} e The internal listener event.
         * @return {undefined}
         */
        function doZoom (e) {

            // Get the relative mouse point
            var rP = mousePos(e, true)
              , oX = rP.x
              , oY = rP.y
              ;

            e.deltaY = e.deltaY || e.wheelDeltaY || -e.wheelDelta;

            // Compute the new scale
            var d = opt_options.zoomSpeed * e.deltaY / 1000
              , tr = pz.transform = self.transform()
              , scale = tr.scaleX + (tr.scaleX * d)
              ;

            zoom(scale, oX, oY, e);

            // Prevent the default browser behavior
            e.preventDefault();
        }


        // The event listeners
        var EventListeners = {
            mouse_down: function (e) {
                pz.pan.mousedown = true;
                pz.pan.iPos = mousePos(e);
            }
          , mouse_up: function (e) {
                pz.pan.mousedown = false;
                pz.pan.fPos = mousePos(e);
                pan();
            }
          , mouse_move: function (e) {
                if (!pz.pan.mousedown) { return; }
                pz.pan.fPos = mousePos(e);
                pan();
            }
          , mouse_leave: function (e) {
                pz.pan.mousedown = false;
            }
        };

        // Add event listeners
        rect
          .on(mousewheel, doZoom)
          .on("mousedown", EventListeners.mouse_down)
          .on("touchstart", EventListeners.mouse_down)
          .on("mousemove", EventListeners.mouse_move)
          .on("touchmove", EventListeners.mouse_move)
          .on("mouseup", EventListeners.mouse_up)
          .on("touchup", EventListeners.mouse_up)
          .on("mouseleave", EventListeners.mouse_leave)
          ;

        self.on(mousewheel, doZoom);
        return pz;
    }

    // Extend the SVG.Element with the new function
    SVG.extend(SVG.Element, {
        panZoom: panZoom
    });
}).call(this);
