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

$str = json_decode($_POST['jsonarray'], true);
$rowLength = count($str);

if ($consulta == "cargar_ordenes") {
    $fecha = $_POST['fecha'];
    $arraycolumnas = [];
    $fila = [];
    $cadenaselect = "SELECT o.id_orden, o.id_orden_alternativa, o.id_artpedido, t.nombre as nombre_tipo,
	 s.nombre as nombre_subtipo, v.nombre as nombre_variedad, a.fecha_planificacion, a.id_artpedido, v.id_articulo as id_variedad,
	 a.bandeja, a.estado, a.fila_siembra, a.cant_band, a.cant_plantas, a.cant_semi,
	 UPPER(a.cod_sobre) as cod_sobre, c.nombre as nombre_cliente, o.obsiembra
	 FROM ordenes_siembra o
	 LEFT JOIN articulospedidos a ON o.id_artpedido = a.id_artpedido
	 INNER JOIN variedades_producto v ON v.id_articulo = a.id_articulo
	 INNER JOIN subtipos_producto s ON s.id_articulo = v.id_subtipo
	 INNER JOIN tipos_producto t ON t.id_articulo = s.id_tipo
	 INNER JOIN pedidos p ON p.id_pedido = a.id_pedido
	 INNER JOIN clientes c ON c.id_cliente = p.id_cliente
	 WHERE a.fecha_planificacion = '$fecha' AND a.estado > 0
	 ORDER BY a.estado ASC, nombre_tipo ASC, o.id_orden_alternativa DESC;";
    $val = mysqli_query($con, $cadenaselect);
    $salida = "";
    $stringproducto = "";
    if (mysqli_num_rows($val) > 0) {
        while ($re = mysqli_fetch_array($val)) {
            $estado = "";
            if ($re['estado'] == 1) {
                $estado = "<div id='estado_" . $re['id_artpedido'] . "' onClick='cambiarEstadoSiembra(this.id);' class='cajita' style='background-color:#FFFF00; padding:5px; cursor:pointer;font-size:0.8em;font-weight:bold;'><span>PLANIFICADO</span></div>";
            } else if ($re['estado'] == 2) {
                $estado = "<div id='estado_" . $re['id_artpedido'] . "' onClick='cambiarEstadoSiembra(this.id);' class='cajita' style='background-color:#74DF00; padding:5px; cursor:pointer;font-size:0.8em;font-weight:bold;'><span>SEMBRADO</span></div>";
            } else if ($re['estado'] > 2) {
                $estado = "<div class='cajita' id='estado_" . $re['id_artpedido'] . "' style='background-color:#A4A4A4; padding:5px; cursor:pointer;font-size:0.8em;font-weight:bold;'><span>ETAPAS POSTERIORES</span></div>";
            }
            $tipoproducto = "$re[nombre_tipo]";
            $producto = "$re[nombre_subtipo] $re[nombre_variedad]";
            $color = "";
            if (strpos($tipoproducto, "TOMATE") !== false) {
                $color = "#FFACAC";
            } else if (strpos($tipoproducto, "PIMIENTO") !== false) {
                $color = "#BAE1A2";
            } else if (strpos($tipoproducto, "BERENJENA") !== false) {
                $color = "#D5B4FF";
            } else if (strpos($tipoproducto, "LECHUGA") !== false) {
                $color = "#7BFF00";
            } else if (strpos($tipoproducto, "ACELGA") !== false) {
                $color = "#BFDCBC";
            } else if (strpos($tipoproducto, "REMOLACHA") !== false) {
                $color = "#eba5b5";
            } else if (strpos($tipoproducto, "COLES") !== false || strpos($tipoproducto, "HINOJO") !== false || strpos($tipoproducto, "APIO") !== false) {
                $color = "#58ACFA";
            } else if (strpos($tipoproducto, "VERDEO") !== false || strpos($tipoproducto, "PUERRO") !== false) {
                $color = "#F7BE81";
            } else {
                $color = "#A4A4A4";
            }

            $tipo = "";
            $id_orden = $re['id_orden_alternativa'];
            if ($id_orden != null) {
                $tipo = strtoupper(substr($re["nombre_tipo"], 0, 3));
            }

            $sobre = $re['cod_sobre'];

            if (is_null($sobre)) {
                $sobre = "NO ASIGNADO";
            }

            $observaciones = $re["obsiembra"];
            if (is_null($observaciones) || strlen(trim($observaciones)) == 0) {
                $observaciones = "<button class='btn btn-success btn-round fa fa-plus-square' onClick='MostrarModalOrden(\"orden_$re[id_orden]\", \"$re[id_orden_alternativa]\", $re[id_artpedido])'></button>";
            } else {
                $observaciones = "<div style='padding:3px;cursor:pointer' onClick='MostrarModalOrden(\"orden_$re[id_orden]\", \"$re[id_orden_alternativa]\", $re[id_artpedido])'>$re[obsiembra]</div>";
            }

            $salida .= "<tr >
				<td id='orden_$re[id_orden]' style='text-align: center; cursor:pointer;  font-weight:bold' onClick='MostrarModalOrden(this.id, $(this).text(), $re[id_artpedido])'><span style='font-size:1.6em;'>$id_orden</span><br><span style='color: blue;font-size:1.2em'> $tipo</span>
				</td>
				<td class='variedad_$re[id_variedad]' style='cursor:pointer;background-color: $color; font-weight:bold; font-size:1.3em; word-wrap:break-word;' onClick='toggleSelection(this)'>$producto</td>
				<td class='band-$re[bandeja]' style='background-color:white;font-weight:bold; font-size:1.5em; word-wrap:break-word;'>x$re[bandeja]</td>
				<td style='word-wrap: break-word;font-size:1.0em;'>$re[nombre_cliente]</td>
				<td style='word-wrap: break-word;font-size:1.4em;font-weight:bold;'>$re[cant_band]</td>
				<td style='word-wrap: break-word;font-size:1.0em;font-weight:bold;'>$re[cant_semi]</td>
				<td style='word-wrap: break-word;font-size:1.0em;font-weight:bold;'>$re[cant_plantas]</td>
				<td style='word-wrap: break-word;font-weight:bold; font-size:1.2em'>$sobre</td>
				<td style='word-wrap: break-word;font-size:1.0em'>$observaciones</td>
				<td>$estado</td>
				</tr>";
        }
        echo $salida;
    }
} else if ($consulta == "cargar_orden_especifica") {
    $id_orden = $_POST['id_orden'];
    $cadena = "SELECT o.id_orden, o.id_artpedido, t.nombre as nombre_tipo, s.nombre as nombre_subtipo, v.nombre as nombre_variedad, o.fecha, a.id_artpedido, a.bandeja, a.estado, a.fila_siembra, a.cant_band, o.id_orden_alternativa, a.cant_plantas, a.cant_semi, UPPER(a.cod_sobre) as cod_sobre, TRIM(o.obsiembra) as obsiembra  FROM ordenes_siembra o LEFT JOIN  articulospedidos a ON o.id_artpedido = a.id_artpedido INNER JOIN variedades_producto v ON v.id_articulo = a.id_articulo INNER JOIN subtipos_producto s ON s.id_articulo = v.id_subtipo INNER JOIN tipos_producto t ON t.id_articulo = s.id_tipo WHERE o.id_orden = $id_orden ORDER BY nombre_tipo, nombre_subtipo, nombre_variedad, o.id_orden";
    $val = mysqli_query($con, $cadena);
    $salida = "";
    if (mysqli_num_rows($val) > 0) {
        while ($re = mysqli_fetch_array($val)) {
            $producto = "$re[nombre_subtipo] $re[nombre_variedad] x$re[bandeja]";
            if ($re['con_semilla'] == 1) {
                $producto .= " CON SEMILLA";
            } else {
                $producto .= " SIN SEMILLA";
            }
            $sobre = $re['cod_sobre'];
            if (is_null($sobre)) {
                $sobre = "NO ASIGNADO";
            }

            $tipo = "";
            $id_orden = $re['id_orden_alternativa'];
            if ($id_orden != null) {
                $tipo = strtoupper(substr($re["nombre_tipo"], 0, 3));
            }

            $estado = generarBoxEstado($re["estado"], false);

            $salida .= "<tr >
				  <td id='orden_$re[id_orden]' style='text-align: center; cursor:pointer;  font-weight:bold' onClick='MostrarModalOrden(this.id, $(this).text())'><span style='font-size:1.3em;'>$id_orden</span><br><span style='color: grey;font-size:0.8em'> $tipo</span>
				  </td>
				  <td style='font-size:1.2em'>$producto</td>
				  <td style='font-size:1.4em;font-weight:bold'>$re[cant_band]</td>
				  <td style='font-size:1.4em'>$re[cant_plantas]</td>
				  <td style='font-size:1.4em'>$re[cant_semi]</td>
				  <td style='font-size:1.4em;font-weight:bold'>$sobre</td>
				  <td style='font-size:1.0em;font-weight:bold'>

				  <div>
					<textarea class='form-control' name='observaciones_txt' disabled='true' id='observaciones_txt' type='text' onkeypress='if (this.value.length > 100) { return false; }' style='height:100px;text-transform:uppercase;resize:none'>$re[obsiembra]
					</textarea>
					<br>
					<button class='btn btn-primary btn-sm' style='font-size:14px' id='btn_modificar' onclick='ActivarText();'><i class='fa fa-edit'></i>  Modificar</button>
					<button class='btn btn-success btn-sm' style='font-size:14px' id='btn_guardarobs' disabled='true' onclick='GuardarObservaciones();'><i class='fa fa-save'></i>  Guardar</button>
				</div>

				  </td>
				  <td>$estado</td>
				</tr>";
        }
        echo $salida;
    }
} else if ($consulta == "check_anteriores") {
    $cadenaselect = 'SELECT o.id_orden_alternativa, t.nombre,STR_TO_DATE(a.fecha_planificacion, "%d/%m/%Y") AS formatted_date, a.fecha_planificacion, a.estado
	FROM ordenes_siembra o INNER JOIN articulospedidos a ON o.id_artpedido = a.id_artpedido INNER JOIN variedades_producto v ON v.id_articulo = a.id_articulo
	INNER JOIN subtipos_producto s ON s.id_articulo = v.id_subtipo INNER JOIN tipos_producto t ON t.id_articulo = s.id_tipo
	 HAVING formatted_date < CURDATE() AND  a.estado = 1 ORDER BY t.nombre, o.id_orden_alternativa;';
    $salida = "";
    $val = mysqli_query($con, $cadenaselect);
    if (mysqli_num_rows($val) > 0) {
        $salida .= "ATENCIÓN! Hay Ordenes de días anteriores que no fueron sembradas:\n\n";
        while ($re = mysqli_fetch_array($val)) {
            $salida .= ("\t• Orden: $re[id_orden_alternativa] | $re[nombre] | Fecha: $re[fecha_planificacion]\n");
        }
        echo $salida;
    } 
} else if ($consulta == "actualiza_a_siembra") {
    $id_art = $_POST['id_artpedido'];
    $fecha = $_POST['fecha'];
    $bandejas = $_POST['bandejas'];
    try {
		$errors = array();
		mysqli_autocommit($con, false);
        $query = "UPDATE articulospedidos SET estado = 2, fila = NULL WHERE id_artpedido = $id_art;";
        if (!mysqli_query($con, $query)) {
			$errors[] = mysqli_error($con);
		}
        $query = "UPDATE ordenes_siembra SET fecha_siembra = '$fecha', cant_band_reales = $bandejas WHERE id_artpedido = $id_art;";
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
    } catch (\Throwable $th) {
        throw $th;
    }

} else if ($consulta == "sembrar_varios") {
    $fecha = $_POST['fecha'];
    try {
		$errors = array();
        mysqli_autocommit($con, false);
        for ($i = 0; $i < count($str); $i++) {
            $id_orden = $str[$i]["idart"];
            $cantidad = $str[$i]["cantidad"];
            $query = "UPDATE articulospedidos SET estado = 2, fila = NULL WHERE id_artpedido = (SELECT id_artpedido FROM ordenes_siembra WHERE id_orden = $id_orden);";
            if (!mysqli_query($con, $query)) {
				$errors[] = mysqli_error($con);
			}
            $query = "UPDATE ordenes_siembra SET fecha_siembra = '$fecha', cant_band_reales = $cantidad WHERE id_orden = $id_orden;";
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
    } catch (\Throwable $th) {
        throw $th;
    }
} else if ($consulta == "cambiocantidadsiembra") {
    $id_orden = $_POST['id_orden'];
    $cantidad = $_POST['cantidad'];
    $query = "UPDATE ordenes_siembra SET cant_band_reales = $cantidad WHERE id_orden = $id_orden;";
    if (mysqli_query($con, $query)){
		echo "success";
	}
	else{
		print_r(mysqli_error($con));
	}
}
