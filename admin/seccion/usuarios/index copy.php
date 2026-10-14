<?php
include("../../bd.php");

if (isset($_GET['txtID'])) {

    $txtID = isset($_GET['txtID']) ? $_GET['txtID'] : "";
    //proceso de borrado de imagen
    $sentencia=$conexion->prepare("select * from tbl_usuarios where ID=:id");
    $sentencia->bindParam(":id",$txtID);
    $sentencia->execute();

    $registro_foto=$sentencia->fetch(PDO::FETCH_LAZY);
    if (isset($registro_foto['foto'])) {
     if (file_exists("../../views/fotos/".$registro_foto['foto'])) {
            unlink("../../views/fotos/".$registro_foto['foto']);
        }
    }
    
    //borrado de usuario
    $sentencia = $conexion->prepare("DELETE FROM tbl_usuarios WHERE ID = :id");
    $sentencia->bindParam(":id", $txtID);
    $sentencia->execute();

    //echo "<script type='text/javascript'>window.location.href = 'index.php';</script>";
    header("Location: index.php");
    exit();
}
$sentencia = $conexion->prepare("SELECT * from tbl_usuarios");
$sentencia->execute();
$lista_usuario = $sentencia->fetchAll(PDO::FETCH_ASSOC);

include("../../templates/header.php");

?>
<div class="card">
    <div class="card-header">
       <a name="" id="" class="btn btn-primary" href="crear.php" role="button">Agregar registros</a>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-striped" id="testimoniosTable">
                <thead>
                    <tr>
                            <th>ID</th>
                            <th>Nombre</th>
                            <th>Usuario</th>
                            <th>Password</th>
                            <th>Correo</th>
                            <th>Foto</th>
                            <th>Acciones</th>
                    </tr>
                </thead>
                    <tbody>
                        <?php foreach ($lista_usuario as $value) { ?>
                            <tr class="">
                                <td scope="row"><?php echo $value['ID']; ?></td>
                                <td><?php echo $value['nombre']; ?></td>
                                <td><?php echo $value['usuario']; ?></td>
                                <td><?php echo $value['password']; ?></td>
                                <td><?php echo $value['correo']; ?></td>
                                <td>
                                <?php
                                    $profile_picture = "../../views/fotos/" . $value['foto'];
                                    $default_picture = "../../views/fotos/default.png"; 
                                ?> 
                                <img src="<?php echo isset($profile_picture) ? $profile_picture : $default_picture; ?>" width="50" alt="" class="img-thumbnail" srcset=""></td>
                                <td>
                                    <a name="" id="" href="editar.php?txtID=<?php echo $value['ID']; ?>" class="btn btn-warning btn-sm"><i class="far fa-edit"></i></a>
                                    <button class="btn btn-danger btn-sm" data-bs-toggle="modal" data-bs-target="#deleteModal" data-id="<?php echo $value['ID']; ?>"><i class="fas fa-trash-alt"></i></button>
                                </td>
                            </tr>
                        <?php } ?>
                </tbody>
            </table>
        </div>
    </div>
    <div class="card-footer text-muted"></div>
</div>

<?php include("../../templates/footer.php"); ?>

     <!-- Modal de Confirmación -->
    <div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="deleteModalLabel">Confirmar Eliminación</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    ¿Estás seguro de que deseas eliminar este registro?
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <a href="#" id="confirmDelete" class="btn btn-danger">Eliminar</a>
                </div>
            </div>
        </div>
    </div>
    
<!-- Incluir jQuery y Bootstrap JavaScript -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

<script>
    var deleteId;

    $('#deleteModal').on('show.bs.modal', function (event) {
        var button = $(event.relatedTarget);
        deleteId = button.data('id');
        // Ajustar el enlace de eliminación en el modal
        $('#confirmDelete').attr('href', 'index.php?txtID=' + deleteId);
    });
</script>
