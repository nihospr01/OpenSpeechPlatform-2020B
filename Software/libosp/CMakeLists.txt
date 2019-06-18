cmake_minimum_required(VERSION 3.8)
project(OpenSpeechPlatformLibraries)

set(Poco_DIR "/usr/local/lib/cmake/Poco/") 
set(Poco_INCLUDE_DIRS "/usr/include/Poco/")

find_package(Poco REQUIRED Foundation Util Net)

set(CMAKE_CXX_STANDARD 14)

if(NOT CMAKE_BUILD_TYPE)
  set(CMAKE_BUILD_TYPE Release)
endif()

set(CMAKE_CXX_FLAGS "-Wall -Wextra")
set(CMAKE_CXX_FLAGS_DEBUG "-g")
set(CMAKE_CXX_FLAGS_RELEASE "-O3")

set(libosp_VERSION 2.0)
set(libosp_VERSION_MAJOR 2)
set(libosp_VERSION_MINOR 0)
configure_file("OSP/OSP.hpp.in" "OSP.hpp")
configure_file("OSP/OSP.pc.in" "OSP.pc")

add_library(OSP STATIC
        OSP/adaptive_filter/adaptive_filter.hpp
        OSP/adaptive_filter/adaptive_filter.cpp
        OSP/afc/afc.hpp
        OSP/afc/afc.cpp
        OSP/array_file/array_file.hpp
        OSP/array_file/array_file.cpp
        OSP/array_utilities/array_utilities.hpp
        OSP/array_utilities/array_utilities.cpp
        OSP/beamformer/beamformer.hpp
        OSP/beamformer/beamformer.cpp
        OSP/circular_buffer/circular_buffer.hpp
        OSP/circular_buffer/circular_buffer.cpp
        OSP/fileio/sema.hpp
        OSP/fileio/AudioFile.h
        OSP/fileio/AudioFile.cpp
        OSP/fileio/playfile.h
        OSP/fileio/playfile.cpp
        OSP/fileio/file_record.h
        OSP/fileio/file_record.cpp
        OSP/filter/filter.hpp
        OSP/filter/filter.cpp
        OSP/resample/resample.hpp
        OSP/resample/resample.cpp
        OSP/subband/noise_management.hpp
        OSP/subband/noise_management.cpp
        OSP/subband/peak_detect.hpp
        OSP/subband/peak_detect.cpp
        OSP/subband/wdrc.hpp
        OSP/subband/wdrc.cpp
        OSP/ReleasePool/ReleasePool.hpp)

install(TARGETS OSP EXPORT OSP-targets
        LIBRARY DESTINATION lib
        ARCHIVE DESTINATION lib
        PUBLIC_HEADER DESTINATION include/OSP)
install(EXPORT OSP-targets FILE OSPConfig.cmake
        DESTINATION share/cmake/OSP)
install(DIRECTORY OSP/
        DESTINATION include/OSP
        FILES_MATCHING PATTERN "*.h" PATTERN "*.hpp")
install(FILES ${CMAKE_CURRENT_BINARY_DIR}/OSP.hpp
        DESTINATION include/OSP)
install(FILES ${CMAKE_CURRENT_BINARY_DIR}/OSP.pc
        DESTINATION lib/pkgconfig)

target_include_directories(OSP PRIVATE .)
target_link_libraries(OSP ${Poco_LIBRARIES})


#include_directories(SYSTEM include)
#add_executable(libosp-adaptive_filter-test include/adaptive_filter/adaptive_filter.hpp
#        test/adaptive_filter_test.cpp)
#
#
#add_executable(libosp-beamformer-test
#        include/beamformer/beamformer.hpp
#        test/beamformer_test.cpp)
#
#add_executable(libosp-afc-test include/afc/afc.hpp
#        include/afc/afc_init_filter.h
#        test/afc_test.cpp
#        include/afc/bandlimited_filter.h
#        include/afc/prefilter.h)
#
#add_executable(libosp-filter-test
#        include/filter/filter.hpp
#        test/filter_test.cpp)
