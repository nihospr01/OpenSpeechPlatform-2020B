# OSP Library and Command Line Interface

    Now that the CLI is in this repo, the old osp-clion-cxx
    repository is not longer used.

We are now using GitFlow on this repository.  See 
https://bitbucket.org/openspeechplatform/osp/src/develop/OSPDev.md

If you have important work on a branch, please merge it into develop,
or document why the branch exists. You can put the
documentation in this document.  If the work is no longer useful,
delete the branch.

**This is an internal document.  PLEASE feel free to add notes
or make any changes**

# Building

`make` or `make release` builds an optimized libOSP in the `release`
directory.   It also installs the library and headers into `../release`.

`make debug` builds a debug version of libOSP in the `debug` directory.

# Testing

You must have the following installed:  

* [Google Test](https://github.com/google/googletest)  
* [pytest](https://docs.pytest.org/en/stable/)  
* [pytest-cpp](https://github.com/pytest-dev/pytest-cpp)  

This allows us to use Google Test for C++ testing and Python
for higher-level testing.  PyTest is also used
as the test runner for both.

`make test` compiles for release and runs the tests.  It does not install.  

`make debug` compiles for debug and runs the tests.  If you want to install
the debug code "`make debug; cd debug; make install`"

You can also run tests by simply typing `pytest` in the toplevel directory.

# Documentation

Building documentation requires Doxygen.  It builds a
large set of HTML pages.  The pages are currently not
checked in but we may do so in the future.

`make docs`

# Adding Tests

See `test/filter_test.cpp` for a C++ test example using Google Test.

# Discussion

Discussion should take place in the open in the #libosp Slack channel.  
https://openspeech.slack.com/archives/C019E5LK0QK

# Notes

bugs?  
problems?  
questions?  

# Internal Docs

See [OSP Architecture](docs/all.md)