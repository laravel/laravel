-----
Clone down and remove git folder and init a new repository.
-----

This is a pre-setup repo of Laravel 5.1 for rapid front end development.

## Already Included Composer Packages ##
- nategood/httpful
- barryvdh/laravel-debugbar
- barryvdh/laravel-ide-helper


## Already Included Node Packages ##
- Underscore 1.x.x
- Bootstrap 3.x.x
- Gulp 3.x.x
- jQuery 2.x.x
- Elixir 3.x.x
- Elixir Live Reload 1.x.x
- CSS/JS Versioning

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