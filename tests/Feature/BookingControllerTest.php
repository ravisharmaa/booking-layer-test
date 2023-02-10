<?php

namespace Tests\Feature;

use App\Models\Booking;
use App\Models\Room;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BookingControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_should_create_a_booking()
    {
        $room = Room::factory()->create();

        $this->post(route('booking.store'), [
            'room_id' => $room->id,
            'starts_at' => now()->format('Y-m-d'),
            'ends_at' => now()->addDays(10)->format('Y-m-d'),
        ])->assertCreated();

        $this->assertDatabaseCount('bookings', 1);
    }

    public function test_it_should_update_a_booking()
    {
        $booking = Booking::factory()->create();
        $startsAt = now()->format('Y-m-d');
        $endsAt = now()->addDays(10)->format('Y-m-d');
        $this->withoutExceptionHandling()
            ->put(route('booking.update', [
                'booking' => $booking,
            ]), [
                'starts_at' => $startsAt,
                'ends_at' => $endsAt,
            ])->assertOk();

        $this->assertSame($booking->fresh()->starts_at, $startsAt);
        $this->assertSame($booking->fresh()->ends_at, $endsAt);
    }
}
