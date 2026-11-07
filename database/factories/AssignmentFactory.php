<?php

namespace Database\Factories;

use App\Enums\AssignmentStatus;
use App\Models\Assignment;
use App\Models\Complaint;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Assignment>
 */
class AssignmentFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'complaint_id' => Complaint::factory(),
            'user_id' => User::factory()->petugas(),
            'assigned_by_id' => User::factory()->admin(),
            'notes' => null,
            'assigned_at' => now(),
            'completed_at' => null,
            'status' => AssignmentStatus::Ditugaskan,
        ];
    }
}
