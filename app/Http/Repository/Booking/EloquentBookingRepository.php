<?php

namespace App\Http\Repository\Booking;

use App\Http\Repository\Contract\BookingRepositoryInterface;
use App\Models\DTO\BookingDTO;
use App\Models\DTO\RoomDTO;
use App\Models\Room;

class EloquentBookingRepository implements BookingRepositoryInterface
{
    public function create(BookingDTO $bookingDTO): void
    {
        $bookingDTO->getRoom()->bookings()->create([
            'starts_at' => $bookingDTO->getStartsAt(),
            'ends_at' => $bookingDTO->getEndsAt(),
        ]);
    }

    public function update(BookingDTO $bookingDTO): void
    {
        $bookingDTO->getBooking()->update([
            'starts_at' => $bookingDTO->getStartsAt(),
            'ends_at' => $bookingDTO->getEndsAt(),
        ]);
    }

    public function findRoomById(int $roomId): RoomDTO
    {
        return new RoomDTO(Room::findOrFail($roomId));
    }
}
