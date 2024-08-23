<?php include "./class_lib/sesionSecurity.php"; ?>
<!DOCTYPE html>
<html>
  <head>
    <title>Siembra</title>
    <?php include "./class_lib/links.php"; ?>
    <?php include "./class_lib/scripts.php"; ?>
    <script src="dist/js/check_permisos.js?v=<?php echo $version ?>"></script>
    <script src="dist/js/ver_plansiembra.js?v=<?php echo $version ?>"></script>
  </head>
  <body onload="$('#select_tiposeleccion').selectpicker('val', [0]);">
    <div id="miVentana">
    </div>
  <div id="ocultar">
    <div class="wrapper">
     <!-- Main Header -->
      <header class="main-header">
        <!-- Logo -->
        <?php
        include('class_lib/nav_header.php');
        ?>
      </header>
      <!-- Left side column. contains the logo and sidebar -->
      <aside class="main-sidebar">
        <!-- sidebar: style can be found in sidebar.less -->
        <?php
        include('class_lib/sidebar.php');
        $dias = array("Domingo","Lunes","Martes","Miércoles","Jueves","Viernes","Sábado");
        $meses = array("Enero","Febrero","Marzo","Abril","Mayo","Junio","Julio","Agosto","Septiembre","Octubre","Noviembre","Diciembre");
        $fecha=$dias[date('w')]." ".date('d')." de ".$meses[date('n')-1]. " del ".date('Y') ;
        ?>
        <!-- /.sidebar -->
      </aside>
      <!-- Content Wrapper. Contains page content -->
      <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <section class="content-header">
          <h1>
           Siembra
          </h1>
          <ol class="breadcrumb">
            <li><a href="inicio.php"> Inicio</a></li>
            <li class="active">Siembra</li>
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
                      <button class="btn btn-dark btn-block" onClick="changeWeek(-1);" style="font-weight: bold;">Día Anterior</button>
                    </div>
                    <div class="col-md-4">
                      <button class="btn btn-dark btn-block" onClick="changeWeek(0);" style="font-weight: bold;">HOY</button>
                    </div>
                    <div class="col-md-4">
                      <button class="btn btn-dark btn-block mt-lg-0 mt-2" onClick="changeWeek(1);" style="font-weight: bold;">Día Siguiente</button>
                    </div>

                  </div>
                </div>
              </div>

              
              <div class="col-md-6 text-center">
                <h4 id="header" class="mt-lg-0 mt-3 mb-lg-0 mb-3 font-weight-bold"></h4>
              </div>

              <div class="col-md-2 text-lg-right text-center">
                <input type='text' data-date-format='dd/mm/yy' value='<?php echo date('d/m/Y'); ?>' class="datepicker form-control" id="currentfecha_txt" placeholder="DD-MM-AAAA"/>
              </div>
            </div>


          <div class='row mt-3'>
          <div class='col-md-6'>
          <div class='box box-primary'>
          <div class='box-header with-border'>
          <div class='row'>
            <div class="col-md-5">
              <h3 class='box-title'>Herramientas de Selección</h3>
            </div>
            <div class="col-md-4">
              <div id="contenedor_tipo" style="display:none">
                <select id="select_tiposeleccion" class="selectpicker"  data-dropup-auto="false" title="Tipo Selección" data-style="btn-info">
                      <option value="0">Por Tipo de Bandeja</option>
                      <option value="1">Por Variedad</option>
                    </select>
                </div>
            </div>
            <div class="col-md-3">  
              <button class='btn btn-primary pull-right' onclick='expande_busqueda()' id='btn-busca'><i class='fa fa-caret-down'></i> Opciones</button>
              </div>
            </div>


          </div>
          <div class='box-body'>
            
            <div id="contenedor_porbandeja" style="display:none;margin-top:10px;height:150px;overflow-y: auto">
            </div> <!-- CONTENEDOR BUSQUEDA -->
          </div>
        </div>
        
      </div>
      <div class="col-md-6">
          <button class='btn btn-primary pull-right mt-2' onclick="$('.selected2').removeClass('selected2');setSumaBandejas(null);" id='btn-busca'><i class='fa fa-close'></i> Deseleccionar Todo</button>
        </div>
    </div>
















            <div class="row">
              <div class="col">  
                <table class="table table-bordered table-responsive w-100 d-block d-md-table" role="grid" id="tablitaa"> 
                  <thead>
                    <tr role="row">
                      <th class="text-center">Nº Orden</th>
                      <th class="text-center w-25">Producto</th>
                      <th class="text-center">Tamaño</th>
                      <th class="text-center">Cliente</th>
                      <th class="text-center">Bandejas</th>
                      <th class="text-center">Semillas</th>
                      <th class="text-center">Plantas</th>
                      <th class="text-center">Cód. de Sobre</th>
                      <th class="text-center">Observ.</th>
                      <th class="text-center">Estado</th>
                    </tr>
                  </thead>
                  <tbody>
                  </tbody>
                </table>
            </div>
            
          </div> <!--FIN DIV TABLA -->
        </div>
        </section><!-- /.content -->

      <div id="ModalVerOrden" class="modal">
        <div class="modal-content-verpedido">
          <div class='box box-primary'>
          <div class='box-header with-border'>
            <h4 class='box-title'><b>Órden de Siembra Nº <span id='num_orden'></span></b></h4>
            <button style="float:right;font-size:1.5em" class="btn fa fa-close" onClick="CerrarModalOrden()"></button>
            <button style='float:right;font-size: 1.5em' class="btn fa fa-print" onClick="printOrdenSiembra(1)"> IMPRIMIR</button> 
           </div>
           <div id="tablita">
           <div class='box-body'>
            <table id="tabla_detallepedido" class="table table-bordered table-responsive w-100 d-block d-md-table" role="grid">
              <thead>
              <tr role="row">
                <th class="text-center">Nº</th>
                <th class="text-center">Producto</th>
                <th class="text-center">Bandejas</th>
                <th class="text-center">Semillas</th>
                <th class="text-center">Plantas</th>
                <th class="text-center">Cód. Sobre</th>
                <th class="text-center">Observación</th>
                <th class="text-center">Estado</th>
              </tr>
              </thead>
              <tbody>
              </tbody>
           </table>
          </div>
          <span id="id_artpedidohidesiembra" style="display:none"></span>
        </div>
          </div>
        </div>
      </div> <!-- FIN MODAL VER ORDEN -->


      <div id="ModalSembrarVarios" class="modal">
        <div class="modal-content-verpedido">
          <div class='box box-primary'>
          <div class='box-header with-border'>
            <h4 class='box-title'><b>Siembra Múltiple</b></h4>
            <button style="font-size:1.5em" class="btn fa fa-close pull-right" onClick="$('#ModalSembrarVarios').modal('hide')"></button>

            <button id="btn-sembrar" class="btn fa fa-save pull-right" style="font-size: 1.5em;margin-right: 1.5em" onclick="guardarSiembra()"> SEMBRAR</button>
            
           </div>
           <div id="tablitasiembra">
           <div class='box-body'>
            <div class='form-group'>
                <div class="row">
                  <div class="col-md-8">
                    <label class="control-label">Fecha:</label>
                    <input type='text'  data-date-format='dd/mm/yy' value='<?php echo date('d/m/Y'); ?>' class="datepicker" id="fechasiembramulti_txt" style="font-size:1.2em">
                    </div>
                  </div>
                </div>
                <br>
                <div class='form-group'>
                  <div class="row">
                  <div class="col-md-8">
                    <label class="control-label">Hora:</label>
                    <input type="time" style="margin-left:9px;padding:2px;font-size:1.2em" id="timesiembra" name="timesiembra" min="07:00" max="20:00" required>
                    </div>
                  </div>
                </div> 


            <table id="tabla_sembrarvarios" class="table table-bordered table-responsive w-100 d-block d-md-table" role="grid">
              <thead>
              <tr role="row">
                <th class="text-center">Ord. Nº</th>
                <th class="text-center">Producto</th>
                <th class="text-center">Tamaño</th>
                <th class="text-center">Cliente</th>
                <th class="text-center">Observación</th>
                <th class="text-center">Bandejas<br>Pedidas</th>
                <th class="text-center">Bandejas<br>Reales</th>
              </tr>
              </thead>
              <tbody>
              </tbody>
           </table>
          </div>
          
        </div>
          </div>
        </div>
      </div> <!-- FIN MODAL SIEMBRA MULTIPLE -->


        <div id="ModalCambiarEstado" class="modal">
          <div class="modal-content3">
            <div class='box box-primary'>
           <div class='box-header with-border'>
           <h3 class='box-title'>Cambiar de Estado Orden Nº <span id="num_orden2"></span></h3>
           </div>
          <div class='box-body'>
              <div class="form-row">
                <div class='form-group col-md-4'>
                  
                    <label for="fechasiembra_txt" class="control-label">Fecha:</label>
                    <input type='text'  data-date-format='dd/mm/yy' value='<?php echo date('d/m/Y'); ?>' class="datepicker form-control" id="fechasiembra_txt" style="font-size:1.2em">
                  
                  </div>
                </div>
                
                
                <div class="form-row">
                  <div class='form-group col-md-4'>
                  
                    <label for="appt" class="control-label">Hora:</label>
                    <input type="time" class="form-control" style="font-size:1.2em" id="appt" name="appt" min="07:00" max="20:00" required>
                    </div>
                  
                </div> 
                
                <div class="form-row">
                  <div class='form-group col-md-4'>
                      <label for="cantidad_bandejas_reales" class="control-label">Bandejas Reales:</label>
                      <input type="number" min="0" step="1" id="cantidad_bandejas_reales" class="form-control" onClick="this.select();" value="0" style="font-size:1.6em"> 
                  </div>
                    
                </div> 
                </div>
              </div>
              <div class="row">
              <div class="col text-right">
                <button type="button" class="btn fa fa-close btn-modal-bottom" id="btn_cancel" onClick="CerrarModalCambio();"></button>
                <button type="button" class="btn fa fa-save btn-modal-bottom ml-3" id="btn_guardarcliente" onClick="GuardarCambioEstado();"></button>
                </div>
              <span id="id_artpedidohide" style="display:none"></span>
              </div>
        </div>
        </div>
      </div>
      </div> <!-- MODAL POPUP FIN -->


      </div><!-- /.content-wrapper -->

      <!-- Main Footer -->
      </div>
</div>


      
      <?php

      include('./class_lib/footer.php');

      ?>

      <!-- Add the sidebar's background. This div must be placed

           immediately after the control sidebar -->

      <div class="control-sidebar-bg"></div>

    </div><!-- ./wrapper -->



    </div>

    <style>
              table,
              thead,
              tr,
              tbody,
              th,
              td {
                text-align: center;
                table-layout: fixed;
                word-wrap:break-word;
              }
              .table td {
                text-align: center;
                height: 70px;
              }
              .table tr.selected td {
                background-color: #333;
                color: #fff;    
              }
              .table tr.selected2 td {
                background-color: #333;
                color: #fff;    
              }
    
            </style> 

<script>
  const id_usuario = "<?php echo $_SESSION['id_usuario'] ?>";
    const permisos = "<?php echo $_SESSION['permisos'] ?>";
    func_check(id_usuario, permisos.split(","));
</script>
  
  </body>
</html>