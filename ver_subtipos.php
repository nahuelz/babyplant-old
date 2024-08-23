<?php include "./class_lib/sesionSecurity.php"; ?>
<!DOCTYPE html>
<html>
  <head>

    <title>Administrar Productos</title>

    <?php include "./class_lib/links.php"; ?>

    <?php include "./class_lib/scripts.php"; ?>

    <link rel="stylesheet" href="plugins/select2/select2.min.css">

    <link rel="stylesheet" href="plugins/daterangepicker/daterangepicker-bs3.css">

    <script src="dist/js/ver_subtipos.js?v=<?php echo $version ?>"></script>

    <script src="dist/js/check_permisos.js?v=<?php echo $version ?>"></script>

    

  </head>

  <body onload="busca_productos(null);">

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

            Subtipos de Producto

            

          </h1>

          <ol class="breadcrumb">

            <li><a href="inicio.php"> Inicio</a></li>

            <li class="active">Subtipos de Producto</li>

          </ol>

        </section>



        <!-- Main content -->

        <section class="content">

          <div class="row">
            <div class="col text-right">
                <button class="btn btn-success btn-lg fa fa-plus-square" style="font-size: 1.3em" onclick="MostrarModalAgregarProducto(null);"></button>
            </div>
          </div>

        

          <!-- Your Page Content Here -->

          <div class='row mt-2 mb-5'>

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

          <div class="modal-content3">

            <div class='box box-primary'>

           <div class='box-header with-border'>

           <h3 id='titulo' class='box-title'>Agregar Subtipos</h3>

           </div>

           <div class='box-body'>

            <div class='form-group'>

              <div class='row'>

                <div class='col-sm-6'>

                <label for="preciou" class="control-label">Tipo:</label>

                <select id="select_tipo2" title="Tipo de Producto" class="selectpicker mobile-device" data-style="btn-custom" data-live-search="true" data-width="100%"></select>

                </div>

              </div>

            </div>



            <div class='form-group'>

              <div class='row'>

                <div class='col-sm-12'>

                <label for="proveedor" class="control-label">Nombre del Subtipo de Producto:</label>

                <input type="text" id="nombrenewtipo_txt" style="text-transform:uppercase" class="form-control" placeholder="Nombre del Nuevo Subtipo"> 

                </div>

                

              </div>

            </div>



            <div align="right">

              <button type="button" class="btn fa fa-close btn-modal-bottom" id="btn_cancel" onClick="CerrarModalProducto();"></button>

              <button type="button" class="btn fa fa-save btn-modal-bottom ml-2" id="btn_guardartipo" onClick="GuardarSubtipo();"></button>

            </div>

            </div>

        </div>

      

      <!-- Add the sidebar's background. This div must be placed

           immediately after the control sidebar -->

      <div class="control-sidebar-bg"></div>

    </div><!-- ./wrapper -->

  </div> <!-- MODAL FIN -->

    
    <script src="plugins/moment/moment.min.js"></script>

    <script src="plugins/daterangepicker/daterangepicker.js"></script>


    <script>

      let id_usuario = "<?php echo $_SESSION['id_usuario'] ?>"; 

       let permisos = "<?php echo $_SESSION['permisos'] ?>"; 

       func_check(id_usuario, permisos.split(","));   
       $(document).ready(function() {

            if (/Android|webOS|iPhone|iPad|iPod|BlackBerry/i.test(navigator.userAgent)) {

                $('.selectpicker').selectpicker('mobile');

            }

            else {

                let elements = document.querySelectorAll('.mobile-device'); 

                for(let i = 0; i < elements.length; i++)

                {

                    elements[i].classList.remove('mobile-device');

                }

                      

                $('.selectpicker').selectpicker({});

            }



          }); 
    </script>

  </body>

</html>