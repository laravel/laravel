Bug with Eloquent Events during testing

There is a bug when models events are registered within the boot method of the class. During tests the registered events works only for the first test Case.

## How to reproduce the bug

I created a test to make it fail ^^ :

- composer update
- vendor/bin/phpunit

## My Idea for a fix

A fix would be to flushEventListeners() for all models in the setUp method of the TestCase Class. Since Model::flushEventListeners(); followed by Model::boot(); seems to fix the problem