<?php

namespace App\Services;

use App\Models\Teacher;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Throwable;

class TeacherImportService
{
    /**
     * Import teachers from CSV
     */
    public function import(string $filePath): array
    {
        $handle = fopen($filePath, 'r');
        $header = fgetcsv($handle);
        
        $results = [
            'success' => 0,
            'errors' => []
        ];

        $rowCount = 1;
        
        DB::beginTransaction();
        try {
            while (($data = fgetcsv($handle)) !== false) {
                $rowCount++;
                
                // Skip empty rows
                if (empty(array_filter($data))) {
                    continue;
                }

                // Check if column counts match
                if (count($header) !== count($data)) {
                    $results['errors'][] = "Baris $rowCount: Jumlah kolom tidak sesuai (Diharapkan " . count($header) . ", ditemukan " . count($data) . "). Pastikan data tidak mengandung karakter koma yang tidak terbungkus tanda kutip.";
                    continue;
                }

                $row = array_combine($header, $data);
                
                $code = trim($row['code'] ?? '');
                $name = trim($row['name'] ?? '');
                $email = trim($row['email'] ?? '');
                if (empty($email)) {
                    $email = "guru.$code@man2kotamakassar.sch.id";
                }
                $nip = trim($row['nip'] ?? '');
                if (empty($nip) || $nip === '0') {
                    $nip = null;
                }
                $phone = trim($row['phone'] ?? '');
                if (empty($phone) || $phone === '0') {
                    $phone = null;
                }
                if (empty($code) || empty($name)) {
                    $results['errors'][] = "Baris $rowCount: Kode dan Nama wajib diisi.";
                    continue;
                }

                // 1. Create or Find User
                $user = User::where('email', $email)->first();
                if (!$user) {
                    $user = User::create([
                        'name' => $name,
                        'email' => $email,
                        'password' => Hash::make('man2kotamakassar'),
                    ]);
                    $user->assignRole('teacher');
                }

                // 2. Create or Update Teacher
                Teacher::updateOrCreate(
                    ['code' => $code],
                    [
                        'name' => $name,
                        'nip' => $nip,
                        'phone' => $phone,
                        'user_id' => $user->id,
                    ]
                );

                $results['success']++;
            }
            
            if (empty($results['errors'])) {
                DB::commit();
            } else {
                DB::rollBack();
                $results['success'] = 0;
            }
        } catch (Throwable $e) {
            DB::rollBack();
            $results['errors'][] = "Terjadi kesalahan sistem: " . $e->getMessage();
        }

        fclose($handle);
        return $results;
    }

    /**
     * Generate Template CSV Content Pre-filled from Excel data
     */
    public function getTemplateContent(): string
    {
        return "code,name,nip,phone,email\n";
    }
}
