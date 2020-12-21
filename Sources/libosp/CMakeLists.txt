cmake_minimum_required(VERSION 3.8)
project(OSP)

set(CMAKE_CXX_STANDARD 17)

if(NOT CMAKE_BUILD_TYPE)
  set(CMAKE_BUILD_TYPE Release)
endif()

#set(CMAKE_CXX_FLAGS_DEBUG "-g -Wall -Wextra -fsanitize=address")
set(CMAKE_CXX_FLAGS_DEBUG "-g -O1 -Wall -Wextra -fsanitize=address -fno-omit-frame-pointer -fno-optimize-sibling-calls")
set(CMAKE_CXX_FLAGS_RELEASE "-O3 -march=native -Wno-unused-result")

install(PROGRAMS bin/osp_cli.py DESTINATION bin RENAME osp_cli)

# add_subdirectory(portaudio)

add_subdirectory (OSP)

if(CMAKE_PROJECT_NAME STREQUAL OSP AND BUILD_TESTING)
  add_subdirectory (test)
endif()

add_subdirectory (src)
