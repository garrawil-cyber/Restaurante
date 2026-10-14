<?php
include("../../bd.php");
if (isset($_GET['txtID'])) {
   
    $txtID=(isset($_GET['txtID']))?$_GET['txtID']:"";

    $sentencia=$conexion->prepare("select * from tbl_comentarios where ID=:id");
    $sentencia->bindParam(":id",$txtID);
    $sentencia->execute();

    
    $registro=$sentencia->fetch(PDO::FETCH_LAZY);
    $nombre=$registro['nombre'];
    $correo=$registro['correo'];
    $mensaje=$registro['mensaje'];
}

    if ($_POST) {
        $nombre=(isset($_POST['nombre']))?$_POST['nombre']:"";
        $correo=(isset($_POST['correo']))?$_POST['correo']:"";
        $mensaje=(isset($_POST['mensaje']))?$_POST['mensaje']:"";
        $txtID=(isset($_POST['txtID']))?$_POST['txtID']:"";

        $sentencia=$conexion->prepare("update tbl_comentarios set nombre=:nomb, correo=:email, mensaje=:mensaj where ID=:id");

        $sentencia->bindParam(":nomb",$nombre);
        $sentencia->bindParam(":email",$correo);
        $sentencia->bindParam(":mensaj",$mensaje);
        $sentencia->bindParam(":id",$txtID);

        $sentencia->execute();
        header("location:index.php");
    }

   
include("../../templates/header.php");?>


<div class="card">
    <div class="card-header">CONTACTOS</div>
        <div class="card-body">
            <form action="" id="" method="post">
                <div class="mb-3">
                    <label for="id" class="form-label" hidden>ID:</label>
                    <input type="text" class="form-control" value="<?php echo $txtID; ?>" name="txtID" id="txtID" aria-describedby="helpId" hidden>
                </div>
                <div class="mb-3">
                    <label for="mensaje" class="form-label">Mensaje:</label>
                   <!-- <input type="text" value="<?php echo $nombre; ?>" class="form-control" name="opinion" id="opinione" aria-describedby="helpId" placeholder="Escriba su opinion">-->
                   <textarea class="form-control" name="mensaje" id="mensaje" aria-describedby="helpId" placeholder="Escriba su mensaje"><?php echo $mensaje; ?></textarea>
                </div>
                <div class="mb-3">
                    <label for="nombre" class="form-label">Nombre:</label>
                    <input type="text" value="<?php echo $nombre; ?>" class="form-control" name="nombre" id="nombre" aria-describedby="helpId" placeholder="Escriba su nombre">
                </div>
                <div class="mb-3">
                    <label for="correo" class="form-label">Correo:</label>
                    <input type="text" value="<?php echo $correo; ?>" class="form-control" name="correo" id="correo" aria-describedby="helpId" placeholder="Escriba su Email">
                </div>
                <button type="submit" name="" id="" class="btn btn-success">Modificar opinion</button>
                <a name="" id="" class="btn btn-primary" href="index.php" role="button">Cancelar</a>

            </form>
        </div>

    <div class="card-footer text-muted">
    </div>
</div>


