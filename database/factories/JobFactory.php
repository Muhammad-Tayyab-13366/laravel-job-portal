<?php

namespace Database\Factories;

use App\Models\Job;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Job>
 */
class JobFactory extends Factory
{
    protected $model = Job::class;
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'title' => $this->faker->jobTitle(),
            'description' => $this->faker->paragraphs(3, true),
            'category_id' => \App\Models\Category::inRandomOrder()->first()->id,
            'job_type_id' => \App\Models\JobType::inRandomOrder()->first()->id,
            'location' => $this->faker->city(),
            'salary' => $this->faker->numberBetween(30000, 100000),
            'posted_by' => \App\Models\User::inRandomOrder()->first()->id,
            'status' => 1,
            'company_name' => $this->faker->company(),
            'created_at' => now(),
            'updated_at' => now(),
            'vacancies' => $this->faker->numberBetween(1, 10),
            'experience' => $this->faker->numberBetween(0, 10) . ' years',
            'keywords' => implode(', ', $this->faker->words(5)),
        ];
    }
}
