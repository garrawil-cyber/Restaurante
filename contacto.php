<?php
include("admin/bd.php"); // Incluye tu archivo de conexión a la base de datos

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nomb = isset($_POST['nombre']) ? $_POST['nombre'] : "";
    $mail = isset($_POST['email']) ? $_POST['email'] : "";
    $msj = isset($_POST['mensaje']) ? $_POST['mensaje'] : "";
    $dni = isset($_POST['dni']) ? $_POST['dni'] : "";
    $telefono = isset($_POST['telefono']) ? $_POST['telefono'] : "";

    if (!empty($nomb) && !empty($mail) && !empty($msj)) {
        $sentencia = $conexion->prepare("INSERT INTO tbl_comentarios (nombre, correo, mensaje, dni, telefono) 
        VALUES (:nomb, :mail, :msj, :dni, :telefono)");
        $sentencia->bindParam(":nomb", $nomb);
        $sentencia->bindParam(":mail", $mail);
        $sentencia->bindParam(":msj", $msj);
        $sentencia->bindParam(":dni", $dni);
        $sentencia->bindParam(":telefono", $telefono);

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

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Formulario de Contacto</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.1/css/all.min.css">
    <style>
        body {
            background-color: #f4f4f4;
        }
        .container {
            max-width: 600px;
            margin: 50px auto;
            background-color: #fff;
            padding: 20px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            border-radius: 8px;
        }
        h2 {
            text-align: center;
            color: #333;
        }
        .form-group {
            margin-bottom: 15px;
        }
        .form-group label {
            display: block;
            margin-bottom: 5px;
            color: #333;
        }
        .form-group input, .form-group textarea {
            width: 100%;
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 4px;
        }
        .form-group input:focus, .form-group textarea:focus {
            border-color: #007BFF;
        }
        .form-group .error {
            color: red;
            font-size: 14px;
        }
        .btn {
            display: inline-block;
            padding: 10px 20px;
            background-color: #007BFF;
            color: #fff;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }
        .btn:hover {
            background-color: #0056b3;
        }
    </style>
</head>
<body>
    <div class="container mt-5">
        <h2 class="text-center mb-4">Contactos</h2>
        <form id="contactForm" method="post">
            <div class="mb-3">
                <label for="nombre" class="form-label">Nombre:</label>
                <input type="text" class="form-control" id="nombre" name="nombre"  maxlength="50"  required>
                <div class="invalid-feedback">Por favor, ingrese solo letras.</div>
            </div>
            <div class="mb-3">
                <label for="dni" class="form-label">DNI:</label>
                <input type="text" class="form-control" id="dni" name="dni"  maxlength="8" required>
                <div class="invalid-feedback">Por favor, ingrese solo números.</div>
            </div>
            <div class="mb-3">
                <label for="telefono" class="form-label">Teléfono:</label>
                <input type="text" class="form-control" id="telefono"  maxlength="12" name="telefono">
                <div class="invalid-feedback">Por favor, ingrese solo números.</div>
            </div>
            <div class="mb-3">
                <label for="email" class="form-label">Correo Electrónico:</label>
                <input type="email" class="form-control" id="email" name="email" required>
                <div class="invalid-feedback">Por favor, ingrese un correo electrónico válido.</div>
            </div>
            <div class="mb-3">
                <label for="mensaje" class="form-label">Mensaje:</label>
                <textarea class="form-control" id="mensaje" name="mensaje" rows="3" required></textarea>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" id="cancelButton">Cancelar</button>&nbsp;
                <button type="submit" class="btn btn-primary">Enviar datos</button>
            </div>

        </form>

        <div class="modal fade" id="thankYouModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel">¡Gracias por tu mensaje!</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body text-center" id="modal-body-content">
                        <i class="fa-solid fa-spinner fa-spin fa-3x"></i> Enviando...
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        // Función para validar el campo de nombre
        function validarNombre() {
            const nombre = document.getElementById('nombre');
            const nombreRegex = /^[A-Za-zÀ-ÿ\u00f1\u00d1 ]+$/; // Permite letras y espacios
            if (!nombreRegex.test(nombre.value)) {
                nombre.classList.add('is-invalid');
                return false;
            } else {
                nombre.classList.remove('is-invalid');
                nombre.classList.add('is-valid');
                return true;
            }
        }

        // Función para validar el campo de DNI
        function validarDNI() {
            const dni = document.getElementById('dni');
            const dniRegex = /^[0-9]+$/; // Permite solo números
            if (!dniRegex.test(dni.value)) {
                dni.classList.add('is-invalid');
                return false;
            } else {
                dni.classList.remove('is-invalid');
                dni.classList.add('is-valid');
                return true;
            }
        }

        // Función para validar el campo de teléfono
        function validarTelefono() {
            const telefono = document.getElementById('telefono');
            const telefonoRegex = /^[0-9]+$/; // Permite solo números
            if (!telefonoRegex.test(telefono.value)) {
                telefono.classList.add('is-invalid');
                return false;
            } else {
                telefono.classList.remove('is-invalid');
                telefono.classList.add('is-valid');
                return true;
            }
        }

        // Función para validar el campo de email
        function validarEmail() {
            const email = document.getElementById('email');
            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/; // Expresión regular para validar el correo electrónico
            if (!emailRegex.test(email.value)) {
                email.classList.add('is-invalid');
                return false;
            } else {
                email.classList.remove('is-invalid');
                email.classList.add('is-valid');
                return true;
            }
        }

        // Validar el formulario antes de enviar
        $('#contactForm').on('submit', function(event) {
            const nombreValido = validarNombre();
            const dniValido = validarDNI();
            const telefonoValido = validarTelefono();
            const emailValido = validarEmail();
            if (!nombreValido || !dniValido || !telefonoValido || !emailValido) {
                event.preventDefault(); // Evitar el envío del formulario si alguna validación falla
            } else {
                event.preventDefault(); // Evitar el envío del formulario

                // Mostrar el modal con el icono de carga
                $('#thankYouModal').modal('show');

                $.ajax({
                    url: '', // La ruta al archivo PHP actual
                    type: 'POST',
                    data: $(this).serialize(),
                    success: function(response) {
                        response = JSON.parse(response);
                        setTimeout(function() { // Esperar un breve período antes de mostrar el mensaje final
                            if (response.success) {
                                // Mensaje de éxito
                                $('#modal-body-content').html('<i class="fa-solid fa-circle-check fa-3x text-success"></i><br>Tu mensaje ha sido enviado correctamente.');
                            } else {
                                // Mensaje de error
                                $('#modal-body-content').html('<i class="fa-solid fa-circle-xmark fa-3x text-danger"></i><br>Ha ocurrido un error al enviar el mensaje.');
                            }
                        }, 500); // Cambiar el contenido después de 500 milisegundos
                    },
                    error: function() {
                        // Mensaje de error
                        setTimeout(function() { // Esperar un breve período antes de mostrar el mensaje de error
                            $('#modal-body-content').html('<i class="fa-solid fa-circle-xmark fa-3x text-danger"></i><br>Ha ocurrido un error al enviar el mensaje.');
                        }, 500); // Cambiar el contenido después de 500 milisegundos
                    }
                });
            }
        });

        // Validar los campos en tiempo real
        $('#nombre').on('input', validarNombre);
        $('#dni').on('input', validarDNI);
        $('#telefono').on('input', validarTelefono);
        $('#email').on('input', validarEmail);

    // Script para regresar a la página anterior al hacer clic en "Cancelar"
    document.getElementById('cancelButton').addEventListener('click', function() {
    window.history.back();
    });

    </script>
</body>
</html>
