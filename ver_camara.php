<?php include "./class_lib/sesionSecurity.php";?>
<!DOCTYPE html>
<html>

<head>
  <title>Cámara</title>
  <?php include "./class_lib/links.php";?>
  <?php include "./class_lib/scripts.php";?>
  <script src="dist/js/check_permisos.js?v=<?php echo $version ?>"></script>
  <script src="dist/js/ver_mesadas.js?v=<?php echo $version ?>"></script>
  <script src="dist/js/ver_camara.js?v=<?php echo $version ?>"></script>
  <style>
    .table tr.selected2 td {
      background-color: #333;
      color: #fff;
    }

    table,
    thead,
    tr,
    tbody,
    th,
    td {
      text-align: center;
      table-layout: fixed;
      word-wrap: break-word;
    }

    .table td {
      text-align: center;
      height: 70px;
    }
  </style>
</head>

<body>
  <div id="miVentana">
  </div>
  <div id="ocultar">
    <div class="wrapper">
      <!-- Main Header -->
      <header class="main-header">
        <!-- Logo -->
        <?php
include 'class_lib/nav_header.php';
?>
      </header>
      <!-- Left side column. contains the logo and sidebar -->
      <aside class="main-sidebar">
        <!-- sidebar: style can be found in sidebar.less -->
        <?php
include 'class_lib/sidebar.php';
?>
        <!-- /.sidebar -->
      </aside>
      <!-- Content Wrapper. Contains page content -->
      <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <section class="content-header">
          <h1>
            Cámara
          </h1>

          <ol class="breadcrumb">
            <li><a href="inicio.php"> Inicio</a></li>
            <li class="active">Cámara</li>
          </ol>
        </section>
        <!-- Main content -->
        <section class="content">
          <div class="tab">
            <button class="tablinks" onclick="openTab(event, 'Entradas');loadPedidos();"
              id="defaultOpen">Entradas</button>
            <button class="tablinks" onclick="openTab(event, 'Salidas');changeWeek2(0);">Salidas</button>
          </div>

          <div id="Entradas" class="tabcontent">
            <div class="row">
              <div class="col-md-5">
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

              <div class="col-md-5 text-center">
                <h4 id="header" class="mt-lg-1 mt-3 mb-lg-1 mb-3 font-weight-bold"></h4>
              </div>

              <div class="col-md-2 text-lg-right text-center">
                <input type='text' data-date-format='dd/mm/yy' value="<?php echo date(' d/m/Y'); ?>" class="datepicker
                form-control" id="currentfecha_txt" placeholder="DD-MM-AAAA" />
              </div>
            </div>

            <div class="row mt-3 mb-3">
              <div class="col-md-3">
                <button onClick='enviarCamara()' class="btn btn-primary btn-block font-weight-bold">ENVIAR A
                  CÁMARA</button>
              </div>
              <div class="col-md-9 text-right">
                <button onClick='imprimirCamara(1)' class="btn btn-primary"><i class="fa fa-print"></i>
                  IMPRIMIR</button>
              </div>
            </div>
            <div class="row mt-3 mb-5">
              <div class="col">
                <table class="table table-bordered table-responsive w-100 d-block d-md-table" role="grid" id="tablitaa">
                  <thead>
                    <tr role="row">
                      <th class="text-center" style="width:150px !important;">Nº Orden</th>
                      <th class="text-center" style="width:40% !important;">Producto</th>
                      <th class="text-center">Bandejas</th>
                      <th class="text-center">Estado</th>
                    </tr>
                  </thead>
                  <tbody>
                  </tbody>
                </table>
              </div>
            </div>

          </div>
          <!--FIN DIV TAB1 -->
          <div id="Salidas" class="tabcontent">

            <div class="row">
              <div class="col-md-5">
                <div class="container-semanas">
                  <div class="row">
                    <div class="col-md-4">
                      <button class="btn btn-dark btn-block" onClick="changeWeek2(-1);" style="font-weight: bold">Semana
                        Anterior</button>
                    </div>
                    <div class="col-md-4">
                      <button class="btn btn-dark btn-block" onClick="changeWeek2(0);"
                        style="font-weight: bold">HOY</button>
                    </div>
                    <div class="col-md-4">
                      <button class="btn btn-dark btn-block mt-lg-0 mt-2" onClick="changeWeek2(1);"
                        style="font-weight: bold">Semana Siguiente</button>
                    </div>
                  </div>
                </div>
              </div>

              <div class="col-md-7 text-center">
                <h4 id="num_semana" class="mt-lg-1 mt-3 mb-lg-1 mb-3 font-weight-bold">Semana Nº </h4>
              </div>
            </div>
            <div class="row">
              <br>
            </div>
            <table class="table table-bordered table-responsive w-100 d-block d-md-table" id="tablasalidas">
              <thead class="thead-light">
                <tr id="headerweek"></tr>
              </thead>
              <tbody>
              </tbody>
            </table>
 
          </div>
          <!--FIN DIV TAB2 -->
      </div>
      </section><!-- /.content -->
      <div id="ModalVerOrden" class="modal">
        <div id="ModalVerOrden" class="modal-content-verpedido">
          <div class='box box-primary'>
            <div class='box-header with-border'>
              <h4 class='box-title'><b>Órden de Siembra Nº <span id='num_orden'></span></b></h4>
              <button style="float:right;font-size:1.6em" class="btn fa fa-close" onClick="CerrarModalOrden()"></button>
              <button style='float:right;font-size:1.6em' class="btn fa fa-print"
                onClick="printOrdenSiembra(1)">Imprimir</button>
            </div>

            <div id="tablita">
              <div class='box-body'>
                <table id="tabla_detallepedido" class="table table-bordered table-responsive w-100 d-block d-md-table"
                  role="grid">
                  <thead>
                    <tr role="row">
                      <th class="text-center">Id</th>
                      <th class="text-center">Producto</th>
                      <th class="text-center">Bandejas</th>
                      <th class="text-center">Semillas</th>
                      <th class="text-center">Plantas</th>
                      <th class="text-center">Cód. Sobre</th>
                      <th class="text-center">Estado</th>
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

      <div id="ModalVerEstado" class="modal">
        <div class="modal-content-verpedido">
          <div class='box box-primary'>
            <div class='box-header with-border'>
              <h4 class="box-title" id="nombre_cliente">Cliente:</h4>
              <button style="float:right;font-size: 1.6em" class="btn fa fa-close"
                onClick="$('#ModalVerEstado').modal('hide')"></button>
            </div>

            <div id="tablita">
              <div class='box-body'>
                <div id='box_info'>
                </div>
              </div>
            </div>
          </div>
          <div id="id_artpedidocamara" style="display:none;"></div>
        </div>
      </div>

      <div id="ModalEnviaraMesadas" class="modal">
        <div class="modal-content-mesadas">
          <div class='box box-primary'>
            <div class='box-header with-border'>
              <h4 class="box-title" id="nombre_cliente">Selecciona Mesadas</h4>
              <button style="font-size: 1.6em" class="btn fa fa-close pull-right"
                onClick="CerrarModalMesadas()"></button>
              <button style="margin-right: 2em" type="button" class="btn btn-success pull-right" id="btn_guardarcliente"
                onClick="GuardarMesadas();"><i class="fa fa-save"></i> GUARDAR</button>
            </div>
            <div class='row'>
              <div class='col-md-7'>
                <div class='box-body'>
                  <div id='box_info'>
                    <h4 id='bandejas_pendientes'></h4>
                    <div id="contenedor">
                      <div class="row">
                        <div class="col text-center">
                          <div class="row row-contenedor">
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>




              <div class='col-md-4 order-first order-md-last'>
                <div id="contenedor_cantidades">
                  <h4>Faltan asignar: <span id='quedan_bandejas'></span></h4>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div> <!-- FIN MODAL MESADAS -->

      <div id="ModalProblema" class="modal">
        <div class="modal-content3">
          <div class='box box-primary'>
            <div class='box-header with-border'>
              <h3 id="titulo-problema" class='box-title'></h3>
            </div>
            <div class='box-body'>
              <div id="contenedor_problema">
              </div>
            </div>
            <div class="row pull-right">
              <div class="col">
                <button type="button" class="btn fa fa-close btn-modal-bottom" id="btn_cancel"
                  onClick="$('#ModalProblema').modal('hide');"></button>
                <button type="button" class="btn fa fa-save btn-modal-bottom ml-3 mr-2" id="btn_guardarcliente"
                  onClick="guardarProblema();"></button>
              </div>
            </div>
            <span id="id_ordenproblemahide" style="display:none"></span>
            <span id="tipo_problema" style="display:none"></span>
          </div>
        </div>
      </div> <!-- MODAL PROBLEMA FIN -->

      <div id="ModalCambiarEstado" class="modal">
        <!-- Modal content -->
        <div class="modal-content3">
          <div class='box box-primary'>
            <div class='box-header with-border'>
              <h3 class='box-title'>Ingresar a Cámara - Orden Nº <span id="num_orden2"></span></h3>
            </div>
            <div class='box-body'>
              <div class='form-row'>
                <div class="form-group col-md-4">
                  <label for="fechasiembra_txt" class="control-label">Fecha:</label>
                  <input type='text' data-date-format='dd/mm/yy' value='<?php echo date(' d/m/Y'); ?>' class="datepicker
                  form-control" id="fechasiembra_txt" style="font-size:1.4em"/>
                </div>
              </div>
              <div class='form-row'>
                <div class="form-group col-md-4">
                  <label for="appt" class="control-label">Hora:</label>
                  <input class="form-control" type="time" style="margin-left:9px;padding:2px;font-size: 1.4em" id="appt"
                    name="appt" min="07:00" max="20:00" required>
                </div>
              </div>
            </div>
          </div>
          <div class="row">
            <div class="col">
              <div class="d-flex flex-row justify-content-end">
                <button type="button" class="btn fa fa-close btn-modal-bottom" id="btn_cancel"
                  onClick="CerrarModalCambio();"></button>
                <button type="button" class="btn fa fa-save btn-modal-bottom ml-3 mr-2" id="btn_guardarcliente"
                  onClick="GuardarCambioEstado();"></button>
              </div>
            </div>
            <span style="display: none;" id="id_artpedidohide"></span>
          </div>
        </div>
      </div>
    </div>
  </div> <!-- MODAL CAMBIAR ESTADO FIN -->

  <div id="ModalCamaraMultiple" class="modal">
    <!-- Modal content -->
    <div class="modal-content3">
      <div class='box box-primary'>
        <div class='box-header with-border'>
          <h3 class='box-title'>Múltiples Órdenes a Cámara</h3>
        </div>
        <div class='box-body'>
          <div class='form-row'>
            <div class="form-group col-md-6">
              <label for="fechacamara_txt" class="control-label">Fecha:</label>
              <input type='text' data-date-format='dd/mm/yy' value='<?php echo date(' d/m/Y'); ?>' class="datepicker
              form-control" id="fechacamara_txt" style="font-size:1.4em"/>
            </div>

          </div>

          <div class='form-row'>
            <div class="form-group col-md-6">
              <label for="appt" class="control-label">Hora:</label>

              <input class="form-control" type="time" style="margin-left:9px;padding:2px;font-size: 1.4em"
                id="horacamara" name="appt" min="07:00" max="20:00" required>
            </div>
          </div>
        </div>
      </div>
      <div class="row">
        <div class="col">
          <div class="d-flex flex-row justify-content-end">
            <button type="button" class="btn fa fa-close btn-modal-bottom" id="btn_cancel"
              onClick="CerrarModalMultiple();"></button>
            <button type="button" class="btn fa fa-save btn-modal-bottom ml-3 mr-2"
              onClick="GuardarCamaraMultiple();"></button>
          </div>
        </div>
      </div>
    </div>
  </div>
  </div>

  </div> <!-- MODAL CAMBIAR ESTADO FIN -->

  <div id="ModalCantidad" class="modal" tabindex="-1">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">Modificar Cantidad Orden Nº <span id="num_orden3"></span></h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
          <div class='form-group'>
            <div class="row">
              <div class="col">
                <label for="cantidad_bandejas_reales" class="control-label">Bandejas Reales:</label>
                <input type="number" style="font-size: 1.5em" min="0" step="1" id="cantidad_bandejas_reales"
                  class="form-control" onClick="this.select();" value="0">
              </div>
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" onClick="guardarCambioCantidad();" class="btn btn-primary">GUARDAR</button>
          <span id="id_ordenhide" style="display:none"></span>
        </div>
      </div>
    </div>
  </div>


  <?php include './class_lib/footer.php';?>
  </div><!-- /.content-wrapper -->

  <!-- Main Footer -->



  </div>

  </div>
  <!-- Add the sidebar's background. This div must be placed
           immediately after the control sidebar -->
  <div class="control-sidebar-bg"></div>
  </div><!-- ./wrapper -->
  </div>

  <script>
    const id_usuario = "<?php echo $_SESSION['id_usuario'] ?>";
    const permisos = "<?php echo $_SESSION['permisos'] ?>";
    func_check(id_usuario, permisos.split(","));
  </script>


</body>

</html>