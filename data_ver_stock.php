<?php
include "class_lib/sesionSecurity.php";
require 'class_lib/class_conecta_mysql.php';
require 'class_lib/funciones.php';

$con = mysqli_connect($host, $user, $password, $dbname);
// Check connection
if (!$con) {
    die("Connection failed: " . mysqli_connect_error());
}

mysqli_query($con, "SET NAMES 'utf8'");

$consulta = $_POST["consulta"];

if ($consulta == "busca_stock") {
    $filtro_tipo = "";
    if ($_POST["id_tipo"] != NULL && strlen($_POST["id_tipo"]) > 0) {
        $filtro_tipo = " AND t.id_articulo = $_POST[id_tipo]";
    }

    $cadena = "SELECT
    t.nombre as nombre_tipo,
    s.nombre as nombre_subtipo,
    v.nombre as nombre_variedad,
    t.id_articulo as id_tipo,
	sb.tipo_bandeja,
    sb.cantidad,
    IFNULL(SUM(rp.cantidad),0) as cantidad_reservada,
    v.id_articulo AS id_variedad,
  GROUP_CONCAT(DISTINCT(sb.id_mesada) SEPARATOR ', ') as id_mesada,
  DATE_FORMAT(sb.fecha_stock, '%d/%m/%y %H:%i') as fecha_stock,
  DATE_FORMAT(sb.fecha_stock, '%Y%m%d%H%i') as fecha_stock_sort,
  sb.id_stock,
  sb.tipo_stock as tipo,
  NULL as id_artpedido,
  NULL as id_orden,
  NULL as fecha_siembra,
  NULL as fecha_siembra_sort,
  NULL as fecha_entrega
    FROM variedades_producto v
    INNER JOIN subtipos_producto s ON v.id_subtipo = s.id_articulo
    INNER JOIN tipos_producto t ON t.id_articulo = s.id_tipo
    INNER JOIN stock_bandejas sb ON sb.id_variedad = v.id_articulo
    LEFT JOIN reservas_productos rp ON rp.id_stock = sb.id_stock
    WHERE sb.tipo_stock = 'IM' $filtro_tipo
    GROUP BY sb.id_stock

    UNION

    SELECT
    t.nombre as nombre_tipo,
    s.nombre as nombre_subtipo,
    v.nombre as nombre_variedad,
    t.id_articulo as id_tipo,
	sb.tipo_bandeja,
    sb.cantidad,
    IFNULL(SUM(rp.cantidad),0) as cantidad_reservada,
    v.id_articulo AS id_variedad,
  GROUP_CONCAT(DISTINCT(sb.id_mesada) SEPARATOR ', ') as id_mesada,
  DATE_FORMAT(sb.fecha_stock, '%d/%m/%y %H:%i') as fecha_stock,
  DATE_FORMAT(sb.fecha_stock, '%Y%m%d%H%i') as fecha_stock_sort,
  sb.id_stock,
  sb.tipo_stock as tipo,
  p.id_artpedido,
  o.id_orden_alternativa as id_orden,
  DATE_FORMAT(o.fecha_siembra, '%d/%m/%y %H:%i') as fecha_siembra,
  DATE_FORMAT(o.fecha_siembra, '%Y%m%d%H%i') as fecha_siembra_sort,
  p.fecha_entrega
    FROM variedades_producto v
    INNER JOIN subtipos_producto s ON v.id_subtipo = s.id_articulo
    INNER JOIN tipos_producto t ON t.id_articulo = s.id_tipo
    INNER JOIN stock_bandejas sb ON sb.id_variedad = v.id_articulo
    INNER JOIN articulospedidos p ON p.id_artpedido = sb.id_artpedido
    INNER JOIN ordenes_siembra o ON o.id_artpedido = p.id_artpedido
    LEFT JOIN reservas_productos rp ON rp.id_stock = sb.id_stock
    WHERE sb.tipo_stock IN ('DEV', 'ENV', 'SOB') $filtro_tipo
    GROUP BY sb.id_stock
    ";

    $val = mysqli_query($con, $cadena);

    if (mysqli_num_rows($val) > 0) {
        echo "<div class='box box-primary'>";
        echo "<div class='box-header with-border'>";
        echo "<div class='row'>
  <div class='col'>
    <span class='box-title'>Stock</span>
  </div>

 </div>
 ";

        echo "</div>";
        echo "<div class='box-body'>";
        echo "<table class='table table-bordered table-responsive w-100 d-block d-md-table tabla-stock'>";
        echo "<thead>";
        echo "<tr>";
        echo "<th>Producto</th><th>Fecha Stock</th><th>Cantidad</th><th>Origen</th><th>Mesada</th><th>Fecha Entrega</th>";
        echo "</tr>";
        echo "</thead>";
        echo "<tbody>";
    } else {
        echo "<div class='callout callout-danger'><b>No se encontraron bandejas en Stock</b></div>";
    }

    if (mysqli_num_rows($val) > 0) {

        while ($ww = mysqli_fetch_array($val)) {
            if ($ww["cantidad"] - $ww["cantidad_reservada"] > 0) {
                $id_artpedido = $ww['id_artpedido'];
                $producto = $ww['nombre_tipo'] . " " . $ww['nombre_subtipo'] . " " . $ww['nombre_variedad'] . " x" . $ww['tipo_bandeja'];
                $cant_band = $ww['cantidad'] - $ww["cantidad_reservada"];
                $id_orden = $ww['id_orden'];

                $today = date("Y-m-d");

                $fecha_entrega_sort = "";

                if ($ww["fecha_entrega"] != NULL){
                    $ftmp = explode("/", $ww["fecha_entrega"]);
                    $fecha_entrega_sort = $ftmp[2].$ftmp[1].$ftmp0;
                }
                
                if ($ww["tipo"] == "SOB") {
                    $origen = "SOBRANTE ORDEN $id_orden";
                } else if ($ww["tipo"] == "DEV") {
                    $origen = "DEVOLUCIÓN ORDEN $id_orden";
                } else if ($ww["tipo"] == "IM") {
                    $origen = "CARGADO MANUALMENTE";
                } else if ($ww["tipo"] == "ENV") {
                    $origen = "PEDIDO ENVIADO A STOCK<br>(ORD. SIEMBRA $ww[id_orden])";
                }

                if ($ww["tipo"] == 1 && $today > $fechaentrega) {
                    $fondo = "background-color:#FA5858";
                } else {
                    $fondo = "";
                }

                echo "<tr id='row_$ww[rowid]' x-id='$ww[id_stock]' x-producto='$producto' x-cant='$cant_band' x-origen='$origen' x-origen-short='$ww[tipo]' x-mesada='$ww[id_mesada]' onclick='toggleselection(this)' style='cursor:pointer;$fondo'>";

                echo "<td>$producto</td>";
                echo "<td class='text-center'><span class='d-none'>$ww[fecha_stock_sort]</span>$ww[fecha_stock]</td>";

                echo "<td style='font-size:16px;font-weight:bold;text-align: center;'>$cant_band</td>";
                echo "<td style='text-align: center;'>$origen</td>";

                if ($ww["id_mesada"] != NULL){
                    $mesada = "<span class='font-weight-bold'>$ww[id_mesada]</span> <button class='d-inline-block btn btn-sm ml-2 btn-primary fa fa-edit' onclick='cambiarMesada($ww[id_stock], $cant_band)'></button>";
                }
                echo "<td style='text-align: center;'>$mesada</td>";
                echo "<td style='text-align: center;'><span style='display:none;'>$fecha_entrega_sort]</span>$ww[fecha_entrega]</td>";
                echo "</tr>";
            }
        }
    }

    echo "</tbody>";
    echo "</table>";
    echo "</div>";
    echo "</div>";

} else if ($consulta == "busca_reservas") {
    $cadena = "SELECT *,
    id_reserva,
    c.id_cliente,
    c.nombre as nombre_cliente,
    DATE_FORMAT(r.fecha, '%d/%m/%y %H:%i') as fecha,
    DATE_FORMAT(r.fecha, '%Y%m%d%H%i') as fecha_sort
    FROM (
        SELECT
            t.nombre as nombre_tipo,
            s.nombre as nombre_subtipo,
            v.nombre as nombre_variedad,
            t.id_articulo as id_tipo,
            sb.tipo_bandeja,
            sb.cantidad,
            IFNULL(SUM(rp.cantidad),0) as cantidad_reservada,
            v.id_articulo AS id_variedad,
          GROUP_CONCAT(DISTINCT(sb.id_mesada) SEPARATOR ', ') as id_mesada,
          sb.id_stock,
          sb.tipo_stock as tipo,
          NULL as id_artpedido,
          NULL as id_orden,
          NULL as fecha_siembra,
          NULL as fecha_siembra_sort
            FROM variedades_producto v
            INNER JOIN subtipos_producto s ON v.id_subtipo = s.id_articulo
            INNER JOIN tipos_producto t ON t.id_articulo = s.id_tipo
            INNER JOIN stock_bandejas sb ON sb.id_variedad = v.id_articulo
            LEFT JOIN reservas_productos rp ON rp.id_stock = sb.id_stock
            WHERE sb.tipo_stock = 'IM'
            GROUP BY sb.id_stock

            UNION

            SELECT
            t.nombre as nombre_tipo,
            s.nombre as nombre_subtipo,
            v.nombre as nombre_variedad,
            t.id_articulo as id_tipo,
            sb.tipo_bandeja,
            sb.cantidad,
            IFNULL(SUM(rp.cantidad),0) as cantidad_reservada,
            v.id_articulo AS id_variedad,
          GROUP_CONCAT(DISTINCT(sb.id_mesada) SEPARATOR ', ') as id_mesada,
          sb.id_stock as id_stock,
          sb.tipo_stock as tipo,
          p.id_artpedido,
          o.id_orden_alternativa as id_orden,
          DATE_FORMAT(o.fecha_siembra, '%d/%m/%y %H:%i') as fecha_siembra,
          DATE_FORMAT(o.fecha_siembra, '%Y%m%d%H%i') as fecha_siembra_sort
            FROM variedades_producto v
            INNER JOIN subtipos_producto s ON v.id_subtipo = s.id_articulo
            INNER JOIN tipos_producto t ON t.id_articulo = s.id_tipo
            INNER JOIN stock_bandejas sb ON sb.id_variedad = v.id_articulo
            INNER JOIN articulospedidos p ON p.id_artpedido = sb.id_artpedido
            INNER JOIN ordenes_siembra o ON o.id_artpedido = p.id_artpedido
            LEFT JOIN reservas_productos rp ON rp.id_stock = sb.id_stock
            WHERE sb.tipo_stock IN ('DEV', 'ENV', 'SOB')
            GROUP BY sb.id_stock
        ) as t1
        INNER JOIN reservas_productos rp ON rp.id_stock = t1.id_stock
        INNER JOIN reservas r ON r.rowid = rp.id_reserva
        INNER JOIN clientes c ON c.id_cliente = r.id_cliente
        WHERE rp.entregado != 1
        ";

    $val = mysqli_query($con, $cadena);
    $arraypedidos = [];
    if (mysqli_num_rows($val) > 0) {
        echo "<div class='box box-primary'>";
        echo "<div class='box-header with-border'>";
        echo "<div class='row'>
              <div class='col'>
                <span class='box-title'>Bandejas Reservadas</span>
              </div>
              </div>";
        echo "</div>";
        echo "<div class='box-body'>";
        echo "<table class='table table-bordered table-responsive w-100 d-block d-md-table tabla-stock'>";
        echo "<thead>";
        echo "<tr>";
        echo "
            <th>ID Reserva</th>
            <th>Producto</th>
            <th>Fecha</th>
            <th>Cantidad</th>
            <th>Cliente</th>
            <th>Origen</th>
            <th>Mesada</th>
            <th></th>
            ";
        echo "</tr>";
        echo "</thead>";
        echo "<tbody>";

        while ($ww = mysqli_fetch_array($val)) {
            $producto = $ww['nombre_tipo'] . " " . $ww['nombre_subtipo'] . " " . $ww['nombre_variedad'] . " x" . $ww['tipo_bandeja'];
            echo "<tr class='text-center'>";
            if (in_array($ww["id_reserva"], $arraypedidos)) {
                echo "<td style='color:#1F618D;font-size:0.7em;'>$ww[id_reserva]</td>";
            } else {
                echo "<td style='color:#1F618D; font-weight:bold; font-size:1.0em;'>$ww[id_reserva]</td>";
            }

            echo "<td>$producto</td>";
            if (in_array($ww["id_reserva"], $arraypedidos)) {
                echo "<td></td>";
            } else {
                echo "<td class='text-center'><span class='d-none'>$ww[fecha_sort]</span>$ww[fecha]</td>";
            }
            echo "<td style='font-size:16px;font-weight:bold;'>$ww[cantidad]</td>";
            if (in_array($ww["id_reserva"], $arraypedidos)) {
                echo "<td></td>";
            } else {
                echo "<td>$ww[nombre_cliente] ($ww[id_cliente])</td>";
            }

            if ($ww["tipo"] == "SOB") {
                $origen = "SOBRANTE ORDEN $ww[id_orden]";
            } else if ($ww["tipo"] == "DEV") {
                $origen = "DEVOLUCIÓN ORDEN $ww[id_orden]";
            } else if ($ww["tipo"] == "IM") {
                $origen = "CARGADO MANUALMENTE";
            } else if ($ww["tipo"] == "ENV") {
                $origen = "PEDIDO ENVIADO A STOCK";
            }
            echo "<td>$origen</td>";
            echo "<td>$ww[id_mesada]</td>";
            if (in_array($ww["id_reserva"], $arraypedidos)) {
                echo "<td></td>";
            } else {
                echo "
                <td class='d-flex flex-row'>
                    <button onclick='eliminar_reserva($ww[id_reserva])' class='btn btn-danger btn-sm fa fa-trash'></button>
                    <button onclick='entregar_reserva($ww[id_reserva])' class='ml-2 btn btn-success btn-sm'><i class='fa fa-check'></i> ENTREGAR</button>
                </td>
                ";
            }

            echo "</tr>";
            array_push($arraypedidos, $ww["id_reserva"]);
        }
    } else {
        echo "<div class='callout callout-danger'><b>No se encontraron bandejas reservadas</b></div>";
    }

    echo "</tbody>";
    echo "</table>";
    echo "</div>";
    echo "</div>";

} else if ($consulta == "guarda_reserva") {
    $id_cliente = $_POST["id_cliente"];
    try {
        $productos = json_decode($_POST["productos"], true);
        if (mysqli_query($con, "INSERT INTO reservas (id_cliente, fecha) VALUES ($id_cliente, NOW())")) {
            $id_reserva = mysqli_insert_id($con);

            mysqli_autocommit($con, false);

            for ($i = 0; $i < count($productos); $i++) {
                $rowid = $productos[$i]["rowid"];
                $cantidad = $productos[$i]["cantidad"];
                $query = "INSERT INTO reservas_productos
                (id_stock, cantidad, id_reserva)
                VALUES
                ($rowid, $cantidad, $id_reserva)";
                mysqli_query($con, $query);
            }

            mysqli_commit($con);
            mysqli_close($con);
            echo "success";
        }

    } catch (\Throwable $th) {
        throw $th;
    }
} else if ($consulta == "eliminar_reserva") {
    try {
        $id_reserva = $_POST["id_reserva"];
        mysqli_autocommit($con, false);
        mysqli_query($con, "DELETE FROM reservas_productos WHERE id_reserva = $id_reserva;");
        mysqli_query($con, "DELETE FROM reservas WHERE id_reserva = $id_reserva;");

        mysqli_commit($con);
        mysqli_close($con);
        echo "success";
    } catch (\Throwable $th) {
        throw $th;
    }
} else if ($consulta == "entregar_reserva") {
    try {
        $id_reserva = $_POST["id_reserva"];
        $id_remito = $_POST["id_remito"];
        $lista_stock = array();
        $codigo = mysqli_real_escape_string($con, $_POST["remito"]);
        $subtotal = $_POST["subtotal"];
        $val = mysqli_query($con, "SELECT rowid FROM reservas_productos WHERE id_reserva = $id_reserva;");
        if (mysqli_num_rows($val) > 0) {
            while ($ww = mysqli_fetch_array($val)) {
                array_push($lista_stock, $ww["rowid"]);
            }
            $errors = array();
            mysqli_autocommit($con, false);

            $query = "UPDATE reservas_productos SET entregado = 1 WHERE id_reserva = $id_reserva;";
            if (!mysqli_query($con, $query)) {
                $errors[] = mysqli_error($con);
            }

            $query="INSERT INTO remitos (
                codigo, 
                id_cliente, 
                fecha, 
                total, 
                descuento
            ) VALUES (
                '$codigo',
                (SELECT id_cliente FROM reservas WHERE rowid = $id_reserva),
                NOW(), 
                $subtotal,
                NULL
            );";

            if (!mysqli_query($con, $query)) {
                $errors[] = mysqli_error($con);
            }

            for ($i = 0; $i < count($lista_stock);$i++){
                $query = "INSERT INTO entregas_stock (
                    fecha, 
                    id_remito,
                    id_reserva_producto
                ) VALUES (
                    NOW(),
                    $id_remito,
                    $lista_stock[$i]
                )";
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
        }
        mysqli_close($con);
    } catch (\Throwable $th) {
        throw $th;
    }
} else if ($consulta == "get_data_reserva") {
    $id_reserva = $_POST["id_reserva"];
    $cadena = "SELECT
    t.nombre as nombre_tipo,
    s.nombre as nombre_subtipo,
    v.nombre as nombre_variedad,
    c.id_cliente,
    c.nombre as nombre_cliente,
    c.telefono as telefono,
    r.rowid as id_reserva,
    rp.cantidad as cantidad,
    o.id_orden_alternativa as id_orden,
    p.bandeja,
    sb.tipo_bandeja,
    v.id_articulo AS id_variedad,
    sb.tipo_stock as tipo,
    GROUP_CONCAT(DISTINCT(sb.id_mesada) SEPARATOR ', ') as id_mesada,
  DATE_FORMAT(r.fecha, '%d/%m/%y %H:%i') as fecha,
  DATE_FORMAT(r.fecha, '%Y%m%d%H%i') as fecha_sort
    FROM variedades_producto v
    INNER JOIN subtipos_producto s ON v.id_subtipo = s.id_articulo
    INNER JOIN tipos_producto t ON t.id_articulo = s.id_tipo
    LEFT JOIN articulospedidos p ON p.id_articulo = v.id_articulo
    LEFT JOIN ordenes_siembra o ON o.id_artpedido = p.id_artpedido
    INNER JOIN stock_bandejas sb ON sb.id_variedad = v.id_articulo
    INNER JOIN reservas_productos rp ON rp.id_stock = sb.id_stock
    INNER JOIN reservas r ON r.rowid = rp.id_reserva
    INNER JOIN clientes c ON c.id_cliente = r.id_cliente
    WHERE r.rowid = $id_reserva
    GROUP BY rp.rowid
    ORDER BY r.rowid DESC
    ";

    $val = mysqli_query($con, $cadena);
    $arrayreserva = array();
    if (mysqli_num_rows($val) > 0) {
        while ($ww = mysqli_fetch_array($val)) {
            $producto = $ww['nombre_tipo'] . " " . $ww['nombre_subtipo'] . " " . $ww['nombre_variedad'] . " x" . ($ww['bandeja'] != NULL ? $ww["bandeja"] : $ww["tipo_bandeja"]);
            $origen = "";
            if ($ww["tipo"] == "SOB") {
                $origen = "SOBRANTE ORDEN $ww[id_orden]";
            } else if ($ww["tipo"] == "DEV") {
                $origen = "DEVOLUCIÓN ORDEN $ww[id_orden]";
            } else if ($ww["tipo"] == "IM") {
                $origen = "CARGADO MANUALMENTE";
            } else if ($ww["tipo"] == "ENV") {
                $origen = "PEDIDO ENVIADO A STOCK";
            }

            array_push($arrayreserva, array(
                "producto" => $producto,
                "cant_bandejas" => $ww["cantidad"],
                "nombre_cliente" => $ww["nombre_cliente"],
                "id_cliente" => $ww["id_cliente"],
                "telefono" => $ww["telefono"],
                "origen" => $origen,
                "mesada" => $ww["id_mesada"],
            ));
        }
        echo json_encode($arrayreserva);
    }
    else{
        echo $cadena;
    }
    
} else if ($consulta == "guardar_ingreso_manual") {
    $id_variedad = $_POST["id_variedad"];
    $tipo_bandeja = $_POST["tipo_bandeja"];
    $cant_bandejas = $_POST["cant_bandejas"];
    $mesada = $_POST["mesada"];

    try {
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
            NULL,
            '$mesada',
            $cant_bandejas,
            $cant_bandejas,
            'IM', #INGRESO MANUAL
            $id_variedad,
            $tipo_bandeja,
            NOW()
        )";
        if (mysqli_query($con, $query)) {
            echo "success";
        } else {
            echo $query;
        }
    } catch (\Throwable $th) {
        throw $th;
    }
} else if ($consulta == "get_id_remito") {
    $remito = mysqli_query($con, "SELECT (IFNULL(MAX(id_remito),0)+1) as id_remito FROM remitos;");
    if (mysqli_num_rows($remito) > 0) {
        $id_remito = mysqli_fetch_assoc($remito)["id_remito"];
        echo "id_remito:".$id_remito;
    }
}
else if ($consulta == "eliminar_stock") {
    $array = json_decode($_POST['jsonarray'], true);
    $errors = array();
    
    mysqli_autocommit($con, FALSE);

    try {
        for ($i = 0; $i < count($array); $i++) {
            $id_stock = $array[$i]["id_stock"];
            $tipo_stock = $array[$i]["tipo_stock"];

            if ($tipo_stock == "PED" || $tipo_stock == "ENV" || $tipo_stock == "SOB"){
                $query = "UPDATE articulospedidos SET estado = -1 WHERE id_artpedido = (SELECT id_artpedido FROM stock_bandejas WHERE id_stock = $id_stock)";
                if (!mysqli_query($con, $query)) {
                    $errors[] = mysqli_error($con);
                }
            }

            $query = "DELETE FROM reservas_productos WHERE id_stock = $id_stock;";
            if (!mysqli_query($con, $query)) {
                $errors[] = mysqli_error($con);
            }
            $query = "DELETE FROM stock_bandejas WHERE id_stock = $id_stock;";
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
}
else if ($consulta == "cargar_disponibles") {
    $cantidad = $_POST["cantidad"];
    if (count((string) ($cantidad)) == 0) {
        $cantidad = 0;
    }

    if ($cantidad < 0) {
        $cantidad = 0;
    }

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
    LEFT JOIN tipos_producto t ON m.id_tipo = t.id_articulo
    GROUP BY m.id_mesada ORDER BY m.id_mesada ASC";

    $val = mysqli_query($con, $cadena);
    if (mysqli_num_rows($val) > 0) {
        while ($re = mysqli_fetch_array($val)) {
            $libres = $re["capacidad"] - ((int) $re['cantidad'] + (int) $re['cantidad_stock'] - (int) $re['cantidad_reservada']);
            if ($libres >= (int)$cantidad){
                echo "<option value=$re[id_mesada]>$re[id_mesada] - Libres: $libres</option>";
            }
        }
    }
}  else if ($consulta == "buscar_pedidos") {
    $id_cliente = $_POST["id_cliente"];
    $cadena = "SELECT pe.id_pedido as id_pe, o.id_orden_alternativa, p.id_artpedido, o.cant_band_reales,
			pe.fecha as fecha_pedido, p.id_pedido, v.id_articulo as id_variedad, t.id_articulo as id_tipo,
		 	t.nombre as nombre_tipo, s.nombre as nombre_subtipo, v.nombre as nombre_variedad, IFNULL(SUM(en.cantidad), 0) as cantirestante,
		  	p.bandeja, p.fecha_entrega, c.nombre as cliente, p.con_semilla, c.id_cliente, p.estado,
		  	GROUP_CONCAT(DISTINCT(om.id_mesada) SEPARATOR ', ') as id_mesada
			FROM variedades_producto v
			INNER JOIN subtipos_producto s ON v.id_subtipo = s.id_articulo
			INNER JOIN tipos_producto t ON t.id_articulo = s.id_tipo
			INNER JOIN articulospedidos p ON p.id_articulo = v.id_articulo
			LEFT JOIN entregas en ON p.id_artpedido = en.id_artpedido
			INNER JOIN pedidos pe ON p.id_pedido = pe.id_pedido
			INNER JOIN clientes c ON pe.id_cliente = c.id_cliente
			LEFT JOIN ordenes_siembra o ON o.id_artpedido = p.id_artpedido
			LEFT JOIN ordenes_mesadas om ON om.id_orden = o.id_orden
			WHERE pe.id_cliente = $id_cliente AND p.estado = 7 AND id_mesada IS NOT NULL GROUP BY p.id_artpedido ORDER BY p.id_artpedido DESC, p.fecha_entrega DESC LIMIT 50
			";
    $val = mysqli_query($con, $cadena);
    $salida = "";

    if (mysqli_num_rows($val) > 0) {
        while ($re = mysqli_fetch_array($val)) {
            $producto = "$re[nombre_tipo] $re[nombre_subtipo] $re[nombre_variedad] x$re[bandeja]";
            if ($re['con_semilla'] == 1) {
                $producto .= " CON SEMILLA";
            }
            $estado = "";
            if ($re['estado'] == 4) {
                $estado = "<div style='text-align:center; background-color:#04B404; border-radius:6px; border-style: solid; border-color: black;border-width: 1px; padding:3px;'><span>INVERNÁCULO</span></div>";
            } else if ($re['estado'] == 5) {
                $estado = "<div style='text-align:center;background-color:#01DFD7; border-radius:6px; border-style: solid; border-color: black;border-width: 1px; padding:3px;'><span>PARA<br>ENTREGAR</span></div>";
            } else if ($re['estado'] == 6) {
                $estado = "<div style='text-align:center;background-color:#FFFF00; border-radius:6px; border-style: solid; border-color: black;border-width: 1px; padding:3px;'><span>ENTREGADO<br>PARCIALMENTE</span></div>";
            } else if ($re['estado'] == 7) {
                $estado = "<div style='text-align:center;background-color:#A9F5BC; border-radius:6px; border-style: solid; border-color: black;border-width: 1px; padding:3px;'><span>ENTREGADO<br>COMPLETAMENTE</span></div>";
            } else if ($re['estado'] == 8) {
                $estado = "<div style='word-wrap:break-word;text-align:center;background-color:#FAAC58; border-radius:6px; border-style: solid; border-color: black;border-width: 1px; padding:3px;cursor:pointer;'><span>EN STOCK</span></div>";
            } else if ($re['estado'] == -1) {
                $estado = "<div style='word-wrap:break-word;text-align:center;background-color:#FA5858; border-radius:6px; border-style: solid; border-color: black;border-width: 1px; padding:3px; cursor:pointer;'><span>CANCELADO</span></div>";
            }
            $probando = explode(", ", $re["id_mesada"]);
            $id_mesada = array_unique($probando);
            $id_mesada = implode(", ", $id_mesada);

            $id_art = $re['id_artpedido'];
            $salida .= "<tr  onClick='selectDevol($re[id_artpedido], this)' x-id-artpedido='$re[id_artpedido]' style='cursor:pointer'>
								<td style='font-size:16px;font-weight:bold;text-align:center;'>$re[id_orden_alternativa]</td>
								<td style='word-wrap:break-word;font-size:1.1em'>$producto</td>
								<td style='text-align:center;font-size:1.3em'>$re[cantirestante]</td>
								<td style='text-align:center;font-size:1.3em'>$re[fecha_entrega]</td>
								<td id='art_$re[id_artpedido]'>$estado</td>
								<td style='text-align:center;font-size:1.1em'>$re[id_pedido]</td>

							</tr>";
        }
        echo $salida;
    }
} else if ($consulta == "devolver") {
    $cantidad = $_POST["cantidad"];
    $id_artpedido = $_POST["id_artpedido"];
    $id_mesada = $_POST["id_mesada"];
    try {
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
            $id_artpedido,
            '$id_mesada',
            $cantidad,
            $cantidad,
            'DEV', #DEVOLUCION
            (SELECT id_articulo FROM articulospedidos WHERE id_artpedido = $id_artpedido),
            (SELECT bandeja FROM articulospedidos WHERE id_artpedido = $id_artpedido),
            NOW()
        )";
        if (mysqli_query($con, $query)) {
            echo "success";
        } else {
            print_r(mysqli_error($con));
        }
    } catch (\Throwable $th) {
        throw $th;
    }
}
else if ($consulta == "cambiar_mesada") {
    $id_mesada = $_POST["id_mesada"];
    $id_stock = $_POST["id_stock"];
    try {
        $query = "UPDATE stock_bandejas SET id_mesada = $id_mesada WHERE id_stock = $id_stock;";
        if (mysqli_query($con, $query)) {
            echo "success";
        } else {
            print_r(mysqli_error($con));
        }
    } catch (\Throwable $th) {
        throw $th;
    }
}
else if ($consulta == "get_cuaderno_lista") {
    $cadena = "SELECT
  pe.id_pedido as id_pe,
  p.id_artpedido,
  pe.fecha as fecha_pedido,
  DATE_FORMAT(pe.fecha, '%d/%m/%Y') AS formatted_date,
  p.id_pedido,
  v.id_articulo as id_variedad,
  t.id_articulo as id_tipo,
  t.nombre as nombre_tipo,
  s.nombre as nombre_subtipo,
  v.nombre as nombre_variedad,
  p.cant_plantas,
  p.cant_semi,
   p.cant_band, p.bandeja, p.fecha_siembraestimada, p.fecha_entrega,
   c.nombre as cliente,
   c.id_cliente
  FROM variedades_producto v
  INNER JOIN subtipos_producto s ON v.id_subtipo = s.id_articulo
  INNER JOIN tipos_producto t ON t.id_articulo = s.id_tipo
  INNER JOIN cuaderno_articulospedidos p ON p.id_articulo = v.id_articulo
  INNER JOIN cuaderno_pedidos pe ON p.id_pedido = pe.id_pedido
  INNER JOIN clientes c ON pe.id_cliente = c.id_cliente
  GROUP BY p.id_artpedido";
    
    $val = mysqli_query($con, $cadena);
    if (mysqli_num_rows($val) > 0) {
        echo "<div class='box box-primary'>";
        echo "<div class='box-header with-border'>";
        echo "<h3 class='box-title'>Pedidos</h3>";
        echo "</div>";
        echo "<div class='box-body'>";
        echo "<table id='tabla-cuaderno' class='table table-responsive w-100 d-block d-md-table'>";
        echo "<thead>";
        echo "<tr>";
        echo "
            <th>Ped</th>
            <th>Fecha</th>
            <th>Producto</th>
            <th>Cliente</th>
            <th>Cantidad<br>Pedida</th>
            <th>Fecha Entrega</th>
            <th></th>";
        echo "</tr>";
        echo "</thead>";
        echo "<tbody>";
        $array = array();

        while ($ww = mysqli_fetch_array($val)) {
            $id_cliente = $ww['id_cliente'];
            $id_pedido = $ww['id_pedido'];
            $id_artpedido = $ww['id_artpedido'];
            $fecha = $ww['formatted_date'];
            $fecha_pedido = explode("/", $fecha);
            $tipo = "";
            
            $fecha_pedido = $fecha_pedido[2] . "/" . $fecha_pedido[1] . "/" . $fecha_pedido[0];
            $producto = "<span class='hidden'>" . $ww['nombre_tipo'] . "</span> " . $ww['nombre_subtipo'] . " " . $ww['nombre_variedad'] . " x" . $ww['bandeja'];

            $cliente = $ww['cliente'];
            $cant_band = $ww['cant_band'];
            $fecha_siembra = $ww['fecha_siembraestimada'];
            $fecha_entrega = $ww['fecha_entrega'];

            $fechafull = $ww["fechafull"];
            //$estado = generarBoxEstado($ww["estado"], true);

            echo "<tr>";

            if (in_array($ww['id_pedido'], $array)) {
                echo "<td style='text-align: center; cursor:pointer; color:#1F618D;font-size:0.7em;$fondo'>$id_pedido</td>";
                echo "<td style='text-align: center;'><span style='display:none;'>" . str_replace("/", "", $fecha_pedido) . "</span><span style='display:none'" . str_replace("/20", "/", $fecha) . "</span></td>";
                echo "<td>$producto</td>";
                echo "<td><span style='display:none'>$cliente</span></td>";
            } else {
                echo "<td style='text-align: center; cursor:pointer; color:#1F618D; font-weight:bold; font-size:1.0em;'>$id_pedido</td>";
                echo "<td style='text-align: center;'><span style='display:none;'>" . str_replace("/", "", $fecha_pedido) . "</span>" . str_replace("/20", "/", $fecha) . "</td>";
                echo "<td>$producto</td>";
                echo "<td>$cliente</td>";
            }

            $fecha_entrega2 = explode("/", $fecha_entrega);
            $fecha_entrega2 = $fecha_entrega2[2] . "/" . $fecha_entrega2[1] . "/" . $fecha_entrega2[0];

            echo "<td style='text-align: center;font-weight:bold;font-size:1.2em;'>$cant_band</td>";
            echo "<td style='text-align: center;'><span style='display:none'>" . str_replace("/", "", $fecha_entrega2) . "</span>" . str_replace("/20", "/", $fecha_entrega) . "</td>";            
            
            if (!in_array($ww['id_pedido'], $array)) {
            echo "<td>
                    <div class='d-flex flex-row'>
                        <button onclick='eliminarPedidoCuaderno($id_pedido)' class='btn btn-danger btn-sm'><i class='fa fa-trash'></i></button>
                        <button onclick='editarPedido($id_pedido)' class='btn btn-primary btn-sm ml-2'><i class='fa fa-edit'></i></button>
                        <button onclick='confirmarPedidoCuaderno($id_pedido)' class='btn btn-success btn-sm ml-2'><i class='fa fa-check'></i></button>

                    </div>
                </td>";
            }
            else{
                echo "<td></td>";
            }
            echo "</tr>";

            array_push($array, $ww['id_pedido']);
        }
        echo "</tbody>";
        echo "</table>";
        echo "</div>";
        echo "</div>";
    } else {
        echo "<div class='callout callout-danger'><b>No se encontraron pedidos...</b></div>";
    }
}
else if ($consulta == "eliminar_pedido_cuaderno") {
    $id_pedido = $_POST["id"];
    
    mysqli_autocommit($con, false);
    $errors = [];

    $query = "DELETE FROM cuaderno_articulospedidos WHERE id_pedido = $id_pedido;";
    if (!mysqli_query($con, $query)) {
        $errors[] = mysqli_error($con);
    }
    else{
        $query = "DELETE FROM cuaderno_pedidos WHERE id_pedido = $id_pedido;";
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
else if ($consulta == "confirmar_pedido_cuaderno") {
    $id_pedido_cuaderno = $_POST["id"];
    $errors = array();

    $query = "SELECT * FROM cuaderno_pedidos WHERE id_pedido = $id_pedido_cuaderno";
    $val = mysqli_query($con, $query);
    if (mysqli_num_rows($val)) {
        $query = "SELECT * FROM cuaderno_articulospedidos WHERE id_pedido = $id_pedido_cuaderno";
        $val2 = mysqli_query($con, $query);
        if (mysqli_num_rows($val2)) {
            mysqli_autocommit($con, false);

            $pedido = mysqli_fetch_assoc($val);

            $query = "INSERT INTO pedidos
            (id_cliente, FECHA, observaciones)
            VALUES (
                $pedido[ID_CLIENTE],
                NOW(),
                '$pedido[observaciones]'
            );";

            if (!mysqli_query($con, $query)) {
                $errors[] = mysqli_error($con)." ".$query;
            } else {
                $id_pedido = mysqli_insert_id($con);
                while ($artped = mysqli_fetch_array($val2)) {
                    
                    $id_articulo = $artped["id_articulo"];
                    $cantidad_plantas = $artped["cant_plantas"];
                    $cantidad_semillas = $artped["cant_semi"];
                    $cantidad_bandejas = $artped["cant_band"];
                    $fecha_siembra = $artped["fecha_siembraestimada"];
                    $fecha_entrega = $artped["fecha_entrega"];
                    $bandeja = $artped["bandeja"];
                    $semilla = $artped["con_semilla"];

                    $query = "INSERT INTO articulospedidos
                        (
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
                            con_semilla
                        ) VALUES (
                            $id_articulo,
                            $cantidad_plantas,
                            $cantidad_bandejas,
                            $cantidad_semillas,
                            '$bandeja',
                            '$fecha_entrega',
                            '$fecha_entrega',
                            '$fecha_siembra',
                            '$fecha_siembra',
                            $id_pedido,
                            $semilla
                        );";
                    if (!mysqli_query($con, $query)) {
                        $errors[] = mysqli_error($con)." ".$query;
                    }
                    else{
                        $query = "DELETE FROM cuaderno_articulospedidos WHERE id_pedido = $id_pedido_cuaderno;";
                        if (!mysqli_query($con, $query)) {
                            $errors[] = mysqli_error($con)." ".$query;
                        }
                        else{
                            $query = "DELETE FROM cuaderno_pedidos WHERE id_pedido = $id_pedido_cuaderno;";
                            if (!mysqli_query($con, $query)) {
                                $errors[] = mysqli_error($con)." ".$query;
                            }
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
        }
    } 
}
else if ($consulta == "guardar_entrega_inmediata") {

    $id_cliente = $_POST["id_cliente"];
    $errors = [];
    $lista_stock = [];
    $codigo = $_POST["remito"];
    try {
        $productos = json_decode($_POST["productos"], true);
        mysqli_autocommit($con, false);

        if (mysqli_query($con, "INSERT INTO reservas (id_cliente, fecha) VALUES ($id_cliente, NOW())")) {
            $id_reserva = mysqli_insert_id($con);

            for ($i = 0; $i < count($productos); $i++) {
                $rowid = $productos[$i]["rowid"];
                $cantidad = $productos[$i]["cantidad"];
                $query = "INSERT INTO reservas_productos
                (id_stock, cantidad, id_reserva, entregado)
                VALUES
                ($rowid, $cantidad, $id_reserva, 1)";
                if (!mysqli_query($con, $query)) {
                    $errors[] = mysqli_error($con);
                }
                array_push($lista_stock, mysqli_insert_id($con));
            }

            $query="INSERT INTO remitos (
                codigo, 
                id_cliente, 
                fecha, 
                total, 
                descuento
            ) VALUES (
                '$codigo',
                (SELECT id_cliente FROM reservas WHERE rowid = $id_reserva),
                NOW(), 
                0,
                NULL
            );";

            if (!mysqli_query($con, $query)) {
                $errors[] = mysqli_error($con);
            }
            $id_remito = mysqli_insert_id($con);
            for ($i = 0; $i < count($lista_stock);$i++){
                $query = "INSERT INTO entregas_stock (
                    fecha, 
                    id_remito,
                    id_reserva_producto
                ) VALUES (
                    NOW(),
                    $id_remito,
                    $lista_stock[$i]
                )";
                if (!mysqli_query($con, $query)) {
                    $errors[] = mysqli_error($con);
                }
            }
        }
        else {
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
}