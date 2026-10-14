<?php
    session_start();
    
    $protocol = isset($_SERVER['HTTPS']) && !empty($_SERVER['HTTPS']) ? 'https://' : 'http://';
    $url_base = $protocol . $_SERVER['HTTP_HOST'] . "/Restaurante/admin/";

    if (!isset($_SESSION["usuario"])) {
        header("Location:" . $url_base . "login.php");
        exit();
    }
?>

<!doctype html>
<html lang="es">
<head>
    <title>Restaurante</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- Recursos externos -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.10.21/css/jquery.dataTables.min.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.datatables.net/1.10.21/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <!-- Estilos personalizados -->
    <link rel="stylesheet" href="<?php echo $url_base; ?>css/menu.css">

 <script>

    function confirmarCerrarSesion() {
        Swal.fire({
            title: '¿Cerrar sesión?',
            text: 'Serás redirigido a la página de inicio de sesión',
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#2a5298',
            cancelButtonColor: '#dc3545',
            confirmButtonText: 'Sí, cerrar sesión',
            cancelButtonText: 'Cancelar'
        }).then((result) => {
            if (result.isConfirmed) {
                Swal.fire({
                    title: 'Cerrando sesión...',
                    text: 'Hasta pronto',
                    icon: 'success',
                    timer: 1500,
                    showConfirmButton: false
                }).then(() => {
                    window.location.href = "<?php echo $url_base; ?>controlador_cerrar_sesion.php";
                });
            }
        });
    }

    document.addEventListener('DOMContentLoaded', function() {
        const currentPath = window.location.pathname;
        document.querySelectorAll('.nav-link').forEach(link => {
            if (link.getAttribute('href') && currentPath.includes(link.getAttribute('href'))) {
                    link.classList.add('active');
            }
        });
    });

</script>
</head>
<body>
<header>
    <nav class="navbar navbar-expand-lg navbar-light bg-light shadow-sm">
        <div class="container-fluid">
            <a class="navbar-brand" href="<?php echo $url_base; ?>">
                <i class="fas fa-desktop me-2"></i>Administrador
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="<?php echo $url_base; ?>seccion/banners/"><i class="fas fa-image"></i> Banners</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="<?php echo $url_base; ?>seccion/colaboradores/"><i class="fas fa-users"></i> Colaboradores</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="<?php echo $url_base; ?>seccion/testimonios/"><i class="fas fa-comment"></i> Testimonios</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="<?php echo $url_base; ?>seccion/categoria/"><i class="fas fa-tags"></i> Categorías</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="<?php echo $url_base; ?>seccion/menu/"><i class="fas fa-utensils"></i> Carta Menú</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="<?php echo $url_base; ?>seccion/ordenes/"><i class="fas fa-shopping-cart"></i> Ver Pedidos</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="<?php echo $url_base; ?>seccion/comentarios/"><i class="fas fa-comments"></i> Comentarios</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="<?php echo $url_base; ?>seccion/usuarios/"><i class="fas fa-user-cog"></i> Usuarios</a>
                    </li>
                </ul>
                <div class="user-profile d-flex align-items-center">
                    <span class="me-2">
                        <i class="fas fa-user"></i> <?php echo htmlspecialchars($_SESSION['nombre'] ?? 'Usuario'); ?>
                    </span>
                    <a href="javascript:void(0);" onclick="confirmarCerrarSesion()" title="Cerrar sesión">
                        <?php
                            $file_path = "../../views/fotos/" . ($_SESSION['foto'] ?? 'default.png');
                        ?>
                        <img class="profile-image" src="<?php echo $file_path; ?>" alt="Foto de perfil">
                    </a>
                </div>
            </div>
        </div>
    </nav>
</header>
<main class="container my-4">
