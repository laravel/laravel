# Laravel on GitHub

## Contents

- [The Basics](#the-basics)
- [Repositories](#repositories)
- [Branches](#branches)
- [Pull Requests](#pull-requests)

<a name="the-basics"></a>
## The Basics

Because Laravel's development and source control is done through GitHub, anyone is able to make contributions to it. Anyone can fix bugs, add features or improve the documentation.

After submitting proposed changes to the project, the Laravel team will review the changes and make the decision to commit them to Laravel's core.

<a name="repositories"></a>
## Repositories

Laravel's home on GitHub is at [github.com/laravel](https://github.com/laravel). Laravel has several repositories. For basic contributions, the only repository you need to pay attention to is the **laravel** repository, located at [github.com/laravel/laravel](https://github.com/laravel/laravel).

<a name="branches"></a>
## Branches

The **laravel** repository has multiple branches, each serving a specific purpose:

- **master** - This is the Laravel release branch. Active development does not happen on this branch. This branch is only for the most recent, stable Laravel core code. When you download Laravel from [laravel.com](http://laravel.com/), you are downloading directly from this master branch. *Do not make pull requests to this branch.*
- **develop** - This is the working development branch. All proposed code changes and contributions by the community are pulled into this branch. *When you make a pull request to the Laravel project, this is the branch you want to pull-request into.*

Once certain milestones have been reached and/or Taylor Otwell and the Laravel team is happy with the stability and additional features of the current development branch, the changes in the **develop** branch are pulled into the **master** branch, thus creating and releasing the newest stable version of Laravel for the world to use.

<a name="pull-requests"></a>
## Pull Requests

[GitHub pull requests](https://help.github.com/articles/using-pull-requests) are a great way for everyone in the community to contribute to the Laravel codebase. Found a bug? Just fix it in your fork and submit a pull request. This will then be reviewed, and, if found as good, merged into the main repository.

In order to keep the codebase clean, stable and at high quality, even with so many people contributing, some guidelines are necessary for high-quality pull requests:

- **Branch:** Unless they are immediate documentation fixes relevant for old versions, pull requests should be sent to the `develop` branch only. Make sure to select that branch as target when creating the pull request (GitHub will not automatically select it.)
- **Documentation:** If you are adding a new feature or changing the API in any relevant way, this should be documented. The documentation files can be found directly in the core repository.
- **Unit tests:** To keep old bugs from re-appearing and generally hold quality at a high level, the Laravel core is thoroughly unit-tested. Thus, when you create a pull request, it is expected that you unit test any new code you add. For any bug you fix, you should also add regression tests to make sure the bug will never appear again. If you are unsure about how to write tests, the core team or other contributors will gladly help.

*Further Reading*

 - [Contributing to Laravel via Command-Line](/docs/contrib/command-line)
 - [Contributing to Laravel using TortoiseGit](/docs/contrib/tortoisegit)
