<?php
include("class_lib/sesionSecurity.php");
require('class_lib/class_conecta_mysql.php');
require('class_lib/funciones.php');
$con = mysqli_connect($host, $user, $password,$dbname);
if (!$con) {
  die("Connection failed: " . mysqli_connect_error());
}

$tipo = $_POST['consulta'];
$usuario = $_SESSION['nombre_de_usuario'];

if ($tipo == "cargar_ordenes"){
  $fecha = $_POST['fecha']; 
  $arraycolumnas = [];
  $fila = [];
  $cadenaselect="SELECT o.id_orden, o.id_orden_alternativa, o.id_artpedido, t.nombre as nombre_tipo, 
  s.nombre as nombre_subtipo, v.nombre as nombre_variedad,
   DATE_FORMAT(DATE(o.fecha_siembra),'%d/%m/%Y') as fechasiembra, a.id_artpedido, 
   a.bandeja, a.estado, a.fila_siembra, o.cant_band_reales 
   FROM ordenes_siembra o 
   LEFT JOIN articulospedidos a ON o.id_artpedido = a.id_artpedido 
   LEFT JOIN variedades_producto v ON v.id_articulo = a.id_articulo 
   LEFT JOIN subtipos_producto s ON s.id_articulo = v.id_subtipo 
   LEFT JOIN tipos_producto t ON t.id_articulo = s.id_tipo 
   HAVING fechasiembra = '$fecha' AND a.estado > 1 ORDER BY nombre_tipo, o.id_orden_alternativa, a.estado;";
  $val = mysqli_query($con,$cadenaselect);
  $salida = "";
  $stringproducto = "";
  if (mysqli_num_rows($val)>0){
      while($re=mysqli_fetch_array($val))
      {
        $estado = "";
        if ($re['estado'] == 2){
          $estado = "<div class='cajita' id='estado_".$re['id_artpedido']."' onClick='cambiarEstadoSiembra(this.id);' style='background-color:#74DF00; padding:5px; cursor:pointer;font-size:0.8em;font-weight:bold;'>SEMBRADO</div>";        
        }
        else if ($re['estado'] == 3){
          $estado = "<div class='cajita' id='estado_".$re['id_artpedido']."' onClick='cambiarEstadoSiembra(this.id);' style='background-color:#2E9AFE; padding:5px; cursor:pointer;font-size:0.8em;font-weight:bold;'>EN CÁMARA</div>";        
        }
        else if ($re['estado'] > 3){
          $estado = "<div class='cajita' style='background-color:#A4A4A4; padding:5px; cursor:pointer;font-size:0.8em;font-weight:bold;'> <span id='estado_".$re['id_artpedido']."'>ETAPAS POSTERIORES</span></div>";        
        }
        $producto = "$re[nombre_tipo] $re[nombre_subtipo] $re[nombre_variedad] x$re[bandeja]";
        $productorow = "";
        $click = "onClick='toggleSelection(this)'";
        if (strpos($producto, "TOMATE") !== false) {
          $productorow = "<td $click style='background-color: #FFACAC; font-size:1.2em; font-weight:bold;'>".$producto."</td>";
        }
        else if (strpos($producto, "PIMIENTO") !== false) {
          $productorow = "<td $click style='background-color: #BAE1A2; font-size:1.2em; font-weight:bold;'>".$producto."</td>";
        }
        else if (strpos($producto, "BERENJENA") !== false) {
          $productorow = "<td $click style='background-color: #D5B4FF; font-size:1.2em;font-weight:bold;'>".$producto."</td>";
        }
        else if (strpos($producto, "LECHUGA") !== false) {
          $productorow = "<td $click style='background-color: #D7FFBC;font-size:1.2em; font-weight:bold;'>".$producto."</td>";
        }
        else if (strpos($producto, "ACELGA") !== false) {
          $productorow = "<td $click style='background-color: #BFDCBC;font-size:1.2em; font-weight:bold;'>".$producto."</td>";
        }
        else if (strpos($producto, "REMOLACHA") !== false) {
          $productorow = "<td $click style='background-color: #eba5b5;font-size:1.2em; font-weight:bold; word-wrap:break-word;'>".$producto."</td>";
        }
        else if (strpos($producto, "COLES") !== false || strpos($producto, "HINOJO") !== false || strpos($producto, "APIO") !== false) {
          //AZUL
          $productorow = "<td $click style='background-color: #58ACFA;font-size:1.2em; font-weight:bold; word-wrap:break-word;'>".$producto."</td>";
        }
        else if (strpos($producto, "VERDEO") !== false || strpos($producto, "PUERRO") !== false) {//NARANJA
          $productorow = "<td $click style='background-color: #F7BE81;font-size:1.2em; font-weight:bold; word-wrap:break-word;'>".$producto."</td>";
        }
        else{
          $productorow = "<td $click style='background-color: #A4A4A4; font-weight:bold;font-size:1.2em; word-wrap:break-word;'>".$producto."</td>";          
        }

        $salida.="<tr x-id-orden='$re[id_orden]' style='cursor:pointer;'>
              <td $click><span id='orden_".$re['id_orden']."' style='color:#1F618D; font-weight:bold; font-size:1.4em;'>$re[id_orden_alternativa]</span></td>
              $productorow";
              
              if ($usuario == "admin")
                $salida.="<td style='font-size:1.4em; font-weight:bold;' onClick='cambiarCantidadSiembra($re[id_orden], $re[id_orden_alternativa], this)'>$re[cant_band_reales]</td>";
              else
                $salida.="<td $click style='font-size:1.4em; font-weight:bold;'>$re[cant_band_reales]</td>";
        $salida.="
              <td>$estado</td>
              </tr>";
        }
        echo $salida;
  }
  else{
    echo "
      <tr>
        <td colspan='4' class='text-center pt-4 pb-4'>
          <h4 class='text-muted'>No hay Órdenes para ingresar el día seleccionado</h4>
        </td>
      </tr>

    ";
  }
}
else if ($tipo == "cargar_orden_especifica"){
    $id_orden = $_POST['id_orden'];
    $cadena = "SELECT o.id_orden, o.id_artpedido, t.nombre as nombre_tipo, s.nombre as nombre_subtipo, v.nombre as nombre_variedad, a.fecha_planificacion, a.id_artpedido, a.bandeja, a.estado, a.fila_siembra, a.cant_band, a.cant_plantas, a.cant_semi, UPPER(a.cod_sobre) as cod_sobre FROM ordenes_siembra o LEFT JOIN  articulospedidos a ON o.id_artpedido = a.id_artpedido INNER JOIN variedades_producto v ON v.id_articulo = a.id_articulo INNER JOIN subtipos_producto s ON s.id_articulo = v.id_subtipo INNER JOIN tipos_producto t ON t.id_articulo = s.id_tipo WHERE o.id_orden = $id_orden ORDER BY nombre_tipo, nombre_subtipo, nombre_variedad, o.id_orden";
    $val = mysqli_query($con, $cadena);
    $salida = "";

    if (mysqli_num_rows($val)>0){
      while($re=mysqli_fetch_array($val))
      {
        $producto = "$re[nombre_tipo] $re[nombre_subtipo] $re[nombre_variedad] x$re[bandeja]";
        if ($re['con_semilla'] == 1){
          $producto.= " CON SEMILLA";
        }
        else{
          $producto.= " SIN SEMILLA";
        }

        $sobre = $re['cod_sobre'];
        if (is_null($sobre)){
          $sobre = "NO ASIGNADO";
        }

        $estado = "";
        if ($re['estado'] == 1){
          $estado = "<span id='estado_".$re['id_artpedido']."' onClick='cambiarEstadoSiembra(this.id);' style='background-color:#FFFF00; border-radius:10px; border-style: solid; border-color: black;border-width: 2px; padding:5px; cursor:pointer;'>PLANIFICADO</span>";
        }
        else if ($re['estado'] == 2){
          $estado = "<span id='estado_".$re['id_artpedido']."' onClick='cambiarEstadoSiembra(this.id);' style='background-color:#74DF00; border-radius:10px; border-style: solid; border-color: black;border-width: 2px; padding:5px; cursor:pointer;'>SEMBRADO</span>";        
        }

        $salida.="<tr >
                <td >$re[id_artpedido]</td>
                <td style='word-wrap:break-word;'>$producto</td>
                <td>$re[cant_band]</td>
                <td>$re[cant_plantas]</td>
                <td>$re[cant_semi]</td>
                <td style='word-wrap:break-word;'>$sobre</td>
                <td>$estado</td>
              </tr>";
      }
      echo $salida;
    }
  }

else if ($tipo == "check_anteriores"){
  $cadenaselect="SELECT o.id_orden_alternativa, t.nombre,DATE_FORMAT(DATE(o.fecha),'%d/%m/%Y') AS fecha 
  FROM ordenes_siembra o INNER JOIN articulospedidos a ON o.id_artpedido = a.id_artpedido INNER JOIN variedades_producto v ON v.id_articulo = a.id_articulo
  INNER JOIN subtipos_producto s ON s.id_articulo = v.id_subtipo INNER JOIN tipos_producto t ON t.id_articulo = s.id_tipo
   WHERE DATE(fecha) < CURDATE() ORDER BY t.nombre, o.id_orden_alternativa;";
  $salida = "";
  $val = mysqli_query($con,$cadenaselect);
  if (mysqli_num_rows($val)>0){
    $salida.="ATENCIÓN! Hay Ordenes de días anteriores que no fueron sembradas:\n\n";
    while($re=mysqli_fetch_array($val))
    {
      $salida.=("\t• Orden: $re[id_orden_alternativa] | $re[nombre] | Fecha: $re[fecha]\n");
    }
    echo $salida;
  }else{
    echo "";
  }
}
else if ($tipo == "carga_pedidos_en_camara"){
$str = json_decode($_POST['fechas'], true); 
$lista = implode( ", ", $str );
$rowLength = count($str);
$query = "";
$arraycolumnas = [];
array_push($arraycolumnas, []);
array_push($arraycolumnas, []);
array_push($arraycolumnas, []);
array_push($arraycolumnas, []);
array_push($arraycolumnas, []);
array_push($arraycolumnas, []);
array_push($arraycolumnas, []);

$fila = [];
$cadenaselect="SELECT t.nombre as nombre_tipo, c.nombre as nombre_cliente, s.nombre as nombre_subtipo, v.nombre as nombre_variedad,
 DATE_FORMAT(DATE_ADD(o.fecha_camara_in, 
  INTERVAL (SELECT dias_en_camara FROM tipos_producto WHERE id_articulo = t.id_articulo) DAY), '%d/%m/%Y') as fecha_camarita, o.problemacamara, o.dataproblema,
o.fecha_camara_in, o.id_artpedido, a.estado, o.id_orden_alternativa, t.dias_en_camara, 
o.cant_band_reales, a.bandeja FROM ordenes_siembra o 
INNER JOIN articulospedidos a ON o.id_artpedido = a.id_artpedido 
INNER JOIN variedades_producto v ON v.id_articulo = a.id_articulo 
INNER JOIN subtipos_producto s ON s.id_articulo = v.id_subtipo 
INNER JOIN tipos_producto t ON t.id_articulo = s.id_tipo
INNER JOIN pedidos p ON p.ID_PEDIDO = a.id_pedido
INNER JOIN clientes c ON c.id_cliente = p.ID_CLIENTE 

HAVING fecha_camarita IN ($lista) AND a.estado IN (3, 4) 
ORDER BY nombre_tipo, nombre_subtipo, nombre_variedad";

$val = mysqli_query($con,$cadenaselect);

if (mysqli_num_rows($val)>0){
    while($re=mysqli_fetch_array($val))
    {
        $input = str_replace("/", "-",$re['fecha_camarita']);
        $date = strtotime($input); 
        $fechareal = date('d-m-Y', $date);
        $fechareal = str_replace("-", "/",$fechareal);

      for ($i = 0;$i < count($str);$i++){
        if ($fechareal == str_replace("'", "", $str[$i])) {
          array_push($arraycolumnas[$i], [$re['nombre_tipo']." ".$re['nombre_subtipo']."|".$re['nombre_variedad']." x".$re['bandeja'], $re['id_artpedido'], $re['estado'], NULL, $re['id_orden_alternativa'], $re['dias_en_camara'], $re['fecha_camara_in'], $re['cant_band_reales'], $fechareal, $re["nombre_cliente"], $re['problemacamara'], $re['dataproblema']]);
        } 
      }
    }
    echo json_encode($arraycolumnas);
}else{
  echo "";
}

}


?>