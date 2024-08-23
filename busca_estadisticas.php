<?php
include("class_lib/sesionSecurity.php");
require('class_lib/class_conecta_mysql.php');
require('class_lib/funciones.php');

$con = mysqli_connect($host, $user, $password,$dbname);
if (!$con) {
  die("Connection failed: " . mysqli_connect_error());
}
mysqli_query($con,"SET NAMES 'utf8'");
$fechai=$_POST['fechai'];
$fechaf=$_POST['fechaf'];

$fechai=str_replace("/", "-", $fechai);
$fechaf=str_replace("/", "-", $fechaf);

if (strlen($fechai) == 0){
  $fechai = (string)date('y-m-d', strtotime("first day of -3 month"));
}
if (strlen($fechaf) == 0){
  $fechaf = "NOW()";
}

$filtros = json_decode($_POST['filtros'], true); 
  if ($_POST["tipoconsulta"] == "actual"){
    $strfiltros = "";
    if ($filtros["tipo"] != NULL){
     $strfiltros.=" AND t.id_articulo = ".$filtros["tipo"]." ";
    }

    if ($filtros["subtipo"] != NULL){
      $strfiltros.=" AND s.nombre REGEXP '".$filtros["subtipo"]."' ";
    }

    if ($filtros["variedad"] != NULL){
      $strfiltros.=" AND v.nombre REGEXP '".$filtros["variedad"]."' ";
    }

    if ($filtros["cliente"] != NULL){
      $strfiltros.=" AND c.id_cliente = '$filtros[cliente]' ";
    }

    $cadena = "
      SELECT  t0.cant_band as cant0,
              t1.cant_band as cant1, 
              t2.cant_band as cant2,
              t3.cant_band as cant3,
              t4.cant_band as cant4,
              t5.cant_band as cant5,
              t6.cant_band as cant6,
              t7.cant_band as cant7

        FROM (
        SELECT IFNULL(SUM(p.cant_band), 0) as cant_band 
        FROM variedades_producto v 
        INNER JOIN subtipos_producto s ON v.id_subtipo = s.id_articulo 
        INNER JOIN tipos_producto t ON t.id_articulo = s.id_tipo
        INNER JOIN articulospedidos p ON p.id_articulo = v.id_articulo
        INNER JOIN pedidos pe ON p.id_pedido = pe.id_pedido
        INNER JOIN clientes c ON c.id_cliente = pe.id_cliente 
        WHERE estado = 0 $strfiltros) as t0,
        (SELECT IFNULL(SUM(p.cant_band), 0) as cant_band 
        FROM variedades_producto v 
        INNER JOIN subtipos_producto s ON v.id_subtipo = s.id_articulo 
        INNER JOIN tipos_producto t ON t.id_articulo = s.id_tipo
        INNER JOIN articulospedidos p ON p.id_articulo = v.id_articulo
        INNER JOIN pedidos pe ON p.id_pedido = pe.id_pedido
        INNER JOIN clientes c ON c.id_cliente = pe.id_cliente 
        WHERE estado = 1 $strfiltros) as t1,
        (SELECT IFNULL(SUM(o.cant_band_reales), 0) as cant_band 
        FROM variedades_producto v 
        INNER JOIN subtipos_producto s ON v.id_subtipo = s.id_articulo 
        INNER JOIN tipos_producto t ON t.id_articulo = s.id_tipo
        INNER JOIN articulospedidos p ON p.id_articulo = v.id_articulo
        INNER JOIN ordenes_siembra o ON o.id_artpedido = p.id_artpedido
        INNER JOIN pedidos pe ON p.id_pedido = pe.id_pedido
        INNER JOIN clientes c ON c.id_cliente = pe.id_cliente 
        WHERE estado = 2 $strfiltros) as t2,
        (SELECT IFNULL(SUM(o.cant_band_reales), 0) as cant_band 
        FROM variedades_producto v 
        INNER JOIN subtipos_producto s ON v.id_subtipo = s.id_articulo 
        INNER JOIN tipos_producto t ON t.id_articulo = s.id_tipo
        INNER JOIN articulospedidos p ON p.id_articulo = v.id_articulo
        INNER JOIN ordenes_siembra o ON o.id_artpedido = p.id_artpedido
        INNER JOIN pedidos pe ON p.id_pedido = pe.id_pedido
        INNER JOIN clientes c ON c.id_cliente = pe.id_cliente 
        WHERE estado = 3 $strfiltros) as t3,
        (SELECT IFNULL(SUM(o.cant_band_reales), 0) as cant_band 
        FROM variedades_producto v 
        INNER JOIN subtipos_producto s ON v.id_subtipo = s.id_articulo 
        INNER JOIN tipos_producto t ON t.id_articulo = s.id_tipo
        INNER JOIN articulospedidos p ON p.id_articulo = v.id_articulo
        INNER JOIN ordenes_siembra o ON o.id_artpedido = p.id_artpedido
        INNER JOIN pedidos pe ON p.id_pedido = pe.id_pedido
        INNER JOIN clientes c ON c.id_cliente = pe.id_cliente 
        WHERE estado = 4 $strfiltros) as t4,
        (SELECT IFNULL(SUM(o.cant_band_reales), 0) as cant_band 
        FROM variedades_producto v 
        INNER JOIN subtipos_producto s ON v.id_subtipo = s.id_articulo 
        INNER JOIN tipos_producto t ON t.id_articulo = s.id_tipo
        INNER JOIN articulospedidos p ON p.id_articulo = v.id_articulo
        INNER JOIN ordenes_siembra o ON o.id_artpedido = p.id_artpedido
        INNER JOIN pedidos pe ON p.id_pedido = pe.id_pedido
        INNER JOIN clientes c ON c.id_cliente = pe.id_cliente 
        WHERE estado = 5 $strfiltros) as t5,
        (SELECT IFNULL(SUM(o.cant_band_reales), 0) as cant_band 
        FROM variedades_producto v 
        INNER JOIN subtipos_producto s ON v.id_subtipo = s.id_articulo 
        INNER JOIN tipos_producto t ON t.id_articulo = s.id_tipo
        INNER JOIN articulospedidos p ON p.id_articulo = v.id_articulo
        INNER JOIN ordenes_siembra o ON o.id_artpedido = p.id_artpedido
        INNER JOIN pedidos pe ON p.id_pedido = pe.id_pedido
        INNER JOIN clientes c ON c.id_cliente = pe.id_cliente 
        WHERE estado = 6 $strfiltros) as t6,
        (SELECT IFNULL(SUM(o.cant_band_reales), 0) as cant_band 
        FROM variedades_producto v 
        INNER JOIN subtipos_producto s ON v.id_subtipo = s.id_articulo 
        INNER JOIN tipos_producto t ON t.id_articulo = s.id_tipo
        INNER JOIN articulospedidos p ON p.id_articulo = v.id_articulo
        INNER JOIN ordenes_siembra o ON o.id_artpedido = p.id_artpedido
        INNER JOIN pedidos pe ON p.id_pedido = pe.id_pedido
        INNER JOIN clientes c ON c.id_cliente = pe.id_cliente 
        WHERE estado = 7 $strfiltros) as t7
    ";



  $val = mysqli_query($con, $cadena);
  if (mysqli_num_rows($val)>0){
   echo "
   <div class='box box-primary'>
    <div class='box-header with-border'>
      <h3 class='box-title'>Estadísticas Actuales</h3>
    </div>
    <div class='box-body'>
    <table id='tabla' class='table table-bordered table-responsive w-100 d-block d-md-table'>
    <tbody>";
   
   while($ww=mysqli_fetch_array($val)){
    echo "
    <tr>
      <td class='font-weight-bold'>Bandejas Pendientes de Planificación</td>
      <td class='text-center'>$ww[cant0]</td>
    </tr>
    <tr>
      <td class='font-weight-bold'>Planificadas/Pendientes de Siembra</td>
      <td class='text-center'>$ww[cant1]</td>
    </tr>
    <tr>
      <td class='font-weight-bold'>Bandejas en Siembra</td>
      <td class='text-center'>$ww[cant2]</td>
    </tr>
    <tr>
      <td class='font-weight-bold'>Bandejas en Cámara</td>
      <td class='text-center'>$ww[cant3]</td>
    </tr>
    <tr>
      <td class='font-weight-bold'>Bandejas en Invernáculo</td>
      <td class='text-center'>$ww[cant4]</td>
    </tr>
    <tr>
      <td class='font-weight-bold'>En Agenda de Entregas</td>
      <td class='text-center'>$ww[cant5]</td>
    </tr>
    <tr>
      <td class='font-weight-bold'>Entregadas Parcialmente</td>
      <td class='text-center'>$ww[cant6]</td>
    </tr>
    ";
}

 echo "</tbody>
    </table>
    </div>
    </div>";

}else{
  echo "
  <div class='callout callout-danger'><b>No se encontraron estadísticas en las fechas indicadas...</b></div>";
}
   
}
else if ($_POST["tipoconsulta"] == "periodos"){
    $strfiltros = "";
    $strfechasiembra = "";
    $strfechasiembra.=" o.fecha_siembra >= '$fechai' AND ";
    if ($fechaf == "NOW()"){
      $strfechasiembra.="o.fecha_siembra <= NOW() ";  
    }else{
      $strfechasiembra.="o.fecha_siembra <= '$fechaf 23:59:59' ";  
    }

    $strfechaentrega = "";
    $strfechaentrega.=" e.fecha >= '$fechai' AND ";
    if ($fechaf == "NOW()"){
      $strfechaentrega.="e.fecha <= NOW() ";  
    }else{
      $strfechaentrega.="e.fecha <= '$fechaf 23:59:59' ";  
    }

    $strfechapedido = "";
    $strfechapedido.=" pe.fecha_real >= '$fechai' AND ";
    if ($fechaf == "NOW()"){
      $strfechapedido.="pe.fecha_real <= NOW() ";  
    }else{
      $strfechapedido.="pe.fecha_real <= '$fechaf 23:59:59' ";  
    }

    if ($filtros["tipo"] != NULL){
     $strfiltros.=" AND t.id_articulo = ".$filtros["tipo"]." ";
    }

    if ($filtros["subtipo"] != NULL){
      $strfiltros.=" AND s.nombre REGEXP '".$filtros["subtipo"]."' ";
    }

    if ($filtros["variedad"] != NULL){
      $strfiltros.=" AND v.nombre REGEXP '".$filtros["variedad"]."' ";
    }

    if ($filtros["cliente"] != NULL){
      $strfiltros.=" AND c.id_cliente = '$filtros[cliente]' ";
    }




    $cadena = "
      SELECT  t1.cant_band as cant1,
              t2.cant_band as cant2,
              t3.cant_band as cant3

        FROM 
        (SELECT IFNULL(SUM(o.cant_band_reales), 0) as cant_band 
        FROM variedades_producto v 
        INNER JOIN subtipos_producto s ON v.id_subtipo = s.id_articulo 
        INNER JOIN tipos_producto t ON t.id_articulo = s.id_tipo
        INNER JOIN articulospedidos p ON p.id_articulo = v.id_articulo
        INNER JOIN ordenes_siembra o ON o.id_artpedido = p.id_artpedido
        INNER JOIN pedidos pe ON p.id_pedido = pe.id_pedido
        INNER JOIN clientes c ON c.id_cliente = pe.id_cliente 
        WHERE $strfechasiembra $strfiltros) as t1,
    (SELECT IFNULL(SUM(e.cantidad), 0) as cant_band FROM 
    entregas e 
    INNER JOIN articulospedidos p ON e.id_artpedido = p.id_artpedido 
    LEFT JOIN variedades_producto v ON v.id_articulo = p.id_articulo 
    LEFT JOIN subtipos_producto s ON v.id_subtipo = s.id_articulo 
    LEFT JOIN tipos_producto t ON t.id_articulo = s.id_tipo 
    LEFT JOIN pedidos pe ON p.id_pedido = pe.id_pedido 
    LEFT JOIN clientes c ON c.id_cliente = pe.id_cliente
    WHERE $strfechaentrega $strfiltros) as t2,
    (SELECT IFNULL(SUM(p.cant_band), 0) as cant_band 
        FROM variedades_producto v 
        INNER JOIN subtipos_producto s ON v.id_subtipo = s.id_articulo 
        INNER JOIN tipos_producto t ON t.id_articulo = s.id_tipo
        INNER JOIN articulospedidos p ON p.id_articulo = v.id_articulo
        INNER JOIN pedidos pe ON p.id_pedido = pe.id_pedido
        INNER JOIN clientes c ON c.id_cliente = pe.id_cliente 
        WHERE $strfechapedido $strfiltros AND p.estado > -1) as t3
    ";

    

$val = mysqli_query($con, $cadena);
if (mysqli_num_rows($val)>0){
 
  $arrayfechai = explode("-", $fechai);
  $arrayfechaf = explode("-", $fechaf);
  $fechas = $arrayfechai[2]."/".$arrayfechai[1]."/".$arrayfechai[0]." - ".$arrayfechaf[2]."/".$arrayfechaf[1]."/".$arrayfechaf[0];


 echo "<div class='box box-primary'>";
 echo "<div class='box-header with-border'>";
 echo "<h3 class='box-title'>Período $fechas</h3>";
 echo "</div>";
 echo "<div class='box-body'>";
 echo "<table id='tabla' class='table table-bordered table-responsive w-100 d-block d-md-table'>";
 echo "<tbody>";
 
 while($ww=mysqli_fetch_array($val)){
  echo "
  <tr> 
    <td class='font-weight-bold' style='cursor:pointer;' onClick='cargarChart(\"sembradas\")'>Bandejas Sembradas</td>
    <td class='text-center'>$ww[cant1]</td>
  </tr>
  <tr> 
    <td class='font-weight-bold' style='cursor:pointer;' onClick='cargarChart(\"entregadas\")'>Bandejas Entregadas</td>
    <td class='text-center'>$ww[cant2]</td>
  </tr>
  <tr> 
    <td class='font-weight-bold' style='cursor:pointer;' onClick='cargarChart(\"pedidas\")'>Bandejas Pedidas</td>
    <td class='text-center'>$ww[cant3]</td>
  </tr>
  ";
 }

 echo "</tbody>";

 echo "</table>";

 echo "
 <div class='row'>
    <div class='col'>
      <div class='chart-container'>
        
      </div>
    </div>
  </div>

 ";

 echo "</div>";

 echo "</div>";

}else{

  echo "
  $cadena
  <div class='callout callout-danger'><b>No se encontraron estadísticas en las fechas indicadas...</b></div>";

}

}
else if ($_POST["tipoconsulta"] == "grafico"){
    $listafechas = json_decode($_POST["listafechas"], true);
    $tipografico = $_POST["tipo"];
    $cadena = "";
    
    $strfiltros = "";

    if ($filtros["tipo"] != NULL){
     $strfiltros.=" AND t.id_articulo = ".$filtros["tipo"]." ";
    }

    if ($filtros["subtipo"] != NULL){
      $strfiltros.=" AND s.nombre REGEXP '".$filtros["subtipo"]."' ";
    }

    if ($filtros["variedad"] != NULL){
      $strfiltros.=" AND v.nombre REGEXP '".$filtros["variedad"]."' ";
    }

    if ($filtros["cliente"] != NULL){
      $strfiltros.=" AND c.id_cliente = '$filtros[cliente]' ";
    }

    for ($i = 0;$i<count($listafechas);$i++){
      $inicio = $listafechas[$i][0];
      $fin = $listafechas[$i][1];

      if ($tipografico == "sembradas"){
        $cadena.="
        SELECT IFNULL(SUM(o.cant_band_reales), 0) as cant_band 
        FROM variedades_producto v 
        INNER JOIN subtipos_producto s ON v.id_subtipo = s.id_articulo 
        INNER JOIN tipos_producto t ON t.id_articulo = s.id_tipo
        INNER JOIN articulospedidos p ON p.id_articulo = v.id_articulo
        INNER JOIN ordenes_siembra o ON o.id_artpedido = p.id_artpedido
        INNER JOIN pedidos pe ON p.id_pedido = pe.id_pedido
        INNER JOIN clientes c ON c.id_cliente = pe.id_cliente 
        WHERE 
        o.fecha_siembra >= '$inicio' 
        AND 
        o.fecha_siembra <= '$fin'
         $strfiltros";
      }
      else if ($tipografico == "entregadas"){
       $cadena.="
        SELECT IFNULL(SUM(e.cantidad), 0) as cant_band FROM 
        entregas e 
        INNER JOIN articulospedidos p ON e.id_artpedido = p.id_artpedido 
        LEFT JOIN variedades_producto v ON v.id_articulo = p.id_articulo 
        LEFT JOIN subtipos_producto s ON v.id_subtipo = s.id_articulo 
        LEFT JOIN tipos_producto t ON t.id_articulo = s.id_tipo 
        LEFT JOIN pedidos pe ON p.id_pedido = pe.id_pedido 
        LEFT JOIN clientes c ON c.id_cliente = pe.id_cliente
        WHERE 
        e.fecha >= '$inicio' 
        AND 
        e.fecha <= '$fin 23:59:59'
         $strfiltros"; 
      }
      else if ($tipografico == "pedidas"){
       $cadena.="
        SELECT IFNULL(SUM(p.cant_band), 0) as cant_band 
        FROM variedades_producto v 
        INNER JOIN subtipos_producto s ON v.id_subtipo = s.id_articulo 
        INNER JOIN tipos_producto t ON t.id_articulo = s.id_tipo
        INNER JOIN articulospedidos p ON p.id_articulo = v.id_articulo
        INNER JOIN pedidos pe ON p.id_pedido = pe.id_pedido
        INNER JOIN clientes c ON c.id_cliente = pe.id_cliente 
        WHERE 
        pe.fecha_real >= '$inicio' 
        AND 
        pe.fecha_real <= '$fin 23:59:59' AND estado > -1 
         $strfiltros"; 
      }

      if (count($listafechas) > 1 && $i < count($listafechas)-1){
        $cadena.=" UNION ALL ";
      }
    }
    
    $val = mysqli_query($con, $cadena);
    
      if (mysqli_num_rows($val)>0){
        $arr = [];
        while($ww=mysqli_fetch_array($val)){
          array_push($arr, $ww["cant_band"]);
        }
        echo json_encode($arr);
      }else{
        echo "error";
      } 

    
    


}

//UPDATE pedidos SET fecha_real = DATE(pedidos.FECHA)

?>