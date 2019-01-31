cmake_minimum_required(VERSION 3.8)
project(OpenSpeechPlatform)

set(OSP_VERSION 2.0)
set(OSP_VERSION_MAJOR 2)
set(OSP_VERSION_MINOR 0)

set(CMAKE_CXX_STANDARD 14)

if(NOT CMAKE_BUILD_TYPE)
  set(CMAKE_BUILD_TYPE Release)
endif()

set(CMAKE_CXX_FLAGS "-Wall -Wextra")
set(CMAKE_CXX_FLAGS_DEBUG "-g")
set(CMAKE_CXX_FLAGS_RELEASE "-O3")

configure_file("include/openspeechplatform.hpp.in" "include/openspeechplatform.hpp")

IF(UNIX AND NOT APPLE)
    find_package(ALSA REQUIRED)
ENDIF()

find_library(portaudio_LIBRARIES portaudio)
find_package(Poco REQUIRED Foundation Util Net)
find_package(OSP REQUIRED)
include_directories(include ${CMAKE_CURRENT_BINARY_DIR}/include)

add_executable(osp
        src/main.cpp
        src/portaudio_wrapper.cpp
        include/osp_parser.hpp
        include/osp_param.h
        include/filter_coef.h
        include/portaudio_wrapper.h
        include/osp_process.hpp
        include/control_param.h
        include/ews_connect.hpp
        include/sema.hpp
        include/openspeechplatform.hpp.in)
target_link_libraries(osp OSP ${portaudio_LIBRARIES})

install(TARGETS osp
        RUNTIME DESTINATION bin)

IF(UNIX AND NOT APPLE)
    add_executable(pa_devs
            src/pa_devs.cpp)
    target_link_libraries(pa_devs ${portaudio_LIBRARIES})

    install(TARGETS pa_devs
            RUNTIME DESTINATION bin)
ENDIF()
