cmake_minimum_required(VERSION 3.8)
project(OpenSpeechPlatformLibraries)

set(Poco_DIR "/usr/local/lib/cmake/Poco/") 
set(Poco_INCLUDE_DIRS "/usr/include/Poco/")

find_package(Poco REQUIRED Foundation Util Net)

set(CMAKE_CXX_STANDARD 14)

if(NOT CMAKE_BUILD_TYPE)
  set(CMAKE_BUILD_TYPE Release)
endif()

set(CMAKE_C_FLAGS "-Wall -Wextra")
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
        OSP/freping/freping.hpp
        OSP/freping/freping.cpp
        OSP/freping/hamming_window128.h
        OSP/freping/hamming_window64.h
        OSP/GarbageCollector/GarbageCollector.hpp OSP/filter/fir_formii.cpp
        OSP/filter/fir_formii.h
        OSP/resample/polyphase_hb_downsampler.cpp
        OSP/resample/polyphase_hb_downsampler.h
        OSP/resample/polyphase_hb_upsampler.cpp
        OSP/resample/polyphase_hb_upsampler.h
        OSP/multirate_filterbank/tenband_filterbank.cpp
        OSP/multirate_filterbank/tenband_filterbank.h
        OSP/multirate_filterbank/sokolovaharris_filtercoef.h
        )

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
# first we can indicate the documentation build as an option and set it to ON by default
option(BUILD_DOC "Build documentation" ON)

# check if Doxygen is installed
find_package(Doxygen)
if (DOXYGEN_FOUND)
  # set input and output files
  set(DOXYGEN_IN ${CMAKE_CURRENT_SOURCE_DIR}/docs/Doxyfile.in)
  set(DOXYGEN_OUT ${CMAKE_CURRENT_BINARY_DIR}/Doxyfile)

  # request to configure the file
  configure_file(${DOXYGEN_IN} ${DOXYGEN_OUT} @ONLY)
  message("Doxygen build started")

  # note the option ALL which allows to build the docs together with the application
  add_custom_target( doc_doxygen ALL
          COMMAND ${DOXYGEN_EXECUTABLE} ${DOXYGEN_OUT}
          WORKING_DIRECTORY ${CMAKE_CURRENT_SOURCE_DIR}/docs
          COMMENT "Generating API documentation with Doxygen"
          VERBATIM )
else (DOXYGEN_FOUND)
  message("Doxygen need to be installed to generate the doxygen documentation")
endif (DOXYGEN_FOUND)

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
#
#find_package(OSP REQUIRED)
#add_executable(libosp-freping-test
#        test/freping_test.cpp)
#find_package(OSP REQUIRED)
#add_executable(libosp-cirbuffer-test
#        OSP/circular_buffer/test.cpp )
#
#target_link_libraries(libosp-cirbuffer-test OSP)
#
#find_package(OSP REQUIRED)
#add_executable(libosp-downsample-test
#        test/down_sample_comparision.cpp OSP/down_sampler/down_sampler.cpp OSP/resample/polyphase_hb_downsampler.cpp )
#
#target_link_libraries(libosp-downsample-test OSP)