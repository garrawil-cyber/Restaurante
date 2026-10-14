<?php
session_start();
include('../bd.php'); // Archivo de conexión a la base de datos

// Importar clases PHPMailer
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

require '../../vendor/autoload.php';

$mail = new PHPMailer(true);


// Configuración de correo
$FROM_EMAIL = 'garrawil@gmail.com';
$FROM_NAME = 'Restaurante La Sombra';

// Función para obtener los detalles del pedido
function obtenerDetallesPedido($conexion, $orden_id) {
    $sql = "SELECT m.nombre, d.cant, d.precio, (d.cant * d.precio) as subtotal 
            FROM tbl_detalle_orden d 
            INNER JOIN tbl_menu m ON d.id_menu = m.ID 
            WHERE d.id_orden = :orden_id";
            
    $stmt = $conexion->prepare($sql);
    $stmt->execute(['orden_id' => $orden_id]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// Función para obtener los datos del cliente
function obtenerDatosCliente($conexion, $cliente_id) {
    $sql = "SELECT nombres, email FROM tbl_cliente WHERE ID = :cliente_id";
    $stmt = $conexion->prepare($sql);
    $stmt->execute(['cliente_id' => $cliente_id]);
    return $stmt->fetch(PDO::FETCH_ASSOC);
}

function generarContenidoEmail($detalles_pedido, $datos_cliente, $orden_id) {
    $total = 0;
    $contenido = "
    <!DOCTYPE html>
    <html lang='es'>
    <head>
        <meta charset='UTF-8'>
        <meta name='viewport' content='width=device-width, initial-scale=1.0'>
        <title>Confirmación de Pedido</title>
        <style>
            body {
                font-family: 'Arial', sans-serif;
                margin: 0;
                padding: 0;
                background-color: #f4f4f4;
            }
            .email-container {
                width: 100%;
                max-width: 600px;
                margin: 0 auto;
                background-color: #ffffff;
                padding: 20px;
                border-radius: 8px;
                box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            }
            .header {
                background-color: #4CAF50;
                color: #fff;
                padding: 20px;
                text-align: center;
                border-radius: 8px 8px 0 0;
            }
            .header h2 {
                margin: 0;
                font-size: 24px;
            }
            .content {
                padding: 20px;
                color: #333;
                line-height: 1.6;
            }
            table {
                width: 100%;
                border-collapse: collapse;
                margin: 20px 0;
            }
            th, td {
                padding: 12px;
                text-align: left;
                border-bottom: 1px solid #ddd;
            }
            th {
                background-color: #4CAF50;
                color: white;
                font-weight: bold;
            }
            td {
                background-color: #f9f9f9;
            }
            .total-row {
                font-weight: bold;
                background-color: #f2f2f2;
            }
            .footer {
                text-align: center;
                margin-top: 20px;
                font-size: 12px;
                color: #777;
            }
            .footer p {
                margin: 5px 0;
            }
            .button {
                display: inline-block;
                padding: 10px 20px;
                background-color: #4CAF50;
                color: white;
                text-decoration: none;
                font-weight: bold;
                border-radius: 4px;
                margin-top: 20px;
            }
            .button:hover {
                background-color: #45a049;
            }
        </style>
    </head>
    <body>
        <div class='email-container'>
            <div class='header'>
                <h2>Confirmación de Pedido #" . htmlspecialchars($orden_id) . "</h2>
            </div>
            <div class='content'>
                <p>Estimado/a " . htmlspecialchars($datos_cliente['nombres']) . ",</p>
                <p>¡Gracias por tu pedido! A continuación encontrarás los detalles de tu compra:</p>
                <table>
                    <tr>
                        <th>Producto</th>
                        <th>Cantidad</th>
                        <th>Precio Unitario</th>
                        <th>Subtotal</th>
                    </tr>";
    
    foreach ($detalles_pedido as $item) {
        $contenido .= "<tr>
                        <td>" . htmlspecialchars($item['nombre']) . "</td>
                        <td>" . htmlspecialchars($item['cant']) . "</td>
                        <td>S/. " . number_format($item['precio'], 2) . "</td>
                        <td>S/. " . number_format($item['subtotal'], 2) . "</td>
                      </tr>";
        $total += $item['subtotal'];
    }

    $contenido .= "<tr class='total-row'>
                    <td colspan='3'>Total</td>
                    <td>S/. " . number_format($total, 2) . "</td>
                   </tr>
                </table>
                <p>Tu pedido está siendo procesado y te notificaremos cuando esté listo.</p>
                <p>Si tienes alguna pregunta sobre tu pedido, no dudes en <a href='mailto:garrawil@gmail.com' style='color: #4CAF50;'>contactarnos</a>.</p>
                <!--<a href='pagina_confirmacion.php' class='button'>Ver Mi Pedido</a>-->
            </div>
            <div class='footer'>
                <p>¡Gracias por tu preferencia!</p>
                <p><small>Este es un correo automático, por favor no responder.</small></p>
            </div>
        </div>
    </body>
    </html>";

    return $contenido;
}



// Verificar si tenemos los IDs necesarios en la sesión
if (isset($_SESSION['Orden_id']) && isset($_SESSION['cliente_ID'])) {
    try {
        // Obtener los datos necesarios
        $detalles_pedido = obtenerDetallesPedido($conexion, $_SESSION['Orden_id']);
        $datos_cliente = obtenerDatosCliente($conexion, $_SESSION['cliente_ID']);
        
        if ($detalles_pedido && $datos_cliente) {
            // Crear una nueva instancia de PHPMailer
            $mail = new PHPMailer(true);
            
            try {
                // Configuración del servidor
                $mail->isSMTP();                                     // Usar SMTP
                $mail->Host       = 'smtp.gmail.com';                // Servidor SMTP de Gmail
                $mail->SMTPAuth   = true;                           // Habilitar autenticación SMTP
                $mail->Username   = 'garrawil@gmail.com';           // Tu correo Gmail
                $mail->Password   = 'phzulksupkdpkrgk';  // Tu contraseña de aplicación de Gmail
                $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;    // Habilitar cifrado implícito TLS
                $mail->Port       = 465;                            // Puerto TCP para conexión segura
                $mail->CharSet    = 'UTF-8';                        // Establecer codificación UTF-8

                // Remitente y destinatario
                $mail->setFrom($FROM_EMAIL , $FROM_NAME);
                $mail->addAddress($datos_cliente['email'], $datos_cliente['nombres']);
                $mail->addCC('garrawil@gmail.com');

                // Contenido
                $mail->isHTML(true);
                $mail->Subject = 'Confirmación de Pedido #' . $_SESSION['Orden_id'];
                $mail->Body    = generarContenidoEmail($detalles_pedido, $datos_cliente, $_SESSION['Orden_id']);

                // Enviar el correo
                $mail->send();
                echo "<script>
                        alert('Se ha enviado un correo de confirmación a " . htmlspecialchars($datos_cliente['email']) . "');
                        window.location.href = 'pagina_confirmacion.php';
                     </script>";
                
            } catch (Exception $e) {
                echo "Error al enviar el correo: " . $mail->ErrorInfo;
            }
        } else {
            echo "No se encontraron los datos del pedido o del cliente.";
        }
        
    } catch (PDOException $e) {
        echo "Error en la base de datos: " . $e->getMessage();
    }
} else {
    echo "No se encontró información del pedido en la sesión.";
}
?>