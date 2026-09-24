<?php

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

$destination = $uploadDirectory . basename($file['name']);

if (!move_uploaded_file($file['tmp_name'], $destination)) {
    die('No se ha podido guardar el archivo.');
}

echo '<h1>Excel guardado correctamente</h1>';

echo '<p>Nombre: ' . htmlspecialchars($file['name']) . '</p>';
echo '<p>Tamaño: ' . $file['size'] . ' bytes</p>';
echo '<p>Ruta: ' . htmlspecialchars($destination) . '</p>';