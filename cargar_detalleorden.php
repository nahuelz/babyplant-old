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
        $query = "SELECT p.id_artpedido as id_articulo, v.id_articulo as id_variedad, t.id_articulo
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
        $val = mysqli_query($con, $query);
        $salida = "";
        echo $query;
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
                    $sobre = "<button id='btn_" . $re['id_articulo'] . "' class='btn btn-success fa fa-plus-square' onclick='showDialogSobre(this.id)'></button>";
                }

                $id_art = $re['id_articulo'];
                $salida .= "<tr >
								<td>$re[id_articulo]</td>
								<td style='word-wrap:break-word;'>$producto</td>
								<td><p style='font-size:18px'>$re[cant_band]</p><p style='font-size:12px'>$re[cant_plantas]</p><p style='font-size:12px'>$re[cant_semi]</p></td>
								<td style='word-wrap:break-word;'>$re[cliente]</td>
								<td style='word-wrap:break-word;'>$sobre</td>
								<td><button id='$id_art' class='removeme btn btn btn-sm btn-danger fa fa-trash' onClick='eliminar_art(this)'></button></td>
							</tr>";
            }
            echo $salida;
        }
    }
} else if ($consulta == "pedido") {
    $id_artpedido = $_POST['id'];
    $query = "SELECT
			p.id_artpedido as id_articulo,
			v.id_articulo as id_variedad,
			t.id_articulo as id_tipo,
			t.nombre as nombre_tipo,
			s.nombre as nombre_subtipo,
			v.nombre as nombre_variedad,
			p.problema,
            UPPER(pe.observaciones) as observaciones_pedido,
			p.observacionproblema,
			p.cant_plantas as cant_plantas,
			p.bandeja as bandeja,
			p.cant_semi as cant_semi,
			p.cant_band as cant_band,
			p.fecha_entrega as fecha_entrega,
			p.revision,
			p.solucion,
			p.fecha_planificacion as fecha_siembra,
			p.con_semilla,
			p.estado,
			UPPER(p.cod_sobre) as codigo,
			o.id_orden_alternativa,
			o.id_orden,
			c.nombre as cliente,
	 		o.cant_band_reales,
			DATE_FORMAT(o.fecha_camara_in, '%d/%m/%Y %H:%i') as fecha_camara,
			DATE_FORMAT(o.fecha_siembra, '%d/%m/%Y %H:%i') as fecha_sembrado,
	 		DATE_FORMAT(o.fecha_mesada_in, '%d/%m/%Y %H:%i') as fechamesada,
			UPPER(o.obsiembra) as observaciones,
			UPPER(o.obsproduccion) as observacionesproduccion,
	 		GROUP_CONCAT(DISTINCT(om.id_mesada) SEPARATOR ', ') as id_mesada
			FROM variedades_producto v
			INNER JOIN subtipos_producto s ON v.id_subtipo = s.id_articulo
			INNER JOIN tipos_producto t ON t.id_articulo = s.id_tipo
			INNER JOIN articulospedidos p ON p.id_articulo = v.id_articulo
			INNER JOIN pedidos pe ON pe.id_pedido = p.id_pedido
			INNER JOIN clientes c ON pe.id_cliente = c.ID_CLIENTE
			LEFT JOIN ordenes_siembra o ON p.id_artpedido = o.id_artpedido
			LEFT JOIN ordenes_mesadas om ON o.id_orden = om.id_orden
			WHERE p.id_artpedido = $id_artpedido";
    $val = mysqli_query($con, $query);
    $salida = "";

    if (mysqli_num_rows($val) > 0) {
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
                $mesada = "<div align='right'><span style='font-weight:bold;font-size: 38px;color:green;'>MESADAS Nº " . $re['id_mesada'] . "</span><br><button class='btn btn-sm btn-primary' onClick='ModificarMesadas($id_artpedido);'><i class='fa fa-edit'></i>  Reasignar Mesadas</button> </div>";
            } else {
                $mesada = "<div align='right'><span style='font-weight:bold;font-size: 38px;color:green;'>MESADA Nº " . $re['id_mesada'] . "</span><br><button class='btn btn-sm btn-primary' onClick='ModificarMesadas($id_artpedido);'><i class='fa fa-edit'></i>  Reasignar Mesadas</button></div>";
            }
        }

        if ($re['estado'] >= 0 && $re['estado'] <= 3 && $re["cliente"] != "STOCK") {
            $band_pedidas = "<h4>Bandejas Pedidas: <span id='bandejaspedidas_original'>$re[cant_band]</span> <button class='btn btn-sm btn-primary' onClick='ModificarCantidadPedida($id_artpedido);'><i class='fa fa-edit'></i>  Modificar</button></h4>";
        } else {
            $band_pedidas = "<h4>Bandejas Pedidas: $re[cant_band]</h4>";
        }

        $salida .= "<div class='infoproducto'>
							<div class='row'>
								<div class='col-md-4'>
									<h4>Orden Nº: <span id='ordenreal_$re[id_orden]' class='id_ordenreal'>$re[id_orden_alternativa]</span>  $estado</h4>

								</div>
								<div class='col-md-8'>";
        if ($re["revision"] == null) {
            $salida .= "<button type='button' class='btn btn-warning pull-right' onClick ='mostrarModalRevision($id_artpedido);'><i class='fa fa-exclamation-triangle'></i> MARCAR REVISIÓN</button>";
        } else {
            $salida .= "<button type='button' class='btn btn-danger pull-right' id='btn_quitar_revision' onClick ='quitarRevision($id_artpedido);'><i class='fa fa-times'></i> QUITAR REVISIÓN</button>";
        }
        $salida .= "
								</div>
							</div>
							<h4>Producto: $producto</h4>
							<div class='row'>
								<div class='col-md-4'>
									<h4>Bandejas Sembradas: <span id='cantidad_bandejas'>$re[cant_band_reales]</span></h4>
								</div>
								<div class='col-md-8'>";
        if ($re["problema"] == null) {
            $salida .= "<button style='color: white;background-color: #610B5E' type='button' class='btn pull-right' onClick ='marcarProblema($id_artpedido);'><i class='fa fa-exclamation-triangle'></i> MARCAR PROBLEMA</button>";
        } else {
            $salida .= "<button type='button' class='btn btn-danger pull-right' id='btn_quitar_problema' onClick ='quitarProblema($id_artpedido);'><i class='fa fa-times'></i> QUITAR PROBLEMA</button>";
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
            $salida .= "<button type='button' class='btn btn-success pull-right' onClick ='MostrarModalSolucion($id_artpedido);'><i class='fa fa-check'></i> Aplicar SOLUCIÓN</span></button>";
        }
        $salida .= "
						   		</div>
							</div>
							<h4>Se sembró el día: $re[fecha_sembrado]</h4>
							<h4>Ingresó a Cámara el día: $re[fecha_camara]</h4>";
        if (!is_null($re['fechamesada'])) {
            $salida .= "<h4>Ingresó a Invernáculo el día: $re[fechamesada]</h4> ";
        }

        $btnfoto = "<h4 class='text-danger'><b>NO TIENE</b></h4>";
        if (file_exists("imagenes/" . $re["id_articulo"] . ".jpg") || file_exists("imagenes/" . $re["id_articulo"] . ".jpeg") || file_exists("imagenes/" . $re["id_articulo"] . ".png")) {
            $btnfoto = "<button class='btn btn-primary' onclick='verFoto($re[id_articulo],1)' id='btn-verfoto'><i class='fa fa-image'></i> Ver Foto</button>";
        }

        $btnfoto2 = "<h4 class='text-danger'><b>NO TIENE</b></h4>";
        if (file_exists("imagenes/" . $re["id_articulo"] . "_2.jpg") || file_exists("imagenes/" . $re["id_articulo"] . "_2.jpeg") || file_exists("imagenes/" . $re["id_articulo"] . "_2.png")) {
            $btnfoto2 = "<button class='btn btn-primary' onclick='verFoto($re[id_articulo],2)' id='btn-verfoto2'><i class='fa fa-image'></i> Ver Foto</button>";
        }

        $btnfoto3 = "<h4 class='text-danger'><b>NO TIENE</b></h4>";
        if (file_exists("imagenes/" . $re["id_articulo"] . "_3.jpg") || file_exists("imagenes/" . $re["id_articulo"] . "_3.jpeg") || file_exists("imagenes/" . $re["id_articulo"] . "_3.png")) {
            $btnfoto3 = "<button class='btn btn-primary' onclick='verFoto($re[id_articulo],3)' id='btn-verfoto3'><i class='fa fa-image'></i> Ver Foto</button>";
        }

        $salida .= "<h4>Fecha Entrega Solicitada: $re[fecha_entrega]</h4>";

        $obs = "";
        if ($re["observaciones"] != null) {
            $obs = $re["observaciones"];
        }
        $obsproduccion = "";
        if ($re["observacionesproduccion"] != null) {
            $obsproduccion = $re["observacionesproduccion"];
        }

        $salida .= "
						   	<div style='background-color:#e6e6e6;padding:5px'>
							   	<span class='text-success' style='font-weight:bold;font-size:1.5em'>Observaciones de SIEMBRA:</span><br>
							   	<textarea class='form-control' name='observaciones_txt' disabled='true' id='observaciones_txt' type='text' style='width:50%;text-transform:uppercase;resize:none'>$obs</textarea>
							   	<br>
							</div>
							<br>
							<div style='background-color:#e6e6e6;padding:5px'>
							   	<span class='text-danger' style='font-weight:bold;font-size:1.5em'>Observaciones de PRODUCCIÓN:</span><br>
							   	<textarea name='observacionesproduccion_txt' disabled='true' id='observacionesproduccion_txt' type='text' style='width:50%;text-transform:uppercase;resize:none'>$obsproduccion</textarea>
							   	<br>
							   	<button class='btn btn-primary' style='font-size:14px' id='btn_modificar' onclick='ActivarText();'><i class='fa fa-edit'></i>  Modificar</button>
							   	<button class='btn btn-success' style='font-size:14px' id='btn_guardarobs' disabled='true' onclick='GuardarObservaciones();'><i class='fa fa-save'></i>  Guardar</button>
							</div>
                            <br>
                            <div style='background-color:#e6e6e6;padding:5px'>
							   	<span style='color:#74DF00;font-weight:bold;font-size:1.5em'>Observaciones de PEDIDO:</span><br>
							   	<textarea name='textarea' class='form-control' disabled='true' type='text' style='width:50%;text-transform:uppercase;resize:none'>$re[observaciones_pedido]
							   	</textarea>
							</div>
						   	";

        $salida .= "$mesada <br> </div>

				<br><br>
				<div class='row'>
						   		<div class='col-md-2'>
						   			<h4 class='text-primary'><b>FOTO 1: </b></h4>
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
						   			<h4 class='text-primary'><b>FOTO 2: </b></h4>
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
						   			<h4 class='text-primary'><b>FOTO 3: </b></h4>
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
    }
	else{
        $salida = "
            <div class='box-header with-border'>
            </div>
        <div class='box-body'>
        ";
    }
    $salida.="</div>";
} else if ($consulta == "cliente") {
    $id_artpedido = $_POST['id'];
    $query = "SELECT c.id_cliente as id_cliente, ap.estado, c.NOMBRE as nombre FROM articulospedidos ap INNER JOIN pedidos p ON ap.id_pedido = p.id_pedido INNER JOIN clientes c ON c.id_cliente = p.id_cliente WHERE ap.id_artpedido = $id_artpedido;";
    $val = mysqli_query($con, $query);
    $salida = "";

    if (mysqli_num_rows($val) > 0) {
        while ($re = mysqli_fetch_array($val)) {
            $salida = "<span id='nombrecliente_label'>$re[nombre]</span> (<span id='id_clienteoriginal'>$re[id_cliente]</span>)";
        }
    }
    echo $salida;
} else if ($consulta == "modificacantidad_simple") {
    $id_artpedido = $_POST["id_artpedido"];
    $nuevacant = (int) $_POST["nuevacant"];
    $query = "SELECT bandeja FROM articulospedidos WHERE id_artpedido = $id_artpedido;";
    $val = mysqli_query($con, $query);
    $bandeja = "";
    if (mysqli_num_rows($val) > 0) {
        while ($re = mysqli_fetch_array($val)) {
            $bandeja = $re["bandeja"];
        }
    }
    $query = "UPDATE articulospedidos SET cant_band = $nuevacant, cant_plantas =
	($bandeja * $nuevacant), cant_semi = ($bandeja * $nuevacant * 1.10) WHERE id_artpedido = $id_artpedido;";
    if (mysqli_query($con, $query)) {
        echo "success";
    } else {
        print_r(mysqli_error($con));
    }
} else if ($consulta == "modificar_observaciones") {
    $id_artpedido = $_POST["id_artpedido"];
    $observaciones = mysqli_real_escape_string($con, $_POST["observaciones"]);
    $query = "UPDATE ordenes_siembra SET obsproduccion = UPPER(TRIM('$observaciones')) WHERE id_artpedido = $id_artpedido;";
    if (mysqli_query($con, $query)) {
        echo "success";
    } else {
        print_r(mysqli_error($con));
    }
} else if ($consulta == "modificar_obsiembra") {
    $id_artpedido = $_POST["id_artpedido"];
    $observaciones = mysqli_real_escape_string($con, $_POST["observaciones"]);
    $query = "UPDATE ordenes_siembra SET obsiembra = UPPER(TRIM('$observaciones')) WHERE id_artpedido = $id_artpedido;";
    if (mysqli_query($con, $query)) {
        echo "success";
    } else {
        print_r(mysqli_error($con));
    }
}
