<?php

namespace App\Services;

use Illuminate\Http\Client\Response;

interface BookInterface
{
    public function getBestsellers(?string $author = null, ?array $isbn = [], ?string $title = null, ?int $offset = null): Response;
}
