<?php

namespace Tests\Unit;

use Illuminate\Support\Facades\Http;
use Nette\Schema\ValidationException;
use Tests\TestCase;

class BookSearchFormRequestTest extends TestCase
{

    /** @test */
    public function isbn_length_less_than_ten_fails(): void
    {
        $response = $this->get('/api/v1/nyt/best-sellers', ['isbn' => '1']);

    }
}
