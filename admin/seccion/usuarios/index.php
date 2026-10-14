<?php
include("../../bd.php");

// Manejo de eliminación de usuario
if (isset($_GET['txtID'])) {
    $txtID = $_GET['txtID'];
    
    try {
        $conexion->beginTransaction();

        // Obtener datos del usuario para eliminar foto
        $sentencia = $conexion->prepare("SELECT foto FROM tbl_usuarios WHERE ID = :id");
        $sentencia->bindParam(":id", $txtID);
        $sentencia->execute();
        $usuario = $sentencia->fetch(PDO::FETCH_ASSOC);

        // Eliminar foto si existe
        if (!empty($usuario['foto'])) {
            $ruta_foto = "../../views/fotos/" . $usuario['foto'];
            if (file_exists($ruta_foto)) {
                unlink($ruta_foto);
            }
        }
        
        // Eliminar usuario
        $sentencia = $conexion->prepare("DELETE FROM tbl_usuarios WHERE ID = :id");
        $sentencia->bindParam(":id", $txtID);
        $sentencia->execute();

        $conexion->commit();
        header("Location: index.php?mensaje=Usuario eliminado correctamente");
        exit();
    } catch (Exception $e) {
        $conexion->rollBack();
        header("Location: index.php?error=Error al eliminar el usuario");
        exit();
    }
}

// Obtener lista de usuarios
$sentencia = $conexion->prepare("SELECT * FROM tbl_usuarios");
$sentencia->execute();
$lista_usuario = $sentencia->fetchAll(PDO::FETCH_ASSOC);

include("../../templates/header.php");
?>

<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h3 class="mb-0">Gestión de Usuarios</h3>
        <a href="crear.php" class="btn btn-success">
            <i class="fas fa-plus"></i> Agregar Usuario
        </a>
    </div>
    
    <?php if(isset($_GET['mensaje'])): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <?= htmlspecialchars($_GET['mensaje']) ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <?php if(isset($_GET['error'])): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <?= htmlspecialchars($_GET['error']) ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-striped" id="testimoniosTable">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Foto</th>
                        <th>Nombre</th>
                        <th>Usuario</th>
                        <th>Correo</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($lista_usuario as $usuario): ?>
                        <tr>
                            <td><?= $usuario['ID'] ?></td>
                            <td>
                                <img src="../../views/fotos/<?= !empty($usuario['foto']) ? $usuario['foto'] : 'default.png' ?>" 
                                     class="img-thumbnail rounded-circle" 
                                     style="width: 50px; height: 50px; object-fit: cover;">
                            </td>
                            <td><?= $usuario['nombre'] ?></td>
                            <td><?= $usuario['usuario'] ?></td>
                            <td><?= $usuario['correo'] ?></td>
                            <td>
                                <div class="btn-group" role="group">
                                    <a href="editar.php?txtID=<?= $usuario['ID'] ?>" 
                                       class="btn btn-warning btn-sm" 
                                       title="Editar">
                                        <i class="far fa-edit"></i>
                                    </a>
                                    <button class="btn btn-danger btn-sm delete-user" 
                                            data-id="<?= $usuario['ID'] ?>" 
                                            data-nombre="<?= htmlspecialchars($usuario['nombre']) ?>"
                                            data-bs-toggle="modal" 
                                            data-bs-target="#deleteModal"
                                            title="Eliminar">
                                        <i class="fas fa-trash-alt"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal de Confirmación -->
<div class="modal fade" id="deleteModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title">Confirmar Eliminación</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                ¿Está seguro de eliminar al usuario <strong id="usuarioEliminar"></strong>? Esta acción no se puede deshacer.
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <a href="#" id="confirmDelete" class="btn btn-danger">Eliminar</a>
            </div>
        </div>
    </div>
</div>

<?php include("../../templates/footer.php"); ?>

<script>
$(document).ready(function() {
    // Configurar modal de eliminación
    $('.delete-user').on('click', function() {
        const userId = $(this).data('id');
        const userName = $(this).data('nombre');
        
        $('#usuarioEliminar').text(userName);
        $('#confirmDelete').attr('href', `index.php?txtID=${userId}`);
    });

});
</script>