<?php

namespace App\Http\Controllers;

use App\Http\Services\Calculator;
use App\Http\Services\OccupancyService;
use App\Models\Block;
use App\Models\Booking;
use Carbon\Carbon;
use Carbon\Exceptions\InvalidFormatException;
use Symfony\Component\HttpFoundation\Response;

class OccupancyController extends Controller
{
    public function __construct(
       private readonly OccupancyService $service,
       private readonly Calculator $calculator
    ) {
    }

    public function daily(): Response
    {
        try {
            $startDate = Carbon::createFromFormat('Y-m-d', request('day'))->startOfDay();
        } catch (InvalidFormatException $exception) {
            info($exception);

            return response(['invalid date format'], 422);
        }

        return response([
            'occupancy' => $this->calculator->calculateOccupancy(
                bookings: $this->service->getOccupancyDates(Booking::class, $startDate, request('room_id') ?? []),
                blocks: $this->service->getOccupancyDates(Block::class, $startDate, request('room_id') ?? []),
                capacity: $this->service->roomCapacity(request('room_id') ?? [])
            ),
        ]);
    }

    public function monthly(): Response
    {
        try {
            $monthYear = explode('-', request('month'));
            $startDate = now()->startOfMonth()->setMonth((int) $monthYear[1]);
        } catch (InvalidFormatException $exception) {
            info($exception);

            return response(['invalid month format'], 422);
        }

        return response([
            'occupancy' => $this->calculator->calculateOccupancy(
                bookings: $this->service->getOccupancyForMonth(Booking::class, $startDate->month, request('room_id') ?? []),
                blocks: $this->service->getOccupancyForMonth(Block::class, $startDate->month, request('room_id') ?? []),
                capacity: $this->service->roomCapacity(request('room_id') ?? []) * $startDate->daysInMonth
            ),
        ]);
    }
}
