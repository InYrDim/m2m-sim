<?php

require 'vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\IOFactory;

$spreadsheet = IOFactory::load('UPDATE ROSTER_25_25.xlsx');

$allTeachers = [];

foreach ($spreadsheet->getSheetNames() as $sheetName) {
    $sheet = $spreadsheet->getSheetByName($sheetName);
    // Scan column V and W which we found earlier
    $highestRow = $sheet->getHighestRow();
    for ($row = 1; $row <= $highestRow; $row++) {
        $code = trim($sheet->getCell('V' . $row)->getValue() ?? '');
        $name = trim($sheet->getCell('W' . $row)->getValue() ?? '');
        
        // Basic heuristic for teacher code (numeric or short string) and long name
        if (!empty($code) && !empty($name) && $code != 'KODE' && $code != 'KODE GURU') {
            if (is_numeric($code) || (strlen($code) <= 10 && !str_contains($code, ' '))) {
                 $allTeachers[$code] = $name;
            }
        }
    }
}

ksort($allTeachers, SORT_NATURAL);

echo "Extracted Teacher CSV Data:\n";
echo "code,name,nip,phone,email\n";
foreach ($allTeachers as $code => $name) {
    echo "$code,\"$name\",0,0,guru.$code@man2kotamakassar.sch.id\n";
}
