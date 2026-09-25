<?php
session_start();

require_once __DIR__ . '/../vendor/autoload.php';

// Comprobaciones básicas
if (empty($_SESSION['excel']['path']) || !file_exists($_SESSION['excel']['path'])) {
    die('No hay ningún archivo Excel cargado. <a href="index.php">Volver</a>');
}

if (empty($_SESSION['selected_sheets'])) {
    die('No hay hojas seleccionadas. <a href="select-sheets.php">Volver</a>');
}

$selectedSheets = $_SESSION['selected_sheets'];
$configs = $_SESSION['sheet_configs'] ?? [];

// Procesar el formulario
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $errors = [];

    foreach ($selectedSheets as $sheetName) {
        $headerRow   = trim($_POST['header_row'][$sheetName] ?? '');
        $startColumn = strtoupper(trim($_POST['start_column'][$sheetName] ?? ''));
        $endColumn   = strtoupper(trim($_POST['end_column'][$sheetName] ?? ''));

        // Validaciones simples
        if (!ctype_digit($headerRow) || (int)$headerRow < 1) {
            $errors[] = "La fila de encabezados de la hoja «{$sheetName}» no es válida.";
        }

        if (!preg_match('/^[A-Z]+$/', $startColumn)) {
            $errors[] = "La columna inicial de la hoja «{$sheetName}» no es válida.";
        }

        if (!preg_match('/^[A-Z]+$/', $endColumn)) {
            $errors[] = "La columna final de la hoja «{$sheetName}» no es válida.";
        }

        // Guardamos aunque haya errores (para no perder lo escrito)
        $configs[$sheetName] = [
            'header_row'   => $headerRow,
            'start_column' => $startColumn,
            'end_column'   => $endColumn,
        ];
    }

    $_SESSION['sheet_configs'] = $configs;

    if (empty($errors)) {
        // Todo correcto → vamos a la visualización
        header('Location: view.php');
        exit;
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Configurar tablas - Excel Dashboard</title>
    <style>
        body { font-family: system-ui, sans-serif; max-width: 800px; margin: 40px auto; padding: 0 20px; }
        h1 { margin-bottom: 8px; }
        .file-info { color: #555; margin-bottom: 30px; }
        .sheet-block { border: 1px solid #ddd; padding: 20px; margin-bottom: 24px; border-radius: 6px; }
        .sheet-block h2 { margin-top: 0; font-size: 1.2rem; }
        .form-row { display: flex; gap: 16px; margin-bottom: 12px; flex-wrap: wrap; }
        .form-group { display: flex; flex-direction: column; }
        label { font-size: 0.9rem; margin-bottom: 4px; color: #444; }
        input[type="text"], input[type="number"] { padding: 8px; width: 100px; }
        .error { color: #c00; margin-bottom: 16px; }
        button { margin-top: 10px; padding: 10px 20px; cursor: pointer; }
    </style>
</head>
<body>
    <h1>Configurar tablas</h1>
    <p class="file-info">
        Archivo: <strong><?= htmlspecialchars($_SESSION['excel']['original_name']) ?></strong>
    </p>

    <?php if (!empty($errors)): ?>
        <div class="error">
            <?php foreach ($errors as $error): ?>
                <p><?= htmlspecialchars($error) ?></p>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

    <form method="POST">
        <?php foreach ($selectedSheets as $sheetName): 
            $config = $configs[$sheetName] ?? [
                'header_row' => '',
                'start_column' => '',
                'end_column' => ''
            ];

            // Aseguramos que nunca sean null
            $headerRow   = $config['header_row'] ?? '';
            $startColumn = $config['start_column'] ?? '';
            $endColumn   = $config['end_column'] ?? '';
        ?>
            <div class="sheet-block">
                <h2><?= htmlspecialchars($sheetName) ?></h2>

                <div class="form-row">
                    <div class="form-group">
                        <label>Fila de encabezados</label>
                        <input type="number" 
                            name="header_row[<?= htmlspecialchars($sheetName) ?>]" 
                            value="<?= htmlspecialchars($headerRow) ?>" 
                            min="1" required>
                    </div>

                    <div class="form-group">
                        <label>Columna inicial</label>
                        <input type="text" 
                            name="start_column[<?= htmlspecialchars($sheetName) ?>]" 
                            value="<?= htmlspecialchars($startColumn) ?>" 
                            placeholder="A" required>
                    </div>

                    <div class="form-group">
                        <label>Columna final</label>
                        <input type="text" 
                            name="end_column[<?= htmlspecialchars($sheetName) ?>]" 
                            value="<?= htmlspecialchars($endColumn) ?>" 
                            placeholder="V" required>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>

        <button type="submit">Guardar configuración y continuar</button>
    </form>
</body>
</html>