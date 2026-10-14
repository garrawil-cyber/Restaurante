<?php
$servidor="localhost";
$basedatos="restaurante";
$usuario="root";
$contrasenia="";

try{
    $conexion=new PDO("mysql:host=$servidor;dbname=$basedatos",$usuario,$contrasenia);
    //echo "Conexion establecida...";

}catch(Exception $error){
    echo $error->getMessage();

}

?>