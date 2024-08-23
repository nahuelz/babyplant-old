<?php
include "class_lib/sesionSecurity.php";
require 'class_lib/class_conecta_mysql.php';
require 'class_lib/funciones.php';

$con = mysqli_connect($host, $user, $password, $dbname);
if (!$con) {
    die("Connection failed: " . mysqli_connect_error());
}
mysqli_query($con, "SET NAMES 'utf8'");

if ($_POST["consulta"] == "busca_situacion") {
    $fechai = $_POST['fechai'];
    $fechaf = $_POST['fechaf'];
    $id_cliente = $_POST["id_cliente"];
    $fechai = str_replace("/", "-", $fechai);
    $fechaf = str_replace("/", "-", $fechaf);

    if (isset($_POST["id_cliente"]) && strlen($_POST["id_cliente"]) && strlen($fechai) == 0){
      $fechai = "1900-01-01";
    }
    else if (!isset($_POST["id_cliente"]) && strlen($fechai) == 0) {
        $fechai = (string) date('y-m-d', strtotime("first day of -6 month"));
    }
    if (strlen($fechaf) == 0) {
        $fechaf = "NOW()";
    }

    if (isset($_POST["id_cliente"]) && strlen($_POST["id_cliente"])){
      $cadena = "SELECT * FROM (
        SELECT 'PEDIDO' as origen, 
        e.id_entrega,
        r.id_remito, 
        r.tipo, 
        r.observaciones, 
        DATE_FORMAT(r.fecha, '%d/%m/%Y %T') as fecha, 
        DATE_FORMAT(r.fecha, '%Y/%m/%d %T') as fecha_sort, 
        r.id_cliente, 
        cl.nombre,
        r.codigo, 
        r.fecha as fecha_raw, 
        e.cantidad, 
        r.total as totalremito, 
        r.pagado, 
        GROUP_CONCAT(e.id_artpedido SEPARATOR ', ') as id_ordenes 
        FROM remitos r 
        INNER JOIN entregas e ON e.id_remito = r.id_remito 
        LEFT JOIN clientes cl ON cl.id_cliente = r.id_cliente
        WHERE r.id_cliente = $id_cliente
        GROUP BY r.id_remito
      UNION
        SELECT 'DEUDA' as origen, 
        NULL as id_entrega,
        r.id_remito, 
        r.tipo, 
        r.observaciones, 
        DATE_FORMAT(r.fecha, '%d/%m/%Y %T') as fecha,
        DATE_FORMAT(r.fecha, '%Y/%m/%d %T') as fecha_sort, 
        r.id_cliente, 
        cl.nombre,
        r.codigo, 
        r.fecha as fecha_raw, 
        NULL as cantidad, 
        r.total as totalremito, 
        r.pagado, 
        NULL as id_ordenes 
        FROM remitos r 
        LEFT JOIN clientes cl ON cl.id_cliente = r.id_cliente
        WHERE r.tipo = 1 AND r.id_cliente = $id_cliente
        GROUP BY r.id_remito
      UNION
        SELECT
        'STOCK' as origen,
        es.id_entrega,
        r.id_remito, 
        r.tipo, 
        r.observaciones, 
        DATE_FORMAT(r.fecha, '%d/%m/%Y %T') as fecha, 
        DATE_FORMAT(r.fecha, '%Y/%m/%d %T') as fecha_sort,
        r.id_cliente, 
        cl.nombre,
        r.codigo, 
        r.fecha as fecha_raw, 
        rp.cantidad, 
        r.total as totalremito, 
        r.pagado,  
        rp.id_stock as id_ordenes
        FROM remitos r 
        INNER JOIN entregas_stock es ON es.id_remito = r.id_remito 
        INNER JOIN reservas_productos rp ON rp.rowid = es.id_reserva_producto
        LEFT JOIN clientes cl ON cl.id_cliente = r.id_cliente
        WHERE r.id_cliente = $id_cliente
        GROUP BY r.id_remito
      UNION
        SELECT
        'PAGOS' as origen,
        NULL as id_entrega,
        NULL as id_remito, 
        NULL as tipo, 
        p.concepto as observaciones, 
        DATE_FORMAT(p.fecha, '%d/%m/%Y %T') as fecha, 
        DATE_FORMAT(p.fecha, '%Y/%m/%d %T') as fecha_sort,
        p.id_cliente, 
        cl.nombre,
        NULL as codigo, 
        p.fecha as fecha_raw, 
        NULL as cantidad, 
        p.monto as totalremito, 
        1 as pagado,  
        NULL as id_ordenes
        FROM pagos_nuevos p 
        LEFT JOIN clientes cl ON cl.id_cliente = p.id_cliente
        WHERE p.id_cliente = $id_cliente
      ) t1
        WHERE t1.totalremito > 0 AND t1.fecha_raw >= '$fechai' ";
    }
    else{
      $cadena = "SELECT * FROM (
        SELECT 'PEDIDO' as origen, 
        e.id_entrega,
        r.id_remito, 
        r.tipo, 
        r.observaciones, 
        DATE_FORMAT(r.fecha, '%d/%m/%Y %T') as fecha, 
        DATE_FORMAT(r.fecha, '%Y/%m/%d %T') as fecha_sort, 
        r.id_cliente, 
        cl.nombre as nombre_cliente,
        r.codigo, 
        r.fecha as fecha_raw, 
        e.cantidad, 
        r.total as totalremito, 
        r.pagado, 
        GROUP_CONCAT(e.id_artpedido SEPARATOR ', ') as id_ordenes 
        FROM remitos r 
        INNER JOIN entregas e ON e.id_remito = r.id_remito 
        LEFT JOIN clientes cl ON cl.id_cliente = r.id_cliente
        GROUP BY r.id_remito
      UNION
        SELECT 'DEUDA' as origen, 
        NULL as id_entrega,
        r.id_remito, 
        r.tipo, 
        r.observaciones, 
        DATE_FORMAT(r.fecha, '%d/%m/%Y %T') as fecha,
        DATE_FORMAT(r.fecha, '%Y/%m/%d %T') as fecha_sort, 
        r.id_cliente, 
        cl.nombre as nombre_cliente,
        r.codigo, 
        r.fecha as fecha_raw, 
        NULL as cantidad, 
        r.total as totalremito, 
        r.pagado, 
        NULL as id_ordenes 
        FROM remitos r 
        LEFT JOIN clientes cl ON cl.id_cliente = r.id_cliente
        WHERE r.tipo = 1
        GROUP BY r.id_remito
      UNION
        SELECT
        'STOCK' as origen,
        es.id_entrega,
        r.id_remito, 
        r.tipo, 
        r.observaciones, 
        DATE_FORMAT(r.fecha, '%d/%m/%Y %T') as fecha, 
        DATE_FORMAT(r.fecha, '%Y/%m/%d %T') as fecha_sort,
        r.id_cliente, 
        cl.nombre as nombre_cliente,
        r.codigo, 
        r.fecha as fecha_raw, 
        rp.cantidad, 
        r.total as totalremito, 
        r.pagado,  
        rp.id_stock as id_ordenes
        FROM remitos r 
        INNER JOIN entregas_stock es ON es.id_remito = r.id_remito 
        INNER JOIN reservas_productos rp ON rp.rowid = es.id_reserva_producto
        LEFT JOIN clientes cl ON cl.id_cliente = r.id_cliente
        GROUP BY r.id_remito
      UNION
        SELECT
        'PAGOS' as origen,
        NULL as id_entrega,
        NULL as id_remito, 
        NULL as tipo, 
        p.concepto as observaciones, 
        DATE_FORMAT(p.fecha, '%d/%m/%Y %T') as fecha, 
        DATE_FORMAT(p.fecha, '%Y/%m/%d %T') as fecha_sort,
        p.id_cliente, 
        cl.nombre as nombre_cliente,
        NULL as codigo, 
        p.fecha as fecha_raw, 
        NULL as cantidad, 
        p.monto as totalremito, 
        1 as pagado,  
        NULL as id_ordenes
        FROM pagos_nuevos p 
        LEFT JOIN clientes cl ON cl.id_cliente = p.id_cliente
      ) t1
        WHERE t1.totalremito > 0 AND t1.fecha_raw >= '$fechai' ";
    }
    
    if ($fechaf =! "NOW()") {
        $cadena .= " AND t1.fecha_raw <= '$fechaf' ";
    }

    //$cadena .= " AND t1.id_cliente = $id_cliente";

    $val = mysqli_query($con, $cadena);
    if (mysqli_num_rows($val) > 0) {
        echo "<div class='box box-primary'>";
        echo "<div class='box-header with-border'>";
        echo "<h3 class='box-title'>Remitos / Deudas</h3>";
        echo "</div>";
        echo "<div class='box-body'>";
        echo "<table id='tabla' class='table table-responsive w-100 d-block d-md-table'>";
        echo "<thead>";
        echo "<tr>";
        echo "<th>Nº Remito</th>
   ".((!isset($_POST["id_cliente"]) || strlen($_POST["id_cliente"]) < 1) ? "<th>Cliente</th>" : "" )."
   <th>Productos</th>
   <th>Monto</th>
   <th>Fecha</th>
   </tr>
   </thead>
   <tbody>";

        while ($ww = mysqli_fetch_array($val)) {
            $codigoproductos = "";
            if ($ww["origen"] == "PEDIDO" && $ww["id_ordenes"] != null && strlen($ww["id_ordenes"]) > 0) {
                $arrordenes = explode(", ", $ww["id_ordenes"]);  
                for ($i = 0; $i < count($arrordenes); $i++) {
                    $id_orden = $arrordenes[$i];
                    $cadenaproductos =
                        "SELECT v.nombre as nombre_variedad, t.nombre as nombre_tipo, s.nombre as nombre_subtipo, p.bandeja, e.cantidad FROM entregas e LEFT JOIN articulospedidos p ON e.id_artpedido = p.id_artpedido LEFT JOIN variedades_producto v ON v.id_articulo = p.id_articulo LEFT JOIN subtipos_producto s ON s.id_articulo = v.id_subtipo LEFT JOIN tipos_producto t ON t.id_articulo = s.id_tipo WHERE e.id_artpedido = $id_orden;";
                    $val2 = mysqli_query($con, $cadenaproductos);
                    $re = mysqli_fetch_assoc($val2);
                    $codigoproductos .= "<p>$re[nombre_tipo] $re[nombre_subtipo] $re[nombre_variedad] x$re[bandeja] [$re[cantidad] bandejas]</p>";
                }
            } 
            else if ($ww["origen"] == "DEUDA") {
                $codigoproductos = "<span class='text-danger'>DEUDA: $ww[observaciones]</span>";
            }
            else if ($ww["origen"] == "PAGOS"){
              $codigoproductos = "PAGO: $ww[observaciones]";
            }
            else if ($ww["origen"] == "STOCK"){
                $arrordenes = explode(", ", $ww["id_ordenes"]);  
                for ($i = 0; $i < count($arrordenes); $i++) {
                    $id_stock = $arrordenes[$i];
                    $cadenaproductos =
                        "SELECT 
                          v.nombre as nombre_variedad, 
                          t.nombre as nombre_tipo, 
                          s.nombre as nombre_subtipo, 
                          sb.tipo_bandeja, 
                          rp.cantidad 
                          FROM reservas_productos rp 
                          INNER JOIN stock_bandejas sb ON sb.id_stock = rp.id_stock 
                          INNER JOIN variedades_producto v ON v.id_articulo = sb.id_variedad 
                          INNER JOIN subtipos_producto s ON s.id_articulo = v.id_subtipo 
                          INNER JOIN tipos_producto t ON t.id_articulo = s.id_tipo 
                          WHERE sb.id_stock = $id_stock;";
                    $val2 = mysqli_query($con, $cadenaproductos);
                    $re = mysqli_fetch_assoc($val2);
                    $codigoproductos .= "<p><span class='text-primary'>[STOCK]</span> $re[nombre_tipo] $re[nombre_subtipo] $re[nombre_variedad] x$re[tipo_bandeja] [$re[cantidad] bandejas]</p>";
                }
            }
            
            $monto = "$" . number_format($ww["totalremito"], 2, ',', '.');
            
            $color = "";
            if ($ww["pagado"] == 1) {
                $color = "background-color: #A9F5BC;";
            }

            echo "<tr style='cursor: pointer;$color' onClick='marcarPagado($ww[pagado], $ww[id_remito])'>
            <td style='text-align: center;font-weight:bold;font-size:1.3em' id='remito_$ww[id_remito]'>$ww[id_remito]</td>";
            
            if (!isset($_POST["id_cliente"]) || strlen($_POST["id_cliente"]) < 1){
              echo "<td style='text-align: center;'>$ww[nombre_cliente]</td>";
            }
            
            echo "<td style='text-align: center;'>$codigoproductos</td>";
            echo "<td style='text-align: center;'>$monto</td>";
            echo "<td style='text-align: center'><span style='display:none'>$ww[fecha_sort]</span>$ww[fecha]</td>";
            echo "</tr>";
        }
        echo "</tbody></table></div></div>";

    } else {

        echo "<div class='callout callout-danger'><b>No se encontraron remitos en las fechas indicadas...</b></div>";

    }
} else if ($_POST["consulta"] == "busca_balance") {
    $id_cliente = $_POST["id_cliente"];

    try {
        $cadena = "SELECT (t1.balance1 + t4.balance4 + t3.balance3 - t2.balance2) as balance FROM (
SELECT IFNULL(SUM(r.total),0) as balance1 FROM remitos r
WHERE r.id_cliente = $id_cliente AND r.tipo = 0) as t1,
(SELECT IFNULL(SUM(pn.monto), 0) as balance2 FROM pagos_nuevos pn
WHERE pn.id_cliente = $id_cliente) as t2,
(SELECT IFNULL(SUM(r.total),0) as balance3 FROM remitos r
WHERE r.id_cliente = $id_cliente AND r.tipo = 1 AND r.total > 0) as t3,
(SELECT IFNULL(SUM(r.total),0) as balance4 FROM remitos r
INNER JOIN entregas_stock e ON e.id_remito = r.id_remito
WHERE r.id_cliente = $id_cliente) as t4
";

        $val = mysqli_query($con, $cadena);
        if (mysqli_num_rows($val) > 0) {
            $ww = mysqli_fetch_assoc($val);

            $balance = $ww["balance"] * -1;
            $balancetype = "success";
            if ($balance < 0) {
                $balance = "-$" . number_format(abs($balance), 2, ',', '.');
                $balancetype = "danger";
            } else if ($balance == 0) {
                $balance = "$0";
            } else {
                $balance = "+$" . number_format(abs($balance), 2, ',', '.');
            }
            echo "<div class='box box-primary'>
          <div class='box-header with-border'>
          <h3 class='box-title'>Cuenta Corriente</h3>
          </div>
          <div class='box-body'>
            <div class='row pt-2'>
              <div class='col-md-5'>
                <div class='contenedor'>
                  <div class='row'>
                    <div class='col text-center'>
                      <h5>Balance</h5>
                    </div>
                  </div>
                  <div class='row'>
                    <div class='col text-center'>
                      <h3 class='text-$balancetype'>$balance</h3>
                    </div>
                  </div>
                </div>
              </div>
              <div class='col-md-7'>
                <div class='d-flex flex-row' style='justify-content: space-between;align-items: center;height: 100%;'>
                  <button onClick='modalPagos($id_cliente)' class='btn btn-success'>Agregar Pago
                  </button>
                  <button onClick='modalVerPagos($id_cliente)' class='btn btn-primary'>Ver Pagos
                  </button>
                  <button onClick='modalDeudas($id_cliente)' class='btn btn-danger'>Agregar Deuda
                  </button>

                </div>
              </div>
            </div>
          </div>
        </div>";
        }
    } catch (Exception $e) {
        echo $e;
    }
} else if ($_POST["consulta"] == "agregar_pago") {
    try {
        $monto = $_POST["monto"];
        $concepto = $_POST["concepto"];
        $id_cliente = $_POST["id_cliente"];
        $query = "INSERT INTO pagos_nuevos (id_cliente, monto, concepto, fecha) VALUES ($id_cliente, $monto, UPPER('$concepto'), NOW())";
        if (mysqli_query($con, $query)){
          echo "success";
        }
        else{
          print_r(mysqli_error($con));
        }
    } catch (Exception $e) {
        echo $e;
    }
} else if ($_POST["consulta"] == "agregar_deuda") {
    try {
        $monto = $_POST["monto"];
        $concepto = $_POST["concepto"];
        $id_cliente = $_POST["id_cliente"];
        $query = "INSERT INTO remitos (id_cliente, total, observaciones, tipo, fecha) VALUES ($id_cliente, $monto, UPPER('$concepto'), 1, NOW())";
        if (mysqli_query($con, $query)){
          echo "success";
        }
        else{
          print_r(mysqli_error($con));
        }
    } catch (Exception $e) {
        echo $e;
    }
} else if ($_POST["consulta"] == "cargar_pagos") {
    $id_cliente = $_POST["id_cliente"];
    $fechai = (string) date('y-m-d', strtotime("first day of -6 month"));
    $cadena = "SELECT monto, concepto, id_pago, DATE_FORMAT(fecha, '%d/%m/%Y %T') as fecha FROM pagos_nuevos WHERE id_cliente = $id_cliente AND fecha >= '$fechai' ORDER BY id_pago DESC;";

    $val = mysqli_query($con, $cadena);
    if (mysqli_num_rows($val) > 0) {
        echo "<table id='tabla-pagos' class='table table-responsive w-100 d-block d-md-table'>";
        echo "<thead>";
        echo "<tr>";
        echo "<th>Fecha</th>
   <th>Concepto</th>
   <th>Monto</th>
   <th></th>
   </tr>
   </thead>
   <tbody>";

        while ($ww = mysqli_fetch_array($val)) {
            $fecha_sort = explode("/", $ww["fecha"]);
            $fecha_sort = $fecha_sort[2] . $fecha_sort[1] . $fecha_sort[0];
            $monto = "";
            if ($ww["monto"] != null) {
                $monto = "$" . (int) $ww["monto"];
            }
            echo "<tr class='text-center'>";
            echo "<td x-fecha='$ww[fecha]'><span style='display:none'>" . $fecha_sort . "</span>$ww[fecha]</td>";
            echo "<td>$ww[concepto]</td>";
            echo "<td class='font-weight-bold'>$monto</td>";
            echo "<td>
      <div class='d-flex flex-row' style='justify-content: center; align-items: center;'>
        <button class='btn btn-danger btn-xs fa fa-trash' onClick='eliminarPago($ww[id_pago])'></button>
        <button class='btn ml-3 btn-primary btn-xs fa fa-print' onClick='printPago(1, $ww[id_pago], this)'></button>
        </div>
      </td>";
            echo "</tr>";
        }
        echo "</tbody></table>";

    } else {
        echo "<div class='callout callout-danger'><b>No se encontraron Pagos</b></div>";
    }
} else if ($_POST["consulta"] == "eliminar_pago") {
    $id_pago = $_POST["id_pago"];
    $query = "DELETE FROM pagos_nuevos WHERE id_pago = $id_pago;";
    if (mysqli_query($con, $query)){
      echo "success";
    }
    else{
      print_r(mysqli_error($con));
    }
} else if ($_POST["consulta"] == "marcar_pago") {
    $valor = $_POST["valor"];
    $id_remito = $_POST["id_remito"];
    $query = "UPDATE remitos SET pagado = $valor WHERE id_remito = $id_remito;";
    if (mysqli_query($con, $query)){
      echo "success";
    }
    else{
      print_r(mysqli_error($con));
    }
}

