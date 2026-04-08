<?php

namespace Database\Seeders;

use App\Models\Classroom;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ClassroomSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $levels = ['x', 'xi', 'xii'];

        $numbers = [
            '1', '2', '3', '4', '5', '6', '7', '8', '9', '10', '11', '12'
        ];

        foreach ($levels as $level) {
            foreach ($numbers as $number) {
                Classroom::insert([
                    'level' => $level,
                    'name' => strtoupper($level) . ' ' . $number,
                ]);
            }
        }
    }
}
