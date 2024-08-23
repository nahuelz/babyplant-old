<?php
include("class_lib/sesionSecurity.php");
require('class_lib/class_conecta_mysql.php');
require('class_lib/funciones.php');
$con = mysqli_connect($host, $user, $password,$dbname);
// Check connection
if (!$con) {
  die("Connection failed: " . mysqli_connect_error());
}
mysqli_query($con,"SET NAMES 'utf8'");
$tipo = $_POST['tipo'];
$id_remito = $_POST["id_remito"];
if ($tipo == "cargar_remito"){
	$cadenaselect="SELECT codigo FROM remitos WHERE id_remito = $id_remito";
	$val = mysqli_query($con,$cadenaselect);
	$resultado = "";
	if (mysqli_num_rows($val)>0){
	    while($re=mysqli_fetch_array($val))
	    {
	     $resultado = $re["codigo"];
	    }
	    
	}
	echo $resultado;
}

?>

