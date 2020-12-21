import _assertThisInitialized from "@babel/runtime/helpers/assertThisInitialized";
import _inheritsLoose from "@babel/runtime/helpers/inheritsLoose";
import _defineProperty from "@babel/runtime/helpers/defineProperty";
import React, { Component, createRef } from 'react';
import { getPosX } from './utils';

var VolumeControls = function (_Component) {
  _inheritsLoose(VolumeControls, _Component);

  function VolumeControls() {
    var _this;

    for (var _len = arguments.length, args = new Array(_len), _key = 0; _key < _len; _key++) {
      args[_key] = arguments[_key];
    }

    _this = _Component.call.apply(_Component, [this].concat(args)) || this;

    _defineProperty(_assertThisInitialized(_this), "audio", void 0);

    _defineProperty(_assertThisInitialized(_this), "hasAddedAudioEventListener", false);

    _defineProperty(_assertThisInitialized(_this), "volumeBar", createRef());

    _defineProperty(_assertThisInitialized(_this), "volumeAnimationTimer", 0);

    _defineProperty(_assertThisInitialized(_this), "lastVolume", _this.props.volume);

    _defineProperty(_assertThisInitialized(_this), "state", {
      currentVolumePos: (_this.lastVolume / 1 * 100 || 0).toFixed(2) + "%",
      hasVolumeAnimation: false,
      isDraggingVolume: false
    });

    _defineProperty(_assertThisInitialized(_this), "getCurrentVolume", function (event) {
      var audio = _this.props.audio;

      if (!_this.volumeBar.current) {
        return {
          currentVolume: audio.volume,
          currentVolumePos: _this.state.currentVolumePos
        };
      }

      var volumeBarRect = _this.volumeBar.current.getBoundingClientRect();

      var maxRelativePos = volumeBarRect.width;
      var relativePos = getPosX(event) - volumeBarRect.left;
      var currentVolume;
      var currentVolumePos;

      if (relativePos < 0) {
        currentVolume = 0;
        currentVolumePos = '0%';
      } else if (relativePos > volumeBarRect.width) {
        currentVolume = 1;
        currentVolumePos = '100%';
      } else {
        currentVolume = relativePos / maxRelativePos;
        currentVolumePos = relativePos / maxRelativePos * 100 + "%";
      }

      return {
        currentVolume: currentVolume,
        currentVolumePos: currentVolumePos
      };
    });

    _defineProperty(_assertThisInitialized(_this), "handleContextMenu", function (event) {
      event.preventDefault();
    });

    _defineProperty(_assertThisInitialized(_this), "handleClickVolumeButton", function () {
      var audio = _this.props.audio;

      if (audio.volume > 0) {
        _this.lastVolume = audio.volume;
        audio.volume = 0;
      } else {
        audio.volume = _this.lastVolume;
      }
    });

    _defineProperty(_assertThisInitialized(_this), "handleVolumnControlMouseOrTouchDown", function (event) {
      event.stopPropagation();
      var audio = _this.props.audio;

      var _this$getCurrentVolum = _this.getCurrentVolume(event.nativeEvent),
          currentVolume = _this$getCurrentVolum.currentVolume,
          currentVolumePos = _this$getCurrentVolum.currentVolumePos;

      audio.volume = currentVolume;

      _this.setState({
        isDraggingVolume: true,
        currentVolumePos: currentVolumePos
      });

      if (event.nativeEvent instanceof MouseEvent) {
        window.addEventListener('mousemove', _this.handleWindowMouseOrTouchMove);
        window.addEventListener('mouseup', _this.handleWindowMouseOrTouchUp);
      } else {
        window.addEventListener('touchmove', _this.handleWindowMouseOrTouchMove);
        window.addEventListener('touchend', _this.handleWindowMouseOrTouchUp);
      }
    });

    _defineProperty(_assertThisInitialized(_this), "handleWindowMouseOrTouchMove", function (event) {
      event.preventDefault();
      event.stopPropagation();
      var audio = _this.props.audio;
      var windowSelection = window.getSelection();

      if (windowSelection && windowSelection.type === 'Range') {
        windowSelection.empty();
      }

      var isDraggingVolume = _this.state.isDraggingVolume;

      if (isDraggingVolume) {
        var _this$getCurrentVolum2 = _this.getCurrentVolume(event),
            currentVolume = _this$getCurrentVolum2.currentVolume,
            currentVolumePos = _this$getCurrentVolum2.currentVolumePos;

        audio.volume = currentVolume;

        _this.setState({
          currentVolumePos: currentVolumePos
        });
      }
    });

    _defineProperty(_assertThisInitialized(_this), "handleWindowMouseOrTouchUp", function (event) {
      event.stopPropagation();

      _this.setState({
        isDraggingVolume: false
      });

      if (event instanceof MouseEvent) {
        window.removeEventListener('mousemove', _this.handleWindowMouseOrTouchMove);
        window.removeEventListener('mouseup', _this.handleWindowMouseOrTouchUp);
      } else {
        window.removeEventListener('touchmove', _this.handleWindowMouseOrTouchMove);
        window.removeEventListener('touchend', _this.handleWindowMouseOrTouchUp);
      }
    });

    _defineProperty(_assertThisInitialized(_this), "handleAudioVolumeChange", function (e) {
      var isDraggingVolume = _this.state.isDraggingVolume;
      var _ref = e.target,
          volume = _ref.volume;

      if (_this.lastVolume > 0 && volume === 0 || _this.lastVolume === 0 && volume > 0) {
        _this.props.onMuteChange();
      }

      _this.lastVolume = volume;
      if (isDraggingVolume) return;

      _this.setState({
        hasVolumeAnimation: true,
        currentVolumePos: (volume / 1 * 100 || 0).toFixed(2) + "%"
      });

      clearTimeout(_this.volumeAnimationTimer);
      _this.volumeAnimationTimer = setTimeout(function () {
        _this.setState({
          hasVolumeAnimation: false
        });
      }, 100);
    });

    return _this;
  }

  var _proto = VolumeControls.prototype;

  _proto.componentDidUpdate = function componentDidUpdate() {
    var audio = this.props.audio;

    if (audio && !this.hasAddedAudioEventListener) {
      this.audio = audio;
      this.hasAddedAudioEventListener = true;
      audio.addEventListener('volumechange', this.handleAudioVolumeChange);
    }
  };

  _proto.componentWillUnmount = function componentWillUnmount() {
    if (this.audio && this.hasAddedAudioEventListener) {
      this.audio.removeEventListener('volumechange', this.handleAudioVolumeChange);
    }

    clearTimeout(this.volumeAnimationTimer);
  };

  _proto.render = function render() {
    var _this$props = this.props,
        audio = _this$props.audio,
        showFilledVolume = _this$props.showFilledVolume;
    var _this$state = this.state,
        currentVolumePos = _this$state.currentVolumePos,
        hasVolumeAnimation = _this$state.hasVolumeAnimation;

    var _ref2 = audio || {},
        volume = _ref2.volume;

    return React.createElement("div", {
      ref: this.volumeBar,
      onMouseDown: this.handleVolumnControlMouseOrTouchDown,
      onTouchStart: this.handleVolumnControlMouseOrTouchDown,
      onContextMenu: this.handleContextMenu,
      role: "progressbar",
      "aria-label": "volume Control",
      "aria-valuemin": 0,
      "aria-valuemax": 100,
      "aria-valuenow": Number((volume * 100).toFixed(0)),
      tabIndex: 0,
      className: "rhap_volume-bar-area"
    }, React.createElement("div", {
      className: "rhap_volume-bar"
    }, React.createElement("div", {
      className: "rhap_volume-indicator",
      style: {
        left: currentVolumePos,
        transitionDuration: hasVolumeAnimation ? '.1s' : '0s'
      }
    }), showFilledVolume && React.createElement("div", {
      className: "rhap_volume-filled",
      style: {
        width: currentVolumePos
      }
    })));
  };

  return VolumeControls;
}(Component);

export default VolumeControls;