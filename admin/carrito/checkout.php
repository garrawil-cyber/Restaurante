<?php
session_start();

include('../bd.php'); // Archivo de conexión a la base de datos

/*----------  Zona horaria  ----------*/
date_default_timezone_set("America/Lima");
// Crear un objeto DateTime con la fecha y hora actual
$ahora = new DateTime();
// Obtener la fecha actual en formato AAAA-MM-DD
$fecha = $ahora->format('Y-m-d');
// Obtener la hora actual en formato HH:MM:SS
$hora = $ahora->format('H:i:s');
$estado = "En Proceso";

// Verificar si el carrito de compras existe y no está vacío
if (isset($_SESSION['carrito']) && !empty($_SESSION['carrito'])) {
    if ($_POST) {
        try {
            // Iniciar transacción
            $conexion->beginTransaction();

            // Obtener y sanitizar datos del formulario
            $nombre = htmlspecialchars(trim($_POST['nombreCliente'] ?? ""));
            $dni =  htmlspecialchars(trim($_POST['dniCliente'] ?? ""));
            $email = filter_var($_POST['emailCliente'] ?? "", FILTER_SANITIZE_EMAIL);
            $telefono = htmlspecialchars(trim($_POST['telefonoCliente'] ?? ""));
            
            // Validar que los campos no estén vacíos
            if (empty($nombre) || empty($dni) || empty($email) || empty($telefono)) {
                throw new Exception("Todos los campos son obligatorios");
            }

            // Validar el formato del email
            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                throw new Exception("El formato del email no es válido");
            }
            
            // Insertar datos del Cliente en la Base de datos
            $sentencia = $conexion->prepare("INSERT INTO tbl_cliente (nombres, dni, telefono, email) 
                                           VALUES (:nombre, :dni, :telefono, :email)");

            $sentencia->bindParam(":nombre", $nombre);
            $sentencia->bindParam(":dni", $dni);
            $sentencia->bindParam(":telefono", $telefono);
            $sentencia->bindParam(":email", $email);
            $sentencia->execute();

            // Obtener el ID del Cliente del último registro insertado
            $Cliente_id = $conexion->lastInsertId();
            $_SESSION['cliente_ID'] = $Cliente_id;

            // Insertar la Orden de pedido
            $sql = $conexion->prepare("INSERT INTO tbl_ordenes (fecha, hora, ID_cliente, estado) 
                                     VALUES (:fecha, :hora, :cliente, :estado)");
  
            $sql->bindParam(":fecha", $fecha);
            $sql->bindParam(":hora", $hora);
            $sql->bindParam(":cliente", $_SESSION['cliente_ID']);
            $sql->bindParam(":estado", $estado);
            $sql->execute();

            // Obtener el ID de la Orden de pedido
            $IdOrden_pedido = $conexion->lastInsertId();
            $_SESSION['Orden_id'] = $IdOrden_pedido;
        
            // Insertar el detalle de la Orden
            foreach ($_SESSION['carrito'] as $producto) {
                $sentencia = $conexion->prepare("INSERT INTO tbl_detalle_orden (id_orden, id_menu, cant, precio) 
                                               VALUES (:id_orden, :id_menu, :cantidad, :precio)");
           
                $sentencia->execute([
                    ':id_orden' => $_SESSION['Orden_id'],
                    ':id_menu' => $producto['id'],
                    ':cantidad' => $producto['cantidad'],
                    ':precio' => $producto['precio']
                ]);
            }

            // Confirmar la transacción
            $conexion->commit();
            
            // Intentar enviar el email
            try {
               //require_once 'copiaEmail.php';
                require_once 'enviarEmail.php';
                //require_once 'enviarCorreo.php';
                 // Si llegamos aquí, el email se envió correctamente
                
                // Limpiar solo el carrito, manteniendo los IDs para la página de confirmación
                destruirCarrito();
                
                // Redirigir a la página de confirmación
                header("Location: pagina_confirmacion.php");
                exit();
                
            } catch (Exception $emailError) {
                // Si hay error en el envío del email, registrarlo pero no afectar la transacción
                error_log("Error al enviar email: " . $emailError->getMessage());
                
                // Limpiar el carrito
                destruirCarrito();
                
                // Redirigir con un mensaje de advertencia
                $_SESSION['email_warning'] = "El pedido se procesó correctamente pero hubo un problema al enviar el email de confirmación.";
                header("Location: pagina_confirmacion.php");
                exit();
            }

        } catch (PDOException $e) {
            // Si hay error en la base de datos, deshacer todos los cambios
            $conexion->rollBack();
            echo "<script>
                    alert('Error en la base de datos: " . addslashes($e->getMessage()) . "');
                    window.history.back();
                  </script>";
        } catch (Exception $e) {
            // Para otros errores (validaciones, etc.)
            echo "<script>
                    alert('" . addslashes($e->getMessage()) . "');
                    window.history.back();
                  </script>";
        }
    }
} else {
    // Redirigir al carrito si está vacío
    header("Location: carrito.php");
    exit();
}

/**
 * Función para destruir el carrito y mantener los IDs necesarios para el email
 * @return void
 */
function destruirCarrito() {
    if (isset($_SESSION['carrito'])) {
        unset($_SESSION['carrito']);
        // Mantenemos cliente_ID y Orden_id para el email y la página de confirmación
    }
}
?>