Extending Laravel's Validator class breaks Validator::extend
====

As per title, extending the `Illuminate\Validation\Validator` class -- for reasons beyond the scope of this
report -- breaks the validator **extension** mechanism.

When a new validation method is attached to the default validator instance as shown in the code below, Laravel
can intercept and forward the call to desired handler:

```php
Validator::extend('something', 'SpecialChecks@checkSomething');

// Validator is the Laravel's Facade.
$validator = Validator::make($data, $rules);
$validator->passes();
```

Sadly when we extend the actual Validator class behing the facade and try to use the custom validation method
the mechanism either seem to ignore the extension declaration, or call the `checkSomething` on the right object.
Moreover, it seem to me that Laravel is also erroneously changing the name of the validation method do `validateSomething`
which is not the intended behaviour.

```php
Validator::extend('something', 'SpecialChecks@checkSomething');

// Acme\Validator extends the Illuminate Validator class.
$validator = new Acme\Validator(App::make('translator'), $data, $rules);
$validator->passes();
```
