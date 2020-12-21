cmake_minimum_required(VERSION 3.13)
project(libimu)

set(CMAKE_CXX_STANDARD 14)

#add_executable(test test1.cpp)
#add_executable(iio-test iio-test.c)
#add_executable(example-libimu example.cpp)
#add_executable(libimu libimu.cpp)
add_executable(refimu reference_imu.cpp)

find_library(LIBIIO iio)
if(NOT LIBIIO)
   message(FATAL_ERROR "libiio not found")
endif()
find_library(LIBLSL lsl)


add_library(LIBIMU libimu.cpp)

#target_link_libraries(test ${LIBLSL} ${LIBIIO})
#target_link_libraries(example-libimu ${LIBLSL} ${LIBIIO})
#target_link_libraries(iio-test ${LIBIIO}) 
#target_link_libraries(libimu ${LIBLSL} ${LIBIIO})
target_link_libraries(refimu ${LIBIMU} ${LIBLSL} ${LIBIIO})
