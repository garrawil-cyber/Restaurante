<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Restaurante La Sombra - Experiencia Culinaria</title>
    
    <!-- CSS Principales -->
    <link href="bootstrap5/css/bootstrap.min.css" rel="stylesheet" />
    <link href="css/styles.css" rel="stylesheet">
    
    <!-- Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;600;700&display=swap" rel="stylesheet">
    
    <!-- Iconos -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css" rel="stylesheet">
</head>
<body>
    <!-- Navegación Moderna -->
    <nav class="navbar navbar-expand-lg fixed-top custom-navbar">
        <div class="container">
            <a class="navbar-brand" href="#inicio">
                <i class="fas fa-utensils"></i> La Sombra
            </a>
            
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#menuPrincipal">
                <span class="navbar-toggler-icon"></span>
            </button>
            
            <div class="collapse navbar-collapse" id="menuPrincipal">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item"><a class="nav-link" href="#inicio">Inicio</a></li>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#menu" data-bs-toggle="dropdown">
                            Menú
                        </a>
                        <div class="dropdown-menu">
                            <a class="dropdown-item" href="#entradas">Entradas</a>
                            <a class="dropdown-item" href="#principales">Platos Principales</a>
                            <a class="dropdown-item" href="#postres">Postres</a>
                        </div>
                    </li>
                    <li class="nav-item"><a class="nav-link" href="#chefs">Chefs</a></li>
                    <li class="nav-item"><a class="nav-link" href="#contacto">Contacto</a></li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Sección Hero -->
    <header class="hero-section text-center py-5">
        <div class="container">
            <h1 class="display-4">Restaurante La Sombra</h1>
            <p class="lead">Experiencia gastronómica única</p>
            <a href="#menu" class="btn btn-primary btn-lg">Explorar Menú</a>
        </div>
    </header>

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

    <!-- Scripts -->
    <script src="js/bootstrap.bundle.min.js"></script>
    <script src="js/custom.js"></script>
</body>
</html>