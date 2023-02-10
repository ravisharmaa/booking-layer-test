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
            'occupancy' => $occupancy
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
            'occupancy' => $calculation
        ]);
    }

    public function occupancyRateForMonth()
    {
        $capacity = Room::sum('capacity');
        $monthYear = explode('-', request('month'));
        $startDate = now()->startOfMonth()->setMonth((int) $monthYear[1]);
        $block = Block::query()->selectRaw(
            'sum(DATEDIFF(ADDDATE(ends_at, INTERVAL 1 DAY), starts_at))
                    as days_between'
        )->whereRaw('month(starts_at) <= :starts_at and month(ends_at) >= :ends_at')
        ->setBindings([
            'starts_at' => $startDate->month,
            'ends_at' => $startDate->month,
        ]);

        $book = Booking::query()->selectRaw(
            'sum(DATEDIFF(ADDDATE(ends_at, INTERVAL 1 DAY), starts_at))
                    as days_between'
        )->whereRaw('month(starts_at) <= :starts_at and month(ends_at) >= :ends_at')
            ->setBindings([
                'starts_at' => $startDate->month,
                'ends_at' => $startDate->month,
            ]);

        $booking = Booking::where(function ($query) use ($startDate) {
            $query->whereMonth('starts_at', '<=', $startDate->month);
            $query->whereMonth('ends_at', '>=', $startDate->month);
        })->get();
        $allBookingDays = 0;
        foreach ($booking as $allBookingDay) {
            $diff = Carbon::parse($allBookingDay->starts_at)->diffInDays(Carbon::parse($allBookingDay->ends_at)) + 1;
            $allBookingDays += $diff;
        }

        $blocksCount = Block::where(function ($query) use ($startDate) {
            $query->whereMonth('starts_at', '<=', $startDate->month);
            $query->whereMonth('ends_at', '>=', $startDate->month);
        })->get();

        $allBlockedDays = 0;
        foreach ($blocksCount as $blockCount) {
            $blockedActualDays = Carbon::parse($blockCount->starts_at)->diffInDays(Carbon::parse($blockCount->ends_at)) + 1;
            $allBlockedDays += $blockedActualDays;
        }

        $occupancy = $allBookingDays / ($capacity * $startDate->daysInMonth - $allBlockedDays);
        $anotherOccupancy = $book->first()->days_between / ($capacity * $startDate->daysInMonth - $block->first()->days_between);
        dump($anotherOccupancy);
        dump($occupancy);
    }
}
