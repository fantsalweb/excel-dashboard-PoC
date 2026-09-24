<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Excel Dashboard</title>
</head>
<body>

    <main>
        <h1>Excel Dashboard</h1>

        <p>
            Analiza y visualiza los datos de tus archivos Excel.
        </p>

        <form action="upload.php" method="POST" enctype="multipart/form-data">
            <label for="excel_file">
                Selecciona un archivo Excel:
            </label>

            <input
                type="file"
                id="excel_file"
                name="excel_file"
                accept=".xlsx"
                required
            >

            <button type="submit">
                Analizar Excel
            </button>
        </form>
    </main>

</body>
</html>