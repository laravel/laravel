# Working With Files

## Contents

- [Reading Files](#get)
- [Writing Files](#put)
- [File Uploads](#upload)
- [File Extensions](#ext)
- [Checking File Types](#is)
- [Getting MIME Types](#mime)
- [Copying Directories](#cpdir)
- [Removing Directories](#rmdir)

<a name="get"></a>
## Reading Files

#### Getting the contents of a file:

	$contents = File::get('path/to/file');

<a name="put"></a>
## Writing Files

#### Writing to a file:

	File::put('path/to/file', 'file contents');

#### Appending to a file:

	File::append('path/to/file', 'appended file content');

<a name="upload"></a>
## File Uploads

#### Moving a $_FILE to a permanent location:

	Input::upload('picture', 'path/to/pictures', 'filename.ext');

> **Note:** You can easily validate file uploads using the [Validator class](/docs/validation).

<a name="ext"></a>
## File Extensions

#### Getting the extension from a filename:

	File::extension('picture.png');

<a name="is"></a>
## Checking File Types

#### Determining if a file is given type:

	if (File::is('jpg', 'path/to/file.jpg'))
	{
		//
	}

The **is** method does not simply check the file extension. The Fileinfo PHP extension will be used to read the content of the file and determine the actual MIME type.

> **Note:** You may pass any of the extensions defined in the **application/config/mimes.php** file to the **is** method.
> **Note:** The Fileinfo PHP extension is required for this functionality. More information can be found on the [PHP Fileinfo page](http://php.net/manual/en/book.fileinfo.php).

<a name="mime"></a>
## Getting MIME Types

#### Getting the MIME type associated with an extension:

	echo File::mime('gif');

> **Note:** This method simply returns the MIME type defined for the extension in the **application/config/mimes.php** file.

<a name="cpdir"></a>
## Copying Directories

#### Recursively copy a directory to a given location:

	File::cpdir($directory, $destination);

<a name="rmdir"></a>
## Removing Directories

#### Recursively delete a directory:

	File::rmdir($directory);