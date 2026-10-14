<?php

$peticion_ajax = true;
const APP_URL = "http://localhost/AsistenciaQR/";
const APP_NAME = "AsistenciaQR";

/*----------  Zona horaria  ----------*/
date_default_timezone_set("America/Lima");

$txtID = '';

if (!empty($_GET["txtID"])) {
    $txtID = $_GET["txtID"];
}

/*---------- Incluyendo configuraciones ----------*/
include("../../Bd/conexion.php");

/*---------- Obtener datos del empleado ----------*/
$sentencia_empleado = $conexion->prepare("SELECT nombre, apellidos, dni, email, codigo_qr FROM empleados WHERE id = :id LIMIT 1");
$sentencia_empleado->bindParam(':id', $txtID, PDO::PARAM_INT);
$sentencia_empleado->execute();
$datos_empleado = $sentencia_empleado->fetch(PDO::FETCH_ASSOC);

if ($datos_empleado) {
    require "./code128.php"; // Incluye FPDF y Code128

    $pdf = new PDF_Code128('P', 'mm', array(70, 90));
    $pdf->SetMargins(4, 10, 4);
    $pdf->AddPage();

    // Título ajustado con menos sangría superior
    $pdf->SetY(5); // Ajustar la posición Y del título
    $pdf->SetFont('Arial', 'B', 14);
    $pdf->SetTextColor(0, 0, 0);
    $pdf->Cell(0, 10, strtoupper('Fotocheck'), 0, 1, 'C');

    

    $pdf->Ln(5);

    // Datos del empleado
    $pdf->SetFont('Arial', '', 10);
    $pdf->Cell(0,6,iconv("UTF-8", "ISO-8859-1","Nombre: ".$datos_empleado['nombre']),0,'C',false);
    $pdf->Cell(0,6,iconv("UTF-8", "ISO-8859-1","Apellidos: ".$datos_empleado['apellidos']),0,'C',false);
    $pdf->Cell(0,6,iconv("UTF-8", "ISO-8859-1","DNI: ".$datos_empleado['dni']),0,'C',false);
    $pdf->Cell(0,6,iconv("UTF-8", "ISO-8859-1","Email: ".$datos_empleado['email']),0,'C',false);

    $pdf->Ln(5);

    // Imagen del QR
    $foto_qr = '../../../qrcodes/' . $datos_empleado['codigo_qr'] . '.png';
    if (file_exists($foto_qr)) {
        $pdf->Image($foto_qr, 15, $pdf->GetY(), 35, 35, 'PNG');
    } else {
        $pdf->Cell(0, 10, "QR no disponible", 0, 1, 'C');
    }

    $pdf->Ln(40);

    $pdf->Output("I", "imprimir_fotocheck" .$datos_empleado['nombre'].".pdf", true);
    
} else {
    // Mensaje de error
    ?>
    <!DOCTYPE html>
    <html lang="es">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title><?php echo APP_NAME; ?></title>
        <?php include '../views/inc/head.php'; ?>
    </head>
    <body>
        <div class="main-container">
            <section class="hero-body">
                <div class="hero-body">
                    <p class="has-text-centered has-text-white pb-3">
                        <i class="fas fa-rocket fa-5x"></i>
                    </p>
                    <p class="title has-text-white">¡Ocurrió un error!</p>
                    <p class="subtitle has-text-white">No hemos encontrado datos del empleado</p>
                </div>
            </section>
        </div>
        <?php include '../views/inc/script.php'; ?>
    </body>
    </html>
    <?php
}
?>
