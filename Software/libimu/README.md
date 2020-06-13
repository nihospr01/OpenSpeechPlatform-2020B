# LibIMU
A library to efficiently work with multiple IMU Devices
## Application Overview
LibIMU is a library that ease the acquisition of data from multiple IMU devices at high speed. It supports multiple IMU devices and buffering of data from IMU.

* Use `reference_imu.cpp` to use libimu.
* Use `misc/iio-test.cpp` to test libiio. 



LibIMU:

1. Acquire data from IMU Hardware using IIO (Industrial I/O)
      * Uses Libiio
      * Supports multiple devices, channels, buffers of different lengths.
      
2. Wrap data into a packet (vector of values for all iio channels)

3. Transmits the timestamped packet over the network using Lab Streaming Layer (LSL)
      * Uses liblsl
      * Uses UDP for service discovery and TCP for data transmission


## Build this application
* Initialize hardware triggers
    
    ```
    $ chmod +x ./init_trigger.sh
    $ ./init_trigger.sh
    ```

*  create a directory and build the library in it

  ```
  $ mkdir build && cd "$_"
  $ cmake ..
  $ make
  ```

* use `./reference_imu` to start transmiting data to the network

## Major Dependencies
#### Please refer to the following documentations for the major packages that are used in this project:
*  [libiio](https://github.com/analogdevicesinc/libiio) for data acquistion from IMU hardware  
*  [liblsl](https://github.com/sccn/liblsl) for data transmission to the network