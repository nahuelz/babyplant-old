<?php include "./class_lib/sesionSecurity.php";?>
<!DOCTYPE html>
<html>

<head>
  <title>Planificación de Entregas</title>
  <?php include "./class_lib/links.php";?>
  <?php include "./class_lib/scripts.php";?>
  <script src="plugins/moment/moment.min.js"></script>
  <script src="dist/js/check_permisos.js?v=<?php echo $version ?>"></script>
  <script src="dist/js/ver_planentregas.js?v=<?php echo $version ?>"></script>
  <script src="dist/js/ver_pedidos.js?v=<?php echo $version ?>"></script>
  <style>
    #tablitaa thead,
    #tablitaa tr,
    #tablitaa tbody,
    #tablitaa th,
    #tablitaa td {
      text-align: center;
      table-layout: fixed;
      height: 70px;
      width: 16.6%;
    }

    #tabla_busqueda tr.selected2 td {
      background-color: #333;
      color: #fff;
    }
  </style>
</head>

<body>
  <div id="mi-ventana"
    style="height: 100%;padding:5px; width: 99%; font-family:Verdana, Arial, Helvetica, sans-serif; font-size: 12px; font-weight: normal; border: #333333 3px solid; background-color: #FAFAFA; color: #000000; display:none;">
  </div>
  <div id="ventana-remito"
    style="padding:5px; width: 99%; font-family:Verdana, Arial, Helvetica, sans-serif; font-size: 12px; font-weight: normal; border: #333333 3px solid; background-color: #FAFAFA; color: #000000; display:none;">
  </div>
  <div id="ocultar">
    <div class="wrapper">
      <header class="main-header">
        <?php include 'class_lib/nav_header.php';?>
      </header>
      <aside class="main-sidebar">
        <?php
include 'class_lib/sidebar.php';
$dias = array("Domingo", "Lunes", "Martes", "Miercoles", "Jueves", "Viernes", "Sábado");
$meses = array("Enero", "Febrero", "Marzo", "Abril", "Mayo", "Junio", "Julio", "Agosto", "Septiembre", "Octubre", "Noviembre", "Diciembre");
$fecha = $dias[date('w')] . " " . date('d') . " de " . $meses[date('n') - 1] . " del " . date('Y');
?>
      </aside>

      <div class="content-wrapper">
        <section class="content-header">
          <h1>Planificación de Entregas</h1>
          <ol class="breadcrumb">
            <li><a href="inicio.php"> Inicio</a></li>
            <li class="active">Planificación de Entregas</li>
          </ol>
        </section>
        <!-- Main content -->
        <section class="content">
          <div id="TABLA">
            <div class="row">
              <div class="col-md-5">
                <div class="container-semanas">
                  <div class="row">
                    <div class="col-md-4">
                      <button class="btn btn-dark btn-block" style="font-weight: bold;" onClick="changeWeek(-1);">Semana
                        Anterior</button>
                    </div>
                    <div class="col-md-4">
                      <button class="btn btn-dark btn-block" style="font-weight:bold;"
                        onClick="changeWeek(0);">HOY</button>
                    </div>
                    <div class="col-md-4">
                      <button class="btn btn-dark btn-block mt-lg-0 mt-2" style="font-weight:bold;"
                        onClick="changeWeek(1);">Semana Siguiente</button>
                    </div>
                  </div>
                  <div class="row mt-2">
                    <div class="col-md-6">
                      <button class="btn btn-dark btn-block" id="btn_toPrevWeek" style="font-weight: bold"
                        onClick="sendToWeek(0);">Enviar a la Semana anterior</button>
                    </div>
                    <div class="col-md-6">
                      <button class="btn btn-dark btn-block mt-lg-0 mt-2" id="btn_toNextWeek" style="font-weight: bold"
                        onClick="sendToWeek(1);">Enviar a la Semana siguiente</button>
                    </div>
                  </div>
                </div>
              </div>

              <div class="col-md-7">
                <div class="container-btn-derecha">
                  <div class="row">
                    <div class="col-md-4">
                      <button disabled="true" class="btn btn-primary btn-block mt-lg-0 mt-2 mb-lg-0 mb-2"
                        style="font-weight:bold;" onClick="MostrarModalOrden();">Agregar a Agenda de Entregas</button>
                    </div>
                    <div class="col-md-4">
                      <button class="btn btn-success btn-block" style="font-weight:bold;"
                        onClick="ModalEntregaInmediata();">Entrega Inmediata</button>
                    </div>
                    <div class="col-md-4">
                      <button class="btn btn-dark btn-block mt-lg-0 mt-2" id="guardar_btn" onClick="GuardarCambios();"
                        style="font-weight:bold;">Guardar Posiciones</button>
                    </div>
                  </div>
                  <div class="row mt-2">
                    <div class="col-md-6">
                      <button class="btn btn-primary btn-block" onClick="buscar_parciales();"><i class="fa fa-search">
                          <span style="font-weight: bold;font-family: Arial">BUSCAR
                            PEDIDO</span></i></button>
                    </div>
                    <div class="col-md-6">
                      <button class="btn btn-secondary btn-block mt-lg-0 mt-2" onClick="DeseleccionarTodo();"
                        style="font-weight: bold">Deseleccionar Todo</button>
                    </div>
                  </div>
                </div>
              </div>



            </div>


            <div class="row">
              <div class="col text-center">
                <h4 id="num_semana" class="mt-3 mb-3" style="font-weight:bold">Semana Nº </h4>
              </div>
            </div>
            <table class="tabledias table-bordered table-responsive w-100 d-block d-md-table" id="tablitaa">
              <thead>
                <tr id="header">
                </tr>
              </thead>
              <tbody>
              </tbody>
            </table>
          </div>
      </div>
      </section><!-- /.content -->

      <div id="ModalCambiarCliente" class="modal" data-backdrop="static" data-keyboard="false">
        <div class="modal-content3">
          <div class='box box-primary'>
            <div class='box-header with-border'>
              <h3 class='box-title'>Entregar a otro Cliente</h3>
            </div>
            <div class='box-body'>
              <h5 class="text-danger">Atención! se generará un pedido nuevo con la cantidad que vas a entregar.</h5>
              <div class="form-row">
                <div class='form-group col-md-8'>
                  <label class="control-label">Cliente:</label>
                  <select id="select_cambiarcliente" class="selectpicker mobile-device ml-3"
                    title="Selecciona un Cliente" data-style="btn-info" data-live-search="true" data-width="100%"
                    data-dropup-auto="false" data-size="10"></select>
                </div>
              </div>
            </div>
          </div>
          <div class="row">
            <div class="col text-right">
              <button type="button" class="btn fa fa-close btn-modal-bottom"
                onClick="cerrarModalCambiarCliente();"></button>
              <button type="button" class="btn fa fa-save btn-modal-bottom ml-3"
                onClick="guardarCambiarCliente();"></button>
            </div>
          </div>
        </div>
      </div> <!-- MODAL POPUP FIN -->

      <?php include("modals/entrega_inmediata.php") ?>

      <div id="ModalEntregaExitosa" class="modal" data-backdrop="static" data-keyboard="false">
        <div class="modal-content-adminpedido">
          <div class="container p-4">
            <div class="row">
              <div class="col text-center">
                <h3>¡La entrega fue guardada correctamente!</h3>
              </div>
            </div>
            <div class="row">
              <div class="col text-center">
                <h4>¿Qué deseas hacer ahora?</h4>
              </div>
            </div>
            <div class="row mt-3">
              <div class="col text-center">
                <button type="button" style="font-size: 1.4em" class="btn btn-success btn-lg fa fa-print"
                  onClick="printRemito(1);"> IMPRIMIR REMITO</button>
              </div>
            </div>
            <div class="row mt-5">
              <div class="col text-center">
                <button type="button" class="btn btn-primary btn-lg fa fa-undo"
                  onClick="$('#ModalEntregaExitosa').modal('hide');CerrarModalEntregaInmediata();location.reload();"> IR
                  A PLANIFICACIÓN</button>
              </div>
            </div>
          </div>
        </div>
      </div>


      <div id="ModalVerOrden" class="modal" data-backdrop="static" data-keyboard="false">
        <div id="ModalVerOrden2" class="modal-content-verpedido">
          <div class='box box-primary'>
            <div class='box-header with-border'>
              <h4 class='box-title'>Órden de Entrega</h4>
              <button style="float:right;" class="btn fa fa-close btn-modal-top" onClick="CerrarModalOrden()"></button>
              <button style='float:right;' class="btn btn-success ml-4 mr-4 btn-modal-top" onclick="GuardarOrden();">
                <i class='fa fa-save'></i> GUARDAR</button>
            </div>
            <div class="row mt-2 mb-2">
              <div class="col">
                <div class="d-flex flex-row">
                  <div style="margin-left:12px;padding-top: 8px;">
                    <h4>AGENDAR PARA LA FECHA: </h4>
                  </div>
                  <div class="ml-2">
                    <input type='text' class="form-control" style="font-size: 1.3em" data-date-format='dd/mm/yy'
                      class="datepicker" id="currentfecha_txt" placeholder="DD/MM/AAAA" />
                  </div>
                </div>
              </div>
            </div>


            <div id="tablitaordenentrega">
              <div class='box-body'>
                <table id="tabla_ordenentrega" class="table table-bordered table-responsive w-100 d-block d-md-table"
                  role="grid">
                  <thead>
                    <tr role="row">
                      <th class="text-center">Ord</th>
                      <th class="text-center">Producto</th>
                      <th class="text-center">Faltan Entregar</th>
                      <th class="text-center">Band. Sembradas</th>
                      <th class="text-center">Cliente</th>
                      <th class="text-center">Entrega Solicitada</th>
                      <th class="text-center">Mesadas</th>
                      <th class="text-center">Domicilio</th>
                      <th class="text-center">Cantidad a Entregar</th>
                      <th class="text-center">Teléfono</th>
                      <th class="text-center" style="width: 8%"></th>
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
      <!--FIN MODAL VER ORDEN -->

      <div id="ModalOrdenconBusqueda" class="modal" data-backdrop="static" data-keyboard="false">
        <div class="modal-content3">
          <div class='box box-primary'>
            <div class='box-header with-border'>
              <h3 class='box-title'>Agregar a Agenda de Entregas<span id="num_orden2"></span></h3>
            </div>
            <div class='box-body'>
              <div class="row">
                <div class="col-md-4">
                  <label for="preciou" class="control-label">Fecha a Entregar:</label>
                </div>
                <div class="col-md-8">
                  <input type='text' data-date-format='dd/mm/yy' value='<?php echo date(' d/m/Y'); ?>'
                  class="datepicker" id="fecha_entregareal"/>
                </div>
              </div>
              <div class="row" style="margin-top:10px">
                <div class="col-md-4">
                  <label for="preciou" class="control-label">Modo de Entrega:</label>
                </div>
                <div class="col-md-8">
                  <select id="select_modoentrega5" title="Selecciona Modo" onChange="setDomicilio2()"
                    class="selectpicker mobile-device" data-width="100%">
                    <option value="1">RETIRA EL CLIENTE</option>
                    <option value="2">ENTREGA A DOMICILIO</option>
                  </select>
                </div>
              </div>
              <div id="contenedor_domicilio" class="row" style="display:none;margin-top:10px">
                <div class="col-md-4">
                  <label class="control-label">Domicilio:</label>
                </div>
                <div class="col-md-8">
                  <input type="text" id="domiciliocliente_txt4" style="text-transform:uppercase" class="form-control">
                </div>
              </div>
            </div>
          </div>
          <div class="row">
            <div class="col">
              <div class="d-flex flex-row justify-content-end">
                <button type="button" class="btn fa fa-close btn-modal-bottom" id="btn_cancel2"
                  onClick="CerrarModalOrdenBusqueda();"></button>
                <button type="button" class="btn fa fa-save btn-modal-bottom" id="btn_guardarordenes"
                  onClick="GuardarEnAgenda();"></button>
              </div>
            </div>
            <span style="display: none;" id="id_clienteorden"></span>
          </div>
        </div>
      </div> <!-- MODAL CAMBIAR ESTADO FIN -->

      <div id="ModalCambiarFecha" class="modal" data-backdrop="static" data-keyboard="false">
        <div class="modal-content3">
          <div class='box box-primary'>
            <div class='box-header with-border'>
              <h3 class='box-title'>Modificar Fecha de Entrega <span style="display:none" id="id_artpedidohide2"></span>
              </h3>
            </div>
            <div class='box-body'>
              <div class='form-group'>
                <div class="row">
                  <div class="col-md-8">
                    <label for="preciou" class="control-label">Fecha:</label>
                    <input type='text' data-date-format='dd/mm/yy' value='<?php echo date(' d/m/Y'); ?>'
                    class="datepicker" id="fechaplanificacion_txt" placeholder="DD/MM/AAAA"/>
                  </div>
                </div>
              </div>
            </div>

            <div class="row">
              <div class="col text-right">
                <div>
                  <button type="button" class="btn fa fa-close btn-modal-bottom" id="btn_cancel"
                    onClick="CerrarModalCambioFecha();"></button>
                  <button type="button" class="btn fa fa-save btn-modal-bottom ml-3" id="btn_guardarcliente"
                    onClick="GuardarCambioFecha();"></button>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <div id="ModalModoEntrega" class="modal" data-backdrop="static" data-keyboard="false">
        <div class="modal-content3">
          <div class='box box-primary'>
            <div class='box-header with-border'>
              <h3 class='box-title'>Establecer Datos de Entrega<span style="display:none;"
                  id="id_artpedidoentrega"></span></h3>
            </div>
            <div class='box-body'>
              <div class="row">
                <div class="col-md-2">
                  <label for="cantidad_entregar" class="control-label">Cantidad:</label>
                </div>
                <div class="col-md-4">
                  <input type="number" min="0" step="1" id="cantidad_entregar" class="form-control">
                </div>
              </div>
              <div class="row" style="margin-top:10px">
                <div class="col-md-2">
                  <label for="telefono_receptor" class="control-label">Teléfono:</label>
                </div>
                <div class="col-md-8">
                  <input type="search" autocomplete="off" maxLength="30" id="telefono_receptor" class="form-control" />
                </div>
              </div>
              <div class="row" style="margin-top:10px">
                <div class="col-md-2">
                  <label for="domiciliocliente_txt2" class="control-label">Domicilio:</label>
                </div>
                <div class="col-md-8">
                  <input type="search" autocomplete="off" maxLength="60" id="domiciliocliente_txt2"
                    style="text-transform:uppercase" class="form-control" />
                </div>
              </div>
              <div class="row pull-right mt-5">
                <div class="col">
                  <button type="button" class="btn fa fa-close btn-modal-bottom" id="btn_cancel"
                    onClick="CerrarModalModoEntrega();"></button>
                  <button type="button" class="btn fa fa-save btn-modal-bottom ml-3" id="btn_guardarmodoentrega"
                    onClick="GuardarModoEntrega();"></button>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <?php include "modals/ver_detalle_pedido.php";?>
      <?php include "modals/cambiar_cliente_pedido.php";?>
      <?php include "modals/reasignar_mesadas.php";?>
      <?php include "modals/marcar_problema.php";?>
      <?php include "modals/enviar_revision.php";?>
      <?php include "modals/aplicar_solucion.php";?>
      <?php include "modals/cambio_cantidad_bandejas.php"?>

      <div id="ModalBuscarPedido" class="modal" data-backdrop="static" data-keyboard="false">
        <div class="modal-content-verpedido">
          <div class='box box-primary'>
            <div class='box-header with-border'>
              <h4 class='box-title'>Buscar Pedidos</b></h4>
              <button class="btn fa fa-close pull-right btn-modal-top" onClick="CerrarModalBuscar()"></button>
            </div>
            <div class='box-body'>
              <div class='row'>
                <div class='col-md-5'>
                  <div class="cliente-container">
                    <div class="d-flex flex-row">
                      <div>
                        <label for="select_cliente2" class="control-label">Cliente:</label>
                      </div>
                      <div class="ml-2" style="flex-grow: 1">
                        <select id="select_cliente2" class="selectpicker mobile-device" title="Selecciona un Cliente"
                          data-style="btn-info" data-live-search="true" data-width="100%" data-dropup-auto="false"
                          data-size="10"></select>
                      </div>
                    </div>
                  </div>
                </div>
                <div class='col-md-3 text-center'>
                  <button class="btn btn-primary" onClick="BusquedaEnAgenda();"><i class="fa fa-plus-square"></i>
                    Agregar a Agenda</button>
                </div>
                <div class='col-md-3 text-center'>
                  <button class="btn btn-primary" onClick="EntregaInmediataBusqueda();"><i class="fa fa-truck"></i>
                    Entrega Inmediata</span></button>
                </div>
              </div>
              <div class="row mt-2">
                <table id="tabla_busqueda" class="table table-bordered table-responsive w-100 d-block d-md-table"
                  role="grid">
                  <thead>
                    <tr role="row">
                      <th class="text-center">Orden Siembra</th>
                      <th class="text-center">Producto</th>
                      <th class="text-center">Faltan Entregar</th>
                      <th class="text-center">Band. Sembradas</th>
                      <th class="text-center">Fecha Entrega<br>Estimada</th>
                      <th class="text-center">Mesadas</th>
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
        <!--FIN MODALBUSCAR -->

        <?php include './class_lib/footer.php';?>
      </div>
    </div>

    <div class="control-sidebar-bg"></div>
  </div><!-- ./wrapper -->


  <script>
    $(document).ready(function () {
      const id_usuario = "<?php echo $_SESSION['id_usuario'] ?>";
      const permisos = "<?php echo $_SESSION['permisos'] ?>";
      func_check(id_usuario, permisos.split(","));

      if (/Android|webOS|iPhone|iPad|iPod|BlackBerry/i.test(navigator.userAgent)) {
        $('.selectpicker').selectpicker('mobile');
      }
      else {
        let elements = document.querySelectorAll('.mobile-device');
        for (let i = 0; i < elements.length; i++) {
          elements[i].classList.remove('mobile-device');
        }
        $('.selectpicker').selectpicker({});
      }
      $("#currentfecha_txt").datepicker({

        dateFormat: "dd/mm/yy",
        autoclose: true,
        minDate: 0,
        disableTouchKeyboard: true,
        Readonly: true,
        onSelect: function (dateText, inst) {
          let date = $(this).val();
          let datesplit = date.split("/");
          let fecha = new Date(datesplit[2] + "/" + datesplit[1] + "/" + datesplit[0]);

        }
      }).attr("readonly", "readonly");;

    });
  </script>

</body>

</html>