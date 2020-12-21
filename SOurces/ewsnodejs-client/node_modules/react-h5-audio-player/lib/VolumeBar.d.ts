import React, { Component, SyntheticEvent } from 'react';
interface VolumeControlsProps {
    audio: HTMLAudioElement;
    volume: number;
    onMuteChange: () => void;
    showFilledVolume: boolean;
}
interface VolumeControlsState {
    currentVolumePos: string;
    hasVolumeAnimation: boolean;
    isDraggingVolume: boolean;
}
interface VolumePosInfo {
    currentVolume: number;
    currentVolumePos: string;
}
declare class VolumeControls extends Component<VolumeControlsProps, VolumeControlsState> {
    audio?: HTMLAudioElement;
    hasAddedAudioEventListener: boolean;
    volumeBar: React.RefObject<HTMLDivElement>;
    volumeAnimationTimer: number;
    lastVolume: number;
    state: VolumeControlsState;
    getCurrentVolume: (event: TouchEvent | MouseEvent) => VolumePosInfo;
    handleContextMenu: (event: SyntheticEvent) => void;
    handleClickVolumeButton: () => void;
    handleVolumnControlMouseOrTouchDown: (event: React.MouseEvent | React.TouchEvent) => void;
    handleWindowMouseOrTouchMove: (event: TouchEvent | MouseEvent) => void;
    handleWindowMouseOrTouchUp: (event: MouseEvent | TouchEvent) => void;
    handleAudioVolumeChange: (e: Event) => void;
    componentDidUpdate(): void;
    componentWillUnmount(): void;
    render(): React.ReactNode;
}
export default VolumeControls;
