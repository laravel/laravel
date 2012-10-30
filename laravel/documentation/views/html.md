# Building HTML

## Content

- [Entities](#entities)
- [Scripts And Style Sheets](#scripts-and-style-sheets)
- [Links](#links)
- [Links To Named Routes](#links-to-named-routes)
- [Links To Controller Actions](#links-to-controller-actions)
- [Mail-To Links](#mail-to-links)
- [Images](#images)
- [Lists](#lists)
- [Custom Macros](#custom-macros)

<a name="entities"></a>
## Entities

When displaying user input in your Views, it is important to convert all characters which have significance in HTML to their "entity" representation.

For example, the < symbol should be converted to its entity representation. Converting HTML characters to their entity representation helps protect your application from cross-site scripting:

#### Converting a string to its entity representation:

	echo HTML::entities('<script>alert('hi');</script>');

#### Using the "e" global helper:

	echo e('<script>alert('hi');</script>');

<a name="scripts-and-style-sheets"></a>
## Scripts And Style Sheets

#### Generating a reference to a JavaScript file:

	echo HTML::script('js/scrollTo.js');

#### Generating a reference to a CSS file:

	echo HTML::style('css/common.css');

#### Generating a reference to a CSS file using a given media type:

	echo HTML::style('css/common.css', array('media' => 'print'));

*Further Reading:*

- *[Managing Assets](/docs/views/assets)*

<a name="links"></a>
## Links

#### Generating a link from a URI:

	echo HTML::link('user/profile', 'User Profile');

#### Generating a link that should use HTTPS:

	echo HTML::link_to_secure('user/profile', 'User Profile');

#### Generating a link and specifying extra HTML attributes:

	echo HTML::link('user/profile', 'User Profile', array('id' => 'profile_link'));

<a name="links-to-named-routes"></a>
## Links To Named Routes

#### Generating a link to a named route:

	echo HTML::link_to_route('profile');

#### Generating a link to a named route with wildcard values:

	$url = HTML::link_to_route('profile', array($username));

*Further Reading:*

- *[Named Routes](/docs/routing#named-routes)*

<a name="links-to-controller-actions"></a>
## Links To Controller Actions

#### Generating a link to a controller action:

	echo HTML::link_to_action('home@index');

### Generating a link to a controller action with wildcard values:

	echo HTML::link_to_action('user@profile', 'User Profile', array($username));

<a name="mail-to-links"></a>
## Mail-To Links

The "mailto" method on the HTML class obfuscates the given e-mail address so it is not sniffed by bots.

#### Creating a mail-to link:

	echo HTML::mailto('example@gmail.com', 'E-Mail Me!');

#### Creating a mail-to link using the e-mail address as the link text:

	echo HTML::mailto('example@gmail.com');

<a name="images"></a>
## Images

#### Generating an HTML image tag:

	echo HTML::image('img/smile.jpg', $alt_text);

#### Generating an HTML image tag with extra HTML attributes:

	echo HTML::image('img/smile.jpg', $alt_text, array('id' => 'smile'));

<a name="lists"></a>
## Lists

#### Creating lists from an array of items:

	echo HTML::ol(array('Get Peanut Butter', 'Get Chocolate', 'Feast'));

	echo HTML::ul(array('Ubuntu', 'Snow Leopard', 'Windows'));

	echo HTML::dl(array('Ubuntu' => 'An operating system by Canonical', 'Windows' => 'An operating system by Microsoft'));

<a name="custom-macros"></a>
## Custom Macros

It's easy to define your own custom HTML class helpers called "macros". Here's how it works. First, simply register the macro with a given name and a Closure:

#### Registering a HTML macro:

	HTML::macro('my_element', function()
	{
		return '<article type="awesome">';
	});

Now you can call your macro using its name:

#### Calling a custom HTML macro:

	echo HTML::my_element();
