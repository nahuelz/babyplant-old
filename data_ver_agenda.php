<?php
include "class_lib/sesionSecurity.php";
require 'class_lib/class_conecta_mysql.php';
require 'class_lib/funciones.php';
$consulta = $_POST['consulta'];
$con = mysqli_connect($host, $user, $password, $dbname);

if (!$con) {
    die("Connection failed: " . mysqli_connect_error());
}

mysqli_query($con, "SET NAMES 'utf8'");

if ($consulta == "busca_agenda") {
    $fecha = $_POST['fecha'];
    $fechanueva = explode("/", $fecha);
    $fechanueva = $fechanueva[2] . "-" . $fechanueva[1] . "-" . $fechanueva[0];
    $cadena = "SELECT pe.id_pedido as id_pe,o.id_orden as id_ordensiembra, o.id_orden_alternativa, p.id_artpedido, pe.fecha as fecha_pedido, p.id_pedido, v.id_articulo as id_variedad, t.id_articulo as id_tipo, t.nombre as nombre_tipo, s.nombre as nombre_subtipo, v.nombre as nombre_variedad, ae.cantidad, p.bandeja, ae.fecha as fecha_entrega, DATE_FORMAT(o.fecha_siembra, '%d/%m/%Y') as fecha_siembra,
    c.nombre as cliente, c.id_cliente, ae.id_remito,
    ae.estado, ae.modo_entrega, p.con_semilla, ae.telefono, ae.id_agenda, GROUP_CONCAT(DISTINCT(om.id_mesada) SEPARATOR ', ') as id_mesada
    FROM variedades_producto v
    INNER JOIN subtipos_producto s ON v.id_subtipo = s.id_articulo
    INNER JOIN tipos_producto t ON t.id_articulo = s.id_tipo
    INNER JOIN articulospedidos p ON p.id_articulo = v.id_articulo
    INNER JOIN pedidos pe ON p.id_pedido = pe.id_pedido
    INNER JOIN clientes c ON pe.id_cliente = c.id_cliente
    INNER JOIN ordenes_siembra o ON o.id_artpedido = p.id_artpedido
    LEFT JOIN ordenes_mesadas om ON om.id_orden = o.id_orden
    INNER JOIN agenda_entregas ae ON ae.id_artpedido = p.id_artpedido
    GROUP BY ae.id_artpedido
    HAVING ae.fecha = '$fechanueva' AND ae.estado IN (-1,0,1,2) ORDER BY o.id_orden DESC;";
    $val = mysqli_query($con, $cadena);

    if (mysqli_num_rows($val) > 0) {

        $array = array();

        while ($ww = mysqli_fetch_array($val)) {
            $id_cliente = $ww['id_cliente'];
            $id_pedido = $ww['id_orden'];
            $id_artpedido = $ww['id_artpedido'];
            $fecha = $ww['formatted_date'];
            $producto = $ww['nombre_tipo'] . " " . $ww['nombre_subtipo'] . " " . $ww['nombre_variedad'] . " x" . $ww['bandeja'];
            $cliente = $ww['cliente'];
            $cant_band = $ww['cantidad'];

            if ($ww['con_semilla'] == 1) {
                $producto .= " CON SEMILLA";
            }

            $id_orden = $ww['id_orden_alternativa'];
            $estado = generaBoxAgenda($ww["estado"], true);

            if ($ww["estado"] == 0) {
                $botones =
                    "<div class='d-flex flex-row justify-content-center'>
                    <button class='btn btn-primary fa fa-edit' onClick='editEntrega(this)'></button>
                    <button class='btn btn-danger fa fa-trash ml-2' onClick='eliminarEntrega($ww[id_agenda])'></button>
                </div>";
            } else if ($ww["estado"] == 1 && $ww["id_remito"] && strlen($ww["id_remito"] > 0)) {
                $botones = "
                <div class='text-center'>
                    <p class='text-primary'>REMITO $ww[id_remito]</p>
                    <div class='d-flex flex-row justify-content-center'>
                        <button class='btn btn-danger fa fa-trash' onClick='eliminarEntrega($ww[id_agenda], $ww[estado], \"$ww[id_remito]\")'></button>
                        <button class='btn btn-success fa fa-truck ml-2' onClick='marcarEntrega($ww[id_agenda], $ww[cantidad])'></button>
                    </div>

                </div>
                ";
            } else if ($ww["estado"] == 2) {
                $botones = "
                    <p class='text-primary text-center'>REMITO $ww[id_remito]</p>
                ";
            }

            echo "<tr x-id-agenda='$ww[id_agenda]' x-cantidad='$cant_band' x-id-cliente='$ww[id_cliente]' x-estado='$ww[estado]' x-id-artpedido='$ww[id_artpedido]' x-id-remito='$ww[id_remito]'>
          <td class='id-orden-entrega' onClick='toggleSelection(this)' id='art_$id_artpedido' style='text-align: center; cursor:pointer; color:#1F618D; font-weight:bold; font-size:1.2em;'>$id_orden</td>
          <td onClick='toggleSelection(this)' style='cursor:pointer;font-size:1.2em;'>$producto</td>
          <td class='td-cliente' onClick='toggleSelection(this)' style='cursor:pointer;font-size:1.2em'>$cliente</td>
          <td style='font-size:1.2em;font-weight:bold;text-align: center;'>$cant_band</td>
          <td style='font-size:1.0em;text-align: center;'>$ww[id_mesada]</td>
          <td style='text-align: center; word-wrap:break-word;'>$ww[modo_entrega]<br>Tel: $ww[telefono]</td>
          <td><div style='cursor:pointer' onClick='ModalMarcarEntrega($ww[id_artpedido], this)' id='entrega_$ww[id_artpedido]'>$estado</div></td>
          <td>
            $botones
          </td>
        </tr>";

            array_push($array, $id_pedido);
        }

    } else {
        echo "
      <tr>
        <td colspan='8' class='text-center pt-4 pb-4'>
          <h4 class='text-muted'>No hay entregas agendadas para el día seleccionado</h4>
        </td>
      </tr>

    ";
    }
} else if ($consulta == "eliminar_entrega") {
    $id_agenda = $_POST["id_agenda"];
    $id_remito = $_POST["id_remito"];
    $estado = $_POST["estado"];

    if ($estado == 0 || $estado == "0") {
        $query = "DELETE FROM agenda_entregas WHERE id_agenda = $id_agenda;";
        if (mysqli_query($con, $query)) {
            echo "success";
        } else {
            print_r(mysqli_error($con));
        }
    } else if ($estado == 1 || $estado == "1") {
        mysqli_autocommit($con, false);
        $errors = array();
        $query = "UPDATE agenda_entregas SET id_remito = NULL, estado = 0 WHERE id_remito = $id_remito;";
        if (!mysqli_query($con, $query)) {
            $errors[] = mysqli_error($con);
        }
        $query = "DELETE FROM remitos WHERE id_remito = $id_remito;";
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
} else if ($consulta == "edita_entrega") {
    $id_agenda = $_POST["id_agenda"];
    $cantidad = $_POST["cantidad"];
    $fecha = $_POST["fecha"];
    $fechanueva = explode("/", $fecha);
    $fechanueva = $fechanueva[2] . "-" . $fechanueva[1] . "-" . $fechanueva[0];

    $query = "UPDATE agenda_entregas SET fecha = '$fechanueva', cantidad = '$cantidad' WHERE id_agenda = $id_agenda;";
    if (mysqli_query($con, $query)) {
        echo "success";
    } else {
        print_r($query);
        print_r(mysqli_error($con));
    }
} else if ($consulta == "cargar_dataremito") {
    $id_cliente = $_POST['id_cliente'];

    $cadena = "
		SELECT t1.telefono as telefono, t2.auto_increment as auto_increment FROM (SELECT telefono FROM clientes WHERE id_cliente = $id_cliente) as t1,
		(SELECT `auto_increment` FROM INFORMATION_SCHEMA.TABLES
			WHERE table_name = 'remitos') as t2
	";
    $val = mysqli_query($con, $cadena);

    if (mysqli_num_rows($val) > 0) {
        $re = mysqli_fetch_assoc($val);
        $array = array(
            "telefono" => $re["telefono"],
            "id_remito" => $re["auto_increment"],
        );
        echo json_encode($array);
    }

} else if ($consulta == "generar_remito") {
    $id_cliente = $_POST['id_cliente'];
    $id_orden = $_POST["id_orden"];
    $cadena = "SELECT SUM(monto) as suma FROM pagos WHERE id_pedido = (SELECT id_pedido FROM articulospedidos WHERE id_artpedido = (SELECT id_artpedido FROM ordenes_siembra WHERE id_orden = $id_orden))";
    $array = array();
    $val = mysqli_query($con, $cadena);
    if (mysqli_num_rows($val) > 0) {
        while ($re = mysqli_fetch_array($val)) {
            if (!is_null($re['suma'])) {
                array_push($array, [$re['suma']]);
            }
        }
    }
    $cadena = "SELECT cuit, telefono FROM clientes WHERE id_cliente = $id_cliente";
    $val = mysqli_query($con, $cadena);
    if (mysqli_num_rows($val) > 0) {
        while ($re = mysqli_fetch_array($val)) {
            array_push($array, [$re['cuit'], $re['telefono']]);
        }
    }
    echo json_encode($array);
} else if ($consulta == "buscar_parciales") {
    $id_cliente = $_POST["id_cliente"];
    $cadena = "
    SELECT
		o.id_orden_alternativa,
        p.id_artpedido,
        o.cant_band_reales,
		p.fecha_entrega,
        t.nombre as nombre_tipo,
        s.nombre as nombre_subtipo,
        v.nombre as nombre_variedad,
        (o.cant_band_reales - IFNULL(SUM(en.cantidad), 0)) as cantirestante,
		 p.bandeja,
         p.con_semilla,
         p.estado,
         STR_TO_DATE(p.fecha_entrega, '%d/%m/%Y') as fecha_entrega_date,
		  	GROUP_CONCAT(DISTINCT(om.id_mesada) SEPARATOR ', ') as id_mesada
			FROM variedades_producto v
			INNER JOIN subtipos_producto s ON v.id_subtipo = s.id_articulo
			INNER JOIN tipos_producto t ON t.id_articulo = s.id_tipo
			INNER JOIN articulospedidos p ON p.id_articulo = v.id_articulo
			LEFT JOIN entregas en ON p.id_artpedido = en.id_artpedido
			INNER JOIN pedidos pe ON pe.ID_PEDIDO = p.id_pedido
            INNER JOIN ordenes_siembra o ON o.id_artpedido = p.id_artpedido
			LEFT JOIN ordenes_mesadas om ON om.id_orden = o.id_orden
			WHERE pe.id_cliente = $id_cliente AND p.estado IN (4,5,6) GROUP BY p.id_artpedido ORDER BY fecha_entrega_date ASC
			";
    $val = mysqli_query($con, $cadena);
    $salida = "";

    if (mysqli_num_rows($val) > 0) {
        while ($re = mysqli_fetch_array($val)) {
            $producto = "$re[nombre_tipo] $re[nombre_subtipo] $re[nombre_variedad] x$re[bandeja]";
            if ($re['con_semilla'] == 1) {
                $producto .= " CON SEMILLA";
            }
            $estado = generarBoxEstado($re["estado"], true);

            $probando = explode(", ", $re["id_mesada"]);
            $id_mesada = array_unique($probando);
            $id_mesada = implode(", ", $id_mesada);

            $cantirestante = $re["cantirestante"] < 0 ? 0 : $re["cantirestante"];
            $id_art = $re['id_artpedido'];
            $salida .= "<tr onClick='seleccionTabla(this)' x-id-artpedido='$re[id_artpedido]' id='art_$re[id_artpedido]' x-estado='$re[estado]'
                        style='cursor:pointer'>
								<td style='font-size:16px;font-weight:bold;text-align:center;'>$re[id_orden_alternativa]</td>
								<td style='word-wrap:break-word;'>$producto</td>
								<td style='text-align:center;font-size:18px'>$cantirestante</td>
								<td style='text-align:center;font-size:16px'>$re[cant_band_reales]</td>
								<td style='text-align:center;font-size:16px'>$re[fecha_entrega]</td>
								<td style='text-align:center;font-size:16px;font-weight:bold;'>$id_mesada</td>
								<td id='art_$re[id_artpedido]'>$estado</td>
							</tr>";
        }
        echo $salida;
    }
} else if ($consulta == "marcar_entrega") {
    $id_agenda = $_POST["id_agenda"];
    
    $id_artpedido = null;
    $val = mysqli_query($con, "SELECT id_artpedido FROM agenda_entregas WHERE id_agenda = $id_agenda");
    if ($val && mysqli_num_rows($val) > 0) {
        $id_artpedido = mysqli_fetch_assoc($val)["id_artpedido"];
        $cadena = "SELECT id_mesada,cantidad FROM ordenes_mesadas WHERE cantidad > 0 AND id_orden = (SELECT id_orden FROM ordenes_siembra WHERE id_artpedido = $id_artpedido)";
        $val = mysqli_query($con, $cadena);
        $errors = array();
        if (mysqli_num_rows($val) == 1) {
            mysqli_autocommit($con, false);

            $query = "UPDATE ordenes_mesadas SET cantidad = cantidad - (SELECT cantidad FROM agenda_entregas WHERE id_agenda = $id_agenda) WHERE id_orden = (SELECT id_orden FROM ordenes_siembra WHERE id_artpedido = $id_artpedido);";
            if (!mysqli_query($con, $query)) {
                $errors[] = mysqli_error($con) . "-" . $query;
            }
            $query = "UPDATE agenda_entregas SET estado = 2 WHERE id_agenda = $id_agenda;";
            if (!mysqli_query($con, $query)) {
                $errors[] = mysqli_error($con) . "-" . $query;
            }
            $query = "INSERT INTO entregas (
                cantidad,
                fecha,
                id_artpedido,
                id_remito
            ) VALUES (
                (SELECT cantidad FROM agenda_entregas WHERE id_agenda = $id_agenda),
                NOW(),
                $id_artpedido,
                (SELECT id_remito FROM agenda_entregas WHERE id_agenda = $id_agenda)
            );";
            if (!mysqli_query($con, $query)) {
                $errors[] = mysqli_error($con) . "-" . $query;
            }
            $query = "UPDATE articulospedidos
		SET estado = IF(IFNULL((SELECT SUM(cantidad) FROM entregas WHERE id_artpedido = $id_artpedido),0) >= ( SELECT * FROM (SELECT cant_band FROM articulospedidos WHERE id_artpedido = $id_artpedido) t1), 7, 6)
		WHERE id_artpedido = $id_artpedido;
	    ";
            if (!mysqli_query($con, $query)) {
                $errors[] = mysqli_error($con) . "-" . $query;
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
        } else if (mysqli_num_rows($val) > 1) {
            mysqli_autocommit($con, false);
            $cantidadnum = (int)$_POST["cantidad"];
            while ($re = mysqli_fetch_array($val)) {
                $cantidadobtenida = (int) $re["cantidad"];
                $mesada = $re["id_mesada"];
                if ($cantidadobtenida > $cantidadnum) {
                    $query = "UPDATE ordenes_mesadas SET cantidad = cantidad - $cantidadnum WHERE id_orden = (SELECT id_orden FROM ordenes_siembra WHERE id_artpedido = $id_artpedido) AND id_mesada = $mesada;";
                    if (!mysqli_query($con, $query)) {
                        $errors[] = mysqli_error($con);
                    }
                    break;
                } else {
                    $query = "UPDATE ordenes_mesadas SET cantidad = 0 WHERE id_orden = (SELECT id_orden FROM ordenes_siembra WHERE id_artpedido = $id_artpedido) AND id_mesada = $mesada;";
                    if (!mysqli_query($con, $query)) {
                        $errors[] = mysqli_error($con);
                    }
                    $cantidadnum = $cantidadnum - $cantidadobtenida;
                }
            }

            $query = "UPDATE agenda_entregas SET estado = 2 WHERE id_agenda = $id_agenda;";
            if (!mysqli_query($con, $query)) {
                $errors[] = mysqli_error($con) . "-" . $query;
            }
            $query = "INSERT INTO entregas (
                cantidad,
                fecha,
                id_artpedido,
                id_remito
            ) VALUES (
                (SELECT cantidad FROM agenda_entregas WHERE id_agenda = $id_agenda),
                NOW(),
                $id_artpedido,
                (SELECT id_remito FROM agenda_entregas WHERE id_agenda = $id_agenda)
            );";
            if (!mysqli_query($con, $query)) {
                $errors[] = mysqli_error($con) . "-" . $query;
            }
            $query = "UPDATE articulospedidos
		SET estado = IF(IFNULL((SELECT SUM(cantidad) FROM entregas WHERE id_artpedido = $id_artpedido),0) >= ( SELECT * FROM (SELECT cant_band FROM articulospedidos WHERE id_artpedido = $id_artpedido) t1), 7, 6)
		WHERE id_artpedido = $id_artpedido;
	    ";
            if (!mysqli_query($con, $query)) {
                $errors[] = mysqli_error($con) . "-" . $query;
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
} else if ($consulta == "generar_orden_entrega") {
    $str = json_decode($_POST['jsonarray'], true);
    $rowLength = count($str);
    $lista = implode(", ", $str);
    if ($rowLength > 0) {
        $cadena = "SELECT o.id_orden_alternativa, p.id_artpedido, p.con_semilla,
		    t.nombre as nombre_tipo,
            ae.cantidad as cantidad_agenda,
            ae.id_agenda,
			s.nombre as nombre_subtipo, v.nombre as nombre_variedad, (o.cant_band_reales - IFNULL(SUM(en.cantidad), 0)) as cantirestante, o.cant_band_reales, p.bandeja,
			c.nombre as cliente, c.id_cliente, p.estado, GROUP_CONCAT(DISTINCT(om.id_mesada) SEPARATOR ', ') as id_mesada,
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
			INNER JOIN ordenes_siembra o ON o.id_artpedido = p.id_artpedido
			LEFT JOIN ordenes_mesadas om ON o.id_orden = om.id_orden
            INNER JOIN agenda_entregas ae ON ae.id_artpedido = p.id_artpedido
			WHERE ae.id_agenda IN ($lista) GROUP BY ae.id_agenda";
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
                $salida .= "<tr x-id-agenda='$re[id_agenda]' x-id-artpedido='$re[id_artpedido]' x-cantidad-agenda='$re[cantidad_agenda]' x-cantidad-restante='$re[cantirestante]'>
								<td id='art_$re[id_artpedido]' style='font-size:1.2em;font-weight:bold;text-align:center;' class='id_artpedidos'>$re[id_orden_alternativa]</td>
								<td style='word-wrap:break-word;'>$producto</td>
								<td class='text-danger' style='font-weight:bold;text-align:center;font-size:1.3em'>$re[cantirestante]</td>
								<td style='text-align:center;font-size:1.3em'>$re[cant_band_reales]</td>
								<td style='font-size:1.2em;font-weight:bold;text-align:center;word-wrap:break-word;'>$re[id_mesada]</td>
								<td class='text-center' style='word-wrap:break-word;'>
								<input
								onkeyup='calcularSubtotal()' onpaste='calcularSubtotal()'
								style='font-size: 1.3em;' class='preciobox text-center text-success font-weight-bold form-control' type='number' min='0' value='$precio' max='999999'> </td>
								<td style='text-align:center;font-size:1.3em'>
                                    $re[cantidad_agenda]
                                </td>
							</tr>";
            }
            echo $salida;
        }
    }
} else if ($consulta == "guarda_remito") {
    $str = json_decode($_POST['jsonarray'], true);
    $descuento = $_POST["descuento"];
    $tipodescuento = $_POST["tipodescuento"];
    $subtotal = $_POST["subtotal"];
    $id_cliente = $_POST["id_cliente"];
    $codigo = $_POST["codigo"];
    $errors = array();

    $query = "INSERT INTO remitos (id_cliente) VALUES ($id_cliente);";
    if (mysqli_query($con, $query)) {
        $id_remito = mysqli_insert_id($con);
        if ($id_remito && (int) $id_remito > 0) {
            mysqli_autocommit($con, false);

            $query = "UPDATE remitos SET codigo = '$codigo', fecha = NOW(), total = '$subtotal', descuento = '$descuento' WHERE id_remito = $id_remito;";
            if (!mysqli_query($con, $query)) {
                $errors[] = mysqli_error($con);
            }

            for ($i = 0; $i < count($str); $i++) {
                $id_agenda = $str[$i]["id_agenda"];
                $query = "UPDATE agenda_entregas SET estado = 1, id_remito = $id_remito WHERE id_agenda = $id_agenda;";
                if (!mysqli_query($con, $query)) {
                    $errors[] = mysqli_error($con);
                }
            }

            if (count($errors) === 0) {
                if (mysqli_commit($con)) {
                    echo "success";
                } else {
                    mysqli_rollback($con);
                    mysqli_autocommit($con, true);
                    mysqli_query($con, "DELETE FROM remitos WHERE id_remito = $id_remito");
                }
            } else {
                mysqli_rollback($con);
                print_r($errors);
                mysqli_autocommit($con, true);
                mysqli_query($con, "DELETE FROM remitos WHERE id_remito = $id_remito");
            }
            mysqli_close($con);
        } else {
            echo "Error al guardar los datos";
        }
    } else {
        print_r(mysqli_error($con));
    }
}

function generaBoxAgenda($estado, $fullWidth)
{
    $w100 = "";
    if ($fullWidth == true) {
        $w100 = "w-100";
    }
    if ($estado == 0) { //PENDIENTE
        return "<div class='d-inline-block cajita $w100' style='background-color:#A4A4A4; padding:5px;'>ENTREGA PENDIENTE</div>";
    } else if ($estado == 1) { //REMITO GENERADO
        return "<div class='d-inline-block cajita $w100' style='text-align:center;background-color:#58D3F7; padding:3px; cursor:pointer;'><div>REMITO GENERADO</div></div>";
    } else if ($estado == 2) { //ENTREGADO COMPLETAMENTE
        return "<div class='d-inline-block cajita $w100' style='text-align:center;background-color:#A9F5BC; padding:3px; cursor:pointer;'><div>ENTREGADO</div></div>";
    } else if ($estado == -1) { //CANCELADO
        return "<div class='d-inline-block cajita $w100' style='word-wrap:break-word;text-align:center;background-color:#FA5858; padding:3px; cursor:pointer;'>ENTREGA CANCELADA</div>";
    } else {
        return "<div class='d-inline-block cajita $w100' style='background-color:#A4A4A4; padding:5px;'>NO DEFINIDO</div>";
    }
}
