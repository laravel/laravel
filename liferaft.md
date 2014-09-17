Input::get('file') does not work

Input::get('file') returns `null` while Input::all() returns an array containing the `file` input.

var_dump(Input::all());

```
array (size=1)
  'file' =>
    object(Symfony\Component\HttpFoundation\File\UploadedFile)[9]
      private 'test' => boolean false
      private 'originalName' => string 'avatar.jpg' (length=10)
      private 'mimeType' => string 'image/jpeg' (length=10)
      private 'size' => int 50770
      private 'error' => int 0
```

var_dump(Input::get('file'));

`null`

To test, just select a file and click the submit button, you will see the var_dumped output as response.
