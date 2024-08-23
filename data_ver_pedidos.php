<?php
include "class_lib/sesionSecurity.php";
require 'class_lib/class_conecta_mysql.php';
require 'class_lib/funciones.php';

$con = mysqli_connect($host, $user, $password, $dbname);
if (!$con) {
    die("Connection failed: " . mysqli_connect_error());
}
mysqli_query($con, "SET NAMES 'utf8'");

$consulta = $_POST["consulta"];

if ($consulta == "busca_pedidos") {
    $fechai = $_POST['fechai'];
    $fechaf = $_POST['fechaf'];
    $fechai = str_replace("/", "-", $fechai);
    $fechaf = str_replace("/", "-", $fechaf);

    if (strlen($fechai) == 0) {
        $fechai = (string) date('y-m-d', strtotime("first day of -3 month"));
    }
    if (strlen($fechaf) == 0) {
        $fechaf = "NOW()";
    }

    $filtros = json_decode($_POST['filtros'], true);

    $cadena = "SELECT
  pe.id_pedido as id_pe,
  p.id_artpedido,
  pe.fecha as fecha_pedido,
  DATE_FORMAT(pe.fecha, '%d/%m/%Y') AS formatted_date,
  DATE_FORMAT(pe.fecha_real, '%d/%m/%Y %H:%i') AS fechafull,
  p.id_pedido,
  v.id_articulo as id_variedad,
  t.id_articulo as id_tipo,
  t.nombre as nombre_tipo,
  s.nombre as nombre_subtipo,
  v.nombre as nombre_variedad,
  p.cant_plantas,
  p.cant_semi,
   p.cant_band, p.bandeja, p.fecha_entrega_original, p.fecha_siembraestimada,
   c.nombre as cliente,
   c.id_cliente,
   p.estado,
   o.id_orden_alternativa,
   p.revision,
   p.solucion,
   GROUP_CONCAT(DISTINCT(om.id_mesada) SEPARATOR ', ') as id_mesada
  FROM variedades_producto v
  INNER JOIN subtipos_producto s ON v.id_subtipo = s.id_articulo
  INNER JOIN tipos_producto t ON t.id_articulo = s.id_tipo
  INNER JOIN articulospedidos p ON p.id_articulo = v.id_articulo
  INNER JOIN pedidos pe ON p.id_pedido = pe.id_pedido
  INNER JOIN clientes c ON pe.id_cliente = c.id_cliente
  LEFT JOIN ordenes_siembra o ON o.id_artpedido = p.id_artpedido
  LEFT JOIN ordenes_mesadas om ON o.id_orden = om.id_orden
  GROUP BY p.id_artpedido
  HAVING fecha_pedido >= '$fechai' AND ";
    if ($fechaf == "NOW()") {
        $cadena .= "fecha_pedido <= NOW() ";
    } else {
        $cadena .= " fecha_pedido <= '$fechaf' ";
    }

    if ($filtros["tipo"] != null) {
        $cadena .= " AND id_tipo IN " . $filtros["tipo"] . " ";
    }

    if ($filtros["subtipo"] != null) {
        $cadena .= " AND nombre_subtipo REGEXP '" . $filtros["subtipo"] . "' ";
    }

    if ($filtros["variedad"] != null) {
        $cadena .= " AND nombre_variedad REGEXP '" . $filtros["variedad"] . "' ";
    }

    if ($filtros["cliente"] != null) {
        $cadena .= " AND cliente REGEXP '" . $filtros["cliente"] . "' ";
    }

    if ($filtros["estado"] != null) {
        $cadena .= " AND estado IN " . $filtros["estado"] . " ";
    }

    if ($filtros["revision"] != null && $filtros["solucion"] == null) {
        if ($filtros["revision"] > 0) {
            $cadena .= " AND revision = " . $filtros["revision"] . " ";
        }

        $cadena .= " AND solucion IS NULL ";
    } else if ($filtros["solucion"] != null) {
        if ($filtros["solucion"] > 0) {
            $cadena .= " AND solucion = " . $filtros["solucion"] . " ";
        }
    }

    $cadena .= " ORDER BY id_pe DESC;";

    $val = mysqli_query($con, $cadena);
    if (mysqli_num_rows($val) > 0) {
        echo "<div class='box box-primary'>";
        echo "<div class='box-header with-border'>";
        echo "<h3 class='box-title'>Pedidos</h3>";
        echo "</div>";
        echo "<div class='box-body'>";
        echo "<table id='tabla' class='table table-responsive w-100 d-block d-md-table'>";
        echo "<thead>";
        echo "<tr>";
        echo "<th>Ped</th><th>Fecha</th><th>Producto</th><th>Cliente</th><th>Cantidad<br>Pedida</th><th>Fecha Siembra</th><th>Fecha Entrega</th><th>Estado</th><th>Orden<br>Siembra</th><th>Mesada</th>";
        echo "</tr>";
        echo "</thead>";
        echo "<tbody>";
        $array = array();

        $tipos_revision = ["", "FALLA GERMINACIÓN", "GOLPE", "PAJARO", "RATA", "REALIZAR DESPUNTE", "USO PARA INJERTO", "D1 REALIZADO", "VER OBSERV."];
        $tipos_solucion = ["", "D1 CANCELADO", "CLASIFICACIÓN", "REPIQUE", "RESIEMBRA", "DEJAR FALLAS 12"];
        while ($ww = mysqli_fetch_array($val)) {
            $id_cliente = $ww['id_cliente'];
            $id_pedido = $ww['id_pedido'];
            $id_artpedido = $ww['id_artpedido'];
            $fecha = $ww['formatted_date'];
            $fecha_pedido = explode("/", $fecha);
            $tipo = "";
            $id_orden = $ww['id_orden_alternativa'];
            if ($id_orden != null) {
                $tipo = strtoupper(substr($ww["nombre_tipo"], 0, 3));
            }

            $fecha_pedido = $fecha_pedido[2] . "/" . $fecha_pedido[1] . "/" . $fecha_pedido[0];
            $producto = "<span class='hidden'>" . $ww['nombre_tipo'] . "</span> " . $ww['nombre_subtipo'] . " " . $ww['nombre_variedad'] . " x" . $ww['bandeja'];

            if ($ww["revision"] != null && $ww["solucion"] == null) {
                $producto .= " [" . $tipos_revision[$ww["revision"]] . "]";
            } else if ($ww["revision"] != null && $ww["solucion"] != null) {
                $producto .= " [" . $tipos_revision[$ww["revision"]] . "] [" . $tipos_solucion[$ww["solucion"]] . "]";
            }

            $cliente = $ww['cliente'];
            $cant_band = $ww['cant_band'];
            $fecha_siembra = $ww['fecha_siembraestimada'];
            $fecha_entrega = $ww['fecha_entrega_original'];

            $fechafull = $ww["fechafull"];
            $estado = generarBoxEstado($ww["estado"], true);

            $fondo = "";
            if ($ww["revision"] != null && $ww["solucion"] == null) {
                $fondo = "background-color:#F7D358;";
            }

            if ($ww["solucion"] != null) {
                $fondo = "background-color:#A9F5A9;";
            }

            echo "<tr x-estado='$ww[estado]'>";

            if (in_array($ww['id_pedido'], $array)) {

                echo "<td onClick='modalEstadoPedido($id_pedido,  \"$fechafull\");' style='text-align: center; cursor:pointer; color:#1F618D;font-size:0.7em;$fondo'>$id_pedido</td>";

                echo "<td style='text-align: center;$fondo'><span style='display:none;'>" . str_replace("/", "", $fecha_pedido) . "</span><span style='display:none'" . str_replace("/20", "/", $fecha) . "</span></td>";

                echo "<td style='$fondo'>$producto</td>";

                echo "<td style='$fondo'><span style='display:none'>$cliente</span></td>";

            } else {

                echo "<td onClick='modalEstadoPedido($id_pedido,  \"$fechafull\");' style='text-align: center; cursor:pointer; color:#1F618D; font-weight:bold; font-size:1.0em;$fondo'>$id_pedido</td>";

                echo "<td style='text-align: center;$fondo'><span style='display:none;'>" . str_replace("/", "", $fecha_pedido) . "</span>" . str_replace("/20", "/", $fecha) . "</td>";

                echo "<td style='$fondo'>$producto</td>";

                echo "<td style='$fondo'>$cliente</td>";

            }

            $fecha_siembra2 = explode("/", $fecha_siembra);

            $fecha_siembra2 = $fecha_siembra2[2] . "/" . $fecha_siembra2[1] . "/" . $fecha_siembra2[0];

            $fecha_entrega2 = explode("/", $fecha_entrega);

            $fecha_entrega2 = $fecha_entrega2[2] . "/" . $fecha_entrega2[1] . "/" . $fecha_entrega2[0];

            echo "<td style='text-align: center;font-weight:bold;font-size:1.2em;$fondo'>$cant_band</td>";

            echo "<td style='text-align: center;$fondo'><span style='display:none'>" . str_replace("/", "", $fecha_siembra2) . "</span>" . str_replace("/20", "/", $fecha_siembra) . "</td>";

            echo "<td style='text-align: center;;$fondo'><span style='display:none'>" . str_replace("/", "", $fecha_entrega2) . "</span>" . str_replace("/20", "/", $fecha_entrega) . "</td>";

            echo "<td style='$fondo'><div style='cursor:pointer' onClick='MostrarModalEstado($id_artpedido)' id='estado_$id_artpedido'>$estado</div></td>";

            echo "<td style='text-align: center; font-size:1.0em; font-weight:bold;$fondo'>
            <span style='font-size:1.2em;'>$id_orden</span><br><span style='color: blue;font-size:1.2em'>$tipo</span>
            </td>";
            echo "<td style='text-align: center; font-size:1.0em; $fondo'>" . $ww['id_mesada'] . "</td>";

            echo "</tr>";

            array_push($array, $ww['id_pedido']);
        }
        echo "</tbody>";
        echo "</table>";
        echo "</div>";
        echo "</div>";
    } else {
        echo "<div class='callout callout-danger'><b>No se encontraron pedidos en las fechas indicadas...</b></div>";
    }
} else if ($consulta == "cargar_mesadas_disponibles") {
    $id_artpedido = $_POST["id_artpedido"];
    $val = mysqli_query($con, "SELECT IFNULL(SUM(cantidad),0) as cantidad, id_mesada as mesada_original FROM ordenes_mesadas WHERE id_orden = (SELECT id_orden FROM ordenes_siembra WHERE id_artpedido = $id_artpedido)");
    if (mysqli_num_rows($val) > 0) {
        $ds = mysqli_fetch_assoc($val);
        $cantidad = $ds["cantidad"];

        $cadenaselect = "SELECT
        m.capacidad,
        m.id_mesada,
        m.id_tipo,
        t.nombre
        FROM mesadas m
        LEFT JOIN tipos_producto t ON m.id_tipo = t.id_articulo
        GROUP BY m.id_mesada ORDER BY m.id_mesada ASC
            ;";
        $val = mysqli_query($con, $cadenaselect);

        echo "<option value='$ds[mesada_original]'>DEJAR EN LA MISMA MESADA</option>";

        if (mysqli_num_rows($val) > 0) {
            while ($re = mysqli_fetch_array($val)) {
                $query2 = "SELECT SUM(t1.cantidad) as cantidad, t2.cantidad_stock, t3.cantidad_reservada FROM ( SELECT DISTINCT(om.id_orden) as id_orden, o.id_orden_alternativa, om.id_mesada, p.estado, om.cantidad FROM ordenes_mesadas om LEFT JOIN ordenes_siembra o ON om.id_orden = o.id_orden LEFT JOIN articulospedidos p ON p.id_artpedido = o.id_artpedido WHERE p.estado IN (4, 5, 6) AND om.cantidad > 0 AND om.id_mesada = $re[id_mesada]) as t1, (SELECT IFNULL(SUM(sb.cantidad),0) as cantidad_stock FROM stock_bandejas sb WHERE sb.id_mesada = $re[id_mesada]) as t2,
                    (SELECT IFNULL(SUM(rp.cantidad), 0) as cantidad_reservada FROM reservas_productos rp INNER JOIN stock_bandejas sb ON rp.id_stock = sb.id_stock WHERE sb.id_mesada = $re[id_mesada] AND rp.entregado = 1) t3
                ";
                $val2 = mysqli_query($con, $query2);
                $ww = mysqli_fetch_assoc($val2);

                $disponibles = (int) $ww['cantidad'] + (int) $ww['cantidad_stock'] - (int) $ww['cantidad_reservada'];
                if ($disponibles >= $cantidad) {
                    echo "<option value='$re[id_mesada]'>$re[id_mesada] - Libres: $disponibles</option>";
                }
            }
        }
    }
} else if ($consulta == "envia_stock_total") {
    $id_artpedido = $_POST["id_artpedido"];
    $id_mesada = $_POST["id_mesada"];
    try {
        $errors = array();
        mysqli_autocommit($con, false);
        if (mysqli_query($con, "DELETE FROM ordenes_mesadas WHERE id_orden = (SELECT id_orden FROM ordenes_siembra WHERE id_artpedido = $id_artpedido)")) {
            $query = "INSERT INTO stock_bandejas (
                cantidad,
                cantidad_original,
                tipo_bandeja,
                id_mesada,
                id_variedad,
                id_artpedido,
                tipo_stock,
                fecha_stock
            )
            VALUES (
                (SELECT cant_band_reales FROM ordenes_siembra WHERE id_artpedido = $id_artpedido),
                (SELECT cant_band_reales FROM ordenes_siembra WHERE id_artpedido = $id_artpedido),
                (SELECT bandeja FROM articulospedidos WHERE id_artpedido = $id_artpedido),
                $id_mesada,
                (SELECT id_articulo FROM articulospedidos WHERE id_artpedido = $id_artpedido),
                $id_artpedido,
                'ENV',
                NOW()
            );";
            if (mysqli_query($con, $query)) {
                $query = "UPDATE articulospedidos SET
				estado = 8
				WHERE id_artpedido = $id_artpedido;";

                if (!mysqli_query($con, $query)) {
                    $errors[] = mysqli_error($con);
                }
            } else {
                $errors[] = mysqli_error($con);
            }
        } else {
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
        }
        mysqli_close($con);
    } catch (\Throwable $th) {
        throw $th;
    }
} else if ($consulta == "envia_stock_sobrante") {
    $id_artpedido = $_POST["id_artpedido"];
    $id_mesada = $_POST["id_mesada"];
    $cantidad = $_POST["cantidad"];
    try {
        $errors = array();
        $val = mysqli_query($con, "SELECT * FROM ordenes_mesadas WHERE id_orden = (SELECT id_orden FROM ordenes_siembra WHERE id_artpedido = $id_artpedido) ORDER BY cantidad ASC");

        if (mysqli_num_rows($val) == 1) { // CASO MAS SIMPLE
            $ww = mysqli_fetch_assoc($con);
            $query = "UPDATE ordenes_mesadas SET cantidad = (cantidad - $cantidad) WHERE id_orden = (SELECT id_orden FROM ordenes_siembra WHERE id_artpedido = $id_artpedido)";
            mysqli_autocommit($con, false);
            if (!mysqli_query($con, $query)) {
                $errors[] = mysqli_error($con);
            }

        } else if (mysqli_num_rows($val) > 1) { //LA ORDEN ESTA REPARTIDA EN DOS O MAS MESADAS = DOLOR DE HUEVOS
            $contador = (int) $cantidad;
            mysqli_autocommit($con, false);
            while ($ww = mysqli_fetch_array($val)) {
                if ($contador > 0) {
                    if ((int) $ww["cantidad"] <= $contador) {
                        $query = "UPDATE ordenes_mesadas SET cantidad = 0 WHERE id_orden = (SELECT id_orden FROM ordenes_siembra WHERE id_artpedido = $id_artpedido)";
                        if (!mysqli_query($con, $query)) {
                            $errors[] = mysqli_error($con);
                        }
                        $contador = $contador - (int) $ww["cantidad"];
                    } else {
                        $query = "UPDATE ordenes_mesadas SET cantidad = (cantidad - $cantidad) WHERE id_orden = $ww[id_orden]";
                        if (!mysqli_query($con, $query)) {
                            $errors[] = mysqli_error($con);
                        }
                        $contador = $contador - (int) $ww["cantidad"];
                    }

                }
            }
        }

        $query = "INSERT INTO stock_bandejas (
                cantidad,
                cantidad_original,
                tipo_bandeja,
                id_mesada,
                id_variedad,
                id_artpedido,
                tipo_stock,
                fecha_stock
            )
            VALUES (
                $cantidad,
                $cantidad,
                (SELECT bandeja FROM articulospedidos WHERE id_artpedido = $id_artpedido),
                $id_mesada,
                (SELECT id_articulo FROM articulospedidos WHERE id_artpedido = $id_artpedido),
                $id_artpedido,
                'SOB',
                NOW()
            );";
        if (!mysqli_query($con, $query)) {
            $errors[] = mysqli_error($con);
        }
        //PROBLEMA AQUI
        $query = "UPDATE ordenes_siembra SET cant_band_reales = (SELECT cant_band FROM articulospedidos WHERE id_artpedido = $id_artpedido) WHERE id_orden = (SELECT id_orden FROM ordenes_siembra WHERE id_artpedido = $id_artpedido)";
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
} else if ($consulta == "asignar_pedido_otrocliente") {
    $id_artpedido = $_POST["id_artpedido"];
    $id_cliente = $_POST["id_cliente"];
    $id_clienteoriginal = $_POST["id_clienteoriginal"];
    try {
        $errors = array();
        $val = mysqli_query($con, "SELECT IFNULL(MAX(ID_PEDIDO),0)+1 as id_pedido FROM pedidos");
        if (mysqli_num_rows($val) > 0) {
            $ww = mysqli_fetch_assoc($val);
            $id_pedidonuevo = $ww["id_pedido"];
            mysqli_autocommit($con, false);
            $query = "INSERT INTO pedidos (ID_CLIENTE, FECHA, id_clienteoriginal, observaciones, fecha_real)
            VALUES ($id_cliente, NOW(), $id_clienteoriginal, 'PERTENECÍA AL CLIENTE $id_clienteoriginal', NOW());";
            if (mysqli_query($con, $query)) {
                $cadena = "UPDATE articulospedidos SET id_pedido = $id_pedidonuevo WHERE id_artpedido = $id_artpedido;";
                if (!mysqli_query($con, $cadena)) {
                    $errors[] = mysqli_error($con);
                }
            } else {
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
            }
            mysqli_close();
        }
    } catch (\Throwable $th) {
        throw $th;
    }
} else if ($consulta == "cargar_ordenes_similares") {
    $id_artpedido = $_POST['id_artpedido'];
    $cadena = "SELECT o.id_orden_alternativa, p.id_artpedido, t.nombre as nombre_tipo,
	s.nombre as nombre_subtipo, v.nombre as nombre_variedad, p.cant_plantas,
	 o.cant_band_reales, p.bandeja, p.fecha_entrega, DATE_FORMAT(o.fecha_siembra, '%d/%m/%y') as fecha_siembra,
	 c.nombre as cliente, c.id_cliente, p.estado
	FROM variedades_producto v
	INNER JOIN subtipos_producto s ON v.id_subtipo = s.id_articulo
	INNER JOIN tipos_producto t ON t.id_articulo = s.id_tipo
	INNER JOIN articulospedidos p ON p.id_articulo = v.id_articulo
	INNER JOIN pedidos pe ON p.id_pedido = pe.id_pedido
	INNER JOIN clientes c ON pe.id_cliente = c.id_cliente
	LEFT JOIN ordenes_siembra o ON o.id_artpedido = p.id_artpedido
	WHERE p.id_articulo = (SELECT id_articulo FROM articulospedidos WHERE id_artpedido = $id_artpedido) AND p.estado IN (4) AND p.id_artpedido <> $id_artpedido ORDER BY fecha_entrega ASC, id_artpedido ASC";
    $val = mysqli_query($con, $cadena);
    if (mysqli_num_rows($val) > 0) {
        $salida = "";
        while ($re = mysqli_fetch_array($val)) {
            $producto = "$re[nombre_tipo] $re[nombre_subtipo] $re[nombre_variedad] x$re[bandeja]";
            if ($re['con_semilla'] == 1) {
                $producto .= " CON SEMILLA";
            } else {
                $producto .= " SIN SEMILLA";
            }

            $salida .= "<tr  style='cursor:pointer;'
                            x-id-artpedido=\"$re[id_artpedido]\"
                            x-id-cliente=\"$re[id_cliente]\"
                            onClick='setSelected(this)'>
							<td style='font-size:16px;font-weight:bold;text-align:center;' >$re[id_orden_alternativa]</td>
							<td style='word-wrap:break-word;'>$producto</td>
							<td style='text-align:center;font-size:16px'>$re[cant_band_reales]</td>
							<td id='cliente_$re[id_cliente]' style='word-wrap:break-word;' >$re[cliente] ($re[id_cliente])</td>
							<td style='word-wrap:break-word;text-align:center;'>$re[fecha_entrega]</td>
							</tr>";
        }
        echo $salida;
    }
} else if ($consulta == "permuta_clientes") {
    $id_artpedido = $_POST['id_artpedido'];
    $id_clienteoriginal = $_POST['id_clienteoriginal'];
    $id_artpedidoseleccionado = $_POST['id_artpedidoseleccionado'];
    $id_clienteseleccionado = $_POST['id_clienteseleccionado'];
    $errors = array();
    try {
        $val = mysqli_query($con, "SELECT  (
            SELECT id_pedido FROM articulospedidos WHERE id_artpedido = $id_artpedidoseleccionado
            )
             AS id1,
            (
                SELECT id_pedido FROM articulospedidos WHERE id_artpedido = $id_artpedido
            )  AS id2");
        if (mysqli_num_rows($val) > 0) {
            $re = mysqli_fetch_assoc($val);
            $id1 = $re["id1"];
            $id2 = $re["id2"];
            if (strlen($id1) > 0 && strlen($id2) > 0) {
                mysqli_autocommit($con, false);
                $cadena = "UPDATE articulospedidos SET id_pedido = $id1 WHERE id_artpedido = $id_artpedido;";
                if (!mysqli_query($con, $cadena)) {
                    $errors[] = mysqli_error($con);
                }
                $cadena = "UPDATE articulospedidos SET id_pedido = $id2 WHERE id_artpedido = $id_artpedidoseleccionado;";
                if (!mysqli_query($con, $cadena)) {
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
                }
            }
        }
        mysqli_close($con);
    } catch (\Throwable $th) {
        throw $th;
    }
} else if ($consulta == "cancelar_pedido") {
    $id_artpedido = $_POST["id_artpedido"];
    $errors = array();
    mysqli_autocommit($con, false);

    if ($id_artpedido && strlen($id_artpedido) > 0) {
        $query = "UPDATE articulospedidos SET estado = -1 WHERE id_artpedido = $id_artpedido;";
        if (!mysqli_query($con, $query)) {
            $errors[] = mysqli_error($con);
        }

        $query = "DELETE FROM agenda_entregas WHERE id_artpedido = $id_artpedido;";
        if (!mysqli_query($con, $query)) {
            $errors[] = mysqli_error($con);
        }

        $query = "DELETE FROM ordenes_mesadas WHERE id_orden = (SELECT id_orden FROM ordenes_siembra WHERE id_artpedido = $id_artpedido);";
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
} else if ($consulta == "eliminar_producto_pedido") {
    $id_art = $_POST["id_art"];
    $cadena = "DELETE FROM articulospedidos WHERE id_artpedido = $id_art;";
    if (mysqli_query($con, $cadena)) {
        echo "success";
    } else {
        echo mysqli_error($con) . "-" . $cadena;
    }
} else if ($consulta == "enviar_a_revision") {
    $id_artpedido = $_POST["id_artpedido"];
    $tipo_revision = $_POST["tipo_revision"];
    $query = "UPDATE articulospedidos SET revision = $tipo_revision WHERE id_artpedido = $id_artpedido;";
    if (mysqli_query($con, $query)) {
        echo "success";
    } else {
        print_r(mysqli_error($con));
    }
} else if ($consulta == "marcarproblema") {
    $id_artpedido = $_POST["id_artpedido"];
    $observaciones = mysqli_real_escape_string($con, $_POST["observaciones"]);
    if ($observaciones != null) {
        $query = "UPDATE articulospedidos SET problema = 1, observacionproblema = UPPER('$observaciones') WHERE id_artpedido = $id_artpedido;";
    } else {
        $query = "UPDATE articulospedidos SET problema = 1 WHERE id_artpedido = $id_artpedido;";
    }
    if (mysqli_query($con, $query)) {
        echo "success";
    } else {
        print_r(mysqli_error($con));
    }
} else if ($consulta == "quitarproblema") {
    $id_artpedido = $_POST["id_artpedido"];
    $query = "UPDATE articulospedidos SET problema = NULL, observacionproblema = NULL WHERE id_artpedido = $id_artpedido;";
    if (mysqli_query($con, $query)) {
        echo "success";
    } else {
        print_r(mysqli_error($con));
    }
} else if ($consulta == "quitar_revision") {
    $id_artpedido = $_POST["id_artpedido"];
    $query = "UPDATE articulospedidos SET revision = NULL, solucion = NULL WHERE id_artpedido = $id_artpedido;";
    if (mysqli_query($con, $query)) {
        echo "success";
    } else {
        print_r(mysqli_error($con));
    }
} else if ($consulta == "aplicar_solucion") {
    $id_artpedido = $_POST["id_artpedido"];
    $tipo_revision = $_POST["tipo_revision"];
    $query = "UPDATE articulospedidos SET solucion = $tipo_revision WHERE id_artpedido = $id_artpedido;";
    if (mysqli_query($con, $query)) {
        echo "success";
    } else {
        print_r(mysqli_error($con));
    }
}
