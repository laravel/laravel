## New York Times Bestseller API Implementation

Per the assessment requirements this application does the following:

- Connects to the New York Times Best Sellers Endpoint
- Utilizes the Laravel `Http Client`, `Form Requests`, and `Http Tests`
- Exposes Endpoint: `GET /api/1/nyt/best-sellers`
- Follows Parameter Criteria:
  - `author: string`
  - `isbn[]: string`
    - `Length: 10 or 13`
    - `Separator: ;`
    - `Cannot end with a semicolon`
  - `title: string`
  - `offset: integer`
    - `Must be a mutliple of 20. Zero is a valid offset.`
- Setup Dockerfile

## Note:
- Please note that I was unable to get the NYTimes API to return when sending semicolon separated ISBN's.  I tried using 
Postman to hit the endpoint directly and it still didn't return data. 

## Tests:
  - You will find both Unit and Feature Tests.
    - The unit tests are centered around the API's functionality
    - The feature test around the FormRequests handling their validations properly
  - All tests pass without internet connection and utilize `Http::fake` in order to not hit the endpoint.  There is also a series of fixtures that simulate the expected return data.

## Error Handling
  - I implemented a trait `ApiResponses` to allow for consist formatting.
  - In previous versions of Laravel there was a global exceptions handler, but in Laravel 11 there is a `withExceptions` method found in `bootstrap/app.php`.  Within that file I implemented an exception handler to catch all `Throwable` exceptions and respond accordingly.
  - Built custom exception `Exception/ConnectionInterruptionException.php` to allow for custom messaging.

## Project Setup
- Clone application
- Copy `.env.example` to `.env` - Fill in necessary API credentials
- Run `docker-compose up -d` to bring up the application
- Exec into the dev container `docker-compose exec dev bash`
- From within the dev container - Install composer dependencies `composer install`, exit container once complete
- Run migrations `docker-compose run dev php artisan migrate`

## Running Tests
- Run `docker-compose exec dev bash`
- `./vendor/bin/phpunit`
