<html>
    <head><title>Test of form submission</title></head>
    <body>
        <p>_GET : [<?php print $_GET['test']; ?>]</p>
        <p>_POST : [<?php print $_POST['test']; ?>]</p>
		<form method="post" name="form_project" id="form_project" action="">
			<input type="hidden" value="test" name="test" />
			<input type="submit" value="Submit Post With Empty Action" name="submit" />
		</form>
    </body>
</html>