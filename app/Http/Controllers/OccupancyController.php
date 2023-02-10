<?php

namespace App\Http\Controllers;

use App\Http\Services\Calculator;
use App\Http\Services\OccupancyService;
use App\Models\Block;
use App\Models\Booking;
use App\Models\Room;
use Carbon\Carbon;

class OccupancyController extends Controller
{
    public function __construct(
       private readonly OccupancyService $service,
       private readonly Calculator $calculator
    ) {
    }

    public function daily()
    {
        $startDate = Carbon::createFromFormat('Y-m-d', request('day'))->startOfDay();
        $occupancy = $this->calculator->calculateOccupancy(
            bookings: $this->service->getOccupancyDates(Booking::class, $startDate, request('room_id') ?? []),
            blocks: $this->service->getOccupancyDates(Block::class, $startDate, request('room_id') ?? []),
            capacity: $this->service->roomCapacity(request('room_id') ?? [])
        );

        return response([
            'occupancy' => $occupancy,
        ]);
    }

    public function monthly()
    {
        $monthYear = explode('-', request('month'));
        $startDate = now()->startOfMonth()->setMonth((int) $monthYear[1]);

        $calculation = $this->calculator->calculateOccupancy(
            bookings: $this->service->getOccupancyForMonth(Booking::class, $startDate->month, request('room_id') ?? []),
            blocks: $this->service->getOccupancyForMonth(Block::class, $startDate->month, request('room_id') ?? []),
            capacity: $this->service->roomCapacity(request('room_id') ?? []) * $startDate->daysInMonth
        );

        return response([
            'occupancy' => $calculation,
        ]);
    }
}
