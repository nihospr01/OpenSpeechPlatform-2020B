TOP_SRCDIR ?=.

.PHONY: all peak_detect wdrc afc filter osp_tcp array_utilities beam_forming osp_process clean

all:
	$(MAKE) -C array_utilities
	$(MAKE) -C filter
	$(MAKE) -C afc
	$(MAKE) -C wdrc
	$(MAKE) -C peak_detect
	$(MAKE) -C osp_tcp
	$(MAKE) -C beam_forming
	$(MAKE) -C osp_process
	$(MAKE) -C resample

peak_detect:
	$(MAKE) -C filter
	$(MAKE) -C peak_detect

wdrc:
	$(MAKE) -C filter
	$(MAKE) -C wdrc

afc:
	$(MAKE) -C array_utilities
	$(MAKE) -C filter
	$(MAKE) -C afc

filter:
	$(MAKE) -C filter

osp_tcp:
	$(MAKE) -C osp_tcp

array_utilities:
	$(MAKE) -C array_utilities

beam_forming:
	$(MAKE) -C beam_forming 

osp_process:
	$(MAKE) -C osp_process

osp_resample:
	$(MAKE) -C filter
	$(MAKE) -C resample

clean:
	$(MAKE) -C filter clean
	$(MAKE) -C array_utilities clean
	$(MAKE) -C afc clean
	$(MAKE) -C wdrc clean
	$(MAKE) -C peak_detect clean
	$(MAKE) -C osp_tcp clean
	$(MAKE) -C beam_forming clean
	$(MAKE) -C osp_process clean
	$(MAKE) -C resample clean
	rm -rf html

docs:
	@doxygen
	@see html/index.html
