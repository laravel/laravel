HTML Provider escaping bug

When using illuminate/html, the html generated is enclosed in double quotes and not parsed by the browser. The e() helper seems to be involved, but I can't tell in what way.

* No seeds/database required

### Tested with
- Browsers
	- Chrome
	- Firefox

- Stack
	- Homestead 0.1.9 (PHP)
	- artisan serve (with host PHP 5.5.9-1ubuntu4.4)


### Steps to reproduce

* Access the "/" route