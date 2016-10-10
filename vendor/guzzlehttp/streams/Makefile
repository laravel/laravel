all: clean coverage

release: tag
	git push origin --tags

tag:
	chag tag --sign --debug CHANGELOG.rst

test:
	vendor/bin/phpunit

coverage:
	vendor/bin/phpunit --coverage-html=artifacts/coverage

view-coverage:
	open artifacts/coverage/index.html

clean:
	rm -rf artifacts/*
