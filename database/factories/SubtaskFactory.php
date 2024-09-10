<?php

namespace Database\Factories;

use App\Enums\TaskStatusEnum;
use App\Models\Task;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Subtask>
 */
class SubtaskFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'task_id' => Task::inRandomOrder()->first()->id,
            'assigned_to' => User::inRandomOrder()->first()->id,
            'title' => fake()->word(),
            'description' => fake()->paragraph(),
            'status' => fake()->randomElement(TaskStatusEnum::getValues()),
        ];
    }
}
