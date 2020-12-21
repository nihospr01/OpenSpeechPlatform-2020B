function _defineProperty(obj, key, value) { if (key in obj) { Object.defineProperty(obj, key, { value: value, enumerable: true, configurable: true, writable: true }); } else { obj[key] = value; } return obj; }

import * as React from 'react';
import classNames from 'classnames';
import Icon from '../icon';
export default function InputIcon(props) {
  var _classNames;

  var suffixIcon = props.suffixIcon,
      prefixCls = props.prefixCls;
  return suffixIcon && ( /*#__PURE__*/React.isValidElement(suffixIcon) ? /*#__PURE__*/React.cloneElement(suffixIcon, {
    className: classNames((_classNames = {}, _defineProperty(_classNames, suffixIcon.props.className, suffixIcon.props.className), _defineProperty(_classNames, "".concat(prefixCls, "-picker-icon"), true), _classNames))
  }) : /*#__PURE__*/React.createElement("span", {
    className: "".concat(prefixCls, "-picker-icon")
  }, suffixIcon)) || /*#__PURE__*/React.createElement(Icon, {
    type: "calendar",
    className: "".concat(prefixCls, "-picker-icon")
  });
}