<?php
include("class_lib/sesionSecurity.php");
require('class_lib/class_conecta_mysql.php');

$consulta = $_POST['consulta'];
$id_pedido = $_POST['id_pedido'];
$con = mysqli_connect($host, $user, $password,$dbname);

if (!$con) {
  die("Connection failed: " . mysqli_connect_error());
}
mysqli_query($con, "SET NAMES 'utf8'");

if ($consulta == "pedido"){
	$cadena = "SELECT 
			p.id_artpedido as id_articulo, 
			v.id_articulo as id_variedad, 
			t.id_articulo as id_tipo, 
			t.nombre as nombre_tipo, 
			s.nombre as nombre_subtipo, 
			v.nombre as nombre_variedad, 
			p.cant_plantas as cant_plantas, 
			p.bandeja as bandeja, 
			p.cant_semi as cant_semi, 
			p.cant_band as cant_band, 
			p.fecha_entrega as fecha_entrega, 
			p.fecha_siembraestimada as fecha_siembra, 
			p.con_semilla,
			p.estado 
			FROM variedades_producto v 
			INNER JOIN subtipos_producto s ON v.id_subtipo = s.id_articulo 
			INNER JOIN tipos_producto t ON t.id_articulo = s.id_tipo
			INNER JOIN articulospedidos p ON p.id_articulo = v.id_articulo 
			WHERE p.id_pedido = $id_pedido";

	$val = mysqli_query($con, $cadena);
	$salida = "";

	if (mysqli_num_rows($val)>0){
		while($re=mysqli_fetch_array($val)){
			$producto = "$re[nombre_tipo] $re[nombre_subtipo] $re[nombre_variedad] x$re[bandeja]";
			if ($re['con_semilla'] == 1){
				$producto.= " CON SEMILLA";
			}
			else{
				$producto.= " SIN SEMILLA";
			}

			if ($re["estado"] == -1){
				$producto = "<del>".$producto."</del> <b>(CANCELADO)</b>";
			}

			$id_art = $re['id_articulo'];
			$btneliminar = $re["estado"] == 0 ? "<button id='$id_art' class='removeme btn btn-sm btn-danger fa fa-trash' onClick='eliminar_producto($id_art)'></button>" : "";
			$salida.="<tr>
							<td >$re[id_articulo]</td>
							<td>$producto</td>
							<td class='text-center'><p style='font-size:22px'>$re[cant_band]</p><p style='font-size:22px'>$re[cant_plantas]</p></td>
							<td class='text-center'>$re[fecha_siembra]</td>
							<td class='text-center'>$re[fecha_entrega]</td>
							<td>$btneliminar</td>
						</tr>";
		}
		echo $salida;
	}
}
else if ($consulta == "cliente"){
	$cadena="SELECT c.id_cliente as id_cliente, c.NOMBRE as nombre FROM clientes c INNER JOIN pedidos p ON c.id_cliente = p.ID_CLIENTE WHERE p.ID_PEDIDO = $id_pedido;";
	$val = mysqli_query($con, $cadena);
	$salida = "";

	if (mysqli_num_rows($val)>0){
		while($re=mysqli_fetch_array($val)){
			$salida = "Cliente: $re[nombre] ($re[id_cliente])";
		}
	}
	echo $salida;
}

?>