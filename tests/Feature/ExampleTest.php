<?php

declare(strict_types=1);

namespace Tests\Feature;

use function Pest\Laravel\get;

it('returns a successful response', function (): void {
    // Example of Ignore PhpStanLine
    /** @phpstan-ignore-next-line */
    test()->withoutVite();

    $response = get('/');

    $response->assertSuccessful();
});
