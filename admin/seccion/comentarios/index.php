<?php
include("../../bd.php"); // Incluye tu archivo de conexión a la base de datos

// Eliminar registro si se proporciona txtID
if (isset($_GET['txtID'])) {
    $txtID = isset($_GET['txtID']) ? $_GET['txtID'] : "";
    $sentencia = $conexion->prepare("DELETE FROM tbl_comentarios WHERE ID = :id");
    $sentencia->bindParam(":id", $txtID);
    $sentencia->execute();
    header("Location: index.php");
    exit();
}

// Configuración de paginación
/*$registrosPorPagina = 10; // Cambia este valor para mostrar más o menos registros por página
$paginaActual = isset($_GET['pagina']) ? (int)$_GET['pagina'] : 1;
$offset = ($paginaActual - 1) * $registrosPorPagina;

// Obtener el total de registros
$sentenciaTotal = $conexion->prepare("SELECT COUNT(*) as total FROM tbl_comentarios");
$sentenciaTotal->execute();
$totalRegistros = $sentenciaTotal->fetch(PDO::FETCH_ASSOC)['total'];
$totalPaginas = ceil($totalRegistros / $registrosPorPagina);

// Obtener los registros de la página actual
$sentencia = $conexion->prepare("SELECT * FROM tbl_comentarios LIMIT :offset, :registrosPorPagina");
$sentencia->bindParam(':offset', $offset, PDO::PARAM_INT);
$sentencia->bindParam(':registrosPorPagina', $registrosPorPagina, PDO::PARAM_INT);
$sentencia->execute();
$lista_comentarios = $sentencia->fetchAll(PDO::FETCH_ASSOC);*/

$sentencia = $conexion->prepare("SELECT * FROM tbl_comentarios");
$sentencia->execute();
$lista_comentarios = $sentencia->fetchAll(PDO::FETCH_ASSOC);

include("../../templates/header.php");
?>

<br>
<div class="card">
    
    <div class="card-header d-flex justify-content-between align-items-center">
        <h3 class="mb-0">Gesti&oacute;n de Comentarios</h3>
    </div>
  
    <div class="card-body">
        <div class="table">
            <table  class="table table-striped" id="testimoniosTable">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Nombre</th>
                        <th>Email</th>
                        <th>Mensaje</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                      <?php foreach ($lista_comentarios as $key => $value) { ?>
                        <tr class="">
                            <td><?php echo $value['ID']; ?></td>
                            <td><?php echo $value['nombre']; ?></td>
                            <td><?php echo $value['correo']; ?></td>
                            <td><?php echo $value['mensaje']; ?></td>
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

    <div class="card-footer text-muted">
        <nav aria-label="Page navigation">
            <ul class="pagination justify-content-center">
                <?php /* if ($paginaActual > 1): ?>
                    <li class="page-item">
                        <a class="page-link" href="?pagina=<?php echo $paginaActual - 1; ?>" aria-label="Previous">
                            <span aria-hidden="true">&laquo;</span>
                        </a>
                    </li>
                <?php endif; ?>

                <?php for ($i = 1; $i <= $totalPaginas; $i++): ?>
                    <li class="page-item <?php echo $i == $paginaActual ? 'active' : ''; ?>">
                        <a class="page-link" href="?pagina=<?php echo $i; ?>"><?php echo $i; ?></a>
                    </li>
                <?php endfor; ?>

                <?php if ($paginaActual < $totalPaginas): ?>
                    <li class="page-item">
                        <a class="page-link" href="?pagina=<?php echo $paginaActual + 1; ?>" aria-label="Next">
                            <span aria-hidden="true">&raquo;</span>
                        </a>
                    </li>
                <?php endif; */?>
            </ul>
        </nav>
    </div>
</div>

<?php include("../../templates/footer.php"); ?>

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
