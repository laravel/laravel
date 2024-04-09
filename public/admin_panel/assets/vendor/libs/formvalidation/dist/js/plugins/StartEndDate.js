/**
 * FormValidation (https://formvalidation.io), v1.10.0 (2236098)
 * The best validation library for JavaScript
 * (c) 2013 - 2021 Nguyen Huu Phuoc <me@phuoc.ng>
 */

(function (global, factory) {
  typeof exports === 'object' && typeof module !== 'undefined' ? module.exports = factory() :
  typeof define === 'function' && define.amd ? define(factory) :
  (global = typeof globalThis !== 'undefined' ? globalThis : global || self, (global.FormValidation = global.FormValidation || {}, global.FormValidation.plugins = global.FormValidation.plugins || {}, global.FormValidation.plugins.StartEndDate = factory()));
})(this, (function () { 'use strict';

  function _classCallCheck(instance, Constructor) {
    if (!(instance instanceof Constructor)) {
      throw new TypeError("Cannot call a class as a function");
    }
  }

  function _defineProperties(target, props) {
    for (var i = 0; i < props.length; i++) {
      var descriptor = props[i];
      descriptor.enumerable = descriptor.enumerable || false;
      descriptor.configurable = true;
      if ("value" in descriptor) descriptor.writable = true;
      Object.defineProperty(target, descriptor.key, descriptor);
    }
  }

  function _createClass(Constructor, protoProps, staticProps) {
    if (protoProps) _defineProperties(Constructor.prototype, protoProps);
    if (staticProps) _defineProperties(Constructor, staticProps);
    Object.defineProperty(Constructor, "prototype", {
      writable: false
    });
    return Constructor;
  }

  function _inherits(subClass, superClass) {
    if (typeof superClass !== "function" && superClass !== null) {
      throw new TypeError("Super expression must either be null or a function");
    }

    subClass.prototype = Object.create(superClass && superClass.prototype, {
      constructor: {
        value: subClass,
        writable: true,
        configurable: true
      }
    });
    Object.defineProperty(subClass, "prototype", {
      writable: false
    });
    if (superClass) _setPrototypeOf(subClass, superClass);
  }

  function _getPrototypeOf(o) {
    _getPrototypeOf = Object.setPrototypeOf ? Object.getPrototypeOf.bind() : function _getPrototypeOf(o) {
      return o.__proto__ || Object.getPrototypeOf(o);
    };
    return _getPrototypeOf(o);
  }

  function _setPrototypeOf(o, p) {
    _setPrototypeOf = Object.setPrototypeOf ? Object.setPrototypeOf.bind() : function _setPrototypeOf(o, p) {
      o.__proto__ = p;
      return o;
    };
    return _setPrototypeOf(o, p);
  }

  function _isNativeReflectConstruct() {
    if (typeof Reflect === "undefined" || !Reflect.construct) return false;
    if (Reflect.construct.sham) return false;
    if (typeof Proxy === "function") return true;

    try {
      Boolean.prototype.valueOf.call(Reflect.construct(Boolean, [], function () {}));
      return true;
    } catch (e) {
      return false;
    }
  }

  function _assertThisInitialized(self) {
    if (self === void 0) {
      throw new ReferenceError("this hasn't been initialised - super() hasn't been called");
    }

    return self;
  }

  function _possibleConstructorReturn(self, call) {
    if (call && (typeof call === "object" || typeof call === "function")) {
      return call;
    } else if (call !== void 0) {
      throw new TypeError("Derived constructors may only return object or undefined");
    }

    return _assertThisInitialized(self);
  }

  function _createSuper(Derived) {
    var hasNativeReflectConstruct = _isNativeReflectConstruct();

    return function _createSuperInternal() {
      var Super = _getPrototypeOf(Derived),
          result;

      if (hasNativeReflectConstruct) {
        var NewTarget = _getPrototypeOf(this).constructor;

        result = Reflect.construct(Super, arguments, NewTarget);
      } else {
        result = Super.apply(this, arguments);
      }

      return _possibleConstructorReturn(this, result);
    };
  }

  var t = FormValidation.Plugin;

  var e = /*#__PURE__*/function (_t) {
    _inherits(e, _t);

    var _super = _createSuper(e);

    function e(t) {
      var _this;

      _classCallCheck(this, e);

      _this = _super.call(this, t);
      _this.fieldValidHandler = _this.onFieldValid.bind(_assertThisInitialized(_this));
      _this.fieldInvalidHandler = _this.onFieldInvalid.bind(_assertThisInitialized(_this));
      return _this;
    }

    _createClass(e, [{
      key: "install",
      value: function install() {
        var _this2 = this;

        var t = this.core.getFields();
        this.startDateFieldOptions = t[this.opts.startDate.field];
        this.endDateFieldOptions = t[this.opts.endDate.field];

        var _e = this.core.getFormElement();

        this.core.on("core.field.valid", this.fieldValidHandler).on("core.field.invalid", this.fieldInvalidHandler).addField(this.opts.startDate.field, {
          validators: {
            date: {
              format: this.opts.format,
              max: function max() {
                var t = _e.querySelector("[name=\"".concat(_this2.opts.endDate.field, "\"]"));

                return t.value;
              },
              message: this.opts.startDate.message
            }
          }
        }).addField(this.opts.endDate.field, {
          validators: {
            date: {
              format: this.opts.format,
              message: this.opts.endDate.message,
              min: function min() {
                var t = _e.querySelector("[name=\"".concat(_this2.opts.startDate.field, "\"]"));

                return t.value;
              }
            }
          }
        });
      }
    }, {
      key: "uninstall",
      value: function uninstall() {
        this.core.removeField(this.opts.startDate.field);

        if (this.startDateFieldOptions) {
          this.core.addField(this.opts.startDate.field, this.startDateFieldOptions);
        }

        this.core.removeField(this.opts.endDate.field);

        if (this.endDateFieldOptions) {
          this.core.addField(this.opts.endDate.field, this.endDateFieldOptions);
        }

        this.core.off("core.field.valid", this.fieldValidHandler).off("core.field.invalid", this.fieldInvalidHandler);
      }
    }, {
      key: "onFieldInvalid",
      value: function onFieldInvalid(t) {
        switch (t) {
          case this.opts.startDate.field:
            this.startDateValid = false;
            break;

          case this.opts.endDate.field:
            this.endDateValid = false;
            break;
        }
      }
    }, {
      key: "onFieldValid",
      value: function onFieldValid(t) {
        switch (t) {
          case this.opts.startDate.field:
            this.startDateValid = true;

            if (this.endDateValid === false) {
              this.core.revalidateField(this.opts.endDate.field);
            }

            break;

          case this.opts.endDate.field:
            this.endDateValid = true;

            if (this.startDateValid === false) {
              this.core.revalidateField(this.opts.startDate.field);
            }

            break;
        }
      }
    }]);

    return e;
  }(t);

  return e;

}));
