<?php
session_start();

require_once __DIR__ . '/../vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;

// Comprobaciones
if (empty($_SESSION['excel']['path']) || !file_exists($_SESSION['excel']['path'])) {
    die('No hay ningún archivo Excel cargado. <a href="index.php">Volver</a>');
}

if (empty($_SESSION['selected_sheets']) || empty($_SESSION['sheet_configs'])) {
    die('Configuración incompleta. <a href="select-sheets.php">Volver</a>');
}

$selectedSheets = $_SESSION['selected_sheets'];
$configs = $_SESSION['sheet_configs'];
$currentSheet = $_GET['sheet'] ?? $selectedSheets[0];

// Validamos que la hoja actual exista en las seleccionadas
if (!in_array($currentSheet, $selectedSheets)) {
    $currentSheet = $selectedSheets[0];
}

$config = $configs[$currentSheet] ?? null;
if (!$config || empty($config['header_row']) || empty($config['start_column']) || empty($config['end_column'])) {
    die('La hoja seleccionada no tiene configuración válida.');
}

// Cargamos el Excel
$spreadsheet = IOFactory::load($_SESSION['excel']['path']);
$worksheet = $spreadsheet->getSheetByName($currentSheet);

if (!$worksheet) {
    die('No se pudo cargar la hoja: ' . htmlspecialchars($currentSheet));
}

// Convertimos columnas de letras a índices
$startColIndex = Coordinate::columnIndexFromString($config['start_column']);
$endColIndex   = Coordinate::columnIndexFromString($config['end_column']);
$headerRow     = (int)$config['header_row'];

// Leemos los encabezados
$headers = [];
for ($col = $startColIndex; $col <= $endColIndex; $col++) {
    $coord = Coordinate::stringFromColumnIndex($col) . $headerRow;
    $value = $worksheet->getCell($coord)->getCalculatedValue();
    $headers[] = $value !== null ? (string)$value : 'Columna ' . Coordinate::stringFromColumnIndex($col);
}

// Leemos las filas de datos (desde la siguiente a la de encabezados hasta el final usado)
$highestRow = $worksheet->getHighestDataRow();
$data = [];

for ($row = $headerRow + 1; $row <= $highestRow; $row++) {
    $rowData = [];
    $hasData = false;

    for ($col = $startColIndex; $col <= $endColIndex; $col++) {
        $coord = Coordinate::stringFromColumnIndex($col) . $row;
        $cell = $worksheet->getCell($coord);
        $value = $cell->getDataType() === 'f' 
            ? $cell->getCalculatedValue() 
            : $cell->getValue();

        if ($value !== null && $value !== '') {
            $hasData = true;
        }

        $rowData[] = $value !== null ? (string)$value : '';
    }

    // Solo añadimos filas que tengan al menos algún dato
    if ($hasData) {
        $data[] = $rowData;
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Visualización - Excel Dashboard</title>

    <!-- DataTables CSS -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.8/css/jquery.dataTables.min.css">

    <style>
        body { font-family: system-ui, sans-serif; margin: 0; padding: 0; }
        header { background: #1a1a1a; color: white; padding: 16px 24px; }
        header h1 { margin: 0; font-size: 1.3rem; }
        .file-name { font-size: 0.9rem; opacity: 0.7; margin-top: 4px; }

        nav { background: #f5f5f5; padding: 12px 24px; border-bottom: 1px solid #ddd; }
        nav a { 
            display: inline-block; 
            margin-right: 12px; 
            padding: 8px 14px; 
            text-decoration: none; 
            color: #333; 
            border-radius: 4px;
        }
        nav a.active { background: #333; color: white; }
        nav a:hover:not(.active) { background: #e0e0e0; }

        main { padding: 24px; }
        h2 { margin-top: 0; }

        table.dataTable { width: 100% !important; }
    </style>
</head>
<body>
    <header>
        <h1>Excel Dashboard</h1>
        <div class="file-name"><?= htmlspecialchars($_SESSION['excel']['original_name']) ?></div>
    </header>

    <nav>
        <?php foreach ($selectedSheets as $sheet): ?>
            <a href="?sheet=<?= urlencode($sheet) ?>" 
               class="<?= $sheet === $currentSheet ? 'active' : '' ?>">
                <?= htmlspecialchars($sheet) ?>
            </a>
        <?php endforeach; ?>
    </nav>

    <main>
        <h2><?= htmlspecialchars($currentSheet) ?></h2>
        <p>
            Rango: 
            <?= htmlspecialchars($config['start_column'] . $headerRow) ?> 
            → 
            <?= htmlspecialchars($config['end_column'] . $highestRow) ?>
            · 
            <?= count($data) ?> filas de datos
        </p>

        <table id="data-table" class="display">
            <thead>
                <tr>
                    <?php foreach ($headers as $header): ?>
                        <th><?= htmlspecialchars($header) ?></th>
                    <?php endforeach; ?>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($data as $row): ?>
                    <tr>
                        <?php foreach ($row as $cell): ?>
                            <td><?= htmlspecialchars($cell) ?></td>
                        <?php endforeach; ?>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </main>

    <!-- jQuery + DataTables -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.8/js/jquery.dataTables.min.js"></script>
    <script>
        $(document).ready(function() {
            $('#data-table').DataTable({
                pageLength: 25,
                lengthMenu: [10, 25, 50, 100],
                language: {
                    url: 'https://cdn.datatables.net/plug-ins/1.13.8/i18n/es-ES.json'
                }
            });
        });
    </script>
</body>
</html>