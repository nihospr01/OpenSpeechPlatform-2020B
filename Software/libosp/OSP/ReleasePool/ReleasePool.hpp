//
// Created by Dhiman Sengupta on 3/26/19.
//

#ifndef OPENSPEECHPLATFORMLIBRARIES_RELEASEPOOL_HPP
#define OPENSPEECHPLATFORMLIBRARIES_RELEASEPOOL_HPP

#include <cstdlib>
#include <vector>
#include <mutex>
#include <memory>
#include <algorithm>

class ReleasePool
{
public:
    ReleasePool() = default;
    template<typename T> void add (const std::shared_ptr<T>& object) {
        if (object.get() == 0)
            return;
        std::lock_guard<std::mutex> lock (m);
        pool.emplace_back (object);
    }
    void release() {
        std::lock_guard<std::mutex> lock (m);
        pool.erase(
                std::remove_if (
                        pool.begin(), pool.end(),
                        [] (auto& object) { return object.use_count() <= 1; } ),
                pool.end());
    }
    std::vector<std::shared_ptr<void>> pool;
    std::mutex m;
};


#endif //OPENSPEECHPLATFORMLIBRARIES_RELEASEPOOL_HPP
