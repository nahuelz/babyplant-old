let current_date = new Date();
let current_weeknumber = null;

$(document).ready(function () {
  $("#guardar_btn").prop("disabled", true);
  current_weeknumber = getWeekNumber(current_date);
  setSumaBandejas(null);

  let hoy = new Date();
  let hoy2 = hoy.getDate() + "/" + hoy.getMonth() + "/" + hoy.getFullYear();
  let current =
    current_date.getDate() +
    "/" +
    current_date.getMonth() +
    "/" +
    current_date.getFullYear();
  if (current == hoy2) {
    $.ajax({
      beforeSend: function () {},
      url: "data_ver_plansiembra.php",
      type: "POST",
      data: { consulta: "check_anteriores" },
      success: function (x) {
        if (x.trim().length > 0) {
          swal("Atención", x, "info");
        }
      },
      error: function (jqXHR, estado, error) {},
    });
  } else {
    LoadPedidosFunc();
  }

  changeWeek(0)
  $("#header").html(getWeek(current_date, 1));

  $(function () {
    $.datepicker.setDefaults($.datepicker.regional["es"]);

    $("#fechasiembra_txt,#fechasiembramulti_txt")
      .datepicker({
        dateFormat: "dd/mm/yy",
        autoclose: true,
        disableTouchKeyboard: true,
        Readonly: true,
      })
      .attr("readonly", "readonly");

    $("#currentfecha_txt")
      .datepicker({
        dateFormat: "dd/mm/yy",
        autoclose: true,
        disableTouchKeyboard: true,
        Readonly: true,
        onSelect: function (dateText, inst) {
          let date = $(this).val();
          let datesplit = date.split("/");
          let fecha = new Date(
            datesplit[2] + "/" + datesplit[1] + "/" + datesplit[0]
          );
          changeWeek(fecha);
        },
      })
      .attr("readonly", "readonly");

    $("#fechasiembra_txt").datepicker("update");
  });

  $("#select_tiposeleccion").on(
    "changed.bs.select",
    function (e, clickedIndex, newValue, oldValue) {
      setSumaBandejas($("#select_tiposeleccion").val().toString());
    }
  );

  $("#cantidad_bandejas_reales").on("keydown", function (e) {
    if (e.keyCode === 13) {
      GuardarCambioEstado();
    }
  });
});

function getWeekNumber(date) {
  let onejan = new Date(date.getFullYear(), 0, 1);
  week = Math.ceil(((date - onejan) / 86400000 + onejan.getDay() + 1) / 7);
  return week;
}

function getWeek(d, tipo) {
  let day = d.getDay();
  let html = "";
  let dias = [
    "Domingo",
    "Lunes",
    "Martes",
    "Miércoles",
    "Jueves",
    "Viernes",
    "Sábado",
  ];

  let dd = d.getDate();
  let mm = d.getMonth() + 1; //January is 0!
  let yyyy = d.getFullYear();
  if (dd < 10) {
    dd = "0" + dd;
  }
  if (mm < 10) {
    mm = "0" + mm;
  }
  let today = dias[day] + " " + dd + "/" + mm + "/" + yyyy;
  html = today;
  if (tipo == 1) {
    return html;
  } else if (tipo == 2) {
    return dd + "/" + mm + "/" + yyyy;
  }
}

function changeWeek(tipo) {
  if (tipo == 1) {
    current_date.setDate(current_date.getDate() + 1);
  } else if (tipo == -1) {
    current_date.setDate(current_date.getDate() - 1);
  } else if (tipo == 0) {
    current_date = new Date();
  } else {
    current_date = tipo;
  }
  $("#header").html(getWeek(current_date, 1));
  loadPedidos();
  setSumaBandejas(null);
}

function loadPedidos() {
  LoadPedidosFunc();
}

function LoadPedidosFunc() {
  const fecha = getWeek(current_date, 2);
  $.ajax({
    url: "data_ver_plansiembra.php",
    type: "POST",
    data: { fecha: fecha, consulta: "cargar_ordenes" },
    success: function (x) {
      $("#tablitaa").find("tbody").html(x);
    },
    error: function (jqXHR, estado, error) {
      console.log(error);
    },
  });
}

function groupBy(objeto, prop) {
  return objeto.reduce(function (groups, item) {
    const val = item[prop];
    groups[val] = groups[val] || [];
    groups[val].push(item);
    return groups;
  }, {});
}

function setSumaBandejas(tipo) {
  if (tipo) {
    let seleccionados = document.getElementsByClassName("selected2");
    let selectreal = [];
    if (
      seleccionados != undefined &&
      seleccionados != null &&
      seleccionados.length > 0
    ) {
      $("#contenedor_porbandeja").html("");
      if (tipo == "0") {
        for (let i = 0; i < seleccionados.length; i++) {
          selectreal.push({
            tamanio: $(seleccionados[i]).find("td:eq(2)").text(),
            cantidad: $(seleccionados[i]).find("td:eq(4)").text(),
          });
        }
        let groupedMap = groupBy(selectreal, "tamanio");

        let keys = Object.keys(groupedMap);
        let texto = "";
        for (let i = 0; i < keys.length; i++) {
          //keys[i] for key
          //dictionary[keys[i]] for the value
          let selected = $(groupedMap[keys[i]]);
          let suma = 0;
          for (let j = 0; j < selected.length; j++) {
            suma += parseInt(selected[j]["cantidad"]);
          }
          texto +=
            `<span style='font-size:1.5em;color:blue'>Bandejas ` +
            keys[i] +
            `: </span><span style='margin-left:10px;font-weight:bold;font-size:1.7em;color:red'>` +
            suma.toString() +
            `</span>
             <button style='margin-left:30px;' class='btn btn-success' onclick='sembrarVarios("` +
            keys[i] +
            `", 0)' id='btn-busca'><i class='fa fa-leaf'></i> Sembrar</button><br>
             `;
        }
        $("#contenedor_porbandeja").html(texto);
      } else {
        for (let i = 0; i < seleccionados.length; i++) {
          selectreal.push({
            variedad: $(seleccionados[i]).find("td:eq(1)").text(),
            cantidad: $(seleccionados[i]).find("td:eq(4)").text(),
          });
        }
        let groupedMap = groupBy(selectreal, "variedad");

        let keys = Object.keys(groupedMap);
        let texto = "";
        for (let i = 0; i < keys.length; i++) {
          //keys[i] for key
          //dictionary[keys[i]] for the value
          let selected = $(groupedMap[keys[i]]);
          let suma = 0;
          for (let j = 0; j < selected.length; j++) {
            suma += parseInt(selected[j]["cantidad"]);
          }
          texto +=
            `<span style='font-size:1.5em;color:blue'>` +
            keys[i] +
            `: </span><span style='margin-left:10px;font-weight:bold;font-size:1.7em;color:red'>` +
            suma.toString() +
            `</span>
             <button style='margin-left:30px;' class='btn btn-success' onclick='sembrarVarios("` +
            keys[i] +
            `", 1)' id='btn-busca'><i class='fa fa-leaf'></i> Sembrar</button><br>
             `;
        }
        $("#contenedor_porbandeja").html(texto);
      }
    } else {
      $("#contenedor_porbandeja").html("No hay órdenes seleccionadas");
    }
  } else {
    $("#contenedor_porbandeja").html("No hay órdenes seleccionadas");
  }
}

function sembrarVarios(bandeja, tipo) {
  $("#tabla_sembrarvarios > tbody").html("");
  let seleccionados = document.getElementsByClassName("selected2");
  if (
    seleccionados &&
    seleccionados.length
  ) {
    for (let i = 0; i < seleccionados.length; i++) {
      if (
        (tipo == 0 &&
          bandeja == $(seleccionados[i]).find("td:eq(2)").text().trim()) ||
        (tipo == 1 &&
          bandeja == $(seleccionados[i]).find("td:eq(1)").text().trim())
      ) {
        $("#tabla_sembrarvarios > tbody").append(
          `<tr class="rowsiembra">
            <td id="orden2_` +
            $(seleccionados[i])
              .find("td:eq(0)")
              .attr("id")
              .replace("orden_", "") +
            `">` +
            $(seleccionados[i]).find("td:eq(0)").text() +
            `</td>
            <td>` +
            $(seleccionados[i]).find("td:eq(1)").text() +
            `</td>
            <td>` +
            $(seleccionados[i]).find("td:eq(2)").text() +
            `</td>
            <td>` +
            $(seleccionados[i]).find("td:eq(3)").text() +
            `</td>
            <td>` +
            $(seleccionados[i]).find("td:eq(8)").text() +
            `</td>
            <td style='font-size:1.5em;font-weight:bold' class="cantisiembraoriginal">` +
            $(seleccionados[i]).find("td:eq(4)").text() +
            `</td>
            <td><input type="text" value="` +
            $(seleccionados[i]).find("td:eq(4)").text() +
            `"style='font-size:1.5em;color:blue;font-weight:bold' class="form-control text-right cantisiembra numericOnly" ></td>
          </tr>`
        );
      }
    }
    let date = new Date();
    let day = date.getDate(),
      month = date.getMonth() + 1,
      hour = date.getHours(),
      min = date.getMinutes();

    month = (month < 10 ? "0" : "") + month;
    day = (day < 10 ? "0" : "") + day;
    hour = (hour < 10 ? "0" : "") + hour;
    min = (min < 10 ? "0" : "") + min;

    let displayTime = hour + ":" + min;

    document.getElementById("timesiembra").value = displayTime;

    $(".numeric-only").keypress(function (e) {
      if (String.fromCharCode(e.keyCode).match(/[^0-9]/g)) return false;
    });

    $("#ModalSembrarVarios").css({ display: "block" });
  }
}

function guardarSiembra() {
  let cantidades = document.getElementsByClassName("cantisiembra");
  let puede = true;
  $("#ModalSembrarVarios").css({ display: "none" });
  for (let i = 0; i < cantidades.length; i++) {
    let cantidad = $(cantidades[i]).val();
    if (
      cantidad == null ||
      cantidad.trim().length == 0 ||
      isNaN(cantidad.trim()) ||
      parseInt(cantidad) < 1
    ) {
      puede = false;
      swal("Ingresá cantidades numéricas!", "", "error");
      $("#ModalSembrarVarios").css({ display: "block" });
      break;
    }
  }

  if (
    $("#timesiembra").val() == null ||
    $("#timesiembra").val().trim().length == 0
  ) {
    puede = false;
    swal("Ingresá una HORA válida!", "", "error");
    $("#ModalSembrarVarios").css({ display: "block" });
  }

  let fecha_siembra = $("#fechasiembramulti_txt")
    .datepicker({ dateFormat: "dd/mm/yy" })
    .val()
    .toString();

  if (fecha_siembra.length < 10 || fecha_siembra.includes("00/") == true) {
    puede = false;
    swal("Ingresá una FECHA válida!", "", "error");
    $("#ModalSembrarVarios").css({ display: "block" });
  }

  if (puede == true) {
    let aux = fecha_siembra.split("/");
    fecha_siembra = aux[2] + "-" + aux[1] + "-" + aux[0];
    let hora_siembra = $("#timesiembra").val().toString();
    let fecha = fecha_siembra + " " + hora_siembra + ":00";
    let rows = document.getElementsByClassName("rowsiembra");
    let listasiembra = [];
    for (let i = 0; i < rows.length; i++) {
      listasiembra.push({
        idart: $(rows[i]).find("td:eq(0)").attr("id").replace("orden2_", ""),
        cantidad: $(rows[i]).find(".cantisiembra").val(),
      });
    }
    listasiembra = JSON.stringify(listasiembra);

    $.ajax({
      beforeSend: function () {},
      url: "data_ver_plansiembra.php",
      type: "POST",
      data: {
        fecha: fecha,
        jsonarray: listasiembra,
        consulta: "sembrar_varios",
      },
      success: function (x) {
        if (x.trim() == "success"){
          loadPedidos();
          setSumaBandejas(null);
          swal("Los cambios se guardaron correctamente!", "", "success");
        }
        else{
          swal("Ocurrió un error al sembrar las Órdenes", x, "error");
        }
      },
      error: function (jqXHR, estado, error) {
        swal("Ocurrió un error al sembrar las Órdenes", error, "error");
      },
    });
  }
}

function cambiarEstadoSiembra(id) {
  const indice2 = $("#" + id)
    .closest("tr")
    .index();
  const cod_sobre = $("#tablitaa")
    .find("tr:eq(" + (parseInt(indice2) + 1).toString() + ") td:eq(7)")
    .text();
  const estado = $("#tablitaa")
    .find("tr:eq(" + (parseInt(indice2) + 1).toString() + ") td:eq(9)")
    .text();
  const bandejas = $("#tablitaa")
    .find("tr:eq(" + (parseInt(indice2) + 1).toString() + ") td:eq(4)")
    .text();
  const hoy = new Date();

  hoy.setHours(0, 0, 0, 0);
  let currenti = current_date;
  currenti.setHours(0, 0, 0, 0);
  
  if (currenti > hoy) {
    swal(
      "ERROR",
      "No se puede cambiar el estado un producto planificado posterior al día de hoy",
      "error"
    );
  } else if (cod_sobre.includes("NO ASIGNADO")) {
    swal(
      "ERROR",
      "No sembrar un producto que no tiene Código de sobre!",
      "error"
    );
  } else if (estado.includes("SEMBRADO")) {
    swal("ERROR", "El producto ya fue sembrado!", "error");
  } else {
    $("#id_artpedidohide").html(id.replace("estado_", ""));
    let id_orden = $("#tablitaa")
      .find("tr:eq(" + (parseInt(indice2) + 1).toString() + ") td:eq(0)")
      .text();
    let date = new Date();
    (hour = date.getHours()), (min = date.getMinutes());

    hour = (hour < 10 ? "0" : "") + hour;

    min = (min < 10 ? "0" : "") + min;
    let displayTime = hour + ":" + min;
    document.getElementById("appt").value = displayTime;
    $("#num_orden2").html(id_orden);
    $("#cantidad_bandejas_reales").val(bandejas);
    $("#ModalCambiarEstado").modal("show")
  }
}

function CerrarModalCambio() {
  id_artpedido_orden = null;
  $("#ModalCambiarEstado").modal("hide")
}

function MostrarModalOrden(id, id_alternativa, id_artpedido) {
  let id_orden = id.replace("orden_", "");
  $("#id_artpedidohidesiembra").html(id_artpedido);

  $("#num_orden").html(id_alternativa);
  $.ajax({
    beforeSend: function () {},
    url: "data_ver_plansiembra.php",
    type: "POST",
    data: { id_orden: id_orden, consulta: "cargar_orden_especifica" },
    success: function (x) {
      $("#tabla_detallepedido > tbody").html(x);
    },
    error: function (jqXHR, estado, error) {
      $("#tabla_detallepedido").html(
        "Hubo un error al cargar la información del pedido"
      );
    },
  });
  $("#ModalVerOrden").modal("show");
}

function CerrarModalOrden() {
  $("#ModalVerOrden").modal("hide");
}

function printOrdenSiembra(tipo) {
  if (tipo == 1) {
    func_printOrdenSiembra();
    document.getElementById("ocultar").style.display = "none";
    document.getElementById("miVentana").style.display = "block";
  } else {
    document.getElementById("ocultar").style.display = "block";
    document.getElementById("miVentana").style.display = "none";
    setTimeout("confirmaEstado();", 500);
  }
}

function func_printOrdenSiembra() {
  $("#miVentana").html("");
  $("#miVentana").append("<h4>ORDEN DE SIEMBRA</h4>");
  $("#miVentana").append(document.getElementById("tablita").innerHTML);
  $("#miVentana").find("th:last").remove();
  $("#miVentana")
    .find("tr")
    .each(function () {
      $(this).find("td:eq(6)").html($("textarea#observaciones_txt").val());
      $(this).find("td:last").remove();
    });
  setTimeout("window.print();printOrdenSiembra(2, null);", 500);
}

function GuardarCambioEstado() {
  let id_artpedido = $("#id_artpedidohide").text();
  let fecha_siembra = $("#fechasiembra_txt")
    .datepicker({ dateFormat: "dd/mm/yy" })
    .val()
    .toString();

  if (fecha_siembra.length == 10 && fecha_siembra.includes("00/") == false) {
    let aux = fecha_siembra.split("/");
    fecha_siembra = aux[2] + "-" + aux[1] + "-" + aux[0];
    let hora_siembra = $("#appt").val().toString();
    let bandejas = $("#cantidad_bandejas_reales").val().trim();
    if (bandejas.length == 0 || isNaN(bandejas) || parseInt(bandejas) < 1) {
      swal(
        "ERROR",
        "Debes ingresar un número de bandejas mayor a cero",
        "error"
      );
    } else {
      if (hora_siembra.trim().length > 0) {
        CerrarModalCambio();
        let fecha = fecha_siembra + " " + hora_siembra + ":00";
        $.ajax({
          beforeSend: function () {},
          url: "data_ver_plansiembra.php",
          type: "POST",
          data: {
            fecha: fecha,
            id_artpedido: id_artpedido,
            bandejas: bandejas,
            consulta: "actualiza_a_siembra",
          },
          success: function (x) {
            if (x.trim() == "success"){
              loadPedidos();
              swal("Los cambios se guardaron correctamente!", "", "success");
            }
            else{
              swal("Ocurrió un error al guardar los cambios", x, "error")
            }
          },
          error: function (jqXHR, estado, error) {
            swal("Ocurrió un error al guardar los cambios", error, "error")
          },
        });
      } else {
        swal("Debes elegir una hora válida", "", "error");
      }
    }
  } else {
    swal("Debes elegir una fecha válida", "", "error");
  }
}

function ActivarText() {
  $("#observaciones_txt").prop("disabled", false).focus();
  $("#btn_guardarobs").prop("disabled", false);
}

function GuardarObservaciones() {
  let observaciones = $("#observaciones_txt").val().trim();
  let id_artpedido = $("#id_artpedidohidesiembra").text();
  
  $.ajax({
    beforeSend: function () {},
    url: "cargar_detalleorden.php",
    type: "POST",
    data: {
      consulta: "modificar_obsiembra",
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
        CerrarModalOrden();
        loadPedidos();
      }
      else{
        swal("Ocurrió un error al modificar las Observaciones", x, "error");
      }
    },
    error: function (jqXHR, estado, error) {
      alert(estado + " " + error);
    },
  });
}

function expande_busqueda() {
  let contenedor = $("#contenedor_porbandeja");
  if ($(contenedor).css("display") == "none") {
    $(contenedor).css({ display: "block" });
    $("#contenedor_tipo").css({ display: "block" });
  } else {
    $(contenedor).css({ display: "none" });
    $("#contenedor_tipo").css({ display: "none" });
  }
}

function toggleSelection(objeto) {
  let tr = $(objeto).parent();
  let estado = $(tr).find("td:last").text();
  if (estado == "PLANIFICADO") {
    if (tr.hasClass("selected2")) {
      tr.removeClass("selected2");
    } else {
      tr.addClass("selected2");
    }
    setSumaBandejas($("#select_tiposeleccion").val().toString());
  }
}
