# Default target executed when no arguments are given to make.
default_target: release 

.PHONY : release clean docs install debug test

PREFIX ?= 1
ifeq ($(PREFIX), 1)
	RELEASE_DIR = $(shell realpath ..)/release
	DEBUG_DIR = $(shell realpath ..)/debug
else
	RELEASE_DIR = $(PREFIX)
	DEBUG_DIR = $(PREFIX)
endif


release:
	mkdir -p release
	cd release && cmake -DCMAKE_INSTALL_PREFIX=$(RELEASE_DIR) ..
	$(MAKE) -C release install

test:
	mkdir -p release
	cd release && cmake -DCMAKE_INSTALL_PREFIX=$(RELEASE_DIR) -DBUILD_TESTING=1 ..
	$(MAKE) -C release
	pytest
    
debug:
	mkdir -p debug
	cd debug && cmake -DCMAKE_BUILD_TYPE=Debug -DCMAKE_INSTALL_PREFIX=$(DEBUG_DIR) -DBUILD_TESTING=1 ..
	$(MAKE) -C debug
	pytest

docs:
	$(MAKE) -C docs

clean:
	$(MAKE) -C docs clean
	$(RM) -rf release debug
