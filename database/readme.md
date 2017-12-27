# Migrations

## Running normal migrations
`php artisan migrate` runs the general migrations. It should be executed after updating the database schema.

## Running "pre-migrations"
`php artisan migrate --path=database/pre-migrations` runs the "pre-migrations". You can use them to change the database before updating the schema.

This is useful in cases like not nullable fields that need to be populated before the schema update. So the update then can make the column not nullable.
