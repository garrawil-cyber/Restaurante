<?php
session_start();

ini_set('display_errors', 0);
ini_set('display_startup_errors', 0);
error_reporting(E_ALL);
ini_set('xdebug.mode', 'off');

// Validación y sanitización de entradas
function sanitizeInput($data) {
    return htmlspecialchars(trim($data), ENT_QUOTES, 'UTF-8');
}

// Manejo de acciones del carrito
try {
    if (isset($_POST['agregar'])) {
        $producto = array(
            'id' => filter_var($_POST['id'], FILTER_SANITIZE_NUMBER_INT),
            'nombre' => sanitizeInput($_POST['nombre']),
            'precio' => filter_var($_POST['precio'], FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION),
            'cantidad' => 1
        );

        if (isset($_SESSION['carrito'])) {
            $ids = array_column($_SESSION['carrito'], 'id');
            if (!in_array($producto['id'], $ids)) {
                $_SESSION['carrito'][] = $producto;
            }
        } else {
            $_SESSION['carrito'] = array($producto);
        }
        header("Location: carrito.php");
        exit();
    }

    if (isset($_POST['actualizar_cantidad']) && isset($_POST['cantidad'])) {
        $id = filter_var($_POST['producto_id'], FILTER_SANITIZE_NUMBER_INT);
        $nueva_cantidad = filter_var($_POST['cantidad'], FILTER_SANITIZE_NUMBER_INT);
        
        if ($nueva_cantidad > 0 && $nueva_cantidad <= 10) {
            foreach ($_SESSION['carrito'] as &$item) {
                if ($item['id'] == $id) {
                    $item['cantidad'] = $nueva_cantidad;
                    break;
                }
            }
        }
        header("Location: carrito.php");
        exit();
    }

    if (isset($_GET['eliminar'])) {
        $id = filter_var($_GET['eliminar'], FILTER_SANITIZE_NUMBER_INT);
        if (isset($_SESSION['carrito'])) {
            $_SESSION['carrito'] = array_filter($_SESSION['carrito'], function($item) use ($id) {
                return $item['id'] != $id;
            });
            $_SESSION['carrito'] = array_values($_SESSION['carrito']);
        }
        header("Location: carrito.php");
        exit();
    }
} catch (Exception $e) {
    error_log("Error en el carrito: " . $e->getMessage());
    $error_message = "Ha ocurrido un error al procesar su solicitud.";
}

if (!isset($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Carrito de compras">
    <title>Mi Carrito de Compras</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <link href="../css/carrito.css" rel="stylesheet" />
</head>
<body>
    <div class="container">
        <div class="cart-container">
            <div class="cart-header">
                <h1>Mi Carrito de Compras</h1>
            </div>

            <?php if (isset($error_message)): ?>
                <div class="alert alert-danger" role="alert">
                    <?php echo $error_message; ?>
                </div>
            <?php endif; ?>

            <?php if (isset($_SESSION['carrito']) && !empty($_SESSION['carrito'])): ?>
                <?php 
                $total = 0;
                foreach ($_SESSION['carrito'] as $producto):
                    $precio = floatval($producto['precio']);
                    $cantidad = intval($producto['cantidad']);
                    $subtotal = $precio * $cantidad;
                    $total += $subtotal;
                ?>
                    <div class="product-card">
                        <div class="product-details">
                            <h3 class="product-name"><?php echo sanitizeInput($producto['nombre']); ?></h3>
                            <div class="price">S/. <?php echo number_format($precio, 2); ?></div>
                        </div>
                        
                        <form action="carrito.php" method="post" class="quantity-control">
                            <button type="button" class="btn btn-quantity" onclick="updateQuantity(this, -1)">-</button>
                            <input type="number" class="quantity-input" name="cantidad" value="<?php echo $cantidad; ?>" min="1" max="10" readonly>
                            <button type="button" class="btn btn-quantity" onclick="updateQuantity(this, 1)">+</button>
                            <input type="hidden" name="producto_id" value="<?php echo $producto['id']; ?>">
                            <input type="hidden" name="actualizar_cantidad">
                        </form>

                        <div class="price">S/. <?php echo number_format($subtotal, 2); ?></div>

                        <a href="carrito.php?eliminar=<?php echo $producto['id']; ?>" 
                           class="btn btn-outline-danger" 
                           onclick="return confirm('¿Está seguro de eliminar este producto?')">
                            <i class="bi bi-trash"></i>
                        </a>
                    </div>
                <?php endforeach; ?>

                <div class="cart-summary">
                    <div class="summary-row">
                        <span>Subtotal</span>
                        <span>S/. <?php echo number_format($total, 2); ?></span>
                    </div>
                    <div class="summary-row">
                        <span>IGV (18%)</span>
                        <span>S/. 00.00<?php //echo number_format($total * 0.18, 2); ?></span>
                    </div>
                    <div class="summary-row">
                        <span>Total</span>
                        <span>S/. <?php echo number_format($total, 2);//echo number_format($total * 1.18, 2); ?></span>
                    </div>
                </div>

                <div class="cart-actions">
                <!--<button onclick="regresarPaginaAnterior()" class="btn btn-outline-primary btn-continue">
                        <i class="bi bi-arrow-left"></i> -->
                        <a href="../../index.php" class="btn btn-primary btn-lg btn-continue">Seguir Comprando</a>
               <!-- </button> -->
                        <a href="#" class="btn btn-success btn-checkout" data-bs-toggle="modal" data-bs-target="#printModal" data-id="<?php echo $value['codEmpleado']; ?>">
                        Proceder al Pago <i class="bi bi-arrow-right"></i>
                    </a>
                </div>

            <?php else: ?>
                <div class="empty-cart">
                    <i class="bi bi-cart-x empty-cart-icon"></i>
                    <h2>Tu carrito está vacío</h2>
                    <p>¿No sabes qué comprar? ¡Miles de productos a tu paladar le esperan!</p>
                    <a href="../../index.php" class="btn btn-primary btn-lg btn-continue">
                        Comenzar a Comprar
                    </a>
                </div>
            <?php endif; ?>
        </div>
    </div>
<!-- Modal de Logueo del cliente con validaciones -->
<div class="modal fade" id="printModal" tabindex="-1" aria-labelledby="printModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="printModalLabel">Datos del Cliente</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <form id="clienteForm" action="checkout.php" method="POST">
          <div class="mb-3">
            <label for="nombreCliente" class="form-label">Nombre Completo</label>
            <input 
              type="text" 
              class="form-control" 
              id="nombreCliente" 
              name="nombreCliente" 
              pattern="^[a-zA-Z\s]+$" 
              title="El nombre solo puede contener letras y espacios" 
              required
            >
          </div>
          <div class="mb-3">
            <label for="dniCliente" class="form-label">DNI</label>
            <input 
              type="text" 
              class="form-control" 
              id="dniCliente" 
              name="dniCliente" 
              pattern="^\d{8}$" 
              title="El DNI debe contener exactamente 8 dígitos, solo numeros" 
              maxlength="8" 
              required
            >
          </div>
          <div class="mb-3">
            <label for="emailCliente" class="form-label">Correo Electrónico</label>
            <input 
              type="email" 
              class="form-control" 
              id="emailCliente" 
              name="emailCliente" 
              placeholder="nombre@ejemplo.com" 
              required
            >
          </div>
          <div class="mb-3">
            <label for="telefonoCliente" class="form-label">Telefono</label>
            <input 
              type="text" 
              class="form-control" 
              id="telefonoCliente" 
              name="telefonoCliente" 
              pattern="^\d{9}$" 
              title="El TELEFONO debe contener exactamente 9 dígitos, solo numeros" 
              maxlength="9"
              placeholder="Ingrese telefono" 
            >
          </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
            <button type="submit" form="clienteForm" class="btn btn-primary">Enviar datos</button>
        </div>
    </form>
    </div>

    </div>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

<script>
    // Función para cargar los datos del cliente que se va a loguear
    $('#printModal').on('show.bs.modal', function (event) {
      var button = $(event.relatedTarget); // Botón que activó el modal
      var employeeId = button.data('id');  // ID del empleado pasado como data-id

  });

  // Validación en tiempo real
const clienteForm = document.getElementById('clienteForm');
clienteForm.addEventListener('submit', function(event) {
  if (!clienteForm.checkValidity()) {
    event.preventDefault();
    event.stopPropagation();
    alert('Por favor, revisa los campos ingresados.');
  }

  clienteForm.classList.add('was-validated');
});

</script>

<script>
    function updateQuantity(button, change) {
        const form = button.closest('form');
        const input = form.querySelector('input[name="cantidad"]');
        const currentValue = parseInt(input.value);
        const newValue = currentValue + change;
        
        if (newValue >= 1 && newValue <= 10) {
            input.value = newValue;
            form.submit();
        }
    }

    function regresarPaginaAnterior() {
        window.history.back();
    }

</script>
</body>
</html>