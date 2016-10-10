Introduction
============

Swift Mailer is a component-based library for sending e-mails from PHP
applications.

Organization of this Book
-------------------------

This book has been written so that those who need information quickly are able
to find what they need, and those who wish to learn more advanced topics can
read deeper into each chapter.

The book begins with an overview of Swift Mailer, discussing what's included
in the package and preparing you for the remainder of the book.

It is possible to read this user guide just like any other book (from
beginning to end). Each chapter begins with a discussion of the contents it
contains, followed by a short code sample designed to give you a head start.
As you get further into a chapter you will learn more about Swift Mailer's
capabilities, but often you will be able to head directly to the topic you
wish to learn about.

Throughout this book you will be presented with code samples, which most
people should find ample to implement Swift Mailer appropriately in their own
projects. We will also use diagrams where appropriate, and where we believe
readers may find it helpful we will discuss some related theory, including
reference to certain documents you are able to find online.

Code Samples
------------

Code samples presented in this book will be displayed on a different colored
background in a monospaced font. Samples are not to be taken as copy & paste
code snippets.

Code examples are used through the book to clarify what is written in text.
They will sometimes be usable as-is, but they should always be taken as
outline/pseudo code only.

A code sample will look like this::

    class AClass
    {
      ...
    }

    // A Comment
    $obj = new AClass($arg1, $arg2, ... );

    /* A note about another way of doing something
    $obj = AClass::newInstance($arg1, $arg2, ... );

    */

The presence of 3 dots ``...`` in a code sample indicates that we have left
out a chunk of the code for brevity, they are not actually part of the code.

We will often place multi-line comments ``/* ... */`` in the code so that we
can show alternative ways of achieving the same result.

You should read the code examples given and try to understand them. They are
kept concise so that you are not overwhelmed with information.

History of Swift Mailer
-----------------------

Swift Mailer began back in 2005 as a one-class project for sending mail over
SMTP. It has since grown into the flexible component-based library that is in
development today.

Chris Corbyn first posted Swift Mailer on a web forum asking for comments from
other developers. It was never intended as a fully supported open source
project, but members of the forum began to adopt it and make use of it.

Very quickly feature requests were coming for the ability to add attachments
and use SMTP authentication, along with a number of other "obvious" missing
features. Considering the only alternative was PHPMailer it seemed like a good
time to bring some fresh tools to the table. Chris began working towards a
more component based, PHP5-like approach unlike the existing single-class,
legacy PHP4 approach taken by PHPMailer.

Members of the forum offered a lot of advice and critique on the code as he
worked through this project and released versions 2 and 3 of the library in
2005 and 2006, which by then had been broken down into smaller classes
offering more flexibility and supporting plugins. To this day the Swift Mailer
team still receive a lot of feature requests from users both on the forum and
in by email.

Until 2008 Chris was the sole developer of Swift Mailer, but entering 2009 he
gained the support of two experienced developers well-known to him: Paul
Annesley and Christopher Thompson. This has been an extremely welcome change.

As of September 2009, Chris handed over the maintenance of Swift Mailer to
Fabien Potencier.

Now 2009 and in its fourth major version Swift Mailer is more object-oriented
and flexible than ever, both from a usability standpoint and from a
development standpoint.

By no means is Swift Mailer ready to call "finished". There are still many
features that can be added to the library along with the constant refactoring
that happens behind the scenes.

It's a Library!
---------------

Swift Mailer is not an application - it's a library.

To most experienced developers this is probably an obvious point to make, but
it's certainly worth mentioning. Many people often contact us having gotten
the completely wrong end of the stick in terms of what Swift Mailer is
actually for.

It's not an application. It does not have a graphical user interface. It
cannot be opened in your web browser directly.

It's a library (or a framework if you like). It provides a whole lot of
classes that do some very complicated things, so that you don't have to. You
"use" Swift Mailer within an application so that your application can have the
ability to send emails.

The component-based structure of the library means that you are free to
implement it in a number of different ways and that you can pick and choose
what you want to use.

An application on the other hand (such as a blog or a forum) is already "put
together" in a particular way, (usually) provides a graphical user interface
and most likely doesn't offer a great deal of integration with your own
application.

Embrace the structure of the library and use the components it offers to your
advantage. Learning what the components do, rather than blindly copying and
pasting existing code will put you in a great position to build a powerful
application!
