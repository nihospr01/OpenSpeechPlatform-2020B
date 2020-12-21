export var getMainLayoutClassName = function getMainLayoutClassName(layout) {
  switch (layout) {
    case 'stacked':
      return 'rhap_stacked';

    case 'stacked-reverse':
      return 'rhap_stacked-reverse';

    case 'horizontal':
      return 'rhap_horizontal';

    case 'horizontal-reverse':
      return 'rhap_horizontal-reverse';

    default:
      return 'rhap_stacked';
  }
};
export var getPosX = function getPosX(event) {
  if (event instanceof MouseEvent) {
    return event.clientX;
  } else {
    return event.touches[0].clientX;
  }
};

var addHeadingZero = function addHeadingZero(num) {
  return num > 9 ? num.toString() : "0" + num;
};

export var getDisplayTimeBySeconds = function getDisplayTimeBySeconds(seconds, totalSeconds, timeFormat) {
  if (!isFinite(seconds)) {
    return null;
  }

  var min = Math.floor(seconds / 60);
  var minStr = addHeadingZero(min);
  var secStr = addHeadingZero(Math.floor(seconds % 60));
  var minStrForHour = addHeadingZero(Math.floor(min % 60));
  var hourStr = Math.floor(min / 60);
  var mmSs = minStr + ":" + secStr;
  var hhMmSs = hourStr + ":" + minStrForHour + ":" + secStr;

  if (timeFormat === 'auto') {
    if (totalSeconds >= 3600) {
      return hhMmSs;
    } else {
      return mmSs;
    }
  } else if (timeFormat === 'mm:ss') {
    return mmSs;
  } else if (timeFormat === 'hh:mm:ss') {
    return hhMmSs;
  }
};
export function throttle(func, limit) {
  var inThrottle = false;
  return function (arg) {
    if (!inThrottle) {
      func(arg);
      inThrottle = true;
      setTimeout(function () {
        return inThrottle = false;
      }, limit);
    }
  };
}