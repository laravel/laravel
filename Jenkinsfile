#!/usr/bin/env groovy

node {
	try {
		checkout scm

		stage('Dependencies') {
			sh 'composer install --no-scripts'
			sh 'cp .env.example .env'
			sh 'docker-compose run --rm php ./artisan key:gen'
		}

		stage('Test') {
			sh 'docker-compose run --rm php ./vendor/bin/phpunit --testsuite "Unit Tests" --coverage-clover build/logs/coverage-unit.xml'
			sh 'docker-compose run --rm php ./vendor/bin/phpunit --testsuite "Feature Tests" --coverage-clover build/logs/coverage-it.xml'
			sh 'docker-compose run --rm php ./vendor/bin/phpunit --coverage-clover build/logs/coverage.xml --log-junit build/logs/tests.xml'
			sh 'sonar-scanner'
		}
	} finally {
		stage('Clean-up') {
			sh 'docker-compose down --remove-orphans'
		}
	}
}
