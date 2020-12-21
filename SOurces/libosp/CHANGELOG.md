# Changelog
All **notable** changes to this project will be documented in this file.
The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),

## [1.1.1] - 2020-12-17

### Fixed
- Multithread restarts without handing
- Restarted threads have correct priority

### Changed
- AFC off by default
- Microphone input on by default (alpha=0)
- Set affinity to even numbered CPU cores if possible (not on PCD)
## [1.1.0] - 2020-12-14

### Added
- osp_cli can list audio file with "play ?"
- "restart" command restarts osp

### Fixed
- Fixes pops when enabling freping
- Switching bands or restarting now works with multithread
### Changed
- switching bands restarts osp

### Removed
- "audio" toggle removed.  Use gain instead.

## [1.0.10] - 2020-12-06

- Increase delay during band switch to avoid race condition..
## [1.0.9] - 2020-12-06

- Fixes 1.0.8 change that broke 10-band switch.
## [1.0.8] - 2020-12-04

- Checks that array sizes match the number of bands.
- Passing a float for a band array sets all bands to that value.

## [1.0.7] - 2020-11-23

- Exits with code -1 when audio stream fails.

## [1.0.6] - 2020-11-06

- Mac compilation fix to find portaudio.h

## [1.0.5] - 2020-11-05

- fix crash with bad audio files

## [1.0.4] - 2020-10-30

- improved signal handling and cleanup

## [1.0.3] - 2020-10-30

- works on Mac now.

## [1.0.2] - 2020-10-29

### Added
- hostname arg for osp_cli

### Changed
- better handling of failed audio streams

## [1.0.1] - 2020-10-21

### Removed
- no longer requires any zmq libs

### Added
- new parameter "audio_playing"

## [1.0.0] - 2020-10-21

### Added
- Rewrite osp and move from osp-clion-cxx
- osp_cli
