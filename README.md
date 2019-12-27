# What is new in Open Speech Platform 2019b? #
* **Transducers**: The BTE-RIC files now contain V2 of the rigid PCB and V4 of the flex PCB. These changes improve the yield and reliability of BTE-RICs assemblies. Release 2019b includes V4 of the CAD files for printing BTE-RIC shells.

* **Hardware**: In this release, we re-designed the carrier board that used  Myon-1 model from Keith&Koep to use model Dart-sd410 from Variscite as we were facing supply problems from Keith&Koep. Release 2019b contains the V8 board design which includes battery charging, buttons for on/off, and mute functionality. The release also contains V5 of the CAD files for printing cases for the processing and communication device (PCD). 

* **Firmware and OS**: The boot.img and rootfs.img files have been updated to include drivers for the IMUs in the BTE-RICs and onboard the pendant. The onboard audio built into the SnapDragon 410C/Power Management IC has been enabled in the device tree for the 2 onboard audio inputs channels. 

* **Software**: The previous release incurred end-to-end analog latency of about 2.5 ms with the RT-MHA disabled and about 5.8 ms with RT-MHA enabled. This release contains a Left+Right beamformer (BF) with 5 ms additional latency and runs realtime. The BF provided about 12 dB speech to interference ratio improvement in file-based simulations. The software also includes additional bug fixes, improvements and documentation.

* **Applications**: This release includes improvements to (i) researcher apps, (ii) self-fitting apps, (iii) outcomes apps and (iv) ecological momentary assessment (EMA) apps. The Goldilocks app has enhancements in support of field studies. 

Additional information of Release 2019b can be found in IEEE Access article [“A Wearable, Extensible, Open-Source Platform for Hearing Healthcare Research.”, Pisha et. Al, 2019.](https://ieeexplore.ieee.org/stamp/stamp.jsp?tp=&arnumber=8890721)
