<?php

namespace Tests\Feature;

use App\Exception\ConnectionInterruptionException;
use GuzzleHttp\Promise\RejectedPromise;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\Client\ConnectionException;
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

        $response = $this->get(route('nyt.bestsellers'));

        $response->assertSuccessful();
        $response->assertSee('Hamazaki Tatsuya');
        $response->assertSee('..and the Horse He Rode In On: The People V. Kenneth Starr');
    }

    /** @test */
    public function get_bestsellers_filtered_by_isbn10(): void
    {
        $body = file_get_contents(base_path('tests/Fixtures/Helpers/NytBestSellersFilteredByIsbn10.json'));

        Http::fake([
            'https://api.nytimes.com/svc/books/v3/lists/best-sellers/history.json*' => Http::response($body, 200)
        ]);

        $response = $this->get(route('nyt.bestsellers', ['isbn' => ['1419726552']]));

        $response->assertSuccessful();
        $response->assertSee('1419726552');
    }

    /** @test */
    public function get_bestsellers_filtered_by_isbn13(): void
    {
        $body = file_get_contents(base_path('tests/Fixtures/Helpers/NytBestSellersFilteredByIsbn13.json'));

        Http::fake([
            'https://api.nytimes.com/svc/books/v3/lists/best-sellers/history.json*' => Http::response($body, 200)
        ]);

        $response = $this->get(route('nyt.bestsellers', ['isbn' => ['9780778316640']]));

        $response->assertSuccessful();
        $response->assertSee('9780778316640');
    }

    public function get_bestsellers_filtered_by_multiple_isbn_numbers(): void
    {
        $body = file_get_contents(base_path('tests/Fixtures/Helpers/NytBestSellersFilteredByIsbn13.json'));

        Http::fake([
            'https://api.nytimes.com/svc/books/v3/lists/best-sellers/history.json*' => Http::response($body, 200)
        ]);

        $response = $this->get(route('nyt.bestsellers', ['isbn' => ['9780778316640', '9780778316640']]));

        $response->assertSuccessful();
        $response->assertSee('OK');
    }

    public function get_bestsellers_filtered_by_author(): void
    {
        $body = file_get_contents(base_path('tests/Fixtures/Helpers/NytBestSellersFilteredByAuthor.json'));

        Http::fake([
            'https://api.nytimes.com/svc/books/v3/lists/best-sellers/history.json*' => Http::response($body, 200)
        ]);

        $response = $this->get(route('nyt.bestsellers', ['author' => 'KATE TELTSCHER']));

        $response->assertSuccessful();
        $response->assertSee('KATE TELTSCHER');
    }

    public function get_bestsellers_with_offset(): void
    {
        $body = file_get_contents(base_path('tests/Fixtures/Helpers/NytBestSellersWithOffset.json'));

        Http::fake([
            'https://api.nytimes.com/svc/books/v3/lists/best-sellers/history.json*' => Http::response($body, 200)
        ]);

        $response = $this->get(route('nyt.bestsellers', ['offset' => '20']));

        $response->assertSuccessful();
    }

    /** @test */
    public function properly_handle_connection_interruptions()
    {
        Http::fake(fn() => throw new ConnectionException("Failed to connect", 504, null));

        $response = $this->get(route('nyt.bestsellers'));

        $response->assertSee('We ran into a problem communicating with the service.  Our team is looking into it - Please check back soon!');

    }
}
