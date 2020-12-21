//======================================================================================================================
/** @file array_file.cpp
 *  @author Open Speech Platform (OSP) Team, UCSD
 *  @copyright Copyright (C) 2020 Regents of the University of California Redistribution and use in source and binary
 *  forms, with or without modification, are permitted provided that the following conditions are met:
 *
 *      1. Redistributions of source code must retain the above copyright notice, this list of conditions and the
 *      following disclaimer.
 *
 *      2. Redistributions in binary form must reproduce the above copyright notice, this list of conditions and the
 *      following disclaimer in the documentation and/or other materials provided with the distribution.
 *
 *  THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS IS" AND ANY EXPRESS OR IMPLIED WARRANTIES,
 *  INCLUDING, BUT NOT LIMITED TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE ARE
 *  DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT HOLDER OR CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL,
 *  SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR
 *  SERVICES; LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND ON ANY THEORY OF LIABILITY,
 *  WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE
 *  USE OF THIS SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.
 */
//======================================================================================================================

//
// Last Modified by Kuan-Lin Chen on 2018/08/18.
//

#include <stdlib.h>
#include <string>
#include <fstream>
#include <OSP/array_file/array_file.hpp>

array_file::array_file(std::string path) {
    std::ifstream f(path, std::ifstream::binary | std::ifstream::ate);
    if (f.is_open()) {
        auto file_size = (size_t) f.tellg();
        if ((file_size & 3) == 0) {
            f.seekg(0);
            array_len_ = file_size >> 2;
            array_ = new float[array_len_];
            f.read((char *) array_, file_size);
            is_read_ = true;
        } else {
            is_read_ = false;
        }
        f.close();
    } else {
        is_read_ = false;
    }
}

array_file::~array_file() {
    if (is_read_) {
        delete[] array_;
    }
}

size_t
array_file::get_len() {
    return array_len_;
}

float *
array_file::get_ptr() {
    return array_;
}
