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
$consulta = $_POST['consulta'];

if ($consulta == "busca_usuarios") {
    $cadena = "SELECT u.id, u.nombre, GROUP_CONCAT(p.modulo SEPARATOR ', ') as modulos, u.password FROM  usuarios u LEFT JOIN permisos p ON p.id_usuario = u.id GROUP BY u.id HAVING u.id <> 1 ORDER BY u.nombre;";
    $val = mysqli_query($con, $cadena);

    if (mysqli_num_rows($val) > 0) {
        echo "<div class='box box-primary'>";
        echo "<div class='box-header with-border'>";
        echo "</div>";
        echo "<div class='box-body'>";
        echo "<table id='tabla' class='table table-bordered table-responsive w-100 d-block d-md-table'>";
        echo "<thead>";
        echo "<tr>";
        echo "<th>Id</th><th>Nombre</th><th>Permisos</th>";
        echo "</tr>";
        echo "</thead>";
        echo "<tbody>";

        while ($ww = mysqli_fetch_array($val)) {
            echo "<tr onClick='ModificarUsuario($ww[id], \"$ww[password]\", \"$ww[nombre]\", \"$ww[modulos]\")' style='cursor:pointer;'>";
            echo "<td style='text-align: center; color:#1F618D; font-weight:bold; font-size:16px;'>$ww[id]</td>";
            echo "<td style='text-align: center;font-weight:bold;font-size:16px;'>$ww[nombre]</td>";
            echo "<td style='text-align: center;font-weight:bold;font-size:16px;'>$ww[modulos]</td>";
            echo "</tr>";
        }
        echo "</tbody>";
        echo "</table>";
        echo "</div>";
        echo "</div>";
    } else {
        echo "<div class='callout callout-danger'><b>No se encontraron usuarios en la base de datos...</b></div>";
    }
} else {
    $nombre = mysqli_real_escape_string($con, $_POST['nombre']);
    $password = mysqli_real_escape_string($con, $_POST['password']);
    $permisos = json_decode($_POST["permisos"]);
    if ($consulta == "agregar") {
        $cadena = "SELECT * FROM usuarios WHERE nombre = '$nombre'";
        $val = mysqli_query($con, $cadena);
        if (mysqli_num_rows($val) > 0) {
            echo "yaexiste";
        } else {
            $usuario = mysqli_query($con, "SELECT (IFNULL(MAX(id),0)+1) as id_usuario FROM usuarios;");
            if (mysqli_num_rows($usuario) > 0) {
                $id_usuario = mysqli_fetch_assoc($usuario)["id_usuario"];
                mysqli_autocommit($con, false);
                $errors = array();
                $query = "INSERT INTO usuarios (nombre, password) VALUES ('$nombre', '$password');";
                if (!mysqli_query($con, $query)) {
                    $errors[] = mysqli_error($con);
                }

                for ($i = 0; $i < count($permisos); $i++) {
                    $modulo = $permisos[$i];
                    $query = "INSERT INTO permisos (id_usuario, modulo) VALUES ($id_usuario, '$modulo');";
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
        }
    } else if ($consulta == "editar") {
        $id_usuario = $_POST['id_usuario'];
        mysqli_autocommit($con, false);
        $errors = array();
            
        $query = "UPDATE usuarios SET nombre = '$nombre', password = '$password' WHERE id = '$id_usuario';";
        if (!mysqli_query($con, $query)) {
            $errors[] = mysqli_error($con);
        }
        $query = "DELETE FROM permisos WHERE id_usuario = $id_usuario";
        if (!mysqli_query($con, $query)) {
            $errors[] = mysqli_error($con);
        }

        for ($i = 0; $i < count($permisos); $i++) {
            $modulo = $permisos[$i];
            $query = "INSERT INTO permisos (id_usuario, modulo) VALUES ($id_usuario, '$modulo');";
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
}
