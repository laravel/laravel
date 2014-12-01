Eloquent does not alias tables in a self join relationship

I have a model with a self relationship. If I use count-based queries in a belongsTo/hasMany query eloquent does not put
an alias in the count subquery so the whole query does not work.

- run migrations
- seed the db
- fire tha app and look at the dump in the root url
