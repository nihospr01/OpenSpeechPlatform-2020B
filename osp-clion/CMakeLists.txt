cmake_minimum_required(VERSION 3.7)
project(osp_clion)
set(CMAKE_C_STANDARD 11)
set(CMAKE_C_FLAGS "${CMAKE_C_FLAGS} -O3 -Wall -Wextra")
set(CMAKE_C_FLAGS "${CMAKE_C_FLAGS} -pthread")

IF(APPLE)
    #SET(GUI_TYPE MACOSX_BUNDLE)
    #INCLUDE_DIRECTORIES ( /Developer/Headers/FlatCarbon )
    FIND_LIBRARY(CORE_SERVICES CoreServices)
    FIND_LIBRARY(CORE_FOUNDATION CoreFoundation )
    FIND_LIBRARY(AUDIO_UNIT AudioUnit )
    FIND_LIBRARY(AUDIO_TOOLBOX AudioToolbox )
    FIND_LIBRARY(CORE_AUDIO CoreAudio)
    MARK_AS_ADVANCED (CORE_SERVICES
            CORE_FOUNDATION
            AUDIO_UNIT
            AUDIO_TOOLBOX
            CORE_AUDIO)
    SET(EXTRA_LIBS ${CORE_SERVICES} ${CORE_FOUNDATION} ${AUDIO_UNIT} ${AUDIO_TOOLBOX} ${CORE_AUDIO})

ENDIF ()

IF(UNIX AND NOT APPLE)
    find_package( ALSA )
ENDIF()

set(SOURCE_FILES main.c logger.c osp-libs/afc/afc.c osp-libs/afc/circular_buffer.c
        osp-libs/array_utilities/array_utilities.c osp-libs/beam_forming/beam_forming.c osp-libs/beam_forming/delay_line.c
        osp-libs/filter/filter.c osp-libs/osp_process/osp_process.c osp-libs/osp_process/utilities.c osp-libs/osp_tcp/osp_tcp.c
        osp-libs/osp_tcp/tcplib.c osp-libs/peak_detect/peak_detect.c osp-libs/resample/resample.c osp-libs/wdrc/mpo.c
        osp-libs/wdrc/wdrc.c osp-libs/wdrc/wdrc_mpo_support.c osp-libs/speech_enhancement/speech_enhancement.c test_main.c)

include_directories(/usr/include osp-libs /usr/local/include osp-libs/afc osp-libs/array_utilities osp-libs/beam_forming osp-libs/common
        osp-libs/filter osp-libs/osp_process osp-libs/osp_tcp osp-libs/peak_detect osp-libs/resample osp-libs/speech_enhancement osp-libs/wdrc)
link_directories(/usr/lib /usr/local/lib)

IF(UNIX AND NOT APPLE)

    add_executable(osp_clion ${SOURCE_FILES} alsa_wrapper.c)
    target_link_libraries(osp_clion ${EXTRA_LIBS} m rt asound)
    target_compile_definitions(osp_clion PRIVATE ALSA_PA=0)
    set(ENV{OSP_FILTER_PATH_BASE} "/home/nal/CLionProjects/osp-clion-alsa/filter_coefficients")

ENDIF()
IF(APPLE)
    add_executable(osp_clion ${SOURCE_FILES} port_wrapper.c)
    target_link_libraries(osp_clion /usr/local/lib/libportaudio.a ${EXTRA_LIBS} m)
    target_compile_definitions(osp_clion PRIVATE ALSA_PA=1)
ENDIF()
