<?php

namespace Tests\Feature;

use App\Models\Day;
use App\Models\Timeslot;
use App\Models\Classroom;
use App\Models\Lesson;
use App\Models\Teacher;
use App\Models\Timetable;
use App\Models\User;
use App\Services\TimetableImportService;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class TimetableImportTest extends TestCase
{
    /**
     * Use DatabaseTransactions so we don't wipe the real database.
     * All changes made during the test will be UNDONE at the end.
     */
    use DatabaseTransactions;

    private TimetableImportService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new TimetableImportService();
    }

    /** @test */
    public function it_can_import_valid_data()
    {
        // 1. Setup - Ensuring dependencies exist for the fixture
        // We use firstOrCreate so we don't hit duplicate key errors on Seeded data
        $day = Day::where('id', 1)->first() ?? Day::create(['id' => 1, 'name' => 'Senin']);
        
        $timeslot1 = Timeslot::firstOrCreate(['id' => 4], [
            'time_start' => '07:00:00', 
            'time_end' => '07:45:00', 
            'full_time' => '07:00-07:45'
        ]);
        $timeslot2 = Timeslot::firstOrCreate(['id' => 5], [
            'time_start' => '07:45:00', 
            'time_end' => '08:30:00', 
            'full_time' => '07:45-08:30'
        ]);
        
        $day->timeslots()->syncWithoutDetaching([
            $timeslot1->id => ['jam_ke' => 1],
            $timeslot2->id => ['jam_ke' => 2],
        ]);

        Classroom::firstOrCreate(['name' => 'X 1']);
        
        $lessonA = Lesson::firstOrCreate(['code' => 'A'], ['name' => 'Mapel A']);
        $lessonB = Lesson::firstOrCreate(['code' => 'B'], ['name' => 'Mapel B']);
        
        $user = User::first() ?? User::factory()->create();
        Teacher::firstOrCreate(['code' => '87'], [
            'name' => 'Guru Dewi',
            'lesson_id' => $lessonA->id,
            'user_id' => $user->id,
            'nip' => '0',
            'phone' => '0'
        ]);

        // 2. Import using the static fixture
        $filePath = base_path('tests/Fixtures/timetable_import.csv');
        $result = $this->service->import($filePath);
        
        // 3. Verifications
        if (!empty($result['errors'])) {
            $this->fail("Import failed with errors: " . implode(', ', $result['errors']));
        }
        
        $this->assertEquals(2, $result['success']);
        $this->assertDatabaseHas('timetables', [
            'day_id' => 1,
            'teacher_code' => '87',
            'lesson_id' => $lessonA->id
        ]);
    }

    /** @test */
    public function it_fails_if_hari_is_invalid()
    {
        // No setup needed as we test failures
        $csvContent = "hari,jam_ke,kelas,data\n999,1,X1,A.87";
        $filePath = storage_path('app/temp_fail_hari.csv');
        file_put_contents($filePath, $csvContent);

        $result = $this->service->import($filePath);
        $this->assertStringContainsString("Hari dengan ID '999' tidak ditemukan", $result['errors'][0]);
        
        unlink($filePath);
    }

    /** @test */
    public function it_fails_if_jam_ke_is_invalid()
    {
        $day = Day::firstOrCreate(['name' => 'Senin']);
        
        $csvContent = "hari,jam_ke,kelas,data\n{$day->id},999,X1,A.87";
        $filePath = storage_path('app/temp_fail_jam.csv');
        file_put_contents($filePath, $csvContent);

        $result = $this->service->import($filePath);
        $this->assertStringContainsString("belum dikonfigurasi di Master Jadwal", $result['errors'][0]);
        
        unlink($filePath);
    }

    /** @test */
    public function it_fails_if_kelas_is_invalid()
    {
        $day = Day::firstOrCreate(['name' => 'Senin']);
        
        $csvContent = "hari,jam_ke,kelas,data\n{$day->id},1,KELAS_MIMPI,A.87";
        $filePath = storage_path('app/temp_fail_kelas.csv');
        file_put_contents($filePath, $csvContent);

        $result = $this->service->import($filePath);
        $this->assertStringContainsString("Kelas 'KELAS_MIMPI' tidak ditemukan", $result['errors'][0]);
        
        unlink($filePath);
    }

    /** @test */
    public function it_fails_if_data_format_is_invalid()
    {
        $day = Day::firstOrCreate(['name' => 'Senin']);
        
        $csvContent = "hari,jam_ke,kelas,data\n{$day->id},1,X1,SALAHFORMAT";
        $filePath = storage_path('app/temp_fail_format.csv');
        file_put_contents($filePath, $csvContent);

        $result = $this->service->import($filePath);
        $this->assertStringContainsString("Gunakan format 'KODE_MAPEL.KODE_GURU'", $result['errors'][0]);
        
        unlink($filePath);
    }

    /** @test */
    public function it_fails_if_lesson_or_teacher_not_found()
    {
        $day = Day::firstOrCreate(['name' => 'Senin']);
        
        // Invalid Lesson
        $csvContent = "hari,jam_ke,kelas,data\n{$day->id},1,X1,MAPEL_PALSU.87";
        $filePath = storage_path('app/temp_fail_lesson.csv');
        file_put_contents($filePath, $csvContent);
        $result = $this->service->import($filePath);
        $this->assertStringContainsString("Kode Mapel 'MAPEL_PALSU' tidak terdaftar", $result['errors'][0]);
        unlink($filePath);

        // Invalid Teacher
        $csvContent = "hari,jam_ke,kelas,data\n{$day->id},1,X1,A.999";
        $filePath = storage_path('app/temp_fail_teacher.csv');
        file_put_contents($filePath, $csvContent);
        $result = $this->service->import($filePath);
        $this->assertStringContainsString("Kode Guru '999' tidak terdaftar", $result['errors'][0]);
        unlink($filePath);
    }

    /** @test */
    public function it_rolls_back_entire_transaction_on_single_error()
    {
        // 1. Setup valid basis
        $day = Day::firstOrCreate(['id' => 1], ['name' => 'Senin']);
        $timeslot = Timeslot::firstOrCreate(['id' => 4], ['full_time' => '07:00-07:45']);
        $day->timeslots()->syncWithoutDetaching([$timeslot->id => ['jam_ke' => 1]]);
        Classroom::firstOrCreate(['name' => 'X 1']);
        $lesson = Lesson::firstOrCreate(['code' => 'A'], ['name' => 'A']);
        Teacher::firstOrCreate(['code' => '87'], ['name' => 'Dewi', 'lesson_id' => $lesson->id, 'user_id' => User::first()->id]);

        $initialCount = Timetable::count();

        // 2. CSV with 1 good row and 1 fatal error row
        $csvContent = "hari,jam_ke,kelas,data\n";
        $csvContent .= "1,1,X1,A.87\n"; // Good
        $csvContent .= "1,1,X1,MAPEL_SALAH.87"; // Bad (Lesson not found)
        
        $filePath = storage_path('app/temp_rollback.csv');
        file_put_contents($filePath, $csvContent);

        $result = $this->service->import($filePath);
        
        $this->assertCount(1, $result['errors']);
        
        // 3. Assert database count is the same as before (rolled back)
        $this->assertEquals($initialCount, Timetable::count());
        
        unlink($filePath);
    }
}
