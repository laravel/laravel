Message Headers
===============

Sometimes you'll want to add your own headers to a message or modify/remove
headers that are already present. You work with the message's HeaderSet to do
this.

Header Basics
-------------

All MIME entities in Swift Mailer -- including the message itself --
store their headers in a single object called a HeaderSet. This HeaderSet is
retrieved with the ``getHeaders()`` method.

As mentioned in the previous chapter, everything that forms a part of a message
in Swift Mailer is a MIME entity that is represented by an instance of
``Swift_Mime_MimeEntity``. This includes -- most notably -- the message object
itself, attachments, MIME parts and embedded images. Each of these MIME entities
consists of a body and a set of headers that describe the body.

For all of the "standard" headers in these MIME entities, such as the
``Content-Type``, there are named methods for working with them, such as
``setContentType()`` and ``getContentType()``. This is because headers are a
moderately complex area of the library. Each header has a slightly different
required structure that it must meet in order to comply with the standards that
govern email (and that are checked by spam blockers etc).

You fetch the HeaderSet from a MIME entity like so:

.. code-block:: php

    $message = Swift_Message::newInstance();

    // Fetch the HeaderSet from a Message object
    $headers = $message->getHeaders();

    $attachment = Swift_Attachment::fromPath('document.pdf');

    // Fetch the HeaderSet from an attachment object
    $headers = $attachment->getHeaders();

The job of the HeaderSet is to contain and manage instances of Header objects.
Depending upon the MIME entity the HeaderSet came from, the contents of the
HeaderSet will be different, since an attachment for example has a different
set of headers to those in a message.

You can find out what the HeaderSet contains with a quick loop, dumping out
the names of the headers:

.. code-block:: php

    foreach ($headers->getAll() as $header) {
      printf("%s<br />\n", $header->getFieldName());
    }

    /*
    Content-Transfer-Encoding
    Content-Type
    MIME-Version
    Date
    Message-ID
    From
    Subject
    To
    */

You can also dump out the rendered HeaderSet by calling its ``toString()``
method:

.. code-block:: php

    echo $headers->toString();

    /*
    Message-ID: <1234869991.499a9ee7f1d5e@swift.generated>
    Date: Tue, 17 Feb 2009 22:26:31 +1100
    Subject: Awesome subject!
    From: sender@example.org
    To: recipient@example.org
    MIME-Version: 1.0
    Content-Type: text/plain; charset=utf-8
    Content-Transfer-Encoding: quoted-printable
    */

Where the complexity comes in is when you want to modify an existing header.
This complexity comes from the fact that each header can be of a slightly
different type (such as a Date header, or a header that contains email
addresses, or a header that has key-value parameters on it!). Each header in the
HeaderSet is an instance of ``Swift_Mime_Header``. They all have common
functionality, but knowing exactly what type of header you're working with will
allow you a little more control.

You can determine the type of header by comparing the return value of its
``getFieldType()`` method with the constants ``TYPE_TEXT``,
``TYPE_PARAMETERIZED``, ``TYPE_DATE``, ``TYPE_MAILBOX``, ``TYPE_ID`` and
``TYPE_PATH`` which are defined in ``Swift_Mime_Header``.


.. code-block:: php

    foreach ($headers->getAll() as $header) {
      switch ($header->getFieldType()) {
        case Swift_Mime_Header::TYPE_TEXT: $type = 'text';
          break;
        case Swift_Mime_Header::TYPE_PARAMETERIZED: $type = 'parameterized';
          break;
        case Swift_Mime_Header::TYPE_MAILBOX: $type = 'mailbox';
          break;
        case Swift_Mime_Header::TYPE_DATE: $type = 'date';
          break;
        case Swift_Mime_Header::TYPE_ID: $type = 'ID';
          break;
        case Swift_Mime_Header::TYPE_PATH: $type = 'path';
          break;
      }
      printf("%s: is a %s header<br />\n", $header->getFieldName(), $type);
    }

    /*
    Content-Transfer-Encoding: is a text header
    Content-Type: is a parameterized header
    MIME-Version: is a text header
    Date: is a date header
    Message-ID: is a ID header
    From: is a mailbox header
    Subject: is a text header
    To: is a mailbox header
    */

Headers can be removed from the set, modified within the set, or added to the
set.

The following sections show you how to work with the HeaderSet and explain the
details of each implementation of ``Swift_Mime_Header`` that may
exist within the HeaderSet.

Header Types
------------

Because all headers are modeled on different data (dates, addresses, text!)
there are different types of Header in Swift Mailer. Swift Mailer attempts to
categorize all possible MIME headers into more general groups, defined by a
small number of classes.

Text Headers
~~~~~~~~~~~~

Text headers are the simplest type of Header. They contain textual information
with no special information included within it -- for example the Subject
header in a message.

There's nothing particularly interesting about a text header, though it is
probably the one you'd opt to use if you need to add a custom header to a
message. It represents text just like you'd think it does. If the text
contains characters that are not permitted in a message header (such as new
lines, or non-ascii characters) then the header takes care of encoding the
text so that it can be used.

No header -- including text headers -- in Swift Mailer is vulnerable to
header-injection attacks. Swift Mailer breaks any attempt at header injection by
encoding the dangerous data into a non-dangerous form.

It's easy to add a new text header to a HeaderSet. You do this by calling the
HeaderSet's ``addTextHeader()`` method.

.. code-block:: php

    $message = Swift_Message::newInstance();

    $headers = $message->getHeaders();

    $headers->addTextHeader('Your-Header-Name', 'the header value');

Changing the value of an existing text header is done by calling it's
``setValue()`` method.

.. code-block:: php

    $subject = $message->getHeaders()->get('Subject');

    $subject->setValue('new subject');

When output via ``toString()``, a text header produces something like the
following:

.. code-block:: php

    $subject = $message->getHeaders()->get('Subject');

    $subject->setValue('amazing subject line');

    echo $subject->toString();

    /*

    Subject: amazing subject line

    */

If the header contains any characters that are outside of the US-ASCII range
however, they will be encoded. This is nothing to be concerned about since
mail clients will decode them back.

.. code-block:: php

    $subject = $message->getHeaders()->get('Subject');

    $subject->setValue('contains – dash');

    echo $subject->toString();

    /*

    Subject: contains =?utf-8?Q?=E2=80=93?= dash

    */

Parameterized Headers
~~~~~~~~~~~~~~~~~~~~~

Parameterized headers are text headers that contain key-value parameters
following the textual content. The Content-Type header of a message is a
parameterized header since it contains charset information after the content
type.

The parameterized header type is a special type of text header. It extends the
text header by allowing additional information to follow it. All of the methods
from text headers are available in addition to the methods described here.

Adding a parameterized header to a HeaderSet is done by using the
``addParameterizedHeader()`` method which takes a text value like
``addTextHeader()`` but it also accepts an associative array of
key-value parameters.

.. code-block:: php

    $message = Swift_Message::newInstance();

    $headers = $message->getHeaders();

    $headers->addParameterizedHeader(
      'Header-Name', 'header value',
      array('foo' => 'bar')
      );

To change the text value of the header, call it's ``setValue()`` method just as
you do with text headers.

To change the parameters in the header, call the header's ``setParameters()``
method or the ``setParameter()`` method (note the pluralization).

.. code-block:: php

    $type = $message->getHeaders()->get('Content-Type');

    // setParameters() takes an associative array
    $type->setParameters(array(
      'name' => 'file.txt',
      'charset' => 'iso-8859-1'
      ));

    // setParameter() takes two args for $key and $value
    $type->setParameter('charset', 'iso-8859-1');

When output via ``toString()``, a parameterized header produces something like
the following:

.. code-block:: php

    $type = $message->getHeaders()->get('Content-Type');

    $type->setValue('text/html');
    $type->setParameter('charset', 'utf-8');

    echo $type->toString();

    /*

    Content-Type: text/html; charset=utf-8

    */

If the header contains any characters that are outside of the US-ASCII range
however, they will be encoded, just like they are for text headers. This is
nothing to be concerned about since mail clients will decode them back.
Likewise, if the parameters contain any non-ascii characters they will be
encoded so that they can be transmitted safely.

.. code-block:: php

    $attachment = Swift_Attachment::newInstance();

    $disp = $attachment->getHeaders()->get('Content-Disposition');

    $disp->setValue('attachment');
    $disp->setParameter('filename', 'report–may.pdf');

    echo $disp->toString();

    /*

    Content-Disposition: attachment; filename*=utf-8''report%E2%80%93may.pdf

    */

Date Headers
~~~~~~~~~~~~

Date headers contains an RFC 2822 formatted date (i.e. what PHP's ``date('r')``
returns). They are used anywhere a date or time is needed to be presented as a
message header.

The data on which a date header is modeled is simply a UNIX timestamp such as
that returned by ``time()`` or ``strtotime()``.  The timestamp is used to create
a correctly structured RFC 2822 formatted date such as
``Tue, 17 Feb 2009 22:26:31 +1100``.

The obvious place this header type is used is in the ``Date:`` header of the
message itself.

It's easy to add a new date header to a HeaderSet.  You do this by calling
the HeaderSet's ``addDateHeader()`` method.

.. code-block:: php

    $message = Swift_Message::newInstance();

    $headers = $message->getHeaders();

    $headers->addDateHeader('Your-Header-Name', strtotime('3 days ago'));

Changing the value of an existing date header is done by calling it's
``setTimestamp()`` method.

.. code-block:: php

    $date = $message->getHeaders()->get('Date');

    $date->setTimestamp(time());

When output via ``toString()``, a date header produces something like the
following:

.. code-block:: php

    $date = $message->getHeaders()->get('Date');

    echo $date->toString();

    /*

    Date: Wed, 18 Feb 2009 13:35:02 +1100

    */

Mailbox (e-mail address) Headers
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

Mailbox headers contain one or more email addresses, possibly with
personalized names attached to them. The data on which they are modeled is
represented by an associative array of email addresses and names.

Mailbox headers are probably the most complex header type to understand in
Swift Mailer because they accept their input as an array which can take various
forms, as described in the previous chapter.

All of the headers that contain e-mail addresses in a message -- with the
exception of ``Return-Path:`` which has a stricter syntax -- use this header
type. That is, ``To:``, ``From:`` etc.

You add a new mailbox header to a HeaderSet by calling the HeaderSet's
``addMailboxHeader()`` method.

.. code-block:: php

    $message = Swift_Message::newInstance();

    $headers = $message->getHeaders();

    $headers->addMailboxHeader('Your-Header-Name', array(
      'person1@example.org' => 'Person Name One',
      'person2@example.org',
      'person3@example.org',
      'person4@example.org' => 'Another named person'
      ));

Changing the value of an existing mailbox header is done by calling it's
``setNameAddresses()`` method.

.. code-block:: php

    $to = $message->getHeaders()->get('To');

    $to->setNameAddresses(array(
      'joe@example.org' => 'Joe Bloggs',
      'john@example.org' => 'John Doe',
      'no-name@example.org'
      ));

If you don't wish to concern yourself with the complicated accepted input
formats accepted by ``setNameAddresses()`` as described in the previous chapter
and you only want to set one or more addresses (not names) then you can just
use the ``setAddresses()`` method instead.

.. code-block:: php

    $to = $message->getHeaders()->get('To');

    $to->setAddresses(array(
      'joe@example.org',
      'john@example.org',
      'no-name@example.org'
      ));

.. note::

    Both methods will accept the above input format in practice.

If all you want to do is set a single address in the header, you can use a
string as the input parameter to ``setAddresses()`` and/or
``setNameAddresses()``.

.. code-block:: php

    $to = $message->getHeaders()->get('To');

    $to->setAddresses('joe-bloggs@example.org');

When output via ``toString()``, a mailbox header produces something like the
following:

.. code-block:: php

    $to = $message->getHeaders()->get('To');

    $to->setNameAddresses(array(
      'person1@example.org' => 'Name of Person',
      'person2@example.org',
      'person3@example.org' => 'Another Person'
    ));

    echo $to->toString();

    /*

    To: Name of Person <person1@example.org>, person2@example.org, Another Person
     <person3@example.org>

    */

ID Headers
~~~~~~~~~~

ID headers contain identifiers for the entity (or the message). The most
notable ID header is the Message-ID header on the message itself.

An ID that exists inside an ID header looks more-or-less less like an email
address.  For example, ``<1234955437.499becad62ec2@example.org>``.
The part to the left of the @ sign is usually unique, based on the current time
and some random factor. The part on the right is usually a domain name.

Any ID passed to the header's ``setId()`` method absolutely MUST conform to
this structure, otherwise you'll get an Exception thrown at you by Swift Mailer
(a ``Swift_RfcComplianceException``).  This is to ensure that the generated
email complies with relevant RFC documents and therefore is less likely to be
blocked as spam.

It's easy to add a new ID header to a HeaderSet.  You do this by calling
the HeaderSet's ``addIdHeader()`` method.

.. code-block:: php

    $message = Swift_Message::newInstance();

    $headers = $message->getHeaders();

    $headers->addIdHeader('Your-Header-Name', '123456.unqiue@example.org');

Changing the value of an existing date header is done by calling its
``setId()`` method.

.. code-block:: php

    $msgId = $message->getHeaders()->get('Message-ID');

    $msgId->setId(time() . '.' . uniqid('thing') . '@example.org');

When output via ``toString()``, an ID header produces something like the
following:

.. code-block:: php

    $msgId = $message->getHeaders()->get('Message-ID');

    echo $msgId->toString();

    /*

    Message-ID: <1234955437.499becad62ec2@example.org>

    */

Path Headers
~~~~~~~~~~~~

Path headers are like very-restricted mailbox headers. They contain a single
email address with no associated name. The Return-Path header of a message is
a path header.

You add a new path header to a HeaderSet by calling the HeaderSet's
``addPathHeader()`` method.

.. code-block:: php

    $message = Swift_Message::newInstance();

    $headers = $message->getHeaders();

    $headers->addPathHeader('Your-Header-Name', 'person@example.org');


Changing the value of an existing path header is done by calling its
``setAddress()`` method.

.. code-block:: php

    $return = $message->getHeaders()->get('Return-Path');

    $return->setAddress('my-address@example.org');

When output via ``toString()``, a path header produces something like the
following:

.. code-block:: php

    $return = $message->getHeaders()->get('Return-Path');

    $return->setAddress('person@example.org');

    echo $return->toString();

    /*

    Return-Path: <person@example.org>

    */

Header Operations
-----------------

Working with the headers in a message involves knowing how to use the methods
on the HeaderSet and on the individual Headers within the HeaderSet.

Adding new Headers
~~~~~~~~~~~~~~~~~~

New headers can be added to the HeaderSet by using one of the provided
``add..Header()`` methods.

To add a header to a MIME entity (such as the message):

Get the HeaderSet from the entity by via its ``getHeaders()`` method.

* Add the header to the HeaderSet by calling one of the ``add..Header()``
  methods.

The added header will appear in the message when it is sent.

.. code-block:: php

    // Adding a custom header to a message
    $message = Swift_Message::newInstance();
    $headers = $message->getHeaders();
    $headers->addTextHeader('X-Mine', 'something here');

    // Adding a custom header to an attachment
    $attachment = Swift_Attachment::fromPath('/path/to/doc.pdf');
    $attachment->getHeaders()->addDateHeader('X-Created-Time', time());

Retrieving Headers
~~~~~~~~~~~~~~~~~~

Headers are retrieved through the HeaderSet's ``get()`` and ``getAll()``
methods.

To get a header, or several headers from a MIME entity:

* Get the HeaderSet from the entity by via its ``getHeaders()`` method.

* Get the header(s) from the HeaderSet by calling either ``get()`` or
  ``getAll()``.

When using ``get()`` a single header is returned that matches the name (case
insensitive) that is passed to it. When using ``getAll()`` with a header name,
an array of headers with that name are returned. Calling ``getAll()`` with no
arguments returns an array of all headers present in the entity.

.. note::

    It's valid for some headers to appear more than once in a message (e.g.
    the Received header). For this reason ``getAll()`` exists to fetch all
    headers with a specified name. In addition, ``get()`` accepts an optional
    numerical index, starting from zero to specify which header you want more
    specifically.

.. note::

    If you want to modify the contents of the header and you don't know for
    sure what type of header it is then you may need to check the type by
    calling its ``getFieldType()`` method.

    .. code-block:: php

        $headers = $message->getHeaders();

        // Get the To: header
        $toHeader = $headers->get('To');

        // Get all headers named "X-Foo"
        $fooHeaders = $headers->getAll('X-Foo');

        // Get the second header named "X-Foo"
        $foo = $headers->get('X-Foo', 1);

        // Get all headers that are present
        $all = $headers->getAll();

Check if a Header Exists
~~~~~~~~~~~~~~~~~~~~~~~~

You can check if a named header is present in a HeaderSet by calling its
``has()`` method.

To check if a header exists:

* Get the HeaderSet from the entity by via its ``getHeaders()`` method.

* Call the HeaderSet's ``has()`` method specifying the header you're looking
  for.

If the header exists, ``true`` will be returned or ``false`` if not.

.. note::

    It's valid for some headers to appear more than once in a message (e.g.
    the Received header). For this reason ``has()`` accepts an optional
    numerical index, starting from zero to specify which header you want to
    check more specifically.

    .. code-block:: php

        $headers = $message->getHeaders();

        // Check if the To: header exists
        if ($headers->has('To')) {
          echo 'To: exists';
        }

        // Check if an X-Foo header exists twice (i.e. check for the 2nd one)
        if ($headers->has('X-Foo', 1)) {
          echo 'Second X-Foo header exists';
        }

Removing Headers
~~~~~~~~~~~~~~~~

Removing a Header from the HeaderSet is done by calling the HeaderSet's
``remove()`` or ``removeAll()`` methods.

To remove an existing header:

* Get the HeaderSet from the entity by via its ``getHeaders()`` method.

* Call the HeaderSet's ``remove()`` or ``removeAll()`` methods specifying the
  header you want to remove.

When calling ``remove()`` a single header will be removed. When calling
``removeAll()`` all headers with the given name will be removed. If no headers
exist with the given name, no errors will occur.

.. note::

    It's valid for some headers to appear more than once in a message (e.g.
    the Received header). For this reason ``remove()`` accepts an optional
    numerical index, starting from zero to specify which header you want to
    check more specifically. For the same reason, ``removeAll()`` exists to
    remove all headers that have the given name.

    .. code-block:: php

        $headers = $message->getHeaders();

        // Remove the Subject: header
        $headers->remove('Subject');

        // Remove all X-Foo headers
        $headers->removeAll('X-Foo');

        // Remove only the second X-Foo header
        $headers->remove('X-Foo', 1);

Modifying a Header's Content
~~~~~~~~~~~~~~~~~~~~~~~~~~~~

To change a Header's content you should know what type of header it is and then
call it's appropriate setter method. All headers also have a
``setFieldBodyModel()`` method that accepts a mixed parameter and delegates to
the correct setter.

To modify an existing header:

* Get the HeaderSet from the entity by via its ``getHeaders()`` method.

* Get the Header by using the HeaderSet's ``get()``.

* Call the Header's appropriate setter method or call the header's
  ``setFieldBodyModel()`` method.

The header will be updated inside the HeaderSet and the changes will be seen
when the message is sent.

.. code-block:: php

    $headers = $message->getHeaders();

    // Change the Subject: header
    $subj = $headers->get('Subject');
    $subj->setValue('new subject here');

    // Change the To: header
    $to = $headers->get('To');
    $to->setNameAddresses(array(
      'person@example.org' => 'Person',
      'thing@example.org'
    ));

    // Using the setFieldBodyModel() just delegates to the correct method
    // So here to calls setNameAddresses()
    $to->setFieldBodyModel(array(
      'person@example.org' => 'Person',
      'thing@example.org'
    ));
