<?php

namespace App\Services;

use Illuminate\Http\Client\Response;

interface BookInterface
{
    public function getBestsellers(array $options = []): Response;
}
