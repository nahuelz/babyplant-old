<?php include "./class_lib/sesionSecurity.php";?>
<!DOCTYPE html>
<html>

<head>
  <title>Bandejas en Stock</title>
  <?php include "./class_lib/links.php";?>
  <?php include "./class_lib/scripts.php";?>
  <script src="dist/js/ver_clientes.js"></script>

  <script src="dist/js/ver_stock.js?v=<?php echo $version ?>"></script>
  <script src="dist/js/cargar_pedido.js"></script>
  <script src="dist/js/check_permisos.js?v=<?php echo $version ?>"></script>
</head>

<body>
  <div id="miVentana"></div>
  <div id="ocultar">
    <div class="wrapper">
      <header class="main-header">
        <?php
include 'class_lib/nav_header.php';
?>
      </header>
      <aside class="main-sidebar">
        <?php include 'class_lib/sidebar.php';?>
      </aside>

      <!-- Content Wrapper. Contains page content -->
      <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <section class="content-header">

          <div class="tab">
            <button class="tablinks" onclick="abrirTab(event, 'stock');" id="defaultOpen">BANDEJAS EN STOCK</button>
            <button class="tablinks" onclick="abrirTab(event, 'reservas');"><span class="label-reservas">BANDEJAS
                RESERVADAS <span class="label-cant label-reservas-cant"></span></span></button>
            <button id="btn-tab-cuaderno" class="tablinks" onclick="abrirTab(event, 'cuaderno');"><span class="label-reservas">CUADERNO - AGREGAR<span class="label-cant label-cuaderno-cant"></span></span></button>
            <button id="btn-tab-cuaderno-lista" class="tablinks" onclick="abrirTab(event, 'cuaderno-lista');">CUADERNO - LISTA</button>
          </div>



        </section>

        <!-- Main content -->
        <section class="content" style="min-height: 600px;">
          <div class="tab-stock d-none">
            <!-- Your Page Content Here -->
            <div class="row">
              <div class="col-md-2">
                <button class="btn btn-primary btn-block" onclick="modalReservar();"><i class="fa fa-plus-square"></i>
                  RESERVAR</button>
              </div>
              <div class="col-md-2">
                <button class="btn btn-success btn-block" onclick="modalEntregar();"><i class="fa fa-truck"></i>
                  ENTREGA RÁPIDA</button>
              </div>
              
              <div class="col-md-2">
                <button class="btn btn-info btn-block" onclick="modalIngresoManual();"><i
                    class="fa fa-plus-square"></i>
                  INGRESO MANUAL</button>
              </div>
              <div class="col-md-2">
                <button class="btn btn-warning btn-block" onclick="ModalDevoluciones();"><i
                    class="fa fa-plus-square"></i>
                  DEVOLUCIÓN</button>
              </div>
              <div class="col-md-2">
                <button class='btn btn-danger btn-block' onClick='eliminar_stock()'><i class="fa fa-trash"></i>
                  ELIMINAR</button>
              </div>

              <div class='col-md-2'>
                <button type="button" class="btn btn-primary btn-block" id="btn_printcliente"
                  onClick="print_Busqueda(1);"><i class="fa fa-print"></i> IMPRIMIR</button>
              </div>
            </div>

            <div class="row mt-3">
              <div class="col-md-4">
                <div class="d-flex flex-row">
                  <select id='select_tipo_filtro' title='Filtrar por Tipo' class='selectpicker' data-style='btn-info'
                    data-live-search='true'></select>
                  <button class="btn btn-secondary fa fa-close d-inline-block ml-2 pt-2 pb-2"
                    onclick="quitar_filtro()"></button>
                </div>
              </div>
              <div class="col-md-2">
                <button onclick="modalAgregarCliente();" class="btn btn-dark btn-block"><i
                    class="fa fa-plus-square"></i> NUEVO CLIENTE</button>
              </div>
            </div>

            <div class="row mt-3">
              <div class='col'>
                <div class='tabla_entradas'></div>
              </div>
            </div>
          </div>
          <div class="tab-reservas d-none">
            <div class="row">
              <div class='col' style="margin-top: 20px;">
                <div class='tabla_entradas'></div>
              </div>
            </div>
          </div>

          <div class="tab-cuaderno d-none">
            <div class='row'>
              <div class="col-md-4">
                <select id="select-cliente" class="selectpicker" title="Selecciona un Cliente" data-style="btn-info"
                  data-dropup-auto="false" data-size="8" data-live-search="true" data-width="100%"
                  data-dropup-auto="false" data-size="10"></select>
              </div>
              <div class="col-md-3 text-right">
                <button class="btn btn-success" style="height:2.4em;" onclick="modalAgregarProducto();"><i
                    class="fa fa-plus-square"></i> AGREGAR PRODUCTO</button>
              </div>
            </div> <!-- FIN ROW -->

            <div class="row" style="margin-top:10px;">
              <div class="col-md-4">
                <textarea rows="2" class="form-control" name="textarea" id="observaciones_txt"
                  placeholder="OBSERVACIONES" style="width:100%;text-transform: uppercase; resize:none"></textarea>
              </div>
              <div class="col-md-4">

              </div>
             
              <div class="col-md-4 text-right pt-3">
                <button onClick="location.reload();" class="btn btn-danger fa fa-close"
                  style="font-size: 1.7em;"></button>
                <button id="btn_guardarpedido" class="btn btn-primary" style="font-size: 1.4em;margin-left: 1em"
                  onclick="GuardarPedido();"><i class="fa fa-save"></i> GUARDAR</button>
              </div>
            </div><!-- FIN ROW -->

            <div class="row mt-4">
              <div class="col">
                <table id="tabla_detail" class="table table-bordered table-light">
                  <thead class="thead-dark">
                    <tr>
                      <th scope="col">Producto</th>
                      <th scope="col">Plantas</th>
                      <th scope="col">Semillas</th>
                      <th scope="col">Bandejas</th>
                      <th scope="col">Siembra Estimada</th>
                      <th scope="col">Fecha Entrega</th>
                      <th scope="col"></th>
                    </tr>
                  </thead>
                  <tbody>
                    <tr class="pedido-vacio-msg">
                      <th scope="row" colspan="7" class="text-center"><span class="text-muted">El Pedido está
                          vacío</span>
                      </th>
                    </tr>
                  </tbody>
                </table>
              </div>
            </div>
          </div>
          
          <div class="tab-cuaderno-lista">
            <div class="row mb-5">
              <div class='col'>
                <div id='tabla-cuaderno-lista'></div>
              </div>
            </div>
          </div>
      </div>
      </section><!-- /.content -->
    </div><!-- /.content-wrapper -->

    <div id="modal-ingreso-manual" class="modal">
      <div class="modal-content" style="height: 98%">
        <div class='box box-primary'>
          <div class='box-header with-border'>
            <h3 class='box-title'>Agregar Producto a Stock</h3>
          </div>
        </div>

        <div class='form-group'>
          <div class="row">
            <div class="col-md-6">
              <label for="select-tipo" class="control-label">Tipo de Producto:</label>
              <select id="select-tipo" title="Selecciona Tipo" class="selectpicker" data-style="btn-info"
                data-live-search="true" data-width="100%"></select>
            </div>
            <div class="col-md-6">
              <label for="select-subtipo" class="control-label">Subtipo:</label>
              <select id="select-subtipo" title="Subtipo" class="selectpicker" data-style="btn-info"
                data-width="100%"></select>
            </div>
          </div>
        </div>
        <div class='form-group'>
          <div class="row">
            <div class="col-md-8">
              <label for="select-variedad" class="control-label">Variedad:</label>
              <select id="select-variedad" title="Selecciona Variedad" class="selectpicker" data-style="btn-info"
                data-live-search="true" data-width="100%"></select>
            </div>
            <div class="col-md-4">
              <label for="select-bandeja" class="control-label">Bandeja:</label>
              <select id="select-bandeja" title="Bandeja" class="selectpicker" data-style="btn-info"
                data-width="100%"></select>
            </div>
          </div>
        </div>
        <div class="form-group">
          <div class="row">
            <div class="col-md-6">
              <label class="label-control">Cant. Bandejas:</label>
              <input style="font-size: 1.7em" type="number" min="0" step="1" id="input-cant-bandejas"
                class="form-control text-right" onkeyup="getMesadas(this.value)" onpaste="getMesadas(this.value)"
                oninput="getMesadas(this.value)">
            </div>

          </div>
        </div>


        <div class="row">
          <div class="col-md-2">
            <label class="label-control">Mesada:</label>
          </div>

          <div class="col-md-4">

            <select class="selectpicker" data-width="100%" data-style="btn-info" data-container="modal-ingreso-manual"
              id="select_mesada" title="Mesada"></select>

          </div>

        </div>


        <div class="row">
          <div class="col">
            <div class="d-flex flex-row justify-content-end">
              <button type="button" class="btn btn-modal-bottom fa fa-close"
                onClick="$('#modal-ingreso-manual').modal('hide')"></button>
              <button type="button" class="btn btn-modal-bottom ml-2 fa fa-save"
                onClick="guardarIngresoManual();"></button>
            </div>

          </div>

        </div>
      </div> <!-- MODAL FIN -->
    </div>


    <div id="ModalDevoluciones" class="modal">
      <div class="modal-content-verpedido">
        <div class='box box-primary'>
          <div class='box-header with-border'>
            <h4 class='box-title'>Agregar Devolución</h4>
            <button style="float:right;" class="btn btn-modal-top fa fa-close"
              onClick="$('#ModalDevoluciones').modal('hide')"></button>
          </div>
          <div id="tablitabusqueda">
            <div class='box-body'>
              <div class='row'>
                <div class='col-md-1'>
                  <label for="select_cliente2" class="control-label">Cliente:</label>
                </div>
                <div class='col-md-5'>
                  <select id="select_cliente2" class="selectpicker" title="Selecciona un Cliente" data-style="btn-info"
                    data-live-search="true" data-width="100%"></select>
                </div>
                <div class='col-md-2'>
                  <input style="font-size: 1.7em" type="number" min="0" step="1" id="cantidad_devolucion"
                    class="form-control text-right" placeholder="Bandejas" onkeyup="getMesadasDevolver(this.value)"
                    onpaste="getMesadasDevolver(this.value)" oninput="getMesadasDevolver(this.value)">
                </div>
                <div class="col-md-2">
                  <select class="selectpicker" data-width="100%" data-style="btn-info"
                    data-container="modalAgregarProducto1" id="select_mesada2" title="Mesada"></select>
                </div>
                <div class='col-md-2'>
                  <button class="btn btn-primary" onClick="guardarDevolucion();"><i class="fa fa-plus-square"></i>
                    AGREGAR EN STOCK</button>
                </div>
              </div>
              <div class="row" style="margin-top: 10px;">
                <table id="tabla_busqueda" class="table table-bordered table-responsive w-100 d-block d-md-table"
                  role="grid">
                  <thead>
                    <tr role="row">
                      <th class="text-center">Orden Siembra</th>
                      <th class="text-center">Producto</th>
                      <th class="text-center">Bandejas Entregadas</th>
                      <th class="text-center">Fecha Entrega</th>
                      <th class="text-center">Estado</th>
                      <th class="text-center">Pedido Nº</th>
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

    </div>
    <!--FIN MODAL DEVOLUCIONES -->

    <div id="modal-reservar" class="modal">
      <div class="modal-content-verpedido">
        <div class='box box-primary'>
          <div class='box-header with-border'>
            <h4 class='box-title'>Reservar Bandejas</h4>
            <button style="float:right;" class="btn btn-modal-top fa fa-close"
              onClick="$('#modal-reservar').modal('hide')"></button>
          </div>
          <div id="tablitabusqueda">
            <div class='box-body'>
              <div class='row'>
                <div class='col-md-1'>
                  <label for="select_cliente_reservar" class="control-label">Cliente:</label>
                </div>
                <div class='col-md-5'>
                  <select id="select_cliente_reservar" data-dropup-auto="false" data-size="10" class="selectpicker"
                    title="Selecciona un Cliente" data-style="btn-info" data-live-search="true"
                    data-width="100%"></select>
                </div>

                <div class='col-md-3 text-center'>
                  <button class="btn btn-primary" onClick="guardarReserva();"><i class="fa fa-plus-square"></i> GUARDAR RESERVA</button>
                </div>
              </div>
              <div class="row" style="margin-top: 10px;">
                <table id="tabla_reservar" class="table table-bordered table-responsive w-100 d-block d-md-table"
                  role="grid">
                  <thead>
                    <tr role="row">
                      <th class="text-center">Producto</th>
                      <th class="text-center">Cant. en Stock</th>
                      <th class="text-center">Cant. Reserva</th>
                      <th class="text-center">Origen</th>
                      <th class="text-center">Mesada</th>

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

    </div>
    <!--FIN MODAL RESERVAR-->

    <div id="modal-entregar" class="modal">
      <div class="modal-content-verpedido">
        <div class='box box-primary'>
          <div class='box-header with-border'>
            <h4 class='box-title'>Entrega Rápida</h4>
            <button style="float:right;" class="btn btn-modal-top fa fa-close"
              onClick="$('#modal-entregar').modal('hide')"></button>
          </div>
          <div id="tablitabusqueda">
            <div class='box-body'>
              <div class='row'>
                <div class='col-md-1'>
                  <label for="select_cliente_entregar" class="control-label">Cliente:</label>
                </div>
                <div class='col-md-5'>
                  <select id="select_cliente_entregar" data-dropup-auto="false" data-size="10" class="selectpicker"
                    title="Selecciona un Cliente" data-style="btn-info" data-live-search="true"
                    data-width="100%"></select>
                </div>

                <div class='col-md-3 text-center'>
                  <button class="btn btn-info" onClick="guardarEntregaInmediata();"><i class="fa fa-truck"></i> ENTREGAR</button>
                </div>
              </div>
              <div class="row" style="margin-top: 10px;">
                <table id="tabla_entregar" class="table table-bordered table-responsive w-100 d-block d-md-table"
                  role="grid">
                  <thead>
                    <tr role="row">
                      <th class="text-center">Producto</th>
                      <th class="text-center">Cant. en Stock</th>
                      <th class="text-center">Cant. Entrega</th>
                      <th class="text-center">Precio U.</th>
                      <th class="text-center">Origen</th>
                      <th class="text-center">Mesada</th>

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
    </div>
    <!--FIN MODAL ENTREGAR-->



    <div id="ModalAdminPedido" class="modal">
      <div class="modal-content-adminpedido">
        <div class="row mt-3">
          <div class="col text-center">
            <h3>¡El pedido fue guardado correctamente!</h3>
            <h4>¿Qué deseas hacer ahora?</h4>
          </div>
        </div>
        <div class="row">
          <div class="col text-center">
            <div class="py-2 mt-3">
              <button type="button" class="btn btn-primary mt-2" id="btn_printinterno"
                onClick="print_Pedido(1);"><i class="fa fa-print"></i> IMPRIMIR INTERNO</button>
              <button type="button" class="btn btn-primary ml-2 mr-2 mt-2"
                onClick="print_Cliente(1);"><i class="fa fa-print"></i> IMPRIMIR CLIENTE</button>
              <button type="button" class="btn btn-success mt-2"
                onClick="ClearPedido();"><i class="fa fa-check"></i> NUEVO PEDIDO</button>
            </div>
          </div>
        </div>
      </div>
    </div>



    <div id="modal-entregar-reserva" class="modal" data-backdrop="static" data-keyboard="false">
      <div class="modal-content-verpedido">
        <div class='box box-primary'>
          <div class='box-header with-border'>
            <h4 class='box-title'>Entregar Reserva</h4>
            <button style="float:right;" class="btn fa fa-close btn-modal-top"
              onClick="$('#modal-entregar-reserva').modal('hide');"></button>
            <button style='float:right;' class="btn btn-modal-top btn-success ml-5 mr-5"
              onclick="guardarEntregaReserva();"><i class="fa fa-save"></i> GUARDAR</button>
          </div>

          <div class='box-body'>
            <div class="row">
              <div class="col text-center">
                <h5 class="label-cliente-reserva"></h5>
                <h5 class="label-telefono-reserva"></h5>
              </div>
            </div>
            <table class="table table-responsive w-100 d-block d-md-table tabla-entrega" role="grid">
              <thead>
                <tr role="row">
                  <th class="text-center">Producto</th>
                  <th class="text-center">Bandejas</th>
                  <th class="text-center">Mesada Nº</th>
                  <th class="text-center">Origen</th>
                  <th class="text-center">Precio ($)</th>
                </tr>
              </thead>
              <tbody>
              </tbody>
            </table>
            <div class="modal-footer d-block">
              <div class="row">
                <div class="col pt-3">
                  <div class="d-flex flex-row">
                    <h6>APLICAR DESCUENTO:</h6>
                    <select class="form-control ml-2 select-descuento" style="max-width: 150px;">
                      <option value="porcentual" selected>Porcentual</option>
                      <option value="fijo">Suma Fija</option>
                    </select>
                    <input type="text" style="font-size: 1.0em; max-width: 150px" onkeyup="calcularSubtotal(this)"
                      onpaste="calcularSubtotal(this);"
                      class="form-control ml-2 input-descuento font-weight-bold text-center">
                  </div>

                </div>
                <div class="col">
                  <div class="d-flex flex-row pull-right">
                    <div class="pt-3">
                      <h4>SUBTOTAL:</h4>
                    </div>
                    <input type="text" style="font-size: 1.6em; max-width: 150px"
                      class="form-control ml-2 input-subtotal font-weight-bold text-center">
                  </div>
                </div>
              </div>
            </div>

          </div>


        </div>
      </div>

    </div>
    <!--FIN MODAL ENTREGA INMEDIATA -->


    <div id="modal-cambiar-mesada" class="modal">
      <div class="modal-cambiar-mesada">
        <div class='box box-primary'>
          <div class='box-header with-border'>
            <h4 class='box-title'>Cambiar Mesada</h4>
            <button style="float:right;" class="btn btn-modal-top fa fa-close"
              onClick="$('#modal-cambiar-mesada').modal('hide')"></button>
          </div>
          <div class="box-body">
            <div class="row">
              <div class="col form-group">
                <label class="label-control">Mesada Nueva:</label>
                <select class="selectpicker" data-width="100%" data-style="btn-info"
                  data-container="modal-cambiar-mesada" data-size="6" id="select_mesada_cambiar"
                  title="Mesada"></select>
              </div>
            </div>
            <div class="row mt-3">
              <div class="col">
                <button onclick="guardarCambioMesada()" class="btn btn-success pull-right"><i class="fa fa-save"></i>
                  GUARDAR</button>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
    <!--FIN MODAL RESERVAR-->

    <?php include "modals/agregar_producto_pedido.php";?>

    <?php include "modals/agregar_cliente.php";?>


    <!-- Main Footer -->
    <?php
include 'class_lib/footer.php';
?>


    <!-- Add the sidebar's background. This div must be placed
           immediately after the control sidebar -->
    <div class="control-sidebar-bg"></div>
  </div><!-- ./wrapper -->

  </div> <!-- ID OCULTAR-->


  <!-- REQUIRED JS SCRIPTS -->



  <script src="plugins/moment/moment.min.js"></script>
  <script src="plugins/daterangepicker/daterangepicker.js"></script>

  <script type="text/javascript">
    $(document).ready(function () {
      $('.selectpicker').selectpicker();
    });

    let id_usuario = "<?php echo $_SESSION['id_usuario'] ?>";
    let permisos = "<?php echo $_SESSION['permisos'] ?>";
    func_check(id_usuario, permisos.split(","));



  </script>

  <style>
    tr.selected td {
      background-color: #333;
      color: #fff;
    }

    tr.selected2 td {
      background-color: #333;
      color: #fff;
    }

    tr.seldevol td {
      background-color: #333;
      color: #fff;
    }
  </style>


</body>

</html>