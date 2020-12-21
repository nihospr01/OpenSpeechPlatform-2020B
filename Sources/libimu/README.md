# LibIMU
A library to efficiently work with multiple IMU devices
## Application Overview
LibIMU is a C++ library that ease the acquisition of data from multiple IMU devices at high speed. It supports multiple IMU devices and buffering of data from IMU.

In our case, there are three devices two ear-level and one on PCD. LibIMU send a vector IMU data with timestamp and sample number from each IMU to the network. The final stream is 20 sized vector as following

`timestamp, sample_number, gx_1, gy_1, gz_1,ax_1,ay_1,az_1, ...` (18 values total, one for each axis of accelerometer and gyroscope data from all three devices)

LibIMU uses [Libiio](https://github.com/analogdevicesinc/libiio) to get raw data from IMU sensors on devices, the sampling frequency is set to 50 Hz by default. It loads the raw data into buffer of an arbitrary length. The buffer can be cyclic or acyclic. The default length of buffer is 4 and default buffer type is acyclic buffer. LibIMU APIs convert data into appropriate SI units. m/s^2 for acceleration data and rad/s for gyroscope data. It then packs data from all three devices into a vector and send the vector as a packet over Lab Streaming Layer.

The [Lab Streaming Layer](https://github.com/sccn/labstreaminglayer) (LSL) uses UDP protocol to find and connect to a client. Every stream will have a stream type (eg. IMU data, EEG Data), stream name and stream Unique ID. The client will use stream information to connect to server and form a TCP connection. It is preferred to use stream unique ID to connect clients to server. When a client is connected to server, the samples are send realibity and in-order using TCP protocol.


**Data Flow in LibIMU:**
1. Acquire data from IMU Hardware using IIO (Industrial I/O)
      - Uses Libiio
      - Supports multiple devices, channels, buffers of different lengths.
2. Wrap data into a packet (vector of values for all iio channels)
3. Transmits the timestamped packet over the network using Lab Streaming Layer (LSL)
      - Uses liblsl
      - Uses UDP for service discovery and TCP for reliable and in-order data transmission

## Installation
Use the following commands on a Linux system to build the library and transmiting data real-time.
```bash
$ git clone https://user_name@bitbucket.org/openspeechplatform/libimu.git
cd libimu

# Initialize hardware triggers
$ chmod +x ./init_trigger.sh
$ ./init_trigger.sh

# build library
mkdir build && cd "$_"
cmake ..
make
```
**Note:** Make sure that both ear-level assembly are connected to PCD device before initializing the hardware triggers.

Once library is build run `reference_imu` to start collecting data from IMU and transmitting it to remote clients.

## Getting Started
After LibIMU is installed use `reference_imu.cpp` to run libIMU

`reference_imu.cpp` has following features:

- `num_dev` for number of IMU devices
- `dev_names` for name of each IMU device connected
- `trigger_str` for name of trigger for each device
- `buffer_length` for length of IMU data buffer
- `lsl_stream_name` for lsl stream name
- `lsl_stream_type` for lsl stream type

**Note:** After modifying `reference_imu.cpp`, `make` the file again in `build` directory:
```bash
cd build
make
```

**Use `misc/iio-test.cpp` to test libiio**

## Major Dependencies

###  [libiio v0.1](https://github.com/analogdevicesinc/libiio)
Data acquistion from IMU hardware
- Build libiio as given in the Linux section in [this link](https://wiki.analog.com/resources/tools-software/linux-software/libiio)
- [optional] Refer [libiio reference page](https://analogdevicesinc.github.io/libiio/v0.19/index.html) for further experimentation with library

### [liblsl](https://github.com/sccn/liblsl)
Data transmission to the network
- Install liblsl on system as following:

``` bash
git clone https://github.com/sccn/liblsl.git
cd liblsl
git checkout 1.13.1
mkdir build && cd build
cmake -DLSL_UNIXFOLDERS=1 -DCMAKE_INSTALL_PREFIX=/usr/local -DLSL_BUILD_EXAMPLES=1 ..
make && make install
```

- **Note:** There are compatibility issues between different versions of liblsl. Make sure both lsl server and client have same versions. We have used version 1.13.1 for libIMU 
- On client-side, verify the version you have for pylsl using `pip show pylsl` and make sure the version is 1.13

### cmake

**Note:** Please refer to the documentations of above dependencies for further understanding