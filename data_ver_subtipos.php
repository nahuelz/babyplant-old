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

if ($consulta == "busca_subtipos") {
    $cadena = "SELECT s.id_articulo, t.nombre as nombre_tipo, s.nombre as nombre_subtipo FROM tipos_producto t INNER JOIN subtipos_producto s ON s.id_tipo = t.id_articulo WHERE s.eliminado IS NULL ORDER BY s.nombre;";
    $val = mysqli_query($con, $cadena);

    if (mysqli_num_rows($val) > 0) {
        echo "<div class='box box-primary'>";
        echo "<div class='box-header with-border'>";
        echo "</div>";
        echo "<div class='box-body'>";
        echo "<table id='tabla' class='table table-bordered table-responsive w-100 d-block d-md-table'>";
        echo "<thead>";
        echo "<tr>";
        echo "<th>Id</th><th>Nombre</th><th>Tipo</th><th></th>";
        echo "</tr>";
        echo "</thead>";
        echo "<tbody>";

        while ($ww = mysqli_fetch_array($val)) {
            $id_articulo = $ww['id_articulo'];
            $tipo = $ww['nombre_tipo'];
            $subtipo = $ww['nombre_subtipo'];

            echo "<tr>";
            echo "<td onclick=\"MostrarModalAgregarProducto($id_articulo, '$subtipo')\" style='text-align: center; width:40px; cursor:pointer; color:#1F618D;font-weight:bold;'>$id_articulo</td>";
            echo "<td onclick=\"MostrarModalAgregarProducto($id_articulo, '$subtipo')\" style='text-align: center; cursor:pointer;'>$subtipo</td>";
            echo "<td onclick=\"MostrarModalAgregarProducto($id_articulo, '$subtipo')\" style='text-align: center; cursor:pointer;'>$tipo</td>";
            echo "<td class='text-center'>";
            if ($_SESSION["id_usuario"] == 1){
                echo "<button class='btn btn-danger fa fa-trash' onClick='eliminarSubtipo($id_articulo)'></button>";
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
} else if ($consulta == "eliminar") {
    $id = $_POST["id"];
    $query = "UPDATE subtipos_producto SET eliminado = 1 WHERE id_articulo = $id";
    if (mysqli_query($con, $query)) {
        echo "success";
    } else {
        print_r(mysqli_error($con));
    }
}
