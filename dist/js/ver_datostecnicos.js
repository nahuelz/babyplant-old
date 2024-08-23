function busca_entradas() {
  let fecha = $("#fi").val();
  let fechaf = $("#ff").val();
  let variedad = $("#busca_variedad").val().trim().toUpperCase();
  if (variedad.length == 0) variedad = null;
  else if (variedad.includes(",")) {
    variedad = variedad.replace(",", "|");
  }
  let estados = $("#select_estado").val();
  if (estados.length == 0) estados = null;
  else {
    estados = JSON.stringify(estados).replace("[", "(").replace("]", ")");
  }
  let revision = $("#busca_tiporevision").val();
  let solucion = $("#busca_tiposolucion").val();
  if (revision != null) revision = parseInt(revision);
  if (solucion != null) solucion = parseInt(solucion);
  let filtros = {
    variedad: variedad,
    estado: estados,
    revision: revision,
    solucion: solucion,
  };
  filtros = JSON.stringify(filtros);
  $.ajax({
    beforeSend: function () {
      $("#tabla_entradas").html(
        "<h4 style='margin-left:15px'>Buscando pedidos, espere...</h4>"
      );
    },
    url: "data_ver_datostecnicos.php",
    type: "POST",
    data: { fechai: fecha, fechaf: fechaf, filtros: filtros },
    success: function (x) {
      $("#tabla_entradas").html(x);
      $("#tabla").DataTable({
        order: [[1, "desc"]],
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
        "Ocurrió un error al cargar los datos: " + estado + " " + error
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
  let direccion =
    "<div align='center'><img src='dist/img/babyplant.png' width='120' height='50'></img><h4>BabyPlant</h4>";
  $("#miVentana").html(direccion);
  $("#miVentana").append(document.getElementById("tabla").outerHTML);
  $("#miVentana")
    .find("tr,td,th")
    .css({ "font-size": "9px", "word-wrap": "break-word" });
  let haymesada = false;
  $("#miVentana")
    .find("tr")
    .each(function () {
      $(this).find("td:eq(7)").css({ "font-size": "7px" });
      if ($(this).find("td:eq(8)").text().trim().length > 0) {
        haymesada = true;
      }
    });
  if (!haymesada) {
    $("#miVentana").find("th:eq(8)").remove();
    $("#miVentana")
      .find("tr")
      .each(function () {
        $(this).find("td:eq(8)").remove();
      });
  }
  setTimeout("window.print();print_Busqueda(2)", 500);
}

function expande_busqueda() {
  let contenedor = $("#contenedor_busqueda");
  if ($(contenedor).css("display") == "none")
    $(contenedor).css({ display: "block" });
  else {
    $(contenedor).css({ display: "none" });
    $("#select_estado,#busca_tiporevision,#busca_tiposolucion")
      .val("default")
      .selectpicker("refresh");
    $("#busca_variedad").val("");
  }
}

function quitar_filtros() {
  $("#select_estado,#busca_tiporevision,#busca_tiposolucion")
    .val("default")
    .selectpicker("refresh");
  $("#busca_variedad").val("");
  busca_entradas();
}