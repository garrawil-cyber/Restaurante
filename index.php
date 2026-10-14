<?php

session_start();
include("admin/bd.php"); // Incluye tu archivo de conexión a la base de datos

$sentencia=$conexion->prepare("select * from tbl_banners limit 1");
$sentencia->execute();
$lista_banners=$sentencia->fetchAll(PDO::FETCH_ASSOC);

$sentencia=$conexion->prepare("select * from tbl_colaboradores order by id asc limit 4");
$sentencia->execute();
$lista_chefs=$sentencia->fetchAll(PDO::FETCH_ASSOC);

$sentencia=$conexion->prepare("select * from tbl_menu where id_categoria = 1 order by id asc");
$sentencia->execute();
$lista_menu=$sentencia->fetchAll(PDO::FETCH_ASSOC);

$sentencia=$conexion->prepare("select * from tbl_comentarios order by id asc");
$sentencia->execute();
$lista_comentarios=$sentencia->fetchAll(PDO::FETCH_ASSOC);

$sentencia=$conexion->prepare("select * from tbl_testimonios order by id asc");
$sentencia->execute();
$lista_testimonio=$sentencia->fetchAll(PDO::FETCH_ASSOC);

?>


<?php
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nomb = isset($_POST['nombre']) ? $_POST['nombre'] : "";
    $mail = isset($_POST['email']) ? $_POST['email'] : "";
    $msj = isset($_POST['mensaje']) ? $_POST['mensaje'] : "";

    if (!empty($nomb) && !empty($mail) && !empty($msj)) {
        $sentencia = $conexion->prepare("INSERT INTO tbl_comentarios (nombre, correo, mensaje) VALUES (:nomb, :mail, :msj)");
        $sentencia->bindParam(":nomb", $nomb);
        $sentencia->bindParam(":mail", $mail);
        $sentencia->bindParam(":msj", $msj);

        if ($sentencia->execute()) {
            echo json_encode(["success" => true]);
        } else {
            echo json_encode(["success" => false]);
        }
    } else {
        echo json_encode(["success" => false, "message" => "Por favor, complete todos los campos."]);
    }
    exit();
}
?>
<?php

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
 
<link href="bootstrap5/css/bootstrap.min.css" rel="stylesheet" />
<link href="css/menu.css" rel="stylesheet" />
<link href="css/chefs.css" rel="stylesheet" />
<link href="css/styles.css" rel="stylesheet" />
<link href="css/testimonios.css" rel="stylesheet" />

<link href="css/carta.css" rel="stylesheet" />

<script src="bootstrap5/js/bootstrap.min.js"></script>

<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Nunito:ital,wght@0,200..1000;1,200..1000&display=swap" rel="stylesheet">
<link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;600;700&family=Poppins:wght@400;600;700&display=swap" rel="stylesheet"> 

<!-- iconos CSS -->
<link href='https://unpkg.com/boxicons@2.0.5/css/boxicons.min.css' rel='stylesheet'>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css" crossorigin="anonymous" referrerpolicy="no-referrer">

<style>
body {
      font-family: 'Poppins', sans-serif;
      background-color: #f8f9fa;
      color: #343a40;
}

.custom-navbar {
  background-color: #212529; /* Mismo color del footer */
  backdrop-filter: blur(10px);
  box-shadow: 0 2px 10px rgba(0, 0, 0, 0.2);
  transition: all 0.3s ease;
  padding: 15px 0;
}

.navbar-brand {
  display: flex;
  align-items: center;
  font-weight: 600;
  color: #fff !important;
  transition: color 0.3s ease;
}

.brand-icon {
  color: #007bff;
  margin-right: 10px;
  font-size: 1.5rem;
}

.brand-text {
  font-size: 1.3rem;
}

.navbar-nav {
  gap: 10px;
}

.nav-link {
  color: rgba(255, 255, 255, 0.9) !important;
  font-weight: 500;
  padding: 8px 16px !important;
  border-radius: 20px;
  transition: all 0.3s ease;
}

.nav-link:hover {
  color: #fff !important;
  background-color: rgba(255, 255, 255, 0.1);
}

.navbar-toggler {
  border: none;
  padding: 0.5rem;
}

.navbar-toggler:focus {
  box-shadow: none;
  outline: none;
}

/* Estilos para navbar scrolled */
.navbar.scrolled {
  padding: 10px 0;
  background-color: #212529;
}

/* Media queries para responsividad */
@media (max-width: 991.98px) {
  .navbar-nav {
    padding: 1rem 0;
  }
  
  .nav-link {
    padding: 10px 20px !important;
  }
  
  .navbar-collapse {
    background-color: #212529;
    border-radius: 10px;
    padding: 1rem;
    margin-top: 1rem;
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.2);
  }

  .dropdown-menu {
        background-color: transparent;
        box-shadow: none;
        padding-left: 20px;
        margin-top: 0;
    }

    .dropdown-item {
        color: rgba(255, 255, 255, 0.7);
        padding: 8px 15px;
    }

    .dropdown-item:hover {
        transform: none;
    }

}

/* Estilos para el dropdown */
.dropdown-menu {
    background-color: rgba(33, 37, 41, 0.95);
    border: none;
    border-radius: 8px;
    margin-top: 10px;
    box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
}

.dropdown-item {
    color: #fff;
    padding: 10px 20px;
    transition: all 0.3s ease;
}

.dropdown-item:hover {
    background-color: rgba(255, 255, 255, 0.1);
    color: #C8A97E;
    transform: translateX(5px);
}

/* Animación del dropdown */
.dropdown-menu.show {
    animation: fadeIn 0.3s ease;
}

/* Estilo para el toggle del dropdown */
.dropdown-toggle::after {
    transition: transform 0.3s ease;
}

.show .dropdown-toggle::after {
    transform: rotate(-180deg);
}

@keyframes fadeIn {
    from {
        opacity: 0;
        transform: translateY(-10px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

</style>

 </head>
 <body>
  
  <nav class="navbar navbar-expand-lg fixed-top custom-navbar navbar-dark">
  <div class="container">
    <a class="navbar-brand" href="#Menu">
      <i class="fas fa-utensils brand-icon"></i>
      <span class="brand-text">Restaurante la Sombra</span>
    </a>
    
    <button class="navbar-toggler" type="button" 
            data-bs-toggle="collapse" 
            data-bs-target="#navbarNav" 
            aria-controls="navbarNav" 
            aria-expanded="false" 
            aria-label="Toggle navigation">
      <span class="navbar-toggler-icon"></span>
    </button>
    
    <div class="collapse navbar-collapse" id="navbarNav">
      <ul class="navbar-nav ms-auto">
        <li class="nav-item">
          <a class="nav-link" href="#bienvenida">Inicio</a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="#Menu">Menú del día</a>
        </li>
        <li class="nav-item dropdown">
          <a class="nav-link dropdown-toggle" href="#Carta" id="navbarDropdown" role="button" 
             data-bs-toggle="dropdown" aria-expanded="false">
            Platos a la Carta
          </a>
          <ul class="dropdown-menu" aria-labelledby="navbarDropdown">
            <li><a class="dropdown-item" href="#entradas">Entradas</a></li>
            <li><a class="dropdown-item" href="#principales">Platos principales</a></li>
            <li><a class="dropdown-item" href="#postres">Postres</a></li>
            <li><a class="dropdown-item" href="#bebidas">Bebidas</a></li>
          </ul>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="#Chefs">Chefs</a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="#Testimonios">Testimonios</a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="contacto.php">Contactos</a>
        </li>
      </ul>
    </div>
  </div>
</nav>

<section id="bienvenida" class="welcome-section py-5">
  <div class="container">
    <div class="row align-items-center">
      <div class="col-lg-6 mb-4 mb-lg-0">
        <div class="welcome-content">
          <span class="subtitle">Bienvenidos a</span>
          <h2 class="title">Restaurante la Sombra</h2>
          <div class="separator">
            <span class="line"></span>
            <i class="fas fa-star"></i>
            <span class="line"></span>
          </div>
          <p class="description">
            Descubra una experiencia culinaria excepcional donde cada plato cuenta una historia. 
            Nuestro equipo de chefs expertos combina técnicas tradicionales con innovación 
            contemporánea para crear sabores inolvidables.
          </p>
          <a href="#Menu" class="btn btn-primary btn-lg rounded-pill">Ver Menú</a>
        </div>
      </div>
      <div class="col-lg-6">
        <div class="welcome-images">
          <div class="image-grid">
            <img src="images/local.png" alt="Interior del restaurante" class="img-2">
            <img src="images/ceviche_pescado.png" alt="Plato especial" class="img-1">
            <div class="experience-badge">
              <span class="number">15</span>
              <span class="text">Años de<br>Experiencia</span>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>

    <!-- Sección Servicios -->
    <section id="servicios" class="py-5">
        <div class="container">
            <div class="row text-center">
                <div class="col-md-4">
                    <i class="fas fa-utensils fa-3x mb-3"></i>
                    <h3>Cocina Premium</h3>
                    <p>Ingredientes de alta calidad</p>
                </div>
                <div class="col-md-4">
                    <i class="fas fa-wine-glass-alt fa-3x mb-3"></i>
                    <h3>Ambiente Elegante</h3>
                    <p>Diseño y confort exclusivo</p>
                </div>
                <div class="col-md-4">
                    <i class="fas fa-star fa-3x mb-3"></i>
                    <h3>Servicio Excepcional</h3>
                    <p>Atención personalizada</p>
                </div>
            </div>
        </div>
    </section>



<!--la carta -->
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

<!-- fin de  la Carta-->

<section id="Chefs" class="py-5 bg-light">
        <div class="container">
            <div class="text-center mb-5">
                <h2 class="display-4 mb-3">Nuestros Chefs</h2>
                <div class="separator d-flex justify-content-center align-items-center mb-4">
                    <span class="line mx-3"></span>
                    <i class="fas fa-hat-chef text-primary"></i>
                    <span class="line mx-3"></span>
                </div>
                <p class="text-muted">Conoce a nuestro equipo de expertos culinarios</p>
            </div>

            <div class="row g-4">
                <?php foreach ($lista_chefs as $lista) { ?>
                    <div class="col-md-6 col-lg-3">
                        <div class="chef-card card border-0 shadow-sm h-100">
                            <div class="position-relative overflow-hidden">
                                <img src="images/colaboradores/<?php echo $lista['foto']; ?>"
                                     class="card-img-top chef-img"
                                     alt="<?php echo $lista['titulo']; ?>"
                                     style="height: 300px; object-fit: cover;">
                                <div class="chef-overlay">
                                    <div class="social-links">
                                        <?php if (!empty($lista['linkfacebook'])) { ?>
                                            <a href="<?php echo $lista['linkfacebook']; ?>" class="social-link" target="_blank">
                                                <i class="fab fa-facebook-f"></i>
                                            </a>
                                        <?php } ?>
                                        <?php if (!empty($lista['linkinstagram'])) { ?>
                                            <a href="<?php echo $lista['linkinstagram']; ?>" class="social-link" target="_blank">
                                                <i class="fab fa-instagram"></i>
                                            </a>
                                        <?php } ?>
                                        <?php if (!empty($lista['linklinkedin'])) { ?>
                                            <a href="<?php echo $lista['linklinkedin']; ?>" class="social-link" target="_blank">
                                                <i class="fab fa-linkedin-in"></i>
                                            </a>
                                        <?php } ?>
                                    </div>
                                </div>
                            </div>
                            <div class="card-body text-center">
                                <h5 class="card-title mb-2"><?php echo $lista['titulo']; ?></h5>
                                <p class="card-text text-muted"><?php echo $lista['descripcion']; ?></p>
                            </div>
                        </div>
                    </div>
                <?php } ?>
            </div>
        </div>
</section><!-- Fin de Chefs -->
 
<section id="Testimonios" class="bg-light py-5">
        <div class="container">
            <h2 class="text-center mb-4">Testimonios</h2>
            <div class="row">
                <?php foreach ($lista_testimonio as $lista) { ?>
                    <div class="col-md-4 d-flex mb-4">
                        <div class="testimonial-card w-100">
                            <div class="card-body">
                                <p class="card-text"><?php echo $lista['opinion']; ?></p>
                            </div>
                            <div class="card-footer text-muted">
                                <p><?php echo $lista['nombre']; ?></p>
                            </div>
                        </div>
                    </div>
                <?php } ?>
            </div>
        </div>
    </section>

<!-- El Menu -->
<section id="Menu" class="py-5" style="background-color: #f8f9fa;">
<div class="container mt-5">
        <header class="header">
            <h1>Nuestro Menú</h1><i class="fas fa-utensils brand-icon"></i>
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
                                src="<?php echo htmlspecialchars('admin/seccion/menu/img/' . $producto['foto']); ?>" 
                                class="card-img-top" 
                                alt="<?php echo htmlspecialchars($producto['nombre']); ?>" loading="lazy">
                            <div class="card-body d-flex flex-column">
                                <h2 class="card-title"><?php echo htmlspecialchars($producto['nombre']); ?></h2>
                                <p class="price">S/. <?php echo number_format($producto['precio'], 2); ?></p>
                                <form action="admin/carrito/carrito.php" method="post" class="mt-auto">
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

    <!-- Mostrar EL CARRITO FLOTANTES CON LA CANTIDAD DE PRODUCTOS SOLICITADOS -->
<a href="admin/carrito/carrito.php" class="btn-carrito-flotante">
    <i class="fas fa-shopping-cart fa-lg"></i>
    <span class="carrito-contador">
        <?php 
        $cantidad_total = 0;
        if (isset($_SESSION['carrito']) && !empty($_SESSION['carrito'])) {
            foreach ($_SESSION['carrito'] as $producto) {
                $cantidad_total += isset($producto['cantidad']) ? intval($producto['cantidad']) : 1;
            }
        }
        echo $cantidad_total;
        ?>
    </span>
</a>
</section>
<!-- Fin del Menu -->

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<script>
// Añadir clase 'scrolled' al navbar cuando se hace scroll
window.addEventListener('scroll', function() {
  const navbar = document.querySelector('.custom-navbar');
  if (window.scrollY > 50) {
    navbar.classList.add('scrolled');
  } else {
    navbar.classList.remove('scrolled');
  }
});

// Cerrar menú móvil al hacer clic en un enlace
document.querySelectorAll('.nav-link').forEach(link => {
  link.addEventListener('click', () => {
    const navbarCollapse = document.querySelector('.navbar-collapse');
    if (navbarCollapse.classList.contains('show')) {
      navbarCollapse.classList.remove('show');
    }
  });
});
</script>

<?php include("footer.php");?>

