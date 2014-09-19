HttpNotFoundException in Tests when hitting the same route twice.

There's no way to define two tests in the same test class which will hit the
same route. It's happening when I got psr-4 mapping to src/ directory.

Just execute phpunit.
All I did in this Liferaft's repository is:
psr-4 mapping to src folder. (Acme namespace)
require once route file from src in route.php in app.
created one simple controller
defined two simple tests.
