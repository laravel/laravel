# Auth Configuration

## Contents

- [The Basics](#the-basics)
- [The Authentication Driver](#driver)
- [The Default "Username"](#username)
- [Authentication Model](#model)
- [Authentication Table](#table)

<a name="the-basics"></a>
## The Basics

Most interactive applications have the ability for users to login and logout. Laravel provides a simple class to help you validate user credentials and retrieve information about the current user of your application.

To get started, let's look over the **application/config/auth.php** file. The authentication configuration contains some basic options to help you get started with authentication.

<a name="driver"></a>
## The Authentication Driver

Laravel's authentication is driver based, meaning the responsibility for retrieving users during authentication is delegated to various "drivers". Two are included out of the box: Eloquent and Fluent, but you are free to write your own drivers if needed!

The **Eloquent** driver uses the Eloquent ORM to load the users of your application, and is the default authentication driver. The **Fluent** driver uses the fluent query builder to load your users.

<a name="username"></a>
## The Default "Username"

The second option in the configuration file determines the default "username" of your users. This will typically correspond to a database column in your "users" table, and will usually be "email" or "username".

<a name="model"></a>
## Authentication Model

When using the **Eloquent** authentication driver, this option determines the Eloquent model that should be used when loading users.

<a name="table"></a>
## Authentication Table

When using the **Fluent** authentication drivers, this option determines the database table containing the users of your application.