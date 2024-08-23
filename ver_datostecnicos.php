<?php include "./class_lib/sesionSecurity.php"; ?>
<!DOCTYPE html>
<html>
<head>
  <title>Datos Técnicos</title>
  <?php include "./class_lib/links.php"; ?>
  <?php include "./class_lib/scripts.php"; ?>
  <link rel="stylesheet" href="plugins/select2/select2.min.css">
  <link rel="stylesheet" href="plugins/daterangepicker/daterangepicker-bs3.css">
  <script src="plugins/moment/moment.min.js"></script>
  <script src="plugins/daterangepicker/daterangepicker.js"></script>
  <script src="dist/js/ver_datostecnicos.js?v=<?php echo $version ?>"></script>
  <script src="dist/js/ver_pedidos.js?v=<?php echo $version ?>"></script>
  <script src="dist/js/check_permisos.js?v=<?php echo $version ?>"></script>
</head>

<body onload="busca_entradas();pone_tipos();">
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
          <h1>Datos Técnicos</h1>
          <ol class="breadcrumb">
            <li><a href="inicio.php"> Inicio</a></li>
            <li class="active">Datos Técnicos</li>
          </ol>
        </section>
        <!-- Main content -->
        <section class="content">
          <!-- Your Page Content Here -->
          <div class='row'>
            <div class='col-md-5'>
              <div class='box box-primary'>
                <div class='box-header with-border'>
                  <h3 class='box-title'>Buscar</h3>
                  <button class='btn btn-primary pull-right' onclick='expande_busqueda()' id='btn-busca'><i
                      class='fa fa-caret-down'></i> Busqueda Avanzada</button>
                </div>
                <div class='box-body'>
                  <div class="form-group">
                    <div class='row'>
                      <div class='col-sm-2'>
                        <label>Fechas:</label>
                      </div>
                      <div class='col-sm-8'>
                        <div class="input-group">
                          <button class="btn btn-default pull-left" id="daterange-btn">
                            <i class="fa fa-calendar"></i> Seleccionar...
                            <i class="fa fa-caret-down"></i>
                          </button>
                        </div>
                      </div>
                      <div class='col-sm-2'>
                        <button class='btn btn-primary pull-right' onclick='busca_entradas();' id='btn-busca'><i
                            class='fa fa-search'></i> Buscar...</button>
                      </div>
                    </div>
                    <span class='fe'></span>
                    <input type='hidden' class='form-control' id='fi' value=''>
                    <input type="hidden" class='form-control' id='ff' value=''>
                  </div><!-- /.form group -->
                  <div id="contenedor_busqueda" style="display:none">
                    <div class="form-group">
                      <div class='row'>
                        <div class='col-sm-2'>
                          <label>Variedad:</label>
                        </div>
                        <div class='col-sm-5'>
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
                        <div class='col-sm-2'>
                          <label>Estado:</label>
                        </div>
                        <div class='col-sm-5'>
                          <select id="select_estado" class="selectpicker mobile-device" data-dropup-auto="false"
                            title="Filtrar por Estados" data-style="btn-info" data-width="100%" multiple>
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
                        <div class='col-sm-2'>
                          <label>Revisión:</label>
                        </div>
                        <div class='col-sm-5'>
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
                        <div class='col-sm-5'>
                          <button class='btn btn-danger pull-right' onclick='quitar_filtros();' id='btn-busca'><i
                              class='fa fa-times'></i> Quitar Filtros</button>
                        </div>
                      </div>
                    </div>

                    <div class="form-group">
                      <div class='row'>
                        <div class='col-sm-2'>
                          <label>Solución:</label>
                        </div>
                        <div class='col-sm-5'>
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
                        <div class='col-sm-5'>
                          <button class='btn btn-primary pull-right' onclick='busca_entradas();' id='btn-busca'><i
                              class='fa fa-search'></i> Buscar...</button>
                        </div>
                      </div>
                    </div>
                  </div> <!-- CONTENEDOR BUSQUEDA -->
                </div>
              </div>
            </div>
            <div class="col text-right">
              <button type="button" class="btn btn-primary btn-round btn-lg fa fa-print" id="btn_printcliente"
                onClick="print_Busqueda(1);"> IMPRIMIR</button>
            </div>

          </div>
          <div class="row mt-2 mb-5">
            <div class='col'>
              <div id='tabla_entradas'></div>
            </div>
          </div>
        </section><!-- /.content -->
      </div><!-- /.content-wrapper -->
      <!-- Main Footer -->
      <?php include('class_lib/footer.php');?>
      <?php include('modals/ver_pedido.php');?>
      <?php include('modals/ver_detalle_pedido.php');?>
      <?php include("modals/enviar_revision.php"); ?>
      <?php include("modals/marcar_problema.php"); ?>
      <?php include("modals/aplicar_solucion.php"); ?>

      <div class="control-sidebar-bg"></div>
    </div><!-- ./wrapper -->
  </div> <!-- ID OCULTAR-->

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

      $.datepicker.setDefaults($.datepicker.regional["es"]);
    });
  </script>
</body>

</html>