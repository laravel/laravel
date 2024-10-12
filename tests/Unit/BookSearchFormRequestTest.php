<?php

namespace Tests\Unit;

use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class BookSearchFormRequestTest extends TestCase
{
    /** @test */
    public function isbn_length_not_ten_or_thirteen_fails(): void
    {
        $body = file_get_contents(base_path('tests/Fixtures/Helpers/NytBestSellers.json'));

        Http::fake([
            'https://api.nytimes.com/svc/books/v3/lists/best-sellers/history.json*' => Http::response($body, 422)
        ]);

        $response = $this->get(route('nyt.bestsellers', ['isbn' => ['12345678912345']]), [], 422);

        $response->assertSee('isbn');
        $response->assertSee('isbn must have a length of 10 or 13 digits');
    }

    /** @test */
    public function isbn_length_of_ten_passes(): void
    {
        $body = file_get_contents(base_path('tests/Fixtures/Helpers/NytBestSellers.json'));

        Http::fake([
            'https://api.nytimes.com/svc/books/v3/lists/best-sellers/history.json*' => Http::response($body, 200)
        ]);

        $response = $this->get(route('nyt.bestsellers', ['isbn' => ['1234567891']]), [], 200);

        $response->assertSuccessful();
    }

    /** @test */
    public function isbn_length_of_thirteen_passes(): void
    {
        $body = file_get_contents(base_path('tests/Fixtures/Helpers/NytBestSellers.json'));

        Http::fake([
            'https://api.nytimes.com/svc/books/v3/lists/best-sellers/history.json*' => Http::response($body, 200)
        ]);

        $response = $this->get(route('nyt.bestsellers', ['isbn' => ['1234567891234']]), [], 200);

        $response->assertSuccessful();
    }

    /** @test */
    public function isbn_fails_if_ending_with_semicolon(): void
    {
        $body = file_get_contents(base_path('tests/Fixtures/Helpers/NytBestSellers.json'));

        Http::fake([
            'https://api.nytimes.com/svc/books/v3/lists/best-sellers/history.json*' => Http::response($body, 422)
        ]);

        $response = $this->get(route('nyt.bestsellers', ['isbn' => ['1234567891;']]), [], 422);

        $response->assertSee('isbn');
        $response->assertSee('isbn cannot end with a semicolon');
    }

    /** @test */
    public function offset_fails_if_not_multiple_of_twenty(): void
    {
        $body = file_get_contents(base_path('tests/Fixtures/Helpers/NytBestSellers.json'));

        Http::fake([
            'https://api.nytimes.com/svc/books/v3/lists/best-sellers/history.json*' => Http::response($body, 422)
        ]);

        $response = $this->get(route('nyt.bestsellers', ['offset' => '5']), [], 422);

        $response->assertSee('offset');
        $response->assertSee('offset needs to be multiple of 20.');
    }

    /** @test */
    public function isbn_passes_with_multiple_isbn_numbers(): void
    {
        $body = file_get_contents(base_path('tests/Fixtures/Helpers/NytBestSellers.json'));

        Http::fake([
            'https://api.nytimes.com/svc/books/v3/lists/best-sellers/history.json*' => Http::response($body, 200)
        ]);

        $response = $this->get(route('nyt.bestsellers', ['isbn' => ['1234567890', '0987654321']]), [], 200);

        $response->assertSuccessful();
    }

}
