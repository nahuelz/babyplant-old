<?php include "./class_lib/sesionSecurity.php";?>
<!DOCTYPE html>
<html>
<head>
  <title>Agregar Pedido</title>
  <?php include "./class_lib/links.php";?>
  <?php include "./class_lib/scripts.php";?>
  <script src="dist/js/check_permisos.js"></script>
  <script src="dist/js/ver_clientes.js"></script>
  <script src="dist/js/cargar_pedido.js"></script>
</head>

<body>
  <div id="miVentana">
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
        <div class="control-sidebar-bg"></div>
      </aside>
      <div class="content-wrapper">
        <section class="content-header">
          <h1>Agregar Pedido <small>
              <?php echo $fecha; ?>
            </small></h1>
          <ol class="breadcrumb">
            <li><a href="inicio.php"> Inicio</a></li>
            <li class="active">Agregar Pedido</li>
          </ol>
        </section>
        <!-- Main content -->
        <section class="content">
          <div class='row'>
            <div class="col-md-4">
              <select id="select_cliente" class="selectpicker" title="Selecciona un Cliente"
                data-style="btn-info" data-dropup-auto="false" data-size="8" data-live-search="true" data-width="100%"
                data-dropup-auto="false" data-size="10"></select>
            </div>
            <div class="col-md-2">
              <button style="height: 2.4em" class="btn btn-info" onclick="modalAgregarCliente();"><i
                  class="fa fa-plus-square"></i> NUEVO CLIENTE</button>
            </div>
            <div class="col-md-3 text-right">
              <button class="btn btn-success" style="height:2.4em;" onclick="modalAgregarProducto();"><i
                  class="fa fa-plus-square"></i> AGREGAR PRODUCTO</button>
            </div>
          </div> <!-- FIN ROW -->

          <div class="row" style="margin-top:10px;">
            <div class="col-md-4">
              <textarea rows="2" class="form-control" name="textarea" id="observaciones_txt" placeholder="OBSERVACIONES"
                style="width:100%;text-transform: uppercase; resize:none"></textarea>
            </div>
            <div class="col-md-4">
              <div class="row">
                <div class="col">
                  <h4>PAGÓ: $<span id="monto_pago">0.00</span></h4>
                </div>
              </div>
              <div class="row">
                <div class="col">
                  <button class="btn btn-success" onclick="AgregarPagoModal();"><i class="fa fa-dollar"></i> AGREGAR
                    PAGO</button>
                </div>
              </div>
            </div>
            <div class="col-md-4 text-right pt-3">
              <button onClick="window.location.href = 'cargar_pedido.php';" class="btn btn-danger fa fa-close"
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
                    <th scope="row" colspan="7" class="text-center"><span class="text-muted">El Pedido está vacío</span>
                    </th>
                  </tr>
                </tbody>
              </table>
            </div>
          </div>
          <!--FIN ROW-->
        </section>
      </div>
      <!--FIN CONTENT-WRAPPER-->
    </div>
    <!--FIN WRAPPER-->

    <div id="ModalPagos" class="modal">
      <div class="modalpago-content">
        <div class='box box-primary'>
          <div class='box-header with-border'>
            <div class="col-md-10">
              <h3 class='box-title'>Agregar Pago</h3>
            </div>
            <div class="col text-right">
              <button type="button" class="btn fa fa-close btn-modal-top" id="btn_cancel"
                onClick="$('#ModalPagos').modal('hide');"></button>
            </div>
          </div>
          <div class='box-body'>
            <div class='form-group'>
              <div class='row'>
                <div class='col'>
                  <label for="input_pago" class="control-label">Monto ($):</label>
                  <input type="text" style="font-size: 1.5em" class="form-control numeric-only" id="input_pago" value=""
                    maxlength="10">
                </div>
              </div>
            </div>
            <div class='form-group'>
              <div class='row'>
                <div class='col'>
                  <label for="input_concepto" class="control-label">Concepto:</label>
                  <input class="form-control" type="text" id="input_concepto" value="" maxlength="50"
                    style="text-transform: uppercase;">
                </div>
              </div>
            </div>
            <div class="row">
              <div class="col text-right">
                <button class="btn btn-success mt-3 font-weight-bold" onClick="agregarPago()">AGREGAR</button>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div> <!-- MODAL PAGOS FIN -->

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

    <?php include "modals/agregar_cliente.php";?>
    <?php include "modals/agregar_producto_pedido.php";?>
    <?php include './class_lib/footer.php';?>

    <script type="text/javascript">
      let id_usuario = "<?php echo $_SESSION['id_usuario'] ?>";
      let permisos = "<?php echo $_SESSION['permisos'] ?>";
      func_check(id_usuario, permisos.split(","));
    </script>
</body>
</html>