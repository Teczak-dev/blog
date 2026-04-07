<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class TestPaginationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $user = \App\Models\User::first();
        
        if (!$user) {
            echo "No users found. Please create a user first.\n";
            return;
        }
        
        $categories = ['Laravel', 'React', 'Vue.js', 'PHP', 'JavaScript', 'CSS', 'HTML', 'Docker', 'MySQL', 'PostgreSQL'];
        $colors = ['blue', 'green', 'purple', 'red', 'yellow', 'indigo', 'pink', 'gray'];
        $authors = ['Jan Kowalski', 'Anna Nowak', 'Piotr Wiśniewski', 'Maria Dąbrowska', 'Tomasz Lewandowski'];
        $tags = ['tutorial', 'advanced', 'beginner', 'tips', 'best-practices', 'performance', 'security', 'testing'];
        
        echo "Creating 100 test posts for pagination testing...\n";
        
        for ($i = 1; $i <= 100; $i++) {
            \App\Models\Post::create([
                'title' => "Test Post #{$i} - " . fake()->sentence(4),
                'category' => fake()->randomElement($categories),
                'category_color' => fake()->randomElement($colors),
                'lead' => fake()->paragraph(2),
                'content' => fake()->paragraphs(8, true),
                'author' => fake()->randomElement($authors),
                'user_id' => $user->id,
                'is_published' => true,
                'tags' => [
                    fake()->randomElement($tags),
                    fake()->randomElement($tags),
                    fake()->randomElement($tags)
                ],
                'read_time_minutes' => rand(3, 20),
                'created_at' => fake()->dateTimeBetween('-6 months', 'now'),
            ]);
            
            if ($i % 20 == 0) {
                echo "Created {$i} posts...\n";
            }
        }
        
        echo "✅ Successfully created 100 test posts!\n";
    }
}
