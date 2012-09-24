# Working With Strings

## Contents

- [Capitalization, Etc.](#capitalization)
- [Word & Character Limiting](#limits)
- [Generating Random Strings](#random)
- [Singular & Plural](#singular-and-plural)
- [Slugs](#slugs)

<a name="capitalization"></a>
## Capitalization, Etc.

The **Str** class also provides three convenient methods for manipulating string capitalization: **upper**, **lower**, and **title**. These are more intelligent versions of the PHP [strtoupper](http://php.net/manual/en/function.strtoupper.php), [strtolower](http://php.net/manual/en/function.strtolower.php), and [ucwords](http://php.net/manual/en/function.ucwords.php) methods. More intelligent because they can handle UTF-8 input if the [multi-byte string](http://php.net/manual/en/book.mbstring.php) PHP extension is installed on your web server. To use them, just pass a string to the method:

	echo Str::lower('I am a string.');
	// i am a string.

	echo Str::upper('I am a string.');
	// I AM A STRING.

	echo Str::title('I am a string.');
	// I Am A String.

<a name="limits"></a>
## Word & Character Limiting

#### Limiting the number of characters in a string:

	echo Str::limit("Lorem ipsum dolor sit amet", 10);
	// Lorem ipsu...

	echo Str::limit_exact("Lorem ipsum dolor sit amet", 10);
	// Lorem i...

#### Limiting the number of words in a string:

	echo Str::words("Lorem ipsum dolor sit amet", 3);
	// Lorem ipsum dolor...

<a name="random"></a>
## Generating Random Strings

#### Generating a random string of alpha-numeric characters:

	echo Str::random(32);

#### Generating a random string of alphabetic characters:

	echo Str::random(32, 'alpha');

<a name="singular-and-plural"></a>
## Singular & Plural

#### Getting the plural form of a word:

	echo Str::plural('user');
	// users

#### Getting the singular form of a word:

	echo Str::singular('users');
	// user

#### Getting the plural form if specified value is greater than one:

	echo Str::plural('comment', count($comments));

<a name="slugs"></a>
## Slugs

#### Generating a URL friendly slug:

	return Str::slug('My First Blog Post!');
	// my-first-blog-post

#### Generating a URL friendly slug using a given separator:

	return Str::slug('My First Blog Post!', '_');
	// my_first_blog_post

