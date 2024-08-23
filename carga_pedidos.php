<?php
include("class_lib/sesionSecurity.php");
require('class_lib/class_conecta_mysql.php');

$id_cliente = $_POST['id_cliente'];
$con = mysqli_connect($host, $user, $password,$dbname);
if (!$con) {
  die("Connection failed: " . mysqli_connect_error());
}

$cadena="SELECT p.id_pedido as id_pedido, t.nombre as nombre, v.nombre_variedad as variedad, p.cant_plantas as cant_plantas, a.bandeja as bandeja, p.cant_semi as cant_semi, p.cant_band as cant_band, a.precio as precio, p.fecha_entrega as fecha_entrega, p.fecha_siembraestimada as fecha_siembra FROM tipo_articulo t INNER JOIN variedades v ON t.id_tipo = v.id_tipo INNER JOIN articulos a ON a.id_variedad = v.id_variedad INNER JOIN articulospedidos p ON p.id_articulo = a.id_articulo WHERE p.id_cliente = $id_cliente ORDER BY id_pedido DESC;";
$val = mysqli_query($con, $cadena);
$salida = "";

if (mysqli_num_rows($val)>0){
	while($re=mysqli_fetch_array($val)){
		$salida.="<tr >
						<td>$re[id_pedido]</td>
						<td>$re[nombre] $re[variedad] ($re[bandeja])</td>
						<td>$re[cant_plantas]</td>
						<td>$re[cant_semi]</td>
						<td>$re[cant_band]</td>
						<td></td>
					</tr>";
	}
	echo $salida;
}
?>