<?php

namespace App\Http\Repository\Contract;

use App\Models\DTO\BookingDTO;
use App\Models\DTO\RoomDTO;

interface BookingRepositoryInterface
{
    public function create(BookingDTO $bookingDTO): void;

    public function update(BookingDTO $bookingDTO): void;

    public function findRoomById(int $roomId): RoomDTO;
}
