<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Room>
 */
class RoomFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
            'capacity' => $this->faker->randomNumber(1),
        ];
    }

    public function roomWithCapacity($capacity): RoomFactory
    {
        return $this->state(function($attributes) use ($capacity) {
            return ['capacity' => $capacity];
        });
    }
}
