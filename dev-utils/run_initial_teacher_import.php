<?php

require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Services\TeacherImportService;

$service = new TeacherImportService();

// Generate the template content (which contains the Excel data I extracted)
$csvContent = $service->getTemplateContent();
$filePath = storage_path('app/initial_teacher_import.csv');
file_put_contents($filePath, $csvContent);

echo "Importing teachers from Excel data...\n";
$result = $service->import($filePath);

if (empty($result['errors'])) {
    echo "SUCCESS: " . $result['success'] . " teachers imported.\n";
} else {
    echo "ERRORS:\n";
    print_r($result['errors']);
}

unlink($filePath);
