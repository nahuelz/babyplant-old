/*
MENSUAL O ANUAL
CUANTO TOMATE ELPIDA TENGO SEMBRADO
CUANTO ENTREGAMOS EN TAL PERIODO
CUANTO TENEMOS PEDIDO
CUANTO SEMBRÓ MARINELLI, 
CUANTO SE ENTREGÓ
CANTIDAD DE PLANTAS
CUANTO QUEDA POR SEMBRAR
*/
let fechaInicio = null;
let fechaFinal = null;
let filtros = null;

$(function () {
  $("#daterange-btn").daterangepicker(
    {
      ranges: {
        "Semana pasada": [
          moment().startOf("isoWeek").subtract(7, "days"),
          moment().startOf("isoWeek").subtract(1, "days"),
        ],
        "Los ultimos 7 dias": [moment().subtract(6, "days"), moment()],
        "Los ultimos 30 dias": [moment().subtract(29, "days"), moment()],
        "Los ultimos 3 meses": [moment().subtract(90, "days"), moment()],
        "Este mes": [moment().startOf("month"), moment().endOf("month")],
        "El mes pasado": [
          moment().subtract(1, "month").startOf("month"),
          moment().subtract(1, "month").endOf("month"),
        ],
        "Todo el año": [moment().startOf("year"), moment()],
        "El año pasado": [
          moment().subtract(1, "year").startOf("year"),
          moment().subtract(1, "year").endOf("year"),
        ],
      },
      startDate: moment().subtract(90, "days"),
      endDate: moment(),
    },
    function (start, end) {
      $(".fe").html(
        start.format("DD/MM/YYYY") + " - " + end.format("DD/MM/YYYY")
      );
      let xstart = start.format("YYYY-MM-DD");
      let xend = end.format("YYYY-MM-DD");
      $("#fi").val(xstart);
      $("#ff").val(xend);
    }
  );
});

function busca_entradas() {
  fechaInicio = $("#fi").val();
  fechaFinal = $("#ff").val();
  let tipos = $("#select_tipo").val();
  if (tipos == null || tipos.length == 0) tipos = null;

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

  let cliente = $("#select_cliente option:selected").val();

  filtros = {
    tipo: tipos,
    subtipo: subtipos,
    variedad: variedad,
    cliente: cliente,
  };

  filtros = JSON.stringify(filtros);
  $.ajax({
    beforeSend: function () {
      $("#tabla_entradas").html("<h4 class='ml-1'>Buscando, espere...</h4>");
    },
    url: "busca_estadisticas.php",
    type: "POST",
    data: {
      fechai: fechaInicio,
      fechaf: fechaFinal,
      filtros: filtros,
      tipoconsulta: fechaFinal.trim().length > 0 ? "periodos" : "actual",
    },
    success: function (x) {
      $("#tabla_entradas").html(x);
      if (fechaInicio.trim().length > 0 && fechaFinal.trim().length > 0) {
        cargarChart("sembradas");
      }
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

function monthDiff(d1, d2) {
  let months;
  months = (d2.getFullYear() - d1.getFullYear()) * 12;
  months -= d1.getMonth();
  months += d2.getMonth();
  return months <= 0 ? 0 : months;
}

function cargarChart(tipo) {
  let arr = fechaInicio.split("-");
  let fechainicio = new Date(arr[0], arr[1] - 1, arr[2]);

  let arr2 = fechaFinal.split("-");
  let fechafinal = new Date(arr2[0], arr2[1] - 1, arr2[2]);
  const meses = [
    "Enero",
    "Febrero",
    "Marzo",
    "Abril",
    "Mayo",
    "Junio",
    "Julio",
    "Agosto",
    "Septiembre",
    "Octubre",
    "Noviembre",
    "Diciembre",
  ];
  let dif = monthDiff(fechainicio, fechafinal) + 1;
  let listameses = [];
  let listafechas = [];

  let start = moment(fechaInicio);
  let end = moment(fechaFinal);

  if (dif == 1) {
    listameses = [meses[arr2[1] - 1]];
    listafechas.push([fechaInicio, fechaFinal]);
  } else if (dif == 2) {
    listameses = [meses[arr[1] - 1], meses[arr2[1] - 1]];
    let fin = start.endOf("month");
    listafechas.push([fechaInicio, fin.format("YYYY-MM-DD")]);
    let fin2 = end.startOf("month");
    listafechas.push([fin2.format("YYYY-MM-DD"), fechaFinal]);
  } else if (dif > 2) {
    for (let i = 1; i <= dif; i++) {
      if (i == 1) {
        let fin = start.clone().endOf("month");
        listafechas.push([fechaInicio, fin.format("YYYY-MM-DD")]);
      } else if (i == dif) {
        let fin2 = end.clone().startOf("month");
        listafechas.push([fin2.format("YYYY-MM-DD"), fechaFinal]);
      } else {
        let start2 = start.clone().startOf("month");
        let end2 = start.clone().endOf("month");
        listafechas.push([
          start2.format("YYYY-MM-DD"),
          end2.format("YYYY-MM-DD"),
        ]);
      }
      listameses.push(meses[start.month()]);
      start.add(1, "month");
    }
  }

  $.ajax({
    beforeSend: function () {
      $(".chart-container").html(`<canvas id="myChart"></canvas>`);
    },
    url: "busca_estadisticas.php",
    type: "POST",
    data: {
      fechai: fechaInicio,
      fechaf: fechaFinal,
      filtros: filtros,
      tipoconsulta: "grafico",
      listafechas: JSON.stringify(listafechas),
      tipo,
    },
    success: function (x) {
      console.log(x);
      if (x.includes("error")) {
        swal("Ocurrió un error al cargar el gráfico", "", "error");
      } else {
        const dataset = JSON.parse(x);
        let ctx = document.getElementById("myChart").getContext("2d");
        let myChart = new Chart(ctx, {
          type: "bar",
          data: {
            labels: listameses,
            datasets: [
              {
                label:
                  tipo == "sembradas"
                    ? "Bandejas Sembradas"
                    : tipo == "entregadas"
                    ? "Bandejas Entregadas"
                    : "Bandejas Pedidas",
                data: dataset,
                backgroundColor: [
                  tipo == "sembradas"
                    ? "rgba(0, 204, 0, 0.2)"
                    : tipo == "entregadas"
                    ? "rgba(54, 162, 235, 0.2)"
                    : "rgba(255, 195, 0, 0.4)",
                ],
                borderColor: [
                  tipo == "sembradas"
                    ? "rgba(0, 204, 0, 1)"
                    : tipo == "entregadas"
                    ? "rgba(54, 162, 235, 1)"
                    : "rgba(255, 195, 0, 1)",
                ],
                borderWidth: 1,
              },
            ],
          },
          options: {
            scales: {
              y: {
                beginAtZero: true,
              },
            },
          },
        });
      }
    },
    error: function (jqXHR, estado, error) {},
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
  //let direccion = "<div align='center'><img src='dist/img/babyplant.png' width='120' height='50'></img><h4>BabyPlant</h4>";

  let brands = $("#select_tipo option:selected");
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

  /*
        $("#miVentana").find("th:eq(7)").remove();
        */
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

  //$("#miVentana").find("th:eq(9)").remove();
  //  $("#miVentana").find("tr").each(function(){
  //    $(this).find("td:eq(9)").remove();
  //  });

  setTimeout("window.print();print_Busqueda(2)", 500);
}

function quitar_filtros() {
  location.reload();
}

function pone_tipos() {
  $.ajax({
    beforeSend: function () {
      $("#select_tipo").html("Cargando productos...");
    },
    url: "pone_tiposdeproducto.php",
    type: "POST",
    data: { tipo: "pone_tiposdeproducto" },
    success: function (x) {
      $(".selectpicker").selectpicker();
      $("#select_tipo").html(x).selectpicker("refresh");
      $("#select_tipo").on(
        "changed.bs.select",
        function (e, clickedIndex, newValue, oldValue) {}
      );
    },
    error: function (jqXHR, estado, error) {},
  });
}

///NUEVO ESTADISTICAS

function pone_clientes() {
  $.ajax({
    beforeSend: function () {
      $("#select_cliente").html("Cargando lista de clientes...");
    },
    url: "pone_clientes.php",
    type: "POST",
    data: null,
    success: function (x) {
      if (
        /Android|webOS|iPhone|iPad|iPod|BlackBerry/i.test(navigator.userAgent)
      ) {
        $(".selectpicker").selectpicker("mobile");
      } else {
        let elements = document.querySelectorAll(".mobile-device");
        for (let i = 0; i < elements.length; i++) {
          elements[i].classList.remove("mobile-device");
        }
        $(".selectpicker").selectpicker({});
      }

      $("#select_cliente").html(x).selectpicker("refresh");
      $("#select_cliente").on(
        "changed.bs.select",
        function (e, clickedIndex, newValue, oldValue) {
          let id_cliente = $("#select_cliente").find("option:selected").val();
        }
      );
      busca_entradas();
    },
    error: function (jqXHR, estado, error) {},
  });
}
