<?php

namespace Database\Factories;

use App\Enums\ComplaintStatus;
use App\Models\Complaint;
use App\Models\ComplaintStatusHistory;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<ComplaintStatusHistory>
 */
class ComplaintStatusHistoryFactory extends Factory
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
            'old_status' => null,
            'new_status' => ComplaintStatus::Pending,
            'user_id' => null,
            'notes' => null,
        ];
    }
}
