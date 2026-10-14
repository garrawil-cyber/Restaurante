<?php
include('conexion.php'); // Archivo de conexión a la base de datos

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nombre = $_POST['nombre'];
    $email = $_POST['email'];
    $telefono = $_POST['telefono'];
    $fecha = $_POST['fecha'];
    $hora = $_POST['hora'];
    $personas = $_POST['personas'];

    $sql = "INSERT INTO reservas (nombre, email, telefono, fecha, hora, personas) VALUES ('$nombre', '$email', '$telefono', '$fecha', '$hora', '$personas')";
    
    if ($conexion->query($sql) === TRUE) {
        echo "Reserva realizada con éxito.";
    } else {
        echo "Error: " . $sql . "<br>" . $conexion->error;
    }

    $conexion->close();
}
?>
