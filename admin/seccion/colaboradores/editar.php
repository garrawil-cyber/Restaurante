<?php
include("../../bd.php");
if (isset($_GET['txtID'])) {
   
    $txtID=(isset($_GET['txtID']))?$_GET['txtID']:"";

    $sentencia=$conexion->prepare("select * from tbl_colaboradores where ID=:id");
    $sentencia->bindParam(":id",$txtID);
    $sentencia->execute();

    $registro=$sentencia->fetch(PDO::FETCH_LAZY);
    $titulo=$registro['titulo'];
    $descripcion=$registro['descripcion'];
    $facebook=$registro['linkfacebook'];
    $instagram=$registro['linkinstagram'];
    $linkedin=$registro['linklinkedin'];
    $foto=$registro['foto'];
    $ruta="../../../images/colaboradores/".$foto;
}

if ($_POST) {
    $titulo=(isset($_POST['titulo']))?$_POST['titulo']:"";
    $descripcion=(isset($_POST['descripcion']))?$_POST['descripcion']:"";
    $facebook=(isset($_POST['facebook']))?$_POST['facebook']:"";
    $instagram=(isset($_POST['instagram']))?$_POST['instagram']:"";
    $linkedin=(isset($_POST['linkedin']))?$_POST['linkedin']:"";
    $foto=(isset($_POST['foto']))?$_POST['foto']:"";

    //la foto
    $foto=(isset($_FILES['foto']['name']))?$_FILES['foto']['name']:"";
    $fecha_foto=new DateTime();
    $nombre_foto=$fecha_foto->getTimestamp()."_".$foto;
    $tmp_foto=$_FILES['foto']['tmp_name'];

    if ($tmp_foto!="") {
        move_uploaded_file($tmp_foto,"../../../images/colaboradores/".$nombre_foto);
    }   
    //proceso de borrado de imagen
        $sentencia=$conexion->prepare("select * from tbl_colaboradores where ID=:id");
        $sentencia->bindParam(":id",$txtID);
        $sentencia->execute();

        $registro_foto=$sentencia->fetch(PDO::FETCH_LAZY);
        if (isset($registro_foto['foto'])) {
         if (file_exists("../../../images/colaboradores/".$registro_foto['foto'])) {
            unlink("../../../images/colaboradores/".$registro_foto['foto']);
            }
        }
        //***fin borrado de imagen*** */
        ////*****actualizando datos la BD******////



    $sentencia=$conexion->prepare("update tbl_colaboradores set titulo=:titulo, descripcion=:descripcion, linkfacebook=:facebook, 
                                   linkinstagram=:instagram,linklinkedin=:linkedin, foto=:foto where ID=:id");

    $sentencia->bindParam(":titulo",$titulo);
    $sentencia->bindParam(":descripcion",$descripcion);
    $sentencia->bindParam(":facebook",$facebook);
    $sentencia->bindParam(":instagram",$instagram);
    $sentencia->bindParam(":linkedin",$linkedin);
    $sentencia->bindParam(":foto",$nombre_foto);
    $sentencia->bindParam(":id",$txtID);

    $sentencia->execute();
    header("location:index.php");
}


include("../../templates/header.php");?>



<div class="card">
    <div class="card-header">COLABORADORES</div>
        <div class="card-body">
            <form action="" id="" method="post" enctype="multipart/form-data">
                <div class="mb-3">
                    <label for="id" class="form-label" hidden>ID:</label>
                    <input type="text" class="form-control" value="<?php echo $txtID; ?>" name="txtID" id="txtID" aria-describedby="helpId" hidden>
                </div>
                <div class="mb-3">
                    <label for="titulo" class="form-label">Nombre:</label>
                    <input type="text" class="form-control" value="<?php echo $titulo; ?>" name="titulo" id="titulo" aria-describedby="helpId" placeholder="Escriba el Titulo del Colaborador" required>
                </div>
                <div class="mb-3">
                <label for="foto" class="form-label">Foto:</label><br>
                    <img src="<?php echo $ruta; ?>" class="img-fluid" width="100" height="100">
                    <input type="file" class="form-control" name="foto" id="foto" aria-describedby="helpId" placeholder="">
                </div>
                <div class="mb-3">
                    <label for="descripcion" class="form-label">Descripci&oacute;n:</label>
                    <input type="text" class="form-control" value="<?php echo $descripcion; ?>" name="descripcion" id="descripcion" aria-describedby="helpId" placeholder="Escriba la descripcion del Colaborador" required>
                </div>
                <div class="mb-3">
                    <label for="facebook" class="form-label">Facebook:</label>
                    <input type="text" class="form-control" value="<?php echo $facebook; ?>" name="descripcion" id="descripcion" aria-describedby="helpId" placeholder="Escriba la descripcion del Colaborador">
                </div>
                <div class="mb-3">
                    <label for="instagram" class="form-label">Instagram:</label>
                    <input type="text" class="form-control" value="<?php echo $instagram; ?>" name="descripcion" id="descripcion" aria-describedby="helpId" placeholder="Escriba la descripcion del Colaborador">
                </div>
                <div class="mb-3">
                    <label for="linkedin" class="form-label">Linkedin:</label>
                    <input type="text" class="form-control" value="<?php echo $linkedin; ?>" name="descripcion" id="descripcion" aria-describedby="helpId" placeholder="Escriba la descripcion del Colaborador">
                </div>
               <button type="submit" name="" id="" class="btn btn-success">Editar colaborador</button>
                <a name="" id="" class="btn btn-primary" href="index.php" role="button">Cancelar</a>

            </form>
        </div>

    <div class="card-footer text-muted">
    </div>
</div>


<?php include("../../templates/footer.php");?>