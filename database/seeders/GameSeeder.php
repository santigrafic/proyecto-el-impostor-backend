<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Game;
use App\Models\User;

class GameSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = User::all();

        Game::factory(50)->create()->each(function ($game) use ($users) {
            $players = $users->random(rand(3, 10));

            $impostorAssigned = false;

            foreach ($players as $user) {

                $isImpostor = !$impostorAssigned && rand(0, 3) === 0;

                if ($isImpostor) {
                    $impostorAssigned = true;
                }

                $game->users()->attach($user->id, [
                    'role' => $isImpostor ? 'impostor' : 'jugador'
                ]);
            }
        });
    }
}
