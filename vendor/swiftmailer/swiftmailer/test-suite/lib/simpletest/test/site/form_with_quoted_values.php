<html>
	<head>
		<title>Form with quoted values</title>
	</head>
	<body>
		<p>
			QUERY_STRING : <?php print $_SERVER['QUERY_STRING']; ?>
		</p>
		<form action="form_with_quoted_values.php" method="GET">
			<input id="text_field" type="text" name="a" value="default" />
			<input id="submit_button" name="submit" type="submit" value="Go" />
		</form>
	</body>
</html>