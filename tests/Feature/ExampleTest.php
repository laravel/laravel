<?php

declare(strict_types=1);

namespace Tests\Feature;

use function Pest\Laravel\get;

it('returns a successful response', function () {
    $response = get('/');

    $response->assertSuccessful();
});
