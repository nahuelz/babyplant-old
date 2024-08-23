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
mysqli_query($con, "SET SESSION SQL_BIG_SELECTS=1");

$consulta = $_POST["consulta"];

if ($consulta == "busca_variedades") {
    $id_variedadfiltro = $_POST['filtro'];
    $cadena = "SELECT v.id_articulo as id_variedad, t.id_articulo as id_tipo, t.nombre as nombre_tipo,
          s.nombre as nombre_subtipo, v.nombre as nombre_variedad,
          t.precio_288, t.precio_200, t.precio_162,
          t.precio_128, t.precio_72, t.precio_50, t.precio_25, t.precio_49,
          t.precio_288_s, t.precio_200_s, t.precio_162_s,
          t.precio_128_s, t.precio_72_s, t.precio_50_s, t.precio_25_s, t.precio_49_s,
          v.precio_288 as v_precio_288, v.precio_200 as v_precio_200, v.precio_162 as v_precio_162,
          v.precio_128 as v_precio_128, v.precio_72  as v_precio_72, v.precio_50 as v_precio_50, v.precio_25 as v_precio_25, v.precio_49 as v_precio_49,
          v.precio_288_s as v_precio_288_s, v.precio_200_s as v_precio_200_s, v.precio_162_s as v_precio_162_s,
          v.precio_128_s as v_precio_128_s, v.precio_72_s as v_precio_72_s, v.precio_50_s as v_precio_50_s, v.precio_25_s as v_precio_25_s, v.precio_49_s as v_precio_49_s
           FROM variedades_producto v INNER JOIN
          subtipos_producto s ON v.id_subtipo = s.id_articulo INNER JOIN tipos_producto t ON t.id_articulo = s.id_tipo";

    if ($id_variedadfiltro != null) {
        $cadena .= " WHERE v.eliminado IS NULL id_tipo = " . $id_variedadfiltro;
    } else {
        $cadena .= " WHERE v.eliminado IS NULL";
    }

    $val = mysqli_query($con, $cadena);

    if (mysqli_num_rows($val) > 0) {

        echo "<div class='box box-primary'>";
        echo "<div class='box-header with-border'>";
        echo "</div>";
        echo "<div class='box-body'>";
        echo "<table id='tabla' class='table table-bordered table-responsive w-100 d-block d-md-table'>";
        echo "<thead>";
        echo "<tr>";
        echo "<th>Tipo</th><th>Variedad</th><th>Precios Específicos</th><th></th>";
        echo "</tr>";
        echo "</thead>";
        echo "<tbody>";

        while ($ww = mysqli_fetch_array($val)) {
            $id_articulo = $ww['id_variedad'];
            $tipo = $ww['nombre_tipo'] . " " . $ww['nombre_subtipo'];
            $variedad = $ww['nombre_variedad'];

            $tipos = "";
            $precios = "";
            $precios_s = "";
            $bandejas = "";

            if (!is_null($ww["v_precio_288"])) {
                if (is_null($ww["v_precio_288_s"])) {
                    $tipos .= "x288 (<span class='text-danger'>\$" . $ww["v_precio_288"] . "</span>)<br>";
                } else {
                    $tipos .= "x288 (<span class='text-danger'>\$" . $ww["v_precio_288"] . "</span> - <span style='color:green;'>\$" . $ww["v_precio_288_s"] . "</span>)<br>";
                }

                $precios .= ($ww["v_precio_288"] . ",");
                $precios_s .= ($ww["v_precio_288_s"] . ",");

            }
            if (!is_null($ww["v_precio_200"])) {
                if (is_null($ww["v_precio_200_s"])) {
                    $tipos .= "x200 (<span class='text-danger'>\$" . $ww["v_precio_200"] . "</span>)<br>";
                } else {
                    $tipos .= "x200 (<span class='text-danger'>\$" . $ww["v_precio_200"] . "</span> - <span style='color:green;'>\$" . $ww["v_precio_200_s"] . "</span>)<br>";
                }

                $precios .= ($ww["v_precio_200"] . ",");
                $precios_s .= ($ww["v_precio_200_s"] . ",");

            }
            if (!is_null($ww["v_precio_162"])) {
                if (is_null($ww["v_precio_162_s"])) {
                    $tipos .= "x162 (<span class='text-danger'>\$" . $ww["v_precio_162"] . "</span>)<br>";
                } else {
                    $tipos .= "x162 (<span class='text-danger'>\$" . $ww["v_precio_162"] . "</span> - <span style='color:green;'>\$" . $ww["v_precio_162_s"] . "</span>)<br>";
                }

                $precios .= ($ww["v_precio_162"] . ",");
                $precios_s .= ($ww["v_precio_162_s"] . ",");

            }
            if (!is_null($ww["v_precio_128"])) {
                if (is_null($ww["v_precio_128_s"])) {
                    $tipos .= "x128 (<span class='text-danger'>\$" . $ww["v_precio_128"] . "</span>)<br>";
                } else {
                    $tipos .= "x128 (<span class='text-danger'>\$" . $ww["v_precio_128"] . "</span> - <span style='color:green;'>\$" . $ww["v_precio_128_s"] . "</span>)<br>";
                }

                $precios .= ($ww["v_precio_128"] . ",");
                $precios_s .= ($ww["v_precio_128_s"] . ",");

            }

            if (!is_null($ww["v_precio_72"])) {
                if (is_null($ww["v_precio_72_s"])) {
                    $tipos .= " x72<span class='ml-3'>(</span><span class='text-danger'>\$" . $ww["v_precio_72"] . "</span>)<br>";
                } else {
                    $tipos .= "x72<span class='ml-3'>(</span><span class='text-danger'>\$" . $ww["v_precio_72"] . "</span> - <span style='color:green;'>\$" . $ww["v_precio_72_s"] . "</span>)<br>";
                }

                $precios .= ($ww["v_precio_72"] . ",");
                $precios_s .= ($ww["v_precio_72_s"] . ",");

            }
            if (!is_null($ww["v_precio_50"])) {
                if (is_null($ww["v_precio_50_s"])) {
                    $tipos .= "x50<span class='ml-3'>(</span><span class='text-danger'>\$" . $ww["v_precio_50"] . "</span>)<br>";
                } else {
                    $tipos .= "x50<span class='ml-3'>(</span><span class='text-danger'>\$" . $ww["v_precio_50"] . "</span> - <span style='color:green;'>\$" . $ww["v_precio_50_s"] . "</span>)<br>";
                }

                $precios .= ($ww["v_precio_50"] . ",");
                $precios_s .= ($ww["v_precio_50_s"] . ",");

            }
            if (!is_null($ww["v_precio_25"])) {
                if (is_null($ww["v_precio_25_s"])) {
                    $tipos .= "x25<span class='ml-3'>(</span><span class='text-danger'>\$" . $ww["v_precio_25"] . "</span>)<br>";
                } else {
                    $tipos .= "x25<span class='ml-3'>(</span><span class='text-danger'>\$" . $ww["v_precio_25"] . "</span> - <span style='color:green;'>\$" . $ww["v_precio_25_s"] . "</span>)<br>";
                }

                $precios .= ($ww["v_precio_25"] . ",");
                $precios_s .= ($ww["v_precio_25_s"] . ",");

            }
            if (!is_null($ww["v_precio_49"])) {
                if (is_null($ww["v_precio_49_s"])) {
                    $tipos .= "x49<span class='ml-3'>(</span><span class='text-danger'>\$" . $ww["v_precio_49"] . "</span>)<br>";
                } else {
                    $tipos .= "x49<span class='ml-3'>(</span><span class='text-danger'>\$" . $ww["v_precio_49"] . "</span> - <span style='color:green;'>\$" . $ww["v_precio_49_s"] . "</span>)<br>";
                }

                $precios .= ($ww["v_precio_49"] . ",");
                $precios_s .= ($ww["v_precio_49_s"] . ",");
            }

            $jsonprecios = array(
                "288" => array(
                    "sinsemilla" => $ww["v_precio_288"],
                    "consemilla" => $ww["v_precio_288_s"],
                    "visible" => $ww["precio_288"] == null ? false : true,
                ),
                "200" => array(
                    "sinsemilla" => $ww["v_precio_200"],
                    "consemilla" => $ww["v_precio_200_s"],
                    "visible" => $ww["precio_200"] == null ? false : true,
                ),
                "162" => array(
                    "sinsemilla" => $ww["v_precio_162"],
                    "consemilla" => $ww["v_precio_162_s"],
                    "visible" => $ww["precio_162"] == null ? false : true,
                ),
                "128" => array(
                    "sinsemilla" => $ww["v_precio_128"],
                    "consemilla" => $ww["v_precio_128_s"],
                    "visible" => $ww["precio_128"] == null ? false : true,
                ),
                "72" => array(
                    "sinsemilla" => $ww["v_precio_72"],
                    "consemilla" => $ww["v_precio_72_s"],
                    "visible" => $ww["precio_72"] == null ? false : true,
                ),
                "50" => array(
                    "sinsemilla" => $ww["v_precio_50"],
                    "consemilla" => $ww["v_precio_50_s"],
                    "visible" => $ww["precio_50"] == null ? false : true,
                ),
                "25" => array(
                    "sinsemilla" => $ww["v_precio_25"],
                    "consemilla" => $ww["v_precio_25_s"],
                    "visible" => $ww["precio_25"] == null ? false : true,
                ),
                "49" => array(
                    "sinsemilla" => $ww["v_precio_49"],
                    "consemilla" => $ww["v_precio_49_s"],
                    "visible" => $ww["precio_49"] == null ? false : true,
                ),
            );

            $jsonprecios = json_encode($jsonprecios);

            echo "
            <tr class='text-center' style='cursor:pointer' x-id='$id_articulo'>
            <td onclick=\"MostrarModalAgregarProducto($id_articulo, '$variedad', '$jsonprecios')\">$tipo</td>
            <td>$variedad</td>
            <td style='font-size: 1.1em; font-weight:bold;' x-precios='$jsonprecios'>$tipos</td>
            <td class='text-center'>";
            if ($_SESSION["id_usuario"] == 1){
                echo "<button onclick='eliminarVariedad($id_articulo)' class='btn btn-danger fa fa-trash'></button>";
            }
            echo "</td>";
            echo "</tr>";

        }
        echo "</tbody>";
        echo "</table>";
        echo "</div>";
        echo "</div>";

    } else {
        echo "<div class='callout callout-danger'><b>No se encontraron productos en la base de datos...</b></div>";
    }
}
else if ($consulta == "eliminar"){
  $id = $_POST["id"];
  $query = "UPDATE variedades_producto SET eliminado = 1 WHERE id_articulo = $id";
  if (mysqli_query($con, $query)){
    echo "success";
  }
  else{
    print_r(mysqli_error($con));
  }
}