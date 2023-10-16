<?php

test('except_columns', function () {

    /** @var \App\Models\Article $article */
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

    $result = \App\Models\Article::query()->exceptColumns(['full_description', 'deleted_at', 'thumbnail'])->get();


    $this->assertArrayNotHasKey('full_description', $result[0]->getAttributes());
    $this->assertArrayNotHasKey('deleted_at', $result[0]->getAttributes());
    $this->assertArrayNotHasKey('thumbnail', $result[0]->getAttributes());

});
