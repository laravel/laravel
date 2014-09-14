<!doctype html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<title>Laravel PHP Framework</title>
	<style>
		body {
			margin: 100px auto;
            max-width: 800px;
		}
	</style>
</head>
<body>
    <ul>
        <?php foreach($errors->all() as $error): ?>
            <li><?php echo $error; ?></li>
        <?php endforeach; ?>
    </ul>

    <h3>Using the new request objects:</h3>
    <p>Posting this form with no input will yield a blank page, if the name is entered but no file is supplied it will still yield a blank page. Selecting a file will make it pass trough to to method woth both file and name input.</p>

    <form action="<?php echo url('/post-with') ?>" method="POST" accept-charset="UTF-8" enctype="multipart/form-data">
        <label for="name">Name:</label><br/>
        <input type="text" name="name" /><br/>
        <label for="file">File input:</label><br/>
        <input type="file" name="file" /><br/>
        <button type="submit">Submit the form</button>
    </form>

    <hr/>

    <h3>Without using a request object:</h3>
    <p>This form works fine any way. With a file, without a file and so on.</p>

    <form action="<?php echo url('/post-without') ?>" method="POST" accept-charset="UTF-8" enctype="multipart/form-data">
        <label for="name">Name:</label><br/>
        <input type="text" name="name" /><br/>
        <label for="file">File input:</label><br/>
        <input type="file" name="file" /><br/>
        <button type="submit">Submit the form</button>
    </form>
</body>
</html>
