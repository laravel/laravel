Form token not set in Codeception when form macros are defined

When I define a form macro and run a functional test in Codeception the value of the hidden form field "_token" is empty.
It doesn't matter if that form macro is used or not.

What I did:
- Clean Laravel install
- Install Codeception via composer
- Enable modules for functional tests: Laravel4, Asserts
- Change database to sqlite to keep it as simple as possible
- Create a simple route and a very simple view
- Create a simple functional test

- With the macro definition in the routes file the test fails
- If I comment the macro definition out, the test goes green
- If I visit the site in the browser, the token is set with and without the macro definition present

