<?php

include "class_lib/sesionSecurity.php";
require 'class_lib/class_conecta_mysql.php';
require 'class_lib/funciones.php';

$consulta = $_POST['consulta'];

$con = mysqli_connect($host, $user, $password, $dbname);
// Check connection
if (!$con) {
    die("Connection failed: " . mysqli_connect_error());
}
mysqli_query($con, "SET NAMES 'utf8'");

if ($consulta == "orden") {
    $str = json_decode($_POST['jsonarray'], true);
    $rowLength = count($str);
    $lista = implode(", ", $str);
    if ($rowLength > 0) {
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

        if (mysqli_num_rows($val) > 0) {
            while ($re = mysqli_fetch_array($val)) {
                $producto = "$re[nombre_tipo] $re[nombre_subtipo] $re[nombre_variedad] x$re[bandeja]";
                if ($re['con_semilla'] == 1) {
                    $producto .= " CON SEMILLA";
                } else {
                    $producto .= " SIN SEMILLA";
                }
                $sobre = $re['codigo'];
                if (is_null($sobre)) {
                    $sobre = "<button id='btn_" . $re['id_articulo'] . "' class='btn btn-success  fa fa-plus-square' onclick='showDialogSobre(this.id)'></button>";
                }

                $id_art = $re['id_articulo'];
                $salida .= "<tr >
								<td>$re[id_articulo]</td>
								<td style='word-wrap:break-word;'>$producto</td>
								<td><p style='font-size:18px'>$re[cant_band]</p><p style='font-size:12px'>$re[cant_plantas]</p><p style='font-size:12px'>$re[cant_semi]</p></td>
								<td style='word-wrap:break-word;'>$re[cliente]</td>
								<td style='word-wrap:break-word;'>$sobre</td>
								<td><button id='$id_art' class='removeme btn btn-sm btn-danger fa fa-trash btn-modal-top' onClick='eliminar_art(this)'></button></td>
							</tr>";
            }
            echo $salida;
        }
    }
}
else if ($consulta == "pedido") {
    $id_artpedido = $_POST['id'];
    $cadena = "SELECT p.id_artpedido as id_articulo, v.id_articulo as id_variedad, t.id_articulo
	as id_tipo, t.nombre as nombre_tipo, s.nombre as nombre_subtipo, v.nombre as nombre_variedad, p.problema, p.observacionproblema,
	p.cant_plantas as cant_plantas, p.bandeja as bandeja, p.cant_semi as cant_semi, p.cant_band as
	cant_band,  p.fecha_entrega as fecha_entrega, p.revision, p.solucion, p.fecha_planificacion as
	fecha_siembra, p.con_semilla, p.estado, UPPER(p.cod_sobre) as codigo, o.id_orden_alternativa, o.id_orden, c.nombre as cliente,
	 o.cant_band_reales, DATE_FORMAT(o.fecha_camara_in, '%d/%m/%Y %H:%i') as fecha_camara, DATE_FORMAT(o.fecha_siembra, '%d/%m/%Y %H:%i') as fecha_sembrado,
	 DATE_FORMAT(o.fecha_mesada_in, '%d/%m/%Y %H:%i') as fechamesada, UPPER(pe.observaciones) as observaciones,
	 GROUP_CONCAT(DISTINCT(om.id_mesada) SEPARATOR ', ') as id_mesada, 
     (SELECT IFNULL(SUM(s.cantidad_original),0) FROM stock_bandejas s WHERE s.id_artpedido = $id_artpedido) as cantidad_stock,
     (SELECT SUM(monto) FROM pagos WHERE id_pedido = (SELECT id_pedido FROM articulospedidos WHERE id_artpedido = $id_artpedido)) as pagos
	FROM variedades_producto v
	INNER JOIN subtipos_producto s ON v.id_subtipo = s.id_articulo
	INNER JOIN tipos_producto t ON t.id_articulo = s.id_tipo
	INNER JOIN articulospedidos p ON p.id_articulo = v.id_articulo
	INNER JOIN pedidos pe ON pe.id_pedido = p.id_pedido
	INNER JOIN clientes c ON pe.id_cliente = c.ID_CLIENTE
	LEFT JOIN ordenes_siembra o ON p.id_artpedido = o.id_artpedido
    LEFT JOIN ordenes_mesadas om ON o.id_orden = om.id_orden
	WHERE p.id_artpedido = $id_artpedido";
    $errors = array();
        
    $val = mysqli_query($con, $cadena);
    if (!$val){
        $errors[] = mysqli_error($con);
        print_r($errors);
    }
    
    

    if (mysqli_num_rows($val) > 0) {
        try {
            $re = mysqli_fetch_assoc($val);

            $botoncambiocliente = $re["estado"] <= 4 ? " <button class='btn btn-primary btn-sm ml-2' onClick='cambiarCliente($id_artpedido, $re[estado]);'><i class='fa fa-edit'></i> Modificar/Enviar a Stock</button>" : "";
            $salida = "
            <div class='box-header with-border'>
                <h4 class='box-title'>Cliente: <span id='nombre_cliente2'>$re[cliente]</span> $botoncambiocliente</h4>
                <button style='float:right;' class='btn btn-modal-top fa fa-close'
                    onClick='$(\"#ModalVerEstado\").modal(\"hide\")'></button>
            </div>
            <div class='box-body'>
            ";

        $producto = "<span id='nombre_tipoproducto'>$re[nombre_tipo]</span> $re[nombre_subtipo] $re[nombre_variedad] x$re[bandeja]";
        if ($re['con_semilla'] == 1) {
            $producto .= " CON SEMILLA";
        } else {
            $producto .= " SIN SEMILLA";
        }
        $estado = generarBoxEstado($re["estado"], false);

        $mesada = "";

        if (!is_null($re['id_mesada'])) {
            if (strpos($re['id_mesada'], ',') !== false) {
                $mesada = "<div align='right'><span style='font-weight:bold;font-size: 38px;color:green;'>MESADAS Nº <span class='label-num-mesada'>$re[id_mesada]</span></span><br><button class='btn btn-primary btn-sm' onClick='ModificarMesadas($id_artpedido);'><i class='fa fa-edit'></i>  Reasignar Mesadas</button> </div>";
            } else {
                $mesada = "<div align='right'><span style='font-weight:bold;font-size: 38px;color:green;'>MESADA Nº <span class='label-num-mesada'>$re[id_mesada]</span></span><br><button class='btn btn-primary btn-sm' style='font-size:14px' onClick='ModificarMesadas($id_artpedido);'><i class='fa fa-edit'></i>  Reasignar Mesadas</button></div>";
            }
        }

        if (is_null($re['pagos'])) {
            $pagos = "0.00";
        } else {
            $pagos = $re['pagos'];
        }

        if ($re['estado'] >= 0 && $re['estado'] <= 1) {
            $band_pedidas = "<h4>Bandejas Pedidas: <span id='bandejaspedidas_original'>$re[cant_band]</span> <button class='btn btn-secondary btn-sm' onClick='ModificarCantidadPedida($id_artpedido);'><i class='fa fa-edit'></i>  Modificar</button></h4>";
        } else {
            $band_pedidas = "<h4>Bandejas Pedidas: <span id='bandejaspedidas_original'>$re[cant_band]</span></h4>";
        }

        $salida .= "<div class='infoproducto'>
							<div class='row'>
								<div class='col-md-4'>
									<h4>Orden Nº: <span id='ordenreal_$re[id_orden]' class='id_ordenreal'>$re[id_orden_alternativa]</span>  $estado</h4>

								</div>
								<div class='col-md-8'>";
        if ($re["revision"] == null) {
            $salida .= "<button type='button' class='btn btn-warning pull-right' onClick ='mostrarModalRevision($id_artpedido);'><i class='fa fa-exclamation-triangle'></i>  MARCAR REVISIÓN</button>";
        } else {
            $salida .= "<button type='button' class='btn btn-danger pull-right' id='btn_quitar_revision' onClick ='quitarRevision($id_artpedido);'><i class='fa fa-times'></i> QUITAR REVISIÓN</button>";
        }

        if ((int)$re["cantidad_stock"] > 0){
            $cantidad_stock = "<p class='text-danger font-weight-bold'> (SE ENVIÓ UN SOBRANTE DE $re[cantidad_stock] BAND. A STOCK)</p>";
        }
        $salida .= "
								</div>
							</div>
							<h4>Producto: $producto</h4>
							<div class='row'>
								<div class='col-md-4'>
									<h4>Bandejas Sembradas: <span id='cantidad_bandejas'>$re[cant_band_reales]</span></h4>
                                    $cantidad_stock
                                    </div>
								<div class='col-md-8'>";
        if ($re["problema"] == null) {
            $salida .= "<button style='color: white;background-color: #610B5E' 
                        type='button' 
                        class='btn pull-right' 
                        onClick ='marcarProblema($id_artpedido);'>
                        <i class='fa fa-exclamation-triangle'></i> MARCAR PROBLEMA</button>";
        } else {
            $salida .= "<button type='button' class='btn btn-danger pull-right' id='btn_quitar_problema' onClick ='quitarProblema($id_artpedido);'><i class='fa fa-times'></i> Quitar PROBLEMA</button>";
        }
        $salida .= "
								</div>
							</div>
							$band_pedidas
							<div class='row'>
								<div class='col-md-6'>
									<h4>Plantas: $re[cant_plantas]</h4>
								</div>
								<div class='col-md-6'>";
        if ($re["solucion"] == null && $re["revision"] != null) {
            $salida .= "<button type='button' class='btn btn-success' onClick ='MostrarModalSolucion($id_artpedido);'><i class='fa fa-check pull-right'></i> Aplicar SOLUCIÓN</button>";
        }
        $salida .= "
						   		</div>
							</div>
							<h4>Semillas: $re[cant_semi]</h4>
							<h4>Se sembró el día: $re[fecha_sembrado]</h4>
							<h4>Ingresó a Cámara el día: $re[fecha_camara]</h4>";
        if (!is_null($re['fechamesada'])) {
            $salida .= "<h4>Ingresó a Invernáculo el día: $re[fechamesada]</h4> ";
        }

        $btnfoto = "<h4 style='color:red'><b>NO TIENE</b></h4>";
        if (file_exists("imagenes/" . $re["id_articulo"] . ".jpg") || file_exists("imagenes/" . $re["id_articulo"] . ".jpeg") || file_exists("imagenes/" . $re["id_articulo"] . ".png")) {
            $btnfoto = "<button class='btn btn-primary' onclick='verFoto($re[id_articulo],1)' id='btn-verfoto'><i class='fa fa-image'></i> Ver Foto</button>";
        }

        $btnfoto2 = "<h4 style='color:red'><b>NO TIENE</b></h4>";
        if (file_exists("imagenes/" . $re["id_articulo"] . "_2.jpg") || file_exists("imagenes/" . $re["id_articulo"] . "_2.jpeg") || file_exists("imagenes/" . $re["id_articulo"] . "_2.png")) {
            $btnfoto2 = "<button class='btn btn-primary' onclick='verFoto($re[id_articulo],2)' id='btn-verfoto2'><i class='fa fa-image'></i> Ver Foto</button>";
        }

        $btnfoto3 = "<h4 style='color:red'><b>NO TIENE</b></h4>";
        if (file_exists("imagenes/" . $re["id_articulo"] . "_3.jpg") || file_exists("imagenes/" . $re["id_articulo"] . "_3.jpeg") || file_exists("imagenes/" . $re["id_articulo"] . "_3.png")) {
            $btnfoto3 = "<button class='btn btn-primary' onclick='verFoto($re[id_articulo],3)' id='btn-verfoto3'><i class='fa fa-image'></i> Ver Foto</button>";
        }

        $salida .= "<h4>Fecha Entrega Solicitada: $re[fecha_entrega]</h4>";

        $salida .= "
						   	<div style='background-color:#e6e6e6;padding:5px'>
							   	<span style='color:#74DF00;font-weight:bold;font-size:1.5em'>Observaciones de PEDIDO:</span><br>
							   	<textarea name='textarea' class='form-control' disabled='true' id='observaciones_txt' type='text' style='width:50%;text-transform:uppercase;resize:none'>$re[observaciones]
							   	</textarea>
							   	<br>
							   	<button class='btn btn-primary btn-sm' id='btn_modificar' onclick='ActivarText();'><i class='fa fa-edit'></i>  Modificar</button>
							   	<button class='btn btn-success btn-sm' id='btn_guardarobs' disabled='true' onclick='GuardarObservaciones();'><i class='fa fa-save'></i>  Guardar</button>
							</div>
						   	";

        $salida .= "$mesada <br><br><button type='button' class='btn btn-danger' onClick ='cancelarPedido();'><i class='fa fa-ban'></i> Marcar como CANCELADO</button> </div>

				<br><br>
				<div class='row'>
						   		<div class='col-md-2'>
						   			<h4 style='color:blue'><b>FOTO 1: </b></h4>
						   		</div>
						   		<div class='col-md-2'>
						   			$btnfoto
						   		</div>
						   		<div class='col-md-2'>

							   			<button class='btn btn-info' id='btn-sacarfoto1' onclick='abrir(1)'><i class='fa fa-camera'></i> Tomar Foto</button>
							   			<input type='file' accept='image/*' onchange='cambiofoto(1)' capture='camera' id='archivo' name='archivo' style='display:none'/>

						   		</div>

						   	</div>";

        $salida .= "
						   	<br>
						   	<div class='row'>
						   		<div class='col-md-2'>
						   			<h4 style='color:blue'><b>FOTO 2: </b></h4>
						   		</div>
						   		<div class='col-md-2'>
						   			$btnfoto2
						   		</div>
						   		<div class='col-md-2'>

							   			<button class='btn btn-info' id='btn-sacarfoto2' onclick='abrir(2)'><i class='fa fa-camera'></i> Tomar Foto</button>
							   			<input type='file' accept='image/*' onchange='cambiofoto(2)' capture='camera' id='archivo2' name='archivo2' style='display:none'/>

						   		</div>
						   	</div>
						   	<br>
						   	<div class='row'>
						   		<div class='col-md-2'>
						   			<h4 style='color:blue'><b>FOTO 3: </b></h4>
						   		</div>
						   		<div class='col-md-2'>
						   			$btnfoto3
						   		</div>
						   		<div class='col-md-2'>

							   			<button class='btn btn-info' id='btn-sacarfoto3' onclick='abrir(3)'><i class='fa fa-camera'></i> Tomar Foto</button>
							   			<input type='file' accept='image/*' onchange='cambiofoto(3)' capture='camera' id='archivo3' name='archivo3' style='display:none'/>

						   		</div>
						   	</div>

						   	";

            echo $salida;
        } catch (\Throwable $th) {
            print_r($th);
        }
    }
    else{
        $salida = "
            <div class='box-header with-border'>
            </div>
        <div class='box-body'>
        ";
    }
    $salida.="</div>";
}   else if ($consulta == "cargar_pagos") {
    $id_artpedido = $_POST['id_artpedido'];
    $cadena = "SELECT DATE_FORMAT(p.fecha, '%d/%m/%Y %H:%i') as fecha, p.monto, p.id_pago, p.concepto FROM pagos p WHERE id_pedido = (SELECT id_pedido FROM articulospedidos WHERE id_artpedido = $id_artpedido)
		ORDER BY p.id_pago DESC;";

    $val = mysqli_query($con, $cadena);
    $salida = "";

    if (mysqli_num_rows($val) > 0) {
        while ($re = mysqli_fetch_array($val)) {
            $salida .= "<tr >
								<td style='word-wrap:break-word;'>$re[fecha]</td>
								<td style='word-wrap:break-word;'>$re[concepto]</td>
								<td style='word-wrap:break-word;' class='text-right'>$re[monto]</td>
								<td><div align='center'><button id='pago_$re[id_pago]' class='removeme btn btn-sm btn-danger btn-modal-top fa fa-edit' onClick='modificar_pago(this)'></button></div></td>
								<td><div align='center'><button id='print_$re[id_pago]' class='btn btn-sm btn-primary   btn-modal-top fa fa-print' onClick='print_pago(1, this)'></button></div></td>
							</tr>";
        }
        echo $salida;
    }
} else if ($consulta == "modificacantidad_simple") {
    $id_artpedido = $_POST["id_artpedido"];
    $nuevacant = (int) $_POST["nuevacant"];
    $cadena = "SELECT bandeja FROM articulospedidos WHERE id_artpedido = $id_artpedido;";
    $val = mysqli_query($con, $cadena);
    $bandeja = "";
    if (mysqli_num_rows($val) > 0) {
        while ($re = mysqli_fetch_array($val)) {
            $bandeja = $re["bandeja"];
        }
    }
    $cadena = "UPDATE articulospedidos SET cant_band = $nuevacant, cant_plantas =
	($bandeja * $nuevacant), cant_semi = ($bandeja * $nuevacant * 1.10) WHERE id_artpedido = $id_artpedido;";
    mysqli_query($con, $cadena);
    echo $cadena;
} else if ($consulta == "modificar_observaciones") {
    $id_artpedido = $_POST["id_artpedido"];
    $observaciones = mysqli_real_escape_string($con, $_POST["observaciones"]);
    $cadena = "UPDATE pedidos SET observaciones = UPPER(TRIM('$observaciones')) WHERE ID_PEDIDO = (SELECT id_pedido FROM articulospedidos WHERE id_artpedido = $id_artpedido);";
    mysqli_query($con, $cadena);
} 
