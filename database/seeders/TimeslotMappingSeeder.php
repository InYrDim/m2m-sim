<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Day;
use App\Models\Timeslot;
use Illuminate\Support\Facades\DB;

class TimeslotMappingSeeder extends Seeder
{
    public function run(): void
    {
        // Clear existing mappings to avoid duplicates
        DB::table('day_time')->truncate();

        $schedule = [
            // SENIN - KAMIS (Days 1-4)
            'regular' => [
                1 => ['07:00', '07:45'],
                2 => ['07:45', '08:30'],
                3 => ['08:30', '09:15'],
                4 => ['09:15', '10:00'],
                5 => ['10:30', '11:15'],
                6 => ['11:15', '12:00'],
                7 => ['12:30', '13:10'],
                8 => ['13:10', '13:50'],
                9 => ['13:50', '14:30'],
                10 => ['14:30', '15:10'],
                11 => ['15:10', '15:50'],
            ],
            // JUMAT (Day 5)
            'friday' => [
                1 => ['07:30', '08:10'],
                2 => ['08:10', '08:50'],
                3 => ['08:50', '09:30'],
                4 => ['09:30', '10:10'],
                5 => ['10:30', '11:15'],
                6 => ['11:15', '12:00'],
                7 => ['13:00', '13:45'],
                8 => ['13:45', '14:30'],
                9 => ['14:30', '15:15'],
                10 => ['15:15', '16:00'],
            ]
        ];

        // Process Days 1-4 (Monday - Thursday)
        for ($dayId = 1; $dayId <= 4; $dayId++) {
            $this->mapDay($dayId, $schedule['regular']);
        }

        // Process Day 5 (Friday)
        $this->mapDay(5, $schedule['friday']);
    }

    private function mapDay($dayId, $periods)
    {
        $day = Day::find($dayId);
        if (!$day) return;

        foreach ($periods as $jamKe => $times) {
            $timeslot = Timeslot::firstOrCreate([
                'time_start' => $times[0],
                'time_end' => $times[1],
            ], [
                'full_time' => $times[0] . '-' . $times[1]
            ]);

            $day->timeslots()->attach($timeslot->id, ['jam_ke' => $jamKe]);
        }
    }
}
