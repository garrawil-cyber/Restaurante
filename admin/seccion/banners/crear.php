<?php
include("../../bd.php");
if ($_POST) {
    $titulo=(isset($_POST['titulo']))?$_POST['titulo']:"";
    $descripcion=(isset($_POST['descripcion']))?$_POST['descripcion']:"";
    $link=(isset($_POST['link']))?$_POST['link']:"";

    $sentencia=$conexion->prepare("INSERT INTO tbl_banners (titulo, descripcion, link) VALUES (:titulo, :descripcion, :link)");

    $sentencia->bindParam(":titulo",$titulo);
    $sentencia->bindParam(":descripcion",$descripcion);
    $sentencia->bindParam(":link",$link);

    $sentencia->execute();
    header("location:index.php");
}


include("../../templates/header.php");?>



<div class="card">
    <div class="card-header">Banners</div>
        <div class="card-body">
            <form action="" id="" method="post">
                <div class="mb-3">
                    <label for="titulo" class="form-label">Titulo:</label>
                    <input type="text" class="form-control" name="titulo" id="titulo" aria-describedby="helpId" placeholder="Escriba el Titulo del Banner" required>
                </div>
                <div class="mb-3">
                    <label for="descripcion" class="form-label">Descripci&oacute;n:</label>
                    <input type="text" class="form-control" name="descripcion" id="descripcion" aria-describedby="helpId" placeholder="Escriba el Titulo del Banner" required>
                </div>
                <div class="mb-3">
                    <label for="link" class="form-label">Link:</label>
                    <input type="text" class="form-control" name="link" id="link" aria-describedby="helpId" placeholder="Escriba el Link" required>
                </div>
                <button type="submit" name="" id="" class="btn btn-success">Crear banner</button>
                <a name="" id="" class="btn btn-primary" href="index.php" role="button">Cancelar</a>

            </form>
        </div>

    <div class="card-footer text-muted">
    </div>
</div>


<?php include("../../templates/footer.php");?>