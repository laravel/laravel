Session Payload Override w Concurrent Connections

When using the database session handler it is possible for concurrent 
connections to override the session payload even if no data was changed.

1. `php artisan migrate --seed`
2. Open `http://domain-name` in your browser
3. In your browser open two pages to be run concurrently:
  1. Open `http://domain-name/login` in your browser
  2. *While* the login is loading open `http://domain-name/ping` in your browser
4. Refresh `http://domain-name`. You will be logged out even though there was no action to log you out.
