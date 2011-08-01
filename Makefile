#**
# Makefile for calling installer script. Modify the DSTDIR variable below
# to install in an other directory.
#
# @octdoc		h:phpreprocess/Makefile
# @copyright	copyright (c) 2011 by Harald Lapp
# @author		Harald Lapp <harald@octris.org>
#**

CURDIR := $(shell pwd)

DSTDIR := "/usr/local/bin"

help:
	@echo "make targets:"
	@echo "    install    creates single-executable '$(DSTDIR)/phpreprocess'"

install:
	@php -dphar.readonly=0 $(CURDIR)/phar/install.php $(DSTDIR)