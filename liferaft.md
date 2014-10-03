[5.0] Templating Issue When Using Form methods

This demonstrates an possible issue when attempting to use the
illuminate\html package and using Form:: methods in a blade template.

To Recreate:
Just start up a server and load up the root page.
You should see the actual form tags on the home page, instead of the expected
html/hidden form tags.
