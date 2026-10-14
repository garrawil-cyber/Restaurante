<?php
require_once('../bd.php');

try {
    $sql = $conexion->prepare("SELECT * FROM tbl_menu ORDER BY nombre ASC");
    $sql->execute();
    $lista_menu = $sql->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    error_log("Error en la consulta: " . $e->getMessage());
    $error_message = "Lo sentimos, ha ocurrido un error al cargar los productos.";
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Menú de productos disponibles">
    <title>Catálogo de Productos</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" rel="stylesheet">
    <link href="../css/productos.css" rel="stylesheet">
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background-color: #f8f9fa;
            color: #343a40;
        }
        .card {
            border: 1px solid #dee2e6;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }
        .card img {
            height: 200px;
            object-fit: cover;
        }
        .card-title {
            font-size: 1.25rem;
            font-weight: 600;
        }
        .price {
            font-size: 1.1rem;
            color: #d63384;
            font-weight: bold;
        }
        .btn-add-cart {
            background-color: #198754;
            color: #fff;
            font-weight: 600;
        }
        .btn-add-cart:hover {
            background-color: #145c43;
        }
        .header {
            text-align: center;
            margin-bottom: 2rem;
        }
        .header h1 {
            font-size: 2.5rem;
            font-weight: 700;
            color: #d63384;
        }
        .header p {
            color: #6c757d;
        }
        .alert-info {
            font-size: 1.1rem;
        }
    </style>
</head>
<body>
    <div class="container mt-5">
        <header class="header">
            <h1>Nuestro Menú</h1>
            <p>Descubre nuestra selección de productos cuidadosamente preparados</p>
        </header>

        <?php if (isset($error_message)): ?>
            <div class="alert alert-danger text-center">
                <?php echo htmlspecialchars($error_message); ?>
            </div>
        <?php endif; ?>

        <div class="row g-4">
            <?php if (!empty($lista_menu)): ?>
                <?php foreach ($lista_menu as $producto): ?>
                    <div class="col-12 col-md-6 col-lg-4">
                        <div class="card h-100 position-relative">
                            <?php if ($producto['destacado'] ?? false): ?>
                                <div class="position-absolute top-0 start-0 bg-warning text-dark p-1 rounded-end">
                                    <i class="fas fa-star"></i> Popular
                                </div>
                            <?php endif; ?>
                            <img 
                                src="<?php echo htmlspecialchars('../seccion/menu/img/' . $producto['foto']); ?>" 
                                class="card-img-top" 
                                alt="<?php echo htmlspecialchars($producto['nombre']); ?>" loading="lazy">
                            <div class="card-body d-flex flex-column">
                                <h2 class="card-title"><?php echo htmlspecialchars($producto['nombre']); ?></h2>
                                <p class="price">S/. <?php echo number_format($producto['precio'], 2); ?></p>
                                <form action="carrito.php" method="post" class="mt-auto">
                                    <input type="hidden" name="id" value="<?php echo htmlspecialchars($producto['ID']); ?>">
                                    <input type="hidden" name="nombre" value="<?php echo htmlspecialchars($producto['nombre']); ?>">
                                    <input type="hidden" name="precio" value="<?php echo htmlspecialchars($producto['precio']); ?>">
                                    <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($_SESSION['csrf_token'] ?? ''); ?>">
                                    <button type="submit" name="agregar" class="btn btn-add-cart">
                                        <i class="fas fa-cart-plus"></i> Agregar al Carrito
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="col-12">
                    <div class="alert alert-info text-center">
                        No hay productos disponibles en este momento.
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
