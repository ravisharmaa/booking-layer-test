<?php

namespace App\Http\Repository\Room;

use App\Models\Room;
use Illuminate\Database\Eloquent\Builder;

abstract class RoomRepositoryAbstract
{
    protected function getQuery(): Builder
    {
        return Room::query();
    }

    abstract public function calculateRoomCapacity(?array $roomIds): int;
}
