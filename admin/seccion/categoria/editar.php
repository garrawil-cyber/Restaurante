<?php
include("../../bd.php");
if (isset($_GET['txtID'])) {
   
    $txtID=(isset($_GET['txtID']))?$_GET['txtID']:"";

    $sentencia=$conexion->prepare("select * from tbl_categorias where ID=:id");
    $sentencia->bindParam(":id",$txtID);
    $sentencia->execute();

    $registro=$sentencia->fetch(PDO::FETCH_LAZY);
    $nombre=$registro['nomb_categoria'];
}

    if ($_POST) {
        $titulo=(isset($_POST['nombre']))?$_POST['nombre']:"";
        $txtID=(isset($_POST['txtID']))?$_POST['txtID']:"";

        $sentencia=$conexion->prepare("update tbl_categorias set nomb_categoria=:nombre where ID=:id");

        $sentencia->bindParam(":nombre",$titulo);
        $sentencia->bindParam(":id",$txtID);

        $sentencia->execute();
        header("location:index.php");
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
                    <label for="titulo" class="form-label">Nombre categoria:</label>
                    <input type="text" value="<?php echo $nombre; ?>" class="form-control" name="nombre" id="nombre" aria-describedby="helpId" placeholder="Escriba el nombre de la Categoria" required>
                </div>
                <button type="submit" name="" id="" class="btn btn-success">Modificar categoria</button>
                <a name="" id="" class="btn btn-primary" href="index.php" role="button">Cancelar</a>

            </form>
        </div>

    <div class="card-footer text-muted">
    </div>
</div>


<?php include("../../templates/footer.php");?>