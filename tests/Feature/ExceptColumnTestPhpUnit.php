<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class ExceptColumnTestPhpUnit extends TestCase
{
    /**
     * A basic feature test example.
     */
    public function except_columns(): void
    {
        $article = \App\Models\Article::query()->create([
            'title' => 'Test Title',
            'slug' => fake()->slug(),
            'sub_title' => fake()->title(),
            'short_description' => fake()->paragraph(),
            'full_description' => fake()->paragraph(10),
            'created_at' => now(),
            'updated_at' => now(),
            'deleted_at' => now(),
            'thumbnail' => 'test_thumbnail.jpg',
        ]);

        // Perform the query and assert the results
        $result = \App\Models\Article::query()->exceptColumns(['full_description', 'deleted_at', 'thumbnail'])->get();

        // Assert that the 'full_description', 'deleted_at', and 'thumbnail' columns are not included in the results
        $this->assertArrayNotHasKey('full_description', $result[0]->getAttributes());
        $this->assertArrayNotHasKey('deleted_at', $result[0]->getAttributes());
        $this->assertArrayNotHasKey('thumbnail', $result[0]->getAttributes());
    }
}
