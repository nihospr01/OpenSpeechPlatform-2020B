function _typeof(obj) { "@babel/helpers - typeof"; if (typeof Symbol === "function" && typeof Symbol.iterator === "symbol") { _typeof = function _typeof(obj) { return typeof obj; }; } else { _typeof = function _typeof(obj) { return obj && typeof Symbol === "function" && obj.constructor === Symbol && obj !== Symbol.prototype ? "symbol" : typeof obj; }; } return _typeof(obj); }

function _extends() { _extends = Object.assign || function (target) { for (var i = 1; i < arguments.length; i++) { var source = arguments[i]; for (var key in source) { if (Object.prototype.hasOwnProperty.call(source, key)) { target[key] = source[key]; } } } return target; }; return _extends.apply(this, arguments); }

function _defineProperty(obj, key, value) { if (key in obj) { Object.defineProperty(obj, key, { value: value, enumerable: true, configurable: true, writable: true }); } else { obj[key] = value; } return obj; }

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

import * as React from 'react';
import { polyfill } from 'react-lifecycles-compat';
import ResizeObserver from 'rc-resize-observer';
import omit from 'omit.js';
import classNames from 'classnames';
import calculateNodeHeight from './calculateNodeHeight';
import raf from '../_util/raf';
import warning from '../_util/warning';

var ResizableTextArea = /*#__PURE__*/function (_React$Component) {
  _inherits(ResizableTextArea, _React$Component);

  var _super = _createSuper(ResizableTextArea);

  function ResizableTextArea(props) {
    var _this;

    _classCallCheck(this, ResizableTextArea);

    _this = _super.call(this, props);

    _this.saveTextArea = function (textArea) {
      _this.textArea = textArea;
    };

    _this.resizeOnNextFrame = function () {
      raf.cancel(_this.nextFrameActionId);
      _this.nextFrameActionId = raf(_this.resizeTextarea);
    };

    _this.resizeTextarea = function () {
      var autoSize = _this.props.autoSize || _this.props.autosize;

      if (!autoSize || !_this.textArea) {
        return;
      }

      var minRows = autoSize.minRows,
          maxRows = autoSize.maxRows;
      var textareaStyles = calculateNodeHeight(_this.textArea, false, minRows, maxRows);

      _this.setState({
        textareaStyles: textareaStyles,
        resizing: true
      }, function () {
        raf.cancel(_this.resizeFrameId);
        _this.resizeFrameId = raf(function () {
          _this.setState({
            resizing: false
          });

          _this.fixFirefoxAutoScroll();
        });
      });
    };

    _this.renderTextArea = function () {
      var _this$props = _this.props,
          prefixCls = _this$props.prefixCls,
          autoSize = _this$props.autoSize,
          autosize = _this$props.autosize,
          className = _this$props.className,
          disabled = _this$props.disabled;
      var _this$state = _this.state,
          textareaStyles = _this$state.textareaStyles,
          resizing = _this$state.resizing;
      warning(autosize === undefined, 'Input.TextArea', 'autosize is deprecated, please use autoSize instead.');
      var otherProps = omit(_this.props, ['prefixCls', 'onPressEnter', 'autoSize', 'autosize', 'defaultValue', 'allowClear']);
      var cls = classNames(prefixCls, className, _defineProperty({}, "".concat(prefixCls, "-disabled"), disabled)); // Fix https://github.com/ant-design/ant-design/issues/6776
      // Make sure it could be reset when using form.getFieldDecorator

      if ('value' in otherProps) {
        otherProps.value = otherProps.value || '';
      }

      var style = _extends(_extends(_extends({}, _this.props.style), textareaStyles), resizing ? {
        overflowX: 'hidden',
        overflowY: 'hidden'
      } : null);

      return /*#__PURE__*/React.createElement(ResizeObserver, {
        onResize: _this.resizeOnNextFrame,
        disabled: !(autoSize || autosize)
      }, /*#__PURE__*/React.createElement("textarea", _extends({}, otherProps, {
        className: cls,
        style: style,
        ref: _this.saveTextArea
      })));
    };

    _this.state = {
      textareaStyles: {},
      resizing: false
    };
    return _this;
  }

  _createClass(ResizableTextArea, [{
    key: "componentDidMount",
    value: function componentDidMount() {
      this.resizeTextarea();
    }
  }, {
    key: "componentDidUpdate",
    value: function componentDidUpdate(prevProps) {
      // Re-render with the new content then recalculate the height as required.
      if (prevProps.value !== this.props.value) {
        this.resizeTextarea();
      }
    }
  }, {
    key: "componentWillUnmount",
    value: function componentWillUnmount() {
      raf.cancel(this.nextFrameActionId);
      raf.cancel(this.resizeFrameId);
    } // https://github.com/ant-design/ant-design/issues/21870

  }, {
    key: "fixFirefoxAutoScroll",
    value: function fixFirefoxAutoScroll() {
      try {
        if (document.activeElement === this.textArea) {
          var currentStart = this.textArea.selectionStart;
          var currentEnd = this.textArea.selectionEnd;
          this.textArea.setSelectionRange(currentStart, currentEnd);
        }
      } catch (e) {// Fix error in Chrome:
        // Failed to read the 'selectionStart' property from 'HTMLInputElement'
        // http://stackoverflow.com/q/21177489/3040605
      }
    }
  }, {
    key: "render",
    value: function render() {
      return this.renderTextArea();
    }
  }]);

  return ResizableTextArea;
}(React.Component);

polyfill(ResizableTextArea);
export default ResizableTextArea;