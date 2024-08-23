
<!DOCTYPE html>
<html>

<head>
  <title>Babyplant - Administración</title>
  <?php include "./class_lib/sesionSecurity.php"; ?>
  <?php include "./class_lib/links.php"; ?>
  <?php include "./class_lib/scripts.php"; ?>
  <script src="dist/js/init.js?v=<?php echo $version ?>"></script>
  <script>
    $(document).ready(function () {
      const id_usuario = "<?php echo $_SESSION['id_usuario'] ?>";
      const permisos = "<?php echo $_SESSION['permisos'] ?>";
      check_permisos(id_usuario, permisos.split(","));
    });

    function check_permisos(id_usuario, permisos) {

      $("#contenedor_modulos").html("");

      if (id_usuario == "1") {
        pone_agregar_pedidos();
        pone_pedidos();
        pone_planificacionpedidos();
        pone_siembra();
        pone_camara();
        pone_mesadas();
        pone_planentregas();
        pone_ordenesentrega();
        pone_historialentregas();
        pone_stock();
        pone_ordenes_siembra();
        pone_datostecnicos();
        pone_remitos();
        pone_problemas();
        pone_situacion();
        pone_estadisticas();
        $("#contenedor_panel").html(`<a href="#"><i class="fa fa-bars"></i> <span>Panel de Control</span> <i class="fa fa-angle-left pull-right"></i></a>
                          <ul class="treeview-menu menu-open" style="display:block;"> 
                            <li><a href="ver_variedades.php"><i class="fa fa-arrow-circle-right"></i> Variedades</a></li> 
                            <li><a href="ver_tipos.php"><i class="fa fa-arrow-circle-right"></i> Tipos (Especies)</a></li> 
                            <li><a href="ver_subtipos.php"><i class="fa fa-arrow-circle-right"></i> Subtipos (Subespecies)</a></li> 
                            <li><a href="ver_clientes.php"><i class="fa fa-arrow-circle-right"></i> Clientes</a></li> 
                            <li><a href="ver_usuarios.php"><i class="fa fa-arrow-circle-right"></i> Usuarios</a></li> 
                            <li><a href="crear_backup.php"><i class="fa fa-arrow-circle-right"></i> Copia de Seguridad</a></li> 
                          </ul>`);

      } else {
        if (permisos.length > 0) {
          const array = permisos;
          for (let i = 0; i < array.length; i++) {
            if (array[i] == "pedidos") {
              pone_agregar_pedidos();
              pone_pedidos();
            }
            else if (array[i] == "planificacionpedidos") {
              pone_planificacionpedidos();
            }
            else if (array[i] == "siembra") {
              pone_siembra();
            }
            else if (array[i] == "camara") {
              pone_camara();
            }
            else if (array[i] == "mesadas") {
              pone_mesadas();
            }
            else if (array[i] == "planentregas") {
              pone_planentregas();
            }
            else if (array[i] == "ordenesentrega") {
              pone_ordenesentrega();
            }
            else if (array[i] == "historialentregas") {
              pone_historialentregas();
            }
            else if (array[i] == "stock") {
              pone_stock();
            }
            else if (array[i] == "ordenes_siembra") {
              pone_ordenes_siembra();
            }
            else if (array[i] == "datostecnicos") {
              pone_datostecnicos();
            }
            else if (array[i] == "remitos") {
              pone_remitos();
            }
            else if (array[i] == "problemas") {
              pone_problemas();
            }
            else if (array[i] == "situacion") {
              pone_situacion();
            }
            else if (array[i] == "estadisticas") {
              pone_estadisticas();
            }

            else if (array[i] == "panel") {

              $("#contenedor_panel").html(`<a href="#"><i class="fa fa-bars"></i> <span>Panel de Control</span> <i class="fa fa-angle-left pull-right"></i></a> 
                          <ul class="treeview-menu menu-open" style="display: block;"> 
                            <li><a href="ver_variedades.php"><i class="fa fa-arrow-circle-right"></i> Variedades</a></li> 
                            <li><a href="ver_tipos.php"><i class="fa fa-arrow-circle-right"></i> Tipos (Especies)</a></li> 
                            <li><a href="ver_subtipos.php"><i class="fa fa-arrow-circle-right"></i> Subtipos (Subespecies)</a></li> 
                            <li><a href="ver_clientes.php"><i class="fa fa-arrow-circle-right"></i> Clientes</a></li> 
                            <li><a href="crear_backup.php"><i class="fa fa-arrow-circle-right"></i> Copia de Seguridad</a></li> 
                          </ul>`);
            }
          }
        }
      }

    }
  </script>
</head>

<body>

  <div class="wrapper">
    <header class="main-header">
      <?php
        include('class_lib/nav_header.php');
        ?>
    </header>
    <!-- Left side column. contains the logo and sidebar -->
    <aside class="main-sidebar">
      <!-- sidebar: style can be found in sidebar.less -->
      <?php
        include('class_lib/sidebar.php');
        include('class_lib/class_conecta_mysql.php');
        $dias = array("Domingo","Lunes","Martes","Miércoles","Jueves","Viernes","Sábado");
        $meses = array("Enero","Febrero","Marzo","Abril","Mayo","Junio","Julio","Agosto","Septiembre","Octubre","Noviembre","Diciembre");
        $fecha=$dias[date('w')]." ".date('d')." de ".$meses[date('n')-1]. " del ".date('Y') ;
        ?>
      <!-- /.sidebar -->
    </aside>

    <!-- Content Wrapper. Contains page content -->
    <div class="content-wrapper">
      <!-- Content Header (Page header) -->
      <section class="content-header">
        <h1>
          <small>
            <?php echo $fecha; ?>
          </small>
        </h1>

      </section>
      <!-- Main content -->
      <section class="content">
        <div class='row row-modulos'>

          <div class="col-6 col-md-3 col-agregar-pedidos d-none">
          </div>

          <div class="col-6 col-md-3 col-pedidos d-none">
          </div>

          <div class="col-6 col-md-3 col-planificacion d-none">
          </div>

          <div class="col-6 col-md-3 col-siembra d-none">
          </div>

          <div class="col-6 col-md-3 col-camara d-none">
          </div>


          <div class="col-6 col-md-3 col-ordenes-siembra d-none">
          </div>

          <div class="col-6 col-md-3 col-stock d-none">
          </div>

          <div class="col-6 col-md-3 col-problemas d-none">
          </div>

          <div class="col-6 col-md-3 col-planificacion-entregas d-none">
          </div>

          <div class="col-6 col-md-3 col-agenda-entregas d-none">
          </div>

          <div class="col-6 col-md-3 col-historial-entregas d-none">
          </div>

          <div class="col-6 col-md-3 col-remitos d-none">
          </div>

          <div class="col-6 col-md-3 col-estadisticas d-none">
          </div>

          <div class="col-6 col-md-3 col-mesadas d-none">
          </div>

          <div class="col-6 col-md-3 col-datos-tecnicos d-none">
          </div>

          <div class="col-6 col-md-3 col-situacion d-none">
          </div>
        </div>
      </section>
    </div><!-- /.content-wrapper -->
    <!-- Main Footer -->
    <?php include('./class_lib/footer.php'); ?>
    <!-- Add the sidebar's background. This div must be placed
           immediately after the control sidebar -->
    <div class="control-sidebar-bg"></div>
  </div><!-- ./wrapper -->

</body>

</html>