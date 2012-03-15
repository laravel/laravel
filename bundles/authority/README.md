# Authority (Role Based Access Control)

This is a clone from codeigniter-authority-authorization

Check https://github.com/machuga/codeigniter-authority-authorization for more info.

All credits go to machuga for PHP-izing this awesome library

## NOTE
Unlike the Codeigniter Authorization library, "Users" and "Roles" have "has_and_belongs_to_many" relations.


## Installation

#### Setting up Laravel

- Enter your database settings in config/database.php
- Enter a key (minimum length 32 chars) in config/application.php
- Enter a session driver in config/session.php
- Change the value of 'inflection' in config/strings.php to the one below this line

```php
'inflection' => array(

	'user' => 'users',
	'role' => 'roles',

),
```
- Add Eloquent and Authority to 'application/bundles.php' like below

```php
return array(
	'eloquent',
	'authority' => array(
		'auto' => true
	)
);
```


#### Setting up Eloquent & Migrations

If you already have the Eloquent bundle installed you can skip this step.
To install Eloquent, use the `cd` command to go to your laravel directory, now run the following commands:

`php artisan bundle:install eloquent`

`php artisan bundle:install authority`

`php artisan migrate:install`

`php artisan migrate`


#### Configure Auth config

By default laravels' auth is configured without Eloquent. to enable this, replace the methods in config/auth.php with the ones below.

```php
'user' => function($id)
{
	if (filter_var($id, FILTER_VALIDATE_INT) !== false)
	{
		return User::find($id);
	}
},

'attempt' => function($username, $password)
{
	$user = User::where_username($username)->first();

	if ( ! is_null($user) and Hash::check($password, $user->password))
	{
		return $user;
	}
},
```

### Setup your Models

#### User model (models/user.php)
```PHP
class User extends Eloquent\Model {

	public static $timestamps = true;

	public function roles()
	{
		return $this->has_and_belongs_to_many('Role');
	}

	public static function has_role($key)
	{
		foreach(Auth::user()->roles as $role)
		{
			if($role->key == $key)
			{
				return true;
			}
		}

		return false;
	}

	public static function has_any_role($keys)
	{
		if( ! is_array($keys))
		{
			$keys = func_get_args();
		}

		foreach(Auth::user()->roles as $role)
		{
			if(in_array($role->key, $keys))
			{
				return true;
			}
		}

		return false;
	}
}
```


#### Role model (models/role.php)
```PHP
class Role extends Eloquent\Model {

	public function users()
	{
		return $this->has_and_belongs_to_many('User');
	}

}
```


### Setting up Rules

Modify `bundles/authority/config/authority.php` to your likings, more info on how to do this can be found at https://github.com/machuga/codeigniter-authority-authorization


## Bonus points

Here is my User model with 2 extra methods for validating and inserting / updating the User it also manages roles.

```php
class User extends Eloquent\Model {

	public static $timestamps = true;

	public $rules = array(
		'email' => 'required|email',
		'name' => 'required',
	);

	public function roles()
	{
		return $this->has_and_belongs_to_many('Role');
	}

	public static function has_role($key)
	{
		foreach(Auth::user()->roles as $role)
		{
			if($role->key == $key)
			{
				return true;
			}
		}

		return false;
	}

	public static function has_any_role($keys)
	{
		if( ! is_array($keys))
		{
			$keys = func_get_args();
		}

		foreach(Auth::user()->roles as $role)
		{
			if(in_array($role->key, $keys))
			{
				return true;
			}
		}

		return false;
	}

	public function validate_and_insert()
	{
		$this->rules['password'] = 'required';
		$validator = new Validator(Input::all(), $this->rules);

		if ($validator->valid())
		{
			$this->email = Input::get('email');
			$this->password = Hash::make(Input::get('password'));
			$this->name = Input::get('name');
			$this->save();

			if(Input::has('role_ids'))
			{
				foreach(Input::get('role_ids') as $role_id)
				{
					if($role_id == 0) continue;

					DB::table('roles_users')
						->insert(array('user_id' => $this->id, 'role_id' => $role_id));
				}
			}
		}

		return $validator->errors;
	}

	public function validate_and_update()
	{
		$validator = new Validator(Input::all(), $this->rules);
		if ($validator->valid())
		{
			DB::table('roles_users')->where_user_id($this->id)->delete();

			if(Input::has('role_ids'))
			{
				foreach (Input::get('role_ids') as $role_id) {
					if($role_id == 0) continue;

					DB::table('roles_users')
						->insert(array('user_id' => $this->id, 'role_id' => $role_id));
				}
			}

			$this->email = Input::get('email');
			if($password = Input::get('password')) $this->password = Hash::make($password);
			$this->name = Input::get('name');
			$this->save();
		}

		return $validator->errors;
	}

}
```

Now in your controller or route you would handle the inserts and updates like this:

```php
public function post_add()
{
	$user = new User;

	$errors = $user->validate_and_insert();
	if(count($errors->all()) > 0)
	{
		return Redirect::to('users/add')
					->with('errors', $errors)
					->with_input('except', array('password'));
	}

	return Redirect::to('users/index')
					->with('notification', 'Successfully created user');
}

public function put_edit($id = 0)
{
	$user = User::find($id);
	if( ! $user OR $id == 0)
	{
		return Redirect::to('users/index');
	}

	$errors = $user->validate_and_update();
	if(count($errors->all()) > 0)
	{
		return Redirect::to('users/edit')
					->with('errors', $errors)
					->with_input('except', array('password'));
	}

	return Redirect::to('users/index')
					->with('notification', 'Successfully updated user');
}
```

# Congrats... Done!
