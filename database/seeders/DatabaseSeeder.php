<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use App\Models\Block;
use App\Models\Booking;
use App\Models\Room;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        // \App\Models\User::factory(10)->create();

        // \App\Models\User::factory()->create([
        //     'name' => 'Test User',
        //     'email' => 'test@example.com',
        // ]);

        $roomA = Room::factory()->create([
            'capacity' => 6,
        ]);

        $roomB = Room::factory()->create([
            'capacity' => 4,
        ]);

        $roomC = Room::factory()->create([
            'capacity' => 2,
        ]);

        Booking::factory()->count(3)->create([
            'room_id' => $roomA->id,
        ]);

        Booking::factory()->create([
            'room_id' => $roomB->id,
        ]);

        Booking::factory()->create([
            'room_id' => $roomB->id,
            'starts_at' => now()->addDays(2)->format('Y-m-d'),
            'ends_at' => now()->addDays(7)->format('Y-m-d'),
        ]);

        Block::factory()->create([
            'room_id' => $roomB->id,
            'starts_at' => now()->format('Y-m-d'),
            'ends_at' => now()->addDays(9)->format('Y-m-d'),
        ]);
    }
}
