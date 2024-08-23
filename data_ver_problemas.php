<?php
include("class_lib/sesionSecurity.php");
require('class_lib/class_conecta_mysql.php');
require('class_lib/funciones.php');

$con = mysqli_connect($host, $user, $password,$dbname);
if (!$con) {
  die("Connection failed: " . mysqli_connect_error());
}
mysqli_query($con,"SET NAMES 'utf8'");

$cadena = "SELECT p.id_artpedido, v.id_articulo as id_variedad, t.id_articulo as id_tipo, t.nombre as nombre_tipo, DATE_FORMAT(o.fecha, '%d/%m/%Y') as fecha_orden, o.fecha as fecha_ordensiembra, 
s.nombre as nombre_subtipo, v.nombre as nombre_variedad, p.cant_plantas, p.cant_semi,
 p.cant_band, p.bandeja, p.fecha_entrega_original, p.fecha_siembraestimada, 
 c.nombre as cliente, c.id_cliente, p.estado, o.id_orden_alternativa, p.revision, p.solucion, GROUP_CONCAT(DISTINCT(om.id_mesada) SEPARATOR ', ') as id_mesada, p.problema, p.observacionproblema 
FROM variedades_producto v 
INNER JOIN subtipos_producto s ON v.id_subtipo = s.id_articulo 
INNER JOIN tipos_producto t ON t.id_articulo = s.id_tipo
INNER JOIN articulospedidos p ON p.id_articulo = v.id_articulo 
INNER JOIN pedidos pe ON p.id_pedido = pe.id_pedido 
INNER JOIN clientes c ON pe.id_cliente = c.id_cliente 
INNER JOIN ordenes_siembra o ON o.id_artpedido = p.id_artpedido
LEFT JOIN ordenes_mesadas om ON o.id_orden = om.id_orden 
GROUP BY o.id_orden 
HAVING p.problema = 1 ORDER BY o.id_orden DESC;";

$val = mysqli_query($con, $cadena);
if (mysqli_num_rows($val)>0){
 echo "<div class='box box-primary'>";
 echo "<div class='box-header with-border'>";
 echo "</div>";
 echo "<div class='box-body'>";
 echo "<table id='tabla' class='table table-responsive w-100 d-block d-md-table'>";
 echo "<thead>";
 echo "<tr>";
 echo "<th>Ord<br>Siembra</th><th>Fecha<br>Siembra</th><th>Producto</th><th>Cliente</th><th>Cantidad</th><th>Estado</th><th>Mesada</th>";
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
    
    if (strlen(trim($ww["observacionproblema"])) > 1){
      echo "<td style='$fondo'><span style='font-weight:bold'>$producto</span><br><span>Problema: ".trim($ww["observacionproblema"])."</span></td>";
    }
    else{
      echo "<td style='$fondo'><span style='font-weight:bold'>$producto</span></td>"; 
    }
    
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