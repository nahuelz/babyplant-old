<?php

include("class_lib/sesionSecurity.php");
require('class_lib/class_conecta_mysql.php');

$con = mysqli_connect($host, $user, $password,$dbname);
// Check connection
if (!$con) {
  die("Connection failed: " . mysqli_connect_error());
}

mysqli_query($con,"SET NAMES 'utf8'");
$cadena="select id_cliente, nombre, telefono from clientes order by nombre";
$val = mysqli_query($con, $cadena);
 if (mysqli_num_rows($val)>0){
    while($re=mysqli_fetch_array($val)){
     	echo "<option x-telefono='$re[telefono]' value='$re[id_cliente]'>$re[nombre] ($re[id_cliente])</option>";
    }
 }
?>
