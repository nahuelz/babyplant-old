<?php include "./class_lib/sesionSecurity.php"; ?>
<!DOCTYPE html>
<html>

<head>
  <title>Agenda de Entregas</title>
  <?php include "./class_lib/links.php"; ?>
  <?php include "./class_lib/scripts.php"; ?>
  <script src="plugins/moment/moment.min.js"></script>
  <script src="dist/js/check_permisos.js?v=<?php echo $version ?>"></script>
  <script src="dist/js/ver_agenda_entregas.js?v=<?php echo $version ?>"></script>
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
        $dias = array("Domingo","Lunes","Martes","Miercoles","Jueves","Viernes","Sábado");
        $meses = array("Enero","Febrero","Marzo","Abril","Mayo","Junio","Julio","Agosto","Septiembre","Octubre","Noviembre","Diciembre");
        $fecha=$dias[date('w')]." ".date('d')." de ".$meses[date('n')-1]. " del ".date('Y') ;
        ?>
      </aside>
      <div class="content-wrapper">
        <section class="content-header">
          <h1>Agenda de Entregas</h1>
          <ol class="breadcrumb">
            <li><a href="inicio.php"> Inicio</a></li>
            <li class="active">Agenda de Entregas</li>
          </ol>
        </section>
        <!-- Main content -->
        <section class="content">
          <div id="TABLA">
            <div class="row">
              <div class="col-md-4">
                <div class="dias-container">
                  <div class="row">
                    <div class="col-md-4">
                      <button class="btn btn-dark btn-block" onClick="changeWeek(-1);" style="font-weight: bold;">Día
                        Anterior</button>
                    </div>
                    <div class="col-md-4">
                      <button class="btn btn-dark btn-block" onClick="changeWeek(0);"
                        style="font-weight: bold;">HOY</button>
                    </div>
                    <div class="col-md-4">
                      <button class="btn btn-dark btn-block mt-lg-0 mt-2" onClick="changeWeek(1);"
                        style="font-weight: bold;">Día Siguiente</button>
                    </div>
                  </div>
                </div>
              </div>

              <div class="col-md-4 text-center">
                <h4 id="header" class="mt-lg-0 mt-3 mb-lg-0 mb-3" style="font-weight:bold;"></h4>
              </div>

              <div class="col-md-4">
                <div class="fecha-container">
                  <div class="row">
                    <div class="col text-right">
                      <input class="form-control w-50 pull-right" type='text' data-date-format='dd/mm/yy'
                        value='<?php echo date(' d/m/Y'); ?>' class="datepicker" id="currentfecha_txt"
                      placeholder="DD-MM-AAAA"/>
                    </div>
                  </div>
                </div>
              </div>
            </div>

            <div class="row mt-2">
              <div class="col-md-6">
              </div>
              <div class="col-md-6">
                <div class="contenedor">
                  <div class="row">
                    <div class="col-md-6">
                      <button type="button" class="btn btn-primary btn-block btn-md" id="btn_printremitos"
                        onClick="modalEntrega();"><i class='fa fa-file'></i> GENERAR REMITO</span></button>
                    </div>
                    <div class="col-md-6">
                      <button type="button" class="btn btn-primary btn-block btn-md mt-lg-0 mt-2"
                        id="btn_printcliente" onClick="printAgenda(1);"><i class='fa fa-print'></i> IMPRIMIR
                          AGENDA</span></button>
                    </div>
                  </div>
                </div>
              </div>
            </div>
            <div class="row">
              <div class="col">
                <table class="table table-responsive w-100 d-block d-md-table mt-3 bg-light" role="grid" id="tablitaa">
                  <thead>
                    <tr role="row">
                      <th class="text-center">Orden<br>Siembra</th>
                      <th class="text-center">Producto</th>
                      <th class="text-center">Cliente</th>
                      <th class="text-center">Cantidad<br>Entrega</th>
                      <th class="text-center">Mesadas</th>
                      <th class="text-center">Domicilio/Teléfono</th>
                      <th class="text-center">Estado</th>
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
      </section><!-- /.content -->

      <?php include("modals/entrega_inmediata.php") ?>

      <div id="ModalEditarEntrega" class="modal">
        <div class="modal-content3">
          <div class='box box-primary'>
            <div class='box-header with-border'>
              <h3 class='box-title'>Modificar Entrega Programada</h3>
            </div>
            <div class='box-body'>
              <div class='form-group'>
                <div class="row">
                  <div class="col-md-8">
                    <label for="fecha_nuevaentrega" class="control-label">Fecha:</label><br>
                    <input type='text' data-date-format='dd/mm/yy' class="datepicker form-control"
                      id="fecha_nuevaentrega" placeholder="DD-MM-AAAA" />
                  </div>
                </div>
              </div>
              <div class='form-group'>
                <div class="row">
                  <div class="col-md-4">
                    <label for="cantidad_entrega" class="control-label">Cantidad Bandejas:</label>
                    <input type="number" min="0" step="1" id="cantidad_entrega" class="form-control"
                      style="font-size: 1.4em; font-weight: bold;">
                  </div>
                </div>
              </div>
            </div>
          </div>
          <div class="row pull-right">
            <div align="col">
              <button type="button" class="btn fa fa-close btn-modal-bottom" id="btn_cancel"
                onClick="$('#ModalEditarEntrega').modal('hide');"></button>
              <button type="button" class="btn btn-modal-bottom fa fa-save ml-2 mr-3" id="btn_guardarcliente"
                onClick="guardarCambioEntrega();"></button>
            </div>
          </div>
          <span id="id_agendahide" style="display:none"></span>
        </div>
      </div>
    </div>
  </div> <!-- MODAL POPUP FIN -->

  


    
    <?php include('./class_lib/footer.php');?>

  </div>
  </div>
  <div class="control-sidebar-bg"></div>



  </div>

  </div>

  <style>
    tr.selected2 td {
      background-color: #333;
      color: #fff;
    }
  </style>



  <script type="text/javascript">
    const id_usuario = "<?php echo $_SESSION['id_usuario'] ?>";
    const permisos = "<?php echo $_SESSION['permisos'] ?>";
    func_check(id_usuario, permisos.split(","));

  </script>
</body>

</html>