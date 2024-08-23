<?php include "./class_lib/sesionSecurity.php"; ?>
<!DOCTYPE html>

<html>
  <head>
    <title>Historial de Entregas</title>
    <?php include "./class_lib/links.php"; ?>
    <?php include "./class_lib/scripts.php"; ?>
    <link rel="stylesheet" href="plugins/select2/select2.min.css">
    <link rel="stylesheet" href="plugins/daterangepicker/daterangepicker-bs3.css">
    <script src="dist/js/ver_mesadas.js?v=<?php echo $version ?>"></script>
    <script src="dist/js/ver_historial_entregas.js?v=<?php echo $version ?>"></script>
    <script src="dist/js/check_permisos.js?v=<?php echo $version ?>"></script>
    
  </head>
  <body onload="busca_entradas();">
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


        ?>
        <!-- /.sidebar -->
      </aside>

      <!-- Content Wrapper. Contains page content -->
      <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <section class="content-header">
          <h1>
            Historial de Entregas
            
          </h1>
          <ol class="breadcrumb">
            <li><a href="inicio.php"> Inicio</a></li>
            <li class="active">Historial de Entregas</li>
          </ol>
        </section>

        <!-- Main content -->
        <section class="content">

          <!-- Your Page Content Here -->
          <div class='row'>
          <div class='col-md-4'>
          <div class='box box-primary'>
          <div class='box-header with-border'>
          <h3 class='box-title'>Filtrar por Fechas</h3>
          </div>
          <div class='box-body'>
            <div class="form-group">
                    <div class='row'>
                      <div class='col-md-2'>
                        <label>Fechas:</label>
                      </div>
                      <div class='col-md-8'>
                        <div class="input-group">
                          <button class="btn btn-default pull-left" id="daterange-btn">
                            <i class="fa fa-calendar"></i> Seleccionar...
                            <i class="fa fa-caret-down"></i>
                          </button>
                        </div>
                      </div>
                      <div class='col-md-2'>
                        <button class='btn btn-primary pull-right' onclick='busca_entradas();' id='btn-busca'><i class='fa fa-search'></i> Buscar...</button>
                      </div>
                    </div>
                    <span class='fe'></span>
                    <input type='hidden'  class='form-control' id='fi' value=''>
                    <input type="hidden"  class='form-control' id='ff' value=''>
            </div><!-- /.form group -->


          </div>
          



          </div>
          </div>
          
        </div>
        <div class="row">
          <div class='col'>
          <div id='tabla_entradas'></div>
          </div>
          </div>
          
        </div>
          

        </section><!-- /.content -->
         </div><!-- /.content-wrapper -->

      
      <!-- Main Footer -->
      <?php
      include('class_lib/footer.php');
      ?>
 
      <div id="ModalInfoEntregas" class="modal">

        <div class="modal-content-verpedido">
           
            
            <div class='box box-primary'>
           <div class='box-header with-border'>
            <h4 class='box-title'>Registro de Entregas</h4>
            
            <button style="float:right;" class="btn btn-modal-top fa fa-close" onClick="CerrarModalInfoEntregas()"></button>
            <button style='float:right;' class="btn btn-modal-top fa fa-print" onClick="printOrdenEntrega(1)"> IMPRIMIR</button> 

           </div>
           
           <div class='box-body'>
            <div id="contenedor_entrega"> 
            </div>
        </div>
          </div>
        </div>

      </div> <!--FIN MODAL VER ORDEN -->

      <div id="ModalActualizarStock" class="modal">
        <div class="modal-content-verpedido">
            <div class='box box-primary'>
           <div class='box-header with-border'>
            <h4 class='box-title'>Actualizar el Stock</h4>
            <button type="button" class="btn btn-modal-top fa fa-save" onClick="GuardarStock();"></button>

            <button style="float:right" class="btn btn-modal-top fa fa-close" onClick="$('#ModalActualizarStock').modal('hide');"></button>
            
           </div>
           
           <div class='box-body'>
            <div style="width: 100%">
              <div style="width:48%;float:left">
                <span style="font-size: 2em;font-weight: bold;color:red">Cantidad Requerida: <span id='cantientrega'></span></span>
              </div>
              <div style="width:48%;float:right;text-align: right;">
                <span style="font-size:1.3em;font-weight: bold;color:green;">Cantidad a Entregar: <span id='cantidad_a_entregar' style="font-size:1.3em;font-weight: bold;color:green;">0</span></span>
              </div>
            </div>
            <div id="contenedor_stock"> 
            </div>
          
          <span id="id_artpedidohide" style="display:none"></span>
          <span id="id_entrega" style="display:none"></span>
        </div>
          </div>
        </div>

      </div> <!--FIN MODAL VER ORDEN -->


        <div id="ModalEnviaraMesadas" class="modal">
          <div class="modal-devolver-mesadas">
              <div class='box box-primary'>

             <div class='box-header with-border'>

              <h4 class="box-title" id="nombre_cliente">Devolver a Mesadas</h4>

              <button style="font-size: 1.6em" class="btn fa fa-close pull-right" onClick="CerrarModalMesadas()"></button>

              <button style="font-size: 1.6em;margin-right: 2em" type="button" class="btn fa fa-save pull-right" id="btn_guardarcliente" onClick="GuardarMesadas();"> GUARDAR</button>

              
             </div>

              <div class='row'>

              <div class='col'>

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

            
           


            

        </div>
            </div>
          </div>
        </div> <!-- FIN MODAL MESADAS -->




      <!-- Add the sidebar's background. This div must be placed
           immediately after the control sidebar -->
      <div class="control-sidebar-bg"></div>
    </div><!-- ./wrapper -->
  
  </div> <!-- ID OCULTAR-->
  

    <!-- REQUIRED JS SCRIPTS -->
    
    
    
    <script src="plugins/moment/moment.min.js"></script>
    <script src="plugins/daterangepicker/daterangepicker.js"></script>
    
    <script type="text/javascript">
       $(function () {


        $.datepicker.setDefaults($.datepicker.regional["es"]);
        $("#fechaentrega_picker").datepicker({
          minDate: 28,
          dateFormat: "dd/mm/yy",
          onSelect: function(dateText, inst) {
            let date = $(this).val();
            let datesplit = date.split("/");

            let fecha = new Date(datesplit[2]+"/"+datesplit[1]+"/"+datesplit[0]);
            fecha.setDate(fecha.getDate() - 28); 
            

            $('#fechasiembra_txt').val(("0" + fecha.getDate()).slice(-2) + "/" + ("0" + (fecha.getMonth() + 1)).slice(-2) + "/" + fecha.getFullYear());
            
          }
        });
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
    </style>


  </body>
</html>