<?php

namespace App\Providers;

use App\Http\Repository\Booking\EloquentBookingRepository;
use App\Http\Repository\Contract\BookingRepositoryInterface;
use App\Http\Repository\Room\EloquentRoomRepository;
use App\Http\Repository\Room\RoomRepositoryAbstract;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        $this->app->bind(BookingRepositoryInterface::class, function () {
            return resolve(EloquentBookingRepository::class);
        });

        $this->app->bind(RoomRepositoryAbstract::class, function () {
            return resolve(EloquentRoomRepository::class);
        });
    }
}
