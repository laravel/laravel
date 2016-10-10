all: clean coverage docs

docs:
	cd docs && make html

view-docs:
	open docs/_build/html/index.html

start-server: stop-server
	node tests/Client/server.js &> /dev/null &

stop-server:
	@PID=$(shell ps axo pid,command \
	  | grep 'tests/Client/server.js' \
	  | grep -v grep \
	  | cut -f 1 -d " "\
	) && [ -n "$$PID" ] && kill $$PID || true

test: start-server
	vendor/bin/phpunit $(TEST)
	$(MAKE) stop-server

coverage: start-server
	vendor/bin/phpunit --coverage-html=build/artifacts/coverage $(TEST)
	$(MAKE) stop-server

view-coverage:
	open build/artifacts/coverage/index.html

clean:
	rm -rf build/artifacts/*
	cd docs && make clean

tag:
	$(if $(TAG),,$(error TAG is not defined. Pass via "make tag TAG=4.2.1"))
	@echo Tagging $(TAG)
	chag update -m '$(TAG) ()'
	git add -A
	git commit -m '$(TAG) release'
	chag tag

perf: start-server
	php tests/perf.php
	$(MAKE) stop-server

.PHONY: docs
