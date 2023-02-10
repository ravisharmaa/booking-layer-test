<?php

namespace Tests\Feature;

use App\Http\Services\Calculator;
use App\Models\Block;
use App\Models\Booking;
use App\Models\Room;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class OccupancyControllerTest extends TestCase
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

    public function test_it_should_return_occupancy_for_a_day()
    {
        $bookingResponse = $this->get(route('daily.booking', [
            'day' => now()->addDays(1)->format('Y-m-d'),
        ]));

        $calculator = new Calculator();
        $occupancy = $calculator->calculateOccupancy(
            bookings: 4, blocks: 1, capacity: 12
        );

        $this->assertSame($occupancy, $bookingResponse->json('occupancy'));

        $response = $this->get(route('daily.booking', [
            'day' => now()->addDays(1)->format('Y-m-d'),
            'room_id' => [$this->roomB->id, $this->roomC->id],
        ]));

        $occupancyWithRooms = $calculator->calculateOccupancy(
            bookings: 1, blocks: 1, capacity: 6
        );

        $this->assertSame($occupancyWithRooms, $response->json('occupancy'));
    }

    public function test_it_should_return_occupancy_for_a_month()
    {
        $this->withoutExceptionHandling();
        $response = $this->get(route('monthly.booking', [
            'month' => now()->format('Y-m'),
        ]));

        $calculator = new Calculator();
        $occupancy = $calculator->calculateOccupancy(
            bookings: 26, blocks: 10, capacity: 12 * now()->month(now()->format('m'))->daysInMonth
        );

        $this->assertSame($occupancy, $response->json('occupancy'));

        $roomIdsResponse = $this->get(route('monthly.booking', [
            'month' => now()->format('Y-m'),
            'room_id' => [$this->roomB->id, $this->roomC->id],
        ]));

        $occupancyForRooms = $calculator->calculateOccupancy(
            bookings: 11, blocks: 10, capacity: 6 * now()->month(now()->format('m'))->daysInMonth
        );
        $this->assertSame($occupancyForRooms, $roomIdsResponse->json('occupancy'));
    }

    public function test_it_should_update_the_occupancy_when_booking_is_updated()
    {
        $booking = Booking::first();

        $booking->update([
            'starts_at' => now()->subDays(200),
            'ends_at' => now()->subDays(199),
        ]);

        $response = $this->get(route('monthly.booking', [
            'month' => now()->format('Y-m'),
        ]));

        $calculator = new Calculator();
        $occupancyForRooms = $calculator->calculateOccupancy(
            bookings: 20, blocks: 10, capacity: 12 * now()->month(now()->format('m'))->daysInMonth
        );

        $this->assertSame($occupancyForRooms, $response->json('occupancy'));
    }
}
