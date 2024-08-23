function func_check(id_usuario, permisos){
  $(document).ready(function(){

      $("#contenedor_modulos").html("");
      
       if (id_usuario == "1"){

          $("#contenedor_modulos").append("<li><a href='cargar_pedido.php'><i class='fa fa-arrow-circle-right'></i> Agregar Pedido</a></li> \
                        <li><a href='ver_pedidos.php'><i class='fa fa-arrow-circle-right'></i> Ver Pedidos</a></li>");
          $("#contenedor_modulos").append("<li><a href='ver_planificacion.php'><i class='fa fa-arrow-circle-right'></i> Planificación de Pedidos</a></li>");
          $("#contenedor_modulos").append("<li><a href='ver_plansiembra.php'><i class='fa fa-arrow-circle-right'></i> Siembra</a></li>");
          $("#contenedor_modulos").append("<li><a href='ver_camara.php'><i class='fa fa-arrow-circle-right'></i> Cámara</a></li>");
          $("#contenedor_modulos").append("<li><a href='ver_mesadas.php'><i class='fa fa-arrow-circle-right'></i> Mesadas</a></li>");
          $("#contenedor_modulos").append("<li><a href='ver_planentregas.php'><i class='fa fa-arrow-circle-right'></i> Planificación Entregas</a></li>");
          $("#contenedor_modulos").append("<li><a href='ver_agenda_entregas.php'><i class='fa fa-arrow-circle-right'></i> Agenda de Entregas</a></li>");
          $("#contenedor_modulos").append("<li><a href='ver_historial_entregas.php'><i class='fa fa-arrow-circle-right'></i> Historial de Entregas</a></li>");
          $("#contenedor_modulos").append("<li><a href='ver_stock.php'><i class='fa fa-arrow-circle-right'></i> Stock</a></li>");
          $("#contenedor_modulos").append("<li><a href='ver_ordenes_siembra.php'><i class='fa fa-arrow-circle-right'></i> Órdenes Siembra</a></li>");
          $("#contenedor_modulos").append("<li><a href='ver_datostecnicos.php'><i class='fa fa-arrow-circle-right'></i> Datos Técnicos</a></li>");
          $("#contenedor_modulos").append("<li><a href='ver_remitos.php'><i class='fa fa-arrow-circle-right'></i> Remitos</a></li>");
          $("#contenedor_modulos").append("<li><a href='ver_problemas.php'><i class='fa fa-arrow-circle-right'></i> Problemas</a></li>");
          $("#contenedor_modulos").append("<li><a href='ver_situacion.php'><i class='fa fa-arrow-circle-right'></i> Situación</a></li>");
          $("#contenedor_panel").html("<a href='#'><i class='fa fa-bars'></i> <span>Panel de Control</span> <i class='fa fa-angle-left pull-right'></i></a> \
                          <ul class='treeview-menu'> \
                            <li><a href='ver_clientes.php'><i class='fa fa-arrow-circle-right'></i> Clientes</a></li> \
                            <li><a href='ver_variedades.php'><i class='fa fa-arrow-circle-right'></i> Variedades</a></li> \
                            <li><a href='ver_tipos.php'><i class='fa fa-arrow-circle-right'></i> Tipos (Especies)</a></li> \
                            <li><a href='ver_subtipos.php'><i class='fa fa-arrow-circle-right'></i> Subtipos (Subespecies)</a></li> \
                            <li><a href='ver_usuarios.php'><i class='fa fa-arrow-circle-right'></i> Usuarios</a></li> \
                            <li><a href='crear_backup.php'><i class='fa fa-arrow-circle-right'></i> Copia de Seguridad</a></li> \
                          </ul>");

      }else{

        

                  if (permisos.length > 0){

                    let array = permisos;

                    let path = window.location.pathname;

                    let page = path.split("/").pop().replace(".php","");

                    if (page == "cargar_pedido"){

                      page = "ver_pedidos";

                    }

                    let permisos1 = ["pedidos","planificacionpedidos","siembra","camara","mesadas","planentregas",
                    "ordenesentrega","historialentregas","stock", "ordenes_siembra", "datostecnicos", "remitos", "problemas", "situacion", "panel", "panel", "panel", "panel"];



                    let permisos2 = ["ver_pedidos","ver_planificacion","ver_plansiembra","ver_camara","ver_mesadas","ver_planentregas",
                    "ver_agenda_entregas","ver_historial_entregas","ver_stock", "ver_ordenes_siembra", "ver_datostecnicos", "ver_remitos", "ver_problemas", "ver_situacion", "ver_clientes","ver_variedades",
                    "ver_tipos","ver_subtipos"];



                    if (permisos.includes(permisos1[permisos2.indexOf(page)]) == false){

                      window.location.href = 'inicio.php';

                    }

                    else{

                      for (let i=0;i<array.length;i++){

                        if (array[i] == "pedidos"){

                          $("#contenedor_modulos").append('<li><a href="cargar_pedido.php"><i class="fa fa-arrow-circle-right"></i> Agregar Pedido</a></li> \
                          <li><a href="ver_pedidos.php"><i class="fa fa-arrow-circle-right"></i> Ver Pedidos</a></li>');

                        }

                        else if (array[i] == "planificacionpedidos"){

                          $("#contenedor_modulos").append('<li><a href="ver_planificacion.php"><i class="fa fa-arrow-circle-right"></i> Planificación de Pedidos</a></li>');

                        }

                        else if (array[i] == "siembra"){

                          $("#contenedor_modulos").append('<li><a href="ver_plansiembra.php"><i class="fa fa-arrow-circle-right"></i> Siembra</a></li>');

                        }

                        else if (array[i] == "camara"){

                          $("#contenedor_modulos").append('<li><a href="ver_camara.php"><i class="fa fa-arrow-circle-right"></i> Cámara</a></li>');

                        }

                        else if (array[i] == "mesadas"){

                          $("#contenedor_modulos").append('<li><a href="ver_mesadas.php"><i class="fa fa-arrow-circle-right"></i> Mesadas</a></li>');

                        }

                        else if (array[i] == "planentregas"){

                          $("#contenedor_modulos").append('<li><a href="ver_planentregas.php"><i class="fa fa-arrow-circle-right"></i> Planificación Entregas</a></li>');

                        }

                        else if (array[i] == "ordenesentrega"){

                          $("#contenedor_modulos").append('<li><a href="ver_agenda_entregas.php"><i class="fa fa-arrow-circle-right"></i> Agenda de Entregas</a></li>');

                        }

                        else if (array[i] == "historialentregas"){

                          $("#contenedor_modulos").append('<li><a href="ver_historial_entregas.php"><i class="fa fa-arrow-circle-right"></i> Historial de Entregas</a></li>');

                        }

                        else if (array[i] == "stock"){

                          $("#contenedor_modulos").append('<li><a href="ver_stock.php"><i class="fa fa-arrow-circle-right"></i> Stock</a></li>');

                        }

                        else if (array[i] == "ordenes_siembra"){

                          $("#contenedor_modulos").append('<li><a href="ver_ordenes_siembra.php"><i class="fa fa-arrow-circle-right"></i> Órdenes Siembra</a></li>');

                        }

                        else if (array[i] == "datostecnicos"){

                          $("#contenedor_modulos").append('<li><a href="ver_datostecnicos.php"><i class="fa fa-arrow-circle-right"></i> Datos Técnicos</a></li>');

                        }

                        else if (array[i] == "remitos"){
                          $("#contenedor_modulos").append('<li><a href="ver_remitos.php"><i class="fa fa-arrow-circle-right"></i> Remitos</a></li>');
                        }

                        else if (array[i] == "problemas"){
                          $("#contenedor_modulos").append('<li><a href="ver_problemas.php"><i class="fa fa-arrow-circle-right"></i> Problemas</a></li>');
                        }

                        else if (array[i] == "situacion"){
                          $("#contenedor_modulos").append('<li><a href="ver_situacion.php"><i class="fa fa-arrow-circle-right"></i> Situación</a></li>');
                        }

                        else if (array[i] == "panel"){

                          $("#contenedor_panel").html('<a href="#"><i class="fa fa-bars"></i> <span>Panel de Control</span> <i class="fa fa-angle-left pull-right"></i></a> \
                          <ul class="treeview-menu"> \
                            <li><a href="ver_clientes.php"><i class="fa fa-arrow-circle-right"></i> Clientes</a></li> \
                            <li><a href="ver_variedades.php"><i class="fa fa-arrow-circle-right"></i> Variedades</a></li> \
                            <li><a href="ver_tipos.php"><i class="fa fa-arrow-circle-right"></i> Tipos</a></li> \
                            <li><a href="ver_subtipos.php"><i class="fa fa-arrow-circle-right"></i> Subtipos</a></li> \
                            <li><a href="crear_backup.php"><i class="fa fa-arrow-circle-right"></i> Copia de Seguridad</a></li> \
                          </ul>');

                        }

                      }

                    }

                  }else{

                     window.location.href = 'inicio.php';

                  }

      }

 });

 }