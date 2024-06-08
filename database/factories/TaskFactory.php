<?php

namespace Database\Factories;

use App\Models\Task;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Task>
 */
class TaskFactory extends Factory
{
    protected $model = Task::class;
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */

    public function definition(): array
    {
        return [
            'title' => $this->faker->sentence,
            'description' => $this->faker->paragraph,
            'assigned_to' => null,
            'created_by' => User::where('role', 'product_owner')->inRandomOrder()->first()->id,
            'due_date' => $this->faker->dateTimeBetween('now', '+1 month'),
            'status' => $this->faker->randomElement(['TODO', 'IN_PROGRESS', 'READY_FOR_TEST', 'PO_REVIEW', 'DONE', 'REJECTED']),
            'created_at' => now(),
        ];
    }
}
