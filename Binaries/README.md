# Binary Installers

## Mac OS

`OSP-macos-installer-x64-1.3.0.pkg` is the 2020b release.
See [Quick Start Guide](https://github.com/nihospr01/OpenSpeechPlatform-UCSD/blob/master/OSP%20Software%20Documentation/OSP%20Software%20Quick%20Start%20Guide%20(macOS%20Installer)%20-%20Release%202020B.pdf)

## PCD Device

Currently requires Linux or a Mac to install.

We have switched bootloaders.  You must download this
https://releases.linaro.org/96boards/dragonboard410c/linaro/rescue/latest/dragonboard-410c-bootloader-emmc-linux-150.zip


Unzip and run “flashall”.  This only needs to be done once.


Download both the boot and rootfs images.
To uncompress the rootfs on linux, install “p7zip” package.

```
> p7zip -d rootfs.simg.7z
> sudo fastboot flash boot boot-XXX.img
> sudo fastboot flash rootfs rootfs-XXX.simg
> sudo fastboot reboot
```

Use putty to connect to /dev/ttyUSB0. Set speed to 115200

