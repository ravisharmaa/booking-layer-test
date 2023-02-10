<?php

namespace App\Http\Services;

class Calculator
{
    public function calculateOccupancy(
        int $bookings,
        int $blocks,
        int $capacity
    ): float
    {
        return round($bookings / ($capacity - $blocks), 2);
    }
}
