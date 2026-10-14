<?php
include("../../bd.php");

if (isset($_GET['txtID'])) {
    $txtID = isset($_GET['txtID']) ? $_GET['txtID'] : "";

    //proceso de borrado de imagen
    $sentencia=$conexion->prepare("select * from tbl_menu where ID=:id");
    $sentencia->bindParam(":id",$txtID);
    $sentencia->execute();

    $registro_foto=$sentencia->fetch(PDO::FETCH_LAZY);
    if (isset($registro_foto['foto'])) {
     if (file_exists("img/".$registro_foto['foto'])) {
            unlink("img/".$registro_foto['foto']);
        }
    }
    //borra n la base de datos
    $sentencia = $conexion->prepare("DELETE FROM tbl_menu WHERE ID = :id");
    $sentencia->bindParam(":id", $txtID);
    $sentencia->execute();
    echo json_encode(["status" => "success"]);
    header("Location: index.php");
    exit();
}


$sentencia=$conexion->prepare("SELECT m.ID, m.nombre, c.nomb_categoria, m.foto, m.ingredientes, m.precio  
FROM tbl_menu m INNER JOIN tbl_categorias c
ON m.id_categoria = c.ID");
$sentencia->execute();
$lista_menu= $sentencia->fetchAll(PDO::FETCH_ASSOC);

include("../../templates/header.php");

?>

<br>
<div class="card">
<div class="card-header d-flex justify-content-between align-items-center">
        <h3 class="mb-0">Gestión Carta men&uacute;</h3>
        <a href="crear.php" class="btn btn-success">
            <i class="fas fa-plus"></i> Agregar registro
        </a>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-striped" id="testimoniosTable">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Foto</th>
                        <th>Menu/th>
                        <th>Categoria</th>
                        <th>Ingredientes</th>
                        <th>Precio</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                      <?php foreach ($lista_menu as $key => $value) { ?>
                        <tr class="">
                            <td><?php echo $value['ID']; ?></td>
                            <td>
                                <img src="img/<?php echo $value['foto']; ?>"  class="img-thumbnail rounded-circle" 
                                style="width: 50px; height: 50px; object-fit: cover;">
                            </td>
                            <td valign="top"><?php echo $value['nombre']; ?></td>
                            <td valign="top"><?php echo $value['nomb_categoria']; ?></td>
                            <td valign="top"><?php echo $value['ingredientes']; ?></td>
                            <td valign="top"><?php echo $value['precio']; ?></td>
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


<?php include("../../templates/footer.php");?>

<!-- Modal de Confirmación -->
<div class="modal fade" id="deleteModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title">Confirmar Eliminación</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                ¿Está seguro de eliminar este registro <strong id="usuarioEliminar"></strong>? Esta acción no se puede deshacer.
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
