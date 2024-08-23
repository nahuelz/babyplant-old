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
$cadena = "SELECT * FROM tipos_producto ORDER BY nombre;";
$val = mysqli_query($con, $cadena);

if (mysqli_num_rows($val)>0){
 echo "<div class='box box-primary'>";
 echo "<div class='box-header with-border'>";
 echo "</div>";
 echo "<div class='box-body'>";
 echo "<table id='tabla' class='table table-bordered table-responsive w-100 d-block d-md-table'>";
 echo "<thead>";
 echo "<tr>";
 echo "<th>Id</th><th>Nombre</th><th>Días en Cámara</th><th>Tipos de Bandeja</th>";
 echo "</tr>";
 echo "</thead>";
 echo "<tbody>";

 while($ww=mysqli_fetch_array($val)){
    $id_articulo = $ww['id_articulo'];
    $tipo = $ww['nombre'];
    $cant_dias = $ww['dias_en_camara'];
    $tipos = "";
    $precios = "";
    $precios_s = "";
    $bandejas = "";
    
    if (!is_null($ww["precio_288"])){
      if (is_null($ww["precio_288_s"]))
        $tipos.="288 (<span class='text-danger'>\$".$ww["precio_288"]."</span>)<br>";
      else
        $tipos.="288 (<span class='text-danger'>\$".$ww["precio_288"]."</span> - <span style='color:green;'>\$".$ww["precio_288_s"]."</span>)<br>";


      $precios.=($ww["precio_288"].",");
      $precios_s.=($ww["precio_288_s"].",");
      $bandejas.="288, ";
    }
    if (!is_null($ww["precio_200"])){
      if (is_null($ww["precio_200_s"]))
        $tipos.="200 (<span class='text-danger'>\$".$ww["precio_200"]."</span>)<br>";
      else
        $tipos.="200 (<span class='text-danger'>\$".$ww["precio_200"]."</span> - <span style='color:green;'>\$".$ww["precio_200_s"]."</span>)<br>";

      $precios.=($ww["precio_200"].",");
      $precios_s.=($ww["precio_200_s"].",");
      $bandejas.="200, ";
    }
    if (!is_null($ww["precio_162"])){
      if (is_null($ww["precio_162_s"]))
        $tipos.="162 (<span class='text-danger'>\$".$ww["precio_162"]."</span>)<br>";
      else
        $tipos.="162 (<span class='text-danger'>\$".$ww["precio_162"]."</span> - <span style='color:green;'>\$".$ww["precio_162_s"]."</span>)<br>";

      $precios.=($ww["precio_162"].",");
      $precios_s.=($ww["precio_162_s"].",");
      $bandejas.="162, ";
    }
    if (!is_null($ww["precio_128"])){
      if (is_null($ww["precio_128_s"]))
        $tipos.="128 (<span class='text-danger'>\$".$ww["precio_128"]."</span>)<br>";
      else
        $tipos.="128 (<span class='text-danger'>\$".$ww["precio_128"]."</span> - <span style='color:green;'>\$".$ww["precio_128_s"]."</span>)<br>";

      $precios.=($ww["precio_128"].",");
      $precios_s.=($ww["precio_128_s"].",");
      $bandejas.="128, ";
    }

    if (!is_null($ww["precio_72"])){
      if (is_null($ww["precio_72_s"]))
        $tipos.="72 (<span class='text-danger'>\$".$ww["precio_72"]."</span>)<br>";
      else
        $tipos.="72 (<span class='text-danger'>\$".$ww["precio_72"]."</span> - <span style='color:green;'>\$".$ww["precio_72_s"]."</span>)<br>";

      $precios.=($ww["precio_72"].",");
      $precios_s.=($ww["precio_72_s"].",");
      $bandejas.="72, ";
    }
    if (!is_null($ww["precio_50"])){
      if (is_null($ww["precio_50_s"]))
        $tipos.="50 (<span class='text-danger'>\$".$ww["precio_50"]."</span>)<br>";
      else
        $tipos.="50 (<span class='text-danger'>\$".$ww["precio_50"]."</span> - <span style='color:green;'>\$".$ww["precio_50_s"]."</span>)<br>";


      $precios.=($ww["precio_50"].",");
      $precios_s.=($ww["precio_50_s"].",");
      $bandejas.="50, ";
    }
    if (!is_null($ww["precio_25"])){
      if (is_null($ww["precio_25_s"]))
        $tipos.="25 (<span class='text-danger'>\$".$ww["precio_25"]."</span>)<br>";
      else
        $tipos.="25 (<span class='text-danger'>\$".$ww["precio_25"]."</span> - <span style='color:green;'>\$".$ww["precio_25_s"]."</span>)<br>";

      $precios.=($ww["precio_25"].",");
      $precios_s.=($ww["precio_25_s"].",");
      $bandejas.="25, ";
    }
    if (!is_null($ww["precio_49"])){
      if (is_null($ww["precio_49_s"]))
        $tipos.="49 (<span class='text-danger'>\$".$ww["precio_49"]."</span>)<br>";
      else
        $tipos.="49 (<span class='text-danger'>\$".$ww["precio_49"]."</span> - <span style='color:green;'>\$".$ww["precio_49_s"]."</span>)<br>";


      $precios.=($ww["precio_49"].",");
      $precios_s.=($ww["precio_49_s"].",");

      $bandejas.="49, ";
    }

    if (substr($tipos, -1) == ">"){
      $tipos = substr($tipos, 0, -4);
    }

    if (substr($precios, -1) == ","){
      $precios = substr($precios, 0, -1);
    }

    if (substr($bandejas, -1) == " "){
      $bandejas = substr($bandejas, 0, -2);
    }

   echo "<tr>";
   echo "<td style='text-align: center; width:40px; cursor:pointer; color:#1F618D;font-weight:bold;'>$id_articulo</td>";
   echo "<td style='text-align: center; cursor:pointer;font-size:1.4em;font-weight:bold;'>$tipo</td>";
   echo "<td style='text-align: center; cursor:pointer;font-size:1.4em;font-weight:bold;'>$cant_dias</td>";
   echo "<td style='text-align: center; cursor:pointer;font-size: 1.2em;' x-precios='$precios' x-precios-s='$precios_s' x-bandejas='$bandejas'>$tipos</td>";
   echo "</tr>";
 }
 echo "</tbody>";
 echo "</table>";
 echo "</div>";
 echo "</div>";
}else{
  echo "<div class='callout callout-danger'><b>No se encontraron productos en la base de datos...</b></div>";
}
?>