import { MAIN_LAYOUT, TIME_FORMAT } from './constants';
declare type throttleFunction<T> = (arg: T) => void;
export declare const getMainLayoutClassName: (layout: MAIN_LAYOUT) => string;
export declare const getPosX: (event: TouchEvent | MouseEvent) => number;
export declare const getDisplayTimeBySeconds: (seconds: number, totalSeconds: number, timeFormat: TIME_FORMAT) => string;
export declare function throttle<K>(func: throttleFunction<K>, limit: number): throttleFunction<K>;
export {};
