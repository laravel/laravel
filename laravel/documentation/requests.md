# Examining Requests

## Contents

- [Working With The URI](#working-with-the-uri)
- [Other Request Helpers](#other-request-helpers)

<a name="working-with-the-uri"></a>
## Working With The URI

#### Getting the current URI for the request:

	echo URI::current();

#### Getting a specific segment from the URI:

	echo URI::segment(1);

#### Returning a default value if the segment doesn't exist:

	echo URI::segment(10, 'Foo');

#### Getting the full request URI, including query string:

	echo URI::full();

Sometimes you may need to determine if the current URI is a given string, or begins with a given string. Here's an example of how you can use the is() method to accomplish this:

#### Determine if the URI is "home":

	if (URI::is('home'))
	{
		// The current URI is "home"!
	}

#### Determine if the current URI begins with "docs/":

	if URI::is('docs/*'))
	{
		// The current URI begins with "docs/"!
	}

<a name="other-request-helpers"></a>
## Other Request Helpers

#### Getting the current request method:

	echo Request::method();

#### Accessing the $_SERVER global array:

	echo Request::server('http_referer');

#### Retrieving the requester's IP address:

	echo Request::ip();

#### Determining if the current request is using HTTPS:

	if (Request::secure())
	{
		// This request is over HTTPS!
	}

#### Determing if the current request is an AJAX request:

	if (Request::ajax())
	{
		// This request is using AJAX!
	}

#### Determining if the current requst is via the Artisan CLI:

	if (Request::cli())
	{
		// This request came from the CLI!
	}