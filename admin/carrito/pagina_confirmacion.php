<?php
session_start();

// Verificar si existe la información del pedido
$orden_id = isset($_SESSION['Orden_id']) ? $_SESSION['Orden_id'] : 'No disponible';
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Confirmación de Pedido</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 20px;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
        }

        .confirmation-container {
            background-color: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
            text-align: center;
            max-width: 600px;
            width: 100%;
        }

        .success-icon {
            color: #4CAF50;
            font-size: 50px;
            margin-bottom: 20px;
        }

        h1 {
            color: #333;
            margin-bottom: 20px;
        }

        .message {
            color: #666;
            margin-bottom: 30px;
            line-height: 1.6;
        }

        .order-number {
            background-color: #f8f8f8;
            padding: 10px;
            border-radius: 5px;
            margin: 20px 0;
            font-weight: bold;
            color: #4CAF50;
        }

        .button {
            background-color: #4CAF50;
            color: white;
            padding: 12px 24px;
            text-decoration: none;
            border-radius: 5px;
            display: inline-block;
            margin-top: 20px;
            transition: background-color 0.3s;
        }

        .button:hover {
            background-color: #45a049;
        }

        .additional-info {
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid #eee;
            font-size: 0.9em;
            color: #777;
        }
    </style>
</head>
<body>
    <div class="confirmation-container">
        <div class="success-icon">✓</div>
        <h1>¡Pedido Confirmado!</h1>
        
        <div class="message">
            Hemos enviado un correo electrónico con los detalles de tu pedido.
            Por favor, revisa tu bandeja de entrada.
        </div>

        <div class="order-number">
            Número de Pedido: <?php echo htmlspecialchars($orden_id); ?>
        </div>

        <div class="additional-info">
            <p>Tiempo estimado de preparación: 30 minutos</p>
            <p>Si tienes alguna pregunta, no dudes en contactarnos.</p>
        </div>

        <a href="../carrito/carrito.php" class="button">Volver al Inicio</a>
    </div>

    <?php
    // Limpiar las variables de sesión relacionadas con el pedido
    if(isset($_SESSION['Orden_id'])) {
        unset($_SESSION['Orden_id']);
    }
    if(isset($_SESSION['cliente_ID'])) {
        unset($_SESSION['cliente_ID']);
    }
    ?>
</body>
</html>