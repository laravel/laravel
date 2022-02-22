
## Answer Step 4

Verifying the url ( App\Http\Services\Requests::MakeRequest ) and using the guzzle library to send HTTTP requests ( App\Http\Infraestructure\Requests\Post::Gouttle_Post_Request ) we ensure the correct request delivery.

- Run Laravel Command: php artisan post:request https://atomic.incfile.com/fakepost

## Answer Step 5

Using an API ( App\Http\Controllers\PostController::Post ) and implementing Laravel Jobs ( App\Jobs\PostRequestjob ) and Queues ( Default queue ) we can handle the 100k requests, this is a good option because we can process this request in queues and not in the server runtime, this helps improving tasks performance

 - Run Server Command: php artisan serve

 - HTTP Request [ POST ] : http://localhost:8000/api/sendrequest


 - HTTP Request Body :

    {
        "url": "https://atomic.incfile.com/fakepost"
    }

 - Run Laravel Queue: php artisan queue:work

    - *For Implementing better performance i recommend using Lumen Micro-framework to implementing Fast APIs and microservices to handle this 100k requests*