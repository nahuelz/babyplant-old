<?php
include "class_lib/sesionSecurity.php";

require 'class_lib/class_conecta_mysql.php';
require 'class_lib/funciones.php';
$con = mysqli_connect($host, $user, $password, $dbname);

if (!$con) {
    die("Connection failed: " . mysqli_connect_error());
}
mysqli_query($con, "SET NAMES 'utf8'");

$consulta = $_POST['consulta'];
$usuario = $_SESSION['nombre_de_usuario'];

$str = json_decode($_POST['jsonarray'], true);
$rowLength = count($str);

if ($consulta == "enviar_a_camara") {
    $errors = array();
    $id_art = $_POST['id_artpedido'];
    $fecha = $_POST['fecha'];
    mysqli_autocommit($con, false);

    $query = "UPDATE articulospedidos SET estado = 3, fila = NULL WHERE id_artpedido = $id_art;";
    if (!mysqli_query($con, $query)) {
        $errors[] = mysqli_error($con);
    }

    $query = "UPDATE ordenes_siembra SET fecha_camara_in = '$fecha' WHERE id_artpedido = $id_art;";
    if (!mysqli_query($con, $query)) {
        $errors[] = mysqli_error($con);
    }

    if (count($errors) === 0) {
        if (mysqli_commit($con)) {
            echo "success";
        } else {
            mysqli_rollback($con);
        }
    } else {
        mysqli_rollback($con);
        print_r($errors);
    }
    mysqli_close($con);
} else if ($consulta == "enviar_camara_multiple") {
    $errors = array();
    $ordenes = json_decode($_POST["ordenes"], true);
    mysqli_autocommit($con, false);
    $fecha = $_POST['fecha'];
    if (count($ordenes) > 0) {
        for ($i = 0; $i < count($ordenes); $i++) {
            $id_orden = $ordenes[$i];
            $query = "UPDATE articulospedidos SET estado = 3, fila = NULL WHERE id_artpedido = (SELECT id_artpedido FROM ordenes_siembra WHERE id_orden = $id_orden)";
            if (!mysqli_query($con, $query)) {
                $errors[] = mysqli_error($con);
            }

            $query = "UPDATE ordenes_siembra SET fecha_camara_in = '$fecha' WHERE id_orden = $id_orden;";
            if (!mysqli_query($con, $query)) {
                $errors[] = mysqli_error($con);
            }
        }
        if (count($errors) === 0) {
			if (mysqli_commit($con)) {
				echo "success";
			} else {
				mysqli_rollback($con);
			}
		} else {
			mysqli_rollback($con);
			print_r($errors);
		}
		mysqli_close($con);
    }
}
else if ($consulta == "orden"){
	$str = json_decode($_POST['jsonarray'], true); 
	$rowLength = count($str);
	$lista = implode( ", ", $str );

	if ($rowLength > 0){
		$cadena = "SELECT p.id_artpedido as id_articulo, v.id_articulo as id_variedad, t.id_articulo 
		as id_tipo, t.nombre as nombre_tipo, s.nombre as nombre_subtipo, v.nombre as nombre_variedad, 
		p.cant_plantas as cant_plantas, p.bandeja as bandeja, p.cant_semi as cant_semi, p.cant_band as 
		cant_band, p.fecha_entrega as fecha_entrega, p.fecha_planificacion as 
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

				$sobre = $re['codigo'];
				if (is_null($sobre)){
					$sobre = "<button id='btn_".$re['id_articulo']."' class='btn btn-success btn-round fa fa-plus-square' onclick='showDialogSobre(this.id)'></button>";
				}

				$id_art = $re['id_articulo'];
				$salida.="<tr >
								<td >$re[id_articulo]</td>
								<td style='word-wrap:break-word;'>$producto</td>
								<td><p style='font-size:18px'>$re[cant_band]</p><p style='font-size:12px'>$re[cant_plantas]</p><p style='font-size:12px'>$re[cant_semi]</p></td>
								<td style='word-wrap:break-word;' >$re[cliente]</td>
								<td style='word-wrap:break-word;'>$sobre</td>
								<td><button id='$id_art' class='removeme btn btn-sm btn-danger fa fa-trash btn-modal-top' onClick='eliminar_art(this)'></button></td>
							</tr>";
			}
			echo $salida;
		}
	}
}
else if ($consulta == "pedido"){
	$id_artpedido = $_POST['id'];
	$cadena = "SELECT p.id_artpedido as id_articulo, v.id_articulo as id_variedad, t.id_articulo 
    as id_tipo, t.nombre as nombre_tipo, s.nombre as nombre_subtipo, v.nombre as nombre_variedad, 
	p.cant_plantas as cant_plantas, p.bandeja as bandeja, p.cant_semi as cant_semi, p.cant_band as 
	cant_band, o.obsiembra, o.obscamara, o.problemacamara, o.dataproblema, p.fecha_entrega as fecha_entrega, p.fecha_planificacion as 
	fecha_siembra, p.con_semilla, p.estado, UPPER(p.cod_sobre) as codigo, o.id_orden_alternativa, o.id_orden, o.cant_band_reales, DATE_FORMAT(o.fecha_camara_in, '%d/%m/%Y %H:%i') as fecha_camara, DATE_FORMAT(o.fecha_siembra, '%d/%m/%Y %H:%i') as fecha_sembrado, DATE_FORMAT(o.fecha_mesada_in, '%d/%m/%Y %H:%i') as fechamesada, UPPER(pe.observaciones) as observaciones   
	FROM variedades_producto v 
	INNER JOIN subtipos_producto s ON v.id_subtipo = s.id_articulo 
	INNER JOIN tipos_producto t ON t.id_articulo = s.id_tipo
	INNER JOIN articulospedidos p ON p.id_articulo = v.id_articulo 
	INNER JOIN pedidos pe ON p.id_pedido = pe.id_pedido
	INNER JOIN ordenes_siembra o ON p.id_artpedido = o.id_artpedido
	WHERE p.id_artpedido = $id_artpedido";
	$val = mysqli_query($con, $cadena);
	$salida = "";
	if (mysqli_num_rows($val)>0){
		while($re=mysqli_fetch_array($val))
		{
			$producto = "<span id='nombre_tipoproducto'>$re[nombre_tipo]</span> $re[nombre_subtipo] $re[nombre_variedad] x$re[bandeja]";

			if ($re['con_semilla'] == 1){
				$producto.= " CON SEMILLA";
			}
			else{
				$producto.= " SIN SEMILLA";
			}

			$estado = generarBoxEstado($re["estado"], false);
			

			$botoncito = "";
			if ($usuario == "admin"){
				$botoncito = "<button class='btn btn-primary btn-sm' id='btn_modificar' onclick='cambiarCantidadSiembra($re[id_orden], $re[id_orden_alternativa], this, $re[cant_band_reales])'><i class='fa fa-edit'></i>  Modificar</button>"; 
			}

			$obsiembra = $re["obsiembra"];
			if ($obsiembra == NULL || strlen(trim($obsiembra)) == 0){
				$obsiembra = "";
			}

			$obscamara = $re["obscamara"];
			if ($obscamara == NULL || strlen(trim($obscamara)) == 0){
				$obscamara = "";
			}

			$problema = NULL;
			switch ($re["problemacamara"]) {
				case 1:
					if ($re["dataproblema"] == 1)
						$problema = "Problema: SACAR 1 DÍA ANTES";
					else if ($re["dataproblema"] > 1){
						$problema = "Problema: SACAR ".(string)$re["dataproblema"]." DÍAS ANTES";
					}
					else{
						$problema = "SACAR ANTES";
					}
					break;
				
				case 2:
					if ($re["dataproblema"] == 1)
						$problema = "Problema: QUEDA 1 DÍA";
					else if ($re["dataproblema"] > 1){
						$problema = "Problema: QUEDA ".(string)$re["dataproblema"]." DÍAS";
					}
					else{
						$problema = "QUEDA";
					}
					break;
				case 3:
					$problema = "Problema: PASA A OPCIÓN ".(string)$re["dataproblema"];
					break;
				default:
					$problema = NULL;# code...
					break;
			}
			
			$salida.="<div class='infoproducto'>
							<div class='row'>
								<div class='col-md-8'>
									<h4 style='font-weight:bold'>Orden Nº: <span id='ordenreal_$re[id_orden]' class='id_ordenreal'>$re[id_orden_alternativa] </span> $estado</h4>
								</div>
								<div class='col-md-4'>";
									if ($problema != NULL){
										$salida.="<h4 class='pull-right' style='font-weight:bold;color:red'>$problema</h4>";
									}
								$salida.="
								</div>
							</div>
							<div class='row'>
								<div class='col-md-8'>
									<h4>Producto: $producto</h4>
								</div>
								<div class='col-md-4'>";
								if ($re["estado"] == 3){
									if ($re["problemacamara"] == NULL){						$salida.="
										<button style='width:16em;' type='button' class='btn btn-danger btn-round fa fa-exclamation-triangle pull-right' onClick ='marcarProblema($re[id_orden], 1, \"Sacar Antes\", $re[id_artpedido]);'><span style='font-size:1.4em;font-family: Calibri'> SACAR ANTES</span></button>";
									}
									else{
										$salida.="
										<button style='width:16em;' type='button' class='btn btn-danger btn-round fa fa-close pull-right' onClick ='quitarProblema($re[id_orden], $re[id_artpedido]);'><span style='font-size:1.4em;font-family: Calibri'> QUITAR PROBLEMA</span></button>";	
									}
								}
								$salida.="
								</div>
							</div>
							<h4>Bandejas Sembradas: <span id='cantidad_bandejas'>$re[cant_band_reales]</span> $botoncito</h4>
							<div class='row'>
								<div class='col-md-8'>
									<h4>Bandejas Pedidas: $re[cant_band]</h4>
								</div>
								<div class='col-md-4'>";
									if ($re["problemacamara"] == NULL && $re["estado"] == 3){
										$salida.="
										<button style='width:16em;' type='button' class='btn btn-warning btn-round fa fa-exclamation-triangle pull-right' onClick ='marcarProblema($re[id_orden], 2, \"Queda\", $re[id_artpedido]);'><span style='font-size:1.4em;font-family: Calibri'> QUEDA</span></button>";
									}
									$salida.="
								</div>
							</div>
							<h4>Plantas: $re[cant_plantas]</h4>
							<div class='row'>
								<div class='col-md-8'>
									<h4>Semillas: $re[cant_semi]</h4>
								</div>
								<div class='col-md-4'>";
									if ($re["problemacamara"] == NULL && $re["estado"] == 3){
										$salida.="
										<button style='width:16em;' type='button' class='btn btn-primary btn-round fa fa-exclamation-triangle pull-right' onClick ='marcarProblema($re[id_orden], 3, \"Pasa de Cámara\", $re[id_artpedido]);'><span style='font-size:1.4em;font-family: Calibri'> PASA DE CÁMARA</span></button>";
									}
									$salida.="
								</div>
							</div>

							<h4>Se sembró el día: $re[fecha_sembrado]</h4>
							<h4>Ingresó a Cámara el día: $re[fecha_camara]</h4>";

							if (!is_null($re['fechamesada'])){
								$salida.="<h4>Ingresó a Invernáculo el día: $re[fechamesada]</h4>";
							}

							$salida.="
						   	<div class='row'>
								<div class='col-md-8'>
									<h4>Fecha Entrega Solicitada: $re[fecha_entrega]</h4>	
								</div>
								<div class='col-md-4'>";
								if ($re["estado"] == 3){
									$salida.="
									<button style='width:16em;' type='button' class='btn btn-success btn-round fa fa-check pull-right' onClick='enviar_a_Mesada($id_artpedido)'><span style='font-size:1.4em;font-family: Calibri'> ENVIAR A MESADA</span></button>";
								}
								$salida.="
								</div>
							</div>";


						   	$salida.="
						   	<div style='background-color:#e6e6e6;padding:5px'>
							   	<span style='color:blue;font-weight:bold;font-size:1.5em'>Observaciones de CÁMARA:</span><br>
							   	<textarea name='observacionescamara_txt' disabled='true' id='observacionescamara_txt' type='text' style='width:50%;text-transform:uppercase;resize:none'>$obscamara</textarea>
							   	<br>
							   	<button class='btn btn-primary btn-sm' id='btn_modificar' onclick='ActivarText();'><i class='fa fa-edit'></i>  Modificar</button>
							   	<button class='btn btn-success btn-sm' id='btn_guardarobs' disabled='true' onclick='GuardarObservaciones();'><i class='fa fa-save'></i>  Guardar</button>
							</div>
							<br>
							<div style='background-color:#e6e6e6;padding:5px'>
							   	<span style='color:green;font-weight:bold;font-size:1.5em'>Observaciones de SIEMBRA:</span><br>
							   	<textarea name='observaciones_txt' disabled='true' id='observaciones_txt' type='text' style='width:50%;text-transform:uppercase;resize:none'>$obsiembra</textarea>
							   	<br>
							</div>
							
						   	";	

			
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
		while($re=mysqli_fetch_array($val))
		{
			$salida = "Cliente: $re[nombre] ($re[id_cliente])";
		}
	}
	echo $salida;
}
else if ($consulta == "modificar_observaciones"){
	$id_artpedido = $_POST["id_artpedido"];
	$observaciones = mysqli_real_escape_string($con, $_POST["observaciones"]);
	$cadena="UPDATE ordenes_siembra SET obscamara = UPPER(TRIM('$observaciones')) WHERE id_artpedido = $id_artpedido;";
	if (mysqli_query($con, $cadena)){
        echo "success";
    }
    else{
        print_r(mysqli_error($con));
    }
}
else if ($consulta == "guardar_problema"){
	$id_orden = $_POST["id_orden"];
	$dias = $_POST["dias"];
	$tipo = $_POST["tipo"];

	$cadena="UPDATE ordenes_siembra SET problemacamara = $tipo, dataproblema = $dias WHERE id_orden = $id_orden";
	if (mysqli_query($con, $cadena)){
        echo "success";
    }
    else{
        print_r(mysqli_error($con));
    }
}
else if ($consulta == "quitar_problema"){
	$id_orden = $_POST["id_orden"];
	
	$cadena="UPDATE ordenes_siembra SET problemacamara = NULL, dataproblema = NULL WHERE id_orden = $id_orden";
	if (mysqli_query($con, $cadena)){
        echo "success";
    }
    else{
        print_r(mysqli_error($con));
    }
}