# Larapi Core

A lightweight, API-only Laravel starter kit. No Blade views, no Vite, no web routes — just JSON endpoints, Sanctum bearer token auth, and a standardized API scaffolding workflow.

## What's included

- **API-only routing** — `routes/api.php` with versioned endpoints (`/api/v1/...`)
- **Sanctum token auth** — register, login, logout, and `/me` endpoints out of the box
- **Standard JSON envelope** — `ApiResponse` helper with consistent `success`, `message`, `data`, and `errors` fields
- **`make:api` command** — scaffold controllers, services, requests, resources, tests, models, and migrations
- **Health check** — `GET /up` for uptime monitoring

## Requirements

- PHP 8.3+
- Composer
- SQLite (default) or MySQL/PostgreSQL

## Quick start

```bash
composer install
cp .env.example .env
php artisan key:generate
touch database/database.sqlite
php artisan migrate
php artisan serve
```

Or use the setup script:

```bash
composer setup
php artisan serve
```

## Authentication

All protected routes use Sanctum bearer tokens.

| Method | Endpoint | Auth | Description |
|--------|----------|------|-------------|
| POST | `/api/v1/auth/register` | No | Create account and receive token |
| POST | `/api/v1/auth/login` | No | Authenticate and receive token |
| POST | `/api/v1/auth/logout` | Bearer | Revoke current token |
| GET | `/api/v1/auth/me` | Bearer | Get authenticated user |

### Example

```bash
# Register
curl -X POST http://localhost:8000/api/v1/auth/register \
  -H "Content-Type: application/json" \
  -d '{"name":"Jane","email":"jane@example.com","password":"secret123","password_confirmation":"secret123"}'

# Use token
curl http://localhost:8000/api/v1/auth/me \
  -H "Authorization: Bearer YOUR_TOKEN"
```

## Response format

Successful responses:

```json
{
  "success": true,
  "message": "Operation completed successfully.",
  "data": {}
}
```

Error responses:

```json
{
  "success": false,
  "message": "The request failed.",
  "errors": {},
  "code": "VALIDATION_ERROR"
}
```

Use the base `Controller` helpers or `ApiResponse` directly in your endpoints.

## Scaffolding APIs

Generate a full resource stack:

```bash
php artisan make:api Post --all --model --migration --routes --api-version=v1
```

This creates:

- `App\Http\Controllers\Api\V1\PostController`
- `App\Services\V1\PostService`
- `App\Http\Requests\V1\PostRequest`
- `App\Http\Resources\V1\PostResource`
- `tests/Feature/V1/PostApiTest`
- Model and migration (with `--model --migration`)

Available flags: `--controller`, `--service`, `--request`, `--resource`, `--test`, `--dto`, `--routes`, `--force`.

## Project structure

```
app/
├── Console/Commands/MakeApiCommand.php
├── Http/
│   ├── Controllers/
│   │   ├── Api/V1/AuthController.php
│   │   └── Controller.php          # successResponse() / errorResponse()
│   └── Responses/ApiResponse.php
├── Models/User.php
routes/
└── api.php                         # Versioned API routes only
stubs/api/                          # Generator templates
```

## Configuration

| File | Purpose |
|------|---------|
| `config/auth.php` | Sanctum guard (token-based) |
| `config/sanctum.php` | Bearer-only token settings |
| `.env.example` | Minimal API-focused environment |

Session, frontend, and SPA cookie auth have been removed. Add them back only if your use case requires it.

## Testing

```bash
composer test
# or
php artisan test
```

## License

MIT
