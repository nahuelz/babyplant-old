function pone_pedidos() {
  $.ajax({
    url: "pone_boxes.php",
    type: "POST",
    data: { consulta: "pone_box_pedidos" },
    success: function (x) {
      $(".col-pedidos").html(x).removeClass("d-none");
      $("#contenedor_modulos").append(
        '<li><a href="cargar_pedido.php"><i class="fa fa-arrow-circle-right"></i> Agregar Pedido</a></li><li><a href="ver_pedidos.php"><i class="fa fa-arrow-circle-right"></i> Ver Pedidos</a></li>'
      );
    },
  });
}

function pone_agregar_pedidos() {
  $(".col-agregar-pedidos")
    .html(
      `
    <a href='cargar_pedido.php' style="text-decoration:none;">
                    <div class="small-box bg-red">
                      <div class="inner">
                        <img src='dist/img/addpedido.png' width='38px' height='38px'>
                          <p style="margin-top:18px">Agregar Pedido</p>
                        </div>
                        <div class="icon">
                          <i class="fa fa-add"></i>
                        </div>
                        <span class="small-box-footer"><i class="fa fa-arrow-circle-right"></i></span>
                      </div>
  </a>
    `
    )
    .removeClass("d-none");
}

function pone_stock() {
  $.ajax({
    url: "pone_boxes.php",
    type: "POST",
    data: { consulta: "pone_box_stock" },
    success: function (x) {
      $(".col-stock").html(x).removeClass("d-none");
      $("#contenedor_modulos").append(
        '<li><a href="ver_stock.php"><i class="fa fa-arrow-circle-right"></i> Stock</a></li>'
      );
    },
  });
}

function pone_ordenes_siembra() {
  $.ajax({
    url: "pone_boxes.php",
    type: "POST",
    data: { consulta: "pone_box_ordenes" },
    success: function (x) {
      $(".col-ordenes-siembra").html(x).removeClass("d-none");
      $("#contenedor_modulos").append(
        '<li><a href="ver_ordenes_siembra.php"><i class="fa fa-arrow-circle-right"></i> Órdenes Siembra</a></li>'
      );
    },
    error: function (jqXHR, estado, error) {},
  });
}

function pone_historialentregas() {
  $.ajax({
    url: "pone_boxes.php",
    type: "POST",
    data: { consulta: "pone_box_historial" },
    success: function (x) {
      $(".col-historial-entregas").html(x).removeClass("d-none");
      $("#contenedor_modulos").append(
        '<li><a href="ver_historial_entregas.php"><i class="fa fa-arrow-circle-right"></i> Historial de Entregas</a></li>'
      );
    },
    error: function (jqXHR, estado, error) {},
  });
}

function pone_planificacionpedidos() {
  $.ajax({
    url: "pone_boxes.php",
    type: "POST",
    data: { consulta: "pone_box_planificacion" },
    success: function (x) {
      $(".col-planificacion").html(x).removeClass("d-none");
      $("#contenedor_modulos").append(
        '<li><a href="ver_planificacion.php"><i class="fa fa-arrow-circle-right"></i> Planificación de Pedidos</a></li>'
      );
    },
  });
}

function pone_siembra() {
  $.ajax({
    url: "pone_boxes.php",
    type: "POST",
    data: { consulta: "pone_box_siembra" },
    success: function (x) {
      $(".col-siembra").html(x).removeClass("d-none");
      $("#contenedor_modulos").append(
        '<li><a href="ver_plansiembra.php"><i class="fa fa-arrow-circle-right"></i> Siembra</a></li>'
      );
    },
  });
}

function pone_remitos() {
  $.ajax({
    url: "pone_boxes.php",
    type: "POST",
    data: { consulta: "pone_box_remitos" },
    success: function (x) {
      $(".col-remitos").html(x).removeClass("d-none");
      $("#contenedor_modulos").append(
        '<li><a href="ver_remitos.php"><i class="fa fa-arrow-circle-right"></i> Remitos</a></li>'
      );
    },
  });
}

function pone_problemas() {
  $.ajax({
    url: "pone_boxes.php",
    type: "POST",
    data: { consulta: "pone_box_problemas" },
    success: function (x) {
      $(".col-problemas").html(x).removeClass("d-none");
      $("#contenedor_modulos").append(
        '<li><a href="ver_problemas.php"><i class="fa fa-arrow-circle-right"></i> Problemas</a></li>'
      );
    },
  });
}

function pone_camara() {
  $.ajax({
    url: "pone_boxes.php",
    type: "POST",
    data: { consulta: "pone_box_camara" },
    success: function (x) {
      $(".col-camara").html(x).removeClass("d-none");
      $("#contenedor_modulos").append(
        '<li><a href="ver_camara.php"><i class="fa fa-arrow-circle-right"></i> Cámara</a></li>'
      );
    },
  });
}

function pone_mesadas() {
  $.ajax({
    url: "pone_boxes.php",
    type: "POST",
    data: { consulta: "pone_box_mesadas" },
    success: function (x) {
      $(".col-mesadas").html(x).removeClass("d-none");
      $("#contenedor_modulos").append(
        '<li><a href="ver_mesadas.php"><i class="fa fa-arrow-circle-right"></i> Mesadas</a></li>'
      );
    },
  });
}

function pone_planentregas() {
  $.ajax({
    url: "pone_boxes.php",
    type: "POST",
    data: { consulta: "pone_box_planentregas" },
    success: function (x) {
      $(".col-planificacion-entregas").html(x).removeClass("d-none");
      $("#contenedor_modulos").append(
        '<li><a href="ver_planentregas.php"><i class="fa fa-arrow-circle-right"></i> Planificación Entregas</a></li>'
      );
    },
  });
}

function pone_ordenesentrega() {
  $.ajax({
    url: "pone_boxes.php",
    type: "POST",
    data: { consulta: "pone_box_agenda" },
    success: function (x) {
      $(".col-agenda-entregas").html(x).removeClass("d-none");
      $("#contenedor_modulos").append(
        '<li><a href="ver_agenda_entregas.php"><i class="fa fa-arrow-circle-right"></i> Agenda de Entregas</a></li>'
      );
    },
  });
}

function pone_datostecnicos() {
  $(".col-datos-tecnicos")
    .html(
      `
            <a href="ver_datostecnicos.php">
              <div class="small-box" style="background-color:#DA81F5"> 
                <div class="inner"  style="height:7.3em;">  
                  <p style='color:white'>Datos Técnicos</p>
                </div>
                <div class="icon">
                  <i class="fa fa-cogs"></i>
                </div>
                <span class="small-box-footer" style="background-color:rgba(0, 0, 0, 0.1);">Ver Datos <i class="fa fa-arrow-circle-right"></i></span>
              </div>
              </a>  
        `
    )
    .removeClass("d-none");
  $("#contenedor_modulos").append(
    '<li><a href="ver_datostecnicos.php"><i class="fa fa-arrow-circle-right"></i> Datos Técnicos</a></li>'
  );
}

function pone_situacion() {
  $(".col-situacion")
    .html(
      `
              <!-- small box -->
              <a href="ver_situacion.php">
              <div class="small-box" style="background-color:#1C1C1C"> 
                <div class="inner"  style="height:7.3em;">
                  
                  <p style='color:white'>Situación Clientes</p>
                </div>
                <div class="icon">
                  <i style="color:grey" class="fa fa-users"></i>
                </div>
                <span class="small-box-footer" style="background-color:rgba(0, 0, 0, 0.1);">Ver Situación <i class="fa fa-arrow-circle-right"></i></span>
              </div>
              </a>
      `
    )
    .removeClass("d-none");
  $("#contenedor_modulos").append(
    '<li><a href="ver_situacion.php"><i class="fa fa-arrow-circle-right"></i> Situación</a></li>'
  );
}

function pone_estadisticas() {
  $(".col-estadisticas")
    .html(
      `
              <a href="ver_estadisticas.php">
              <div class="small-box" style="background-color:#ffd700"> 
                <div class="inner"  style="height:7.3em;">
                  
                  <p style='color:black'>Estadísticas</p>
                </div>
                <div class="icon">
                  <i style="color:rgba(0, 0, 0, 0.15);" class="fa fa-line-chart"></i>
                </div>
                <span class="small-box-footer" style="background-color:rgba(0, 0, 0, 0.1);">Ver Estadísticas <i class="fa fa-arrow-circle-right"></i></span>
              </div>
              </a>

      `
    )
    .removeClass("d-none");
  $("#contenedor_modulos").append(
    '<li><a href="ver_estadisticas.php"><i class="fa fa-arrow-circle-right"></i> Estadísticas</a></li>'
  );
}
