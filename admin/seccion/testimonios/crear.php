<?php
include("../../bd.php");
if ($_POST) {
    $opinion=(isset($_POST['opinion']))?$_POST['opinion']:"";
    $nombre=(isset($_POST['nombre']))?$_POST['nombre']:"";

    $sentencia=$conexion->prepare("INSERT INTO tbl_testimonios (opinion, nombre) VALUES (:opinion, :nombre)");

    $sentencia->bindParam(":opinion",$opinion);
    $sentencia->bindParam(":nombre",$nombre);

    $sentencia->execute();
    header("location:index.php");
}


include("../../templates/header.php");?>



<div class="card">
    <div class="card-header">TESTIMONIOS</div>
        <div class="card-body">
            <form action="" id="" method="post">
                <div class="mb-3">
                    <label for="opinion" class="form-label">Opinion:</label>
                    <input type="text" class="form-control" name="opinion" id="opinion" aria-describedby="helpId" placeholder="Escriba su opinion" required>
                </div>
                <div class="mb-3">
                    <label for="nombre" class="form-label">Nombre:</label>
                    <input type="text" class="form-control" name="nombre" id="nombre" aria-describedby="helpId" placeholder="Escriba su Nombre" required>
                </div>
                <button type="submit" name="" id="" class="btn btn-success">Crear opinion</button>
                <a name="" id="" class="btn btn-primary" href="index.php" role="button">Cancelar</a>

            </form>
        </div>

    <div class="card-footer text-muted">
    </div>
</div>


<?php include("../../templates/footer.php");?>