# Contributing to Laravel using TortoiseGit

## Contents

- [Getting Started](#getting-started)
- [Forking Laravel](#forking-laravel)
- [Cloning Laravel](#cloning-laravel)
- [Adding your Fork](#adding-your-fork)
- [Creating Branches](#creating-branches)
- [Committing](#committing)
- [Submitting a Pull Request](#submitting-a-pull-request)
- [What's Next?](#whats-next)

<a name="getting-started"></a>
## Getting Started

This tutorial explains the basics of contributing to a project on [GitHub](https://github.com/) using [TortoiseGit](http://code.google.com/p/tortoisegit/) for Windows. The workflow can apply to most projects on GitHub, but in this case, we will be focused on the [Laravel](https://github.com/laravel/laravel) project.

This tutorial assumes you have installed TortoiseGit for Windows and you have created a GitHub account. If you haven't already, look at the [Laravel on GitHub](/docs/contrib/github) documentation in order to familiarize yourself with Laravel's repositories and branches.

<a name="forking-laravel"></a>
## Forking Laravel

Login to GitHub and visit the [Laravel Repository](https://github.com/laravel/laravel). Click on the **Fork** button. This will create your own fork of Laravel in your own GitHub account. Your Laravel fork will be located at **https://github.com/username/laravel** (your GitHub username will be used in place of *username*).

<a name="cloning-laravel"></a>
## Cloning Laravel

Open up Windows Explorer and create a new directory where you can make development changes to Laravel.

- Right-click the Laravel directory to bring up the context menu. Click on **Git Clone...**
- Git clone
  - **Url:** https://github.com/laravel/laravel.git
  - **Directory:** the directory that you just created in the previous step
  - Click **OK**

> **Note**: The reason you are cloning the original Laravel repository (and not the fork you made) is so you can always pull down the most recent changes from the Laravel repository to your local repository.

<a name="adding-your-fork"></a>
## Adding your Fork

After the cloning process is complete, it's time to add the fork you made as a **remote repository**.

- Right-click the Laravel directory and goto **TortoiseGit > Settings**
- Goto the **Git/Remote** section. Add a new remote:
  - **Remote**: fork
  - **URL**: https://github.com/username/laravel.git
  - Click **Add New/Save**
  - Click **OK**

Remember to replace *username* with your GitHub username. *This is case-sensitive*.

<a name="creating-branches"></a>
## Creating Branches

Now you are ready to create a new branch for your new feature or bug-fix. When you create a new branch, use a self-descriptive naming convention. For example, if you are going to fix a bug in Eloquent, name your branch *bug/eloquent*. Or if you were going to make changes to the localization documentation, name your branch *feature/localization-docs*. A good naming convention will encourage organization and help others understand the purpose of your branch.

- Right-click the Laravel directory and goto **TortoiseGit > Create Branch**
  - **Branch:** feature/localization-docs
  - **Base On Branch:** remotes/origin/develop
  - **Check** *Track*
  - **Check** *Switch to new branch*
  - Click **OK**

This will create your new *feature/localization-docs* branch and switch you to it.

> **Note:** Create one new branch for every new feature or bug-fix. This will encourage organization, limit interdependency between new features/fixes and will make it easy for the Laravel team to merge your changes into the Laravel core.

Now that you have created your own branch and have switched to it, it's time to make your changes to the code. Add your new feature or fix that bug.

<a name="committing"></a>
##Committing

Now that you have finished coding and testing your changes, it's time to commit them to your local repository:

-  Right-click the Laravel directory and goto **Git Commit -> "feature/localization-docs"...**
- Commit
  - **Message:** Provide a brief explaination of what you added or changed
  - Click **Sign** - This tells the Laravel team know that you personally agree to your code being added to the Laravel core
  - **Changes made:** Check all changed/added files
  - Click **OK**

<a name="pushing-to-your-fork"></a>
## Pushing to your Fork

Now that your local repository has your committed changes, it's time to push (or sync) your new branch to your fork that is hosted in GitHub:

- Right-click the Laravel directory and goto **Git Sync...**
- Git Syncronization
  - **Local Branch:** feature/localization-docs
  - **Remote Branch:** leave this blank
  - **Remote URL:** fork
  - Click **Push**
  - When asked for "username:" enter your GitHub *case-sensitive* username
  - When asked for "password:" enter your GitHub *case-sensitive* account

Your branch has been successfully pushed to your fork on GitHub.

<a name="submitting-a-pull-request"></a>
## Submitting a Pull Request

The final step is to submit a pull request to the Laravel repository. This means that you are requesting that the Laravel team pull and merge your changes to the Laravel core. In your browser, visit your Laravel fork at [https://github.com/username/laravel](https://github.com/username/laravel). Click on **Pull Request**. Next, make sure you choose the proper base and head repositories and branches:

- **base repo:** laravel/laravel
- **base branch:** develop
- **head repo:** username/laravel
- **head branch:** feature/localization-docs

Use the form to write a more detailed description of the changes you made and why you made them. Finally, click **Send pull request**. That's it! The changes you made have been submitted to the Laravel team.

<a name="whats-next"></a>
## What's Next?

Do you have another feature you want to add or another bug you need to fix? Just follow the same instructions as before in the [Creating Branches](#creating-branches) section. Just remember to always create a new branch for every new feature/fix and don't forget to always base your new branches off of the *remotes/origin/develop* branch.
