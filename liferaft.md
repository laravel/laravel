Sessions are not being saved if the application throws and handles an exception.

Somehow Illuminate\Session\Middleware\Writer is being skipped in this case.

##Recreating

Recreating it is very simple:

1) Throw an exception

2) Handle the exception using App::make('exception')->error(...)

3) Inside the handler do a Session::put(...)

4) In the next request try to Session::get(...) and you should see nothing.

##Liferaft application

Just hit home and use the three links that will be shown.

1) Home, should work in the first page hit

2) Exception 1, doesn't work

3) Exception 3, works because I'm explicitly executing Session::save()

