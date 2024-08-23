<?php

include("class_lib/sesionSecurity.php");
require('class_lib/class_conecta_mysql.php');

$id_tipo = $_POST['id_tipo'];

$con = mysqli_connect($host, $user, $password,$dbname);
// Check connection
if (!$con) {
  die("Connection failed: " . mysqli_connect_error());
}

mysqli_query($con,"SET NAMES 'utf8'");
$cadena="select a.id_articulo as id_articulo, a.bandeja as bandeja, a.precio as precio from articulos a WHERE a.id_variedad = $id_tipo order by a.bandeja;";
$val = mysqli_query($con, $cadena);

 if (mysqli_num_rows($val)>0){

    while($re=mysqli_fetch_array($val)){
    	echo "<option value=$re[id_articulo]>$re[bandeja]</option>";
    }
  }
?>
