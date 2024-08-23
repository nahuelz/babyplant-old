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

if ($consulta == "cargar_mesadas_camara") {
    $arraycolumnas = [];
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

    if (mysqli_num_rows($val) > 0) {
        while ($re = mysqli_fetch_array($val)) {
            $query2 = "SELECT SUM(t1.cantidad) as cantidad, t2.cantidad_stock, t3.cantidad_reservada FROM ( SELECT DISTINCT(om.id_orden) as id_orden, o.id_orden_alternativa, om.id_mesada, p.estado, om.cantidad FROM ordenes_mesadas om LEFT JOIN ordenes_siembra o ON om.id_orden = o.id_orden LEFT JOIN articulospedidos p ON p.id_artpedido = o.id_artpedido WHERE p.estado IN (4, 5, 6) AND om.cantidad > 0 AND om.id_mesada = $re[id_mesada]) as t1, (SELECT IFNULL(SUM(sb.cantidad),0) as cantidad_stock FROM stock_bandejas sb WHERE sb.id_mesada = $re[id_mesada]) as t2,
            (SELECT IFNULL(SUM(rp.cantidad), 0) as cantidad_reservada FROM reservas_productos rp INNER JOIN stock_bandejas sb ON rp.id_stock = sb.id_stock WHERE sb.id_mesada = $re[id_mesada] AND rp.entregado = 1) t3
        ";
            $val2 = mysqli_query($con, $query2);
            $ww = mysqli_fetch_assoc($val2);

            array_push($arraycolumnas, array(
                "id_mesada" => $re['id_mesada'],
                "capacidad" => $re['capacidad'],
                "cantidad_ordenes" => $ww['cantidad'],
                "cantidad_stock" => $ww['cantidad_stock'],
                "cantidad_reservada" => $ww['cantidad_reservada'],
                "query" => $query2,
                "cantidad" => (int) $ww['cantidad'] + (int) $ww['cantidad_stock'] - (int) $ww['cantidad_reservada'],
                "nombre" => $re['nombre'],
                "id_tipo" => $re['id_tipo'],
            ));
        }
        echo json_encode($arraycolumnas);
    }
} else if ($consulta == "cargar_infomesada") {
    $id_mesada = $_POST['id_mesada'];
    $$query = "SELECT
        DISTINCT(p.id_artpedido) as id_artpedido,
        o.id_orden_alternativa,
        t.nombre as nombre_tipo,
        s.nombre as nombre_subtipo,
        v.nombre as nombre_variedad,
        DATE_FORMAT(o.fecha_mesada_in, '%d/%m/%Y') as fecha_mesada_in,
        p.bandeja,
        p.fecha_entrega,
        DATE_FORMAT(o.fecha_siembra, '%d/%m/%Y') as fecha_siembra,
        c.nombre as cliente,
        c.id_cliente,
        p.estado,
        om.cantidad,
        p.cant_band - IFNULL(en.cantidad,0) as cantirestante,
        o.id_orden
        FROM variedades_producto v
        INNER JOIN subtipos_producto s ON v.id_subtipo = s.id_articulo
        INNER JOIN tipos_producto t ON t.id_articulo = s.id_tipo
        INNER JOIN articulospedidos p ON p.id_articulo = v.id_articulo
        LEFT JOIN entregas en ON p.id_artpedido = en.id_artpedido
        INNER JOIN pedidos pe ON p.id_pedido = pe.id_pedido
        INNER JOIN clientes c ON pe.id_cliente = c.id_cliente
        LEFT JOIN ordenes_siembra o ON o.id_artpedido = p.id_artpedido
        LEFT JOIN ordenes_mesadas om ON o.id_orden = om.id_orden
        WHERE om.id_mesada = $id_mesada AND om.cantidad > 0 AND p.estado IN (4,5,6)

        UNION

        SELECT
        sb.id_stock as id_artpedido,
        NULL as id_orden_alternativa,
        t.nombre as nombre_tipo,
        s.nombre as nombre_subtipo,
        v.nombre as nombre_variedad,
        DATE_FORMAT(sb.fecha_stock, '%d/%m/%y') as fecha_stock,
        sb.tipo_bandeja as bandeja,
        NULL as fecha_entrega,
        NULL as fecha_siembra,
        NULL as cliente,
        NULL as id_cliente,
        8 as estado,
        sb.cantidad,
        NULL as cantirestante,
        NULL as id_orden
        FROM variedades_producto v
        INNER JOIN subtipos_producto s ON v.id_subtipo = s.id_articulo
        INNER JOIN tipos_producto t ON t.id_articulo = s.id_tipo
        INNER JOIN stock_bandejas sb ON sb.id_variedad = v.id_articulo
        LEFT JOIN reservas_productos rp ON rp.id_stock = sb.id_stock
        WHERE sb.id_mesada = $id_mesada AND sb.tipo_stock IN ('IM', 'DEV', 'ENV', 'SOB')

    ";

    $val = mysqli_query($con, $$query);
    $salida = "";
    if (mysqli_num_rows($val) > 0) {
        $ignorar = false;
        while ($re = mysqli_fetch_array($val)) {
            if ((int) $re["estado"] == 8) {
                $query2 = "SELECT IFNULL(SUM(rp.cantidad), 0) as cantidad_reservada
                        FROM reservas_productos rp
                        INNER JOIN stock_bandejas sb ON rp.id_stock = sb.id_stock
                        WHERE sb.id_stock = $re[id_artpedido] AND rp.entregado = 1
                ";
                $val2 = mysqli_query($con, $query2);
                $ww = mysqli_fetch_assoc($val2);

                if (((int)$re["cantidad"] - (int)$ww["cantidad_reservada"]) <= 0) {
                    $ignorar = true;
                }
                else{
                    $ignorar = false;
                }
            }

            if ($ignorar == false) {
                $producto = "$re[nombre_tipo] $re[nombre_subtipo] $re[nombre_variedad] x$re[bandeja]";
                if ($re['con_semilla'] == 1) {
                    $producto .= " CON SEMILLA";
                } else {
                    $producto .= " SIN SEMILLA";
                }

                $estado = "";

                if ($re['estado'] == 4) {
                    $estado = "<div id='entrega_$re[id_artpedido]' onClick='MostrarModalEntregar(this)' style='text-align:center; background-color:#04B404; border-radius:6px; border-style: solid; border-color: black;border-width: 1px; padding:3px;'><span>EN<br>INVERNÁCULO</span></div>";
                } else if ($re['estado'] == 5) {
                    $estado = "<div id='entrega_$re[id_artpedido]'  onClick='MostrarModalEntregar(this)' style='word-wrap:break-word;text-align:center;background-color:#01DFD7; border-radius:6px; 	border-style: solid; border-color: black;border-width: 1px; padding:3px;'><span>PARA<br>ENTREGAR</span></div>";
                } else if ($re['estado'] == 6) {
                    $estado = "<div id='entrega_$re[id_artpedido]'  onClick='MostrarModalEntregar(this)' style='word-wrap:break-word;text-align:center;background-color:#FFFF00; border-radius:6px; 	border-style: solid; border-color: black;border-width: 1px; padding:3px; cursor:pointer;'><span>ENTREGADO<br>	PARCIALMENTE</span></div>";
                } else if ($re['estado'] == 7) {
                    $estado = "<div id='entrega_$re[id_artpedido]'  onClick='MostrarModalEntregar(this)' style='word-wrap:break-word;text-align:center;background-color:#A9F5BC; border-radius:6px; 	border-style: solid; border-color: black;border-width: 1px; padding:3px; cursor:pointer;'><span>ENTREGADO<br>	COMPLETAMENTE</span></div>";
                } else if ($re['estado'] == 8) {
                    $estado = "<div id='entrega_$re[id_artpedido]'  onClick='MostrarModalEntregar(this)' style='word-wrap:break-word;text-align:center;background-color:#FAAC58; border-radius:6px; 	border-style: solid; border-color: black;border-width: 1px; padding:3px;cursor:pointer;'><span>EN STOCK</span></div>";
                } else if ($re['estado'] == -1) {
                    $estado = "<div style='word-wrap:break-word;text-align:center;background-color:#FA5858; border-radius:6px; border-style: solid; border-color: black;border-width: 1px; padding:3px; cursor:pointer;'><span>CANCELADO</span></div>";
                }

                if ((int) $re["estado"] == 8) {
                    $cantidad = (int)$re["cantidad"] - (int)$ww["cantidad_reservada"];
                }
                else{
                    $cantidad = $re["cantidad"];
                }

                $id_art = $re['id_artpedido'];
                $salida = "<tr  style='cursor:pointer'>
							<td id='art_$re[id_artpedido]' style='font-size:1.4em;font-weight:bold;text-align:center;'>$re[id_orden_alternativa]</td>
							<td style='font-size:1.0em;word-wrap:break-word;' onClick='VerEstadoOrden($re[id_artpedido])'>$producto</td>
							<td onClick='ModificarCantidad($re[id_orden], this)' style='text-align:center;font-size:1.4em'>$cantidad</td>
							<td style='text-align:center;font-size:0.8em'>$re[cantirestante]</td>
							<td id='cliente_$re[id_cliente]' style='word-wrap:break-word;font-size:1.0em;' >$re[cliente] ($re[id_cliente])</td>
							<td style='font-size:1.1em;word-wrap:break-word;text-align:center;'>$re[fecha_siembra]</td>
							<td style='font-size:1.1em;word-wrap:break-word;text-align:center;'>$re[fecha_mesada_in]</td>
							<td style='font-size:1.1em;word-wrap:break-word;text-align:center;'>$re[fecha_entrega]</td>
							<td style='font-size:1.0em'>$estado</td>
						</tr>";
                echo $salida;
            }
            
        }

    }
} else if ($consulta == "guardar_mesadas") {
    $str = json_decode($_POST['jsonarray'], true);
    $id_orden = $_POST['id_orden'];
    $errors = array();
    mysqli_autocommit($con, false);

    for ($i = 0; $i < count($str); $i++) {
        $id_mesada = $str[$i][0];
        $cantidad = $str[$i][1];
        $query = "INSERT INTO ordenes_mesadas (id_orden, id_mesada, cantidad, cantidadinicial, id_variedad) VALUES ($id_orden, $id_mesada, $cantidad, $cantidad, (SELECT id_articulo FROM articulospedidos WHERE id_artpedido = (SELECT id_artpedido FROM ordenes_siembra WHERE id_orden = $id_orden)));";
        if (!mysqli_query($con, $query)) {
            $errors[] = mysqli_error($con);
        }
    }
    $query = "UPDATE articulospedidos SET estado = 4 WHERE id_artpedido = (SELECT id_artpedido FROM ordenes_siembra WHERE id_orden = $id_orden);";
    if (!mysqli_query($con, $query)) {
        $errors[] = mysqli_error($con);
    }
    $query = "UPDATE ordenes_siembra SET fecha_mesada_in = NOW() WHERE id_orden = $id_orden;";
    if (!mysqli_query($con, $query)) {
        $errors[] = mysqli_error($con);
    }

    if (count($errors) === 0) {
        if (mysqli_commit($con)) {
            echo "success";
        } else {
            mysqli_rollback($con);
            print_r($errors);
        }
    } else {
        mysqli_rollback($con);
        print_r($errors);
    }
    mysqli_close($con);
} else if ($consulta == "guardar_reasignacion_mesadas") {
    $str = json_decode($_POST['jsonarray'], true);
    $id_orden = $_POST['id_orden'];
    $rowLength = count($str);
    $errors = array();

    mysqli_autocommit($con, false);
    $query = "DELETE FROM ordenes_mesadas WHERE id_orden = $id_orden";
    if (!mysqli_query($con, $query)) {
        $errors[] = mysqli_error($con) . "-" . $query;
    }
    for ($i = 0; $i < $rowLength; $i++) {
        $id_mesada = $str[$i][0];
        $cantidad = $str[$i][1];
        $query = "INSERT INTO ordenes_mesadas (id_orden, id_mesada, cantidad, cantidadinicial, id_variedad) VALUES ($id_orden, $id_mesada, $cantidad, $cantidad, (SELECT id_articulo FROM articulospedidos WHERE id_artpedido = (SELECT id_artpedido FROM ordenes_siembra WHERE id_orden = $id_orden)));";
        if (!mysqli_query($con, $query)) {
            $errors[] = mysqli_error($con) . "-" . $query;
        }
    }
    $query = "UPDATE articulospedidos SET estado = 4 WHERE id_artpedido = (SELECT id_artpedido FROM ordenes_siembra WHERE id_orden = $id_orden);";
    if (!mysqli_query($con, $query)) {
        $errors[] = mysqli_error($con);
    }

    $query = "UPDATE ordenes_siembra SET fecha_mesada_in = NOW() WHERE id_orden = $id_orden;";
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
} else if ($consulta == "crear_mesada") {
    $capacidad = $_POST['capacidad'];
    $id_tipo = $_POST['id_tipo'];
    $query = "";
    if ($id_tipo == 0) {
        $query = "INSERT INTO mesadas (capacidad) VALUES ($capacidad);";
    } else {
        $query = "INSERT INTO mesadas (capacidad, id_tipo) VALUES ($capacidad, $id_tipo);";
    }

    if (mysqli_query($con, $query)) {
        echo "success";
    } else {
        print_r(mysqli_error($con));
    }
} else if ($consulta == "editar_mesada") {
    $capacidad = $_POST['capacidad'];
    $id_tipo = $_POST['id_tipo'];
    $id_mesada = $_POST['id_mesa_global'];

    $cadena = "SELECT
    m.capacidad,
    (SELECT IFNULL(SUM(om.cantidad), 0) FROM ordenes_mesadas om INNER JOIN ordenes_siembra o ON o.id_orden = om.id_orden INNER JOIN articulospedidos p ON p.id_artpedido = o.id_artpedido WHERE p.estado IN (4, 5, 6) AND om.id_mesada = m.id_mesada) as cantidad,
    IFNULL(SUM(sb.cantidad),0) as cantidad_stock,
    (SELECT IFNULL(SUM(rp.cantidad), 0) FROM reservas_productos rp INNER JOIN stock_bandejas sb ON rp.id_stock = sb.id_stock WHERE sb.id_mesada = m.id_mesada AND rp.entregado != 1) as cantidad_reservada,
    m.id_mesada,
    m.id_tipo,
    t.nombre
    FROM mesadas m
    LEFT JOIN ordenes_mesadas om ON m.id_mesada = om.id_mesada
    LEFT JOIN stock_bandejas sb ON sb.id_mesada = m.id_mesada
    LEFT JOIN tipos_producto t ON m.id_tipo = t.id_articulo WHERE m.id_mesada = '$id_mesada'";

    $val = mysqli_query($con, $cadena);
    $puede = true;
    if (mysqli_num_rows($val) > 0) {
        $ww = mysqli_fetch_assoc($val);
        $cantidad_ocupada = (int) $re['cantidad'] + (int) $re['cantidad_stock'] - (int) $re['cantidad_reservada'];
        if ($cantidad_ocupada <= (int) $capacidad) {
            $query = "";
            if ($id_tipo == 0) {
                $query = "UPDATE mesadas SET capacidad = $capacidad, id_tipo = NULL WHERE id_mesada = '$id_mesada';";
            } else {
                $query = "UPDATE mesadas SET capacidad = $capacidad, id_tipo = $id_tipo WHERE id_mesada = '$id_mesada';";
            }
            if (mysqli_query($con, $query)) {
                echo "success";
            } else {
                print_r(mysqli_error($con));
            }
        } else {
            echo "La cantidad de bandejas en la Mesada es superior a la capacidad que deseas establecer.";
        }
    }
} else if ($consulta == "modifica_cantidad") {
    $id_orden = $_POST["id_orden"];
    $cantidad = $_POST["cantidad"];
    $id_mesada = $_POST["id_mesada"];
    $cadena = "UPDATE ordenes_mesadas SET cantidad = $cantidad WHERE id_orden = $id_orden AND id_mesada = $id_mesada;";
    if (mysqli_query($con, $cadena)) {
        echo "success";
    } else {
        print_r(mysqli_error($con));
    }
}
