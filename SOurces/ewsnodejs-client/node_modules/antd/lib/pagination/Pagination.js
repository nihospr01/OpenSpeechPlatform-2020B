"use strict";

Object.defineProperty(exports, "__esModule", {
  value: true
});
exports["default"] = void 0;

var React = _interopRequireWildcard(require("react"));

var _rcPagination = _interopRequireDefault(require("rc-pagination"));

var _en_US = _interopRequireDefault(require("rc-pagination/lib/locale/en_US"));

var _classnames = _interopRequireDefault(require("classnames"));

var _MiniSelect = _interopRequireDefault(require("./MiniSelect"));

var _icon = _interopRequireDefault(require("../icon"));

var _select = _interopRequireDefault(require("../select"));

var _LocaleReceiver = _interopRequireDefault(require("../locale-provider/LocaleReceiver"));

var _configProvider = require("../config-provider");

function _interopRequireDefault(obj) { return obj && obj.__esModule ? obj : { "default": obj }; }

function _getRequireWildcardCache() { if (typeof WeakMap !== "function") return null; var cache = new WeakMap(); _getRequireWildcardCache = function _getRequireWildcardCache() { return cache; }; return cache; }

function _interopRequireWildcard(obj) { if (obj && obj.__esModule) { return obj; } if (obj === null || _typeof(obj) !== "object" && typeof obj !== "function") { return { "default": obj }; } var cache = _getRequireWildcardCache(); if (cache && cache.has(obj)) { return cache.get(obj); } var newObj = {}; var hasPropertyDescriptor = Object.defineProperty && Object.getOwnPropertyDescriptor; for (var key in obj) { if (Object.prototype.hasOwnProperty.call(obj, key)) { var desc = hasPropertyDescriptor ? Object.getOwnPropertyDescriptor(obj, key) : null; if (desc && (desc.get || desc.set)) { Object.defineProperty(newObj, key, desc); } else { newObj[key] = obj[key]; } } } newObj["default"] = obj; if (cache) { cache.set(obj, newObj); } return newObj; }

function _typeof(obj) { "@babel/helpers - typeof"; if (typeof Symbol === "function" && typeof Symbol.iterator === "symbol") { _typeof = function _typeof(obj) { return typeof obj; }; } else { _typeof = function _typeof(obj) { return obj && typeof Symbol === "function" && obj.constructor === Symbol && obj !== Symbol.prototype ? "symbol" : typeof obj; }; } return _typeof(obj); }

function _extends() { _extends = Object.assign || function (target) { for (var i = 1; i < arguments.length; i++) { var source = arguments[i]; for (var key in source) { if (Object.prototype.hasOwnProperty.call(source, key)) { target[key] = source[key]; } } } return target; }; return _extends.apply(this, arguments); }

function _classCallCheck(instance, Constructor) { if (!(instance instanceof Constructor)) { throw new TypeError("Cannot call a class as a function"); } }

function _defineProperties(target, props) { for (var i = 0; i < props.length; i++) { var descriptor = props[i]; descriptor.enumerable = descriptor.enumerable || false; descriptor.configurable = true; if ("value" in descriptor) descriptor.writable = true; Object.defineProperty(target, descriptor.key, descriptor); } }

function _createClass(Constructor, protoProps, staticProps) { if (protoProps) _defineProperties(Constructor.prototype, protoProps); if (staticProps) _defineProperties(Constructor, staticProps); return Constructor; }

function _inherits(subClass, superClass) { if (typeof superClass !== "function" && superClass !== null) { throw new TypeError("Super expression must either be null or a function"); } subClass.prototype = Object.create(superClass && superClass.prototype, { constructor: { value: subClass, writable: true, configurable: true } }); if (superClass) _setPrototypeOf(subClass, superClass); }

function _setPrototypeOf(o, p) { _setPrototypeOf = Object.setPrototypeOf || function _setPrototypeOf(o, p) { o.__proto__ = p; return o; }; return _setPrototypeOf(o, p); }

function _createSuper(Derived) { var hasNativeReflectConstruct = _isNativeReflectConstruct(); return function _createSuperInternal() { var Super = _getPrototypeOf(Derived), result; if (hasNativeReflectConstruct) { var NewTarget = _getPrototypeOf(this).constructor; result = Reflect.construct(Super, arguments, NewTarget); } else { result = Super.apply(this, arguments); } return _possibleConstructorReturn(this, result); }; }

function _possibleConstructorReturn(self, call) { if (call && (_typeof(call) === "object" || typeof call === "function")) { return call; } return _assertThisInitialized(self); }

function _assertThisInitialized(self) { if (self === void 0) { throw new ReferenceError("this hasn't been initialised - super() hasn't been called"); } return self; }

function _isNativeReflectConstruct() { if (typeof Reflect === "undefined" || !Reflect.construct) return false; if (Reflect.construct.sham) return false; if (typeof Proxy === "function") return true; try { Date.prototype.toString.call(Reflect.construct(Date, [], function () {})); return true; } catch (e) { return false; } }

function _getPrototypeOf(o) { _getPrototypeOf = Object.setPrototypeOf ? Object.getPrototypeOf : function _getPrototypeOf(o) { return o.__proto__ || Object.getPrototypeOf(o); }; return _getPrototypeOf(o); }

var __rest = void 0 && (void 0).__rest || function (s, e) {
  var t = {};

  for (var p in s) {
    if (Object.prototype.hasOwnProperty.call(s, p) && e.indexOf(p) < 0) t[p] = s[p];
  }

  if (s != null && typeof Object.getOwnPropertySymbols === "function") for (var i = 0, p = Object.getOwnPropertySymbols(s); i < p.length; i++) {
    if (e.indexOf(p[i]) < 0 && Object.prototype.propertyIsEnumerable.call(s, p[i])) t[p[i]] = s[p[i]];
  }
  return t;
};

var Pagination = /*#__PURE__*/function (_React$Component) {
  _inherits(Pagination, _React$Component);

  var _super = _createSuper(Pagination);

  function Pagination() {
    var _this;

    _classCallCheck(this, Pagination);

    _this = _super.apply(this, arguments);

    _this.getIconsProps = function (prefixCls) {
      var prevIcon = /*#__PURE__*/React.createElement("a", {
        className: "".concat(prefixCls, "-item-link")
      }, /*#__PURE__*/React.createElement(_icon["default"], {
        type: "left"
      }));
      var nextIcon = /*#__PURE__*/React.createElement("a", {
        className: "".concat(prefixCls, "-item-link")
      }, /*#__PURE__*/React.createElement(_icon["default"], {
        type: "right"
      }));
      var jumpPrevIcon = /*#__PURE__*/React.createElement("a", {
        className: "".concat(prefixCls, "-item-link")
      }, /*#__PURE__*/React.createElement("div", {
        className: "".concat(prefixCls, "-item-container")
      }, /*#__PURE__*/React.createElement(_icon["default"], {
        className: "".concat(prefixCls, "-item-link-icon"),
        type: "double-left"
      }), /*#__PURE__*/React.createElement("span", {
        className: "".concat(prefixCls, "-item-ellipsis")
      }, "\u2022\u2022\u2022")));
      var jumpNextIcon = /*#__PURE__*/React.createElement("a", {
        className: "".concat(prefixCls, "-item-link")
      }, /*#__PURE__*/React.createElement("div", {
        className: "".concat(prefixCls, "-item-container")
      }, /*#__PURE__*/React.createElement(_icon["default"], {
        className: "".concat(prefixCls, "-item-link-icon"),
        type: "double-right"
      }), /*#__PURE__*/React.createElement("span", {
        className: "".concat(prefixCls, "-item-ellipsis")
      }, "\u2022\u2022\u2022")));
      return {
        prevIcon: prevIcon,
        nextIcon: nextIcon,
        jumpPrevIcon: jumpPrevIcon,
        jumpNextIcon: jumpNextIcon
      };
    };

    _this.renderPagination = function (contextLocale) {
      var _a = _this.props,
          customizePrefixCls = _a.prefixCls,
          customizeSelectPrefixCls = _a.selectPrefixCls,
          className = _a.className,
          size = _a.size,
          customLocale = _a.locale,
          restProps = __rest(_a, ["prefixCls", "selectPrefixCls", "className", "size", "locale"]);

      var locale = _extends(_extends({}, contextLocale), customLocale);

      var isSmall = size === 'small';
      return /*#__PURE__*/React.createElement(_configProvider.ConfigConsumer, null, function (_ref) {
        var getPrefixCls = _ref.getPrefixCls;
        var prefixCls = getPrefixCls('pagination', customizePrefixCls);
        var selectPrefixCls = getPrefixCls('select', customizeSelectPrefixCls);
        return /*#__PURE__*/React.createElement(_rcPagination["default"], _extends({}, restProps, {
          prefixCls: prefixCls,
          selectPrefixCls: selectPrefixCls
        }, _this.getIconsProps(prefixCls), {
          className: (0, _classnames["default"])(className, {
            mini: isSmall
          }),
          selectComponentClass: isSmall ? _MiniSelect["default"] : _select["default"],
          locale: locale
        }));
      });
    };

    return _this;
  }

  _createClass(Pagination, [{
    key: "render",
    value: function render() {
      return /*#__PURE__*/React.createElement(_LocaleReceiver["default"], {
        componentName: "Pagination",
        defaultLocale: _en_US["default"]
      }, this.renderPagination);
    }
  }]);

  return Pagination;
}(React.Component);

exports["default"] = Pagination;