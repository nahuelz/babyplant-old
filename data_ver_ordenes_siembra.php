<?php
include "class_lib/sesionSecurity.php";
require 'class_lib/class_conecta_mysql.php';
require 'class_lib/funciones.php';

$con = mysqli_connect($host, $user, $password, $dbname);
if (!$con) {
    die("Connection failed: " . mysqli_connect_error());
}
mysqli_query($con, "SET NAMES 'utf8'");
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

$cadena = "SELECT p.id_artpedido, v.id_articulo as id_variedad, t.id_articulo as id_tipo, t.nombre as nombre_tipo,
s.nombre as nombre_subtipo, v.nombre as nombre_variedad, p.cant_plantas, p.cant_semi, o.cant_band_reales as cant_band, DATE_FORMAT(o.fecha, '%d/%m/%Y') as fecha_orden,
  p.bandeja, o.id_orden as id_orden_real, p.fecha_entrega_original, o.fecha as fecha_ordensiembra,
 c.nombre as cliente, c.id_cliente, p.estado, o.id_orden_alternativa, p.revision, p.solucion, GROUP_CONCAT(DISTINCT(om.id_mesada) SEPARATOR ', ') as id_mesada,
DATE_FORMAT(o.fecha_mesada_in, '%d/%m/%Y') as fecha_mesada_in, DATE_FORMAT(o.fecha_camara_in, '%d/%m/%Y') as fecha_camara_in, DATE_FORMAT(o.fecha_siembra, '%d/%m/%Y') as fecha_siembra, o.fecha_siembra as fecha_siembra_raw,
o.fecha_camara_in as fecha_camara_raw, o.fecha_mesada_in as fecha_mesada_raw, o.obsiembra
FROM variedades_producto v
INNER JOIN subtipos_producto s ON v.id_subtipo = s.id_articulo
INNER JOIN tipos_producto t ON t.id_articulo = s.id_tipo
INNER JOIN articulospedidos p ON p.id_articulo = v.id_articulo
INNER JOIN pedidos pe ON p.id_pedido = pe.id_pedido
INNER JOIN clientes c ON pe.id_cliente = c.id_cliente
INNER JOIN ordenes_siembra o ON o.id_artpedido = p.id_artpedido
LEFT JOIN ordenes_mesadas om ON o.id_orden = om.id_orden
GROUP BY o.id_orden ";

if ($filtros["tipofecha"] == 0 || $filtros["tipofecha"] == null) {
    $cadena .= "HAVING DATE(fecha_ordensiembra) >= '$fechai' AND ";
    if ($fechaf == "NOW()") {
        $cadena .= "DATE(fecha_ordensiembra) <= NOW() ";
    } else {
        $cadena .= "DATE(fecha_ordensiembra) <= '$fechaf' ";
    }
} else if ($filtros["tipofecha"] == 1) {
    $cadena .= "HAVING DATE(fecha_siembra_raw) >= '$fechai' AND ";
    if ($fechaf == "NOW()") {
        $cadena .= "DATE(fecha_siembra_raw) <= NOW() ";
    } else {
        $cadena .= "DATE(fecha_siembra_raw) <= '$fechaf' ";
    }
} else if ($filtros["tipofecha"] == 2) {
    $cadena .= "HAVING DATE(fecha_camara_raw) >= '$fechai' AND ";
    if ($fechaf == "NOW()") {
        $cadena .= "DATE(fecha_camara_raw) <= NOW() ";
    } else {
        $cadena .= "DATE(fecha_camara_raw) <= '$fechaf' ";
    }
} else if ($filtros["tipofecha"] == 3) {
    $cadena .= "HAVING DATE(fecha_mesada_raw) >= '$fechai' AND ";
    if ($fechaf == "NOW()") {
        $cadena .= "DATE(fecha_mesada_raw) <= NOW() ";
    } else {
        $cadena .= "DATE(fecha_mesada_raw) <= '$fechaf' ";
    }
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
    if ($filtros["revision"] == -1) {
        $cadena .= " AND revision > 0 ";
    } else if ($filtros["revision"] > 0) {
        $cadena .= " AND revision = " . (string) $filtros["revision"] . " ";
    }
    $cadena .= " AND solucion IS NULL ";
} else if ($filtros["solucion"] != null) {
    if ($filtros["solucion"] == -1) {
        $cadena .= " AND solucion > 0 ";
    } else if ($filtros["solucion"] > 0) {
        $cadena .= " AND solucion = " . (string) $filtros["solucion"] . " ";
    }
}

$cadena .= " ORDER BY o.id_orden DESC;";

$val = mysqli_query($con, $cadena);
if (mysqli_num_rows($val) > 0) {
    echo "<div class='box box-primary'>";
    echo "<div class='box-header with-border'>";
    echo "<h3 class='box-title'>Órdenes Siembra</h3>";
    echo "</div>";
    echo "<div class='box-body'>";
    echo "<table id='tabla' class='table table-bordered table-responsive w-100 d-block d-md-table'>";
    echo "<thead>";
    echo "<tr>";
    echo "<th>Orden<br>Siembra</th>
 <th>Producto</th>
 <th>Cliente</th>
 <th>Bandejas<br>Sembradas</th>
 <th>Fecha Siembra</th>
 <th>Entrada Cámara</th>
 <th>Entrada Invernáculo</th>
 <th>Entrega<br>Solicitada</th>
 <th>Estado</th>
 <th>Mesada</th>
 <th>Observaciones</th>";
    echo "</tr>";
    echo "</thead>";
    echo "<tbody>";

    $tipos_revision = ["", "FALLA GERMINACIÓN", "GOLPE", "PAJARO", "RATA", "REALIZAR DESPUNTE", "USO PARA INJERTO", "D1 REALIZADO", "VER OBSERV."];
    $tipos_solucion = ["", "D1 CANCELADO", "CLASIFICACIÓN", "REPIQUE", "RESIEMBRA", "DEJAR FALLAS 12"];
    while ($ww = mysqli_fetch_array($val)) {
        $id_cliente = $ww['id_cliente'];
        $id_artpedido = $ww['id_artpedido'];

        $producto = "<span class='hidden'>" . $ww['nombre_tipo'] . "</span> " . $ww['nombre_subtipo'] . " " . $ww['nombre_variedad'] . " x" . $ww['bandeja'];

        $tipo = strtoupper(substr($ww["nombre_tipo"], 0, 3));

        if ($ww["revision"] != null && $ww["solucion"] == null) {
            $producto .= " [" . $tipos_revision[$ww["revision"]] . "]";
        } else if ($ww["revision"] != null && $ww["solucion"] != null) {
            $producto .= " [" . $tipos_revision[$ww["revision"]] . "] [" . $tipos_solucion[$ww["solucion"]] . "]";
        }

        $cliente = $ww['cliente'];
        $cant_band = $ww['cant_band'];
        $fecha_siembra = $ww['fecha_siembra'];
        $fecha_entrega = $ww['fecha_entrega_original'];
        $id_orden = $ww['id_orden_alternativa'];

        $estado = generarBoxEstado($ww['estado'], true);

        $fondo = "";
        if ($ww["revision"] != null && $ww["solucion"] == null) {
            $fondo = "background-color:#F7D358;";
        }

        if ($ww["solucion"] != null) {
            $fondo = "background-color:#A9F5A9;";
        }

        $fecha_orden = explode("/", $ww["fecha_orden"]);
        $fecha_orden2 = $ww["fecha_orden"];
        $fecha_orden = $fecha_orden[2] . "/" . $fecha_orden[1] . "/" . $fecha_orden[0];

        echo "<tr>";
        echo "<td id='ordens_$ww[id_orden]' style='text-align: center; cursor:pointer; color:#1F618D; font-weight:bold; $fondo'>
          <span style='display:none'>$ww[id_orden_real]</span>
          <span style='font-size:1.4em;'>$id_orden</span><br><span style='color: blue;font-size:1.2em'>$tipo</span>
        </td>";
        echo "<td style='$fondo'>$producto</td>";
        echo "<td style='$fondo'>$cliente</td>";

        $fecha_siembra2 = explode("/", $fecha_siembra);

        $fecha_siembra2 = $fecha_siembra2[2] . "/" . $fecha_siembra2[1] . "/" . $fecha_siembra2[0];

        $fecha_camara = explode("/", $ww["fecha_camara_in"]);

        $fecha_camara = $fecha_camara[2] . "/" . $fecha_camara[1] . "/" . $fecha_camara[0];

        $fecha_mesada = explode("/", $ww["fecha_mesada_in"]);

        $fecha_mesada = $fecha_mesada[2] . "/" . $fecha_mesada[1] . "/" . $fecha_mesada[0];

        $fecha_entrega2 = explode("/", $fecha_entrega);

        $fecha_entrega2 = $fecha_entrega2[2] . "/" . $fecha_entrega2[1] . "/" . $fecha_entrega2[0];

        echo "<td style='text-align: center;font-weight:bold;font-size:1.5em;$fondo'>$cant_band</td>";

        echo "<td style='text-align: center;$fondo'><span style='display:none'>" . str_replace("/", "", $fecha_siembra2) . "</span>" . str_replace("/20", "/", $fecha_siembra) . "</td>";

        echo "<td style='text-align: center;$fondo'><span style='display:none'>" . str_replace("/", "", $fecha_camara) . "</span>" . str_replace("/20", "/", $ww["fecha_camara_in"]) . "</td>";

        echo "<td style='text-align: center;$fondo'><span style='display:none'>" . str_replace("/", "", $fecha_mesada) . "</span>" . str_replace("/20", "/", $ww["fecha_mesada_in"]) . "</td>";

        echo "<td style='text-align: center;$fondo'><span style='display:none'>" . str_replace("/", "", $fecha_entrega2) . "</span>" . str_replace("/20", "/", $fecha_entrega) . "</td>";

        echo "<td style='$fondo'><div style='cursor:pointer' onClick='modalOrdenSiembra($id_artpedido)'>$estado</div></td>";

        echo "<td style='text-align: center; font-size:1.0em; $fondo'>" . $ww['id_mesada'] . "</td>";

        echo "<td style='text-align: center; font-size:1.0em; $fondo'>$ww[obsiembra]</td>";

        echo "</tr>";

    }

    echo "</tbody>";

    echo "</table>";

    echo "</div>";

    echo "</div>";

} else {

    echo "<div class='callout callout-danger'><b>No se encontraron pedidos en las fechas indicadas...</b></div>";

}
