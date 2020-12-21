#ifndef OSP_HPP__
#define OSP_HPP__

#include <atomic>

namespace osp {
std::atomic<int> running(0);
std::atomic<int> reset(0);  // when reset requested, this gets set to the number of bands
std::atomic<int> audio_enabled(0);
}  // namespace osp

#endif  // OSP_HPP__
