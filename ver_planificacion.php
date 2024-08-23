<?php include "./class_lib/sesionSecurity.php"; ?>
<!DOCTYPE html>
<html>
  <head>
    <title>Planificación de Pedidos</title>
    <?php include "./class_lib/links.php"; ?>
    <?php include "./class_lib/scripts.php"; ?>
    <script src="dist/js/check_permisos.js?v=<?php echo $version ?>"></script>
    <script src="dist/js/ver_planificacion.js?v=<?php echo $version ?>"></script>
  </head>
  <body>
    <div id="miVentana">
    </div>
  <div id="ocultar">
    <div class="wrapper">
      <header class="main-header">
        <?php include('class_lib/nav_header.php');?>
      </header>
      <aside class="main-sidebar">
        <?php
        include('class_lib/sidebar.php');
        $dias = array("Domingo","Lunes","Martes","Miércoles","Jueves","Viernes","Sábado");
        $meses = array("Enero","Febrero","Marzo","Abril","Mayo","Junio","Julio","Agosto","Septiembre","Octubre","Noviembre","Diciembre");
        $fecha=$dias[date('w')]." ".date('d')." de ".$meses[date('n')-1]. " del ".date('Y') ;
        ?>
      </aside>
      <div class="content-wrapper">
        <section class="content-header">
          <h1>Planificación de Pedidos</h1>
          <ol class="breadcrumb">
            <li><a href="inicio.php"> Inicio</a></li>
            <li class="active">Planificación</li>
          </ol>
        </section>
        <!-- Main content -->
        <section class="content">
          <div id="TABLA">
            <div class="row">
              <div class="col-md-5">
                <div class="semanas-container">
                  <div class="row">
                    <div class="col-md-4">
                      <button class="btn btn-dark btn-block" style="font-weight:bold;" onClick="changeWeek(-1);">Semana Anterior</button>
                    </div>
                    <div class="col-md-4">
                      <button class="btn btn-dark btn-block" style="font-weight:bold;" onClick="changeWeek(0);">HOY</button>
                    </div>
                    <div class="col-md-4">
                      <button  class="btn btn-dark btn-block mt-lg-0 mt-2" style="font-weight:bold;" onClick="changeWeek(1);">Semana Siguiente</button>
                    </div>
                  </div>
                  <div class="row pt-lg-3 pt-2">
                    <div class="col-md-6">
                      <button class="btn btn-dark btn-block" id="btn_toPrevWeek" onClick="sendToWeek(0);">Enviar a la Semana anterior</button>
                    </div>
                    <div class="col-md-6">
                      <button class="btn btn-dark btn-block mt-lg-0 mt-2" id="btn_toNextWeek" onClick="sendToWeek(1);">Enviar a la Semana siguiente</button>
                    </div>
                  </div>
                </div>
              </div>

              <div class="col-md-3 text-center">
                <h4 id="num_semana" class="mt-lg-0 mt-3 mb-lg-0 mb-3 font-weight-bold">Semana Nº </h4>
              </div>

              <div class="col-md-4">
                  <div class="container-botones">
                    <div class="row">
                      <div class="col-md-6 text-right">
                        <button class="btn btn-secondary btn-block" onClick="DeseleccionarTodo();">Desmarcar Todo</button>
                      </div>
                      <div class="col-md-6 text-right">
                        <button class="btn btn-primary btn-block mt-lg-0 mt-2" style="font-weight:bold;"  id="guardar_btn" onClick="GuardarCambios();">Guardar Posiciones</button>
                      </div>
                    </div>
                    <div class="row">
                      <div class="col text-right">
                        <button class="btn btn-success btn-block mt-lg-3 mt-2" style="font-weight:bold;" onClick="MostrarModalOrden();">Generar Orden de Siembra</button>        
                      </div>
                    </div>
                  </div>
              </div>
            </div>  
              <table class="table table-responsive mt-3 w-100 d-block d-md-table" id="tablitaa">
                  <thead class="thead-light">
                    <tr id ="header">
                    </tr>
                  </thead>
                  <tbody>
                  </tbody>
              </table>
            <style>
              table,
              thead,
              tr,
              tbody,
              th,
              td {
                text-align: center;
                table-layout: fixed;
              }
              .table td {
                text-align: center;
                height: 70px;
              }
              
            </style> 
          </div> <!--FIN DIV TABLA -->
        </div>
        </section><!-- /.content -->

      <div id="ModalVerOrden" class="modal">
        <div id="ModalVerOrden" class="modal-content-verpedido">
            <div class='box box-primary'>
           <div class='box-header with-border'>
            <h4 class='box-title'>Órden de Siembra</b></h4>
            <button style="float:right;font-size: 1.5em;" class="btn fa fa-close" onClick="CerrarModalOrden()"></button>
            <button style='float:right;font-size: 1.5em;' class="btn btn-success fa fa-save mr-5 ml-2" onclick="GuardarOrden();"> GUARDAR</button>
            <button style='float:right;font-size: 1.5em;' class="btn btn-primary fa fa-print" onClick="printOrdenSiembra(1)"> IMPRIMIR</button> 
           </div>
           <div id="tablita">
           <div class='box-body'>
            <table id="tabla_detallepedido" class="table table-bordered table-responsive w-100 d-block d-md-table" role="grid">
              <thead>
              <tr role="row">
                <th class="text-center">Id</th>
                <th class="text-center">Producto</th>
                <th class="text-center">Bandejas<br>Plantas<br>Semillas</th>
                <th class="text-center">Cliente</th>
                <th class="text-center">Cód. Sobre</th>
                <th class="text-center"></th>
              </tr>
              </thead>
              <tbody>
              </tbody>
           </table>
          </div>
        </div>
          </div>
        </div>
      </div>

      <div id="ModalGetTotales" class="modal">
        <div class="modal-content-gettotales">
            <div class='box box-primary'>
           <div class='box-header with-border'>
            <h4 class='box-title' style="font-weight: bold;">Pendientes de Planificación día<span id='headerday'></span></h4>
            <button style="float:right; font-size: 2em" class="btn fa fa-close" onClick="$('#ModalGetTotales').modal('hide')"></button>
           </div>
           <div id="tablatotales">
           <div class='box-body'>
            <table id="tabla_detallepedido" class="table table-responsive w-100 d-block d-md-table" role="grid">
              <thead>
              <tr role="row">
                <th class="text-center" style="width: 80%;">Producto</th>
                <th class="text-center" style="width: 20%;">Cantidades</th>
                </tr>
              </thead>
              <tbody>
              </tbody>
           </table>
          </div>
        </div>
          </div>
        </div>
      </div> <!-- FIN MODAL TOTALES -->

      <div id="ModalVerEstado" class="modal">
          <div class="modal-content-verpedido">
              <div class='box box-primary'>
             <div class='box-header with-border'>
              <h4 class="box-title" id="nombre_cliente">Cliente:</h4>
              <button style="float:right;font-size: 1.6em" class="btn fa fa-close" onClick="$('#ModalVerEstado').modal('hide')"></button>
             </div>
             <div id="tablita">
                <div class='box-body'>
                  <div id='box_info'>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div><!-- /.content-wrapper -->
      <!-- Main Footer -->
      <?php
      include('./class_lib/footer.php');
      ?>
      </div>
</div>
      <!-- Add the sidebar's background. This div must be placed
           immediately after the control sidebar -->
      <div class="control-sidebar-bg"></div>
    </div><!-- ./wrapper -->
    </div>
    
    
    <script type="text/javascript">
      const id_usuario = "<?php echo $_SESSION['id_usuario'] ?>"; 
      const permisos = "<?php echo $_SESSION['permisos'] ?>"; 
      func_check(id_usuario, permisos.split(","));
    </script>
</body>
</html>