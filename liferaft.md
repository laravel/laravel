Laravel doesn't properly handle DateTime objects in different timezones

When a datetime property on a model is set to a Carbon or DateTime object with a 
different timezone than Laravel's configured timezone, it is stored incorrectly.

In the User model, the attribute 'born' is described as a date.

Hit the / route for a plain text demonstration of the issue.
