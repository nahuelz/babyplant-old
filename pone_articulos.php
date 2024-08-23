<?php
include "class_lib/sesionSecurity.php";
require 'class_lib/class_conecta_mysql.php';

$con = mysqli_connect($host, $user, $password, $dbname);
if (!$con) {
    die("Connection failed: " . mysqli_connect_error());
}

mysqli_query($con, "SET NAMES 'utf8'");
$cadena = "select id_articulo, nombre from tipos_producto order by nombre ASC";
$val = mysqli_query($con, $cadena);

if (mysqli_num_rows($val) > 0) {
    while ($re = mysqli_fetch_array($val)) {
        echo "<option value=$re[id_articulo]>$re[nombre]</option>";
    }
}
