<?php

namespace Tests\Feature;

use App\Models\Teacher;
use App\Models\User;
use App\Services\TeacherImportService;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class TeacherImportTest extends TestCase
{
    use DatabaseTransactions;

    private TeacherImportService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new TeacherImportService();
    }

    /** @test */
    public function it_can_import_teachers_and_create_users()
    {
        $csvContent = "code,name,nip,phone,email\n";
        $csvContent .= "999,Teacher Name,12345,08123,teacher@example.com";
        
        $filePath = storage_path('app/temp_teacher_import.csv');
        file_put_contents($filePath, $csvContent);

        $result = $this->service->import($filePath);

        $this->assertEquals(1, $result['success']);
        $this->assertEmpty($result['errors']);

        // Check Teacher
        $this->assertDatabaseHas('teachers', [
            'code' => '999',
            'name' => 'Teacher Name',
            'nip' => '12345'
        ]);

        // Check User
        $this->assertDatabaseHas('users', [
            'email' => 'teacher@example.com',
            'name' => 'Teacher Name'
        ]);

        $user = User::where('email', 'teacher@example.com')->first();
        $this->assertTrue($user->hasRole('teacher'));

        unlink($filePath);
    }
    
    /** @test */
    public function it_generates_default_email_if_missing()
    {
        $csvContent = "code,name,nip,phone,email\n";
        $csvContent .= "888,Another Teacher,0,0,";
        
        $filePath = storage_path('app/temp_teacher_email.csv');
        file_put_contents($filePath, $csvContent);

        $result = $this->service->import($filePath);
        if (!empty($result['errors'])) {
            print_r($result['errors']);
        }

        $this->assertDatabaseHas('users', [
            'email' => 'guru.888@man2kotamakassar.sch.id'
        ]);

        unlink($filePath);
    }
    /** @test */
    public function it_handles_mismatched_column_counts()
    {
        $csvContent = "code,name,nip,phone,email\n";
        $csvContent .= "111,Valid Teacher,0,0,valid@example.com\n";
        $csvContent .= "222,Invalid Teacher,0,0\n"; // Missing email column entirely
        $csvContent .= "\n"; // Empty row
        
        $filePath = storage_path('app/temp_teacher_mismatch.csv');
        file_put_contents($filePath, $csvContent);

        $result = $this->service->import($filePath);

        // Transaction should roll back if any error occurs
        $this->assertEquals(0, $result['success']);
        $this->assertNotEmpty($result['errors']);
        $this->assertStringContainsString("Jumlah kolom tidak sesuai", $result['errors'][0]);

        unlink($filePath);
    }
}
