<?php
include "class_lib/sesionSecurity.php";
require 'class_lib/class_conecta_mysql.php';
include "class_lib/funciones.php";
$con = mysqli_connect($host, $user, $password, $dbname);
// Check connection
if (!$con) {
    die("Connection failed: " . mysqli_connect_error());
}
mysqli_query($con, "SET NAMES 'utf8'");
$consulta = $_POST['consulta'];
if ($consulta == "busca_entregas") {
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

    $cadena = "SELECT
  			'PED' as tipo,
			o.id_orden_alternativa,
			p.id_artpedido,
			DATE(e.fecha) as fecha_pedido,
			t.nombre as nombre_tipo,
			s.nombre as nombre_subtipo,
			v.nombre as nombre_variedad,
			p.cant_band,
			p.bandeja,
			c.nombre as cliente,
			c.id_cliente,
			p.estado,
			e.cantidad,
			DATE_FORMAT(e.fecha, '%d/%m/%Y %H:%i') as fecha_entrega,
			DATE_FORMAT(e.fecha, '%Y%m%d%H%i %d/%m/%Y %H:%i') as fecha_entrega_sort,
			e.actualizado,
			e.id_entrega
  FROM variedades_producto v
  INNER JOIN subtipos_producto s ON v.id_subtipo = s.id_articulo
  INNER JOIN tipos_producto t ON t.id_articulo = s.id_tipo
  INNER JOIN articulospedidos p ON p.id_articulo = v.id_articulo
  INNER JOIN pedidos pe ON p.id_pedido = pe.id_pedido
  INNER JOIN clientes c ON pe.id_cliente = c.id_cliente
  LEFT JOIN ordenes_siembra o ON o.id_artpedido = p.id_artpedido
  INNER JOIN entregas e ON e.id_artpedido = p.id_artpedido
  WHERE (e.fecha >= '$fechai' AND ";
    if ($fechaf == "NOW()") {
        $cadena .= "e.fecha <= NOW()) AND p.estado IN (6,7) ";
    } else {
        $cadena .= " e.fecha <= '$fechaf') AND p.estado IN (6,7) ";
    }
    $cadena .= "
	UNION

	SELECT
  			sb.tipo_stock as tipo,
			o.id_orden_alternativa,
			sb.id_artpedido,
			DATE(es.fecha) as fecha_pedido,
			t.nombre as nombre_tipo,
			s.nombre as nombre_subtipo,
			v.nombre as nombre_variedad,
			rp.cantidad as cant_band,
			sb.tipo_bandeja as bandeja,
			c.nombre as cliente,
			c.id_cliente,
			7 as estado,
			rp.cantidad,
			DATE_FORMAT(es.fecha, '%d/%m/%Y %H:%i') as fecha_entrega,
			DATE_FORMAT(es.fecha, '%Y%m%d%H%i %d/%m/%Y %H:%i') as fecha_entrega_sort,
			NULL as actualizado,
			es.id_entrega
  FROM variedades_producto v
  INNER JOIN subtipos_producto s ON v.id_subtipo = s.id_articulo
  INNER JOIN tipos_producto t ON t.id_articulo = s.id_tipo
  INNER JOIN stock_bandejas sb ON sb.id_variedad = v.id_articulo
  INNER JOIN reservas_productos rp ON rp.id_stock = sb.id_stock
  INNER JOIN reservas r ON r.rowid = rp.id_reserva
  INNER JOIN clientes c ON r.id_cliente = c.id_cliente
  LEFT JOIN ordenes_siembra o ON o.id_artpedido = sb.id_artpedido
  INNER JOIN entregas_stock es ON es.id_reserva_producto = rp.rowid
  WHERE (es.fecha >= '$fechai' AND ";
    if ($fechaf == "NOW()") {
        $cadena .= "es.fecha <= NOW())";
    } else {
        $cadena .= " es.fecha <= '$fechaf');";
    }

    $val = mysqli_query($con, $cadena);
    if (mysqli_num_rows($val) > 0) {
        echo "<div class='box box-primary'>";
        echo "<div class='box-header with-border'>";
        echo "<h3 class='box-title'>Entregas</h3>";
        echo "</div>";
        echo "<div class='box-body'>";
        echo "<table id='tabla' class='table table-bordered table-responsive w-100 d-block d-md-table'>";
        echo "<thead>";
        echo "<tr>";
        echo "<th>Orden<br>Siembra</th><th>Producto</th><th>Cliente</th><th>Cantidad<br>Entregada</th><th>Origen</th><th>Fecha Entrega</th><th>Estado</th>";
        echo "</tr>";
        echo "</thead>";
        echo "<tbody>";

        while ($ww = mysqli_fetch_array($val)) {
            $producto = $ww['nombre_tipo'] . " " . $ww['nombre_subtipo'] . " " . $ww['nombre_variedad'] . " x" . $ww['bandeja'];
            $cliente = $ww['cliente'];
            $cant_band = $ww['cantidad'];
            $fecha_entrega = $ww['fecha_entrega'];
            $id_orden = $ww['id_orden_alternativa'];
            if ($id_orden != null) {
                $tipo = strtoupper(substr($ww["nombre_tipo"], 0, 3));
            } else {
                $tipo = "";
            }

            $origen = "";
            if ($ww["tipo"] == "PED") {
                $origen = "INVERNÁCULO";
            } else if ($ww["tipo"] == "SOB") {
                $origen = "<span class='text-primary'>[STOCK]</span> SOBRANTE ORDEN $ww[id_orden_alternativa]";
            } else if ($ww["tipo"] == "DEV") {
                $origen = "<span class='text-primary'>[STOCK]</span> DEVOLUCIÓN ORDEN $ww[id_orden_alternativa]";
            } else if ($ww["tipo"] == "IM") {
                $origen = "<span class='text-primary'>[STOCK]</span> CARGADO MANUALMENTE";
            } else if ($ww["tipo"] == "ENV") {
                $origen = "<span class='text-primary'>[STOCK]</span> PEDIDO ENVIADO A STOCK";
            }

            $estado = generarBoxEstado($ww["estado"], true);
            echo "<tr onClick='mostrarInfoEntregas($ww[id_entrega], \"$ww[tipo]\")' style='cursor:pointer;'>";
            echo "<td style='text-align: center; font-size:1.0em; font-weight:bold;'>
            <span style='font-size:1.2em;'>$id_orden</span><br><span style='color: blue;font-size:1.2em'>$tipo</span>
            </td>";
            echo "<td class='cell-normal'>$producto</td>";
            echo "<td class='cell-normal'>$cliente</td>";
            echo "<td style='font-size:1.4em;font-weight:bold;text-align: center;'>$cant_band</td>";
            echo "<td class='cell-normal' style='text-align: center;'>$origen</td>";
            echo "<td class='cell-normal' style='text-align: center;'><span style='display:none;'>$ww[fecha_entrega_sort]</span>$fecha_entrega</td>";
            echo "<td class='text-center'>$estado</td>";
            echo "</tr>";
        }
        echo "</tbody>";
        echo "</table>";
        echo "</div>";
        echo "</div>";
    } else {
        echo "<div class='callout callout-danger'><b> No se encontraron entregas...</b></div>";
    }
} else if ($consulta == "cargar_entregas") {
    $id = $_POST["id"];
    $tipo = $_POST["tipo"];

    if ($tipo == "PED") {
        $cadena = "SELECT
        'PED' as tipo,
        o.id_orden_alternativa,
        p.id_artpedido,
        DATE(e.fecha) as fecha_pedido,
        e.fecha as fechita,
        p.id_pedido,
        t.nombre as nombre_tipo,
        s.nombre as nombre_subtipo,
        v.nombre as nombre_variedad,
        p.cant_band,
        p.bandeja,
        c.nombre as cliente,
        c.id_cliente,
        p.estado,
        e.cantidad,
        DATE_FORMAT(e.fecha, '%d/%m/%Y %H:%i') as fecha_entrega,
        e.id_entrega,
        o.cant_band_reales
        FROM variedades_producto v
        INNER JOIN subtipos_producto s ON v.id_subtipo = s.id_articulo
        INNER JOIN tipos_producto t ON t.id_articulo = s.id_tipo
        INNER JOIN articulospedidos p ON p.id_articulo = v.id_articulo
        INNER JOIN pedidos pe ON p.id_pedido = pe.id_pedido
        INNER JOIN clientes c ON pe.id_cliente = c.id_cliente
        INNER JOIN ordenes_siembra o ON o.id_artpedido = p.id_artpedido
        INNER JOIN entregas e ON e.id_artpedido = p.id_artpedido
        WHERE p.id_artpedido = (SELECT id_artpedido FROM entregas WHERE id_entrega = $id)";
    } else {
        $cadena = "SELECT
            sb.tipo_stock as tipo,
          o.id_orden_alternativa,
          sb.id_artpedido,
          DATE(es.fecha) as fecha_pedido,
          t.nombre as nombre_tipo,
          s.nombre as nombre_subtipo,
          v.nombre as nombre_variedad,
          rp.cantidad as cant_band,
          sb.tipo_bandeja as bandeja,
          c.nombre as cliente,
          c.id_cliente,
          7 as estado,
          rp.cantidad,
          DATE_FORMAT(es.fecha, '%d/%m/%Y %H:%i') as fecha_entrega,
          DATE_FORMAT(es.fecha, '%Y%m%d%H%i %d/%m/%Y %H:%i') as fecha_entrega_sort,
          NULL as actualizado,
          es.id_entrega
            FROM variedades_producto v
            INNER JOIN subtipos_producto s ON v.id_subtipo = s.id_articulo
            INNER JOIN tipos_producto t ON t.id_articulo = s.id_tipo
            INNER JOIN stock_bandejas sb ON sb.id_variedad = v.id_articulo
            INNER JOIN reservas_productos rp ON rp.id_stock = sb.id_stock
            INNER JOIN reservas r ON r.rowid = rp.id_reserva
            INNER JOIN clientes c ON r.id_cliente = c.id_cliente
            LEFT JOIN ordenes_siembra o ON o.id_artpedido = sb.id_artpedido
            INNER JOIN entregas_stock es ON es.id_reserva_producto = rp.rowid
            WHERE es.id_entrega = $id";
    }
    $salida = "";
    $val = mysqli_query($con, $cadena);
    $cant_entregada = 0;
    $salida .= '<table style="margin-top: 15px;" id="tabla_entregas" class="table table-bordered table-responsive w-100 d-block d-md-table" role="grid">
              <thead>
              <tr role="row">
                <th class="text-center">Producto</th>
                <th class="text-center">Cantidad<br>Entregada</th>
                <th class="text-center">Origen</th>
                <th class="text-center">Fecha Entrega</th>
                <th class="text-center"></th>
              </tr>
              </thead>
              <tbody>';
    if (mysqli_num_rows($val) > 0) {
        while ($re = mysqli_fetch_array($val)) {
            $producto = "$re[nombre_tipo] $re[nombre_subtipo] $re[nombre_variedad] x$re[bandeja]";

            $cant_entregada += (int) $re["cantidad"];
            $codigo = "<h4>Orden Siembra Origen: $re[id_orden_alternativa] ($re[nombre_tipo])</h4>
					   <h4>Cliente: $re[cliente] ($re[id_cliente])</h4>
					   <h4>Cantidad Total Entregada: $cant_entregada</h4>";
            $codigo .= "<h4 style='color:green'>Falta Entregar: 0</h4>";
            if ($re["tipo"] == "SOB") {
                $origen = "<span class='text-primary'>[STOCK]</span> SOBRANTE ORDEN $re[id_orden_alternativa]";
            } else if ($re["tipo"] == "DEV") {
                $origen = "<span class='text-primary'>[STOCK]</span> DEVOLUCIÓN ORDEN $re[id_orden_alternativa]";
            } else if ($re["tipo"] == "IM") {
                $origen = "<span class='text-primary'>[STOCK]</span> CARGADO MANUALMENTE";
            } else if ($re["tipo"] == "ENV") {
                $origen = "<span class='text-primary'>[STOCK]</span> PEDIDO ENVIADO A STOCK";
            }
            $salida .= "
					<tr>
						<td style='word-wrap:break-word;'>$producto</td>
						<td style='text-align:center;font-weight:bold;font-size:18px;'>$re[cantidad]</td>
						<td style='word-wrap:break-word;text-align:center;'>$origen</td>
						<td style='word-wrap:break-word;text-align:center;'>$re[fecha_entrega]</td>
						<td style='text-align:center;'>
							<button class='btn btn-danger btn-sm fa fa-times' onClick='cancelarEntrega($re[id_entrega], $re[cantidad], \"$re[tipo]\")'></button>
						</td>
					</tr>";
        }
    }
    $salida .= "</tbody></table>";
    echo $codigo . $salida;
}  else if ($consulta == "eliminar_entrega") {
    $tipo = $_POST["tipo"];
    $cantidad = $_POST["cantidad"];
    $id_entrega = $_POST["id_entrega"];
    $mesada = $_POST["mesada"];
    $errors = array();
    if ($tipo == "PED") {
        $queryselect = "SELECT * FROM entregas WHERE id_artpedido = $id_artpedido AND id_entrega != $id_entrega;";
        $val = mysqli_query($con, $queryselect);
        mysqli_autocommit($con, false);
        if (mysqli_num_rows($val) == 0) {
            $query = "UPDATE articulospedidos SET estado = 4 WHERE id_artpedido = (SELECT id_artpedido FROM entregas WHERE id_entrega = $id_entrega);";
            if (!mysqli_query($con, $query)) {
                $errors[] = mysqli_error($con);
            }
        }
        $query = "DELETE FROM entregas WHERE id_entrega = $id_entrega;";
        if (!mysqli_query($con, $query)) {
            $errors[] = mysqli_error($con);
        }
        
        $query = "DELETE FROM remitos WHERE id_remito = (SELECT id_remito FROM entregas WHERE id_entrega = $id_entrega)";
        if (!mysqli_query($con, $query)) {
            $errors[] = mysqli_error($con);
        }
        

        $query = "INSERT INTO ordenes_mesadas (id_orden, id_mesada, cantidad, cantidadinicial, id_variedad) VALUES (
		(SELECT id_orden FROM ordenes_siembra WHERE id_artpedido =
		(SELECT id_artpedido FROM entregas WHERE id_entrega = $id_entrega)),
		$mesada,
		$cantidad,
		$cantidad,
		(SELECT id_articulo FROM articulospedidos WHERE id_artpedido = (SELECT id_artpedido FROM entregas WHERE id_entrega = $id_entrega) ));";
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
    } else { // ES STOCK
        $queryselect = "SELECT * FROM stock_bandejas WHERE id_stock = (SELECT id_stock FROM entregas_stock WHERE id_entrega = $id_entrega);";
        $val = mysqli_query($con, $queryselect);
        if (mysqli_num_rows($val) > 0) {
            $ww = mysqli_fetch_assoc($val);
            mysqli_autocommit($con, false);
            $query = "DELETE FROM remitos WHERE id_remito = (SELECT id_remito FROM entregas WHERE id_entrega = $id_entrega)";
            if (!mysqli_query($con, $query)) {
                $errors[] = mysqli_error($con);
            }

            $query = "DELETE FROM entregas_stock WHERE id_entrega = $id_entrega;";
            if (!mysqli_query($con, $query)) {
                $errors[] = mysqli_error($con);
            }
            $id_variedad = $ww["id_variedad"];
            $tipo_bandeja = $ww["tipo_bandeja"];
            $tipo_stock = $ww["tipo_stock"];
            // TENGO Q ARREGLAR ESTO
            $id_artpedido_stock = is_null($ww["id_artpedido"]) ? 'NULL' : $ww["id_artpedido"];
            $query = "INSERT INTO stock_bandejas (
            id_artpedido,
            id_mesada,
            cantidad,
            cantidad_original,
            tipo_stock,
            id_variedad,
            tipo_bandeja,
            fecha_stock

        ) VALUES (
            $id_artpedido_stock,
            '$mesada',
            $cantidad,
            $cantidad,
            '$tipo_stock', 
            $id_variedad,
            '$tipo_bandeja',
            NOW()
        )";
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
        }
    }
    mysqli_close($con);
} else if ($consulta == "pedido") {
    $id_artpedido = $_POST['id'];
    $cadena = "SELECT p.id_artpedido as id_articulo, v.id_articulo as id_variedad, t.id_articulo
	as id_tipo, t.nombre as nombre_tipo, s.nombre as nombre_subtipo, v.nombre as nombre_variedad,
	p.cant_plantas as cant_plantas, p.bandeja as bandeja, p.cant_semi as cant_semi, p.cant_band as
	cant_band, p.fecha_entrega as fecha_entrega, p.fecha_planificacion as
	fecha_siembra, p.con_semilla, p.estado, UPPER(p.cod_sobre) as codigo, o.id_orden_alternativa, o.id_orden,
	 o.cant_band_reales, DATE_FORMAT(o.fecha_camara_in, '%d/%m/%Y %H:%i') as fecha_camara, DATE_FORMAT(o.fecha_siembra, '%d/%m/%Y %H:%i') as fecha_sembrado,
	 DATE_FORMAT(o.fecha_mesada_in, '%d/%m/%Y %H:%i') as fechamesada, UPPER(pe.observaciones) as observaciones,
	 GROUP_CONCAT(DISTINCT(om.id_mesada) SEPARATOR ', ') as id_mesada, (SELECT SUM(monto) FROM pagos WHERE id_pedido = (SELECT id_pedido FROM articulospedidos WHERE id_artpedido = $id_artpedido)) as pagos
	FROM variedades_producto v

	INNER JOIN subtipos_producto s ON v.id_subtipo = s.id_articulo
	INNER JOIN tipos_producto t ON t.id_articulo = s.id_tipo
	INNER JOIN articulospedidos p ON p.id_articulo = v.id_articulo
	INNER JOIN pedidos pe ON pe.id_pedido = p.id_pedido
	LEFT JOIN ordenes_siembra o ON p.id_artpedido = o.id_artpedido
    LEFT JOIN ordenes_mesadas om ON o.id_orden = om.id_orden
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

            $estado = generarBoxEstado($re["estado"], false);
            $mesada = "";

            if (!is_null($re['id_mesada'])) {
                if (strpos($re['id_mesada'], ',') !== false) {
                    $mesada = "<div align='right'><span style='font-weight:bold;font-size: 38px;color:green;'>MESADAS Nº " . $re['id_mesada'] . "</span></div>";
                } else {
                    $mesada = "<div align='right'><span style='font-weight:bold;font-size: 38px;color:green;'>MESADA Nº " . $re['id_mesada'] . "</span></div>";
                }
            }

            if ($re['estado'] >= 0 && $re['estado'] <= 3) {
                $band_pedidas = "<h4>Bandejas Pedidas: <span id='bandejaspedidas_original'>$re[cant_band]</span> <button style='font-size:14px' onClick='ModificarCantidadPedida($id_artpedido);'><i class='fa fa-edit'></i>  Modificar</button></h4>";
            } else {
                $band_pedidas = "<h4>Bandejas Pedidas: $re[cant_band]</h4>";
            }

            $salida .= "<div class='infoproducto'>
							<h4>Orden Nº: <span id='ordenreal_$re[id_orden]' class='id_ordenreal'>$re[id_orden_alternativa]</span></h4>
							<h4>Producto: $producto</h4>
							<h4>Bandejas Sembradas: <span id='cantidad_bandejas'>$re[cant_band_reales]</span></h4>
							$band_pedidas
							<h4>Plantas: $re[cant_plantas]</h4>
							<h4>Semillas: $re[cant_semi]</h4>
							<h4>Se sembró el día: $re[fecha_sembrado]</h4>
							<h4>Ingresó a Cámara el día: $re[fecha_camara]</h4>";
            if (!is_null($re['fechamesada'])) {
                $salida .= "<h4>Ingresó a Invernáculo el día: $re[fechamesada]</h4> ";
            }

            $salida .= "<h4>Fecha Entrega Solicitada: $re[fecha_entrega]</h4>
						   	<h4 id='estado_txt'>Estado: $estado</h4>
						   	<h4>Observaciones:</h4>
						   	<textarea name='textarea' disabled='true' id='observaciones_txt' type='text' style='width:50%;resize:none'>$re[observaciones]</textarea>
						   	";
            $salida .= "$mesada</div>";
        }
        echo $salida;
        echo $re['observaciones'];
    }
} else if ($consulta == "cliente") {
    $id_artpedido = $_POST['id'];
    $cadena = "SELECT c.id_cliente as id_cliente, c.NOMBRE as nombre, ap.estado FROM articulospedidos ap INNER JOIN pedidos p ON ap.id_pedido = p.id_pedido INNER JOIN clientes c ON c.id_cliente = p.id_cliente WHERE ap.id_artpedido = $id_artpedido;";
    $val = mysqli_query($con, $cadena);
    $salida = "";

    if (mysqli_num_rows($val) > 0) {
        while ($re = mysqli_fetch_array($val)) {
            $salida = "<span id='nombrecliente_label'>$re[nombre]</span> (<span id='id_clienteoriginal'>$re[id_cliente]</span>) <button style='font-size:18px' onClick='cambiarCliente($id_artpedido, $re[estado]);'><i class='fa fa-edit'></i> Modificar/Enviar a Stock</button>";
        }
    }
    echo $salida;
}  else if ($consulta == "cargar_pagos") {
    $id_artpedido = $_POST['id_artpedido'];
    $cadena = "SELECT DATE_FORMAT(p.fecha, '%d/%m/%Y %H:%i') as fecha, p.monto, p.id_pago, p.concepto FROM pagos p WHERE id_pedido = (SELECT id_pedido FROM articulospedidos WHERE id_artpedido = $id_artpedido)
		ORDER BY p.id_pago DESC;";

    $val = mysqli_query($con, $cadena);
    $salida = "";

    if (mysqli_num_rows($val) > 0) {
        while ($re = mysqli_fetch_array($val)) {
            $salida .= "<tr>
					    <td style='word-wrap:break-word;'>$re[fecha]</td>
						<td style='word-wrap:break-word;'>$re[concepto]</td>
						<td style='word-wrap:break-word;' class='text-right'>$re[monto]</td>
						<td><div align='center'><button id='pago_$re[id_pago]' class='removeme btn btn-sm btn-danger btn-modal-top fa fa-edit' onClick='modificar_pago(this)'></button></div></td>
						<td><div align='center'><button id='print_$re[id_pago]' class='btn btn-sm btn-primary   fa fa-print btn-modal-top' onClick='print_pago(1, this)'></button></div></td>
					</tr>";
        }
        echo $salida;
    }
}
