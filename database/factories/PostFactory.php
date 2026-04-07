<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Post>
 */
class PostFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $title = $this->faker->sentence(4);
        $categories = ['Laravel', 'Vue', 'React', 'PHP', 'JavaScript', 'CSS', 'DevOps', 'Tutorial'];
        $colors = array_keys(\App\Models\Post::getCategoryColors());
        
        return [
            'title' => $title,
            'category' => $this->faker->randomElement($categories),
            'category_color' => $this->faker->randomElement($colors),
            'lead' => $this->faker->paragraph(2),
            'content' => $this->faker->paragraphs(3, true),
            'author' => $this->faker->name(),
            'photo' => null,
            'user_id' => User::factory(),
            'is_published' => $this->faker->boolean(80),
            'tags' => $this->faker->randomElements(['PHP', 'Laravel', 'Vue', 'React', 'CSS', 'JS'], rand(1, 3)),
            'read_time_minutes' => $this->faker->numberBetween(1, 15),
        ];
    }

    /**
     * Indicate that the post has a photo.
     */
    public function withPhoto(): static
    {
        return $this->state(fn (array $attributes) => [
            'photo' => 'posts/test-photo.jpg',
        ]);
    }

    /**
     * Indicate that the post is published.
     */
    public function published(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_published' => true,
        ]);
    }

    /**
     * Indicate that the post is a draft.
     */
    public function draft(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_published' => false,
        ]);
    }
}
