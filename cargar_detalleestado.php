<?php
include("class_lib/sesionSecurity.php");
require('class_lib/class_conecta_mysql.php');
require('class_lib/funciones.php');

$consulta = $_POST['consulta'];
$con = mysqli_connect($host, $user, $password,$dbname);
if (!$con) {
  die("Connection failed: " . mysqli_connect_error());
}

if ($consulta == "orden"){
	$str = json_decode($_POST['jsonarray'], true); 
	$rowLength = count($str);
	$lista = implode( ", ", $str );

	if ($rowLength > 0){
		$cadena = "SELECT p.id_artpedido as id_articulo, v.id_articulo as id_variedad, t.id_articulo 
		as id_tipo, t.nombre as nombre_tipo, s.nombre as nombre_subtipo, v.nombre as nombre_variedad, 
		p.cant_plantas as cant_plantas, p.bandeja as bandeja, p.cant_semi as cant_semi, p.cant_band as 
		cant_band,p.fecha_entrega as fecha_entrega, p.fecha_planificacion as 
		fecha_siembra, p.con_semilla, p.estado, c.nombre as cliente, UPPER(p.cod_sobre) as codigo FROM variedades_producto v 
		INNER JOIN subtipos_producto s ON v.id_subtipo = s.id_articulo 
		INNER JOIN tipos_producto t ON t.id_articulo = s.id_tipo
		INNER JOIN articulospedidos p ON p.id_articulo = v.id_articulo
		INNER JOIN pedidos pe ON p.id_pedido = pe.ID_PEDIDO  
    	INNER JOIN clientes c ON c.id_cliente = pe.ID_CLIENTE 
		WHERE p.id_artpedido IN ($lista)";

		$val = mysqli_query($con, $cadena);
		$salida = "";

		if (mysqli_num_rows($val)>0){
			while($re=mysqli_fetch_array($val))
			{
				$producto = "$re[nombre_tipo] $re[nombre_subtipo] $re[nombre_variedad] x$re[bandeja]";
				if ($re['con_semilla'] == 1){
					$producto.= " CON SEMILLA";
				}
				else{
					$producto.= " SIN SEMILLA";
				}
				
				if (strlen(trim($re["codigo"])) > 0 && !is_null($re["codigo"])) {
					$sobre = $re["codigo"];
				}
				else{
					$sobre = "<button id='btn_".$re['id_articulo']."' class='btn btn-success btn-round fa fa-plus-square' onclick='showDialogSobre(this.id)'></button>";
				}

				$id_art = $re['id_articulo'];
				$salida.="<tr >
								<td>$re[id_articulo]</td>
								<td style='word-wrap:break-word;font-size:1.2em'>$producto</td>
								<td><p style='font-size:1.2em'>$re[cant_band]</p><p style='font-size:1em'>$re[cant_plantas]</p><p style='font-size:1em'>$re[cant_semi]</p></td>
								<td style='word-wrap:break-word;' >$re[cliente]</td>
								<td style='word-wrap:break-word;'>$sobre</td>
								<td><button id='$id_art' style='font-size:1.5em' class='removeme btn btn btn-xs fa fa-trash' onClick='eliminar_art(this)'></button></td>
							</tr>";
			}
			echo $salida;
		}
	}
}

if ($consulta == "pedido"){
	$id_artpedido = $_POST['id'];
	$cadena = "SELECT p.id_artpedido as id_articulo, v.id_articulo as id_variedad, t.id_articulo 
	as id_tipo, t.nombre as nombre_tipo, s.nombre as nombre_subtipo, v.nombre as nombre_variedad, 
	p.cant_plantas as cant_plantas, p.bandeja as bandeja, p.cant_semi as cant_semi, p.cant_band as 
	cant_band, p.fecha_entrega as fecha_entrega, p.fecha_planificacion as 
	fecha_siembra, p.con_semilla, p.estado, UPPER(p.cod_sobre) as codigo FROM variedades_producto v 
	INNER JOIN subtipos_producto s ON v.id_subtipo = s.id_articulo 
	INNER JOIN tipos_producto t ON t.id_articulo = s.id_tipo
	INNER JOIN articulospedidos p ON p.id_articulo = v.id_articulo 
	WHERE p.id_artpedido = $id_artpedido";

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
			$estado = generarBoxEstado($re["estado"], false);
			$salida.="<div class='infoproducto'>
							<h4>Producto: $producto</h4>
							<h4>Bandejas: $re[cant_band]</h4>
							<h4>Plantas: $re[cant_plantas]</h4>
							<h4>Semillas: $re[cant_semi]</h4>
							<h4>Fecha Entrega: $re[fecha_entrega]</h4>
							<h4>Código de Sobre: $re[codigo]";
							if ($re['estado'] < 2){
							 $salida.="<button class='btn btn-primary btn-sm ml-3' id='btn_$re[id_articulo]' onClick='showDialogSobre2(this.id);'>Cambiar Código</button></h4>";
							}
					   		$salida.="<h4 id='estado_txt'>Estado: $estado</h4>
					   </div>";
		}
		echo $salida;
	}
}
else if ($consulta == "cliente"){
	$id_artpedido = $_POST['id'];
	$cadena="SELECT c.id_cliente as id_cliente, c.NOMBRE as nombre FROM articulospedidos ap INNER JOIN pedidos p ON ap.id_pedido = p.id_pedido INNER JOIN clientes c ON c.id_cliente = p.id_cliente WHERE ap.id_artpedido = $id_artpedido;";
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

