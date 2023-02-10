# Booking Layer Test

The task from Booking Layer

## Assumptions
These have been the assumptions while working on the test application.
1. Room (can be blocked or booked)
   - id
   - capacity (integer)
2. Booking (is a room reservation, that takes 1 capacity. So room with 4 capacity can be booked 4 times for same date)
   - id
   - room_id
   - starts_at (date)
   - ends_at (date)
   
3. Block (that’s not booking it’s just a indicator that room is not available then, same as booking  one block takes only 1 capacity)-
   - id
   - room_id
   - starts_at (date)
   - ends_at (date)



## Solution formulation

Steps I thought of and executed for solving the given problem:

1. Prepare the migrations for the required tables
2. Seed the data so that we can formulate a query. Even though the document states the feasibility the 
of the test-taker to create/edit models. To my understanding adding a table or a field should not be added
unless really necessary.
3. Prepare necessary query
   - First Pass
     - Prepare query to filter bookings by given date/month/room_id
       - To prepare the monthly query for the first time i retrieved all the days and counted the booking 
       start date and end date summed up the (inclusive)difference which was a not an efficent approach. 
       ```php
        $booking = Booking::where(function ($query) use ($startDate) {
            
            $query->whereMonth('starts_at', '<=', $startDate->month);
            
            $query->whereMonth('ends_at', '>=', $startDate->month);
       
       })->get();
       
       $allBookingDays = 0;
       
       foreach ($booking as $allBookingDay) {
           
            $diff = Carbon::parse($allBookingDay->starts_at)->diffInDays(Carbon::parse($allBookingDay->ends_at)) + 1;
            
             $allBookingDays += $diff;
       }
       ```
     
       - Prepare query to filter blocking by given date/month/room_id
       - Prepare query to calculate room capacity
  - Second Pass
    - Improve the query to count the booking days
        - To improve the query I thought of delegating the task of calculation to database rather than the program itself,
      which came as:
      ```php 
        $this->getQuery(Booking::class)
            ->selectRaw(
                'sum(DATEDIFF(ADDDATE(ends_at, INTERVAL 1 DAY), starts_at))
                    as days_between'
            )->whereRaw('month(starts_at) <= ? and month(ends_at) >= ?', [
                $month, $month,
            ])
            ->when(! empty($roomId), function ($query) use ($roomId) {
                return $query->whereIn('room_id', $roomId);
            });
      ```
      The above query helped me to get the exclusive interval if I swapped it for `Booking`, `Block` which gave the 
    accurate results as above. Also the `when` method came handy here as we know the `room_id` os optional in requests.
    
    - Third Pass:
        - Encaupaltion of necessary logic into place so that the controller does not get bloated
        - Refactoring and removing un-necessary code.
      


## Libraries/Tools used
* No any third party library other than the framework itself provides.
* Uses php 8.1
* Uses phpunit for testing and xDebug 3.0 for code coverage analysis.

## Infrastructure
- Docker is used to run the application, which encapsulates all the necessary services.
## Installation

Run the following commands to set up the application, given that Docker is available in the host machine:

1. `git clone` repo
2. `docker-compose up -d`

## Running Tests
1. `vendor/bin/phpunit`:
1. For code coverage:  `vendor/bin/phpunit --coverage-text`
1. Additionally a report can also be generated using the command `vendor/bin/phpunit --coverage-html=reports`

## Usage of the project
The tests provide a basic overview of the application. Some steps can be done to see the application in action, which are.

1. To seed a basic data according to the document.
    1. `php artisan db:seed`
   2. Use the necessary routes to view the occupancy.

## Decisions, tradeoffs and constraints

1. I have not used any form request validation or json resource to show the response.
2. I have not also validated the get request, lets say if the room_ids is a valid array or not and the date/month are valid or not.
3. I tried to use bindings but somehow it was not possible/working in some places.
## Future Improvements.

It was a challenge. However, if I had to improve upon this:

1. I would work out to implement proper validation on places like Booking create, update, and get endpoints.
2. I would work on implementing proper bindings as it is a get request and there might be malicious attacks. 
3. I would also work on using `JsonResource` to send the response.
4. More testing can also be done, to ensure applications integrity and behaviour.
