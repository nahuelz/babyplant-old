<?php include "./class_lib/sesionSecurity.php"; ?>
<!DOCTYPE html>
<html>

<head>
  <title>Mesadas</title>
  <?php include "./class_lib/links.php"; ?>
  <?php include "./class_lib/scripts.php"; ?>
  <script src="dist/js/check_permisos.js?v=<?php echo $version ?>"></script>
  <script src="dist/js/ver_mesadas.js?v=<?php echo $version ?>"></script>
</head>

<body>
  <div id="miVentana">
  </div>
  <div id="ocultar">
    <div class="wrapper">
      <header class="main-header">
        <?php
        include('class_lib/nav_header.php');
        ?>
      </header>
      <aside class="main-sidebar">
        <?php
        include('class_lib/sidebar.php');
        ?>
      </aside>
      <div class="content-wrapper">
        <section class="content-header">
          <h1>
            Mesadas
          </h1>
          <ol class="breadcrumb">
            <li><a href="inicio.php"> Inicio</a></li>
            <li class="active">Mesadas</li>
          </ol>
        </section>
        <section class="content">
          <div class='box-body'>
            <div align='right'><button style="font-size: 1.5em" class="btn btn-success btn-round fa fa-plus-square"
                onclick="CrearMesada();"></button></div>
            <div id="contenedor" class="w-100 mt-3 mb-5">
              <div class="row">
                <div class="col text-center">
                  <div class="row row-contenedor"></div>
                </div>
              </div>
            </div>
          </div>
      </div>
      </section><!-- /.content -->


      <div id="ModalMesadas" class="modal">
        <div class="modal-content3">
          <div class='box box-primary'>
            <div class='box-header with-border'>
              <h3 class='box-title'><span id="titulo_modal">Agregar Mesada</span></h3>
            </div>
            <div class='box-body'>
              <div class='form-group'>
                <div class="row">
                  <div class="col-md-6">
                    <label for="capacidad_txt" class="control-label">Capacidad:</label>
                    <input type="number" min="0" step="1" id="capacidad_txt" class="form-control" value="500">
                  </div>
                  <div class="col-md-6">
                    <label for="select_producto" class="control-label">Tipo de Mesada:</label>
                    <select id="select_producto" title="Selecciona Tipo" class="selectpicker" data-style="btn-info"
                      data-live-search="true" data-width="100%" name="SelectAddress"></select>
                  </div>
                </div>
              </div>
              <div class="row">
                <div class="col">
                  <div class="d-flex flex-row justify-content-end">
                    <button type="button" class="btn fa fa-close btn-modal-bottom"
                      onClick="CerrarModalMesadas();"></button>
                    <button type="button" class="btn fa fa-save btn-modal-bottom" 
                      onClick="GuardarMesada();"></button>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div> <!-- MODAL MESADAS FIN -->

      <div id="ModalVerMesada" class="modal">
        <div id="ModalVerMesadaContent" class="modal-content-verpedido">
          <div class='box box-primary'>
            <div class='box-header with-border'>
              <h4 class='box-title'>Mesada Nº <b id='num_mesadaview'></b></h4>
              <button class="btn fa fa-close pull-right btn-modal-top" onClick="CerrarModalVerMesada()"></button>
            </div>
            <div id="tablita">
              <div class='box-body'>
                <table id="tabla_contenidomesada" class="table table-responsive w-100 d-block d-md-table"
                  role="grid">
                  <thead>
                    <tr role="row">
                      <th class="text-center">Orden</th>
                      <th class="text-center">Producto</th>
                      <th class="text-center">Cantidad<br>Bandejas</th>
                      <th class="text-center">Faltan<br>Entregar</th>
                      <th class="text-center">Cliente</th>
                      <th class="text-center">Fecha Siembra</th>
                      <th class="text-center">Fecha Ingreso Mesada</th>
                      <th class="text-center">Entrega Solicitada</th>
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
      </div> <!-- FIN MODAL VER -->

      <?php include("modals/ver_detalle_pedido.php"); ?>

      <?php include("modals/reasignar_mesadas.php"); ?>

            <div id="ModalModificarCantidad" class="modal">
              <div class="modal-content3">
                <div class='box box-primary'>
                  <div class='box-header with-border'>
                    <h3 class='box-title'>Modificar Cantidad en Mesada <span id="id_orden_mesada"
                        style="display:none"></span></h3>
                  </div>
                  <div id="bodymodal" class='box-body'>
                    <div class='form-group'>
                      <div class="row">
                        <div class="col-md-6">
                          <label for="cantidad_bandejas_nueva" class="control-label">Cantidad Bandejas:</label>
                          <input style="font-size: 2em;font-weight: bold;" type="number" min="0" step="1"
                            id="cantidad_bandejas_nueva" class="form-control" value="0">
                        </div>
                      </div>
                    </div>
                    <div class="row" style="margin-top: 80px;">
                    </div>
                  </div>
                  <div class="row">
                    <div class="col">
                      <div class="d-flex flex-row justify-content-end">
                        <button type="button" style="font-size: 2.3em" class="btn fa fa-close btn-modal-bottom"
                          id="btn_cancel" onClick="$('#ModalModificarCantidad').modal('hide');"></button>
                        <button type="button" style="font-size: 2.3em" class="btn fa fa-save btn-modal-bottom"
                          id="btn_guardarentrega" onClick="GuardarNuevaCantidad();"></button>
                      </div>
                    </div>
                  </div>
                  <div style="display:none" id="id_artpedido1"></div>
                </div>
              </div>
              <!--FIN MODAL-->

    </div><!-- /.content-wrapper -->
            <!-- Main Footer -->
            <?php
      include('./class_lib/footer.php');
      ?>
          </div>
        </div>
        <div class="control-sidebar-bg"></div>



  <script type="text/javascript">
    const id_usuario = "<?php echo $_SESSION['id_usuario'] ?>";
    const permisos = "<?php echo $_SESSION['permisos'] ?>";
    func_check(id_usuario, permisos.split(","));
  </script>
</body>

</html>