<html>
	<head>
		<title>Form with quoted values</title>
	</head>
	<body>
		<p>
			QUERY_STRING : <?php print $_SERVER['QUERY_STRING']; ?>
		</p>
		<form action="form_with_array_based_inputs.php" method="GET">
			<input type="text" name="value[]" value="value1">
			<input type="text" name="value[]" value="value2">
			<input id="submit_button" name="submit" type="submit" value="Go" />
		</form>
	</body>
</html>