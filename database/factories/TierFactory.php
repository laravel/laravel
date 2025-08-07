<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Tier>
 */
class TierFactory extends Factory
{
    public function definition(): array
    {
        $name = 'Tier '.fake()->unique()->randomDigitNotNull();
        return [
            'name' => $name,
            'slug' => Str::slug($name).'-'.Str::random(3),
            'min_credits' => fake()->numberBetween(10, 1000),
            'description' => 'Auto generated tier',
        ];
    }
}
