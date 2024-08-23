<?php include "./class_lib/sesionSecurity.php"; ?>
<!DOCTYPE html>
<html>
  <head>
    <title>Variedades</title>
    <?php include "./class_lib/links.php"; ?>
    <?php include "./class_lib/scripts.php"; ?>
    <link rel="stylesheet" href="plugins/select2/select2.min.css">
    <script src="dist/js/check_permisos.js?v=<?php echo $version ?>"></script>
    <script src="dist/js/ver_variedades.js?v=<?php echo $version ?>"></script>
    
  </head>

  <body onload="chequear_permisos();busca_productos(null);pone_tipos();openCity(event, 'Variedades');">
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
            Variedades
            
          </h1>
          <ol class="breadcrumb">
            <li><a href="inicio.php"> Inicio</a></li>
            <li class="active">Variedades</li>
          </ol>
        </section>
        <!-- Main content -->
        <section class="content">
        <div class="row">
          <div class="col-6">
            <div class='d-flex flex-row'>
              <label for="select_tipo" class="control-label">Filtrar por:</label>
              <select id="select_tipo" class="selectpicker mobile-device ml-3 w-75" title="Selecciona Tipo de Producto" data-style="btn-info" data-live-search="true" onChange="busca_productos(this.value);"></select>
            </div>
          </div>
          <div class="col text-right">
            <button class="btn btn-success btn-round fa fa-plus-square" style="font-size:1.6em" onclick="pone_tipos();MostrarModalAgregarProducto(null);"></button>
          </div>
        </div>
                
        
         <!-- Your Page Content Here -->
          <div class='row mt-3 mb-5'>
          <div class='col'>
          <div id='tabla_entradas'></div>
          </div>
          </div>
        </section><!-- /.content -->
         </div><!-- /.content-wrapper -->
      <!-- Main Footer -->
      <?php
      include('class_lib/footer.php');
      ?>
      <div id="ModalAgregarProducto" class="modal"> <!--MODALITO AGREGAR ARTICULO -->
      <!-- Modal content -->
        <div class="modal-tipo">
          <div class="tab">
            <button class="tablinks" id="variedades_btn" onclick="openCity(event, 'Variedades')">Variedades</button>
            <button class="tablinks" id="tipos_btn" onclick="openCity(event, 'Tipos');mostrarVistaAgregarTipo();">Tipos</button>
            <button class="tablinks" id="subtipos_btn" onclick="openCity(event, 'Subtipos')">Subtipos</button>
            </div>
        <div id="Variedades" class="tabcontent">
          <div class='box box-primary'>
          <div class='box-header with-border'>
            <h3 id='titulo' class='box-title'>Agregar Variedad</h3>
          </div>
          <div class='box-body'>
            <div class='form-group'>
              <div class='row'>
                <div class='col-md-6'>
                <label for="select_tipo2" class="control-label">Tipo:</label>
                <select id="select_tipo2" title="Tipo de Producto" class="selectpicker mobile-device" data-style="btn-custom" data-live-search="true" data-width="100%"></select>
                </div>
                <div class='col-md-6'>
                <label for="select_subtipo" class="control-label">Subtipo:</label>
                <select id="select_subtipo" title="Subtipo de Producto" class="selectpicker mobile-device" data-style="btn-custom" data-live-search="true" data-width="100%"></select>
                </div>
              </div>
            </div>
            <div class='form-group'>
              <div class='row'>
                <div class='col'>
                <label for="proveedor" class="control-label">Nombre de Variedad:</label>
                <input type="text" id="variedadproducto_txt" style="text-transform:uppercase" class="form-control" placeholder="Ingresa Nombre Variedad"> 
                </div>
              </div>
            </div>
            
            <div class='row mt-2'>
                <div class='col'>
                  <label class="control-label">PRECIOS ESPECÍFICOS ($):</label>
                </div>
            </div>

            <div class="row">
              <div class="col">
                <table id="table-precios-variedad" class="table">
                  <thead class="thead-dark">
                    <tr>
                      <th scope="col">Tamaño</th>
                      <th scope="col">Precio</th>
                      <th scope="col">Precio C/ Semilla</th>
                    </tr>
                  </thead>
                  <tbody>
                  </tbody>
                </table>
              </div>
            </div>
            
            <div align="right">
              <button type="button" class="btn fa fa-close btn-modal-bottom" id="btn_cancel" onClick="CerrarModalProducto();"></button>
              <button type="button" class="btn fa fa-save btn-modal-bottom ml-2"  id= "btn_guardarproducto" onClick="GuardarProducto();"></button>
            </div>
            </div>
        </div>
      </div> <!--FIN TAB -->

      <div id="Tipos" class="tabcontent">
        <div class='box box-primary'>
          <div class='box-header with-border'>
            <h3 id='titulo' class='box-title'>Agregar Tipos</h3>
          </div>
          <div class='box-body'>
            <div class='form-group'>
              <div class='row'>
                <div class='col'>
                <label for="select_tipos_disponibles" class="control-label">Tipos Disponibles (Sólo visualización):</label>
                <select id="select_tipos_disponibles" title="Tipo de Producto" class="selectpicker mobile-device" data-style="btn-custom" data-dropup-auto="false" data-size="5" data-live-search="true" data-width="100%"></select>
                </div>
              </div>
            </div>
            <div class='form-group'>
              <div class='row'>
                <div class='col'>
                <label for="proveedor" class="control-label">Nombre del Tipo de Producto:</label>
                <input type="text" id="nombrenewtipo_txt" style="text-transform:uppercase" class="form-control" placeholder="Ingresa Nombre del Nuevo Tipo"> 
                </div>
              </div>
            </div>
            <div class='form-group'>
              <div class='row'>
                <div class='col-md-6'>
                <label for="proveedor" class="control-label">Días en Cámara:</label>
                <input type="number" id="cant_dias_camara_txt" min="0" step="1" style="text-transform:uppercase" class="form-control" placeholder="Días que debe permanecer en cámara"> 
                </div>
              </div>
            </div>

            <div class='row'>
                <div class='col-md-6'>
                <label for="proveedor" class="control-label">Tipos de Bandeja:</label>
                </div>
            </div>
            <div class="row">
              <div class="col-md-6">

              <select id="select_tipobandeja" class="selectpicker mobile-device" title="Seleccionar" data-style="btn-info" data-dropup-auto="false" data-size="5" data-width="100%" multiple>
                <option value="288">288</option>
                  <option value="200">200</option>
                  <option value="162">162</option>
                  <option value="128">128</option>
                  <option value="72">72</option>
                  <option value="50">50</option>
                  <option value="25">25</option>
                  <option value="49">49</option>


              </select>

              
              </div>
            </div>
            
            <div class='row' style="margin-top: 15px">
                <div class='col'>
                <label class="control-label">PRECIOS ($):</label>
                </div>
            </div>

            <div class="row">
              <div class="col">
                <table id="table-precios" class="table table-responsive w-100 d-block d-md-table">
                  <thead class="thead-dark">
                    <tr>
                      <th scope="col">Tamaño</th>
                      <th scope="col">Precio</th>
                      <th scope="col">Precio C/ Semilla</th>
                    </tr>
                  </thead>
                  <tbody>
                  </tbody>
                </table>
              </div>
            </div>

            <div align="right">
              <button type="button" class="btn fa fa-close btn-modal-bottom" id="btn_cancel" onClick="CerrarModalProducto();"></button>
              <button type="button" class="btn fa fa-save btn-modal-bottom" id="btn_guardartipo" onClick="GuardarTipo();"></button>
            </div>
            </div>
        </div>
      </div> <!--FIN TAB -->

      <div id="Subtipos" class="tabcontent">
        <div class='box box-primary'>
          <div class='box-header with-border'>
            <h3 id='titulo' class='box-title'>Agregar Subtipo</h3>
          </div>
           <div class='box-body'>

            <div class='form-group'>

              <div class='row'>

                <div class='form-group col'>

                <label for="select_tipo3" class="control-label">Tipo:</label>

                <select id="select_tipo3" title="Tipo de Producto" class="selectpicker mobile-device" data-style="btn-custom" data-live-search="true" data-width="100%"></select>

                </div>
              </div>
              <div class="row">
                <div class='form-group col'>

                <label for="select_subtipos_disponibles" class="control-label">Subtipos Disponibles<br>(Sólo Visualización):</label>

                <select id="select_subtipos_disponibles" title="Subtipo de Producto" class="selectpicker mobile-device" data-style="btn-custom" data-live-search="true" data-width="100%"></select>

                </div>

              </div>

            </div>

            <div class='form-group'>
              <div class='row'>
                <div class='col'>
                  <label for="proveedor" class="control-label">Nombre del Subtipo:</label>
                  <input type="text" id="nombrenewsubtipo_txt" style="text-transform:uppercase" class="form-control" placeholder="Ingresa Nombre del Nuevo Subtipo"> 
                </div>
              </div>
            </div>

            <div align="right">

              <button type="button" class="btn fa fa-close btn-modal-bottom" id="btn_cancel" onClick="CerrarModalProducto();"></button>

              <button type="button" class="btn fa fa-save btn-modal-bottom ml-2" id="btn_guardarsubtipo" onClick="GuardarSubTipo();"></button>

            </div>

            </div>

        </div>

      </div> <!--FIN TAB -->



      <!-- Add the sidebar's background. This div must be placed

           immediately after the control sidebar -->

      <div class="control-sidebar-bg"></div>

    </div><!-- ./wrapper -->

  </div> <!-- MODAL FIN -->

    <!-- REQUIRED JS SCRIPTS -->

    <script src="plugins/moment/moment.min.js"></script>
    <script src="plugins/daterangepicker/daterangepicker.js"></script>
    <script type="text/javascript">
      $(document).ready(function() {
        if (/Android|webOS|iPhone|iPad|iPod|BlackBerry/i.test(navigator.userAgent)) {
          $('.selectpicker').selectpicker('mobile');
        }
        else {
          let elements = document.querySelectorAll('.mobile-device'); 
          for(let i = 0; i < elements.length; i++){
            elements[i].classList.remove('mobile-device');
          }
          $('.selectpicker').selectpicker({});
        }
      }); 

      

    function chequear_permisos(){
      let permisos = "<?php echo $_SESSION['permisos'] ?>"; 
    }

    let id_usuario = "<?php echo $_SESSION['id_usuario'] ?>"; 
    let permisos = "<?php echo $_SESSION['permisos'] ?>"; 
    func_check(id_usuario, permisos.split(","));   
    </script>
  </body>
</html>