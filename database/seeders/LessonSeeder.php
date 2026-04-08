<?php

namespace Database\Seeders;

use App\Models\Lesson;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class LessonSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Lesson::insert([
            ['code' => 'A', 'name' => "Qur'an Hadits"],
            ['code' => 'B', 'name' => 'Aqidah Akhlak'],
            ['code' => 'C', 'name' => 'Fiqhi'],
            ['code' => 'D', 'name' => 'SKI'],
            ['code' => 'E', 'name' => 'PKn'],
            ['code' => 'F', 'name' => 'Bahasa Indonesia'],
            ['code' => 'G', 'name' => 'Bahasa Arab'],
            ['code' => 'GP', 'name' => 'Bahasa Arab Peminatan'],
            ['code' => 'H', 'name' => 'Bahasa Inggris'],
            ['code' => 'HL', 'name' => 'Bahasa Inggris Peminatan'],
            ['code' => 'I', 'name' => 'Matematika'],
            ['code' => 'IP', 'name' => 'Matematika Peminatan'],
            ['code' => 'J', 'name' => 'Fisika'],
            ['code' => 'JL', 'name' => 'Fisika Lintas Minat'],
            ['code' => 'K', 'name' => 'Biologi'],
            ['code' => 'KL', 'name' => 'Biologi Lintas Minat'],
            ['code' => 'L', 'name' => 'Kimia'],
            ['code' => 'LL', 'name' => 'Kimia Lintas Minat'],
            ['code' => 'M', 'name' => 'Sejarah'],
            ['code' => 'MP', 'name' => 'Sejarah Peminatan'],
            ['code' => 'N', 'name' => 'Ekonomi'],
            ['code' => 'NL', 'name' => 'Ekonomi Lintas Minat'],
            ['code' => 'O', 'name' => 'Geografi'],
            ['code' => 'OL', 'name' => 'Geografi Lintas Minat'],
            ['code' => 'P', 'name' => 'Sosiologi'],
            ['code' => 'Q', 'name' => 'Seni Budaya'],
            ['code' => 'R', 'name' => 'Penjaskes'],
            ['code' => 'S', 'name' => 'TIK'],
            ['code' => 'T', 'name' => 'Bahasa Asing'],
            ['code' => 'U', 'name' => 'Prakarya'],
            ['code' => 'V', 'name' => 'Ilmu Tafsir'],
            ['code' => 'W', 'name' => 'Ushul Fiqhi'],
            ['code' => 'X', 'name' => 'Ilmu Hadits'],
            ['code' => 'Y', 'name' => 'Akhlak'],
            ['code' => 'Z', 'name' => 'Fiqhi Ushul Fiqhi'],

            ['code' => 'GC', 'name' => 'Geografi Club'],
            ['code' => 'HC', 'name' => 'Bahasa Inggris Club'],
            ['code' => 'IC', 'name' => 'Matematika Club'],
            ['code' => 'JC', 'name' => 'Fisika CLub'],
            ['code' => 'LC', 'name' => 'Kimia Club'],
            ['code' => 'KC', 'name' => 'Biologi Club'],
            ['code' => 'NC', 'name' => 'Ekonomi CLub'],
            ['code' => 'SC', 'name' => 'Jepang Club'],
            ['code' => 'TC', 'name' => 'Jerman Club'],
            ['code' => 'TIK', 'name' => 'Robotik Club'],
            ['code' => 'QC', 'name' => 'Seni Budaya Club'],

            ['code' => '1', 'name' => 'Upacara'],
            ['code' => '2', 'name' => 'Istirahat'],
            ['code' => '3', 'name' => 'Shalat Dhuhur'],
            ['code' => '4', 'name' => 'Shalat Ashar'],
            ['code' => '5', 'name' => 'Tadarrus'],
            ['code' => '6', 'name' => "Sima'an/Penguatan Bahasa"],
            ['code' => '7', 'name' => "BK/Wali Kelas"],
            ['code' => '8', 'name' => "Jum'at Bersih/Senam"],
        ]);
    }
}
