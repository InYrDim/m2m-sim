<?php

require 'vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\IOFactory;

$spreadsheet = IOFactory::load('UPDATE ROSTER_25_25.xlsx');
$sheet = $spreadsheet->getSheet(0); // ROSTER XII

echo "Teacher List starting at V40:\n";

for ($row = 40; $row <= 150; $row++) {
    $code = $sheet->getCell('V' . $row)->getValue();
    $name = $sheet->getCell('W' . $row)->getValue();
    if (!empty($code) || !empty($name)) {
        echo "Row $row: Code: [$code] | Name: [$name]\n";
    }
}
