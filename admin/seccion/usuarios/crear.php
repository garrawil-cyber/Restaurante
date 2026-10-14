<?php
include("../../bd.php");

$errors = [];
$formData = [
    'nombre' => '',
    'usu' => '',
    'clave' => '',
    'email' => '',
    'foto' => ''
];

if ($_POST) {
    // Sanitize and validate input
    $formData['nombre'] = isset($_POST['nombre']) ? trim($_POST['nombre']) : '';
    $formData['usu'] = isset($_POST['usu']) ? trim($_POST['usu']) : '';
    $formData['clave'] = isset($_POST['clave']) ? $_POST['clave'] : '';
    $formData['email'] = isset($_POST['email']) ? trim($_POST['email']) : '';

    // Validate Name
    if (empty($formData['nombre'])) {
        $errors['nombre'] = "El nombre es obligatorio";
    } elseif (!preg_match("/^[A-Za-zÀ-ÿñÑ ]+$/u", $formData['nombre'])) {
        $errors['nombre'] = "El nombre solo puede contener letras y espacios";
    }

    // Validate Username
    if (empty($formData['usu'])) {
        $errors['usu'] = "El nombre de usuario es obligatorio";
    } elseif (strlen($formData['usu']) < 4) {
        $errors['usu'] = "El nombre de usuario debe tener al menos 4 caracteres";
    } else {
        // Check if username already exists
        $sql_usu = $conexion->prepare("SELECT COUNT(*) AS total FROM tbl_usuarios WHERE usuario = :usu");
        $sql_usu->bindParam(":usu", $formData['usu']);
        $sql_usu->execute();
        
        if ($sql_usu->fetchColumn() > 0) {
            $errors['usu'] = "El nombre de usuario ya está registrado";
        }
    }

    // Validate Password
    if (empty($formData['clave'])) {
        $errors['clave'] = "La contraseña es obligatoria";
    } elseif (strlen($formData['clave']) < 5) {
        $errors['clave'] = "La contraseña debe tener al menos 5 caracteres";
    }

    // Validate Email
    if (empty($formData['email'])) {
        $errors['email'] = "El correo electrónico es obligatorio";
    } elseif (!filter_var($formData['email'], FILTER_VALIDATE_EMAIL)) {
        $errors['email'] = "Correo electrónico inválido";
    }

    // Validate File Upload
    if (isset($_FILES['foto']) && $_FILES['foto']['error'] == 0) {
        $allowedTypes = ['image/jpeg', 'image/png', 'image/gif'];
        $maxFileSize = 5 * 1024 * 1024; // 5MB

        if (!in_array($_FILES['foto']['type'], $allowedTypes)) {
            $errors['foto'] = "Tipo de archivo no permitido. Solo se aceptan JPEG, PNG y GIF.";
        } elseif ($_FILES['foto']['size'] > $maxFileSize) {
            $errors['foto'] = "El archivo es demasiado grande. Máximo 5MB.";
        }
    } else {
        $errors['foto'] = "La foto de perfil es obligatoria";
    }

    // If no errors, proceed with registration
    if (empty($errors)) {
        // Process file upload
        $foto = $_FILES['foto']['name'];
        $fecha_foto = new DateTime();
        $nombre_foto = $fecha_foto->getTimestamp() . "_" . $foto;
        $tmp_foto = $_FILES['foto']['tmp_name'];

        // Move uploaded file
        if (move_uploaded_file($tmp_foto, "../../views/fotos/" . $nombre_foto)) {
            // Prepare and execute insert statement
            $sentencia = $conexion->prepare("INSERT INTO tbl_usuarios (nombre, usuario, password, correo, foto) 
                VALUES (:nombre, :usu, :pass, :email, :foto)");

            $sentencia->bindParam(":nombre", $formData['nombre']);
            $sentencia->bindParam(":usu", $formData['usu']);
            $sentencia->bindParam(":pass", $formData['clave']);
            $sentencia->bindParam(":email", $formData['email']);
            $sentencia->bindParam(":foto", $nombre_foto);
            
            $sentencia->execute();
            header("location:index.php");
            exit();
        } else {
            $errors['foto'] = "Error al subir la foto";
        }
    }
}
include("../../templates/header.php");
?>

<div class="card">
    <div class="card-header">Registro de Usuario</div>
    <div class="card-body">
        <form id="registroForm" action="" method="post" enctype="multipart/form-data" novalidate>
            <div class="mb-3">
                <label for="nombre" class="form-label">Nombre:</label>
                <input type="text" class="form-control <?= isset($errors['nombre']) ? 'is-invalid' : '' ?>" 
                       name="nombre" id="nombre" 
                       value="<?= htmlspecialchars($formData['nombre']) ?>"
                       placeholder="Escriba nombre" 
                       required>
                <?php if (isset($errors['nombre'])): ?>
                    <div class="invalid-feedback"><?= $errors['nombre'] ?></div>
                <?php endif; ?>
            </div>

            <div class="mb-3">
                <label for="usu" class="form-label">Usuario:</label>
                <input type="text" class="form-control <?= isset($errors['usu']) ? 'is-invalid' : '' ?>" 
                       name="usu" id="usu" 
                       value="<?= htmlspecialchars($formData['usu']) ?>"
                       placeholder="Escriba el nombre de usuario" 
                       required>
                <?php if (isset($errors['usu'])): ?>
                    <div class="invalid-feedback"><?= $errors['usu'] ?></div>
                <?php endif; ?>
            </div>

            <div class="mb-3">
                <label for="clave" class="form-label">Contraseña:</label>
                <input type="password" class="form-control <?= isset($errors['clave']) ? 'is-invalid' : '' ?>" 
                       name="clave" id="clave" 
                       placeholder="Escriba la contraseña" 
                       required>
                <?php if (isset($errors['clave'])): ?>
                    <div class="invalid-feedback"><?= $errors['clave'] ?></div>
                <?php endif; ?>
            </div>

            <div class="mb-3">
                <label for="email" class="form-label">Correo:</label>
                <input type="email" class="form-control <?= isset($errors['email']) ? 'is-invalid' : '' ?>" 
                       name="email" id="email" 
                       value="<?= htmlspecialchars($formData['email']) ?>"
                       placeholder="Escriba el Email" 
                       required>
                <?php if (isset($errors['email'])): ?>
                    <div class="invalid-feedback"><?= $errors['email'] ?></div>
                <?php endif; ?>
            </div>

            <div class="mb-3">
                <label for="foto" class="form-label">Foto:</label>
                <input type="file" class="form-control <?= isset($errors['foto']) ? 'is-invalid' : '' ?>" 
                       name="foto" id="foto" 
                       required>
                <?php if (isset($errors['foto'])): ?>
                    <div class="invalid-feedback"><?= $errors['foto'] ?></div>
                <?php endif; ?>
            </div>

            <button type="submit" class="btn btn-success">Enviar registro</button>
            <a name="" id="" class="btn btn-primary" href="index.php" role="button">Cancelar</a>
        </form>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('registroForm');
    const fields = {
        nombre: document.getElementById('nombre'),
        usu: document.getElementById('usu'),
        clave: document.getElementById('clave'),
        email: document.getElementById('email'),
        foto: document.getElementById('foto')
    };

    const validations = {
        nombre: function(value) {
            const regex = /^[A-Za-zÀ-ÿñÑ ]+$/u;
            return value.trim() !== '' && regex.test(value);
        },
        usu: function(value) {
            return value.trim().length >= 4;
        },
        clave: function(value) {
            return value.length >= 5;
        },
        email: function(value) {
            const regex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            return value.trim() !== '' && regex.test(value);
        },
        foto: function() {
            return fields.foto.files.length > 0;
        }
    };

    function validateField(field) {
        const value = field.value;
        const isValid = validations[field.id](value);
        
        if (isValid) {
            field.classList.remove('is-invalid');
            field.classList.add('is-valid');
            return true;
        } else {
            field.classList.remove('is-valid');
            field.classList.add('is-invalid');
            return false;
        }
    }

    // Real-time validation
    Object.values(fields).forEach(field => {
        field.addEventListener('input', function() {
            validateField(this);
        });
    });

    // Form submission validation
    form.addEventListener('submit', function(event) {
        let isFormValid = true;

        Object.values(fields).forEach(field => {
            if (!validateField(field)) {
                isFormValid = false;
            }
        });

        if (!isFormValid) {
            event.preventDefault();
        }
    });
});
</script>

<?php include("../../templates/footer.php"); ?>