<?php include "./class_lib/sesionSecurity.php"; ?>
<!DOCTYPE html>
<html>

<head>
  <title>Ver Órdenes Siembra</title>
  <?php include "./class_lib/links.php"; ?>
  <?php include "./class_lib/scripts.php"; ?>
  <link rel="stylesheet" href="plugins/select2/select2.min.css">
  <link rel="stylesheet" href="plugins/daterangepicker/daterangepicker-bs3.css">
  <script src="dist/js/ver_ordenes_siembra.js?v=<?php echo $version ?>"></script>
  <script src="dist/js/ver_pedidos.js?v=<?php echo $version ?>"></script>
  <script src="dist/js/check_permisos.js?v=<?php echo $version ?>"></script>
  <script src="dist/js/ver_mesadas.js?v=<?php echo $version ?>"></script>
  <script src="plugins/moment/moment.min.js"></script>
  <script src="plugins/daterangepicker/daterangepicker.js"></script>
</head>

<body onload="busca_entradas();pone_tipos();$('#select_tipofecha').selectpicker('val', [0]); ">
  <div id="miVentana"></div>

  <div id="ocultar">
    <div class="wrapper">
      <header class="main-header">
        <?php include('class_lib/nav_header.php'); ?>
      </header>
      <!-- Left side column. contains the logo and sidebar -->
      <aside class="main-sidebar">
        <?php include('class_lib/sidebar.php'); ?>
      </aside>
      <div class="content-wrapper">
        <section class="content-header">
          <h1>Ver Órdenes Siembra</h1>

          <ol class="breadcrumb">
            <li><a href="inicio.php"> Inicio</a></li>
            <li class="active">Ver Órdenes Siembra</li>
          </ol>
        </section>

        <!-- Main content -->
        <section class="content">
          <div class='row'>
            <div class='col-md-6'>
              <div class='box box-primary'>
                <div class='box-header with-border'>
                  <h3 class='box-title'>Buscar</h3>
                  <button class='btn btn-primary pull-right' onclick='expande_busqueda()' id='btn-busca'><i
                      class='fa fa-caret-down'></i> Busqueda Avanzada</button>
                </div>
                <div class='box-body'>
                  <div id="contenedor_busqueda" style="display:none">
                  <div class="form-group">
                    <div class='row'>
                      <div class='col-md-2'>
                        <label>Fechas:</label>
                      </div>
                      <div class='col-md-3'>
                        <div class="input-group">
                          <button class="btn btn-default pull-left" id="daterange-btn">
                            <i class="fa fa-calendar"></i> Seleccionar...
                            <i class="fa fa-caret-down"></i>
                          </button>
                        </div>
                      </div>
                      <div class='col-md-3'>
                        <select id="select_tipofecha" class="selectpicker mobile-device" data-dropup-auto="false"
                          title="Tipo Fecha" data-style="btn-info" data-width="100%">
                          <option value="0">Fecha Creación</option>
                          <option value="1">Fecha Siembra</option>
                          <option value="2">Fecha Cámara</option>
                          <option value="3">Fecha Invernáculo</option>
                        </select>
                      </div>
                      <div class='col-md-4'>
                        <button class='btn btn-primary pull-right' onclick='busca_entradas();' id='btn-busca'><i
                            class='fa fa-search'></i> Buscar...</button>
                      </div>
                    </div>

                    <span class='fe'></span>
                    <input type='hidden' class='form-control' id='fi' value=''>
                    <input type="hidden" class='form-control' id='ff' value=''>
                  </div><!-- /.form group -->
                    <div class="form-group">
                      <div class='row'>
                        <div class='col-md-2'>
                          <label>Producto:</label>
                        </div>
                        <div class='col-md-5'>
                          <select id="select_tipo_filtro" class="selectpicker mobile-device" title="Tipo"
                            data-style="btn-info" data-dropup-auto="false" data-size="5" data-width="100%"
                            multiple></select>
                        </div>
                      </div>
                    </div>
                    <div class="form-group">
                      <div class='row'>
                        <div class='col-md-2'>
                          <label>Subtipo:</label>
                        </div>
                        <div class='col-md-5'>
                          <div class="btn-group" style="width:100%">
                            <input id="busca_subtipo" style="text-transform:uppercase;width:100%" type="search"
                              class="form-control">
                            <span id="searchclear" onClick="$('#busca_subtipo').val('');"
                              class="glyphicon glyphicon-remove-circle"></span>
                          </div>

                        </div>

                      </div>
                    </div>

                    <div class="form-group">
                      <div class='row'>
                        <div class='col-md-2'>
                          <label>Variedad:</label>
                        </div>
                        <div class='col-md-5'>
                          <div class="btn-group" style="width:100%">
                            <input id="busca_variedad" style="text-transform:uppercase" type="search"
                              class="form-control">
                            <span id="searchclear" onClick="$('#busca_variedad').val('');"
                              class="glyphicon glyphicon-remove-circle"></span>
                          </div>
                        </div>
                      </div>
                    </div>

                    <div class="form-group">
                      <div class='row'>
                        <div class='col-md-2'>
                          <label>Cliente:</label>
                        </div>
                        <div class='col-md-5'>
                          <div class="btn-group" style="width:100%">
                            <input id="busca_cliente" style="text-transform:uppercase;" type="search"
                              class="form-control">
                            <span id="searchclear" onClick="$('#busca_cliente').val('');"
                              class="glyphicon glyphicon-remove-circle"></span>
                          </div>
                        </div>
                      </div>
                    </div>

                    <div class="form-group">
                      <div class='row'>
                        <div class='col-md-2'>
                          <label>Estado:</label>
                        </div>
                        <div class='col-md-5'>
                          <select id="select_estado" class="selectpicker mobile-device" data-dropup-auto="false"
                            title="Filtrar por Estados" data-style="btn-info" data-width="100%" multiple>
                            <option value="0">Pendiente</option>
                            <option value="1">Planificado</option>
                            <option value="2">Sembrado</option>
                            <option value="3">En Cámara</option>
                            <option value="4">En Invernáculo</option>
                            <option value="5">En Agenda</option>
                            <option value="6">Entrega Parcial</option>
                            <option value="7">Entrega Completa</option>
                            <option value="8">En Stock</option>
                            <option value="-1">Cancelado</option>
                          </select>
                        </div>
                      </div>
                    </div>

                    <div class="form-group">
                      <div class='row'>
                        <div class='col-md-2'>
                          <label>Revisión:</label>
                        </div>
                        <div class='col-md-5'>
                          <select id="busca_tiporevision" class="selectpicker mobile-device" data-dropup-auto="false"
                            title="Tipo de Revisión" data-style="btn-info" data-width="100%">
                            <option value="-1">TODOS</option>
                            <option value="1">Falla de Germinación</option>
                            <option value="2">Golpe</option>
                            <option value="3">Pájaro</option>
                            <option value="4">Rata</option>
                            <option value="5">Realizar Despunte</option>
                            <option value="6">Uso para Injerto</option>
                            <option value="7">D1 Realizado</option>
                          </select>

                        </div>
                        <div class='col-md-5'>
                          <button class='btn btn-danger pull-right' onclick='quitar_filtros();' id='btn-busca'><i
                              class='fa fa-times'></i> Quitar Filtros</button>
                        </div>
                      </div>
                    </div>

                    <div class="form-group">
                      <div class='row'>
                        <div class='col-md-2'>
                          <label>Solución:</label>
                        </div>
                        <div class='col-md-5'>
                          <select id="busca_tiposolucion" class="selectpicker mobile-device" data-dropup-auto="false"
                            title="Tipo de Solución" data-style="btn-info" data-width="100%">
                            <option value="-1">TODOS</option>
                            <option value="1">D1 Cancelado</option>
                            <option value="2">Clasificación</option>
                            <option value="3">Repique</option>
                            <option value="4">Resiembra</option>
                            <option value="5">Dejar Fallas 12</option>
                          </select>
                        </div>
                        <div class='col-md-5'>
                          <button class='btn btn-primary pull-right' onclick='busca_entradas();' id='btn-busca'><i
                              class='fa fa-search'></i> Buscar...</button>
                        </div>
                      </div>
                    </div>
                  </div> <!-- CONTENEDOR BUSQUEDA -->
                </div>
              </div>
            </div>
            <div class='col-md-6 text-right'>
              <button type="button" class="btn btn-primary btn-round" id="btn_printcliente"
                onClick="print_Busqueda(1);"><i class='fa fa-print'></i> IMPRIMIR REGISTROS</button>
            </div>

          </div> <!-- FIN ROW-->
          <div class="row mb-5">
            <div class='col'>
              <div id='tabla_entradas'></div>
            </div>
          </div>
        </section><!-- /.content -->
      </div><!-- /.content-wrapper -->
      <!-- Main Footer -->
      <?php include('class_lib/footer.php'); ?>

      <?php include("modals/ver_pedido.php"); ?>
      <?php include("modals/ver_detalle_pedido.php"); ?>
      <?php include("modals/cambiar_cliente_pedido.php"); ?>
      <?php include("modals/enviar_revision.php"); ?>
      <?php include("modals/marcar_problema.php"); ?>
      <?php include("modals/reasignar_mesadas.php");?>
      <?php include("modals/aplicar_solucion.php"); ?>
      <?php include("modals/cambio_cantidad_bandejas.php"); ?>

      <div class="control-sidebar-bg"></div>

    </div><!-- ./wrapper -->
  </div> <!-- ID OCULTAR-->

  <script type="text/javascript">
    const id_usuario = "<?php echo $_SESSION['id_usuario'] ?>";
    const permisos = "<?php echo $_SESSION['permisos'] ?>";
    func_check(id_usuario, permisos.split(","));

    $(document).ready(function () {
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

      $('#busca_tiposolucion').on('changed.bs.select', function (e, clickedIndex, newValue, oldValue) {
        $("#busca_tiporevision").val('default').selectpicker("refresh");
      });
      $('#busca_tiporevision').on('changed.bs.select', function (e, clickedIndex, newValue, oldValue) {
        $("#busca_tiposolucion").val('default').selectpicker("refresh");
      });

      $.datepicker.setDefaults($.datepicker.regional["es"]);
    });
  </script>
</body>
</html>