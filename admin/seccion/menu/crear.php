<?php
include("../../bd.php");

// Obtener las categorías antes de la lógica de envío del formulario
$sentencia=$conexion->prepare("SELECT * FROM tbl_categorias");
$sentencia->execute();
$lista_categoria = $sentencia->fetchAll(PDO::FETCH_ASSOC);

if ($_POST) {
    $titulo=(isset($_POST['titulo']))?$_POST['titulo']:"";
    $ingrediente=(isset($_POST['ingrediente']))?$_POST['ingrediente']:"";
    $precio=(isset($_POST['precio']))?$_POST['precio']:"";
    $id_categoria=(isset($_POST['categoria']))?$_POST['categoria']:"";
    
    $foto=(isset($_FILES['foto']['name']))?$_FILES['foto']['name']:"";
    $fecha_foto=new DateTime();
    $nombre_foto=$fecha_foto->getTimestamp()."_".$foto;
    $tmp_foto=$_FILES['foto']['tmp_name'];

    if ($tmp_foto!="") {
        move_uploaded_file($tmp_foto,"img/".$nombre_foto);
    }

    $sentencia=$conexion->prepare("INSERT INTO tbl_menu(nombre, ingredientes, foto, precio, id_categoria) 
    VALUES (:nombre, :ingredientes, :foto, :precio, :categoria)");
    $sentencia->bindParam(":nombre",$titulo);
    $sentencia->bindParam(":ingredientes",$ingrediente);
    $sentencia->bindParam(":foto",$nombre_foto);
    $sentencia->bindParam(":precio",$precio);
    $sentencia->bindParam(":categoria",$id_categoria);
    $sentencia->execute();

    header("location:index.php");
}

include("../../templates/header.php");?>

<div class="card">
    <div class="card-header">Gestión Carta Menú</div>
        <div class="card-body">
            <form action="" id="" method="post" enctype="multipart/form-data">
                <div class="mb-3">
                    <label for="titulo" class="form-label">Nombre:</label>
                    <input type="text" class="form-control" name="titulo" id="titulo" aria-describedby="helpId" placeholder="Escriba el nombre del menú" required>
                </div>
                <div class="mb-3">
                    <label for="categoria" class="form-label" style="font-weight: bold;">Categoría:</label>
                    <select class="form-control" name="categoria" id="categoria">
                        <?php foreach ($lista_categoria as $categoria) { ?>
                            <option value="<?php echo $categoria['ID']; ?>"><?php echo $categoria['nomb_categoria']; ?></option>
                        <?php } ?>
                    </select>
                </div>
                <div class="mb-3">
                    <label for="foto" class="form-label">Foto:</label>
                    <input type="file" class="form-control" name="foto" id="foto" aria-describedby="helpId" placeholder="" required>
                </div>
                <div class="mb-3">
                    <label for="ingrediente" class="form-label">Ingredientes:</label>
                    <input type="text" class="form-control" name="ingrediente" id="ingrediente" aria-describedby="helpId" placeholder="Escriba los ingredientes del menú">
                </div>
                <div class="mb-3">
                    <label for="precio" class="form-label">Precio:</label>
                    <input type="text" class="form-control" name="precio" id="precio" aria-describedby="helpId" placeholder="Escriba el precio del menú" required>
                </div>
                <button type="submit" name="" id="" class="btn btn-success">Crear Plato</button>
                <a name="" id="" class="btn btn-primary" href="index.php" role="button">Cancelar</a>
            </form>
        </div>

    <div class="card-footer text-muted">
    </div>
</div>

<?php include("../../templates/footer.php");?>