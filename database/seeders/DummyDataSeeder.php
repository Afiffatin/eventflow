<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Event;
use App\Models\User;

class DummyDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Simulated Seeding for PRK42-43
        $users = User::factory(50)->create();
        
        foreach ($users as $user) {
            Event::factory(3)->create([
                'organizer_id' => $user->id,
            ]);
        }
    }
}
