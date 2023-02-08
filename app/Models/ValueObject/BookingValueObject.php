<?php

namespace App\Models\ValueObject;

use App\Models\Booking;

class BookingValueObject
{
    public function __construct(private readonly Booking $booking)
    {
    }

    public function getBooking(): Booking
    {
        return $this->booking;
    }
}
