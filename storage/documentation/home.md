# Laravel Documentation

- [The Basics](#the-basics)
- [Who Will Enjoy Laravel?](#who-will-enjoy-laravel)
- [What Makes Laravel Different?](#laravel-is-different)
- [Application Structure](#application-structure)
- [Laravel's Community](#laravel-community)
- [License Information](#laravel-license)

<a name="the-basics"></a>
## The Basics

Welcome to the Laravel documentation. These documents were designed to function both as a getting-started guide and as a feature reference. Even though you may jump into any section and start learning, we recommend reading the documentation in order as it allows us to progressively establish concepts that will be used in later documents. 

<a name="who-will-enjoy-laravel"></a>
## Who Will Enjoy Laravel?

Laravel is a powerful framework that emphasizes flexibility and expressiveness. Users new to Laravel will enjoy the same ease of development that is found in the most popular and lightweight PHP frameworks. More experienced users will appreciate the opportunity to modularize their code in ways that are not possible with other frameworks. Laravel's flexibility will allow your organization to update and mold the application over time as is needed and its expressiveness will allow you and your team to develop code that is both concise and easily read.


<a name="laravel-is-different"></a>
## What Makes Laravel Different?

There are many ways in which Laravel differentiates itself from other frameworks. Here are a few examples that we think make good bullet points:

- **Bundles** are Laravel's modular packaging system. [The Laravel Bundle Repository](http://bundles.laravel.com/) is already populated with quite a few features that can be easily added to your application. You can either download a bundle repository to your bundles directory or use the "Artisan" command-line tool to automatically install them.
- **The Eloquent ORM** is the most advanced PHP ActiveRecord implementation available.  With the capacity to easily apply constraints to both relationships and nested eager-loading you'll have complete control over your data with all of the conveniences of ActiveRecord.  Eloquent natively supports all of the methods from Laravel's Fluent query-builder.
- **Application Logic** can be implemented within your application either using controllers (which many web-developers are already familiar with) or directly into route declarations using syntax similar to the Sinatra framework. Laravel is designed with the philosophy of giving a developer the flexibility that they need to create everything from very small sites to massive enterprise applications.
- **Reverse Routing** allows you to create links to named routes. When creating links just use the route's name and Laravel will automatically insert the correct URI.  This allows you to change your routes at a later time and Laravel will update all of the relevant links site-wide.
- **Restful Controllers** are an optional way to separate your GET and POST request logic. In a login example your controller's get_login() action would serve up the form and your controller's post_login() action would accept the posted form, validate, and either redirect to the login form with an error message or redirect your user to their dashboard.
- **Class Auto Loading** keeps you from having to maintain an autoloader configuration and from loading unnecessary components when they won't be used. Want to use a library or model?  Don't bother loading it, just use it. Laravel will handle the rest.
- **View Composers** are blocks of code that can be run when a view is loaded. A good example of this would be a blog side-navigation view that contains a list of random blog posts. Your composer would contain the logic to load the blog posts so that all you have to do i load the view and it's all ready for you. This keeps you from having to make sure that your controllers load the a bunch of data from your models for views that are unrelated to that method's page content.
- **The IoC container** (Inversion of Control) gives you a method for generating new objects and optionally instantiating and referencing singletons. IoC means that you'll rarely ever need to bootstrap any external libraries. It also means that you can access these objects from anywhere in your code without needing to deal with an inflexible monolithic structure. 
- **Migrations** are version control for your database schemas and they are directly integrated into Laravel. You can both generate and run migrations using the "Artisan" command-line utility. Once another member makes schema changes you can update your local copy from the repository and run migrations. Now you're up to date, too!
- **Unit-Testing** is an important part of Laravel. Laravel itself sports hundreds of tests to help ensure that new changes don't unexpectedly break anything. This is one of the reasons why Laravel is widely considered to have some of the most stable releases in the industry.  Laravel also makes it easy for you to write unit-tests for your own code.  You can then run tests with the "Artisan" command-line utility.
- **Automatic Pagination** prevents your application logic from being cluttered up with a bunch of pagination configuration. Instead of pulling in the current page, getting a count of db records, and selected your data using a limit/offset just call 'paginate' and tell Laravel where to output the paging links in your view. Laravel automatically does the rest. Laravel's pagination system was designed to be easy to implement and easy to change. It's also important to note that just because Laravel can handle these things automatically doesn't mean that you can't call and configure these systems manually if you prefer.

These are just a few ways in which Laravel differentiates itself from other PHP frameworks.  All of these features and many more are discussed thoroughly in this documentation.

<a name="application-structure"></a>
## Application Structure

Laravel's directory structure is designed to be familiar to users of other popular PHP frameworks. Web applications of any shape or size can easily be created using this structure similarly to the way that they would be created in other frameworks.

However due to Laravel's unique architecture, it is possible for developers to create their own infrastructure that is specifically designed for their application. This may be most beneficial to large projects such as content-management-systems. This kind of architectural flexibility is unique to Laravel.

Throughout the documentation we'll specify the default locations for declarations where appropriate.

<a name="laravel-community"></a>
## Laravel's Community

Laravel is lucky to be supported by rapidly growing, friendly and enthusiastic community. The [Laravel Forums](http://forums.laravel.com) are a great place to find help, make a suggestion, or just see what other people are saying.

Many of us hang out every day in the #laravel IRC channel on FreeNode. [Here's a forum post explaining how you can join us.](http://forums.laravel.com/viewtopic.php?id=671) Hanging out in the IRC channel is a really great way to learn more about web-development using Laravel. You're welcome to ask questions, answer other people's questions, or just hang out and learn from other people's questions being answered. We love Laravel and would love to talk to you about it, so don't be a stranger!

<a name="laravel-license"></a>
## License Information

Laravel is open-sourced software licensed under the [MIT License](http://www.opensource.org/licenses/mit-license.php).