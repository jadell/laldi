#!/bin/sh
cd `dirname $0`
if [ ! -f data/hello.json ]; then
	cp data/hello.bak.json data/hello.json
	chmod 666 data/hello.json
fi
