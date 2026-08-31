.. index::
    single: Installation

Installation
============

Mockery can be installed using Composer or by cloning it from its GitHub
repository. These two options are outlined below.

Composer
--------

You can read more about Composer on `getcomposer.org <https://getcomposer.org>`_.
To install Mockery using Composer, first install Composer for your project
using the instructions on the `Composer download page <https://getcomposer.org/download/>`_.
You can then define your development dependency on Mockery using the suggested
parameters below. While every effort is made to keep the master branch stable,
you may prefer to use the current stable version tag instead (use the
``@stable`` tag).

.. code-block:: json

    {
        "require-dev": {
            "mockery/mockery": "dev-master"
        }
    }

To install, you then may call:

.. code-block:: bash

    php composer.phar update

This will install Mockery as a development dependency, meaning it won't be
installed when using ``php composer.phar update --no-dev`` in production.

Other way to install is directly from composer command line, as below.

.. code-block:: bash

    php composer.phar require --dev mockery/mockery

Git
---

The Git repository hosts the development version in its master branch. You can
install this using Composer by referencing ``dev-master`` as your preferred
version in your project's ``composer.json`` file as the earlier example shows.
