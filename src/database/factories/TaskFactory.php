<?php

namespace Database\Factories;

use App\Models\Task;
use Illuminate\Database\Eloquent\Factories\Factory;

class TaskFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */

    protected $model = Task::class;

    public function definition()
    {
        return [
            'title' => $this->faker->sentence,
            'content' => $this->faker->realText(100), //https://fwhy.github.io/faker-docs/formatters/text/real_text.html
            'person_in_charge' =>  $this->faker->name,
            'board_id' => $this->faker->numberBetween(1,5),
        ];
    }
}
