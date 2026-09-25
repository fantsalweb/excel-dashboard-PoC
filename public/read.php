<?php

require_once __DIR__ . '/../vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\IOFactory;

$file = __DIR__ . '/../uploads/CONTROL DE PESO Y TENSIÓN.xlsx';

$spreadsheet = IOFactory::load($file);

$worksheet = $spreadsheet->getSheetByName('Histórico');

echo '<h1>Histórico: A8:V15</h1>';

echo '<table border="1" cellpadding="5" cellspacing="0">';

for ($row = 8; $row <= 15; $row++) {

    echo '<tr>';

    for ($column = 1; $column <= 22; $column++) {

        $coordinate = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($column) . $row;

        $cell = $worksheet->getCell($coordinate);

        $value = $cell->getDataType() === 'f'
            ? $cell->getCalculatedValue()
            : $cell->getValue();

        if ($row === 8) {
            echo '<th>';
        } else {
            echo '<td>';
        }

        echo htmlspecialchars((string) $value);

        if ($row === 8) {
            echo '</th>';
        } else {
            echo '</td>';
        }
    }

    echo '</tr>';
}

echo '</table>';