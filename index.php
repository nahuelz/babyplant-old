<!DOCTYPE html>
<html>
  <head>
    <title>Inicio de sesion</title>
    <?php include "./class_lib/scripts.php"; ?>
    <?php include "./class_lib/links.php"; ?>
    <?php
      
      session_start();
      if (isset($_SESSION) && isset($_SESSION["babyplant-token"]) && isset($_COOKIE["babyplant-token"]) && ($_SESSION["babyplant-token"] == $_COOKIE["babyplant-token"])){
        echo "<script>
                document.location.href = 'inicio.php';
              </script>

        ";
      }
    ?>
    <script type="text/javascript" src="dist/js/login.js"></script>
    
    <style>
      .MainLogin{
	position: absolute;
	top: 50%;
	left: 50%;
	transform: translate(-50%, -50%);
	width: 90%;
	max-width: 300px;
	padding: 15px;
	box-sizing: border-box;
	box-shadow: 0 0 5px rgba(0,0,0,.2);
	border-top: 7px solid #2196F3;
	background-color: #fff;
	color: #777777;
}
.MainLogin button{
	background-color: #2196F3;
}
.MainLogin input{
	border-radius: 0;
}
    </style>
  </head>
  <body onLoad="document.getElementById('UserName').focus();">
    <div class="container d-flex justify-content-center align-items-center" style="max-width:500px;height:100vh">
      <div>
      <form class="MainLogin" id="loginform" data-type-form="login" autocomplete="off">
        <div align="center"><img src="dist/img/babyplant.png" style="width: 250px;height:75px;"/></div>
        <div class="form-group">
          <label class="control-label" for="UserName">Usuario</label>
          <input class="form-control" name="usuario" id="UserName" type="text" required="">
        </div>
        <div class="form-group">
          <label class="control-label" for="Pass">Contraseña</label>
          <input class="form-control" name="pass" id="Pass" type="password" required="">
        </div>
        <p class="text-center">
            <button onclick="logear()" class="btn btn-primary btn-block">Ingresar</button>        
        </p>
      </form>
      </div>
    </div>
    <div class="contenedor"></div>
  </body>

 
</html>
