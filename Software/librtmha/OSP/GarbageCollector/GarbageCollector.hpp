//======================================================================================================================
/** @file ReleasePool.hpp
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
// Last Modified by Dhiman Sengupta on 3/26/19.
//

#ifndef OPENSPEECHPLATFORMLIBRARIES_RELEASEPOOL_HPP
#define OPENSPEECHPLATFORMLIBRARIES_RELEASEPOOL_HPP

#include <cstdlib>
#include <vector>
#include <mutex>
#include <memory>
#include <algorithm>

class GarbageCollector
{
public:
    GarbageCollector() = default;
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
    std::vector<std::shared_ptr<void> > pool;
    std::mutex m;
};


#endif //OPENSPEECHPLATFORMLIBRARIES_RELEASEPOOL_HPP
