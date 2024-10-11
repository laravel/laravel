<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class NewYorkTimesBookApiTest extends TestCase
{
    /** @test */
    public function get_bestsellers_list(): void
    {
        $body = file_get_contents(base_path('tests/Fixtures/Helpers/NytBestSellers.json'));

        Http::fake([
            'https://api.nytimes.com/svc/books/v3/lists/best-sellers/history.json*' => Http::response($body, 200)
        ]);

        $response = $this->get('api/v1/nyt/best-sellers');

        $response->assertSuccessful();
        $response->assertSee('Hamazaki Tatsuya');
        $response->assertSee('..and the Horse He Rode In On: The People V. Kenneth Starr');
    }

    /** @test */
    public function get_bestsellers_filtered_by_isbn10(): void
    {
        $body = file_get_contents(base_path('tests/Fixtures/Helpers/NytBestSellersFilteredByIsbn10.json'));

        Http::fake([
            'https://api.nytimes.com/svc/books/v3/lists/best-sellers/history.json?isbn=1419726552' => Http::response($body, 200)
        ]);

        $response = $this->get('api/v1/nyt/best-sellers?isbn=1419726552');

        $response->assertSuccessful();
        $response->assertSee('1419726552');
    }

    /** @test */
    public function get_bestsellers_filtered_by_isbn13(): void
    {
        $body = file_get_contents(base_path('tests/Fixtures/Helpers/NytBestSellersFilteredByIsbn13.json'));

        Http::fake([
            'https://api.nytimes.com/svc/books/v3/lists/best-sellers/history.json?isbn=9780778316640' => Http::response($body, 200)
        ]);

        $response = $this->get('api/v1/nyt/best-sellers?isbn=9780778316640');

        $response->assertSuccessful();
        $response->assertSee('9780778316640');
    }
}
