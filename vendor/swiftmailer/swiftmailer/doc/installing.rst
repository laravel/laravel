Installing the Library
======================

Installing with Composer
------------------------

The recommended way to install Swiftmailer is via Composer:

.. code-block:: bash

    $ php composer.phar require swiftmailer/swiftmailer @stable

Installing from Git
-------------------

It's possible to download and install Swift Mailer directly from github.com if
you want to keep up-to-date with ease.

Swift Mailer's source code is kept in a git repository at github.com so you
can get the source directly from the repository.

.. note::

    You do not need to have git installed to use Swift Mailer from GitHub. If
    you don't have git installed, go to `GitHub`_ and click the "Download"
    button.

Cloning the Repository
~~~~~~~~~~~~~~~~~~~~~~

The repository can be cloned from git://github.com/swiftmailer/swiftmailer.git
using the ``git clone`` command.

You will need to have ``git`` installed before you can use the
``git clone`` command.

To clone the repository:

* Open your favorite terminal environment (command line).

* Move to the directory you want to clone to.

* Run the command ``git clone git://github.com/swiftmailer/swiftmailer.git
  swiftmailer``.

The source code will be downloaded into a directory called "swiftmailer".

The example shows the process on a UNIX-like system such as Linux, BSD or Mac
OS X.

.. code-block:: bash

    $ cd source_code/
    $ git clone git://github.com/swiftmailer/swiftmailer.git swiftmailer
    Initialized empty Git repository in /Users/chris/source_code/swiftmailer/.git/
    remote: Counting objects: 6815, done.
    remote: Compressing objects: 100% (2761/2761), done.
    remote: Total 6815 (delta 3641), reused 6326 (delta 3286)
    Receiving objects: 100% (6815/6815), 4.35 MiB | 162 KiB/s, done.
    Resolving deltas: 100% (3641/3641), done.
    Checking out files: 100% (1847/1847), done.
    $ cd swiftmailer/
    $ ls
    CHANGES LICENSE ...
    $

Troubleshooting
---------------

Swift Mailer does not work when used with function overloading as implemented
by ``mbstring`` (``mbstring.func_overload`` set to ``2``). A workaround is to
temporarily change the internal encoding to ``ASCII`` when sending an email:

.. code-block:: php

    if (function_exists('mb_internal_encoding') && ((int) ini_get('mbstring.func_overload')) & 2)
    {
      $mbEncoding = mb_internal_encoding();
      mb_internal_encoding('ASCII');
    }

    // Create your message and send it with Swift Mailer

    if (isset($mbEncoding))
    {
      mb_internal_encoding($mbEncoding);
    }

.. _`GitHub`: http://github.com/swiftmailer/swiftmailer
