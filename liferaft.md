Error when trying to change session driver during runtime

I tried to change the session driver "at runtime" like I did in Laravel 4.x 
applications, but noticed that this causes error.

> ErrorException (Undefined index: _sf2_meta)

In test environment the session-driver is `array`. I tried to change this 
to `file` in the HomeController. So running the `ExampleTest` should reproduce
the error and fail.
