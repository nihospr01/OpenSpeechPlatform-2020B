#ifndef LIBOSP_HPP
#define LIBOSP_HPP

#define LIBOSP_VERSION_MAJOR 2
#define LIBOSP_VERSION_MINOR 0

#include <OSP/adaptive_filter/adaptive_filter.hpp>
#include <OSP/afc/afc.hpp>
#include <OSP/afc/afc_init_filter.h>
#include <OSP/afc/bandlimited_filter.h>
#include <OSP/afc/prefilter.h>
#include <OSP/array_file/array_file.hpp>
#include <OSP/array_utilities/array_utilities.hpp>
#include <OSP/beamformer/beamformer.hpp>
#include <OSP/circular_buffer/circular_buffer.hpp>
#include <OSP/fileio/AudioFile.h>
#include <OSP/fileio/playfile.h>
#include <OSP/fileio/file_record.h>
#include <OSP/filter/filter.hpp>
#include <OSP/filter/fir_formii.h>
#include <OSP/resample/32_48_filter.h>
#include <OSP/resample/48_32_filter.h>
#include <OSP/resample/resample.hpp>
#include <OSP/subband/noise_management.hpp>
#include <OSP/subband/peak_detect.hpp>
#include <OSP/subband/wdrc.hpp>

#endif //LIBOSP_HPP
