# OSP-2018c-Release

* Release 2018c Lab System supports both OS X and Linux computers. Our portable and wearable systems use 64-bit Debian Linux to run OSP on the realtime, embedded systems. As a byproduct, the lab system can now run on Linux also. 

* Release 2018c supports distributed processing on multiple processor cores. The portable system currently uses Snapdragon™ 8016 processor. This and other emerging IoT chipsets feature multiple cores. Snapdragon™ 8016 processor has four A53 ARM cores, running at 1.2 GHz. An important requirement for OSP is that it is responsive to user input, while not missing realtime deadlines for HA processing. Release 2018c has gone through extensive code refactoring to meet this requirement. One core is dedicated to all the kernel and the user interactions. Two cores are dedicated to left HA and right HA processing, respectively.

* Release 2018c includes a simplified  “Getting Started User Guide.” This document describes install, build and test procedures for both RT-MHA and Embedded Web Server (EWS) comprising the OSP. 

* Release 2018c has been tested with a commercial, off the shelf (COTS) headset such as [Andrea Communications 3D Surround Sound Recording CANS] (https://tinyurl.com/y9xnee32). They plug in to the USB port and provide microphones and receivers on each ear. These headsets are useful for engineers working on HA processing software and developing web apps. Currently, they may be of limited value for research with hearing-impaired listeners.

* Release 2018c includes complete hardware release of the portable system and customs BTE-RICs. Schematics, Altium project files, CAD files for 3D printing cases, CAD files for 3D printing BTE-RIC cases, and CAD files for 3D printing strain relief for BTE-RIC cables connecting to the processor system are included. We have manufactured limited number of systems and plan to test them in the field with collaborating labs in the coming months.

* Release 2018c includes three functional web apps; (i) Researcher App for controlling HA parameters for left and right processing, (ii) Goldilocks App for self fitting research designed at SDSU and (iii) Ecological Momentary Assessment app. Release 2018c also includes 4AFC and AB comparison web apps with complete GUI, but not yet connected to the RT-MHA. We included them in this release to seek early feedback on the user interface design.

```bash
├── Documentation
│   ├── EWS_User_Guide.pdf
│   ├── OSP_Getting_Started_Guide.pdf
│   ├── README.md
│   └── RTMHA_API.pdf
├── Hardware
│   ├── BTE-RIC\ Files
│   ├── BoB-Break\ Out\ Board\ Files
│   ├── Hardware\ User\ Guide\ -\ Portable\ System\ (Gdoc).docx
│   ├── Hardware\ User\ Guide\ -\ Portable\ System.pdf
│   ├── Mechanical\ CAD\ Files
│   └── Mezzanine\ Board\ Files
├── LICENSE
├── README.md
└── Software
    ├── EWS
    ├── OSP
    ├── README.md
    ├── install
    ├── libosp
    └── submodule

11 directories, 10 files
```
