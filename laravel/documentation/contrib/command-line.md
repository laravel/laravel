# Command Line

## Contents
 - [Getting Started](#getting-started)
 - [Forking Laravel](#forking-laravel)
 - [Cloning Laravel](#cloning-laravel)
 - [Creating Branches](#creating-branches)
 - [Committing](#committing)
 - [Submitting a Pull Request](#submitting-a-pull-request)
 - [What's Next?](#whats-next)

<a name='getting-started'></a>
## Getting Started

This tutorial explains the basics of contributing to a project on [GitHub](https://github.com/). The workflow can apply to most projects on GitHub, but in this case, we will be focused on the [Laravel](https://github.com/laravel/laravel) project.

This tutorial assumes that you have installed [Git](http://git-scm.com/) and that you have created a [GitHub account](https://github.com/signup/free). If you haven't already, look at the documentation for [Laravel on GitHub](/docs/contrib/github) in order to familiarize yourself with Laravel's repositories and branches.

*[Jason Lewis](http://jasonlewis.me/)'s blog post on [Contributing to a GitHub Project](http://jasonlewis.me/blog/2012/06/how-to-contributing-to-a-github-project) was the inspiration for this tutorial.*

<a name='forking-laravel'></a>
## Forking Laravel

Login to GitHub and visit the [Laravel Repository](https://github.com/laravel/laravel). Click on the **Fork** button. This will create your own fork of Laravel in your own GitHub account. Your Laravel fork will be located at **https://github.com/username/laravel** (your GitHub username will be used in place of *username*).

<a name='cloning-laravel'></a>
## Cloning Laravel

Open up the command-line or terminal and make a new directory where you can make development changes to Laravel:

	# mkdir laravel-develop
	# cd laravel-develop

Next, clone the Laravel repository (not your fork you made):

	# git clone https://github.com/laravel/laravel.git .

The reason you are cloning the original Laravel repository (and not the fork you made) is so you can use it to always have the latest Laravel development code. If you simply clone the fork you made, you would have to re-fork the Laravel repository again every time you want the latest changes to Laravel development code.

Next, it's time to add the fork you made as a **remote repository**:

	# git remote add fork git@github.com:username/laravel.git

Remember to replace **username** with your GitHub username. *This is case-sensitive*. You can verify that your fork was added by typing:

	# git remote

Now you have a pristine clone of the Laravel repository along with your fork as a remote repository. You are ready to begin branching for new features or fixing bugs.

<a name='creating-branches'></a>
## Creating Branches

First, make sure you are working in the **develop** branch. If you submit changes to the **master** branch, it is unlikely they will be pulled in anytime in the near future. For more information on this, read the documentation for [Laravel on GitHub](/docs/contrib/github). To switch to the develop branch:

	# git checkout develop

Next, you want to make sure you are up-to-date with the latest Laravel repository. If any new features or bug fixes have been added to the Laravel project since you cloned it, this will ensure that your local repository has all of those changes. This important step is the reason we originally cloned the Laravel repository instead of your own fork.

	# git pull origin develop

Now you are ready to create a new branch.

> **Create a new branch for every new feature or bug-fix.** Why? Let's say you make a new branch and add 5 different new features and fix 12 bugs. If one or more of your new features or bug-fixes are rejected by the Laravel team (for whatever reason) your entire pull request will be ignored, even if most of your new features or bug-fixes are perfect. On top of this, separate branches will encourage organization and limit interdependency between new features and bug-fixes that you add.

When you create a new branch, use a self-descriptive naming convention. For example, if there is a bug in Eloquent that you want to fix, name your branch accordingly:

	# git branch bug/eloquent
	# git checkout bug/eloquent
	Switched to branch 'bug/eloquent'

Or if there is a new feature or change to the documentation that you want to make, for example, the localization documentation:

	# git branch feature/localization-docs
	# git checkout feature/localization-docs
	Switched to branch 'feature/localization-docs'

Now that you have created your own branch and have switched to it, it's time to make your changes to the code. Add your new feature or fix that bug.

<a name='committing'></a>
## Committing

Now that you have finished coding and testing your changes, it's time to commit them to your local repository. First, add the files that you changed/added:

	# git add laravel/documentation/localization.md

Next, commit the changes to the repository:

	# git commit -s -m "I added some more stuff to the Localization documentation."

 - **-s** means that you are signing-off on your commit with your name. This tells the Laravel team know that you personally agree to your code being added to the Laravel core.
 - **-m** is the message that goes with your commit. Provide a brief explanation of what you added or changed.

<a name='pushing-to-your-fork'></a>
## Pushing to your Fork

Now that your local repository has your committed changes, it's time to push (or sync) your new branch to your fork that is hosted in GitHub:

	# git push fork feature/localization-docs

<a name='submitting-a-pull-request'></a>
## Submitting a Pull Request

The final step is to submit a pull request to the Laravel repository. This means that you are requesting that the Laravel team pull and merge your changes to the Laravel core. In your browser, visit your Laravel fork at [https://github.com/username/laravel](https://github.com/username/laravel). Click on **Pull Request**. Next, make sure you choose the proper base and head repositories and branches:

 - **base repo:** laravel/laravel
 - **base branch:** develop
 - **head repo:** username/laravel
 - **head branch:** feature/localization-docs

Use the form to write a more detailed description of the changes you made and why you made them. Finally, click **Send pull request**. That's it! The changes you made have been submitted to the Laravel team.

<a name='whats-next'></a>
## What's Next?

Do you have another feature you want to add or another bug you need to fix? First, make sure you always base your new branch off of the develop branch:

	# git checkout develop

Then, pull down the latest changes from Laravel's repository:

	# git pull origin develop

Now you are ready to create a new branch and start coding again!
