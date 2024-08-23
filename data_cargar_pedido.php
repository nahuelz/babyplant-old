<?php
include "class_lib/sesionSecurity.php";
require 'class_lib/class_conecta_mysql.php';
require 'class_lib/funciones.php';

$con = mysqli_connect($host, $user, $password, $dbname);
if (!$con) {
    die("Connection failed: " . mysqli_connect_error());
}

$consulta = $_POST["consulta"];

if ($consulta == "guardar_pedido") {
    $esCuaderno = $_POST["esCuaderno"];

    $idPedidoEdit = (isset($_POST["idPedidoEdit"]) && strlen($_POST["idPedidoEdit"]) && (int) $_POST["idPedidoEdit"] > 0) ? $_POST["idPedidoEdit"] : null;

    $errors = array();

    if ($esCuaderno != 1) { // NO ES CUADERNO - ES PEDIDO NORMAL
        $pedido = mysqli_query($con, "SELECT (IFNULL(MAX(ID_PEDIDO),0)+1) as id_pedido FROM pedidos;");
        if ($pedido && mysqli_num_rows($pedido) > 0) {
            $id_pedido = mysqli_fetch_assoc($pedido)["id_pedido"];
            $id_cliente = $_POST['id_cliente'];
            $observaciones = strlen($_POST['observaciones']) ? '"'.mysqli_real_escape_string($con, test_input(strtoupper($_POST['observaciones']))).'"' : "NULL";
            $str = json_decode($_POST['jsonarray'], true);
            $pago = $_POST["pago"];

            $query = "INSERT INTO pedidos (id_cliente, FECHA, observaciones, fecha_real) VALUES ($id_cliente, NOW(), $observaciones, NOW());";
            try {
                if (!mysqli_query($con, $query)) {
                    $errors[] = mysqli_error($con);
                }
                for ($i = 0; $i < count($str); $i++) {
                    $id_articulo = $str[$i]["id_variedad"];
                    $cantidad_plantas = $str[$i]["cant_plantas"];
                    $cantidad_semillas = $str[$i]["cant_semillas"];
                    $cantidad_bandejas = $str[$i]["cant_bandejas"];
                    $fecha_siembra = $str[$i]["fecha_siembra"];
                    $fecha_entrega = $str[$i]["fecha_entrega"];
                    $bandeja = $str[$i]["tipo_bandeja"];
                    $semilla = $str[$i]["con_semilla"];

                    $query = "INSERT INTO articulospedidos (id_articulo, cant_plantas, cant_band, cant_semi, bandeja, fecha_entrega, fecha_entrega_original, fecha_siembraestimada, fecha_planificacion, id_pedido, con_semilla) VALUES ($id_articulo, $cantidad_plantas, $cantidad_bandejas, $cantidad_semillas, '$bandeja', '$fecha_entrega', '$fecha_entrega', '$fecha_siembra', '$fecha_siembra', $id_pedido, $semilla);";
                    if (!mysqli_query($con, $query)) {
                        $errors[] = mysqli_error($con);
                    }
                }

                $pago = $_POST["pago"];

                if ($pago != "NULL" && $pago != null && strlen($pago) > 0) {
                    $concepto = mysqli_real_escape_string($con, $_POST["concepto"]);
                    $query = "INSERT INTO pagos_nuevos (monto, fecha, id_cliente, concepto) VALUES ('$pago', NOW(), $id_cliente, '$concepto');";
                    if (!mysqli_query($con, $query)) {
                        $errors[] = mysqli_error($con);
                    }
                }

                if (count($errors) === 0) {
                    if (mysqli_commit($con)) {
                        echo "pedidonum:" . $id_pedido;
                    } else {
                        mysqli_rollback($con);
                    }
                } else {
                    mysqli_rollback($con);
                    print_r($errors);
                }
                mysqli_close($con);
            } catch (\Throwable $th) {
                echo "error";
            }
        }
    } else { // ES CUADERNO
        $id_cliente = $_POST['id_cliente'];
        $observaciones = strlen($_POST['observaciones']) ? '"'.mysqli_real_escape_string($con, test_input(strtoupper($_POST['observaciones']))).'"' : "NULL";
        $str = json_decode($_POST['jsonarray'], true);
        $id_pedido = null;
        mysqli_autocommit($con, false);

        if (!isset($idPedidoEdit)) { // ESTOY AGREGANDO PEDIDO NUEVO
            $query = "INSERT INTO cuaderno_pedidos (id_cliente, FECHA, observaciones) VALUES ($id_cliente, NOW(), $observaciones);";
            try {
                if (!mysqli_query($con, $query)) {
                    $errors[] = mysqli_error($con);
                } else {
                    $id_pedido = mysqli_insert_id($con);
                    for ($i = 0; $i < count($str); $i++) {
                        $id_articulo = $str[$i]["id_variedad"];
                        $cantidad_plantas = $str[$i]["cant_plantas"];
                        $cantidad_semillas = $str[$i]["cant_semillas"];
                        $cantidad_bandejas = $str[$i]["cant_bandejas"];
                        $fecha_siembra = $str[$i]["fecha_siembra"];
                        $fecha_entrega = $str[$i]["fecha_entrega"];
                        $bandeja = $str[$i]["tipo_bandeja"];
                        $semilla = $str[$i]["con_semilla"];

                        $query = "INSERT INTO cuaderno_articulospedidos (id_articulo, cant_plantas, cant_band, cant_semi, bandeja, fecha_entrega, fecha_siembraestimada, id_pedido, con_semilla) VALUES ($id_articulo, $cantidad_plantas, $cantidad_bandejas, $cantidad_semillas, '$bandeja', '$fecha_entrega', '$fecha_siembra', $id_pedido, $semilla);";
                        if (!mysqli_query($con, $query)) {
                            $errors[] = mysqli_error($con);
                        }
                    }
                }

                if (count($errors) === 0) {
                    if (mysqli_commit($con)) {
                        echo "pedidonum:" . $id_pedido;
                    } else {
                        mysqli_rollback($con);
                    }
                } else {
                    mysqli_rollback($con);
                    print_r($errors);
                }
                mysqli_close($con);
            } catch (\Throwable $th) {
                echo "error";
            }
        } else { // ESTOY EDITANDO PEDIDO
            $query = "UPDATE cuaderno_pedidos SET
                id_cliente = $id_cliente,
                observaciones = $observaciones
                WHERE id_pedido = $idPedidoEdit
            ";
            try {
                if (!mysqli_query($con, $query)) {
                    $errors[] = mysqli_error($con);
                } else {
                    $query = "DELETE FROM cuaderno_articulospedidos WHERE id_pedido = $idPedidoEdit;";
                    if (!mysqli_query($con, $query)) {
                        $errors[] = mysqli_error($con);
                    } else {
                        
                        for ($i = 0; $i < count($str); $i++) {
                            $id_articulo = $str[$i]["id_variedad"];
                            $cantidad_plantas = $str[$i]["cant_plantas"];
                            $cantidad_semillas = $str[$i]["cant_semillas"];
                            $cantidad_bandejas = $str[$i]["cant_bandejas"];
                            $fecha_siembra = $str[$i]["fecha_siembra"];
                            $fecha_entrega = $str[$i]["fecha_entrega"];
                            $bandeja = $str[$i]["tipo_bandeja"];
                            $semilla = $str[$i]["con_semilla"];

                            $query = "INSERT INTO cuaderno_articulospedidos (id_articulo, cant_plantas, cant_band, cant_semi, bandeja, fecha_entrega, fecha_siembraestimada, id_pedido, con_semilla) VALUES ($id_articulo, $cantidad_plantas, $cantidad_bandejas, $cantidad_semillas, '$bandeja', '$fecha_entrega', '$fecha_siembra', $idPedidoEdit, $semilla);";
                            if (!mysqli_query($con, $query)) {
                                $errors[] = mysqli_error($con);
                            }
                        }
                    }
                }

                if (count($errors) === 0) {
                    if (mysqli_commit($con)) {
                        echo "pedidonum:" . $id_pedido;
                    } else {
                        mysqli_rollback($con);
                    }
                } else {
                    mysqli_rollback($con);
                    print_r($errors);
                }
                mysqli_close($con);
            } catch (\Throwable $th) {
                echo "error";
            }
        }
    }
} else if ($consulta == "get_pedido_para_editar") {
    $id_pedido_cuaderno = $_POST["id"];
    $errors = array();
    $productos = [];

    $query = "SELECT * FROM cuaderno_pedidos WHERE id_pedido = $id_pedido_cuaderno";
    $val = mysqli_query($con, $query);
    if (mysqli_num_rows($val)) {
        $query = "SELECT
        p.id_artpedido,
        p.id_articulo,
        p.cant_plantas,
        p.cant_band,
        p.cant_semi,
        p.bandeja,
        p.fecha_entrega,
        p.fecha_siembraestimada,
        p.id_pedido,
        p.con_semilla,
        v.nombre as nombre_variedad,
        s.nombre as nombre_subtipo,
        t.nombre as nombre_tipo,
        t.id_articulo as id_tipo,
        s.id_articulo as id_subtipo
        FROM variedades_producto v
        INNER JOIN subtipos_producto s ON v.id_subtipo = s.id_articulo
        INNER JOIN tipos_producto t ON t.id_articulo = s.id_tipo
        INNER JOIN cuaderno_articulospedidos p ON p.id_articulo = v.id_articulo
        WHERE p.id_pedido = $id_pedido_cuaderno";
        $val2 = mysqli_query($con, $query);
        if (mysqli_num_rows($val2)) {
            mysqli_autocommit($con, false);

            $pedido = mysqli_fetch_assoc($val);
            while ($artped = mysqli_fetch_array($val2)) {
                array_push($productos, [
                    "id_artpedido" => $artped["id_artpedido"],
                    "id_articulo" => $artped["id_articulo"],
                    "id_variedad" => $artped["id_articulo"],
                    "id_tipo" => $artped["id_tipo"],
                    "id_subtipo" => $artped["id_subtipo"],
                    "cant_plantas" => $artped["cant_plantas"],
                    "cant_band" => $artped["cant_band"],
                    "cant_semi" => $artped["cant_semi"],
                    "bandeja" => $artped["bandeja"],
                    "fecha_entrega" => $artped["fecha_entrega"],
                    "fecha_siembraestimada" => $artped["fecha_siembraestimada"],
                    "id_pedido" => $artped["id_pedido"],
                    "con_semilla" => $artped["con_semilla"],
                    "subtipo" => $artped["nombre_subtipo"],
                    "variedad" => $artped["nombre_variedad"],
                    "producto" => $artped["nombre_variedad"],
                    "tipo" => $artped["nombre_tipo"],
                ]);
            }

            echo json_encode([
                "pedido" => [
                    "id_pedido" => $pedido["ID_PEDIDO"],
                    "id_cliente" => $pedido["ID_CLIENTE"],
                    "fecha" => $pedido["FECHA"],
                    "observaciones" => $pedido["observaciones"],
                ],
                "productos" => $productos,
            ]);
        }
    }
}
