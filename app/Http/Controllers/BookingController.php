<?php

namespace App\Http\Controllers;

use App\Http\Services\BookingService;
use App\Models\Booking;
use App\Models\DTO\BookingDTO;
use App\Models\DTO\RoomDTO;
use App\Models\ValueObject\BookingValueObject;
use Illuminate\Http\Response;

class BookingController extends Controller
{
    public function __construct(private readonly BookingService $service)
    {
    }

    public function store(): Response
    {
        try {
            $this->service->create(
                new BookingDTO(
                    roomDTO: $this->service->findRoom(request('room_id')),
                    startsAt: request('starts_at'),
                    endsAt: request('ends_at')
                )
            );
        } catch (\Exception $exception) {
            info('Error while booking a room:', $exception);

            return response(['message' => 'Could not add booking'], 500);
        }

        return response()->noContent(201);
    }

    public function update(Booking $booking): Response
    {
        try {
            $this->service->update(new BookingDTO(
                roomDTO: new RoomDTO($booking->room),
                startsAt: request('starts_at'),
                endsAt: request('ends_at'),
                bookingValueObject: new BookingValueObject($booking)
            ));
        } catch (\Exception $exception) {
            info('Error while booking a room:', $exception);

            return response(['message' => 'Could not update booking'], 500);
        }

        return response()->noContent(200);
    }
}
