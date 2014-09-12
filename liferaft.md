Laravel date mutators fail for Postgresql timestamps

According to the docs: http://laravel.com/docs/eloquent#date-mutators
Laravel can be configured to "mutate" database fields to Carbon date
objects. However, this fails on postgresql databases where a timestamp
field has be set to any of the default functions:

CURRENT_TIME
CURRENT_TIMESTAMP
LOCALTIME
LOCALTIMESTAMP
TRANSACTION_TIMESTAMP()
STATEMENT_TIMESTAMP()
CLOCK_TIMESTAMP()
TIMEOFDAY()
NOW()

To reproduce in this liferaft:

1. Update the conf with postgresql connection parameters.
2. artisan migrate --seed
3. View the / route in your browser

You should see an InvalidArgumentException (Trailing data).

NOTE: the column default I used in this liferaft is not necesarry, 
you can also trigger this bug by having an UPDATE query that sets any
TIME or TIMESTAMP column to one of the above functions, or use a
stored procedure, or a trigger, etc... 

