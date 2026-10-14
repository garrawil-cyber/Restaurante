<?php
include('../bd.php'); // Archivo de conexión a la base de datos
$sql = "SELECT * FROM reservas";
$resultado = $conexion->query($sql);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reservas</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
</head>
<body>
    <div class="container mt-5">
        <h2 class="text-center">Reservas Realizadas</h2>
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nombre</th>
                    <th>Email</th>
                    <th>Teléfono</th>
                    <th>Fecha</th>
                    <th>Hora</th>
                    <th>Personas</th>
                </tr>
            </thead>
            <tbody>
                <?php
                //if ($resultado->num_rows > 0) 
                {
                   // while($row = $resultado->fetch_assoc()) 
                   {
                        echo "<tr><td>{$row['id']}</td><td>{$row['nombre']}</td><td>{$row['email']}</td><td>{$row['telefono']}</td><td>{$row['fecha']}</td><td>{$row['hora']}</td><td>{$row['personas']}</td></tr>";
                    }
              //  } else {
                    echo "<tr><td colspan='7'>No se encontraron reservas</td></tr>";
                }
                //$conexion->close();
                ?>
            </tbody>
        </table>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
