<?php include "./class_lib/sesionSecurity.php"; ?>
<!DOCTYPE html>

<html>

<head>
  <title>Usuarios</title>
  <?php include "./class_lib/links.php"; ?>
  <?php include "./class_lib/scripts.php"; ?>
  <link rel="stylesheet" href="plugins/select2/select2.min.css">
  <link rel="stylesheet" href="plugins/daterangepicker/daterangepicker-bs3.css">
  <script src="dist/js/check_permisos.js?v=<?php echo $version ?>"></script>
  <script src="dist/js/ver_usuarios.js?v=<?php echo $version ?>"></script>
</head>

<body onload="busca_usuarios();">
  <div class="wrapper">
    <header class="main-header">
      <?php include('class_lib/nav_header.php');?>
    </header>
    <aside class="main-sidebar">
      <?php include('class_lib/sidebar.php'); ?>
    </aside>
    <div class="content-wrapper">
      <section class="content-header">
        <h1>Usuarios <button class="btn btn-success ml-3" onclick="MostrarModalAgregarUsuario();"><i
              class="fa fa-plus-square"></i> AGREGAR</button></h1>
        <ol class="breadcrumb">
          <li><a href="inicio.php"> Inicio</a></li>
          <li class="active">Usuarios</li>
        </ol>
      </section>

      <!-- Main content -->
      <section class="content">
        <div class='row mt-2 mb-5'>
          <div class='col'>
            <div id='tabla_entradas'></div>
          </div>
        </div>
      </section><!-- /.content -->
    </div><!-- /.content-wrapper -->
  </div>

  <!-- Main Footer -->
  <?php
      include('class_lib/footer.php');
      ?>

  <div id="ModalAgregarUsuario" class="modal">
    <div class="modal-usuarios">
      <div class='box box-primary'>
        <div class='box-header with-border'>
          <h3 id='titulo' class='box-title'>Agregar Usuario</h3>
        </div>
        <div class='box-body'>
          <div class='form-group'>
            <label for="username_txt" class="control-label">Nombre de Usuario:</label>
              <input autocomplete="off" type="search" id="username_txt" style="text-transform:lowercase"
                  class="form-control">
          </div>

          <div class='form-group'>
            <label for="password_txt" class="control-label">Contraseña:</label>
            <input autocomplete="new-password" type="password" id="password_txt" class="form-control">
          </div>

          <div class='form-group'>
            <label for="password2_txt" class="control-label">Repita Contraseña:</label>
            <input autocomplete="new-password" type="password" id="password2_txt" class="form-control">
          </div>

          <div class='form-group'>
            <label for="select_permisos" class="control-label">Permisos:</label>
            <select id="select_permisos" class="selectpicker mobile-device" title="Selecciona los Permisos"
              data-style="btn-info" data-dropup-auto="false" data-size="6" data-width="100%" multiple>
              <option value="pedidos">Cargar/Ver Pedidos</option>
              <option value="planificacionpedidos">Planificación de Pedidos</option>
              <option value="siembra">Siembra</option>
              <option value="camara">Cámara</option>
              <option value="mesadas">Mesadas</option>
              <option value="planentregas">Planificación de Entregas</option>
              <option value="ordenesentrega">Agenda de Entregas</option>
              <option value="historialentrega">Historial de Entregas</option>
              <option value="stock">Stock</option>
              <option value="panel">Panel de Control</option>
              <option value="ordenes_siembra">Órdenes de Siembra</option>
              <option value="datostecnicos">Datos Técnicos</option>
              <option value="remitos">Remitos</option>
              <option value="problemas">Problemas</option>
              <option value="estadisticas">Estadísticas</option>
            </select>
          </div>

          <div class="row mt-2">
            <div class="col">
              <div class="d-flex flex-row justify-content-end">
                <button type="button" class="btn btn-modal-bottom fa fa-close"
                  onClick="$('#ModalAgregarUsuario').modal('hide');"></button>
                <button type="button" class="btn btn-modal-bottom ml-3 fa fa-save"
                  onClick="GuardarUsuario();"></button>
              </div>
            </div>
          </div>
        </div>
      </div>

    </div>
    <div class="control-sidebar-bg"></div>
    <script>
      const id_usuario = "<?php echo $_SESSION['id_usuario'] ?>";
      const permisos = "<?php echo $_SESSION['permisos'] ?>";
      func_check(id_usuario, permisos.split(","));   
    </script>
</body>

</html>