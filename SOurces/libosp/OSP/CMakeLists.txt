

set(libosp_VERSION 2.0)
set(libosp_VERSION_MAJOR 2)
set(libosp_VERSION_MINOR 0)
configure_file("OSP.hpp.in" "OSP.hpp")
configure_file("OSP.pc.in" "OSP.pc")

add_library(OSP STATIC
        adaptive_filter/adaptive_filter.hpp
        adaptive_filter/adaptive_filter.cpp
        afc/afc.hpp
        afc/afc.cpp
        array_file/array_file.hpp
        array_file/array_file.cpp
        array_utilities/array_utilities.hpp
        array_utilities/array_utilities.cpp
        beamformer/beamformer.hpp
        beamformer/beamformer.cpp
        circular_buffer/circular_buffer.hpp
        circular_buffer/circular_buffer.cpp
        fileio/sema.hpp
        fileio/AudioFile.h
        fileio/playfile.h
        fileio/playfile.cpp
        fileio/file_record.h
        fileio/file_record.cpp
        filter/filter.hpp
        filter/filter.cpp
        resample/resample.hpp
        resample/resample.cpp
        subband/noise_management.hpp
        subband/noise_management.cpp
        subband/peak_detect.hpp
        subband/peak_detect.cpp
        subband/wdrc.hpp
        subband/wdrc.cpp
        subband/wdrc10.hpp
        subband/wdrc10.cpp
        freping/freping.hpp
        freping/freping.cpp
        freping/hamming_window128.h
        freping/hamming_window64.h
        GarbageCollector/GarbageCollector.hpp 
        filter/fir_formii.cpp
        filter/fir_formii.h
        resample/polyphase_hb_downsampler.cpp
        resample/polyphase_hb_downsampler.h
        resample/polyphase_hb_upsampler.cpp
        resample/polyphase_hb_upsampler.h
        multirate_filterbank/tenband_filterbank.cpp
        multirate_filterbank/tenband_filterbank.h
        multirate_filterbank/sokolovaharris_filtercoef.h
        )

target_include_directories(OSP PRIVATE
  ${CMAKE_CURRENT_SOURCE_DIR}/..
)

install(TARGETS OSP EXPORT OSP-targets
        LIBRARY DESTINATION lib
        ARCHIVE DESTINATION lib
        PUBLIC_HEADER DESTINATION include/OSP)
install(EXPORT OSP-targets FILE OSPConfig.cmake
        DESTINATION share/cmake/OSP)
install(DIRECTORY ${CMAKE_CURRENT_SOURCE_DIR}/
        DESTINATION include/OSP
        FILES_MATCHING PATTERN "*.h" PATTERN "*.hpp")
install(FILES ${CMAKE_CURRENT_BINARY_DIR}/OSP.hpp
        DESTINATION include/OSP)
install(FILES ${CMAKE_CURRENT_BINARY_DIR}/OSP.pc
        DESTINATION lib/pkgconfig)

target_include_directories(OSP PRIVATE OSP)

# target_link_libraries(OSP ${Poco_LIBRARIES})
# first we can indicate the documentation build as an option and set it to ON by default
#option(BUILD_DOC "Build documentation" ON)

# check if Doxygen is installed
#find_package(Doxygen)
#if (DOXYGEN_FOUND)
#  # set input and output files
#  set(DOXYGEN_IN ${CMAKE_CURRENT_SOURCE_DIR}/docs/Doxyfile.in)
#  set(DOXYGEN_OUT ${CMAKE_CURRENT_BINARY_DIR}/Doxyfile)

#  # request to configure the file
#  configure_file(${DOXYGEN_IN} ${DOXYGEN_OUT} @ONLY)
#  message("Doxygen build started")

#  # note the option ALL which allows to build the docs together with the application
#  add_custom_target( doc_doxygen ALL
#          COMMAND ${DOXYGEN_EXECUTABLE} ${DOXYGEN_OUT}
#          WORKING_DIRECTORY ${CMAKE_CURRENT_SOURCE_DIR}/docs
#          COMMENT "Generating API documentation with Doxygen"
#          VERBATIM )
#else (DOXYGEN_FOUND)
#  message("Doxygen need to be installed to generate the doxygen documentation")
#endif (DOXYGEN_FOUND)

#include_directories(SYSTEM include)
#add_executable(libosp-adaptive_filter-test OSP/adaptive_filter/adaptive_filter.hpp test/adaptive_filter_test.cpp)
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