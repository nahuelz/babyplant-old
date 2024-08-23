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
  $fechai = (string)date('y-m-d', strtotime('first day of -3 month'));
}
if (strlen($fechaf) == 0){
  $fechaf = "NOW()";
}


$filtros = json_decode($_POST['filtros'], true); 

$cadena = "SELECT p.id_artpedido, p.id_pedido, v.id_articulo as id_variedad, t.id_articulo as id_tipo, t.nombre as nombre_tipo, DATE_FORMAT(o.fecha, '%d/%m/%Y') as fecha_orden, o.fecha as fecha_ordensiembra, 
s.nombre as nombre_subtipo, v.nombre as nombre_variedad, p.cant_plantas, p.cant_semi,
 p.cant_band, p.bandeja, p.fecha_entrega_original, p.fecha_siembraestimada, 
 c.nombre as cliente, c.id_cliente, p.estado, o.id_orden_alternativa, p.revision, p.solucion, UPPER(p.cod_sobre) as cod_sobre, GROUP_CONCAT(DISTINCT(om.id_mesada) SEPARATOR ', ') as id_mesada 
FROM variedades_producto v 
INNER JOIN subtipos_producto s ON v.id_subtipo = s.id_articulo 
INNER JOIN tipos_producto t ON t.id_articulo = s.id_tipo
INNER JOIN articulospedidos p ON p.id_articulo = v.id_articulo 
INNER JOIN pedidos pe ON p.id_pedido = pe.id_pedido 
INNER JOIN clientes c ON pe.id_cliente = c.id_cliente 
INNER JOIN ordenes_siembra o ON o.id_artpedido = p.id_artpedido
LEFT JOIN ordenes_mesadas om ON o.id_orden = om.id_orden 
GROUP BY o.id_orden 
HAVING DATE(fecha_ordensiembra) >= '$fechai' AND ";
if ($fechaf == "NOW()"){
  $cadena.="DATE(fecha_ordensiembra) <= NOW() ";  
}else{
  $cadena.=" DATE(fecha_ordensiembra) <= '$fechaf' ";  
}

if ($filtros["tipo"] != NULL){
  $cadena.=" AND id_tipo IN ".$filtros["tipo"]." ";
}

if ($filtros["variedad"] != NULL){
  $cadena.=" AND nombre_variedad REGEXP '".$filtros["variedad"]."' ";
}

if ($filtros["estado"] != NULL){
  $cadena.=" AND estado IN ".$filtros["estado"]." ";
}
else{
  $cadena.=" AND estado >= 2 ";
}

if ($filtros["revision"] != NULL && $filtros["solucion"] == NULL){
  
  if ($filtros["revision"] == -1){
    $cadena.=" AND revision > 0 ";
  }
  else if ($filtros["revision"] > 0){
    $cadena.=" AND revision = ".(string)$filtros["revision"]." ";
  }

  $cadena.=" AND solucion IS NULL ";
}

else if ($filtros["solucion"] != NULL){
  if ($filtros["solucion"] == -1){
    $cadena.=" AND solucion > 0 ";
  }
  else if ($filtros["solucion"] > 0){
    $cadena.=" AND solucion = ".(string)$filtros["solucion"]." ";
  }
}

$cadena.=" ORDER BY o.id_orden DESC;";

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
 echo "<th>Ord<br>Siembra</th><th>Fecha<br>Siembra</th><th>Producto</th><th>Cod. Sobre</th><th>Cliente</th><th>Cantidad</th><th>Estado</th><th>Mesada</th>";
 echo "</tr>";
 echo "</thead>";
 echo "<tbody>";
 $array = array();

 $tipos_revision = ["", "FALLA GERMINACIÓN", "GOLPE", "PAJARO", "RATA", "REALIZAR DESPUNTE", "USO PARA INJERTO", "D1 REALIZADO", "VER OBSERV."];
 $tipos_solucion = ["", "D1 CANCELADO", "CLASIFICACIÓN", "REPIQUE", "RESIEMBRA", "DEJAR FALLAS 12"];
 while($ww=mysqli_fetch_array($val)){
     $id_cliente=$ww['id_cliente'];
     $id_artpedido=$ww['id_artpedido'];
     $producto = $ww['nombre_subtipo']." ".$ww['nombre_variedad']." x".$ww['bandeja'];
     $tipo = "";
     $id_orden = $ww['id_orden_alternativa'];
     if ($id_orden != NULL)
      $tipo = strtoupper(substr($ww["nombre_tipo"], 0,3));

     if ($ww["revision"] != NULL && $ww["solucion"] == NULL){
      $producto.=" [".$tipos_revision[$ww["revision"]]."]";
     }
     else if ($ww["revision"] != NULL && $ww["solucion"] != NULL){
      $producto.=" [".$tipos_revision[$ww["revision"]]."] [".$tipos_solucion[$ww["solucion"]]."]";
     }

     $cliente = $ww['cliente'];
     $cant_band = $ww['cant_band'];
     $fecha_siembra = $ww['fecha_orden'];
     $estado = generarBoxEstado($ww["estado"], true);
     

    $fondo = "";
    if ($ww["revision"] != NULL && $ww["solucion"] == NULL)
      $fondo = "background-color:#F7D358;"; 
    
    if($ww["solucion"] != NULL)
      $fondo = "background-color:#A9F5A9;";   
  
  $fecha_siembra2 = explode("/",$fecha_siembra);

   $fecha_siembra2 = $fecha_siembra2[2]."/".$fecha_siembra2[1]."/".$fecha_siembra2[0];


   echo "<tr>";

    

    echo "<td style='text-align: center; font-size:1.0em; font-weight:bold;$fondo'>
   <span style='font-size:1.2em;'>$id_orden</span><br><span style='color: blue;font-size:1.2em'>$tipo</span>
   </td>";   

    
    echo "<td style='text-align: center;$fondo'><span style='display:none'>".str_replace("/","",$fecha_siembra2)."</span>".str_replace("/20", "/", $fecha_siembra)."</td>";
    echo "<td style='$fondo'>$producto</td>";
    echo "<td style='font-weight:bold;$fondo'>".$ww['cod_sobre']."</td>";
    echo "<td style='$fondo'>$cliente</td>";
    echo "<td style='text-align: center;font-weight:bold;font-size:1.2em;$fondo'>$cant_band</td>";
    echo "<td style='$fondo'><div style='cursor:pointer' onClick='MostrarModalEstado($id_artpedido)'>$estado</div></td>";
    echo "<td style='text-align: center; font-size:1.0em; $fondo'>".$ww['id_mesada']."</td>";


   echo "</tr>";

   
 }

 echo "</tbody>";

 echo "</table>";

 echo "</div>";

 echo "</div>";





}else{

  echo "<div class='callout callout-danger'><b>No se encontraron pedidos en las fechas indicadas...</b></div>";

}

?>