.PHONY: clean

SOURCES=intro.md arch.md
MMD_SOURCES=$(wildcard *.mmd)
MERMAID_PNGS := $(MMD_SOURCES:%.mmd=images/%.png)
MERMAID_CLI=mmdc

DOXYGEN=doxygen
# DOXYGEN=/opt/doxygen/build/bin/doxygen

images/%.png: %.mmd
	$(MERMAID_CLI) -i $< -o $@

all: $(SOURCES) $(MERMAID_PNGS)
	cat $(SOURCES) > all.md
	$(DOXYGEN) Doxyfile.in
	@echo "Open html/index.html in your browser"

clean:
	$(RM) -r html
