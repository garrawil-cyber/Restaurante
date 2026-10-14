<?php
include("../../bd.php");
if (isset($_GET['txtID'])) {
   
    $txtID=(isset($_GET['txtID']))?$_GET['txtID']:"";

    $sentencia=$conexion->prepare("select * from tbl_testimonios where ID=:id");
    $sentencia->bindParam(":id",$txtID);
    $sentencia->execute();

    
    $registro=$sentencia->fetch(PDO::FETCH_LAZY);
    $opinion=$registro['opinion'];
    $nombre=$registro['nombre'];
}

    if ($_POST) {
        $opinion=(isset($_POST['opinion']))?$_POST['opinion']:"";
        $nombre=(isset($_POST['nombre']))?$_POST['nombre']:"";
        $txtID=(isset($_POST['txtID']))?$_POST['txtID']:"";

        $sentencia=$conexion->prepare("update tbl_testimonios set opinion=:opinion, nombre=:nombre where ID=:id");

        $sentencia->bindParam(":opinion",$opinion);
        $sentencia->bindParam(":nombre",$nombre);
        $sentencia->bindParam(":id",$txtID);

        $sentencia->execute();
        header("location:index.php");
    }

   
include("../../templates/header.php");?>


<div class="card">
    <div class="card-header">TESTIMONIOS</div>
        <div class="card-body">
            <form action="" id="" method="post">
                <div class="mb-3">
                    <label for="id" class="form-label" hidden>ID:</label>
                    <input type="text" class="form-control" value="<?php echo $txtID; ?>" name="txtID" id="txtID" aria-describedby="helpId" hidden>
                </div>
                <div class="mb-3">
                    <label for="opinion" class="form-label">Opinion:</label>
                   <!-- <input type="text" value="<?php echo $opinion; ?>" class="form-control" name="opinion" id="opinione" aria-describedby="helpId" placeholder="Escriba su opinion">-->
                   <textarea class="form-control" name="opinion" id="opinion" aria-describedby="helpId" placeholder="Escriba su opinon" required><?php echo $opinion; ?></textarea>
                </div>
                <div class="mb-3">
                    <label for="nombre" class="form-label">Nombre:</label>
                    <input type="text" value="<?php echo $nombre; ?>" class="form-control" name="nombre" id="nombre" aria-describedby="helpId" placeholder="Escriba su nombre" required>
                </div>
                <button type="submit" name="" id="" class="btn btn-success">Modificar opinion</button>
                <a name="" id="" class="btn btn-primary" href="index.php" role="button">Cancelar</a>

            </form>
        </div>

    <div class="card-footer text-muted">
    </div>
</div>


