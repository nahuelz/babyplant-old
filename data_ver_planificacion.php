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

if ($consulta == "get_totales") {
    $fecha = $_POST["fecha"];
    $cadenaselect = "SELECT t.nombre as nombre_tipo, s.nombre as nombre_subtipo, v.nombre as nombre_variedad,
    a.fecha_planificacion, a.bandeja, a.id_artpedido, a.estado, SUM(a.cant_band) as suma
    FROM articulospedidos a INNER JOIN variedades_producto v ON v.id_articulo = a.id_articulo
    INNER JOIN subtipos_producto s ON s.id_articulo = v.id_subtipo
    INNER JOIN tipos_producto t ON t.id_articulo = s.id_tipo
    INNER JOIN pedidos p ON p.ID_PEDIDO = a.id_pedido
    WHERE a.fecha_planificacion = '$fecha' AND a.estado IN (0,1)
    GROUP BY t.nombre
    ORDER BY nombre_tipo";

    $val = mysqli_query($con, $cadenaselect);
    $salida = "";
    $total = 0;
    if (mysqli_num_rows($val) > 0) {
        while ($re = mysqli_fetch_array($val)) {
            $total += (int) $re["suma"];
            $salida .= "<tr>
                <td style='word-wrap:break-word;'>$re[nombre_tipo]</td>
                <td style='text-align:center;font-weight:bold;font-size:1.2em;'>$re[suma]</td>
              </tr>";
        }
        $salida .= "<tr>
                <td style='word-wrap:break-word;font-weight:bold;'>Bandejas Totales: </td>
                <td style='text-align:center;font-weight:bold;font-size:1.5em;'>$total</td>
              </tr>";
    } else {
        $salida = "<tr>
                <td style='word-wrap:break-word;font-weight:bold;'>El día seleccionado no tiene bandejas planificadas</td>
                <td></td>
              </tr>";
    }
    echo $salida;
} else if ($consulta == "carga_pedidos") {
    $str = json_decode($_POST['fechas'], true);
    $lista = str_replace('"', "'", $_POST["fechas"]);
    $lista = str_replace("[", "(", $lista);
    $lista = str_replace("]", ")", $lista);

    $arraycolumnas = array(
        "$str[0]" => array(),
        "$str[1]" => array(),
        "$str[2]" => array(),
        "$str[3]" => array(),
        "$str[4]" => array(),
        "$str[5]" => array(),
    );

    $cadenaselect = "SELECT t.nombre as nombre_tipo, s.nombre as nombre_subtipo, v.nombre as nombre_variedad,
a.fecha_planificacion, a.bandeja, a.id_artpedido, a.estado, a.fila, a.cant_band, c.nombre as nombre_cliente
FROM articulospedidos a
INNER JOIN variedades_producto v ON v.id_articulo = a.id_articulo
INNER JOIN subtipos_producto s ON s.id_articulo = v.id_subtipo
INNER JOIN tipos_producto t ON t.id_articulo = s.id_tipo
INNER JOIN pedidos p ON p.ID_PEDIDO = a.id_pedido
INNER JOIN clientes c ON c.id_cliente = p.ID_CLIENTE
WHERE a.fecha_planificacion IN $lista AND a.estado IN (0,1,2) ORDER BY nombre_tipo, nombre_subtipo, nombre_variedad
";

    $val = mysqli_query($con, $cadenaselect);
    if (mysqli_num_rows($val) > 0) {
        while ($re = mysqli_fetch_array($val)) {
            array_push($arraycolumnas["$re[fecha_planificacion]"], [
                $re['nombre_tipo'] . " " . $re['nombre_subtipo'] . "|" . $re['nombre_variedad'] . " x" . $re['bandeja'],
                $re['id_artpedido'],
                $re['estado'],
                $re['fila'],
                $re['cant_band'],
                $re["fecha_planificacion"],
                $re["nombre_cliente"],
            ]);
        }
        echo json_encode($arraycolumnas);
    }
} else if ($consulta == "actualiza_filas") {
    $str = json_decode($_POST['jsonarray'], true);
    $errors = array();
    mysqli_autocommit($con, FALSE);
    for ($i = 0; $i < count($str); $i++) {
        $id_art = $str[$i][0];
        $fecha = $str[$i][1];
        $fila = $str[$i][2];
        $query = "UPDATE articulospedidos SET fecha_planificacion = '$fecha', fila = $fila WHERE id_artpedido = $id_art;";
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
    }
    mysqli_close($con);
    
} else if ($consulta == "change_week") {
    $fecha = $_POST['nuevafecha'];
    $str = json_decode($_POST['jsonarray'], true);
    $errors = array();
    mysqli_autocommit($con, false);
    for ($i = 0; $i < count($str); $i++) {
        $id_art = $str[$i];
        $query = "UPDATE articulospedidos SET fecha_planificacion = '$fecha', fila = NULL WHERE id_artpedido = $id_art;";
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
} else if ($consulta == "actualiza_codsobre") {
    $cod_sobre = $_POST['codigo'];
    $id_art = $_POST['id_art'];
    $query = "UPDATE articulospedidos SET cod_sobre = '$cod_sobre' WHERE id_artpedido = $id_art;";
    if (mysqli_query($con, $query)){
        echo "success";
    }
    else{
        print_r(mysqli_error($con));
    }
} else if ($consulta == "agrega_orden") {
    $fecha_planificacion = $_POST['fecha_planificacion'];
    $str = json_decode($_POST['jsonarray'], true);
    mysqli_autocommit($con, false);
    $errors = array();

    for ($i = 0; $i < count($str); $i++) {
        $id_art = $str[$i];
        $query = "INSERT INTO ordenes_siembra (id_artpedido, fecha, id_orden_alternativa) VALUES ($id_art, NOW(), (SELECT IFNULL(MAX(o.id_orden_alternativa), 0)+1 FROM ordenes_siembra o LEFT JOIN articulospedidos a ON o.id_artpedido = a.id_artpedido INNER JOIN variedades_producto v ON v.id_articulo = a.id_articulo INNER JOIN subtipos_producto s ON s.id_articulo = v.id_subtipo INNER JOIN tipos_producto t ON t.id_articulo = s.id_tipo WHERE t.nombre = (SELECT t.nombre FROM articulospedidos a INNER JOIN variedades_producto v ON v.id_articulo = a.id_articulo INNER JOIN subtipos_producto s ON s.id_articulo = v.id_subtipo INNER JOIN tipos_producto t ON t.id_articulo = s.id_tipo
		WHERE a.id_artpedido = $id_art)));";
        if (!mysqli_query($con, $query)) {
            $errors[] = mysqli_error($con);
        }
        $query = "UPDATE articulospedidos SET estado = 1, fecha_planificacion = '$fecha_planificacion' WHERE id_artpedido = $id_art;";
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
