<?php include "./class_lib/sesionSecurity.php"; ?>
<!DOCTYPE html>
<html>
  <head>
    <title>Ver Remitos</title>
    <?php include "./class_lib/links.php"; ?>
    <?php include "./class_lib/scripts.php"; ?>
    <link rel="stylesheet" href="plugins/select2/select2.min.css">
    <link rel="stylesheet" href="plugins/daterangepicker/daterangepicker-bs3.css">
    <script src="dist/js/ver_remitos.js?v=<?php echo $version ?>"></script>
    <script src="dist/js/check_permisos.js?v=<?php echo $version ?>"></script>
  </head>
  <body onload="busca_entradas();">
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
            Ver Remitos
            
          </h1>
          <ol class="breadcrumb">
            <li><a href="inicio.php"> Inicio</a></li>
            <li class="active">Ver Remitos</li>
          </ol>
        </section>
        <!-- Main content -->
        <section class="content">
          <!-- Your Page Content Here -->
          <div class='row'>
          <div class='col-md-6'>
          <div class='box box-primary'>
          <div class='box-header with-border'>
          <h3 class='box-title'>Buscar</h3>
          <button class='btn btn-primary pull-right' onclick='expande_busqueda()' id='btn-busca'><i class='fa fa-caret-down'></i> Busqueda Avanzada</button>
          </div>

          <div class='box-body'>
            <div class="form-group">
                    <div class='row'>
                      <div class='col-sm-2'>
                        <label>Fechas:</label>
                      </div>
                      <div class='col-sm-3'>
                        <div class="input-group">
                          <button class="btn btn-default pull-left" id="daterange-btn">
                            <i class="fa fa-calendar"></i> Seleccionar...
                            <i class="fa fa-caret-down"></i>
                          </button>
                        </div>
                      </div>
                      <div class='col-sm-7'>
                        <button class='btn btn-primary pull-right' onclick='busca_entradas();' id='btn-busca'><i class='fa fa-search'></i> Buscar...</button>
                      </div>
                    </div>
                    <span class='fe'></span>
                    <input type='hidden'  class='form-control' id='fi' value=''>
                    <input type="hidden"  class='form-control' id='ff' value=''>
            </div><!-- /.form group -->
         
        <div id="contenedor_busqueda" style="display:none">
        <div class="form-group">
          <div class='row'>
            <div class='col-sm-2'>
              <label>Cliente:</label>
            </div>
            <div class='col-sm-5'>
              <div class="btn-group" style="width:100%">
                <input id="busca_cliente" style="text-transform:uppercase;" type="search" class="form-control">
                <span id="searchclear" onClick="$('#busca_cliente').val('');" class="glyphicon glyphicon-remove-circle"></span>
              </div>
            </div>
          </div>
        </div>
      </div> <!-- CONTENEDOR BUSQUEDA -->
          </div>
          </div>
          </div>
          
          </div>
          <div class="row">
            <div class='col'>
              <div id='tabla_entradas'></div>
            </div>
          </div>

        </section><!-- /.content -->
         </div><!-- /.content-wrapper -->

        <div id="ModalVerRemito" class="modal">
          <div class="modal-content-verpedido">
            <div class='box box-primary'>
             <div class='box-header with-border'>
              <h4 class="box-title" id="nombre_cliente2">Ver Remito</h4>
              <button style="float:right;" class="btn btn-modal-top fa fa-close" onClick="$('#ModalVerRemito').modal('hide');"></button>
              <button style='float:right;' class="btn btn-modal-top fa fa-print" onClick="print_Remito(1)"> IMPRIMIR</button> 
             </div>
                <div class='box-body'>
                  <div id='remito_container'>
                  </div>
                </div>
            </div>
          </div>
        </div> <!-- FIN MOD -->
      <!-- Main Footer -->
      <?php
      include('class_lib/footer.php');
      ?>
      <!-- Add the sidebar's background. This div must be placed
           immediately after the control sidebar -->
      <div class="control-sidebar-bg"></div>
    </div><!-- ./wrapper -->
  </div> <!-- ID OCULTAR-->
    <!-- REQUIRED JS SCRIPTS -->
    
    <style>
      .table2 tr.selected td {
        background-color: #333;
        color: #fff;    
      }
      .table2 tr.selected2 td {
        background-color: #333;
        color: #fff;    
      }
    </style> 

    <script src="plugins/moment/moment.min.js"></script>
    <script src="plugins/daterangepicker/daterangepicker.js"></script>
    <script type="text/javascript">
      let id_usuario = "<?php echo $_SESSION['id_usuario'] ?>"; 
      let permisos = "<?php echo $_SESSION['permisos'] ?>"; 
      func_check(id_usuario, permisos.split(","));

      $(document).ready(function () {
        if (/Android|webOS|iPhone|iPad|iPod|BlackBerry/i.test(navigator.userAgent)) {
          $('.selectpicker').selectpicker('mobile');
        }
        else {
          let elements = document.querySelectorAll('.mobile-device'); 
          for(let i = 0; i < elements.length; i++)
          {
              elements[i].classList.remove('mobile-device');
          }
          $('.selectpicker').selectpicker({});
        }
        $.datepicker.setDefaults($.datepicker.regional["es"]);
      });
    </script>
  </body>
</html>