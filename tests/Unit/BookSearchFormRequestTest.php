<?php

namespace Tests\Unit;

use Illuminate\Http\Exceptions\HttpResponseException;
use Tests\TestCase;

class BookSearchFormRequestTest extends TestCase
{

    /** @test */
    public function isbn_length_less_than_ten_fails(): void
    {
        $response = $this->get('/api/v1/nyt/best-sellers?isbn=1;',[], 422);

        $response->assertInvalid(['isbn']);
    }

    /** @test */
    public function isbn_length_greater_than_thirteen_fails(): void
    {
        $response = $this->get('/api/v1/nyt/best-sellers?isbn=12345678912345', [], 422);

        $response->assertValid(['isbn']);
    }

    /** @test */
    public function isbn_length_between_ten_and_thirteen_passes(): void
    {
        $response = $this->get('/api/v1/nyt/best-sellers?isbn=12345678911', [], 200);
    }

    /** @test */
    public function isbn_fails_if_ending_with_semicolon(): void
    {
        $response = $this->get('/api/v1/nyt/best-sellers?isbn=12345678911;', [], 200);
    }

    /** @test */
    public function offset_fails_if_not_multiple_of_twenty(): void
    {
        $response = $this->get('/api/v1/nyt/best-sellers?offset=5', [], 200);

        $response->assertInvalid(['offset']);
    }
}
