<?php

namespace Tests\Feature;

use App\Models\Block;
use App\Models\Booking;
use App\Models\Room;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BookingControllerTest extends TestCase
{
    use RefreshDatabase;

    private Room $roomB;
    private Room $roomC;

    protected function setUp(): void
    {
        parent::setUp();

        Room::factory()
            ->roomWithCapacity(6)
            ->has(Booking::factory()
                ->count(3))->create();

        $this->roomB = Room::factory()
            ->roomWithCapacity(4)
            ->has(Booking::factory()->count(1))
            ->create();

        $this->roomC = Room::factory()->roomWithCapacity(2)->create();

        Booking::factory()->create([
            'room_id' => $this->roomB->id,
            'starts_at' => now()->addDays(2)->format('Y-m-d'),
            'ends_at' => now()->addDays(7)->format('Y-m-d'),
        ]);

        Block::factory()->create([
            'room_id' => $this->roomB->id,
            'starts_at' => now()->format('Y-m-d'),
            'ends_at' => now()->addDays(9)->format('Y-m-d'),
        ]);

    }

    public function test_it_should_create_a_booking()
    {
        $room = Room::factory()->create();

        $this->post(route('booking.store'), [
            'room_id' => $room->id,
            'starts_at' => now()->format('Y-m-d'),
            'ends_at' => now()->addDays(10)->format('Y-m-d'),
        ])->assertCreated();

        $this->assertDatabaseCount('bookings', 6);
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

    public function test_it_should_return_occupancy_for_a_day()
    {

        $bookingResponse = $this->get(route('daily.booking', [
            'day' => now()->addDays(1)->format('Y-m-d'),
        ]));

        $this->assertSame(0.36, $bookingResponse->json('occupancy'));

        $response = $this->get(route('daily.booking', [
            'day' => now()->addDays(1)->format('Y-m-d'),
            'room_id' => [$this->roomB->id, $this->roomC->id],
        ]));

        $this->assertSame(0.2, $response->json('occupancy'));
    }

    public function test_it_should_return_occupancy_for_a_month()
    {
        $this->withoutExceptionHandling();
        $response = $this->get(route('monthly.booking', [
            'month' => now()->format('Y-m'),
        ]));

        $this->assertSame(0.08, $response->json('occupancy'));

        $roomIdsResponse = $this->get(route('monthly.booking', [
            'month' => now()->format('Y-m'),
            'room_id' => [$this->roomB->id, $this->roomC->id],
        ]));

        $this->assertSame(0.07, $roomIdsResponse->json('occupancy'));
    }
}
