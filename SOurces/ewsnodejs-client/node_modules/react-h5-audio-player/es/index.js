import _assertThisInitialized from "@babel/runtime/helpers/assertThisInitialized";
import _inheritsLoose from "@babel/runtime/helpers/inheritsLoose";
import _defineProperty from "@babel/runtime/helpers/defineProperty";
import React, { Component, cloneElement, isValidElement, createRef } from 'react';
import { Icon } from '@iconify/react';
import playCircle from '@iconify/icons-mdi/play-circle';
import pauseCircle from '@iconify/icons-mdi/pause-circle';
import skipPrevious from '@iconify/icons-mdi/skip-previous';
import skipNext from '@iconify/icons-mdi/skip-next';
import fastForward from '@iconify/icons-mdi/fast-forward';
import rewind from '@iconify/icons-mdi/rewind';
import volumeHigh from '@iconify/icons-mdi/volume-high';
import volumeMute from '@iconify/icons-mdi/volume-mute';
import repeat from '@iconify/icons-mdi/repeat';
import repeatOff from '@iconify/icons-mdi/repeat-off';
import ProgressBar from './ProgressBar';
import CurrentTime from './CurrentTime';
import Duration from './Duration';
import VolumeBar from './VolumeBar';
import { RHAP_UI } from './constants';
import { throttle, getMainLayoutClassName, getDisplayTimeBySeconds } from './utils';

var H5AudioPlayer = function (_Component) {
  _inheritsLoose(H5AudioPlayer, _Component);

  function H5AudioPlayer() {
    var _this;

    for (var _len = arguments.length, args = new Array(_len), _key = 0; _key < _len; _key++) {
      args[_key] = arguments[_key];
    }

    _this = _Component.call.apply(_Component, [this].concat(args)) || this;

    _defineProperty(_assertThisInitialized(_this), "audio", createRef());

    _defineProperty(_assertThisInitialized(_this), "progressBar", createRef());

    _defineProperty(_assertThisInitialized(_this), "container", createRef());

    _defineProperty(_assertThisInitialized(_this), "lastVolume", _this.props.volume);

    _defineProperty(_assertThisInitialized(_this), "listenTracker", void 0);

    _defineProperty(_assertThisInitialized(_this), "volumeAnimationTimer", void 0);

    _defineProperty(_assertThisInitialized(_this), "downloadProgressAnimationTimer", void 0);

    _defineProperty(_assertThisInitialized(_this), "togglePlay", function (e) {
      e.stopPropagation();
      var audio = _this.audio.current;

      if (audio.paused && audio.src) {
        _this.playAudioPromise();
      } else if (!audio.paused) {
        audio.pause();
      }
    });

    _defineProperty(_assertThisInitialized(_this), "playAudioPromise", function () {
      _this.audio.current.play().then(null).catch(function (err) {
        var onPlayError = _this.props.onPlayError;
        onPlayError && onPlayError(new Error(err));
      });
    });

    _defineProperty(_assertThisInitialized(_this), "isPlaying", function () {
      var audio = _this.audio.current;
      if (!audio) return false;
      return !audio.paused && !audio.ended;
    });

    _defineProperty(_assertThisInitialized(_this), "handlePlay", function (e) {
      _this.forceUpdate();

      _this.props.onPlay && _this.props.onPlay(e);
    });

    _defineProperty(_assertThisInitialized(_this), "handlePause", function (e) {
      if (!_this.audio) return;

      _this.forceUpdate();

      _this.props.onPause && _this.props.onPause(e);
    });

    _defineProperty(_assertThisInitialized(_this), "handleAbort", function (e) {
      _this.props.onAbort && _this.props.onAbort(e);
    });

    _defineProperty(_assertThisInitialized(_this), "handleClickVolumeButton", function () {
      var audio = _this.audio.current;

      if (audio.volume > 0) {
        _this.lastVolume = audio.volume;
        audio.volume = 0;
      } else {
        audio.volume = _this.lastVolume;
      }
    });

    _defineProperty(_assertThisInitialized(_this), "handleMuteChange", function () {
      _this.forceUpdate();
    });

    _defineProperty(_assertThisInitialized(_this), "handleClickLoopButton", function () {
      _this.audio.current.loop = !_this.audio.current.loop;

      _this.forceUpdate();
    });

    _defineProperty(_assertThisInitialized(_this), "handleClickRewind", function () {
      var _this$props = _this.props,
          progressJumpSteps = _this$props.progressJumpSteps,
          progressJumpStep = _this$props.progressJumpStep;
      var jumpStep = progressJumpSteps.backward || progressJumpStep;

      _this.setJumpTime(-jumpStep);
    });

    _defineProperty(_assertThisInitialized(_this), "handleClickForward", function () {
      var _this$props2 = _this.props,
          progressJumpSteps = _this$props2.progressJumpSteps,
          progressJumpStep = _this$props2.progressJumpStep;
      var jumpStep = progressJumpSteps.forward || progressJumpStep;

      _this.setJumpTime(jumpStep);
    });

    _defineProperty(_assertThisInitialized(_this), "setJumpTime", function (time) {
      var audio = _this.audio.current;
      var duration = audio.duration,
          prevTime = audio.currentTime;
      if (!isFinite(duration) || !isFinite(prevTime)) return;
      var currentTime = prevTime + time / 1000;

      if (currentTime < 0) {
        audio.currentTime = 0;
        currentTime = 0;
      } else if (currentTime > duration) {
        audio.currentTime = duration;
        currentTime = duration;
      } else {
        audio.currentTime = currentTime;
      }
    });

    _defineProperty(_assertThisInitialized(_this), "setJumpVolume", function (volume) {
      var newVolume = _this.audio.current.volume + volume;
      if (newVolume < 0) newVolume = 0;else if (newVolume > 1) newVolume = 1;
      _this.audio.current.volume = newVolume;
    });

    _defineProperty(_assertThisInitialized(_this), "handleKeyDown", function (e) {
      switch (e.keyCode) {
        case 32:
          if (e.target === _this.container.current || e.target === _this.progressBar.current) {
            e.preventDefault();

            _this.togglePlay(e);
          }

          break;

        case 37:
          _this.handleClickRewind();

          break;

        case 39:
          _this.handleClickForward();

          break;

        case 38:
          e.preventDefault();

          _this.setJumpVolume(_this.props.volumeJumpStep);

          break;

        case 40:
          e.preventDefault();

          _this.setJumpVolume(-_this.props.volumeJumpStep);

          break;

        case 76:
          _this.handleClickLoopButton();

          break;

        case 77:
          _this.handleClickVolumeButton();

          break;
      }
    });

    _defineProperty(_assertThisInitialized(_this), "renderUIModules", function (modules) {
      return modules.map(function (comp, i) {
        return _this.renderUIModule(comp, i);
      });
    });

    _defineProperty(_assertThisInitialized(_this), "renderUIModule", function (comp, key) {
      var _this$props3 = _this.props,
          defaultCurrentTime = _this$props3.defaultCurrentTime,
          progressUpdateInterval = _this$props3.progressUpdateInterval,
          showDownloadProgress = _this$props3.showDownloadProgress,
          showFilledProgress = _this$props3.showFilledProgress,
          showFilledVolume = _this$props3.showFilledVolume,
          defaultDuration = _this$props3.defaultDuration,
          customIcons = _this$props3.customIcons,
          showSkipControls = _this$props3.showSkipControls,
          onClickPrevious = _this$props3.onClickPrevious,
          onClickNext = _this$props3.onClickNext,
          showJumpControls = _this$props3.showJumpControls,
          customAdditionalControls = _this$props3.customAdditionalControls,
          customVolumeControls = _this$props3.customVolumeControls,
          muted = _this$props3.muted,
          timeFormat = _this$props3.timeFormat,
          volumeProp = _this$props3.volume,
          loopProp = _this$props3.loop,
          mse = _this$props3.mse;

      switch (comp) {
        case RHAP_UI.CURRENT_TIME:
          return React.createElement("div", {
            key: key,
            id: "rhap_current-time",
            className: "rhap_time rhap_current-time"
          }, React.createElement(CurrentTime, {
            audio: _this.audio.current,
            isLeftTime: false,
            defaultCurrentTime: defaultCurrentTime,
            timeFormat: timeFormat
          }));

        case RHAP_UI.CURRENT_LEFT_TIME:
          return React.createElement("div", {
            key: key,
            id: "rhap_current-left-time",
            className: "rhap_time rhap_current-left-time"
          }, React.createElement(CurrentTime, {
            audio: _this.audio.current,
            isLeftTime: true,
            defaultCurrentTime: defaultCurrentTime,
            timeFormat: timeFormat
          }));

        case RHAP_UI.PROGRESS_BAR:
          return React.createElement(ProgressBar, {
            key: key,
            ref: _this.progressBar,
            audio: _this.audio.current,
            progressUpdateInterval: progressUpdateInterval,
            showDownloadProgress: showDownloadProgress,
            showFilledProgress: showFilledProgress,
            onSeek: mse && mse.onSeek,
            srcDuration: mse && mse.srcDuration
          });

        case RHAP_UI.DURATION:
          return React.createElement("div", {
            key: key,
            className: "rhap_time rhap_total-time"
          }, mse && mse.srcDuration ? getDisplayTimeBySeconds(mse.srcDuration, mse.srcDuration, _this.props.timeFormat) : React.createElement(Duration, {
            audio: _this.audio.current,
            defaultDuration: defaultDuration,
            timeFormat: timeFormat
          }));

        case RHAP_UI.ADDITIONAL_CONTROLS:
          return React.createElement("div", {
            key: key,
            className: "rhap_additional-controls"
          }, _this.renderUIModules(customAdditionalControls));

        case RHAP_UI.MAIN_CONTROLS:
          {
            var isPlaying = _this.isPlaying();

            var actionIcon;

            if (isPlaying) {
              actionIcon = customIcons.pause ? customIcons.pause : React.createElement(Icon, {
                icon: pauseCircle
              });
            } else {
              actionIcon = customIcons.play ? customIcons.play : React.createElement(Icon, {
                icon: playCircle
              });
            }

            return React.createElement("div", {
              key: key,
              className: "rhap_main-controls"
            }, showSkipControls && React.createElement("button", {
              "aria-label": "Previous",
              className: "rhap_button-clear rhap_main-controls-button rhap_skip-button",
              type: "button",
              onClick: onClickPrevious
            }, customIcons.previous ? customIcons.previous : React.createElement(Icon, {
              icon: skipPrevious
            })), showJumpControls && React.createElement("button", {
              "aria-label": "Rewind",
              className: "rhap_button-clear rhap_main-controls-button rhap_rewind-button",
              type: "button",
              onClick: _this.handleClickRewind
            }, customIcons.rewind ? customIcons.rewind : React.createElement(Icon, {
              icon: rewind
            })), React.createElement("button", {
              "aria-label": isPlaying ? 'Pause' : 'Play',
              className: "rhap_button-clear rhap_main-controls-button rhap_play-pause-button",
              type: "button",
              onClick: _this.togglePlay
            }, actionIcon), showJumpControls && React.createElement("button", {
              "aria-label": "Forward",
              className: "rhap_button-clear rhap_main-controls-button rhap_forward-button",
              type: "button",
              onClick: _this.handleClickForward
            }, customIcons.forward ? customIcons.forward : React.createElement(Icon, {
              icon: fastForward
            })), showSkipControls && React.createElement("button", {
              "aria-label": "Skip",
              className: "rhap_button-clear rhap_main-controls-button rhap_skip-button",
              type: "button",
              onClick: onClickNext
            }, customIcons.next ? customIcons.next : React.createElement(Icon, {
              icon: skipNext
            })));
          }

        case RHAP_UI.VOLUME_CONTROLS:
          return React.createElement("div", {
            key: key,
            className: "rhap_volume-controls"
          }, _this.renderUIModules(customVolumeControls));

        case RHAP_UI.LOOP:
          {
            var loop = _this.audio.current ? _this.audio.current.loop : loopProp;
            var loopIcon;

            if (loop) {
              loopIcon = customIcons.loop ? customIcons.loop : React.createElement(Icon, {
                icon: repeat
              });
            } else {
              loopIcon = customIcons.loopOff ? customIcons.loopOff : React.createElement(Icon, {
                icon: repeatOff
              });
            }

            return React.createElement("button", {
              key: key,
              "aria-label": loop ? 'Enable Loop' : 'Disable Loop',
              className: "rhap_button-clear rhap_repeat-button",
              type: "button",
              onClick: _this.handleClickLoopButton
            }, loopIcon);
          }

        case RHAP_UI.VOLUME:
          {
            var _ref = _this.audio.current || {},
                _ref$volume = _ref.volume,
                volume = _ref$volume === void 0 ? muted ? 0 : volumeProp : _ref$volume;

            var volumeIcon;

            if (volume) {
              volumeIcon = customIcons.volume ? customIcons.volume : React.createElement(Icon, {
                icon: volumeHigh
              });
            } else {
              volumeIcon = customIcons.volume ? customIcons.volumeMute : React.createElement(Icon, {
                icon: volumeMute
              });
            }

            return React.createElement("div", {
              key: key,
              className: "rhap_volume-container"
            }, React.createElement("button", {
              "aria-label": volume ? 'Mute' : 'Unmute',
              onClick: _this.handleClickVolumeButton,
              type: "button",
              className: "rhap_button-clear rhap_volume-button"
            }, volumeIcon), React.createElement(VolumeBar, {
              audio: _this.audio.current,
              volume: volume,
              onMuteChange: _this.handleMuteChange,
              showFilledVolume: showFilledVolume
            }));
          }

        default:
          if (!isValidElement(comp)) {
            return null;
          }

          return comp.key ? comp : cloneElement(comp, {
            key: key
          });
      }
    });

    return _this;
  }

  var _proto = H5AudioPlayer.prototype;

  _proto.componentDidMount = function componentDidMount() {
    var _this2 = this;

    this.forceUpdate();
    var audio = this.audio.current;

    if (this.props.muted) {
      audio.volume = 0;
    } else {
      audio.volume = this.lastVolume;
    }

    audio.addEventListener('error', function (e) {
      _this2.props.onError && _this2.props.onError(e);
    });
    audio.addEventListener('canplay', function (e) {
      _this2.props.onCanPlay && _this2.props.onCanPlay(e);
    });
    audio.addEventListener('canplaythrough', function (e) {
      _this2.props.onCanPlayThrough && _this2.props.onCanPlayThrough(e);
    });
    audio.addEventListener('play', this.handlePlay);
    audio.addEventListener('abort', this.handleAbort);
    audio.addEventListener('ended', function (e) {
      _this2.props.onEnded && _this2.props.onEnded(e);
    });
    audio.addEventListener('pause', this.handlePause);
    audio.addEventListener('timeupdate', throttle(function (e) {
      _this2.props.onListen && _this2.props.onListen(e);
    }, this.props.listenInterval));
    audio.addEventListener('volumechange', function (e) {
      _this2.props.onVolumeChange && _this2.props.onVolumeChange(e);
    });
    audio.addEventListener('encrypted', function (e) {
      var mse = _this2.props.mse;
      mse && mse.onEcrypted && mse.onEcrypted(e);
    });
  };

  _proto.componentDidUpdate = function componentDidUpdate(prevProps) {
    var _this$props4 = this.props,
        src = _this$props4.src,
        autoPlayAfterSrcChange = _this$props4.autoPlayAfterSrcChange;

    if (prevProps.src !== src) {
      if (autoPlayAfterSrcChange) {
        this.playAudioPromise();
      } else {
        this.forceUpdate();
      }
    }
  };

  _proto.componentWillUnmount = function componentWillUnmount() {
    var audio = this.audio.current;

    if (audio) {
      audio.removeEventListener('play', this.handlePlay);
      audio.removeEventListener('pause', this.handlePause);
      audio.removeEventListener('abort', this.handleAbort);
      audio.removeAttribute('src');
      audio.load();
    }
  };

  _proto.render = function render() {
    var _this$props5 = this.props,
        className = _this$props5.className,
        src = _this$props5.src,
        loopProp = _this$props5.loop,
        preload = _this$props5.preload,
        autoPlay = _this$props5.autoPlay,
        crossOrigin = _this$props5.crossOrigin,
        mediaGroup = _this$props5.mediaGroup,
        header = _this$props5.header,
        footer = _this$props5.footer,
        layout = _this$props5.layout,
        customProgressBarSection = _this$props5.customProgressBarSection,
        customControlsSection = _this$props5.customControlsSection,
        children = _this$props5.children,
        style = _this$props5.style;
    var loop = this.audio.current ? this.audio.current.loop : loopProp;
    return React.createElement("div", {
      role: "group",
      tabIndex: 0,
      "aria-label": "Audio Player",
      className: "rhap_container " + className,
      onKeyDown: this.handleKeyDown,
      ref: this.container,
      style: style
    }, React.createElement("audio", {
      src: src,
      controls: false,
      loop: loop,
      autoPlay: autoPlay,
      preload: preload,
      crossOrigin: crossOrigin,
      mediaGroup: mediaGroup,
      ref: this.audio
    }, children), header && React.createElement("div", {
      className: "rhap_header"
    }, header), React.createElement("div", {
      className: "rhap_main " + getMainLayoutClassName(layout)
    }, React.createElement("div", {
      className: "rhap_progress-section"
    }, this.renderUIModules(customProgressBarSection)), React.createElement("div", {
      className: "rhap_controls-section"
    }, this.renderUIModules(customControlsSection))), footer && React.createElement("div", {
      className: "rhap_footer"
    }, footer));
  };

  return H5AudioPlayer;
}(Component);

_defineProperty(H5AudioPlayer, "defaultProps", {
  autoPlay: false,
  autoPlayAfterSrcChange: true,
  listenInterval: 1000,
  progressJumpStep: 5000,
  progressJumpSteps: {},
  volumeJumpStep: 0.1,
  loop: false,
  muted: false,
  preload: 'auto',
  progressUpdateInterval: 20,
  defaultCurrentTime: '--:--',
  defaultDuration: '--:--',
  timeFormat: 'auto',
  volume: 1,
  className: '',
  showJumpControls: true,
  showSkipControls: false,
  showDownloadProgress: true,
  showFilledProgress: true,
  showFilledVolume: false,
  customIcons: {},
  customProgressBarSection: [RHAP_UI.CURRENT_TIME, RHAP_UI.PROGRESS_BAR, RHAP_UI.DURATION],
  customControlsSection: [RHAP_UI.ADDITIONAL_CONTROLS, RHAP_UI.MAIN_CONTROLS, RHAP_UI.VOLUME_CONTROLS],
  customAdditionalControls: [RHAP_UI.LOOP],
  customVolumeControls: [RHAP_UI.VOLUME],
  layout: 'stacked'
});

export default H5AudioPlayer;
export { RHAP_UI };