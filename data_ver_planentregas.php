<?php
include "class_lib/sesionSecurity.php";
require 'class_lib/class_conecta_mysql.php';
$consulta = $_POST['consulta'];
$con = mysqli_connect($host, $user, $password, $dbname);
if (!$con) {
    die("Connection failed: " . mysqli_connect_error());
}
mysqli_query($con, "SET NAMES 'utf8'");

if ($consulta == "orden") {
    $str = json_decode($_POST['jsonarray'], true);
    $rowLength = count($str);
    $lista = implode(", ", $str);
    if ($rowLength > 0) {
        $cadena = "SELECT pe.id_pedido as id_pe, o.id_orden_alternativa, p.id_artpedido,
			p.id_pedido, v.id_articulo as id_variedad, t.id_articulo as id_tipo, t.nombre as nombre_tipo,
			s.nombre as nombre_subtipo, v.nombre as nombre_variedad, (o.cant_band_reales - IFNULL(SUM(en.cantidad), 0)) as cantirestante, o.cant_band_reales, p.bandeja, p.fecha_entrega,
			DATE_FORMAT(o.fecha_siembra, '%d/%m/%Y') as fecha_siembra, c.nombre as cliente, c.id_cliente, p.estado, GROUP_CONCAT(DISTINCT(om.id_mesada) SEPARATOR ', ') as id_mesada
			FROM variedades_producto v
			INNER JOIN subtipos_producto s ON v.id_subtipo = s.id_articulo
			INNER JOIN tipos_producto t ON t.id_articulo = s.id_tipo
			INNER JOIN articulospedidos p ON p.id_articulo = v.id_articulo
			LEFT JOIN entregas en ON p.id_artpedido = en.id_artpedido
			INNER JOIN pedidos pe ON p.id_pedido = pe.id_pedido
			INNER JOIN clientes c ON pe.id_cliente = c.id_cliente
			LEFT JOIN ordenes_siembra o ON o.id_artpedido = p.id_artpedido
			LEFT JOIN ordenes_mesadas om ON o.id_orden = om.id_orden
			WHERE p.id_artpedido IN ($lista) GROUP BY p.id_artpedido";
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
                $id_art = $re['id_artpedido'];
                $salida .= "<tr x-id-artpedido='$re[id_artpedido]'>
								<td style='font-size:1.2em;font-weight:bold;text-align:center;' >$re[id_orden_alternativa]</td>
								<td style='word-wrap:break-word;'>$producto</td>
								<td class='text-danger font-weight-bold' style='text-align:center;font-size:1.3em'>$re[cantirestante]</td>
								<td style='text-align:center;font-size:1.3em'>$re[cant_band_reales]</td>
								<td id='cliente_$re[id_cliente]' style='word-wrap:break-word;' >$re[cliente] ($re[id_cliente])</td>
								<td style='word-wrap:break-word;'>$re[fecha_entrega]</td>
								<td style='font-size:1.2em;font-weight:bold;text-align:center;word-wrap:break-word;'>$re[id_mesada]</td>
								<td style='word-wrap:break-word;'><div id='modo_$re[id_artpedido]' align='center'></div></td>
								<td style='word-wrap:break-word;font-size:1.2em;'><div id='entregar_$re[id_artpedido]' align='center'></div></td>
								<td style='word-wrap:break-word;font-size:1.2em;'><div id='telefono_$re[id_artpedido]' align='center'></div></td>
								<td>

								 <div class='d-flex flex-row'>
								 	<div class='p-2'>
									 	<button id='$id_art' class='removeme btn btn btn-sm btn-danger fa fa-trash' onClick='eliminar_item(`$re[id_artpedido]`)' style='font-size:1.4em'></button>
								 	</div>
								 	<div class='p-2'>
								 	 	<button id='boton_$re[id_artpedido]' class='btn btn-sm btn-success fa fa-plus-square' style='font-size:1.4575em'  onClick='MostrarModalModoEntrega(this);'></button>
									</div>
								 </div>


								</td>
							</tr>";
            }
            echo $salida;
        }
    }
} else if ($consulta == "orden_inmediata") {
    $str = json_decode($_POST['jsonarray'], true);
    $rowLength = count($str);
    $lista = implode(", ", $str);
    if ($rowLength > 0) {
        $cadena = "SELECT pe.id_pedido as id_pe, o.id_orden_alternativa, p.id_artpedido, p.con_semilla,
			p.id_pedido, v.id_articulo as id_variedad, t.id_articulo as id_tipo, t.nombre as nombre_tipo,
			s.nombre as nombre_subtipo, v.nombre as nombre_variedad, (o.cant_band_reales - IFNULL(SUM(en.cantidad), 0)) as cantirestante, o.cant_band_reales, p.bandeja, p.fecha_entrega,
			DATE_FORMAT(o.fecha_siembra, '%d/%m/%Y') as fecha_siembra, c.nombre as cliente, c.id_cliente, p.estado, GROUP_CONCAT(DISTINCT(om.id_mesada) SEPARATOR ', ') as id_mesada,
			t.precio_288, t.precio_200, t.precio_162,
            t.precio_128, t.precio_72, t.precio_50, t.precio_25, t.precio_49,
            t.precio_288_s, t.precio_200_s, t.precio_162_s,
            t.precio_128_s, t.precio_72_s, t.precio_50_s, t.precio_25_s, t.precio_49_s,
            v.precio_288 as v_precio_288, v.precio_200 as v_precio_200, v.precio_162 as v_precio_162,
            v.precio_128 as v_precio_128, v.precio_72  as v_precio_72, v.precio_50 as v_precio_50, v.precio_25 as v_precio_25, v.precio_49 as v_precio_49,
            v.precio_288_s as v_precio_288_s, v.precio_200_s as v_precio_200_s, v.precio_162_s as v_precio_162_s,
            v.precio_128_s as v_precio_128_s, v.precio_72_s as v_precio_72_s, v.precio_50_s as v_precio_50_s, v.precio_25_s as v_precio_25_s, v.precio_49_s as v_precio_49_s
			FROM variedades_producto v
			INNER JOIN subtipos_producto s ON v.id_subtipo = s.id_articulo
			INNER JOIN tipos_producto t ON t.id_articulo = s.id_tipo
			INNER JOIN articulospedidos p ON p.id_articulo = v.id_articulo
			LEFT JOIN entregas en ON p.id_artpedido = en.id_artpedido
			INNER JOIN pedidos pe ON p.id_pedido = pe.id_pedido
			INNER JOIN clientes c ON pe.id_cliente = c.id_cliente
			LEFT JOIN ordenes_siembra o ON o.id_artpedido = p.id_artpedido
			LEFT JOIN ordenes_mesadas om ON o.id_orden = om.id_orden
			WHERE p.id_artpedido IN ($lista) GROUP BY p.id_artpedido";
        $val = mysqli_query($con, $cadena);
        $salida = "";
        if (mysqli_num_rows($val) > 0) {
            while ($re = mysqli_fetch_array($val)) {
                $producto = "$re[nombre_tipo] $re[nombre_subtipo] $re[nombre_variedad] x$re[bandeja]";

                $precio = "";
                if ($re['con_semilla'] == 1) {
                    $producto .= " CON SEMILLA";
                    if ($re["v_precio_" . $re["bandeja"] . "_s"] != null) {
                        $precio = (int) $re["v_precio_" . $re["bandeja"] . "_s"];
                    } else if ($re["precio_" . $re["bandeja"] . "_s"] != null) {
                        $precio = (int) $re["precio_" . $re["bandeja"] . "_s"];
                    }
                } else {
                    $producto .= " SIN SEMILLA";
                    if ($re["v_precio_" . $re["bandeja"]] != null) {
                        $precio = (int) $re["v_precio_" . $re["bandeja"]];
                    } else if ($re["precio_" . $re["bandeja"]] != null) {
                        $precio = (int) $re["precio_" . $re["bandeja"]];
                    }
                }
                $id_art = $re['id_artpedido'];
                $salida .= "<tr class='tr-entrega'>
								<td id='art_$re[id_artpedido]' style='font-size:1.2em;font-weight:bold;text-align:center;' class='id_artpedidos'>$re[id_orden_alternativa]</td>
								<td style='word-wrap:break-word;'>$producto</td>
								<td class='text-danger' style='font-weight:bold;text-align:center;font-size:1.3em'>$re[cantirestante]</td>
								<td style='text-align:center;font-size:1.3em'>$re[cant_band_reales]</td>
								<td style='font-size:1.2em;font-weight:bold;text-align:center;word-wrap:break-word;'>$re[id_mesada]</td>
								<td class='text-center' style='word-wrap:break-word;'>
								<input
								onkeyup='calcularSubtotal(this)' onpaste='calcularSubtotal(this)'
								style='font-size: 1.3em;' class='preciobox text-center text-success font-weight-bold form-control' type='number' min='0' value='$precio' max='999999'> </td>
								<td><div id='entregar_$re[id_artpedido]' align='center'><input class='cantidadbox text-center text-primary font-weight-bold form-control' type='number' min='0' style='font-size: 1.3em !important;' max='$re[cantirestante]' onkeyup='checkCantidad(this);calcularSubtotal(this)' onpaste='checkCantidad(this);calcularSubtotal(this)'> </div></td>
							</tr>";
            }
            echo $salida;
        }
    }
} else if ($consulta == "pedido") {
    $id_artpedido = $_POST['id'];
    $cadena = "SELECT p.id_artpedido as id_articulo, v.id_articulo as id_variedad, t.id_articulo
	as id_tipo, t.nombre as nombre_tipo, s.nombre as nombre_subtipo, v.nombre as nombre_variedad,
	p.cant_plantas as cant_plantas, p.bandeja as bandeja, p.cant_semi as cant_semi, p.con_semilla, p.cant_band as
	cant_band, p.fecha_entrega as fecha_entrega, p.fecha_planificacion as
	fecha_siembra, p.con_semilla, p.estado, UPPER(p.cod_sobre) as codigo, UPPER(pe.observaciones) FROM variedades_producto v
	INNER JOIN subtipos_producto s ON v.id_subtipo = s.id_articulo
	INNER JOIN tipos_producto t ON t.id_articulo = s.id_tipo
	INNER JOIN articulospedidos p ON p.id_articulo = v.id_articulo
	INNER JOIN pedidos pe ON p.id_pedido = pe.id_pedido
	WHERE p.id_artpedido = $id_artpedido";

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
            $estado = "";
            if ($re['estado'] == 0) {
                $estado = "<span style='background-color:#A4A4A4; border-radius:10px; border-style: solid; border-color: black;border-width: 2px; padding:5px;'>PENDIENTE</span>";
            } else if ($re['estado'] == 1) {
                $estado = "<span style='background-color:#FFFF00; border-radius:10px; border-style: solid; border-color: black;border-width: 2px; padding:5px;'>PLANIFICADO</span>";
            } else if ($re['estado'] == 2) {
                $estado = "<span style='background-color:#74DF00; border-radius:10px; border-style: solid; border-color: black;border-width: 2px; padding:5px;'>SEMBRADO</span>";
            } else if ($re['estado'] == 3) {
                $estado = "<span style='background-color:#2E9AFE; border-radius:10px; border-style: solid; border-color: black;border-width: 2px; padding:5px;'>EN CÁMARA</span>";
            }
            $salida .= "<div class='infoproducto'>
							<h4>Producto: $producto</h4>
							<h4>Bandejas: $re[cant_band]</h4>
							<h4>Plantas: $re[cant_plantas]</h4>
							<h4>Semillas: $re[cant_semi]</h4>
							<h4>Fecha Entrega: $re[fecha_entrega]</h4>
							<h4>Código de Sobre: $re[codigo] <button id='btn_$re[id_articulo]' onClick='showDialogSobre2(this.id);'>Cambiar Código</button></h4>
					   		<h4 id='estado_txt'>Estado: $estado</h4>
					   </div>";
        }
        echo $salida;
    }
} else if ($consulta == "domicilio") {
    $id_cliente = $_POST['id_cliente'];
    $cadena = "SELECT domicilio FROM clientes WHERE id_cliente = $id_cliente;";
    $val = mysqli_query($con, $cadena);
    $salida = "";
    if (mysqli_num_rows($val) > 0) {
        while ($re = mysqli_fetch_array($val)) {
            $salida = "$re[domicilio]";
        }
    }
    echo $salida;
} else if ($consulta == "agrega_orden") {
    $str = json_decode($_POST['jsonarray'], true);
    $fecha_entrega = $_POST["fecha"];
    $errors = array();
    mysqli_autocommit($con, false);
    for ($i = 0; $i < count($str); $i++) {
        $id_artpedido = $str[$i]["id_artpedido"];
        $modoentrega = mysqli_real_escape_string($con, $str[$i]["domicilio"]);
        $cantidad_entregar = $str[$i]["cantidad"];
        $telefono = $str[$i]["telefono"];
        $fecha = explode("/", $fecha_entrega);
        $fechanueva = $fecha[2] . "-" . $fecha[1] . "-" . $fecha[0];

        $query = "UPDATE articulospedidos SET estado =
		IF ( estado = 6, 6, 5), fecha_entrega = '$fecha_entrega' WHERE id_artpedido = $id_artpedido;";
        if (!mysqli_query($con, $query)) {
            $errors[] = mysqli_error($con);
        }
        $query = "INSERT INTO agenda_entregas (cantidad, fecha, id_artpedido, modo_entrega, telefono) VALUES ($cantidad_entregar, '$fechanueva', $id_artpedido, '$modoentrega', '$telefono');";
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
} else if ($consulta == "entrega_inmediata") {
    $str = json_decode($_POST['jsonarray'], true);
    $descuento = $_POST["descuento"];
    $tipodescuento = $_POST["tipodescuento"];
    $subtotal = $_POST["subtotal"];
    $id_cliente = $_POST["id_cliente"];
    $codigo = $_POST["codigo"];
    $errors = array();

    $query = "SELECT id_mesada,cantidad FROM ordenes_mesadas WHERE id_orden = (SELECT id_orden FROM ordenes_siembra WHERE id_artpedido = $id_artpedido) ORDER BY cantidad DESC";
    $query_mesadas = mysqli_query($con, $query);

    $query = "INSERT INTO remitos (id_cliente) VALUES ($id_cliente);";
    if (mysqli_query($con, $query)) {
        $id_remito = mysqli_insert_id($con);
        if ($id_remito && (int)$id_remito > 0) {
            mysqli_autocommit($con, false);

            $query = "UPDATE remitos SET codigo = '$codigo', fecha = NOW(), total = '$subtotal', descuento = '$descuento' WHERE id_remito = $id_remito;";
            if (!mysqli_query($con, $query)) {
                $errors[] = mysqli_error($con);
            }

            $fecha = date('d/m/Y');
            for ($i = 0; $i < count($str); $i++) {
                $id_artpedido = $str[$i]["id_artpedido"];
                $cantidad = $str[$i]["cantidad"];
                $cantidadnum = (int) $str[$i]["cantidad"];
                $tipo_entrega = $str[$i]["tipo_entrega"];
                $precio = $str[$i]["precio"];

                if ($tipo_entrega == "parcial") {
                    $query = "UPDATE articulospedidos SET estado = 6, fecha_entrega = '$fecha', modoentrega = 'ENTREGA INMEDIATA PARCIAL' WHERE id_artpedido = $id_artpedido;";
                } else if ($tipo_entrega == "completa") {
                    $query = "UPDATE articulospedidos SET estado = 7, fecha_entrega = '$fecha', modoentrega = 'ENTREGA INMEDIATA COMPLETA' WHERE id_artpedido = $id_artpedido;";
                }
                if (!mysqli_query($con, $query)) {
                    $errors[] = mysqli_error($con);
                }

                if (mysqli_num_rows($query_mesadas) == 1) {
                    $query = "UPDATE ordenes_mesadas SET cantidad = cantidad - $cantidad, estado = 1 WHERE id_orden = (SELECT id_orden FROM ordenes_siembra WHERE id_artpedido = $id_artpedido);";
                    if (!mysqli_query($con, $query)) {
                        $errors[] = mysqli_error($con);
                    }
                } else if (mysqli_num_rows($query_mesadas) > 1) {
                    while ($re = mysqli_fetch_array($query_mesadas)) {
                        $cantidadobtenida = (int) $re["cantidad"];
                        $mesada = $re["id_mesada"];
                        if ($cantidadobtenida > $cantidadnum) {
                            $query = "UPDATE ordenes_mesadas SET cantidad = cantidad - $cantidad, estado = 1 WHERE id_orden = (SELECT id_orden FROM ordenes_siembra WHERE id_artpedido = $id_artpedido) AND id_mesada = $mesada;";
                            if (!mysqli_query($con, $query)) {
                                $errors[] = mysqli_error($con);
                            }
                            break;
                        } else {
                            $query = "UPDATE ordenes_mesadas SET cantidad = 0, estado = 1 WHERE id_orden = (SELECT id_orden FROM ordenes_siembra WHERE id_artpedido = $id_artpedido) AND id_mesada = $mesada;";
                            if (!mysqli_query($con, $query)) {
                                $errors[] = mysqli_error($con);
                            }
                            $cantidadnum = $cantidadnum - $cantidadobtenida;
                        }
                    }
                }

                $query = "INSERT INTO entregas
			(cantidad, fecha, id_artpedido, id_remito, precio) VALUES
			($cantidad, NOW(), $id_artpedido, $id_remito, $precio);";
                if (!mysqli_query($con, $query)) {
                    $errors[] = mysqli_error($con) . "-" . $query;
                }
            }
            if (count($errors) === 0) {
                if (mysqli_commit($con)) {
                    echo "success";
                } else {
                    mysqli_rollback($con);
                    mysqli_query($con, "DELETE FROM remitos WHERE id_remito = $id_remito");
                }
            } else {
                mysqli_rollback($con);
                print_r($errors);
                mysqli_query($con, "DELETE FROM remitos WHERE id_remito = $id_remito");
            }
            mysqli_close($con);
        }
        else{
            echo "Error al guardar los datos";
        }
    } else {
        print_r(mysqli_error($con));
    }

} else if ($consulta == "entrega_inmediata_cambiocliente") {
    //29-05-2022 HAY QUE ARREGLAR ESTO, ES MUY REBUSCADO
    $str = json_decode($_POST['jsonarray'], true);
    $descuento = $_POST["descuento"];
    $tipodescuento = $_POST["tipodescuento"];
    $subtotal = $_POST["subtotal"];
    $id_cliente = $_POST["id_cliente"];
    $codigo = $_POST["codigo"];
    $cadena = "";

    try {

        $query = "INSERT INTO pedidos (ID_CLIENTE, FECHA, id_clienteoriginal, observaciones, fecha_real)
		VALUES ($id_cliente, NOW(), $id_cliente, '', NOW());";

        mysqli_query($con, $query);
        $id_pedido = mysqli_insert_id($con);

        $query = "INSERT INTO remitos (codigo, id_cliente, fecha, total, descuento) VALUES ('$codigo',$id_cliente,NOW(), $subtotal, $descuento);";

        mysqli_query($con, $query);
        $id_remito = mysqli_insert_id($con);

        $fecha = date('d/m/Y');
        for ($i = 0; $i < count($str); $i++) {
            $id_artpedido = $str[$i]["id_artpedido"];
            $id_artpedidonuevo = null;
            $cantidad = $str[$i]["cantidad"];
            $cantidadnum = (int) $str[$i]["cantidad"];
            $tipo_entrega = $str[$i]["tipo_entrega"];
            $precio = $str[$i]["precio"];

            if ($tipo_entrega == "parcial") {
                $query = "INSERT INTO articulospedidos (
				id_articulo,
				cant_plantas,
				cant_band,
				cant_semi,
				bandeja,
				fecha_entrega,
				fecha_entrega_original,
				fecha_siembraestimada,
				fecha_planificacion,
				id_pedido,
				estado,
				con_semilla,
				revision,
				solucion,
				problema,
				observacionproblema
			)
			SELECT
				id_articulo,
				0,
				$cantidad,
				0,
				bandeja,
				DATE_FORMAT(NOW(), '%d/%m/%Y'),
				DATE_FORMAT(NOW(), '%d/%m/%Y'),
				fecha_siembraestimada,
				fecha_planificacion,
				$id_pedido,
				7,
				con_semilla,
				revision,
				solucion,
				problema,
				observacionproblema
			FROM articulospedidos WHERE id_artpedido = $id_artpedido;

			";
                mysqli_query($con, $query);
                $id_artpedidonuevo = mysqli_insert_id();

                $cadena .= "UPDATE articulospedidos SET cant_band = cant_band - $cantidad, estado = IF(estado - $cantidad <= 0, 7, 5) WHERE id_artpedido = $id_artpedido;";

            } else if ($tipo_entrega == "completa") {
                $cadena .= "UPDATE articulospedidos SET estado = 7, fecha_entrega = '$fecha', id_pedido = $id_pedido, modoentrega = 'ENTREGA INMEDIATA COMPLETA' WHERE id_artpedido = $id_artpedido;";
            }

            //CREO PEDIDO NUEVO PARA EL NUEVO CLIENTE

            $consulta = "SELECT id_mesada,cantidad FROM ordenes_mesadas WHERE id_orden = (SELECT id_orden FROM ordenes_siembra WHERE ids_artpedido = $id_artpedido) ORDER BY cantidad DESC";
            $val = mysqli_query($con, $consulta);
            if (mysqli_num_rows($val) == 1) {
                $cadena .= "UPDATE ordenes_mesadas SET cantidad = cantidad - $cantidad, estado = 1 WHERE id_orden = (SELECT id_orden FROM ordenes_siembra WHERE id_artpedido = $id_artpedido);";
            } else if (mysqli_num_rows($val) > 1) {
                while ($re = mysqli_fetch_array($val)) {
                    $cantidadobtenida = (int) $re["cantidad"];
                    $mesada = $re["id_mesada"];
                    if ($cantidadobtenida > $cantidadnum) {
                        $cadena .= "UPDATE ordenes_mesadas SET cantidad = cantidad - $cantidad, estado = 1 WHERE id_orden = (SELECT id_orden FROM ordenes_siembra WHERE id_artpedido = $id_artpedido) AND id_mesada = $id_mesada;";
                        break;
                    } else {
                        $cadena .= "UPDATE ordenes_mesadas SET cantidad = 0, estado = 1 WHERE id_orden = (SELECT id_orden FROM ordenes_siembra WHERE id_artpedido = $id_artpedido) AND id_mesada = $mesada;";
                        $cantidadnum = $cantidadnum - $cantidadobtenida;
                    }
                }
            }

            $idart = $id_artpedido;
            if ($id_artpedidonuevo != null) {
                $idart = $id_artpedidonuevo;
            }
            $cadena .= "INSERT INTO entregas
		(cantidad, fecha, id_artpedido, id_remito, precio) VALUES
		($cantidad, NOW(), $idart, $id_remito, $precio);";

            if ($tipo_entrega == "completa") {
                $cadena .= "UPDATE ordenes_mesadas
				SET tipo = IF( (SELECT SUM(cantidad) FROM ordenes_mesadas WHERE id_orden = (SELECT id_orden FROM ordenes_siembra WHERE id_artpedido = $id_artpedido)) > 0, 1, 0) WHERE id_orden = (SELECT id_orden FROM ordenes_siembra WHERE id_artpedido = $id_artpedido);";
            }

        }
        if ($rowLength > 0) {
            mysqli_multi_query($con, $cadena);
        }

    } catch (Exception $e) {
        echo "ERROR:";
        echo $e;
    }

} else if ($consulta == "actualiza_filas") {
    $str = json_decode($_POST['jsonarray'], true);
    $errors = array();
    mysqli_autocommit($con, false);
    for ($i = 0; $i < count($str); $i++) {
        $id_art = $str[$i][0];
        $fecha = $str[$i][1];
        $fila = $str[$i][2];
        $query = "UPDATE articulospedidos SET fecha_entrega = $fecha, fila = $fila WHERE id_artpedido = $id_art;";
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
} else if ($consulta == "change_week") {
    $fecha = $_POST['nuevafecha'];
    $str = json_decode($_POST['jsonarray'], true);
    $errors = array();

    mysqli_autocommit($con, false);
    for ($i = 0; $i < count($str); $i++) {
        $id_art = $str[$i];
        $query = "UPDATE articulospedidos SET fecha_entrega = '$fecha', fila = NULL WHERE id_artpedido = $id_art;";
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
