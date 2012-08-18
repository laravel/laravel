# Laravel on GitHub

## Contents

- [The Basics](#the-basics)
- [Repositories](#repositoriess)
- [Branches](#branches)

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
- **staging** - I'm not sure what this is for... Last minute testing before pushing develop to master?
- **develop** - This is the working development branch. All proposed code changes and contributions by the community are pulled into this branch. *When you make a pull request to the Laravel project, this is the branch you want to pull-request into.*

Once certain milestones have been reached and/or Taylor Otwell and the Laravel team is happy with the stability and additional features of the current development branch, the changes in the **develop** branch are pulled into the **master** branch, thus creating and releasing the newest stable version of Laravel for the world to use.

*Further Reading*

 - [Contributing to Laravel via Command-Line](docs/contrib/command-line)
 - [Contributing to Laravel using TortoiseGit](docs/contrib/tortoisegit)
