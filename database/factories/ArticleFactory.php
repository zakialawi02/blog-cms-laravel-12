<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use App\Models\Article;
use App\Models\User;
use App\Models\Category;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Article>
 */
class ArticleFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $title = $this->faker->sentence;
        return [
            'user_id' => User::factory(),
            'category_id' => Category::factory(), // Assuming CategoryFactory exists
            'title' => $title,
            'slug' => Str::slug($title),
            'content' => $this->faker->paragraphs(3, true),
            'excerpt' => $this->faker->paragraph,
            'status' => 'published',
            'published_at' => now(),
            'is_featured' => false,
            'meta_title' => $title,
            'meta_desc' => $this->faker->sentence,
            'meta_keywords' => $title,
        ];
    }
}
