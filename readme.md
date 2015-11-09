-----
Clone down and remove git folder and init a new repository.
-----

This is a pre-setup repo of Laravel 5.1 for rapid front end development.

## Known Issues ##
#### Laravel Elixir ####
Gulp build process is semi-broken. 
Running the task 'gulp' will error.

To start the initial build process run these gulp tasks:
`gulp copy
gulp sass
gulp browserify
gulp version
gulp watch`

Gulp watch looks for changes in this directory
`resources/assets/**`
and will recompile when changes are detected.


## What's Included ##
- Bootstrap
- Font Awesome
- jQuery
- Gulp
- Browserify
- Underscore

## View folder structure ##
- Emails
- Errors
- Layouts
    - Partials
- Pages
    - Home
        - Partials *(not in example repo)*
- Partials


### View folder structure explanation ###
To be added...


## Sass folder structure ##
- Components
- Config
- Pages
	- Home
		- Partials
- Partials
	- Responsive
		- Range
			- Pages
			- Partials

### Sass folder structure explanation (To be updated...) ###
**Components**
*These sass file(s) import the required file(s) for the component (from the vendor folder), can custom contain non-vendor components (Mixins, Extends)*

**Config**
*These sass file(s) set variables *(or configure)* Components. Global config is for settings that can be used in any sass file(s)*

**Pages**
*These sass file(s) are specific to pages only*

**Partials**
*These sass file(s) are typically elements used throughout the website and not to a specific page*