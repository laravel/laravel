## Working With Files

- [Reading Files](#get)
- [Writing Files](#put)
- [File Uploads](#upload)
- [File Extensions](#ext)
- [Checking File Types](#is)
- [Getting MIME Types](#mime)

<a name="get"></a>
### Reading Files

It's a breeze to get the contents of a file using the **get** method on the **File** class:

	$contents = File::get('path/to/file');

<a name="put"></a>
### Writing Files

Need to write to a file? Check out the **put** method:

	File::put('path/to/file', 'file contents');

Want to append to the file instead of overwriting the existing contents? No problem. Use the **append** method:

	File::append('path/to/file', 'appended file content');

<a name="upload"></a>
### File Uploads

After a file has been uploaded to your application, you will want to move it from its temporary location to a permanent directory. You can do so using the **upload** method. Simply mention the **name** of the uploaded file and the path where you wish to store it:

	File::upload('picture', 'path/to/pictures');

> **Note:** You can easily validate file uploads using the [Validator class](/docs/start/validation).

<a name="ext"></a>
### File Extensions

Need to get the extension of a file? Just pass the filename to the **extension** method:

	File::extension('picture.png');

<a name="is"></a>
### Checking File Types

Often, it is important to know the type of a file. For instance, if a file is uploaded to your application, you may wish to verify that it is an image. It's easy using the **is** method on the **File** class. Simply pass the extension of the file type you are expecting. Here's how to verify that a file is a JPG image:

	if (File::is('jpg', 'path/to/file.jpg'))
	{
		//
	}

The **is** method does not simply check the file extension. The Fileinfo PHP extension will be used to read the content of the file and determine the actual MIME type. Pretty cool, huh?

> **Note:** You may pass any of the extensions defined in the **application/config/mimes.php** file to the **is** method.

<a name="mime"></a>
### Getting MIME Types

Need to know the MIME type associated with a file extension? Check out the **mime** method:

	echo File::mime('gif');

The statement above returns the following string:

	image/gif

> **Note:** This method simply returns the MIME type defined for the extension in the **application/config/mimes.php** file.