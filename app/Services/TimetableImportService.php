<?php

namespace App\Services;

use App\Models\Day;
use App\Models\Timeslot;
use App\Models\Classroom;
use App\Models\Lesson;
use App\Models\Teacher;
use App\Models\Timetable;
use Illuminate\Support\Facades\DB;

class TimetableImportService
{
    public function import($filePath)
    {
        $rows = array_map('str_getcsv', file($filePath));
        $header = array_shift($rows);

        $results = [
            'success' => 0,
            'errors' => []
        ];

        DB::beginTransaction();
        try {
            foreach ($rows as $index => $row) {
                // Ensure row has enough columns
                if (count($row) < 4) {
                    $results['errors'][] = "Baris " . ($index + 2) . ": Format baris tidak lengkap (Minimal 4 kolom).";
                    continue;
                }

                $dayNum = trim($row[0]);
                $jamKe = trim($row[1]);
                $kelasName = trim($row[2]);
                $data = trim($row[3]); // e.g. ST.92

                // 1. Validate 'hari'
                if (empty($dayNum) || !is_numeric($dayNum)) {
                    $results['errors'][] = "Baris " . ($index + 2) . ": Kolom 'hari' harus berisi angka.";
                    continue;
                }
                $dayId = (int)$dayNum;
                if (!Day::where('id', $dayId)->exists()) {
                    $results['errors'][] = "Baris " . ($index + 2) . ": Hari dengan ID '$dayNum' tidak ditemukan.";
                    continue;
                }

                // 2. Map Classroom
                if (empty($kelasName)) {
                    $results['errors'][] = "Baris " . ($index + 2) . ": Kolom 'kelas' tidak boleh kosong.";
                    continue;
                }
                $classroom = $this->findClassroom($kelasName);
                if (!$classroom) {
                    $results['errors'][] = "Baris " . ($index + 2) . ": Kelas '$kelasName' tidak ditemukan di database.";
                    continue;
                }

                // 3. Map Timeslot via jam_ke
                if (empty($jamKe) || !is_numeric($jamKe)) {
                    $results['errors'][] = "Baris " . ($index + 2) . ": Kolom 'jam_ke' harus berisi angka.";
                    continue;
                }
                $timeslotId = $this->findTimeslotId($dayId, $jamKe);
                if (!$timeslotId) {
                    $results['errors'][] = "Baris " . ($index + 2) . ": Jam ke-$jamKe pada hari " . Day::find($dayId)->name . " belum dikonfigurasi di Master Jadwal.";
                    continue;
                }

                // 4. Parse Lesson & Teacher (e.g. ST.92)
                if (empty($data)) {
                    $results['errors'][] = "Baris " . ($index + 2) . ": Kolom 'id_pelajaran_guru' tidak boleh kosong.";
                    continue;
                }
                $parsed = $this->parseData($data);
                if (!$parsed) {
                    $results['errors'][] = "Baris " . ($index + 2) . ": Format '$data' salah. Gunakan format 'KODE_MAPEL.KODE_GURU' (Contoh: A.87).";
                    continue;
                }

                $lesson = Lesson::where('code', $parsed['lesson_code'])->first();
                if (!$lesson) {
                    $results['errors'][] = "Baris " . ($index + 2) . ": Kode Mapel '{$parsed['lesson_code']}' tidak terdaftar.";
                    continue;
                }

                $teacherExists = Teacher::where('code', $parsed['teacher_code'])->exists();
                if (!$teacherExists) {
                    $results['errors'][] = "Baris " . ($index + 2) . ": Kode Guru '{$parsed['teacher_code']}' tidak terdaftar.";
                    continue;
                }

                // 5. Save Timetable
                Timetable::updateOrCreate(
                    [
                        'day_id' => $dayId,
                        'timeslot_id' => $timeslotId,
                        'classroom_id' => $classroom->id,
                    ],
                    [
                        'lesson_id' => $lesson->id,
                        'teacher_code' => $parsed['teacher_code'],
                    ]
                );

                $results['success']++;
            }

            if (count($results['errors']) > 0) {
                DB::rollBack();
            } else {
                DB::commit();
            }
        } catch (\Exception $e) {
            DB::rollBack();
            $results['errors'][] = "System Error: " . $e->getMessage();
        }

        return $results;
    }

    private function findClassroom($name)
    {
        // Handle "X12" -> "X 12"
        $formattedName = $name;
        if (preg_match('/^([XI]+)(\d+)$/', strtoupper($name), $matches)) {
            $formattedName = $matches[1] . ' ' . $matches[2];
        }

        return Classroom::where('name', $formattedName)->first();
    }

    private function findTimeslotId($dayId, $jamKe)
    {
        $day = Day::find($dayId);
        if (!$day) return null;

        $timeslot = $day->timeslots()
            ->wherePivot('jam_ke', $jamKe)
            ->first();

        return $timeslot ? $timeslot->id : null;
    }

    private function parseData($data)
    {
        $parts = explode('.', $data);
        if (count($parts) < 2) return null;

        return [
            'lesson_code' => trim($parts[0]),
            'teacher_code' => trim($parts[1])
        ];
    }
}
