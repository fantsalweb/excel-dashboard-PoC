<?php
session_start();

require_once __DIR__ . '/../vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\IOFactory;

// Comprobamos que exista un Excel en sesión
if (empty($_SESSION['excel']['path']) || !file_exists($_SESSION['excel']['path'])) {
    die('No hay ningún archivo Excel cargado. <a href="index.php">Volver</a>');
}

$spreadsheet = IOFactory::load($_SESSION['excel']['path']);
$sheetNames = $spreadsheet->getSheetNames();

// Si se envió el formulario
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $selected = $_POST['sheets'] ?? [];

    if (empty($selected)) {
        $error = 'Debes seleccionar al menos una hoja.';
    } else {
        // Guardamos las hojas seleccionadas
        $_SESSION['selected_sheets'] = $selected;

        // Inicializamos configuración vacía para cada hoja
        $_SESSION['sheet_configs'] = [];
        foreach ($selected as $sheetName) {
            $_SESSION['sheet_configs'][$sheetName] = [
                'header_row'   => null,
                'start_column' => null,
                'end_column'   => null,
            ];
        }

        // Redirigimos a la siguiente fase (definición de rangos)
        header('Location: configure-sheets.php');
        exit;
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Seleccionar hojas - Excel Dashboard</title>
    <style>
        body { font-family: system-ui, sans-serif; max-width: 700px; margin: 40px auto; padding: 0 20px; }
        h1 { margin-bottom: 8px; }
        .file-info { color: #555; margin-bottom: 24px; }
        .sheet-list { list-style: none; padding: 0; }
        .sheet-list li { padding: 10px 0; border-bottom: 1px solid #eee; }
        .error { color: #c00; margin-bottom: 16px; }
        button { margin-top: 20px; padding: 10px 18px; cursor: pointer; }
    </style>
</head>
<body>
    <h1>Seleccionar hojas</h1>
    <p class="file-info">
        Archivo: <strong><?= htmlspecialchars($_SESSION['excel']['original_name']) ?></strong>
    </p>

    <?php if (!empty($error)): ?>
        <p class="error"><?= htmlspecialchars($error) ?></p>
    <?php endif; ?>

    <form method="POST">
        <p>Selecciona las hojas que quieres utilizar:</p>
        <ul class="sheet-list">
            <?php foreach ($sheetNames as $name): ?>
                <li>
                    <label>
                        <input type="checkbox" name="sheets[]" value="<?= htmlspecialchars($name) ?>">
                        <?= htmlspecialchars($name) ?>
                    </label>
                </li>
            <?php endforeach; ?>
        </ul>

        <button type="submit">Continuar</button>
    </form>
</body>
</html>