<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Task>
 */
class TaskFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $icons = [
            '<i class="fas fa-book-open fa-lg"></i>', 
            '<i class="fas fa-dumbbell fa-lg"></i>',   
            '<i class="fas fa-paint-brush fa-lg"></i>', 
            '<i class="fas fa-hands-helping fa-lg"></i>', 
            '<i class="fas fa-user-tie fa-lg"></i>'     
        ];

        return [
            'task' => fake()->sentence(),
            'priority' => fake()->randomElement([0, 1, 2]),
            'complete' => fake()->boolean(),
            'category' => fake()->randomElement($icons), 
        ];
    }

}
