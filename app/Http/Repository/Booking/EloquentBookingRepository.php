<?php

namespace App\Http\Repository\Booking;

use App\Http\Repository\Contract\BookingRepositoryInterface;
use App\Models\DTO\BookingDTO;
use App\Models\DTO\RoomDTO;
use App\Models\Room;
use Illuminate\Database\Eloquent\Builder;

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

    private function getQuery(string $class): Builder
    {
        return resolve($class)::query();
    }

    public function getOccupancyForDate(
        string $forModel,
        string $startDate, ?array $roomIds = null
    ): Builder {
        return $this->getQuery($forModel)->where(function ($query) use ($startDate) {
            $query->whereDate('starts_at', '<=', $startDate);
            $query->whereDate('ends_at', '>=', $startDate);
        })->when(! empty($roomIds), function ($query) use ($roomIds) {
            return $query->whereIn('room_id', $roomIds);
        });
    }

    public function getOccupancyForMonth(
        string $forModel,
        string $month,
        ?array $roomId = []
    ): Builder {
        return $this->getQuery($forModel)
            ->selectRaw(
                'sum(DATEDIFF(ADDDATE(ends_at, INTERVAL 1 DAY), starts_at))
                    as days_between'
            )->whereRaw('month(starts_at) <= ? and month(ends_at) >= ?', [
                $month, $month,
            ])
            ->when(! empty($roomId), function ($query) use ($roomId) {
                return $query->whereIn('room_id', $roomId);
            });
    }
}
