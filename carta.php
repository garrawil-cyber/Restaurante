<?php
session_start();
// Añadir al inicio del script, después de session_start() si es necesario
if (!isset($_SESSION['carrito'])) {
    $_SESSION['carrito'] = [];
}

// Contar elementos en el carrito de forma más segura
$contador_carrito = count($_SESSION['carrito']);
include("admin/bd.php");

// Función para obtener ítems de menú por categoría
function obtenerMenuPorCategoria($conexion, $id_categoria) {
    $sentencia = $conexion->prepare("SELECT * FROM tbl_menu WHERE id_categoria = :categoria ORDER BY id ASC");
    $sentencia->bindParam(':categoria', $id_categoria, PDO::PARAM_INT);
    $sentencia->execute();
    return $sentencia->fetchAll(PDO::FETCH_ASSOC);
}

// Definir categorías
$categorias = [
    'entradas' => 2,
    'principales' => 4,
    'postres' => 6,
    'bebidas' => 7
];

// Almacenar los menús en un arreglo
$menus = [];
foreach ($categorias as $nombre => $id) {
    $menus[$nombre] = obtenerMenuPorCategoria($conexion, $id);
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Carta - Restaurante La Sombra</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;600;700&family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">

    <style>
        :root {
            --primary-color: #2C3E50;
            --accent-color: #E74C3C;
            --secondary-color: #34495E;
            --background-light: #F8F9FA;
            --text-primary: #2C3E50;
            --text-secondary: #7F8C8D;
            --success-color: #27AE60;
            --card-shadow: 0 8px 20px rgba(0,0,0,0.1);
            --hover-transition: all 0.3s ease;
        }

        body {
            font-family: 'Poppins', sans-serif;
            background-color: var(--background-light);
            color: var(--text-primary);
        }

        .hero-section {
            background: linear-gradient(rgba(0,0,0,0.7), rgba(0,0,0,0.7)), url('images/hero-bg.jpg');
            background-size: cover;
            background-position: center;
            padding: 6rem 0;
            color: white;
            text-align: center;
            margin-bottom: 3rem;
        }

        .hero-title {
            font-family: 'Playfair Display', serif;
            font-size: 3.5rem;
            font-weight: 700;
            margin-bottom: 1rem;
        }

        .menu-nav {
            background-color: var(--primary-color);
            padding: 1rem 0;
            position: sticky;
            top: 0;
            z-index: 1000;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }

        .menu-nav .nav-link {
            color: white;
            font-weight: 500;
            padding: 0.5rem 1.5rem;
            margin: 0 0.5rem;
            border-radius: 25px;
            transition: var(--hover-transition);
        }

        .menu-nav .nav-link:hover {
            background-color: rgba(255,255,255,0.1);
        }

        .menu-nav .nav-link.active {
            background-color: var(--accent-color);
        }

        .categoria-title {
            font-family: 'Playfair Display', serif;
            font-weight: 700;
            color: var(--primary-color);
            text-align: center;
            margin: 3rem 0 2rem;
            position: relative;
            padding-bottom: 1rem;
        }

        .categoria-title::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 50%;
            transform: translateX(-50%);
            width: 80px;
            height: 3px;
            background-color: var(--accent-color);
        }

        .plato-card {
            border: none;
            border-radius: 15px;
            overflow: hidden;
            box-shadow: var(--card-shadow);
            margin-bottom: 2rem;
            transition: var(--hover-transition);
            background: white;
            position: relative;
        }

        .plato-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 12px 25px rgba(0,0,0,0.15);
        }

        .plato-imagen-container {
            position: relative;
            overflow: hidden;
            height: 250px;
        }

        .plato-card img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: var(--hover-transition);
        }

        .plato-card:hover img {
            transform: scale(1.05);
        }

        .plato-info {
            padding: 1.5rem;
        }

        .plato-nombre {
            font-size: 1.4rem;
            font-weight: 600;
            margin-bottom: 0.75rem;
            color: var(--primary-color);
        }

        .plato-descripcion {
            font-size: 0.95rem;
            color: var(--text-secondary);
            margin-bottom: 1rem;
            line-height: 1.6;
        }

        .plato-footer {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding-top: 1rem;
            border-top: 1px solid #eee;
        }

        .plato-precio {
            font-size: 1.25rem;
            font-weight: 700;
            color: var(--accent-color);
        }

        .btn-agregar {
            background-color: var(--success-color);
            color: white;
            border: none;
            padding: 0.5rem 1.25rem;
            border-radius: 25px;
            font-weight: 500;
            transition: var(--hover-transition);
        }

        .btn-agregar:hover {
            background-color: #219a52;
            transform: translateY(-2px);
        }

        .recomendado-badge {
            position: absolute;
            top: 1rem;
            right: 1rem;
            background-color: var(--accent-color);
            color: white;
            padding: 0.5rem 1rem;
            border-radius: 25px;
            font-size: 0.9rem;
            font-weight: 500;
            z-index: 1;
            box-shadow: 0 2px 10px rgba(231, 76, 60, 0.3);
        }

        @media (max-width: 768px) {
            .hero-title {
                font-size: 2.5rem;
            }
            
            .menu-nav .nav-link {
                padding: 0.5rem 1rem;
                margin: 0 0.25rem;
            }

            .plato-imagen-container {
                height: 200px;
            }
        }
    </style>
    
    <!-- Estilos adicionales para el carrito flotante -->
<style>
    .carrito-flotante-container {
        position: fixed;
        bottom: 2rem;
        right: 2rem;
        z-index: 1050;
        transition: transform 0.3s ease;
    }

    .btn-carrito-flotante {
        display: flex;
        align-items: center;
        justify-content: center;
        width: 60px;
        height: 60px;
        background-color: var(--accent-color);
        border-radius: 50%;
        box-shadow: 0 4px 15px rgba(0,0,0,0.2);
        text-decoration: none;
        position: relative;
        transition: all 0.3s ease;
    }

    .btn-carrito-flotante:hover {
        transform: scale(1.1) rotate(6deg);
        background-color: var(--primary-color);
    }

    .carrito-icono {
        position: relative;
        color: white;
    }

    .carrito-contador {
        position: absolute;
        top: -10px;
        right: -10px;
        background-color: var(--primary-color);
        color: white;
        border-radius: 50%;
        width: 24px;
        height: 24px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 0.75rem;
        font-weight: bold;
        box-shadow: 0 2px 5px rgba(0,0,0,0.3);
    }
</style>
</head>
<body>

<header class="py-3 text-center">
        <h1 class="fw-bold">Restaurante La Sombra</h1>
        <p class="lead text-muted">Sabores que deleitan tus sentidos</p>
    </header>

    <nav class="menu-nav">
        <div class="container">
            <ul class="nav justify-content-center">
                <li class="nav-item">
                    <a class="nav-link active" href="#entradas">Entradas</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="#principales">Platos Principales</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="#postres">Postres</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="#bebidas">Bebidas</a>
                </li>
            </ul>
        </div>
    </nav>

    <div class="container">
        <?php foreach ($menus as $categoria => $platos): ?>
            <section id="<?php echo $categoria; ?>">
                <h2 class="categoria-title"><?php echo ucfirst($categoria); ?></h2>
                <div class="row">
                    <?php foreach ($platos as $plato): ?>
                        <div class="col-md-6 col-lg-4">
                            <div class="plato-card">
                                <div class="plato-imagen-container">
                                <?php if ($plato['destacado'] ?? false): ?>
                                <div class="position-absolute top-0 start-0 bg-warning text-dark p-1 rounded-end">
                                <i class="fas fa-star me-1"></i> Recomendado
                                </div>
                            <?php endif; ?>
                                    <img src="<?php echo htmlspecialchars('admin/seccion/menu/img/' .$plato['foto']); ?>" 
                                         alt="<?php echo htmlspecialchars($plato['nombre']); ?>" 
                                         class="plato-imagen">
                                </div>
                                <div class="plato-info">
                                    <h3 class="plato-nombre"><?php echo htmlspecialchars($plato['nombre']); ?></h3>
                                    <p class="plato-descripcion"><?php echo htmlspecialchars($plato['ingredientes']); ?></p>
                                    <div class="plato-footer">
                                        <div class="plato-precio">S/. <?php echo number_format($plato['precio'], 2); ?></div>
                                        <form action="admin/carrito/carrito.php" method="post" class="mt-auto">
                                            <input type="hidden" name="id" value="<?php echo $plato['ID']; ?>">
                                            <input type="hidden" name="nombre" value="<?php echo htmlspecialchars($plato['nombre']); ?>">
                                            <input type="hidden" name="precio" value="<?php echo $plato['precio']; ?>">
                                            <button type="submit" name="agregar" class="btn btn-agregar">
                                                <i class="fas fa-cart-plus me-2"></i>Agregar
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </section>
        <?php endforeach; ?>
    </div>
<!-- Añadir después de cerrar el div del container -->
<div class="carrito-flotante-container">
    <a href="admin/carrito/carrito.php" class="btn-carrito-flotante">
        <div class="carrito-icono">
            <i class="fas fa-shopping-cart fa-lg"></i>
            <?php if ($contador_carrito > 0): ?>
                <span class="carrito-contador">
                    <?php echo $contador_carrito; ?>
                </span>
            <?php endif; ?>
        </div>
    </a>
</div>

</body>
</html>