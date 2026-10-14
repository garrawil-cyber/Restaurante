<?php
include("../../bd.php");
if ($_POST) {
    $titulo=(isset($_POST['titulo']))?$_POST['titulo']:"";
    $descripcion=(isset($_POST['descripcion']))?$_POST['descripcion']:"";
    $facebook=(isset($_POST['facebook']))?$_POST['facebook']:"";
    $instagram=(isset($_POST['instagram']))?$_POST['instagram']:"";
    $linkedin=(isset($_POST['linkedin']))?$_POST['linkedin']:"";
    
    $sentencia=$conexion->prepare("INSERT INTO tbl_colaboradores (titulo, descripcion, linkfacebook,linkinstagram,linklinkedin,foto) 
    VALUES (:titulo, :descripcion, :facebook, :instagram, :linkedin, :foto)");

    $foto=(isset($_FILES['foto']['name']))?$_FILES['foto']['name']:"";
    $fecha_foto=new DateTime();
    $nombre_foto=$fecha_foto->getTimestamp()."_".$foto;
    $tmp_foto=$_FILES['foto']['tmp_name'];

    if ($tmp_foto!="") {
        move_uploaded_file($tmp_foto,"../../../images/colaboradores/".$nombre_foto);
    }

    $sentencia->bindParam(":foto",$nombre_foto);
    $sentencia->bindParam(":titulo",$titulo);
    $sentencia->bindParam(":descripcion",$descripcion);
    $sentencia->bindParam(":facebook",$facebook);
    $sentencia->bindParam(":instagram",$instagram);
    $sentencia->bindParam(":linkedin",$linkedin);
    
    $sentencia->execute();
    header("location:index.php");
}


include("../../templates/header.php");?>



<div class="card">
    <div class="card-header">COLABORADORES</div>
        <div class="card-body">
            <form action="" id="" method="post" enctype="multipart/form-data">
                <div class="mb-3">
                    <label for="titulo" class="form-label">Nombre:</label>
                    <input type="text" class="form-control" name="titulo" id="titulo" aria-describedby="helpId" placeholder="Escriba el Titulo del Colaborador" required>
                </div>
                <div class="mb-3">
                    <label for="foto" class="form-label">Foto:</label>
                    <input type="file" class="form-control" name="foto" id="foto" aria-describedby="helpId" placeholder="">
                </div>
                <div class="mb-3">
                    <label for="descripcion" class="form-label">Descripci&oacute;n:</label>
                    <input type="text" class="form-control" name="descripcion" id="descripcion" aria-describedby="helpId" placeholder="Escriba el Titulo del Colaborador" required>
                </div>
                <div class="mb-3">
                    <label for="facebook" class="form-label">Facebook:</label>
                    <input type="text" class="form-control" name="facebook" id="facebook" aria-describedby="helpId" placeholder="Escriba el Link del Facebook">
                </div>
                <div class="mb-3">
                    <label for="instagram" class="form-label">Instagram:</label>
                    <input type="text" class="form-control" name="instagram" id="instagram" aria-describedby="helpId" placeholder="Escriba el Link del Instagram">
                </div>
                <div class="mb-3">
                    <label for="linkedin" class="form-label">Linkedin:</label>
                    <input type="text" class="form-control" name="linkedin" id="linkedin" aria-describedby="helpId" placeholder="Escriba el Link del Linkedin">
                </div>
                <button type="submit" name="" id="" class="btn btn-success">Crear colaborador</button>
                <a name="" id="" class="btn btn-primary" href="index.php" role="button">Cancelar</a>

            </form>
        </div>

    <div class="card-footer text-muted">
    </div>
</div>


<?php include("../../templates/footer.php");?>