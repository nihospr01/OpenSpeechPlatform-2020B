#  OSP Release Notes - 2020B

## Transducers

Stable and no issues reported since 2020a. https://github.com/nihospr01/OpenSpeechPlatform-UCSD/tree/2020a

## Hardware

Stable and no issues reported since 2020a. https://github.com/nihospr01/OpenSpeechPlatform-UCSD/tree/2020a 

## Firmware and OS

**Multiple IMU support:** Baseline version of libIMU that provides access to raw data from the three IMUs (3-D Accl and 3D-gyro data from left ear, right ear, and processing unit – a total of 18 dimensional vector at 100 Hz sampling). The three IMUs (two on each BTE-RIC) and the PCD can be used for physical activity, balance, and head orientation research. 

## Software


- Libosp now has the ability to switch between 6 and 10 bands.  
- There is a new command-line interface to directly control it.
  - Written in python for easy extensibility.
  - Allows easier troubleshooting of web apps communicating with RT-MHA.
  - Provides expanded help with RT-MHA commands.

- A binary software installer is available for the Mac, and soon for Linux.  
  - This provides for simple installation of the complete OSP package without the complexity of building from sources. 
  - It further provides sanity checks for corect operation of the peripherals, the algorithms, and the apps.



## Applications


In 2020a, we introduced Node.js and React for extending EWS features. Both are based on JavaScript and expected to improve rapid prototyping capabilities of OSP for audiology research.   For 2020b we have reimplemented most of the old PHP/Laravel apps in Node/React.

New Apps in 2020b:

- Researcher Page. Has ability to switch between 6 and 10 band RT-MHA.
- Freping Demo app allows for expanding or compressing content in up to three spectral bands. This enables research on frequency domain manipulations along with WDRC to compensate for hearing loss.
- Goldilocks app available as a PHP/Laravel app.
- CoarseFit Demo app allows users to select stimulus (with and without closed captioning display) and select pre-configured NAL-NL2 preseciptions for normal, mild, moderate, severe, and profound.
- 4AFC Demo app provides a template for word recognition accuracy using minimal contrast word sets written in Javascript and React.

&nbsp;

---

We also now have…



## Guides and Documentation


### *OSP Software (macOS)*


[Software Quick Start Guide](OSP%20Software%20Documentation/OSP%20Software%20Quick%20Start%20Guide%20(macOS%20Installer)%20-%20Release%202020B.pdf) - Essential requirements and steps for installing and getting OSP software running.

[Software Getting Started Guide](OSP%20Software%20Documentation/OSP%20Software%20Getting%20Started%20Guide%20-%20Release%202020B.md) - Comprehensive guide to installing and testing OSP software. This guide covers the following, which are also available as separate guides.

* Installation Requirements and Steps (included in the [Software Quick Start Guide](OSP%20Software%20Documentation/OSP%20Software%20Quick%20Start%20Guide%20(macOS%20Installer)%20-%20Release%202020B.pdf))

* [Software Sanity Check - Audio Input and Output Sources](OSP%20Software%20Documentation/OSP%20Software%20Sanity%20Check%20-%20Audio%20Input_Output%20Sources%20(Release%202020B).pdf) - Guide to checking that your audio input and output sources are connected for OSP software usage.

* [Software Sanity Check - Node.js Version of EWS](OSP%20Software%20Documentation/OSP%20Software%20Sanity%20Check%20-%20Nodejs%20Version%20of%20EWS%20(Release%202020B).pdf) - Guide to testing that the Node.js version of the embedded web server (EWS) works as intended.

* [Software Sanity Check - PHP/Laravel Version of EWS](OSP%20Software%20Documentation/OSP%20Software%20Sanity%20Check%20-%20PHP:Laravel%20Version%20of%20EWS%20(Release%202020B).pdf) - Guide to testing that the PHP/Laravel version of the embedded web server (EWS) works as intended.

* [Software Troubleshooting Guide](OSP%20Software%20Documentation/OSP%20Software%20Troubleshooting%20Quick%20Start%20Guide%20(macOS%20Installer)%20-%20Release%202020B.pdf) - Covers steps for possible issues during OSP software installation.

### *OSP Hardware*

[Hardware Quick Start Guide](OSP%20Hardware%20Documentation/OSP%20Hardware%20Quick%20Start%20Guide%20-%20Release%202020B.pdf) - Essential requirements and steps for installing and getting OSP hardware running.

[Hardware Getting Started Guide](OSP%20Hardware%20Documentation/OSP%20Hardware%20Getting%20Started%20Guide%20-%20Release%202020B.md) - Comprehensive guide to installing and testing OSP hardware. This guide covers the following, which are also available as separate guides.

* Installation Requirements and Steps (included in the [Hardware Quick Start Guide](OSP%20Hardware%20Documentation/OSP%20Hardware%20Quick%20Start%20Guide%20-%20Release%202020B.pdf))

* [Hardware Sanity Check - Node.js Version of EWS](OSP%20Hardware%20Documentation/OSP%20Hardware%20Sanity%20Check%20-%20Nodejs%20Version%20of%20EWS%20(Release%202020B).pdf) - Guide to testing that the Node.js version of the embedded web server (EWS) works as intended.

* [Hardware Sanity Check - PHP/Laravel Version of EWS](OSP%20Hardware%20Documentation/OSP%20Hardware%20Sanity%20Check%20-%20PHP_Laravel%20Version%20of%20EWS%20(Release%202020B).pdf) - Guide to testing that the PHP/Laravel version of the embedded web server (EWS) works as intended.

&nbsp;

---

## OSP Forum

Link: [http://openspeechplatform.ucsd.edu/forum/](http://openspeechplatform.ucsd.edu/forum/)

In the [OSP website](http://openspeechplatform.ucsd.edu/), this is where audiologists, hearing aid researchers, and others can connect with one another to provide feedback on issues and suggest improvements to OSP software and hardware.


Anyone can search and read OSP documentation, announcements, and other upcoming posts within the forum. For those who [register an account](http://openspeechplatform.ucsd.edu/register/), they can participate in the forum by creating new posts or responding to existing posts.

