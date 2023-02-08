<?php

namespace App\Models\DTO;

use App\Models\Room;

class RoomDTO
{
    public function __construct(
        private readonly Room $room
    ) {
    }

    public function getRoom(): Room
    {
        return $this->room;
    }
}
