<?php
// Configuración inicial de sesión segura
ini_set('session.cookie_httponly', 1);
ini_set('session.use_only_cookies', 1);
session_start();

// Headers de seguridad
header('X-Frame-Options: DENY');
header('X-XSS-Protection: 1; mode=block');
header('X-Content-Type-Options: nosniff');

// Incluir archivo de conexión
require_once("bd.php");

// Función para sanitizar input
function sanitize_input($data) {
    return htmlspecialchars(strip_tags(trim($data)));
}

// Verificar si ya hay sesión activa
if (isset($_SESSION['logueado']) && $_SESSION['logueado'] === true) {
    header("Location: index.php");
    exit();
}

// Procesar el formulario
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $usuario = sanitize_input($_POST['usuario'] ?? "");
    $password = $_POST['password'] ?? "";

    if (!empty($usuario) && !empty($password)) {
        try {
            // Consulta segura usando password_hash
            $sentencia = $conexion->prepare("SELECT * FROM tbl_usuarios WHERE usuario = :usuario");
            $sentencia->bindParam(":usuario", $usuario);
            $sentencia->execute();
            
            if ($sentencia->rowCount() == 1) {
                $registro = $sentencia->fetch(PDO::FETCH_ASSOC);
                
                // Verificar contraseña con hash
                if (trim($password)==trim($registro['password'])) {
                    // Regenerar ID de sesión por seguridad
                    session_regenerate_id(true);
                    
                    // Establecer variables de sesión
                    $_SESSION['usuario'] = $registro['usuario'];
                    $_SESSION['nombre'] = $registro['nombre'];
                    $_SESSION['foto'] = $registro['foto'];
                    $_SESSION['rol'] = $registro['rol'];
                    $_SESSION['logueado'] = true;
                    $_SESSION['last_activity'] = time();

                    header("Location: index.php");
                    exit();
                }
            }
            // Error de credenciales
            header("Location: login.php?error=1");
            exit();
        } catch (PDOException $e) {
            error_log("Error de base de datos: " . $e->getMessage());
            header("Location: login.php?error=3");
            exit();
        }
    } else {
        header("Location: login.php?error=2");
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Sistema de Control de Asistencia QR - Login">
    <title>AsistenciaQR - Iniciar Sesión</title>

    <!-- CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" rel="stylesheet">

    <style>
        :root {
            --primary-color: #2a5298;
            --secondary-color: #4a90e2;
            --success-color: #28a745;
            --error-color: #dc3545;
            --warning-color: #ffc107;
        }

        body {
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
            font-family: 'Segoe UI', system-ui, -apple-system, sans-serif;
        }

        .login-container {
            width: 100%;
            max-width: 400px;
            margin: 1rem;
        }

        .login-form {
            background: rgba(255, 255, 255, 0.95);
            padding: 2.5rem;
            border-radius: 15px;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.15);
            backdrop-filter: blur(10px);
        }

        .login-header {
            text-align: center;
            margin-bottom: 2rem;
        }

        .login-header img {
            width: 100px;
            height: 100px;
            object-fit: cover;
            border-radius: 50%;
            padding: 5px;
            background: white;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            margin-bottom: 1.5rem;
        }

        .form-control {
            border: 2px solid #e1e1e1;
            padding: 0.75rem;
            font-size: 1rem;
            border-radius: 8px;
            transition: all 0.3s ease;
        }

        .form-control:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 0.2rem rgba(42, 82, 152, 0.25);
        }

        .btn-login {
            background: var(--primary-color);
            border: none;
            padding: 0.75rem;
            font-size: 1.1rem;
            font-weight: 600;
            border-radius: 8px;
            transition: all 0.3s ease;
        }

        .btn-login:hover {
            background: var(--secondary-color);
            transform: translateY(-2px);
        }

        .alert {
            border-radius: 8px;
            padding: 1rem;
            margin-bottom: 1.5rem;
            border: none;
            animation: fadeIn 0.5s ease;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(-10px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .password-container {
            position: relative;
        }

        .toggle-password {
            position: absolute;
            right: 1rem;
            top: 50%;
            transform: translateY(-50%);
            cursor: pointer;
            color: #666;
            transition: color 0.3s ease;
        }

        .toggle-password:hover {
            color: var(--primary-color);
        }

        .form-label {
            font-weight: 500;
            color: #444;
        }

        .invalid-feedback {
            font-size: 0.875rem;
            color: var(--error-color);
        }
    </style>
</head>
<body>
    <div class="login-container">
        <div class="login-form">
            <div class="login-header">
                <img src="../images/seg.jpg" alt="Logo AsistenciaQR">
                <h2 class="fw-bold text-dark mb-4">Iniciar Sesión</h2>
            </div>

            <?php if (isset($_GET['error'])): ?>
                <div class="alert <?php echo $_GET['error'] == 1 ? 'alert-danger' : ($_GET['error'] == 2 ? 'alert-warning' : 'alert-danger'); ?> d-flex align-items-center">
                    <i class="fas <?php echo $_GET['error'] == 1 ? 'fa-exclamation-circle' : 'fa-exclamation-triangle'; ?> me-2"></i>
                    <?php
                        switch($_GET['error']) {
                            case 1:
                                echo "Credenciales incorrectas";
                                break;
                            case 2:
                                echo "Por favor, complete todos los campos";
                                break;
                            case 3:
                                echo "Error del sistema. Por favor, intente más tarde";
                                break;
                        }
                    ?>
                </div>
            <?php endif; ?>

            <form id="loginForm" method="post" action="login.php" class="needs-validation" novalidate>
                <div class="mb-4">
                    <label for="usuario" class="form-label">
                        <i class="fas fa-user-secret">&nbsp;</i>Usuario
                    </label>
                    <input type="text" 
                           class="form-control" 
                           id="usuario" 
                           name="usuario" 
                           pattern="[a-zA-Z0-9]{4,20}" 
                           maxlength="20" 
                           required 
                           autocomplete="username">
                    <div class="invalid-feedback">
                        El usuario debe tener entre 4 y 20 caracteres alfanuméricos
                    </div>
                </div>

                <div class="mb-4">
                    <label for="password" class="form-label">
                        <i class="fas fa-key">&nbsp;</i>Contraseña
                    </label>
                    <div class="password-container">
                        <input type="password" 
                               class="form-control" 
                               id="password" 
                               name="password" 
                               pattern="[a-zA-Z0-9$@.-]{5,100}" 
                               maxlength="100" 
                               required 
                               autocomplete="current-password">
                        <i class="fas fa-eye toggle-password" id="togglePassword">&nbsp;</i>
                        <div class="invalid-feedback">
                            La contraseña debe tener al menos 5 caracteres
                        </div>
                    </div>
                </div>

                <button type="submit" class="btn btn-login btn-primary w-100">
                    <i class="fas fa-sign-in-alt me-2"></i>Ingresar
                </button>
            </form>
        </div>
    </div>

    <!-- Scripts -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Validación del formulario
            const form = document.getElementById('loginForm');
            
            form.addEventListener('submit', function(event) {
                if (!form.checkValidity()) {
                    event.preventDefault();
                    event.stopPropagation();
                }
                form.classList.add('was-validated');
            });

            // Toggle password visibility
            const togglePassword = document.getElementById('togglePassword');
            const passwordInput = document.getElementById('password');

            togglePassword.addEventListener('click', function() {
                const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
                passwordInput.setAttribute('type', type);
                this.classList.toggle('fa-eye');
                this.classList.toggle('fa-eye-slash');
            });

            // Limpiar alertas después de 5 segundos
            setTimeout(function() {
                const alerts = document.querySelectorAll('.alert');
                alerts.forEach(alert => {
                    alert.style.opacity = '0';
                    alert.style.transition = 'opacity 0.5s ease';
                    setTimeout(() => alert.remove(), 500);
                });
            }, 5000);
        });
    </script>
</body>
</html>