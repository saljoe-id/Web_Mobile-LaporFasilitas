<?php

namespace Database\Factories;

use App\Enums\ComplaintStatus;
use App\Models\Category;
use App\Models\Complaint;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Complaint>
 */
class ComplaintFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'complaint_code' => strtoupper('PGD-'.now()->format('ymd').'-'.Str::random(8)),
            'category_id' => Category::factory(),
            'title' => fake()->sentence(6),
            'description' => fake()->paragraph(),
            'address' => fake()->address(),
            'district' => fake()->citySuffix(),
            'village' => fake()->streetName(),
            'latitude' => null,
            'longitude' => null,
            'reporter_name' => fake()->name(),
            'reporter_phone' => fake()->numerify('08##########'),
            'reporter_email' => fake()->safeEmail(),
            'photo_path' => null,
            'status' => ComplaintStatus::Pending,
        ];
    }

    public function withStatus(ComplaintStatus $status): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => $status,
        ]);
    }
}
