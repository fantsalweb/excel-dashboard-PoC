<?php
session_start();

// Si ya había un archivo en sesión, lo borramos
if (!empty($_SESSION['excel']['path']) && file_exists($_SESSION['excel']['path'])) {
    @unlink($_SESSION['excel']['path']);
}

// Limpiamos la sesión anterior
unset($_SESSION['excel']);
unset($_SESSION['selected_sheets']);
unset($_SESSION['sheet_configs']);

if (!isset($_FILES['excel_file'])) {
    die('No se ha recibido ningún archivo.');
}

$file = $_FILES['excel_file'];

if ($file['error'] !== UPLOAD_ERR_OK) {
    die('Se ha producido un error durante la subida.');
}

$extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
if ($extension !== 'xlsx') {
    die('El archivo debe tener extensión .xlsx');
}

if ($file['size'] <= 0) {
    die('El archivo está vacío.');
}

$uploadDirectory = __DIR__ . '/../uploads/';

// Aseguramos que exista la carpeta
if (!is_dir($uploadDirectory)) {
    mkdir($uploadDirectory, 0755, true);
}

// Nombre único para evitar colisiones
$uniqueName = uniqid('excel_', true) . '.xlsx';
$destination = $uploadDirectory . $uniqueName;

if (!move_uploaded_file($file['tmp_name'], $destination)) {
    die('No se ha podido guardar el archivo.');
}

// Guardamos en sesión
$_SESSION['excel'] = [
    'original_name' => $file['name'],
    'stored_name'   => $uniqueName,
    'path'          => $destination,
    'size'          => $file['size'],
];

// Redirigimos a la selección de hojas
header('Location: select-sheets.php');
exit;