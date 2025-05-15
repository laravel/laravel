(function() {
    SVG.extend(SVG.Element, {
        /**
         * draggy
         * Makes an element draggable.
         *
         * @name draggy
         * @function
         * @param {Object|Function} constraint An object containing the
         * constraint values or a function which gets the `x` and `y` values
         * and returns a boolean or an object containing the `x` and `y`
         * boolean values.`false` skips moving while `true` allows it.
         * @return {SVG} The SVG element.
         */
        draggy: function (constraint) {

            var start
              , drag
              , end
              , element = this
              ;

            // Remove draggable if already present
            if (typeof this.fixed === "function") {
                this.fixed();
            }

            // Ensure constraint object
            constraint = constraint || {};

            // Start dragging
            start = function(event) {
                var parent = this.parent(SVG.Nested) || this.parent(SVG.Doc);
                event = event || window.event;

                // Invoke any callbacks
                if (element.beforedrag) {
                    element.beforedrag(event);
                }

                // Get element bounding box
                var box = element.bbox();

                if (element instanceof SVG.G) {
                    box.x = element.x();
                    box.y = element.y();
                } else if (element instanceof SVG.Nested) {
                    box = {
                        x: element.x()
                      , y: element.y()
                      , width: element.width()
                      , height: element.height()
                    };
                }

                // Store event
                element.startEvent = event;

                // Store start position
                element.startPosition = {
                    x: box.x
                  , y: box.y
                  , width: box.width
                  , height: box.height
                  , zoom: parent.viewbox().zoom
                  , rotation: element.transform("rotation") * Math.PI / 180
                };

                // Add while and end events to window
                SVG.on(window, "mousemove", drag);
                SVG.on(window, "touchmove", drag);

                SVG.on(window, "mouseup", end);
                SVG.on(window, "touchend", end);

                // Invoke any callbacks
                element.node.dispatchEvent(new CustomEvent("dragstart", {
                    detail: {
                        event: event
                      , delta: {
                            x: 0
                          , y: 0
                        }
                    }
                }));

                // Prevent selection dragging
                if (event.preventDefault) {
                    event.preventDefault();
                } else {
                    event.returnValue = false;
                }
            };

            function elmZoom(elm) {
                if (!elm || typeof elm.transform !== "function") { return { x: 1, y: 1 }; }
                var p = elm.parent();
                var t = elm.transform();
                pz = {};
                var pz = elmZoom(p);
                return {
                    x: t.scaleX * pz.x
                  , y: t.scaleY * pz.y
                };
            }

            // While dragging
            drag = function(event) {
                event = event || window.event;

                if (element.startEvent) {
                    // Calculate move position
                    var x
                      , y
                      , rotation = element.startPosition.rotation
                      , width = element.startPosition.width
                      , height = element.startPosition.height
                      , delta = {
                            x: event.pageX - element.startEvent.pageX
                          , y: event.pageY - element.startEvent.pageY
                        }
                      ;

                    if (/^touchstart|touchmove$/.test(event.type)) {
                        delta.x = event.touches[0].pageX - element.startEvent.touches[0].pageX;
                        delta.y = event.touches[0].pageY - element.startEvent.touches[0].pageY;
                    } else if(/^click|mousedown|mousemove$/.test(event.type)) {
                        delta.x = event.pageX - element.startEvent.pageX;
                        delta.y = event.pageY - element.startEvent.pageY;
                    }

                    delta.scale = elmZoom(element);

                    x = element.startPosition.x + (delta.x * Math.cos(rotation) + delta.y * Math.sin(rotation)) / Math.pow(delta.scale.x, 1);
                    y = element.startPosition.y + (delta.y * Math.cos(rotation) + delta.x * Math.sin(-rotation)) / Math.pow(delta.scale.y, 1);

                    // Move the element to its new position, if possible by constraint
                    if (typeof constraint === "function") {
                        var coord = constraint(x, y);
                        if (typeof coord === "object") {
                            if (typeof coord.x !== "boolean" || coord.x) {
                                x = typeof coord.x === "number" ? coord.x : x;
                                element.x(x);
                            } else {
                                x = element.x();
                            }

                            if (typeof coord.y !== "boolean" || coord.y) {
                                y = typeof coord.y === "number" ? coord.y : y;
                                element.y(y);
                            } else {
                                y = element.y();
                            }
                        } else if (typeof coord === "boolean" && coord) {
                            element.move(x, y);
                        } else {
                            x = element.x();
                            y = element.y();
                        }
                    } else if (typeof constraint === "object") {
                        // Keep element within constrained box
                        if (constraint.minX !== null && x < constraint.minX) {
                            x = constraint.minX;
                        } else if (constraint.maxX !== null && x > constraint.maxX - width) {
                            x = constraint.maxX - width;
                        }

                        if (constraint.minY !== null && y < constraint.minY) {
                            y = constraint.minY;
                        } else if (constraint.maxY !== null && y > constraint.maxY - height) {
                            y = constraint.maxY - height;
                        }

                        element.move(x, y);
                    }

                    // Calculate the total movement
                    delta.movedX = x - element.startPosition.x;
                    delta.movedY = y - element.startPosition.y;

                    // Invoke any callbacks
                    element.node.dispatchEvent(new CustomEvent("dragmove", {
                        detail: {
                            delta: delta
                          , event: event
                        }
                    }));
                }
            };

            // When dragging ends
            end = function(event) {
                event = event || window.event;

                // Calculate move position
                var delta = {
                    x: event.pageX - element.startEvent.pageX
                  , y: event.pageY - element.startEvent.pageY
                  , zoom: element.startPosition.zoom
                };

                // Reset store
                element.startEvent = null;
                element.startPosition = null;

                // Remove while and end events to window
                SVG.off(window, "mousemove", drag);
                SVG.off(window, "touchmove", drag);
                SVG.off(window, "mouseup", end);
                SVG.off(window, "touchend", end);

                // Invoke any callbacks
                element.node.dispatchEvent(new CustomEvent("dragend", {
                    detail: {
                        delta: {
                            x: 0
                          , y: 0
                        }
                      , event: event
                    }
                }));
            };

            // Bind mousedown event
            element.on("mousedown", start);
            element.on("touchstart", start);

            // Disable draggable
            element.fixed = function() {
                element.off("mousedown", start);
                element.off("touchstart", start);

                SVG.off(window, "mousemove", drag);
                SVG.off(window, "touchmove", drag);
                SVG.off(window, "mouseup", end);
                SVG.off(window, "touchend", end);

                start = drag = end = null;

                return element;
            };

            return this;
        }
    });
}).call(this);
