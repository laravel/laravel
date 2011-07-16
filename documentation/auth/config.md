## Authentication Configuration

Most interactive applications have the ability for users to login and logout. Obvious, right? Laravel provides a simple class to help you validate user credentials and retrieve information about the current user of your application.

The quickest way to get started is to create an [Eloquent User model](/docs/database/eloquent) in your **application/models** directory:

	class User extends Eloquent {}

Next, you will need to define **email** and **password** columns on your user database table. The password column should hold 60 alpha-numeric characters. The Auth class **requires** that all passwords be hashed and salted.

> **Note:** The password column on your user table must really be named "password".

Great job! You're ready to start using the Auth class. However, there are more advanced configuration options available if you wish to use them.

Let's dig into the **application/config/auth.php** file. In this file you will find two closures: **by\_id** and **by\_username**:

	'by_id' => function($id)
	{
		return User::find($id);
	}

The **by_id** function is called when the Auth class needs to retrieve a user by their primary key. As you can see, the default implementation of this function uses an "User" Eloquent model to retrieve the user by ID. However, if you are not using Eloquent, you are free to modify this function to meet the needs of your application.

	'by_username' => function($username)
	{
		return User::where('email', '=', $username)->first();
	}


The **by_username** function is called when the Auth class needs to retrieve a user by their username, such as when using the **login** method. The default implementation of this function uses an "User" Eloquent model to retrieve the user by e-mail address. However, if you are not using Eloquent or do not wish to use e-mail addresses as usernames, you are free to modify this function as you wish as long as you return an object with **password** and **id** properties.