<?php include "./class_lib/sesionSecurity.php"; ?>
<!DOCTYPE html>
<html>
  <head>
    <title>Administrar Productos</title>
    <?php include "./class_lib/links.php"; ?>
    <?php include "./class_lib/scripts.php"; ?>
    <link rel="stylesheet" href="plugins/select2/select2.min.css">
    <link rel="stylesheet" href="plugins/daterangepicker/daterangepicker-bs3.css">
    <script src="dist/js/ver_tipos.js?v=<?php echo $version ?>"></script>
    <script src="dist/js/check_permisos.js?v=<?php echo $version ?>"></script>
    
  </head>
  <body onload="busca_productos(null);">
    <div id="miVentana">
    </div>
    
    <div id="ocultar">
    <div class="wrapper">

      <!-- Main Header -->
      <header class="main-header">

        <!-- Logo -->
        <?php
        include('class_lib/nav_header.php');
        ?>

      </header>
      <!-- Left side column. contains the logo and sidebar -->
      <aside class="main-sidebar">

        <!-- sidebar: style can be found in sidebar.less -->
        <?php
        include('class_lib/sidebar.php');


        ?>
        <!-- /.sidebar -->
      </aside>

      <!-- Content Wrapper. Contains page content -->
      <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <section class="content-header">
          <h1>
            Tipos de Producto
            
          </h1>
          <ol class="breadcrumb">
            <li><a href="inicio.php"> Inicio</a></li>
            <li class="active">Tipos de Producto</li>
          </ol>
        </section>

        <!-- Main content -->
        <section class="content">
          <div class="row">
            <div class="col text-right">
                <button class="btn btn-success btn-lg fa fa-plus-square" style="font-size: 1.3em" onclick="MostrarModalAgregarProducto(null);"></button>
            </div>
          </div>

          <!-- Your Page Content Here -->
          <div class='row mt-2 mb-5'>
          <div class='col'>
          <div id='tabla_entradas'></div>
          </div>
          </div>

        </section><!-- /.content -->
         </div><!-- /.content-wrapper -->


      <!-- Main Footer -->
      <?php
      include('class_lib/footer.php');
      ?>



      <div id="ModalAgregarProducto" class="modal"> <!--MODALITO AGREGAR ARTICULO -->
          <!-- Modal content -->
          <div class="modal-tipo">
            <div class='box box-primary'>
           <div class='box-header with-border'>
           <h3 id='titulo' class='box-title'>Agregar Tipos</h3>
           </div>
           <div class='box-body'>
            <div class='form-group'>
              <div class='row'>
                <div class='col'>
                <label for="proveedor" class="control-label">Nombre del Tipo de Producto:</label>
                <input type="text" id="nombrenewtipo_txt" style="text-transform:uppercase" class="form-control" placeholder="Ingresa Nombre del Nuevo Tipo"> 
                </div>
              </div>
            </div>
            <div class='form-group'>
              <div class='row'>
                <div class='col-md-6'>
                <label for="proveedor" class="control-label">Días en Cámara:</label>
                <input type="number" id="cant_dias_camara_txt" min="0" step="1" style="text-transform:uppercase" class="form-control" placeholder="Días que debe permanecer en cámara"> 
                </div>
              </div>
            </div>

            <div class='row'>
                <div class='col-md-6'>
                <label for="proveedor" class="control-label">Tipos de Bandeja:</label>
                </div>
            </div>
            <div class="row">
              <div class="col-md-6">

              <select id="select_tipobandeja" class="selectpicker mobile-device" title="Selecciona Tipo Bandeja" data-style="btn-info" data-width="100%" multiple>
                  <option value="288">288</option>
                  <option value="200">200</option>
                  <option value="162">162</option>
                  <option value="128">128</option>
                  <option value="72">72</option>
                  <option value="50">50</option>
                  <option value="25">25</option>
                  <option value="49">49</option>
                </select>
              </div>
            </div>
            
            <div class='row' style="margin-top: 15px">
                <div class='col'>
                <label class="control-label">PRECIOS ($):</label>
                </div>
            </div>

            <div class="row">
              <div class="col">
                <table id="table-precios" class="table">
                  <thead class="thead-dark">
                    <tr>
                      <th scope="col">Tamaño</th>
                      <th scope="col">Precio</th>
                      <th scope="col">Precio C/ Semilla</th>
                    </tr>
                  </thead>
                  <tbody>
                  </tbody>
                </table>
              </div>
            </div>

            <div align="right">
              <button type="button" class="btn fa fa-close btn-modal-bottom" id="btn_cancel" onClick="CerrarModalProducto();"></button>
              <button type="button" class="btn fa fa-save btn-modal-bottom ml-2" id="btn_guardartipo" onClick="GuardarTipo();"></button>
            </div>
            </div>
        </div>
      
      <!-- Add the sidebar's background. This div must be placed
           immediately after the control sidebar -->
      <div class="control-sidebar-bg"></div>
    </div><!-- ./wrapper -->
  </div> <!-- MODAL FIN -->
    
    <script src="plugins/moment/moment.min.js"></script>
    <script src="plugins/daterangepicker/daterangepicker.js"></script>
    <script>
      let id_usuario = "<?php echo $_SESSION['id_usuario'] ?>"; 
       let permisos = "<?php echo $_SESSION['permisos'] ?>"; 
       func_check(id_usuario, permisos.split(","));   
    </script>
    
  </body>
</html>