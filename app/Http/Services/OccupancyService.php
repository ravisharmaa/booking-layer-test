<?php

namespace App\Http\Services;

use App\Http\Repository\Contract\BookingRepositoryInterface;
use App\Http\Repository\Room\RoomRepositoryAbstract;

class OccupancyService
{
    public function __construct(
        private readonly BookingRepositoryInterface $bookingRepository,
        private readonly RoomRepositoryAbstract $repositoryAbstract
    ) {
    }

    public function roomCapacity(?array $roomId = []): int
    {
        return $this->repositoryAbstract->calculateRoomCapacity($roomId);
    }

    public function getOccupancyDates(string $forModel, string $startDate, ?array $roomIds = []): int
    {
        return $this->bookingRepository->getOccupancyForDate($forModel, $startDate, $roomIds)->count();
    }

    public function getOccupancyForMonth(
        string $forModel,
        string $startMonth,
        ?array $roomIds = []
    ): ?int
    {
        return (int)$this->bookingRepository->getOccupancyForMonth(
            $forModel, $startMonth, $roomIds
        )->first()?->days_between;
    }
}
