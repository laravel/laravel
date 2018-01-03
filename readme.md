## Installation
1. Clone repository
2. composer install
3. `npm install` in node container
4. copy .env
5. `php artisan key:gen` in php container
6. `php artisan d:s:u` in php container
7. `npm run dev` in node container for dev server

## Minio
1. ADD "127.0.0.1 s3" to your hosts file
2. ACCESS `http://localhost:9000/minio`,
3. Login with the access and secret keys located in `docker-compose.yml`.
4. Create a bucket with some name. Add a R/W policy to the bucket.
5. Configure the bucket name in your env file
6. Change the filesystem driver to `minio`
