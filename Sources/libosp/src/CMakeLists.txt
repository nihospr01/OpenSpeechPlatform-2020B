macro(print_all_variables)
    message(STATUS "print_all_variables------------------------------------------{")
    get_cmake_property(_variableNames VARIABLES)
    foreach (_variableName ${_variableNames})
        message(STATUS "${_variableName}=${${_variableName}}")
    endforeach()
    message(STATUS "print_all_variables------------------------------------------}")
endmacro()

IF(UNIX AND NOT APPLE)
    find_package(ALSA REQUIRED)
ENDIF()
find_package(Git QUIET REQUIRED)

find_library(portaudio_LIBRARIES portaudio)
find_package(Threads REQUIRED)
# find_package(ZeroMQ REQUIRED)

# print_all_variables()

# Store version into variable
execute_process(COMMAND ${GIT_EXECUTABLE} describe --tags --always
    OUTPUT_VARIABLE GIT_REPO_VERSION OUTPUT_STRIP_TRAILING_WHITESPACE)
configure_file("OspVersion.hpp.in" "OspVersion.hpp")

add_custom_target( OSPGitVersion
    COMMAND ${CMAKE_COMMAND}
        -D GIT_EXECUTABLE=${GIT_EXECUTABLE}
        -D INPUT_FILE=${CMAKE_CURRENT_SOURCE_DIR}/OspVersion.hpp.in
        -D OUTPUT_FILE=${CMAKE_CURRENT_BINARY_DIR}/OspVersion.hpp
        -P ${CMAKE_CURRENT_SOURCE_DIR}/generate_version.cmake
)

add_custom_command (
  OUTPUT ${CMAKE_CURRENT_BINARY_DIR}/config.hpp ${CMAKE_CURRENT_BINARY_DIR}/config10.hpp
  COMMAND ${CMAKE_CURRENT_SOURCE_DIR}/../bin/xxd.py --var OSP_CONFIG -f ${CMAKE_CURRENT_SOURCE_DIR}/config.json > ${CMAKE_CURRENT_SOURCE_DIR}/include/config.hpp
  COMMAND ${CMAKE_CURRENT_SOURCE_DIR}/../bin/xxd.py --var OSP_CONFIG10 -f ${CMAKE_CURRENT_SOURCE_DIR}/config10.json > ${CMAKE_CURRENT_SOURCE_DIR}/include/config10.hpp
  DEPENDS config.json config10.json
)

include_directories(include ${CMAKE_CURRENT_BINARY_DIR}/include ${CMAKE_CURRENT_SOURCE_DIR}/.. ${CMAKE_CURRENT_BINARY_DIR}/)

add_executable(osp
        main.cpp
        config.hpp
        config10.hpp
        portaudio_wrapper.cpp
        )
target_link_libraries(osp OSP ${portaudio_LIBRARIES} Threads::Threads)
IF(APPLE)
    target_include_directories(osp PRIVATE /usr/local/include)
ENDIF()

install(TARGETS osp
        RUNTIME DESTINATION bin)


IF(UNIX AND NOT APPLE)
    add_executable(pa_devs
                   pa_devs.cpp)
    target_link_libraries(pa_devs ${portaudio_LIBRARIES})

    install(TARGETS pa_devs
            RUNTIME DESTINATION bin)
ENDIF()