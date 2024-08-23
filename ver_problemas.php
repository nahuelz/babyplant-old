<?php include "./class_lib/sesionSecurity.php"; ?>
<!DOCTYPE html>
<html>
  <head>
    <title>Problemas</title>
    <?php include "./class_lib/links.php"; ?>
    <?php include "./class_lib/scripts.php"; ?>
    <link rel="stylesheet" href="plugins/select2/select2.min.css">
    <link rel="stylesheet" href="plugins/daterangepicker/daterangepicker-bs3.css">
    <script src="dist/js/ver_pedidos.js?v=<?php echo $version ?>"></script>
    <script src="dist/js/ver_problemas.js?v=<?php echo $version ?>"></script>
    <script src="dist/js/check_permisos.js?v=<?php echo $version ?>"></script>
    <script src="plugins/moment/moment.min.js"></script>
    <script src="plugins/daterangepicker/daterangepicker.js"></script>
  </head>
  <body onload="busca_entradas();">
    <div id="miVentana">
    </div>
    <div id="ocultar">
      <div class="wrapper">
        <header class="main-header">
          <?php include('class_lib/nav_header.php');?>
        </header>
        <aside class="main-sidebar">
          <?php include('class_lib/sidebar.php'); ?>
        </aside>
      
        <div class="content-wrapper">
          <section class="content-header">
            <h1>Pedidos con Problemas</h1>
            <ol class="breadcrumb">
              <li><a href="inicio.php"> Inicio</a></li>
              <li class="active">Pedidos con Problemas</li>
            </ol>
          </section>
          <!-- Main content -->
          <section class="content">
            <!-- Your Page Content Here -->
            <div class='row'>
              <div class='col-md-5'>
              </div>
              <div class='col-md-7 text-center text-lg-right'>
                <button type="button" class="btn btn-primary btn-lg fa fa-print" id="btn_printcliente" onClick="print_Busqueda(1);"> <span style="font-family: Calibri"> IMPRIMIR REGISTROS</span></button>
              </div>
            </div>

            <div class="row mt-3 mb-5">
              <div class='col'>
                <div id='tabla_entradas'></div>
              </div>
            </div>
          </section><!-- /.content -->
          </div><!-- /.content-wrapper -->

          <?php include('class_lib/footer.php'); ?>
          
          <?php include("modals/ver_pedido.php"); ?>
          <?php include("modals/ver_detalle_pedido.php") ?>   
          <?php include("modals/enviar_revision.php"); ?>
          
      
          <div class="control-sidebar-bg"></div>
        </div><!-- content-wrapper -->
      </div> <!-- wrapper -->
    </div>

   
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