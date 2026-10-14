<?php
include("../../bd.php");
if ($_POST) {
    $nombre=(isset($_POST['nombre']))?$_POST['nombre']:"";
    $sentencia=$conexion->prepare("INSERT INTO tbl_categorias (nomb_categoria) VALUES (:categoria)");

    $sentencia->bindParam(":categoria",$nombre);
    $sentencia->execute();
    header("location:index.php");
}


include("../../templates/header.php");?>



<div class="card">
    <div class="card-header">Categorias</div>
        <div class="card-body">
            <form action="" id="" method="post">
                <div class="mb-3">
                    <label for="titulo" class="form-label">Nombre categoria:</label>
                    <input type="text" class="form-control" name="nombre" id="nombre" aria-describedby="helpId" placeholder="Escriba el Nombre de la Categoria" required>
                </div>
                <button type="submit" name="" id="" class="btn btn-success">Crear Categoria</button>
                <a name="" id="" class="btn btn-primary" href="index.php" role="button">Cancelar</a>

            </form>
        </div>

    <div class="card-footer text-muted">
    </div>
</div>


<?php include("../../templates/footer.php");?>