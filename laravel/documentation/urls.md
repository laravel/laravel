# Generating URLs

## Contents

- [The Basics](#the-basics)
- [URLs To Routes](#urls-to-routes)
- [URLs To Controller Actions](#urls-to-controller-actions)
- [URLs To Assets](#urls-to-assets)
- [URL Helpers](#url-helpers)

<a name="the-basics"></a>
## The Basics

#### Retrieving the application's base URL:

	$url = URL::base();

#### Generating a URL relative to the base URL:

	$url = URL::to('user/profile');

#### Generating a HTTPS URL:

	$url = URL::to_secure('user/login');

#### Retrieving the current URL:

	$url = URL::current();

#### Retrieving the current URL including query string:

	$url = URL::full();

<a name="urls-to-routes"></a>
## URLs To Routes

#### Generating a URL to a named route:

	$url = URL::to_route('profile');

Sometimes you may need to generate a URL to a named route, but also need to specify the values that should be used instead of the route's URI wildcards. It's easy to replace the wildcards with proper values:

#### Generating a URL to a named route with wildcard values:

	$url = URL::to_route('profile', array($username));

*Further Reading:*

- [Named Routes](/docs/routing#named-routes)

<a name="urls-to-controller-actions"></a>
## URLs To Controller Actions

#### Generating a URL to a controller action:

	$url = URL::to_action('user@profile');

#### Generating a URL to an action with wildcard values:

	$url = URL::to_action('user@profile', array($username));

<a name="urls-to-a-different-language"></a>
## URLs To A Different Language

#### Generating a URL to the same page in another language:

	$url = URL::to_language('fr');

#### Generating a URL to your home page in another language:

	$url = URL::to_language('fr', true);

<a name="urls-to-assets"></a>
## URLs To Assets

URLs generated for assets will not contain the "application.index" configuration option.

#### Generating a URL to an asset:

	$url = URL::to_asset('js/jquery.js');

<a name="url-helpers"></a>
## URL Helpers

There are several global functions for generating URLs designed to make your life easier and your code cleaner:

#### Generating a URL relative to the base URL:

	$url = url('user/profile');

#### Generating a URL to an asset:

	$url = asset('js/jquery.js');

#### Generating a URL to a named route:

	$url = route('profile');

#### Generating a URL to a named route with wildcard values:

	$url = route('profile', array($username));

#### Generating a URL to a controller action:

	$url = action('user@profile');

#### Generating a URL to an action with wildcard values:

	$url = action('user@profile', array($username));