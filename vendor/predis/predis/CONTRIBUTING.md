## Filing bug reports ##

Bugs or feature requests can be posted on the [GitHub issues](http://github.com/nrk/predis/issues)
section of the project.

When reporting bugs, in addition to the obvious description of your issue you __must__ always provide
some essential information about your environment such as:

  1. version of Predis (check the `VERSION` file or the `Predis\Client::VERSION` constant).
  2. version of Redis (check `redis_version` returned by [`INFO`](http://redis.io/commands/info)).
  3. version of PHP.
  4. name and version of the operating system.
  5. when possible, a small snippet of code that reproduces the issue.

__Think about it__: we do not have a crystal ball and cannot predict things or peer into the unknown
so please provide as much details as possible to help us isolating issues and fix them.

__Never__ use GitHub issues to post generic questions about Predis! When you have questions about
how Predis works or how it can be used, please just hop me an email and I will get back to you as
soon as possible.


## Contributing code ##

If you want to work on Predis, it is highly recommended that you first run the test suite in order
to check that everything is OK and report strange behaviours or bugs. When modifying Predis please
make sure that no warnings or notices are emitted by PHP running the interpreter in your development
environment with the `error_reporting` variable set to `E_ALL | E_STRICT`.

The recommended way to contribute to Predis is to fork the project on GitHub, create topic branches
on your newly created repository to fix bugs or add new features (possibly with tests covering your
modifications) and then open a pull request with a description of the applied changes. Obviously you
can use any other Git hosting provider of your preference.

We always aim for consistency in our code base so you should follow basic coding rules as defined by
[PSR-1](https://github.com/php-fig/fig-standards/blob/master/accepted/PSR-1-basic-coding-standard.md)
and [PSR-2](https://github.com/php-fig/fig-standards/blob/master/accepted/PSR-2-coding-style-guide.md)
and stick with the conventions used in Predis to name classes and interfaces. Indentation should be
done with 4 spaces and code should be wrapped at 100 columns (please try to stay within this limit
even if the above mentioned official coding guidelines set the soft limit to 120 columns).

Please follow these [commit guidelines](http://git-scm.com/book/ch5-2.html#Commit-Guidelines) when
committing your code to Git and always write a meaningful (not necessarily extended) description of
your changes before opening pull requests.
