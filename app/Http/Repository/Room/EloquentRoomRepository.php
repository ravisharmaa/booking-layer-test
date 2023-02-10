<?php

namespace App\Http\Repository\Room;

class EloquentRoomRepository extends RoomRepositoryAbstract
{
    public function calculateRoomCapacity(?array $roomIds = []): int
    {
        return $this->getQuery()->when(! empty($roomIds), function ($query) use ($roomIds) {
            $query->whereIn('id', $roomIds);
        })->sum('capacity');
    }
}
