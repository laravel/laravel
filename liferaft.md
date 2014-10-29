Empty input file throw InvalidArgumentException

InvalidArgumentException is thrown when submitting a form with an empty input type file

submit form without selecting file.
App should trigger this error : InvalidArgumentException: An uploaded file must be an array or an instance of UploadedFile. in /home/vagrant/Code/fileUpload/vendor/symfony/http-foundation/Symfony/Component/HttpFoundation/FileBag.php on line 59
