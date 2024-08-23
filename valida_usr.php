<?php

header('Content-type: text/html; charset=utf-8');
session_start();
error_reporting(0);
$version = 9;

include './class_lib/class_conecta_mysql.php';

$con = mysqli_connect($host, $user, $password, $dbname);
// Check connection
if (!$con) {
  die("Connection failed: " . mysqli_connect_error());
}


$usuario = mysqli_real_escape_string($con, $_POST['user']);
$password = mysqli_real_escape_string($con, $_POST['pass']);

mysqli_query($con, "SET NAMES 'utf8'");

$val = mysqli_query($con, "SELECT u.nombre, u.password, u.id, GROUP_CONCAT(p.modulo SEPARATOR ',') as modulos FROM usuarios u LEFT JOIN permisos p ON p.id_usuario = u.id WHERE u.nombre='$usuario' AND BINARY u.password='$password'");
if (mysqli_num_rows($val) > 0) {
    $r = mysqli_fetch_assoc($val);
    if ($r["nombre"] != null) {
        $_SESSION['nombre_de_usuario'] = $r['nombre'];
        $_SESSION['clave'] = $r['password'];
        $_SESSION['id_usuario'] = $r["id"];
        $_SESSION['permisos'] = $r["modulos"];
        $_SESSION["arraypermisos"] = implode(",", $r["modulos"]);
        
        $token = sha1(uniqid("babyplant", true));
        $_SESSION["babyplant-token"] = $token;
        setcookie("babyplant-usuario", $r['nombre'], time() + (60 * 60 * 24 * 30), '/');
        setcookie("babyplant-token", $token, time() + (60 * 60 * 24 * 30), '/');

        echo "
        <script>
          document.location.href = 'inicio.php';
        </script>
        ";

    } else {
        echo "<script>
        swal(
          'Nombre o contraseña inválidos',
          'Por favor verifique sus datos e intente nuevamente',
          'error'
        );
        </script>";
        setcookie('babyplant-usuario', '', time() - 3600, '/');
        setcookie('babyplant-token', '', time() - 3600, '/');
    }

} else {
    echo "<script>
        swal(
          'Nombre o contraseña inválidos',
          'Por favor verifique sus datos e intente nuevamente',
          'error'
        );
      </script>";
    setcookie('babyplant-usuario', '', time() - 3600, '/');
    setcookie('babyplant-token', '', time() - 3600, '/');
}
