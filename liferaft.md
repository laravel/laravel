Routing Contract Not Aliased

The routing contract is not aliased. Browse to `/` to see.

I CAN inject the Authenticator contract, but not the Registrar contract. See /app/Http/FrontendRoutes.php, loaded from /app/Providers/RouteServiceProvider.php

Fix: `laravel/framework/src/Illuminate/Foundation/Application.php`

```
1169 - 			'router'         => 'Illuminate\Routing\Router',
1169 + 			'router'         => ['Illuminate\Routing\Router', 'Illuminate\Contracts\Routing\Registrar'],
```