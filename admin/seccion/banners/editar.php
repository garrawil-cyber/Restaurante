<?php
include("../../bd.php");
if (isset($_GET['txtID'])) {
   
    $txtID=(isset($_GET['txtID']))?$_GET['txtID']:"";

    $sentencia=$conexion->prepare("select * from tbl_banners where ID=:id");
    $sentencia->bindParam(":id",$txtID);
    $sentencia->execute();

    $registro=$sentencia->fetch(PDO::FETCH_LAZY);
    $titulo=$registro['titulo'];
    $descripcion=$registro['descripcion'];
    $link=$registro['link'];
}

    if ($_POST) {
        $titulo=(isset($_POST['titulo']))?$_POST['titulo']:"";
        $descripcion=(isset($_POST['descripcion']))?$_POST['descripcion']:"";
        $link=(isset($_POST['link']))?$_POST['link']:"";
        $txtID=(isset($_POST['txtID']))?$_POST['txtID']:"";

        $sentencia=$conexion->prepare("update tbl_banners set titulo=:titulo, descripcion=:descripcion, link=:link where ID=:id");

        $sentencia->bindParam(":titulo",$titulo);
        $sentencia->bindParam(":descripcion",$descripcion);
        $sentencia->bindParam(":link",$link);
        $sentencia->bindParam(":id",$txtID);

        $sentencia->execute();
        header("location: index.php");
    }

   
include("../../templates/header.php");?>


<div class="card">
    <div class="card-header">Banners</div>
        <div class="card-body">
            <form action="" id="" method="post">
                <div class="mb-3">
                    <label for="id" class="form-label" hidden>ID:</label>
                    <input type="text" class="form-control" value="<?php echo $txtID; ?>" name="txtID" id="txtID" aria-describedby="helpId" hidden>
                </div>
                <div class="mb-3">
                    <label for="titulo" class="form-label">Titulo:</label>
                    <input type="text" value="<?php echo $titulo; ?>" class="form-control" name="titulo" id="titulo" aria-describedby="helpId" placeholder="Escriba el Titulo del Banner" required>
                </div>
                <div class="mb-3">
                    <label for="descripcion" class="form-label">Descripci&oacute;n:</label>
                    <input type="text" value="<?php echo $descripcion; ?>" class="form-control" name="descripcion" id="descripcion" aria-describedby="helpId" placeholder="Escriba la descripcion del Banner" required>
                </div>
                <div class="mb-3">
                    <label for="link" class="form-label">Link:</label>
                    <input type="text" value="<?php echo $link; ?>" class="form-control" name="link" id="link" aria-describedby="helpId" placeholder="Escriba el Link" required>
                </div>
                <button type="submit" name="" id="" class="btn btn-success">Modificar banner</button>
                <a name="" id="" class="btn btn-primary" href="index.php" role="button">Cancelar</a>

            </form>
        </div>

    <div class="card-footer text-muted">
    </div>
</div>


<?php include("../../templates/footer.php");?>