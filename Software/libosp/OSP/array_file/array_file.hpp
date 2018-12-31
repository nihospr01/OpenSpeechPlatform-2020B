//
// Created by Kuan-Lin Chen on 10/8/18.
//

#ifndef OSP_ARRAY_FILE_H
#define OSP_ARRAY_FILE_H

/**
 * @brief Array File Class
 * @details Reading a binary file into an array in single-precision floating-point format
 */
class array_file {

public:
    /**
     * @brief Array file constructor
     * @param path The path of the binary file
     */
    array_file(std::string path);

    /**
     * @brief Array file destructor
     */
    ~array_file();

    /**
     * @brief Getting the length of the array
     * @return The length of the array
     */
    size_t get_len();

    /**
     * @brief Getting the pointer which points to the array
     * @return The pointer which points to the array
     */
    float* get_ptr();

private:
    float* array_;
    size_t array_len_;
    bool is_read_;
};


#endif //OSP_ARRAY_FILE_H
