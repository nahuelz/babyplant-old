<?php include "./class_lib/sesionSecurity.php"; ?>
<!DOCTYPE html>
<html>
  <head>
    <title>Clientes</title>
    <?php include "./class_lib/links.php"; ?>
    <?php include "./class_lib/scripts.php"; ?>
    <link rel="stylesheet" href="plugins/select2/select2.min.css">
    <script src="dist/js/check_permisos.js?v=<?php echo $version ?>"></script>
    <script src="dist/js/ver_clientes.js?v=<?php echo $version ?>"></script>
  </head>
  <body>
    <div class="wrapper">
      <header class="main-header">
        <?php include('class_lib/nav_header.php');?>
      </header>
      <aside class="main-sidebar">
        <?php include('class_lib/sidebar.php');?>
      </aside>

      <div class="content-wrapper">
        <section class="content-header">
          <h1>Clientes <button class="btn btn-success ml-3" onclick="modalAgregarCliente();"><i class="fa fa-plus-square"></i> AGREGAR</button></h1>
          <ol class="breadcrumb">
            <li><a href="inicio.php"> Inicio</a></li>
            <li class="active">Clientes</li>
          </ol>
        </section>

        <!-- Main content -->
        <section class="content">
          <div class='row mt-3 mb-5'>
            <div class='col'>
              <div id='tabla_entradas'></div>
            </div>
          </div>
        </section><!-- /.content -->
         </div><!-- /.content-wrapper -->

      <?php include('class_lib/footer.php');?>
      <?php include("modals/agregar_cliente.php"); ?>
     
      <div class="control-sidebar-bg"></div>
    </div><!-- ./wrapper -->
  
    <script>
       const id_usuario = "<?php echo $_SESSION['id_usuario'] ?>"; 
       const permisos = "<?php echo $_SESSION['permisos'] ?>"; 
       func_check(id_usuario, permisos.split(","));   
    </script>
  </body>
</html>