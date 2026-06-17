# Larapi Core

Larapi Core is a lightweight API-only Laravel starter kit. It ships with JSON-first defaults, Sanctum bearer token auth, standardized API responses, and an internal generator for API resources.

No Blade views, no Vite setup, no web routes, and no session-first assumptions.

## Included

- API-only routing through `routes/api.php`
- Versioned auth endpoints under `/api/v1/auth`
- Sanctum bearer token authentication
- Consistent JSON response envelope
- JSON-first exception handling for validation, auth, not-found, authorization, and rate-limit errors
- Configurable API metadata in `config/api.php`
- Lightweight local defaults: SQLite, file cache, sync queue
- `make:api` scaffolding for controllers, services, requests, resources, tests, DTOs, models, and migrations

## Requirements

- PHP 8.3+
- Composer
- SQLite by default, or another Laravel-supported database

## Quick Start

```bash
composer install
cp .env.example .env
php artisan key:generate
php artisan migrate
php artisan serve
```

Or:

```bash
composer setup
php artisan serve
```

## API Defaults

```dotenv
API_NAME="${APP_NAME}"
API_VERSION=v1
API_RATE_LIMIT=60,1
QUEUE_CONNECTION=sync
CACHE_STORE=file
```

`API_RATE_LIMIT=60,1` means 60 requests per 1 minute.

## Endpoints

| Method | Endpoint | Auth | Description |
| --- | --- | --- | --- |
| GET | `/api/v1/status` | No | API metadata and readiness |
| POST | `/api/v1/auth/register` | No | Create account and receive token |
| POST | `/api/v1/auth/login` | No | Authenticate and receive token |
| POST | `/api/v1/auth/logout` | Bearer | Revoke current token |
| GET | `/api/v1/auth/me` | Bearer | Get authenticated user |

## Response Envelope

Success:

```json
{
  "success": true,
  "message": "Operation completed successfully.",
  "data": {}
}
```

Error:

```json
{
  "success": false,
  "message": "The request failed.",
  "errors": {},
  "code": "VALIDATION_ERROR"
}
```

Use `successResponse()` and `errorResponse()` from the base controller, or call `App\Http\Responses\ApiResponse` directly.

## Authentication Example

```bash
curl -X POST http://localhost:8000/api/v1/auth/register \
  -H "Content-Type: application/json" \
  -d '{"name":"Jane","email":"jane@example.com","password":"secret123","password_confirmation":"secret123"}'
```

```bash
curl http://localhost:8000/api/v1/auth/me \
  -H "Authorization: Bearer YOUR_TOKEN"
```

## Generator

Generate a full API resource:

```bash
php artisan make:api Post --all --model --migration --routes --api-version=v1
```

Useful combinations:

```bash
php artisan make:api Post --controller --service --request
php artisan make:api Post --model --migration
php artisan make:api Post --all --model --migration --force
```

Available flags:

```txt
--controller
--service
--request
--resource
--test
--dto
--model
--migration
--routes
--api-version=v1
--force
```

## Structure

```txt
app/
  Console/Commands/MakeApiCommand.php
  Http/
    Controllers/
      Api/V1/AuthController.php
      Controller.php
    Middleware/ForceJsonResponse.php
    Responses/ApiResponse.php
  Models/User.php
config/
  api.php
routes/
  api.php
stubs/
  api/
tests/
  Feature/
```

## Testing

```bash
composer test
```

or:

```bash
php artisan test
```

## License

MIT
