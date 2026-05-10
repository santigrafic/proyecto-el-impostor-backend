<?php

namespace Database\Factories;

use App\Models\Game;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Game>
 */
class GameFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'theme' => fake()->randomElement([
                'Generico',
                'Animales',
                'Peliculas',
                'Peliculas 80s',
                'Peliculas 90s',
                'The Simpsons'
            ]),
            'word' => fake()->word(),
            'winner' => fake()->optional()->randomElement([
                'impostor',
                'jugadores',
                'sin ganador'
            ]),
            'started_at' => fake()->dateTimeBetween('-1 month', 'now'),
            'finished_at' => fake()->optional()->dateTimeBetween('-1 month', 'now'),
        ];
    }
}