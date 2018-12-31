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
