# Open Speech Platform


## Prerequisites

All platforms need python3 with the ansicolors package installed.

```
pip3 install ansicolors
```

### Mac

- xcode

Install xcode from from "Software Update".

- brew

```
ruby -e "$(curl -fsSL https://raw.githubusercontent.com/Homebrew/install/master/install)"
```

- more packages from brew

```
brew install coreutils cmake portaudio pkg-config composer php@7.4 node googletest
```


### Linux (Debian)

```
sudo apt install alsa-base alsa-utils libasound2-dev portaudio19-dev \
    cmake sqlite sqlite3 php php-mbstring libgtest-dev \
    php-xml composer zip unzip php-sqlite3 nodejs yarn

sudo apt install libnode-dev node-gyp libssl-dev npm libgtest-dev googletest
```

### Linux (Red Hat)

```
sudo yum install alsa-base alsa-utils libasound2-dev portaudio19-dev \
    cmake sqlite sqlite3 php php-mbstring \
    php-xml composer zip unzip php-sqlite3 libnode-dev node-gyp\
    libssl-dev npm git libgtest-dev
```

## Building Everything

`scripts/install` will build everything and install it in a directory called "release". 

Note that OSP_MEDIA is set to the `release/media` directory which is the contents
of `embeddedwebserver/public/audio` and `ewsnodejs-server/src/utils/audio`.  The osp
process will look here for audio files it is requested to play.  Filenames should be
relative to that location.

```
/opt/osp ❯❯❯ ls -l release
drwxrwxr-x  2 mmh mmh 4096 Nov  4 16:31 bin
drwxrwxr-x 11 mmh mmh 4096 Nov  4 16:31 embeddedwebserver
drwxrwxr-x 13 mmh mmh 4096 Nov  4 16:31 ewsnodejs-server
drwxrwxr-x  3 mmh mmh 4096 Nov  4 16:30 include
drwxrwxr-x  3 mmh mmh 4096 Nov  4 16:30 lib
drwxrwxr-x  9 mmh mmh 4096 Nov  4 16:31 media
drwxrwxr-x  3 mmh mmh 4096 Nov  4 16:30 share

/opt/osp ❯❯❯ ls -l release/bin
-rwxr-xr-x 1 mmh mmh 446392 Nov  4 16:30 osp
-rwxr-xr-x 1 mmh mmh  17704 Nov  3 13:26 osp_cli
-rwxr-xr-x 1 mmh mmh  17648 Nov  4 16:30 pa_devs
-rwxrwxr-x 1 mmh mmh    430 Nov  4 16:16 run_osp
-rwxrwxr-x 1 mmh mmh     64 Nov  2 17:46 start-ews
-rwxrwxr-x 1 mmh mmh     88 Nov  2 17:48 start-ews-php
```

- osp - The audio processing server, formerly RT-MHA  
- osp_cli - The command line interface for osp  
- pa_devs - Linux only.  Print all the audio devices.  
- run_osp - Runs osp, osp_cli, ews, and ews-php in tabs in a terminal.  
- start-ews - Starts the nodejs Embedded Web Server
- start-ews-php - Starts the PHP Embedded Web Server
