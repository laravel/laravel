Library Overview
================

Most features (and more) of your every day mail client software are provided
by Swift Mailer, using object-oriented PHP code as the interface.

In this chapter we will take a short tour of the various components, which put
together form the Swift Mailer library as a whole. You will learn key
terminology used throughout the rest of this book and you will gain a little
understanding of the classes you will work with as you integrate Swift Mailer
into your application.

This chapter is intended to prepare you for the information contained in the
subsequent chapters of this book. You may choose to skip this chapter if you
are fairly technically minded, though it is likely to save you some time in
the long run if you at least read between the lines here.

System Requirements
-------------------

The basic requirements to operate Swift Mailer are extremely minimal and
easily achieved. Historically, Swift Mailer has supported both PHP 4 and PHP 5
by following a parallel development workflow. Now in it's fourth major
version, and Swift Mailer operates on servers running PHP 5.2 or higher.

The library aims to work with as many PHP 5 projects as possible:

* PHP 5.2 or higher, with the SPL extension (standard)

* Limited network access to connect to remote SMTP servers

* 8 MB or more memory limit (Swift Mailer uses around 2 MB)

Component Breakdown
-------------------

Swift Mailer is made up of many classes. Each of these classes can be grouped
into a general "component" group which describes the task it is designed to
perform.

We'll take a brief look at the components which form Swift Mailer in this
section of the book.

The Mailer
~~~~~~~~~~

The mailer class, ``Swift_Mailer`` is the central class in the library where
all of the other components meet one another. ``Swift_Mailer`` acts as a sort
of message dispatcher, communicating with the underlying Transport to deliver
your Message to all intended recipients.

If you were to dig around in the source code for Swift Mailer you'd notice
that ``Swift_Mailer`` itself is pretty bare. It delegates to other objects for
most tasks and in theory, if you knew the internals of Swift Mailer well you
could by-pass this class entirely. We wouldn't advise doing such a thing
however -- there are reasons this class exists:

* for consistency, regardless of the Transport used

* to provide abstraction from the internals in the event internal API changes
  are made

* to provide convenience wrappers around aspects of the internal API

An instance of ``Swift_Mailer`` is created by the developer before sending any
Messages.

Transports
~~~~~~~~~~

Transports are the classes in Swift Mailer that are responsible for
communicating with a service in order to deliver a Message. There are several
types of Transport in Swift Mailer, all of which implement the Swift_Transport
interface and offer underlying start(), stop() and send() methods.

Typically you will not need to know how a Transport works under-the-surface,
you will only need to know how to create an instance of one, and which one to
use for your environment.

+---------------------------------+---------------------------------------------------------------------------------------------+-----------------------------------------------------------------------------------------------------------------------------------------------+
| Class                           | Features                                                                                    | Pros/cons                                                                                                                                     |
+=================================+=============================================================================================+===============================================================================================================================================+
| ``Swift_SmtpTransport``         | Sends messages over SMTP; Supports Authentication; Supports Encryption                      | Very portable; Pleasingly predictable results; Provides good feedback                                                                         |
+---------------------------------+---------------------------------------------------------------------------------------------+-----------------------------------------------------------------------------------------------------------------------------------------------+
| ``Swift_SendmailTransport``     | Communicates with a locally installed ``sendmail`` executable (Linux/UNIX)                  | Quick time-to-run;  Provides less-accurate feedback than SMTP; Requires ``sendmail`` installation                                             |
+---------------------------------+---------------------------------------------------------------------------------------------+-----------------------------------------------------------------------------------------------------------------------------------------------+
| ``Swift_MailTransport``         | Uses PHP's built-in ``mail()`` function                                                     | Very portable; Potentially unpredictable results; Provides extremely weak feedback                                                            |
+---------------------------------+---------------------------------------------------------------------------------------------+-----------------------------------------------------------------------------------------------------------------------------------------------+
| ``Swift_LoadBalancedTransport`` | Cycles through a collection of the other Transports to manage load-reduction                | Provides graceful fallback if one Transport fails (e.g. an SMTP server is down); Keeps the load on remote services down by spreading the work |
+---------------------------------+---------------------------------------------------------------------------------------------+-----------------------------------------------------------------------------------------------------------------------------------------------+
| ``Swift_FailoverTransport``     | Works in conjunction with a collection of the other Transports to provide high-availability | Provides graceful fallback if one Transport fails (e.g. an SMTP server is down)                                                               |
+---------------------------------+---------------------------------------------------------------------------------------------+-----------------------------------------------------------------------------------------------------------------------------------------------+

MIME Entities
~~~~~~~~~~~~~

Everything that forms part of a Message is called a MIME Entity. All MIME
entities in Swift Mailer share a common set of features. There are various
types of MIME entity that serve different purposes such as Attachments and
MIME parts.

An e-mail message is made up of several relatively simple entities that are
combined in different ways to achieve different results. All of these entities
have the same fundamental outline but serve a different purpose. The Message
itself can be defined as a MIME entity, an Attachment is a MIME entity, all
MIME parts are MIME entities -- and so on!

The basic units of each MIME entity -- be it the Message itself, or an
Attachment -- are its Headers and its body:

.. code-block:: text

    Other-Header: Another value

    The body content itself

The Headers of a MIME entity, and its body must conform to some strict
standards defined by various RFC documents. Swift Mailer ensures that these
specifications are followed by using various types of object, including
Encoders and different Header types to generate the entity.

Each MIME component implements the base ``Swift_Mime_MimeEntity`` interface,
which offers methods for retrieving Headers, adding new Headers, changing the
Encoder, updating the body and so on!

All MIME entities have one Header in common -- the Content-Type Header,
updated with the entity's ``setContentType()`` method.

Encoders
~~~~~~~~

Encoders are used to transform the content of Messages generated in Swift
Mailer into a format that is safe to send across the internet and that
conforms to RFC specifications.

Generally speaking you will not need to interact with the Encoders in Swift
Mailer -- the correct settings will be handled by the library itself.
However they are probably worth a brief mention in the event that you do want
to play with them.

Both the Headers and the body of all MIME entities (including the Message
itself) use Encoders to ensure the data they contain can be sent over the
internet without becoming corrupted or misinterpreted.

There are two types of Encoder: Base64 and Quoted-Printable.

Plugins
~~~~~~~

Plugins exist to extend, or modify the behaviour of Swift Mailer. They respond
to Events that are fired within the Transports during sending.

There are a number of Plugins provided as part of the base Swift Mailer
package and they all follow a common interface to respond to Events fired
within the library. Interfaces are provided to "listen" to each type of Event
fired and to act as desired when a listened-to Event occurs.

Although several plugins are provided with Swift Mailer out-of-the-box, the
Events system has been specifically designed to make it easy for experienced
object-oriented developers to write their own plugins in order to achieve
goals that may not be possible with the base library.
