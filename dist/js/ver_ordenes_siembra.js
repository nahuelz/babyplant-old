function busca_entradas() {
  let fecha = $("#fi").val();
  let fechaf = $("#ff").val();
  let tipos = $("#select_tipo_filtro").val();
  if (tipos.length == 0) tipos = null;
  else {
    tipos = JSON.stringify(tipos).replace("[", "(").replace("]", ")");
  }

  let subtipos = $("#busca_subtipo").val().trim().toUpperCase();
  if (subtipos.length == 0) subtipos = null;
  else if (subtipos.includes(",")) {
    subtipos = subtipos.replace(",", "|");
  }

  let variedad = $("#busca_variedad").val().trim().toUpperCase();
  if (variedad.length == 0) variedad = null;
  else if (variedad.includes(",")) {
    variedad = variedad.replace(",", "|");
  }

  let cliente = $("#busca_cliente").val().trim().toUpperCase();
  if (cliente.length == 0) cliente = null;
  else if (cliente.includes(",")) {
    cliente = cliente.replace(",", "|");
  }

  let estados = $("#select_estado").val();
  if (estados.length == 0) estados = null;
  else {
    estados = JSON.stringify(estados).replace("[", "(").replace("]", ")");
  }

  let revision = $("#busca_tiporevision").val();
  let solucion = $("#busca_tiposolucion").val();

  let tipofecha = $("#select_tipofecha").val();
  if (tipofecha != null) tipofecha = parseInt(tipofecha);

  if (revision != null) revision = parseInt(revision);
  if (solucion != null) solucion = parseInt(solucion);

  let filtros = {
    tipo: tipos,
    subtipo: subtipos,
    variedad: variedad,
    cliente: cliente,
    estado: estados,
    revision: revision,
    solucion: solucion,
    tipofecha: tipofecha,
  };

  filtros = JSON.stringify(filtros);

  $.ajax({
    beforeSend: function () {
      $("#tabla_entradas").html(
        "<h4 class='ml-1'>Buscando órdenes, espere...</h4>"
      );
    },
    url: "data_ver_ordenes_siembra.php",
    type: "POST",
    data: { fechai: fecha, fechaf: fechaf, filtros: filtros },
    success: function (x) {
      $("#tabla_entradas").html(x);
      $("#tabla").DataTable({
        pageLength: 50,
        order: [[0, "desc"]],
        columnDefs: [
          { width: 300, targets: 0 }
        ],
        language: {
          lengthMenu: "Mostrando _MENU_ pedidos por página",
          zeroRecords: "No hay pedidos",
          info: "Página _PAGE_ de _PAGES_",
          infoEmpty: "No hay pedidos",
          infoFiltered: "(filtrado de _MAX_ pedidos en total)",
          lengthMenu: "Mostrar _MENU_ pedidos",
          loadingRecords: "Cargando...",
          processing: "Procesando...",
          search: "Buscar:",
          zeroRecords: "No se encontraron resultados",
          paginate: {
            first: "Primera",
            last: "Última",
            next: "Siguiente",
            previous: "Anterior",
          },
          aria: {
            sortAscending: ": tocá para ordenar en modo ascendente",
            sortDescending: ": tocá para ordenar en modo descendente",
          },
        },
      });
    },
    error: function (jqXHR, estado, error) {
      $("#tabla_entradas").html(
        "Ocurrió un error: contactá al desarrollador" +
          "     " +
          estado +
          " " +
          error
      );
    },
  });
}

function print_Busqueda(tipo) {
  if (tipo == 1) {
    func_printBusqueda();
    document.getElementById("ocultar").style.display = "none";
    document.getElementById("miVentana").style.display = "block";
  } else {
    document.getElementById("ocultar").style.display = "block";
    document.getElementById("miVentana").style.display = "none";
    $("#miVentana").html("");
  }
}

function func_printBusqueda() {
  let brands = $("#select_tipo_filtro option:selected");
  let selected = "";
  $(brands).each(function (index, brand) {
    selected = selected + $(this).text() + ", ";
  });

  if (selected.length > 0) {
    selected = selected.substring(0, selected.lastIndexOf(", "));
  }

  let fechas = $(".fe").text();

  $("#miVentana").html(
    "<h3>ORDENES DE SIEMBRA</h3><h4>Productos: " + selected + "</h4"
  );

  if (fechas != undefined && fechas.length > 0) {
    $("#miVentana").append("<h4>Período: " + fechas + "</h4>");
  }

  $("#miVentana").append(document.getElementById("tabla").outerHTML);

  $("#miVentana")
    .find("tr,td,th")
    .css({ "font-size": "11px", "word-wrap": "break-word" });

  let haymesada = false;
  $("#miVentana")
    .find("tr")
    .each(function () {
      $(this).find("td:eq(0)").css({ "font-size": "14px" });
      $(this).find("td:eq(1)").css({ "font-size": "14px" });
      $(this).find("td:eq(3)").css({ "font-size": "16px" });
      $(this).find("td:eq(4)").css({ "font-size": "14px" });
      $(this).find("td:eq(7)").css({ "font-size": "14px" });
      $(this)
        .find("td:eq(9)")
        .css({ "font-size": "16px", "font-weight": "bold" });

      let estado = $(this).find("td:eq(8)");
      let estadito = $(estado).text();
      $(estado).css({ "font-size": "12px" });
      $(estado).html(estadito);

      if ($(this).find("td:eq(9)").text().trim().length > 0) {
        haymesada = true;
      }
    });

  if (!haymesada) {
    $("#miVentana").find("th:eq(9)").remove();
    $("#miVentana")
      .find("tr")
      .each(function () {
        $(this).find("td:eq(9)").remove();
      });
  }
  setTimeout("window.print();print_Busqueda(2)", 500);
}

function modalOrdenSiembra(id) {
  $("#ModalVerEstado").modal("show");
  $.ajax({
    beforeSend: function () {},
    url: "cargar_detalleorden.php",
    type: "POST",
    data: { id: id, consulta: "pedido" },
    success: function (x) {
      $("#box_info").html(x);
      $("#ModalVerEstado").attr("x-id-artpedido",id);
    },
    error: function (jqXHR, estado, error) {
      $("#box_info").html("Hubo un error al cargar la información del pedido");
    },
  });
}

function ActivarText() {
  $("#observacionesproduccion_txt").prop("disabled", false).focus();
  $("#btn_guardarobs").prop("disabled", false);
}

function GuardarObservaciones() {
  let observaciones = $("#observacionesproduccion_txt").val().trim();
  let id_artpedido = $("#id_artpedidohide").text();
  $.ajax({
    beforeSend: function () {},
    url: "cargar_detalleorden.php",
    type: "POST",
    data: {
      consulta: "modificar_observaciones",
      id_artpedido: id_artpedido,
      observaciones: observaciones,
    },
    success: function (x) {
      if (x.trim() == "success"){
        swal(
          "Las observaciones fueron modificadas correctamente!",
          "",
          "success"
        );
        modalOrdenSiembra(id_artpedido);
      }
      else{
        swal("Ocurrió un error al guardar las observaciones", x, "error")
      }
    },
    error: function (jqXHR, estado, error) {
      swal("Ocurrió un error al guardar las observaciones", x, "error")
    },
  });
}
