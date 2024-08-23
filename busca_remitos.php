<?php
include("class_lib/sesionSecurity.php");
require('class_lib/class_conecta_mysql.php');
require('class_lib/funciones.php');

$con = mysqli_connect($host, $user, $password,$dbname);
if (!$con) {
  die("Connection failed: " . mysqli_connect_error());
}
mysqli_query($con,"SET NAMES 'utf8'");
$fechai=$_POST['fechai'];
$fechaf=$_POST['fechaf'];
$fechai=str_replace("/", "-", $fechai);
$fechaf=str_replace("/", "-", $fechaf);

if (strlen($fechai) == 0){
  $fechai = (string)date('y-m-d', strtotime("first day of -3 month"));
}
if (strlen($fechaf) == 0){
  $fechaf = "NOW()";
}

$filtros = json_decode($_POST['filtros'], true); 
$cadena = "SELECT r.id_remito, DATE_FORMAT(r.fecha, '%d/%m/%Y %T') as fecha, r.id_cliente, r.codigo, c.nombre, r.fecha as fecha_raw, r.tipo FROM remitos r 
          INNER JOIN clientes c ON c.id_cliente = r.id_cliente HAVING fecha_raw >= '$fechai' AND r.tipo = 0 AND ";

if ($fechaf == "NOW()"){
  $cadena.="fecha_raw <= NOW() ";  
}else{
  $cadena.=" fecha_raw <= '$fechaf' ";  
}

if ($filtros["cliente"] != NULL){
  $cadena.=" AND c.nombre REGEXP '".$filtros["cliente"]."' ";
}

$cadena.=" ORDER BY r.id_remito DESC;";

$val = mysqli_query($con, $cadena);
if (mysqli_num_rows($val)>0){
 echo "<div class='box box-primary'>";
 echo "<div class='box-header with-border'>";
 echo "<h3 class='box-title'>Pedidos</h3>";
 echo "</div>";
 echo "<div class='box-body'>";
 echo "<table id='tabla' class='table table-responsive w-100 d-block d-md-table'>";
 echo "<thead>";
 echo "<tr>";
 echo "<th>Nº Remito</th>
 <th>Fecha</th>
 <th>Cliente</th>
 <th></th>
 </tr>
 </thead>
 <tbody>";
 
while($ww=mysqli_fetch_array($val)){
  $id_cliente=$ww['id_cliente'];
  $nombre_cliente = $ww["nombre"];
  $fecha_sort = explode("/",$ww["fecha"]);
  $fecha_sort = $fecha_sort[2].$fecha_sort[1].$fecha_sort[0];
  echo "<td style='text-align: center;font-weight:bold;font-size:1.3em' id='remito_$ww[id_remito]'>$ww[id_remito]</td>";
  echo "<td style='text-align: center'><span style='display:none'>".$fecha_sort."</span>$ww[fecha]</td>";
  echo "<td style='text-align: center;'>$nombre_cliente</td>";
  echo "<td style='text-align: center;'><button id='r_$ww[id_remito]' style='font-size:1.8em;' class='btn btn-primary btn-sm fa fa-print' onclick='printRemito(1,this.id)'></button></td>";
  echo "</tr>";
}

echo "</tbody>";
echo "</table>";
echo "</div>";
echo "</div>";

}else{
  echo "<div class='callout callout-danger'><b>No se encontraron remitos en las fechas indicadas...</b></div>";
}

?>