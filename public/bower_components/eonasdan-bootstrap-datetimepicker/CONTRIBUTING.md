Submitting Issues
=================

If you are submitting a bug, please test and/or fork [this jsfiddle](http://jsfiddle.net/Eonasdan/0Ltv25o8/) demonstrating the issue. Code issues and fringe case bugs that do not include a jsfiddle (or similar) will be closed.

Issues that are submitted without a description (title only) will be closed with no further explanation.

Contributing code
=================

To contribute, fork the library and install grunt and dependencies. You need [node](http://nodejs.org/); use [nvm](https://github.com/creationix/nvm) or [nenv](https://github.com/ryuone/nenv) to install it.

```bash
git clone https://github.com/Eonasdan/bootstrap-datetimepicker.git
cd bootstrap-datetimepicker
npm install -g grunt-cli
npm install
git checkout development  # all patches against development branch, please!
grunt                 # this runs tests and jshint
```

Very important notes
====================

 * **Pull requests to the `master` branch will be closed.** Please submit all pull requests to the `development` branch.
 * **Do not include the minified files in your pull request.** Don't worry, we'll build them when we cut a release.
 * Pull requests that do not include a description (title only) and the following will be closed:
  * What the change does
  * A use case (for new features or enhancements)

Grunt tasks
===========

We use Grunt for managing the build. Here are some useful Grunt tasks:

  * `grunt` The default task lints the code and runs the tests. You should make sure you do this before submitting a PR.
  * `grunt build` Compiles the less stylesheet and minifies the javascript source in build directory.
  * `grunt build:travis` Compliles and runs the jasmine/travis tests. **All PR's MUST pass tests in place**