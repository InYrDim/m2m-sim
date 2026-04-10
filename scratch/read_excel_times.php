<?php

require 'vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\IOFactory;

$spreadsheet = IOFactory::load('UPDATE ROSTER_25_25.xlsx');
$sheet = $spreadsheet->getActiveSheet();

echo "Friday (JUM'AT) Details:\n";

for ($row = 70; $row <= 90; $row++) {
    $r = [
        'B' => $sheet->getCell('B' . $row)->getValue(),
        'C' => $sheet->getCell('C' . $row)->getValue(),
        'E' => $sheet->getCell('E' . $row)->getValue(),
    ];
    echo "Row $row: " . json_encode($r) . "\n";
}
