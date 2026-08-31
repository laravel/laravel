# Contributing
hamcrest-php is an open source, community-driven project. If you'd like to contribute, feel free to do this, but remember to follow these few simple rules:

## Asking Questions
Feel free to ask any questions and share your experiences in the [Issue tracking system](https://github.com/hamcrest/hamcrest-php/issues/) and help to improve the documentation.

## Submitting an issues
- A reproducible example is required for every bug report, otherwise it will most probably be __closed without warning__.
- If you are going to make a big, substantial change, let's discuss it first.

## Working with Pull Requests
1. Create your feature addition or a bug fix branch based on __`master`__ branch in your repository's fork.
2. Make necessary changes, but __don't mix__ code reformatting with code changes on topic.
3. Add tests for those changes (please look into `tests/` folder for some examples). This is important so we don't break it in a future version unintentionally.
4. Check your code using "Coding Standard" (see below).
5. Commit your code.
6. Squash your commits by topic to preserve a clean and readable log.
7. Create Pull Request.

## Running the Tests

### Installation/Configuration

1. Using `git clone https://github.com/hamcrest/hamcrest-php` to clone this repository.
2. Using the `composer update` to update the dependencies to support your development environment.
3. Using `vendor/bin/phpunit -c tests/phpunit.xml.dist` command to do unit test works.

## Contributor Code of Conduct

Please note that this project is released with a [Contributor Code of
Conduct](http://contributor-covenant.org/). By participating in this project
you agree to abide by its terms. See [CODE_OF_CONDUCT](CODE_OF_CONDUCT.md) file.
