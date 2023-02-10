<?php

namespace Tests\Unit;

use App\Http\Services\Calculator;
use PHPUnit\Framework\TestCase;

class OccupancyCalculatorTest extends TestCase
{
    /**
     * @dataProvider calculationDataProvider
     */
    public function test_it_should_calculate_occupancy($data)
    {
        $calculator = new Calculator();
        $this->assertSame($calculator->calculateOccupancy(
            bookings: $data['booking'],
            blocks: $data['block'],
            capacity: $data['capacity']
        ), $data['expected']);
    }

    public function calculationDataProvider(): \Generator
    {
        yield 'calculation with 4 booking' => [
            [
                'booking' => 4,
                'block' => 1,
                'capacity' => 12,
                'expected' => 0.36,
            ],
        ];
    }
}
