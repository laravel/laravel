# Copilot Instructions - Laravel API

## Architecture Overview

This Laravel 12 application follows a **Domain-Driven Design (DDD)** architecture with modular organization under `src/`:

- **`Domain/`** - Business domain modules (Users, etc.)
- **`Security/`** - Security-related functionality and middleware
- **`Shared/`** - Shared infrastructure, providers, and exceptions

Each domain follows the pattern: `Domain/{Entity}/{App|Domain}/` where:
- `App/` contains Controllers, Requests, Resources, Notifications (framework layer)
- `Domain/` contains Models, Actions, DTOs (business logic layer)

## Key Patterns & Conventions

### Single-Action Controllers
Controllers are invokable classes with single responsibility:
```php
final readonly class StoreUserController
{
    public function __invoke(UpsertUserRequest $request, StoreUserAction $action): JsonResponse
    {
        $user = $action->execute($request->toDto());
        return UserResource::make($user)->response()->setStatusCode(201);
    }
}
```

### Domain Actions
Business logic lives in Domain Actions, not controllers:
```php
class StoreUserAction
{
    public function execute(UserDto $userDto): User { /* business logic */ }
}
```

### Request Classes & DTOs
Form requests include `toDto()` method for clean data transfer:
```php
class UpsertUserRequest extends FormRequest
{
    public function toDto(): UserDto
    {
        return new UserDto(
            name: $this->string('name')->toString(),
            emailAddress: $this->string('email_address')->toString(),
            password: Hash::make($this->string('password')->toString())
        );
    }
}
```

## Development Workflow

### Quality Commands
- `composer fixer` - Runs all code quality tools (ECS, PHPStan, Rector)
- `composer test` - Runs Pest tests with coverage report to `reports/`
- `sail composer test` - Run tests in Docker environment

### Laravel Sail Commands
Always use `sail` prefix for Docker commands:
- `sail up -d` - Start in background
- `sail artisan migrate --seed` - Setup database
- `sail composer require package/name`

### Code Standards
- **PHP 8.4** with strict types (`declare(strict_types=1);`)
- **PHPStan Level 10** - Maximum static analysis
- **Worksome Coding Style** - ECS configuration
- **Pest PHP** for testing with RequestFactories for API testing

## Authentication & API

### Sanctum Authentication
- Uses Laravel Sanctum for API authentication
- Routes protected with `auth:sanctum` middleware
- Custom User model: `Lightit\Backoffice\Users\Domain\Models\User`

### API Documentation
- **Scramble** generates OpenAPI/Swagger docs automatically
- Access at `/docs/api` (configured for routes starting with `api/`)
- API versioning via `API_VERSION` env variable

### Security Features
- Custom `SecurityHeaders` middleware (currently commented in bootstrap)
- Environment-specific CSP headers for local development
- Production safety checks in `PreventDebugInProductionAction`

## Debugging & Monitoring

### Telescope (Local Only)
- Auto-registered in local environment
- Custom filtering for production-like behavior
- Sensitive headers/parameters hidden in non-local environments

### Clockwork Debugbar
- Browser extension required for console-based debugging
- Replaces standard Laravel Debugbar

### Sentry Integration
- Error tracking and performance monitoring
- Test with `sail artisan sentry:test`

## Testing Patterns

### Pest Configuration & Structure
Tests use `RefreshDatabase` trait and `Tests\TestCase` base class:
```php
uses(
    Tests\TestCase::class,
    Illuminate\Foundation\Testing\RefreshDatabase::class,
)->in('Feature');
```

### RequestFactories Pattern
Create type-safe request data with `RequestFactory`:
```php
class StoreUserRequestFactory extends RequestFactory
{
    public function definition(): array
    {
        return [
            'name' => fake()->name,
            'email_address' => fake()->email,
            'password' => '>e$pV4chNFcJoAB%X#{',
            'password_confirmation' => '>e$pV4chNFcJoAB%X#{',
        ];
    }
}
```

### Describe/It Structure
Organize tests with descriptive blocks and controller references:

```php
use Lightit\Users\App\Controllers\StoreUserController;describe('users', function (): void {
    /** @see StoreUserController */
    it('can create a user successfully', function (): void {
        $data = StoreUserRequestFactory::new()->create();
        
        $response = postJson(url('/api/users'), $data);
        
        $response->assertCreated()
            ->assertJson(fn (AssertableJson $json) => 
                $json->has('data', fn ($json) => 
                    $json->whereAll(UserResource::make($user)->resolve())
                )
            );
    });
});
```

### Validation Testing with Datasets
Use Pest datasets for comprehensive validation testing:
```php
dataset('validation-rules', [
    'name is required' => ['name', ''],
    'name be a string' => ['name', ['array']],
    'email be valid' => ['email_address', 'invalid-email'],
    'password be >=8 chars' => ['password', 'short'],
]);

it('cannot create user with invalid data', function (string $field, $value): void {
    $data = StoreUserRequestFactory::new()->create();
    
    postJson(url('/api/users'), [...$data, $field => $value])
        ->assertUnprocessable()
        ->assertJsonValidationErrors([$field], 'error.fields');
})->with('validation-rules');
```

### Database Assertions
Use Laravel's database testing helpers:
```php
// Assert data exists
assertDatabaseHas('users', [
    'name' => $data['name'],
    'email' => $data['email_address'],
]);

// Assert data removed (soft deletes)
assertDatabaseMissing('users', ['id' => $user->id]);

// Verify password hashing
expect(Hash::check('password', $user->password))->toBeTrue();
```

### Notification Testing
Fake and assert notifications:
```php
beforeEach(fn () => Notification::fake());

it('triggers user registration notification', function (): void {
    $data = StoreUserRequestFactory::new()->create();
    
    postJson(url('/api/users'), $data);
    
    $user = User::query()->where('email', $data['email_address'])->firstOrFail();
    Notification::assertSentTo($user, UserRegisteredNotification::class);
});
```

### Factory Usage
Use model factories for test data:
```php
it('can list users successfully', function (): void {
    $users = UserFactory::new()->createMany(5);
    
    getJson(url('/api/users'))
        ->assertSuccessful()
        ->assertJsonCount(5, 'data');
});
```

### PHPStan Integration
Handle PHPStan in tests when needed:
```php
// Example of ignoring PHPStan for test-specific code
/** @phpstan-ignore-next-line */
test()->withoutVite();
```

## Git Hooks & CI

### CaptainHook Integration
Pre-configured git hooks run:
- ECS code style fixes
- PHPStan analysis
- Rector code modernization
- Conventional commit validation

Install with: `vendor/bin/captainhook install --force`

## Environment-Specific Notes

- **Local**: Telescope enabled, relaxed CSP headers, debug mode allowed
- **Production**: Strict security headers, HTTPS forced, debug prevented
- **Database**: PostgreSQL 16 with strict destructive command prevention
- **Cache**: Redis with separate cache/session connections
