<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Category;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Product>
 */
class ProductFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'tamil_name' => 'lars',
            'name' => fake()->name(),
            'url_slug' =>'text',
            'seo_title' =>'text',
            'category_id' => Category::inRandomOrder()->first()->id,
            'price' => random_int(5, 500),
            'description' => 'lars',
            'image' => 'Tfc1Y3MnfsfPAU5WAfWozpu4QPh5Dy-metaaGluZHUtZGl3YWxpLWZlc3RpdmFsLWNyYWNrZXItYmFja2dyb3VuZF8xMDE3LTE1NTAxLndlYnA=-.webp',
        ];
    }
}
