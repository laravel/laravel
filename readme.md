## Laravue - a sensible starting point for single-page apps

Laravue is a fork of the Laravel framework. It includes the vue js
framework for the front end, and contains much of the boilerplate
required for using it.

## Installing

1. Clone the repository
2. CD into the repo
3. Run `npm install`
4. Run `npm run dev`
5. Run `composer install`
5. Run `php artisan serve`
6. Enjoy!

## Roadmap

Wondering what the plans are for Laravue? Check out this list to see where we're going with it!

- JWT Tokens
- Authentication API
- Better documentation site
- Lumen build for lightweight sites
- CLI for building views and components
- Composer tool to create new projects using `laravue new project`
- Composer `create-project` tool for an alternative way of starting off


## Usage
Laravue gives you a few things that may not sound like much, but are really the foundations of any app you may build:
1. a standard way to communicate from app -> view
2. a standard way to communicate from view -> app
3. a standard way to communicate from components -> view
4. a standard way to communicate from components -> app

Why would we want these features? Suppose we want to change the currentView from within a view. Right now, there’s no easy way to do that. Using my setup, we can just run `@app.laravue.view ‘awesome’` .

Another example is if you want to have one user object served up from the backend that can be accessed application-wide. Just add it to the data object of your main app and it can be accessed from views using `@app.user` !

### Methods
#### `view(name)`
This method changes the view component's `currentView` to the argument you passed it. The function will automatically add `-view` to the end of the name you pass it. If you're using coffeescript, like I suggest, you can just run `@app.laravue.view 'about'` and it will take you to the about page. If not, just run `this.app.laravue.view('about');`.

#### `call(view, method, args...)`
This method calls a method on another view, regardless of whether or not it's already loaded. If it is loaded, Laravue goes ahead and calls it. If not, we wait for the view to be loaded, then run the function. The first argument is the name of the view that you want to have a method called on. The second one is the name of the method you want run. Any arguments after that are passed into the method as arguments using javascript magic closures! To use it, go `@app.laravue.call('contacts', 'load')`. If you want to pass arguments, just go `@app.laravue.call('contacts', 'load', 'russweas@gmail.com')`. The third paramater, my email, will be passed into the `load()` method on the contacs-view component. For example, you might have:
```
module.exports =
  methods:
    load: (email) ->
      console.log email
  ready: () -> require '../view-ready.coffee'.call this # required for laravue to work
  props: ['app'] # if you want to use @app from within the view
```
#### `goToAnd(view, name, args...)`
`goToAnd()` is just a shorter way of running `call()` then `view()`. Really useful for most use case scenarios!

## Official Documentation

Documentation for the framework can be found on the [Laravue website](http://laravue.github.io/docs).

## Contributing

Contributing is easy! Just open a Pull Request and tell me why I should accept it.

## Security Vulnerabilities

If you discover a vulnerability in Laravue itself, contact me at russweas@gmail.com and open an issue.

If you discover a security vulnerability within Laravel, please send an e-mail to Taylor Otwell at taylor@laravel.com. All security vulnerabilities will be promptly addressed.

### License

The Laravue framework is open-sourced software licensed under the [MIT license](http://opensource.org/licenses/MIT).
Credit to Taylor Otwell for the creation of Laravel and Evan You for the creation of Vue JS!
