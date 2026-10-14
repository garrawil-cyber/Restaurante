<?php
include("../../bd.php");

$errors = [];
$formData = [
    'nombre' => '',
    'usu' => '',
    'email' => '',
    'foto_actual' => '',
    'foto_nueva' => ''
];

// Obtener datos del usuario para editar
if (isset($_GET['txtID'])) {
    $id = $_GET['txtID'];
    $sentencia = $conexion->prepare("SELECT * FROM tbl_usuarios WHERE ID = :id");
    $sentencia->bindParam(":id", $id);
    $sentencia->execute();
    $registro = $sentencia->fetch(PDO::FETCH_ASSOC);

    // Llenar datos iniciales
    $formData['nombre'] = $registro['nombre'];
    $formData['usu'] = $registro['usuario'];
    $formData['email'] = $registro['correo'];
    $formData['foto_actual'] = $registro['foto'];
}

if ($_POST) {
    // Sanitize and validate input
    $id = $_POST['id'];
    $formData['nombre'] = isset($_POST['nombre']) ? trim($_POST['nombre']) : '';
    $formData['usu'] = isset($_POST['usu']) ? trim($_POST['usu']) : '';
    $formData['email'] = isset($_POST['email']) ? trim($_POST['email']) : '';
    $password_nuevo = isset($_POST['clave']) ? $_POST['clave'] : '';

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
    }

    // Validate Email
    if (empty($formData['email'])) {
        $errors['email'] = "El correo electrónico es obligatorio";
    } elseif (!filter_var($formData['email'], FILTER_VALIDATE_EMAIL)) {
        $errors['email'] = "Correo electrónico inválido";
    }

    // Validate Password (opcional)
    if (!empty($password_nuevo)) {
        if (strlen($password_nuevo) < 5) {
        $errors['clave'] = "La contraseña debe tener al menos 5 caracteres";
    }
}

    // Manejar la foto
    $nombre_foto = $formData['foto_actual'];
    if (isset($_FILES['foto']) && $_FILES['foto']['error'] == 0) {
        $allowedTypes = ['image/jpeg', 'image/png', 'image/gif'];
        $maxFileSize = 5 * 1024 * 1024; // 5MB

        if (!in_array($_FILES['foto']['type'], $allowedTypes)) {
            $errors['foto'] = "Tipo de archivo no permitido. Solo se aceptan JPEG, PNG y GIF.";
        } elseif ($_FILES['foto']['size'] > $maxFileSize) {
            $errors['foto'] = "El archivo es demasiado grande. Máximo 5MB.";
        } else {
            // Eliminar foto anterior
            $ruta_foto_anterior = "../../views/fotos/" . $formData['foto_actual'];
            if (file_exists($ruta_foto_anterior)) {
                unlink($ruta_foto_anterior);
            }

            // Subir nueva foto
            $foto = $_FILES['foto']['name'];
            $fecha_foto = new DateTime();
            $nombre_foto = $fecha_foto->getTimestamp() . "_" . $foto;
            move_uploaded_file($_FILES['foto']['tmp_name'], "../../views/fotos/" . $nombre_foto);
        }
    }

// Si no hay errores, actualizar
if (empty($errors)) {
    // Modificar la consulta SQL para incluir password
    $sentencia = $conexion->prepare("UPDATE tbl_usuarios 
        SET nombre = :nombre, 
            correo = :email, 
            foto = :foto" . 
            (!empty($password_nuevo) ? ", password = :password" : "") . 
        " WHERE ID = :id");

    $sentencia->bindParam(":nombre", $formData['nombre']);
    $sentencia->bindParam(":email", $formData['email']);
    $sentencia->bindParam(":foto", $nombre_foto);
    $sentencia->bindParam(":id", $id);
    
    // Agregar binding de contraseña si se proporciona
    if (!empty($password_nuevo)) {
        $sentencia->bindParam(":password", $password_nuevo);
    }
    
    $sentencia->execute();
    header("location:index.php");
    exit();
}

}
include("../../templates/header.php");
?>

<div class="card">
    <div class="card-header">Editar Usuario</div>
    <div class="card-body">
        <form id="editarForm" action="" method="post" enctype="multipart/form-data" novalidate>
            <input type="hidden" name="id" value="<?= $id ?>">
            
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
                       placeholder="Nombre de usuario" 
                       readonly>
                <?php if (isset($errors['usu'])): ?>
                    <div class="invalid-feedback"><?= $errors['usu'] ?></div>
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
                       name="foto" id="foto">
                <?php if (isset($errors['foto'])): ?>
                    <div class="invalid-feedback"><?= $errors['foto'] ?></div>
                <?php endif; ?>
                
                <?php if (!empty($formData['foto_actual'])): ?>
                    <div class="mt-2">
                        <img src="../../views/fotos/<?= $formData['foto_actual'] ?>" 
                             alt="Foto actual" 
                             style="max-width: 200px; max-height: 200px;">
                    </div>
                <?php endif; ?>
            </div>
            <div class="mb-3">
                 <label for="clave" class="form-label">Contraseña (opcional):</label>
                 <input type="password" class="form-control <?= isset($errors['clave']) ? 'is-invalid' : '' ?>" 
                    name="clave" id="clave" 
                    placeholder="Deje en blanco si no desea cambiar" 
                    minlength="5">
                    <?php if (isset($errors['clave'])): ?>
                    <div class="invalid-feedback"><?= $errors['clave'] ?></div>
                    <?php endif; ?>
            </div>

            <button type="submit" class="btn btn-success">Actualizar</button>
            <a name="" id="" class="btn btn-primary" href="index.php" role="button">Cancelar</a>
        </form>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('editarForm');
    const fields = {
        nombre: document.getElementById('nombre'),
        email: document.getElementById('email')
    };

    const validations = {
        nombre: function(value) {
            const regex = /^[A-Za-zÀ-ÿñÑ ]+$/u;
            return value.trim() !== '' && regex.test(value);
        },
        email: function(value) {
            const regex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            return value.trim() !== '' && regex.test(value);
        },

        clave: function(value) {
         // Si está vacío, es válido
        if (value.trim() === '') return true;
        // De lo contrario, debe tener al menos 5 caracteres
        return value.length >= 5;
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