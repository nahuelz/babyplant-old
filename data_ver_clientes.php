<?php

include "class_lib/sesionSecurity.php";
require 'class_lib/class_conecta_mysql.php';

$con = mysqli_connect($host, $user, $password, $dbname);
if (!$con) {
    die("Connection failed: " . mysqli_connect_error());
}
mysqli_query($con, "SET NAMES 'utf8'");

$consulta = $_POST["consulta"];

if ($consulta == "busca_clientes") {
    $cadena = "SELECT c.id_cliente as id_cliente, c.nombre as nombre, c.domicilio as domicilio, c.telefono, c.mail as mail,  c.cuit as cuit FROM clientes c ORDER BY nombre ASC;";
    $val = mysqli_query($con, $cadena);

    if (mysqli_num_rows($val) > 0) {
        echo "<div class='box box-primary'>";
        echo "<div class='box-header with-border'>";
        echo "</div>";
        echo "<div class='box-body'>";
        echo "<table id='tabla' class='table table-bordered table-responsive w-100 d-block d-md-table'>";
        echo "<thead>";
        echo "<tr>";
        echo "<th>ID</th><th>Nombre</th><th>Domicilio</th><th>Teléfono</th><th>E-Mail</th><th>CUIT</th>";
        echo "</tr>";
        echo "</thead>";
        echo "<tbody>";

        while ($ww = mysqli_fetch_array($val)) {
            echo "<tr style='cursor:pointer;' onClick='modificarCliente($ww[id_cliente], this)'
              x-nombre=\"$ww[nombre]\"
              x-domicilio=\"$ww[domicilio]\"
              x-telefono=\"$ww[telefono]\"
              x-mail=\"$ww[mail]\"
              x-cuit=\"$ww[cuit]\"
            >";
            echo "<td style='text-align: center; color:#1F618D; font-weight:bold; font-size:16px;'>$id_cliente</td>";
            echo "<td style='text-align: center;'>$ww[nombre]</td>";
            echo "<td style='text-align: center;'>$ww[domicilio]</td>";
            echo "<td style='text-align: center;'>$ww[telefono]</td>";
            echo "<td style='text-align: center;'>$ww[mail]</td>";
            echo "<td style='text-align: center;'>$ww[cuit]</td>";
            echo "</tr>";
        }
        echo "</tbody>";
        echo "</table>";
        echo "</div>";
        echo "</div>";
    } else {
        echo "<div class='callout callout-danger'><b>No se encontraron clientes en la base de datos...</b></div>";
    }
}
else if ($consulta == "agregar_cliente" || $consulta == "modificar_cliente"){
    $nombre = $_POST['nombre'];
    $domicilio = $_POST['domicilio'];
    $telefono = $_POST['telefono'];
    $mail = $_POST["mail"];
    $cuit = $_POST['cuit'];

    if ($consulta == "agregar_cliente"){
        $query="INSERT INTO clientes (
                nombre, 
                domicilio, 
                telefono, 
                mail, 
                cuit) 
            VALUES (
                UPPER('$nombre'), 
                UPPER('$domicilio'), 
                '$telefono', 
                LOWER('$mail'), 
                '$cuit');";
    }
    else if ($consulta == "modificar_cliente"){
        $query="UPDATE clientes SET 
            nombre = UPPER('$nombre'), 
            domicilio = UPPER('$domicilio'), 
            telefono = '$telefono', 
            mail = LOWER('$mail'), 
            cuit = '$cuit' 
                WHERE id_cliente = $_POST[id_cliente];";  
    }
    try {
        if (mysqli_query($con,$query)){
            echo "success";
        }
    } catch (\Throwable $th) {
        throw $th;
    }
}



