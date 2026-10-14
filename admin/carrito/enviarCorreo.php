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

// Función para generar el contenido del email
function generarContenidoEmail($detalles_pedido, $datos_cliente, $orden_id) {
    $total = 0;
    $contenido = "
    <!DOCTYPE html>
    <html>
    <head>
        <meta charset='UTF-8'>
        <style>
            body { font-family: Arial, sans-serif; }
            table { border-collapse: collapse; width: 100%; margin: 20px 0; }
            th, td { padding: 12px; text-align: left; border-bottom: 1px solid #ddd; }
            th { background-color: #4CAF50; color: white; }
            .total { font-weight: bold; background-color: #f2f2f2; }
            .header { background-color: #4CAF50; color: white; padding: 20px; }
            .container { padding: 20px; }
        </style>
    </head>
    <body>
        <div class='header'>
            <h2>Confirmación de Pedido #" . htmlspecialchars($orden_id) . "</h2>
        </div>
        <div class='container'>
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

    $contenido .= "<tr class='total'>
                    <td colspan='3'>Total</td>
                    <td>S/. " . number_format($total, 2) . "</td>
                   </tr>
            </table>
            <p>Tu pedido está siendo procesado y te notificaremos cuando esté listo.</p>
            <p>Si tienes alguna pregunta sobre tu pedido, no dudes en contactarnos.</p>
            <br>
            <p>¡Gracias por tu preferencia!</p>
            <hr>
            <p><small>Este es un correo automático, por favor no responder.</small></p>
        </div>
    </body>
    </html>";

    return $contenido;
}

// Función para enviar el correo
function enviarCorreo($detalles_pedido, $datos_cliente, $orden_id) {
    global $mail, $FROM_EMAIL, $FROM_NAME;

    try {
        // Configuración del servidor
        $mail->isSMTP();                                     // Usar SMTP
        $mail->Host       = 'smtp.gmail.com';                // Servidor SMTP de Gmail
        $mail->SMTPAuth   = true;                           // Habilitar autenticación SMTP
        $mail->Username   = $FROM_EMAIL;                    // Tu correo Gmail
        $mail->Password   = 'phzulksupkdpkrgk';       // Tu contraseña de aplicación de Gmail (usar variable de entorno)
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;    // Habilitar cifrado implícito TLS
        $mail->Port       = 465;                            // Puerto TCP para conexión segura
        $mail->CharSet    = 'UTF-8';                        // Establecer codificación UTF-8

        // Remitente y destinatario
        $mail->setFrom($FROM_EMAIL, $FROM_NAME);
        $mail->addAddress($datos_cliente['email'], $datos_cliente['nombres']);
        $mail->addCC($FROM_EMAIL);

        // Contenido
        $mail->isHTML(true);
        $mail->Subject = 'Confirmación de Pedido #' . $orden_id;
        $mail->Body    = generarContenidoEmail($detalles_pedido, $datos_cliente, $orden_id);

        // Enviar el correo
        $mail->send();
        echo "<script>
                alert('Se ha enviado un correo de confirmación a " . htmlspecialchars($datos_cliente['email']) . "');
                window.location.href = 'pagina_confirmacion.php';
             </script>";

    } catch (Exception $e) {
        echo "Error al enviar el correo: " . $mail->ErrorInfo;
    }
}

// Verificar si tenemos los IDs necesarios en la sesión
if (isset($_SESSION['Orden_id']) && isset($_SESSION['cliente_ID'])) {
    try {
        // Obtener los datos necesarios
        $detalles_pedido = obtenerDetallesPedido($conexion, $_SESSION['Orden_id']);
        $datos_cliente = obtenerDatosCliente($conexion, $_SESSION['cliente_ID']);

        if ($detalles_pedido && $datos_cliente) {
            enviarCorreo($detalles_pedido, $datos_cliente, $_SESSION['Orden_id']);
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
