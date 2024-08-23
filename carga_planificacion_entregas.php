<?php
include("class_lib/sesionSecurity.php");
require('class_lib/class_conecta_mysql.php');
require('class_lib/funciones.php');

$con = mysqli_connect($host, $user, $password,$dbname);
if (!$con) {
  die("Connection failed: " . mysqli_connect_error());
}

$str = json_decode($_POST['fechas'], true); 
$lista = implode( ", ", $str );
$rowLength = count($str);
$query = "";
$arraycolumnas = [];
array_push($arraycolumnas, []);
array_push($arraycolumnas, []);
array_push($arraycolumnas, []);
array_push($arraycolumnas, []);
array_push($arraycolumnas, []);
array_push($arraycolumnas, []);
$fila = [];

$cadenaselect="SELECT t.nombre as nombre_tipo, s.nombre as nombre_subtipo, v.nombre as nombre_variedad, 
a.fecha_entrega, a.bandeja, a.id_artpedido, a.estado, a.fila, a.cant_band, c.nombre as nombrecliente, 
c.id_cliente, o.id_orden_alternativa, a.revision, a.solucion 
FROM articulospedidos a 
INNER JOIN variedades_producto v ON v.id_articulo = a.id_articulo 
INNER JOIN subtipos_producto s ON s.id_articulo = v.id_subtipo 
INNER JOIN tipos_producto t ON t.id_articulo = s.id_tipo 
INNER JOIN pedidos p ON p.id_pedido = a.id_pedido
INNER JOIN clientes c ON c.ID_CLIENTE = p.id_cliente
LEFT JOIN ordenes_siembra o ON o.id_artpedido = a.id_artpedido
WHERE a.fecha_entrega IN ($lista) AND a.estado IN (4,5,6) 
ORDER BY nombre_tipo, nombre_subtipo, nombre_variedad";

$val = mysqli_query($con,$cadenaselect);
if (mysqli_num_rows($val)>0){
    while($re=mysqli_fetch_array($val))
    {
      for ($i = 0;$i < count($str);$i++){
        if ("'".$re['fecha_entrega']."'" == $str[$i]){
          array_push($arraycolumnas[$i], [$re['nombre_tipo']." ".$re['nombre_subtipo']."|".$re['nombre_variedad']." x".$re['bandeja'], $re['id_artpedido'], $re['estado'], $re['fila'], $re['cant_band'], $re['nombrecliente'], $re['id_orden_alternativa'], $re['id_cliente'], $re['revision'], $re['solucion']]);
        }
      }
    }
    echo json_encode($arraycolumnas);
}else{
  echo "";
}

?>
