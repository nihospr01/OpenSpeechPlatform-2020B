
find_package(PkgConfig REQUIRED)
pkg_check_modules(gtest REQUIRED gtest>=1.8.1)
include_directories(${gtest_INCLUDE_DIRS})


add_executable(adaptive_filter_test adaptive_filter_test.cpp)
target_include_directories(adaptive_filter_test PRIVATE
  ${CMAKE_CURRENT_SOURCE_DIR}/../
  ${CMAKE_CURRENT_SOURCE_DIR}/../OSP/
)
target_link_libraries (adaptive_filter_test PUBLIC OSP ${gtest_LIBRARIES} pthread)


add_executable(filter_test filter_test.cpp)
target_include_directories(filter_test PRIVATE
  ${CMAKE_CURRENT_SOURCE_DIR}/../
  ${CMAKE_CURRENT_SOURCE_DIR}/../OSP/
)
target_link_libraries (filter_test PUBLIC OSP ${gtest_LIBRARIES} pthread)

# Missing golden files
# add_executable(freping_test freping_test.cpp)
# target_include_directories(freping_test PRIVATE
#   ${CMAKE_CURRENT_SOURCE_DIR}/../
#   ${CMAKE_CURRENT_SOURCE_DIR}/../OSP/
# )
# target_link_libraries (freping_test LINK_PUBLIC OSP)

# BROKEN
# add_executable(afc_test afc_test.cpp)
# target_include_directories(afc_test PRIVATE
#   ${CMAKE_CURRENT_SOURCE_DIR}/../
#   ${CMAKE_CURRENT_SOURCE_DIR}/../OSP/
# )
# target_link_libraries (afc_test LINK_PUBLIC OSP)

# BROKEN
# add_executable(beamformer_test beamformer_test.cpp)
# target_include_directories(beamformer_test PRIVATE
#   ${CMAKE_CURRENT_SOURCE_DIR}/../
#   ${CMAKE_CURRENT_SOURCE_DIR}/../OSP/
# )
# target_link_libraries (beamformer_test LINK_PUBLIC OSP)
