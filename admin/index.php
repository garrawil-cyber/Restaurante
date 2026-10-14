<?php
session_start();

// Verificar si el usuario está autenticado
if (!isset($_SESSION['usuario'])) {
    header("Location: login.php");
    exit();
}

// Función helper para mostrar la imagen de perfil
function getProfileImage($foto) {
    if ($foto && file_exists("views/fotos/" . $foto)) {
        return "views/fotos/" . $foto;
    }
    return "views/fotos/default.png";
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Panel de administración">
    <title>Panel de Administración | Bienvenido</title>
    
    <link href="css/styles.css" rel="stylesheet" />

    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <!-- Font Awesome para iconos -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    
    <style>
        :root {
            --primary-color: #2575fc;
            --secondary-color: #6a11cb;
            --text-color: #ffffff;
            --transition-speed: 0.3s;
        }

        body {
            background: linear-gradient(135deg, var(--secondary-color), var(--primary-color));
            color: var(--text-color);
            min-height: 100vh;
            display: flex;
            align-items: center;
        }

        .dashboard-container {
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(10px);
            border-radius: 20px;
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
            padding: 2.5rem;
            transition: transform var(--transition-speed);
        }

        .dashboard-container:hover {
            transform: translateY(-5px);
        }

        .profile-image {
            width: 150px;
            height: 150px;
            border-radius: 50%;
            border: 4px solid rgba(255, 255, 255, 0.3);
            object-fit: cover;
            margin-bottom: 1.5rem;
            transition: transform var(--transition-speed);
        }

        .profile-image:hover {
            transform: scale(1.05);
        }

        .welcome-title {
            font-size: 2.5rem;
            font-weight: 700;
            margin-bottom: 1rem;
            color: var(--text-color);
        }

        .user-name {
            font-size: 1.8rem;
            color: #f0f0f0;
            margin-bottom: 1.5rem;
        }

        .welcome-text {
            font-size: 1.2rem;
            margin-bottom: 2rem;
            opacity: 0.9;
        }

        .btn-start {
            background: linear-gradient(to right, var(--secondary-color), var(--primary-color));
            color: var(--text-color);
            padding: 0.8rem 2.5rem;
            border-radius: 50px;
            border: none;
            font-size: 1.1rem;
            font-weight: 600;
            transition: all var(--transition-speed);
            text-decoration: none;
            display: inline-block;
        }

        .btn-start:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.3);
            color: var(--text-color);
        }

        .btn-start i {
            margin-left: 0.5rem;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="dashboard-container text-center">
                    <img 
                        src="<?php echo getProfileImage($_SESSION['foto']); ?>" 
                        alt="Foto de perfil"
                        class="profile-image"
                    >
                    <h1 class="welcome-title">Bienvenido al Panel</h1>
                    <div class="user-name">
                        <?php echo htmlspecialchars($_SESSION["nombre"]); ?>
                    </div>
                    <p class="welcome-text">
                        Acceda a todas las herramientas necesarias para administrar su sitio web de manera eficiente.
                    </p>
                    <a href="seccion/comentarios/" class="btn-start">
                        Acceder al Panel
                        <i class="fas fa-arrow-right"></i>
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>