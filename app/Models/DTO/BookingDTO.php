<?php

namespace App\Models\DTO;

use App\Models\Booking;
use App\Models\Room;
use App\Models\ValueObject\BookingValueObject;

class BookingDTO
{
    public function __construct(
        private readonly RoomDTO $roomDTO,
        private readonly string $startsAt,
        private readonly string $endsAt,
        private readonly ?BookingValueObject $bookingValueObject = null
    ) {
    }

    public function getRoom(): Room
    {
        return $this->roomDTO->getRoom();
    }

    public function getStartsAt(): string
    {
        return  $this->startsAt;
    }

    public function getEndsAt(): string
    {
        return $this->endsAt;
    }

    public function getBooking(): Booking
    {
        return $this->bookingValueObject?->getBooking();
    }
}
