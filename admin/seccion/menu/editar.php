<?php
include("../../bd.php");

// Obtener las categorías para llenar la lista
$sentencia_categorias=$conexion->prepare("SELECT * FROM tbl_categorias");
$sentencia_categorias->execute();
$lista_categoria = $sentencia_categorias->fetchAll(PDO::FETCH_ASSOC);

if (isset($_GET['txtID'])) {
    $txtID=(isset($_GET['txtID']))?$_GET['txtID']:"";

    $sentencia=$conexion->prepare("SELECT m.ID, m.nombre, m.id_categoria, c.nomb_categoria, m.foto, m.ingredientes, m.precio
    FROM tbl_menu m 
    INNER JOIN tbl_categorias c 
    ON m.id_categoria = c.ID 
    WHERE m.ID=:id");

    $sentencia->bindParam(":id",$txtID);
    $sentencia->execute();
    $registro=$sentencia->fetch(PDO::FETCH_LAZY);

    // Recuperación de datos para el formulario
    $titulo=$registro['nombre'];
    $ingrediente=$registro['ingredientes'];
    $categoria_actual=$registro['id_categoria'];
    $precio=$registro['precio'];
    $foto_plato=$registro['foto'];
    $ruta="img/".$foto_plato;
}

if ($_POST) {
    $txtID=(isset($_POST['txtID']))?$_POST['txtID']:"";
    $titulo=(isset($_POST['titulo']))?$_POST['titulo']:"";
    $ingrediente=(isset($_POST['ingrediente']))?$_POST['ingrediente']:"";
    $categoria=(isset($_POST['categoria']))?$_POST['categoria']:"";
    $precio=(isset($_POST['precio']))?$_POST['precio']:"";
    $foto=(isset($_FILES['foto']['name']))?$_FILES['foto']['name']:"";

    // Manejo de la foto
   // $nombre_foto=$foto;
    
    if ($foto!="") {
        $fecha_foto=new DateTime();
        $nombre_foto=$fecha_foto->getTimestamp()."_".$foto;
        $tmp_foto=$_FILES['foto']['tmp_name'];
        move_uploaded_file($tmp_foto,"img/".$nombre_foto);

        // Borrar imagen anterior
        $sentencia=$conexion->prepare("SELECT foto FROM tbl_menu WHERE ID=:id");
        $sentencia->bindParam(":id",$txtID);
        $sentencia->execute();
        $registro_foto=$sentencia->fetch(PDO::FETCH_LAZY);
        
        if (file_exists("img/".$registro_foto['foto'])) {
            unlink("img/".$registro_foto['foto']);
        }
    } else {
        // Si no se sube nueva foto, mantener la foto existente
        $nombre_foto=$foto_plato;
    }

    // Actualizar datos en la BD
    $sentencia=$conexion->prepare("UPDATE tbl_menu SET nombre=:titulo, ingredientes=:ingrediente, precio=:precio, foto=:foto, id_categoria=:categoria WHERE ID=:id");

    $sentencia->bindParam(":titulo",$titulo);
    $sentencia->bindParam(":ingrediente",$ingrediente);
    $sentencia->bindParam(":precio",$precio);
    $sentencia->bindParam(":foto",$nombre_foto);
    $sentencia->bindParam(":categoria",$categoria);
    $sentencia->bindParam(":id",$txtID);

    $sentencia->execute();

    header("location:index.php");
}

include("../../templates/header.php");?>

<div class="card">
    <div class="card-header">Editar Carta Menú</div>
        <div class="card-body">
            <form action="" method="post" enctype="multipart/form-data">
                <div class="mb-3">
                    <input type="hidden" value="<?php echo $txtID; ?>" name="txtID" id="txtID">
                </div>
                <div class="mb-3">
                    <label for="titulo" class="form-label">Nombre:</label>
                    <input type="text" class="form-control" value="<?php echo $titulo; ?>" name="titulo" id="titulo" required>
                </div>
                <div class="mb-3">
                    <label for="categoria" class="form-label">Categoría:</label>
                    <select class="form-control" name="categoria" id="categoria">
                        <?php foreach ($lista_categoria as $cat) { ?>
                            <option value="<?php echo $cat['ID']; ?>" 
                                <?php echo ($cat['ID'] == $categoria_actual) ? 'selected' : ''; ?>>
                                <?php echo $cat['nomb_categoria']; ?>
                            </option>
                        <?php } ?>
                    </select>
                </div>
                <div class="mb-3">
                    <label for="foto" class="form-label">Foto:</label><br>
                    <img src="<?php echo $ruta; ?>" class="img-fluid" width="100" height="100">
                    <input type="file" class="form-control" name="foto" id="foto">
                </div>
                <div class="mb-3">
                    <label for="ingrediente" class="form-label">Ingredientes:</label>
                    <input type="text" class="form-control" value="<?php echo $ingrediente; ?>" name="ingrediente" id="ingrediente">
                </div>
                <div class="mb-3">
                    <label for="precio" class="form-label">Precio:</label>
                    <input type="text" class="form-control" value="<?php echo $precio; ?>" name="precio" id="precio" required>
                </div>
                <button type="submit" class="btn btn-success">Editar Plato</button>
                <a class="btn btn-primary" href="index.php">Cancelar</a>
            </form>
        </div>
</div>

<?php include("../../templates/footer.php");?>