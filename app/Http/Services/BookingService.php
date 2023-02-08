<?php

namespace App\Http\Services;

use App\Http\Repository\Contract\BookingRepositoryInterface;
use App\Models\DTO\BookingDTO;
use App\Models\DTO\RoomDTO;

class BookingService
{
    public function __construct(private readonly BookingRepositoryInterface $bookingRepository)
    {
    }

    public function create(BookingDTO $bookingDTO): void
    {
        $this->bookingRepository->create($bookingDTO);
    }

    public function update(BookingDTO $bookingDTO): void
    {
        $this->bookingRepository->update($bookingDTO);
    }

    public function findRoom(int $roomId): RoomDTO
    {
        return $this->bookingRepository->findRoomById($roomId);
    }
}
