<?php

namespace Database\Seeders;

use App\Models\Timeslot;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class TimeslotSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Timeslot::insert([
            ['time_start' => '06:30', 'time_end' => "07:00", 'full_time' => '06:30-07:00'],
            ['time_start' => '06:30', 'time_end' => "07:15", 'full_time' => '06:30-07:15'],
            ['time_start' => '07:00', 'time_end' => "07:40", 'full_time' => '07:00-07:40'],
            ['time_start' => '07:00', 'time_end' => "07:45", 'full_time' => '07:00-07:45'],
            ['time_start' => '07:15', 'time_end' => "08:00", 'full_time' => '07:15-08:00'],
            ['time_start' => '07:40', 'time_end' => "08:20", 'full_time' => '07:40-08:20'],
            ['time_start' => '07:45', 'time_end' => "08:30", 'full_time' => '07:45-08:30'],
            ['time_start' => '08:00', 'time_end' => "08:45", 'full_time' => '08:00-08:45'],
            ['time_start' => '08:20', 'time_end' => "09:00", 'full_time' => '08:20-09:00'],
            ['time_start' => '08:30', 'time_end' => "09:15", 'full_time' => '08:30-09:15'],
            ['time_start' => '08:45', 'time_end' => "09:30", 'full_time' => '08:45-09:30'],
            ['time_start' => '09:00', 'time_end' => "09:40", 'full_time' => '09:00-09:40'],
            ['time_start' => '09:15', 'time_end' => "10:00", 'full_time' => '09:15-10:00'],
            ['time_start' => '09:30', 'time_end' => "10:15", 'full_time' => '09:30-10:15'],
            ['time_start' => '09:40', 'time_end' => "10:20", 'full_time' => '09:40-10:20'],
            ['time_start' => '10:00', 'time_end' => "10:30", 'full_time' => '10:00-10:30'],
            ['time_start' => '10:15', 'time_end' => "10:35", 'full_time' => '10:15-10:35'],
            ['time_start' => '10:20', 'time_end' => "11:00", 'full_time' => '10:20-11:00'],
            ['time_start' => '10:30', 'time_end' => "11:15", 'full_time' => '10:30-11:15'],
            ['time_start' => '10:35', 'time_end' => "11:15", 'full_time' => '10:35-11:15'],
            ['time_start' => '11:00', 'time_end' => "11:40", 'full_time' => '11:00-11:40'],
            ['time_start' => '11:15', 'time_end' => "12:00", 'full_time' => '11:15-12:00'],
            ['time_start' => '11:40', 'time_end' => "12:30", 'full_time' => '11:40-12:30'],
            ['time_start' => '12:00', 'time_end' => "12:30", 'full_time' => '12:00-12:30'],
            ['time_start' => '12:00', 'time_end' => "13:00", 'full_time' => '12:00-13:00'],
            ['time_start' => '12:30', 'time_end' => "13:10", 'full_time' => '12:30-13:10'],
            ['time_start' => '12:30', 'time_end' => "13:15", 'full_time' => '12:30-13:15'],
            ['time_start' => '13:00', 'time_end' => "13:45", 'full_time' => '13:00-13:45'],
            ['time_start' => '13:10', 'time_end' => "13:50", 'full_time' => '13:10-13:50'],
            ['time_start' => '13:15', 'time_end' => "14:00", 'full_time' => '13:15-14:00'],
            ['time_start' => '13:45', 'time_end' => "14:30", 'full_time' => '13:45-14:30'],
            ['time_start' => '13:50', 'time_end' => "14:30", 'full_time' => '13:50-14:30'],
            ['time_start' => '14:00', 'time_end' => "14:45", 'full_time' => '14:00-14:45'],
            ['time_start' => '14:30', 'time_end' => "15:10", 'full_time' => '14:30-15:10'],
            ['time_start' => '14:30', 'time_end' => "15:15", 'full_time' => '14:30-15:15'],
            ['time_start' => '14:45', 'time_end' => "15:30", 'full_time' => '14:45-15:30'],
            ['time_start' => '15:10', 'time_end' => "15:50", 'full_time' => '15:10-15:50'],
            ['time_start' => '15:15', 'time_end' => "16:00", 'full_time' => '15:15-16:00'],
            ['time_start' => '15:30', 'time_end' => "16:00", 'full_time' => '15:30-16:00'],
            ['time_start' => '15:50', 'time_end' => "16:30", 'full_time' => '15:50-16:30'],
            ['time_start' => '16:00', 'time_end' => "16:30", 'full_time' => '16:00-16:30'],
        ]);
    }
}
