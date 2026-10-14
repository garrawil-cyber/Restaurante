<?php
// Simulación de datos de platos (en producción esto vendría de una base de datos)
$categorias = [
    'entradas' => [
        ['id' => 1, 'nombre' => 'Causa Limeña', 'descripcion' => 'Pasta de papa amarilla aderezada con limón, rellena de pollo o atún', 'precio' => 25.00, 'imagen' => 'Causa_limena.jpg'],
        ['id' => 2, 'nombre' => 'Ceviche Clásico', 'descripcion' => 'Pescado fresco marinado en limón, con choclo, camote y cancha', 'precio' => 35.00, 'imagen' => 'ceviche_clasico.jpg'],
        ['id' => 3, 'nombre' => 'Pulpo al Olivo', 'descripcion' => 'Finas láminas de pulpo con salsa de aceitunas negras', 'precio' => 40.00, 'imagen' => 'pulpo_olivo.jpg'],
    ],
    'principales' => [
        ['id' => 4, 'nombre' => 'Lomo Saltado', 'descripcion' => 'Trozos de lomo fino saltados con verduras, papas fritas y arroz', 'precio' => 45.00, 'imagen' => 'lomo_saltado.jpg'],
        ['id' => 5, 'nombre' => 'Ají de Gallina', 'descripcion' => 'Cremosa salsa de ají amarillo con gallina deshilachada sobre papas', 'precio' => 38.00, 'imagen' => 'aji_gallina.jpg'],
        ['id' => 6, 'nombre' => 'Pescado a lo Macho', 'descripcion' => 'Filete de pescado en salsa de mariscos picante', 'precio' => 48.00, 'imagen' => 'pescado_lo_macho.jpg'],
    ],
    'postres' => [
        ['id' => 7, 'nombre' => 'Suspiro a la Limeña', 'descripcion' => 'Dulce de leche con merengue italiano y canela', 'precio' => 20.00, 'imagen' => 'suspiro.jpg'],
        ['id' => 8, 'nombre' => 'Tres Leches', 'descripcion' => 'Bizcocho bañado en tres tipos de leche con cobertura de merengue', 'precio' => 22.00, 'imagen' => 'tres_leches.jpg'],
        ['id' => 9, 'nombre' => 'Picarones', 'descripcion' => 'Rosquillas peruanas bañadas en miel de chancaca', 'precio' => 18.00, 'imagen' => 'picarones.jpg'],
    ]
];
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
        body {
            font-family: 'Poppins', sans-serif;
            background-color: #f8f9fa;
            color: #343a40;
        }
        .categoria-title {
            font-family: 'Playfair Display', serif;
            font-weight: 700;
            text-align: center;
            margin: 2rem 0 1rem;
            color: #d63384;
        }
        .plato-card {
            border: 1px solid #dee2e6;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            margin-bottom: 2rem;
            background: #ffffff;
        }
        .plato-card img {
            width: 100%;
            height: 200px;
            object-fit: cover;
        }
        .plato-info {
            padding: 1rem;
        }
        .plato-nombre {
            font-size: 1.25rem;
            font-weight: 600;
            margin-bottom: 0.5rem;
        }
        .plato-descripcion {
            font-size: 0.9rem;
            color: #6c757d;
            margin-bottom: 0.75rem;
        }
        .plato-precio {
            font-size: 1rem;
            font-weight: 700;
            color: #d63384;
        }
        .menu-nav {
            background-color: #d63384;
            color: white;
            padding: 1rem 0;
        }
        .menu-nav .nav-link {
            color: white;
            font-weight: 500;
        }
        .menu-nav .nav-link.active {
            font-weight: 700;
            text-decoration: underline;
        }
        .recomendado-badge {
            position: absolute;
            top: 10px;
            left: 10px;
            background-color: #ffc107;
            color: #343a40;
            padding: 0.25rem 0.5rem;
            font-size: 0.8rem;
            font-weight: 700;
            border-radius: 5px;
        }
        .btn-volver-arriba {
            position: fixed;
            bottom: 20px;
            right: 20px;
            background-color: #d63384;
            color: white;
            border: none;
            border-radius: 50%;
            width: 50px;
            height: 50px;
            display: flex;
            justify-content: center;
            align-items: center;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            cursor: pointer;
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
            </ul>
        </div>
    </nav>

    <div class="container">
        <!-- Entradas -->
        <section id="entradas">
            <h2 class="categoria-title">Entradas</h2>
            <div class="row">
                <?php foreach ($categorias['entradas'] as $plato): ?>
                    <div class="col-md-6 col-lg-4">
                        <div class="plato-card">
                            <img src="images/<?php echo htmlspecialchars($plato['imagen']); ?>" alt="<?php echo htmlspecialchars($plato['nombre']); ?>" class="plato-imagen">
                            <div class="plato-info">
                                <h3 class="plato-nombre"><?php echo htmlspecialchars($plato['nombre']); ?></h3>
                                <p class="plato-descripcion"><?php echo htmlspecialchars($plato['descripcion']); ?></p>
                                <div class="plato-precio">S/. <?php echo number_format($plato['precio'], 2); ?></div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </section>

        <!-- Platos Principales -->
        <section id="principales">
            <h2 class="categoria-title">Platos Principales</h2>
            <div class="row">
                <?php foreach ($categorias['principales'] as $plato): ?>
                    <div class="col-md-6 col-lg-4">
                        <div class="plato-card position-relative">
                            <?php if ($plato['id'] === 4): ?>
                                <div class="recomendado-badge">
                                    <i class="fas fa-star"></i> Recomendado
                                </div>
                            <?php endif; ?>
                            <img src="images/<?php echo htmlspecialchars($plato['imagen']); ?>" alt="<?php echo htmlspecialchars($plato['nombre']); ?>" class="plato-imagen">
                            <div class="plato-info">
                                <h3 class="plato-nombre"><?php echo htmlspecialchars($plato['nombre']); ?></h3>
                                <p class="plato-descripcion"><?php echo htmlspecialchars($plato['descripcion']); ?></p>
                                <div class="plato-precio">S/. <?php echo number_format($plato['precio'], 2); ?></div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </section>

        <!-- Postres -->
        <section id="postres">
            <h2 class="categoria-title">Postres</h2>
            <div class="row">
                <?php foreach ($categorias['postres'] as $plato): ?>
                    <div class="col-md-6 col-lg-4">
                        <div class="plato-card">
                            <img src="images/<?php echo htmlspecialchars($plato['imagen']); ?>" alt="<?php echo htmlspecialchars($plato['nombre']); ?>" class="plato-imagen">
                            <div class="plato-info">
                                <h3 class="plato-nombre"><?php echo htmlspecialchars($plato['nombre']); ?></h3>
                                <p class="plato-descripcion"><?php echo htmlspecialchars($plato['descripcion']); ?></p>
                                <div class="plato-precio">S/. <?php echo number_format($plato['precio'], 2); ?></div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </section>
    </div>

    <button class="btn-volver-arriba" onclick="window.scrollTo({ top: 0, behavior: 'smooth' });">
        <i class="fas fa-arrow-up"></i>
    </button>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
