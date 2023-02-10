<?php

namespace App\Http\Repository\Booking;

use App\Models\DTO\BookingDTO;
use App\Models\DTO\RoomDTO;
use Illuminate\Database\Eloquent\Builder;

interface BookingRepositoryInterface
{
    public function create(BookingDTO $bookingDTO): void;

    public function update(BookingDTO $bookingDTO): void;

    public function findRoomById(int $roomId): RoomDTO;

    public function getOccupancyForDate(string $forModel, string $startDate, ?array $roomIds): Builder;

    public function getOccupancyForMonth(string $forModel, string $month, ?array $roomId = []): Builder;
}
