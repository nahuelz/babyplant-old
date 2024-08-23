let current_date = new Date();
let original_weeknumber = getWeekNumber(current_date);
let current_weeknumber = original_weeknumber;
let listaseleccionados = [];
let listacolumnas = [];
let id_articulopedido = null;
let estadoactual = null;
let current_date2 = new Date();
let original_weeknumber2 = getWeekNumber2(current_date2);
let current_weeknumber2 = original_weeknumber2;

$(document).ready(() => {
  document.getElementById("defaultOpen").click();
  $("#guardar_btn").prop("disabled", true);
  $("#num_semana").html("Semana Nº " + current_weeknumber);
  $("#guardar_btn").prop("disabled", true);
  $("#num_semana").html("Semana Nº " + current_weeknumber2);
  changeWeek(0);
  $.datepicker.setDefaults($.datepicker.regional["es"]);
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
});

function openTab(evt, tabName) {
  let i, tabcontent, tablinks;
  // Get all elements with class="tabcontent" and hide them
  tabcontent = document.getElementsByClassName("tabcontent");
  for (i = 0; i < tabcontent.length; i++) {
    tabcontent[i].style.display = "none";
  }
  tablinks = document.getElementsByClassName("tablinks");
  for (i = 0; i < tablinks.length; i++) {
    tablinks[i].className = tablinks[i].className.replace(" active", "");
  }
  document.getElementById(tabName).style.display = "block";
  evt.currentTarget.className += " active";
}

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
  listaseleccionados = [];
  listacolumnas = [];
  let html2 = "";
  $("#TABLA").find("tbody").html(html2);
  $("#header").html(getWeek(current_date, 1));
  loadPedidos();
}

function loadPedidos() {
  $("#fechasiembra_txt,#fechacamara_txt")
    .datepicker({
      dateFormat: "dd/mm/yy",
      autoclose: true,
      disableTouchKeyboard: true,
      Readonly: true,
    })
    .attr("readonly", "readonly");
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
      url: "carga_pedidos_camara.php",
      type: "POST",
      data: { consulta: "check_anteriores" },
      success: function (x) {
        LoadPedidosFunc();
      },
      error: function (jqXHR, estado, error) {},
    });
  } else {
    LoadPedidosFunc();
  }
}

function LoadPedidosFunc() {
  let fecha = getWeek(current_date, 2);
  $.ajax({
    beforeSend: function () {},
    url: "carga_pedidos_camara.php",
    type: "POST",
    data: { fecha: fecha, consulta: "cargar_ordenes" },
    success: function (x) {
      $("#tablitaa").find("tbody").html(x);
    },
    error: function (jqXHR, estado, error) {},
  });
}

function addToLista(id) {
  if (document.getElementById(id).checked) {
    listaseleccionados.push(id.replace("cbox_", ""));

    listacolumnas.push(
      $("#" + id)
        .closest("td")
        .index()
    );
  } else {
    let index = listaseleccionados.indexOf(id.replace("cbox_", ""));
    if (index > -1) {
      listacolumnas.splice(index, 1);
    }
    listaseleccionados = listaseleccionados.filter(
      (e) => e !== id.replace("cbox_", "")
    );
  }
}

function MostrarModalEstado(id) {
  id_articulopedido = id;
  $.ajax({
    beforeSend: function () {},
    url: "data_ver_camara.php",
    type: "POST",
    data: { id: id.replace("id_", ""), consulta: "cliente" },
    success: function (x) {
      $("#nombre_cliente").html(x);
    },
    error: function (jqXHR, estado, error) {
      swal("ERROR, intentá nuevamente", "", "error");
    },
  });

  $.ajax({
    beforeSend: function () {},
    url: "data_ver_camara.php",
    type: "POST",
    data: { id: id.replace("id_", ""), consulta: "pedido" },
    success: function (x) {
      $("#box_info").html(x);
      $("#ModalVerEstado").css({ display: "block" });
    },
    error: function (jqXHR, estado, error) {
      swal("ERROR, intentá nuevamente", "", "error");
    },
  });
}
function cambiarEstadoSiembra(id) {
  let estado = $("#" + id).text();
  let hoy = new Date();
  hoy.setHours(0, 0, 0, 0);
  let currenti = current_date;
  currenti.setHours(0, 0, 0, 0);

  if (currenti > hoy) {
    swal(
      "No se puede cambiar el estado un producto posterior al día de hoy",
      "",
      "error"
    );
  } else if (estado.includes("EN CÁMARA")) {
    swal("El producto ya se encuentra en la cámara!", "", "error");
  } else {
    $("#id_artpedidohide").html(id.replace("estado_", ""));
    let id_orden = $("#" + id)
      .closest("tr")
      .find("td:eq(0)")
      .text();
    let date = new Date();
    (hour = date.getHours()), (min = date.getMinutes());
    hour = (hour < 10 ? "0" : "") + hour;
    min = (min < 10 ? "0" : "") + min;

    let displayTime = hour + ":" + min;
    document.getElementById("appt").value = displayTime;
    $("#num_orden2").html(id_orden);
    $("#ModalCambiarEstado").modal("show");
  }
}

function cambiarCantidadSiembra(id_orden, id_orden_alternativa, objeto, canti) {
  $("#num_orden3").html(id_orden_alternativa);
  $("#id_ordenhide").html(id_orden);
  if (canti == null || canti == undefined)
    $("#cantidad_bandejas_reales").val($(objeto).text());
  else $("#cantidad_bandejas_reales").val(canti);
  $("#ModalCantidad").modal("show");
  $("#cantidad_bandejas_reales").focus().select();
}

function guardarCambioCantidad() {
  let cantidad = $("#cantidad_bandejas_reales").val().trim();
  if (isNaN(cantidad) || parseInt(cantidad) < 1 || cantidad.length < 1) {
    swal("Debes ingresar una cantidad numérica mayor a cero", "", "error");
  } else {
    let id_orden = $("#id_ordenhide").text();
    cantidad = cantidad.trim();
    $.ajax({
      beforeSend: function () {
        $("#ModalCantidad").modal("hide");
        $("#ModalVerEstado").modal("hide");
      },
      url: "data_ver_plansiembra.php",
      type: "POST",
      data: {
        cantidad: cantidad,
        id_orden: id_orden,
        consulta: "cambiocantidadsiembra",
      },
      success: function (x) {
        if (x.trim() == "success") {
          loadPedidosEnCamara();
          swal("Los cambios se guardaron correctamente!", "", "success");
        } else {
          swal("Ocurrió un error al guardar los cambios", x, "error");
        }
      },
      error: function (jqXHR, estado, error) {
        console.log("ERROR");
      },
    });
  }
}

function CerrarModalCambio() {
  $("ModalCambiarEstado").modal("hide");
}

function eliminar_art(btn) {
  let row = btn.parentNode.parentNode;
  row.parentNode.removeChild(row);
}

function MostrarModalOrden(id) {
  let id_orden = id.replace("orden_", "");
  $("#num_orden").html(id_orden);
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

  $("ModalVerOrden").modal("show");
}

function CerrarModalOrden() {
  $("ModalVerOrden").modal("hide");
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
  $("#miVentana").append("<h4>ORDEN DE SIEMBRA</h4>");
  $("#miVentana").append(document.getElementById("tablita").innerHTML);
  $("#miVentana").find("th:last").remove();
  $("#miVentana")
    .find("tr")
    .each(function () {
      $(this).find("td:last").remove();
    });
  setTimeout("window.print();printOrdenSiembra(2, null);", 500);
}

function GuardarCambioEstado() {
  let id_artpedido = $("#id_artpedidohide").text();
  let fecha_siembra = $("#fechasiembra_txt").val().trim();
  if (
    fecha_siembra.length == 10 &&
    !fecha_siembra.includes("00/") &&
    !fecha_siembra.includes("DD/")
  ) {
    let aux = fecha_siembra.split("/");
    fecha_siembra = aux[2] + "-" + aux[1] + "-" + aux[0];
    let hoy = new Date();
    hoy.setHours(0, 0, 0, 0);
    let currenti = new Date(aux[2] + "/" + aux[1] + "/" + aux[0]);
    currenti.setHours(0, 0, 0, 0);
    if (currenti > hoy) {
      swal(
        "No se puede seleccionar una fecha posterior a la de hoy",
        "",
        "error"
      );
    } else {
      let hora_siembra = $("#appt").val().toString();
      if (hora_siembra.trim().length > 0) {
        CerrarModalCambio();
        let fecha = fecha_siembra + " " + hora_siembra + ":00";
        $.ajax({
          beforeSend: function () {},
          url: "data_ver_camara.php",
          type: "POST",
          data: {
            fecha: fecha,
            id_artpedido: id_artpedido,
            consulta: "enviar_a_camara",
          },
          success: function (x) {
            if (x.trim() == "success") {
              loadPedidos();
              swal("Los cambios se guardaron correctamente!", "", "success");
            } else {
              swal("Error al enviar a Cámara", x, "error");
            }
          },
          error: function (jqXHR, estado, error) {
            swal("Error al enviar a Cámara", error, "error");
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

function GuardarCamaraMultiple() {
  let fecha_siembra = $("#fechacamara_txt").val().trim();
  if (
    fecha_siembra.length == 10 &&
    !fecha_siembra.includes("00/") &&
    !fecha_siembra.includes("DD/")
  ) {
    let aux = fecha_siembra.split("/");
    fecha_siembra = aux[2] + "-" + aux[1] + "-" + aux[0];
    let hoy = new Date();
    hoy.setHours(0, 0, 0, 0);
    let currenti = new Date(aux[2] + "/" + aux[1] + "/" + aux[0]);
    currenti.setHours(0, 0, 0, 0);
    if (currenti > hoy) {
      swal(
        "No se puede seleccionar una fecha posterior a la de hoy",
        "",
        "error"
      );
    } else {
      let hora_siembra = $("#horacamara").val().toString();
      if (hora_siembra.trim().length > 0) {
        CerrarModalMultiple();
        let fecha = fecha_siembra + " " + hora_siembra + ":00";
        const ordenes = $("#ModalCamaraMultiple").attr("x-ordenes");
        $.ajax({
          beforeSend: function () {},
          url: "data_ver_camara.php",
          type: "POST",
          data: {
            fecha: fecha,
            consulta: "enviar_camara_multiple",
            ordenes: ordenes,
          },
          success: function (x) {
            if (x.trim() == "success") {
              loadPedidos();
              swal("Los cambios se guardaron correctamente!", "", "success");
            } else {
              swal(
                "Ocurrió un error al enviar las órdenes a Cámara",
                x,
                "error"
              );
            }
          },
          error: function (jqXHR, estado, error) {
            swal(
              "Ocurrió un error al enviar las órdenes a Cámara",
              error,
              "error"
            );
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

function getWeekNumber2(date) {
  let onejan = new Date(date.getFullYear(), 0, 1);
  week = Math.ceil(((date - onejan) / 86400000 + onejan.getDay() + 1) / 7);
  return week;
}

function getWeek2(d, tipo) {
  original = new Date(d);
  d = new Date(d);
  let day = d.getDay();
  let lista = [],
    listafechas = [];
  let html = "";
  let dias = [
    "Lunes",
    "Martes",
    "Miércoles",
    "Jueves",
    "Viernes",
    "Sábado",
    "Domingo",
  ];
  lista.push(addDays(d, -(day - 1)));
  lista.push(addDays(d, -(day - 2)));
  lista.push(addDays(d, -(day - 3)));
  lista.push(addDays(d, -(day - 4)));
  lista.push(addDays(d, -(day - 5)));
  lista.push(addDays(d, -(day - 6)));
  lista.push(addDays(d, -(day - 7)));

  for (i = 0; i < lista.length; i++) {
    var today = lista[i];
    let dd = today.getDate();
    let mm = today.getMonth() + 1; //January is 0!
    let yyyy = today.getFullYear();
    if (dd < 10) {
      dd = "0" + dd;
    }

    if (mm < 10) {
      mm = "0" + mm;
    }

    today = dd + "/" + mm + "/" + yyyy;
    html += `<th id='dia_${(i + 1).toString()}' scope='col'
      style='cursor:pointer;' onClick='imprimirSalida(this)' x-dia='${today}'
      >${dias[i]} 
      ${today}
      <br><small class='text-muted label-cantidad'></small>
      </th>`;
    listafechas.push("'" + today + "'");
  }
  if (tipo == 1) return html;
  else if (tipo == 2) return JSON.stringify(listafechas);
}

function addDays(date, days) {
  let result = new Date(date);
  result.setDate(result.getDate() + days);
  return result;
}

function changeWeek2(tipo) {
  if (tipo == 1) {
    current_date2.setDate(current_date2.getDate() + 7);
  } else if (tipo == -1) {
    current_date2.setDate(current_date2.getDate() - 7);
  } else if (tipo == 0) {
    current_date2 = new Date();
  }

  let html2 = "";
  for (let i = 0; i < 300; i++) {
    html2 +=
      "<tr class='fila'><td></td><td></td><td></td><td></td><td></td><td></td><td></td></tr>";
  }
  $("#tablasalidas").find("tbody").html(html2);
  current_weeknumber2 = getWeekNumber2(current_date2);
  $("#headerweek").html(getWeek2(current_date2, 1));
  $("#num_semana").html("Semana Nº " + current_weeknumber2);
  listaseleccionados = [];
  listacolumnas = [];
  loadPedidosEnCamara();

  let dia = new Date().getDay();

  if (current_weeknumber2 == original_weeknumber2) {
    $("#Salidas")
      .find("td:nth-child(" + dia.toString() + ")")
      .css({ backgroundColor: "#CEECF5" });
    $("#dia_" + dia.toString()).css({ backgroundColor: "#CEECF5" });
  } else {
    $("#Salidas")
      .find("td:nth-child(" + dia.toString() + ")")
      .css({ backgroundColor: "" });
    $("#dia_" + dia.toString()).css({ backgroundColor: "" });
  }
}

function CerrarModalMultiple() {
  $("#ModalCamaraMultiple").modal("hide");
}

function enviarCamara() {
  if ($(".selected2").length == 0) {
    swal(
      "Seleccioná las bandejas que quieras ingresar a la Cámara!",
      "",
      "error"
    );
  } else {
    let date = new Date();
    (hour = date.getHours()), (min = date.getMinutes());
    hour = (hour < 10 ? "0" : "") + hour;
    min = (min < 10 ? "0" : "") + min;

    let displayTime = hour + ":" + min;
    document.getElementById("horacamara").value = displayTime;

    let listaordenes = [];
    $(".selected2").each(function () {
      const id_orden = $(this).attr("x-id-orden");
      listaordenes.push(id_orden);
      $("#ModalCamaraMultiple").attr("x-ordenes", JSON.stringify(listaordenes));
    });
    $("#ModalCamaraMultiple").modal("show");
  }
}

function loadPedidosEnCamara() {
  $(".label-cantidad").html("(0)")
  listaseleccionados = [];
  listacolumnas = [];
  let fechas = getWeek2(current_date2, 2);
  let consulta = "carga_pedidos_en_camara";
  $.ajax({
    beforeSend: function () {},
    url: "carga_pedidos_camara.php",
    type: "POST",
    data: { fechas: fechas, consulta: consulta },
    success: function (x) {
      $("#tablasalidas tbody tr td").html("");
      if (x.trim().length > 0) {
        let obj = JSON.parse(x);
        let sinpos = [];
        for (let j = 0; j < obj.length; j++) {
          let cant_band = 0;
          for (let i = 0; i < obj[j].length; i++) {
            let celda = $("#Salidas").find(
              "tr:eq(" + (i + 1).toString() + ") td:eq(" + j.toString() + ")"
            );
            let data = obj[j][i];


            cant_band += parseInt(data[7]);

            if(i == (obj[j].length)-1){
              $("#dia_"+(j+1)).find(".label-cantidad").html("("+cant_band+")")
            }

            if (data[10] != null) {
              celda.css({ "background-color": "red", padding: "5px" });
              let problema = "";
              if (data[10] == 1) {
                problema = "SACAR " + data[11].toString() + " DÍAS ANTES";
              } else if (data[10] == 2) {
                problema = "QUEDA " + data[11].toString() + " DÍAS";
              } else if (data[10] == 3) {
                problema = "PASA A OPCIÓN " + data[11].toString();
              }

              celda.html(
                `
                <div>
                  <span style='font-size:1em;font-weight:bold;color:white'>` +
                  problema +
                  `</span>
                </div>
                <div>
                ` +
                  MakeBox2(data) +
                  `
                </div>
                `
              );
            } else {
              celda.html(MakeBox2(data));
              celda.css("background-color", "");
            }
          }
        }
      }
    },
    error: function (jqXHR, estado, error) {},
  });
}

function MakeBox2(data) {
  let color;
  let producto = data[0];
  let estado = data[2];
  let fecha = data[6];
  let cant_bandejas = data[7].toString();
  let fecha_salida = data[8];
  let cliente = data[9];
  let problema = data[10];
  let dataproblema = data[11];

  let t = fecha.split(/[- :]/);
  let d = new Date(Date.parse(fecha));
  let now = new Date().getTime();
  let distance = now - d;
  let days = Math.floor(distance / (1000 * 60 * 60 * 24));
  let hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
  if (producto.includes("TOMATE")) {
    if (estado != 4) {
      color = "#FFACAC";
    }
  } else if (producto.includes("PIMIENTO")) {
    if (estado != 4) {
      color = "#BAE1A2";
    }
  } else if (producto.includes("BERENJENA")) {
    if (estado != 4) {
      color = "#D5B4FF";
    }
  } else if (producto.includes("LECHUGA")) {
    if (estado != 4) {
      color = "#D7FFBC";
    }
  } else if (producto.includes("ACELGA")) {
    if (estado != 4) {
      color = "#BFDCBC";
    }
  } else if (producto.includes("REMOLACHA")) {
    if (estado != 4) {
      color = "#eba5b5";
    }
  } else if (producto.includes("REMOLACHA")) {
    if (estado != 4) {
      color = "#eba5b5";
    }
  } else if (
    producto.includes("COLES") ||
    producto.includes("HINOJO") ||
    producto.includes("APIO")
  ) {
    if (estado != 4) {
      color = "#58ACFA";
    }
  } else if (producto.includes("VERDEO") || producto.includes("PUERRO")) {
    if (estado != 4) {
      color = "#F7BE81";
    }
  } else {
    if (estado != 4) {
      color = "#f2f2f2";
    }
  }
  if (estado == 4) {
    color = "#A4A4A4";
  }
  let dd = current_date2.getDate();
  let mm = current_date2.getMonth() + 1; //January is 0!
  let yyyy = current_date2.getFullYear();
  if (dd < 10) {
    dd = "0" + dd;
  }
  if (mm < 10) {
    mm = "0" + mm;
  }
  let borde = "gray";
  if (fecha_salida == dd + "/" + mm + "/" + yyyy && estado != 4) {
    //borde = "red";
  }
  producto = producto.substring(producto.indexOf("|") + 1);
  let html =
    "<div id='id2_" +
    data[1] +
    "' class='cajita' style='background-color:" +
    color +
    "; border-radius:10px; cursor: pointer; border-style: solid; border-color: " +
    borde +
    ";border-width: 1px;' onClick='MostrarModalEstado2(" +
    data[1] +
    ")'>";
  if (estado == 3) {
    html +=
      "<span id='id_" +
      data[1] +
      "' style='font-weight:bold;font-size:1.2em; word-wrap:break-word;'>[" +
      data[4] +
      "] " +
      producto +
      "<br>" +
      cant_bandejas +
      " bandejas<br><span style='font-size:12px;'>Ingresó hace:<br>" +
      days.toString() +
      " días, " +
      hours.toString() +
      " horas</span></span>";
  } else if (estado == 4) {
    html +=
      "<span id='id_" +
      data[1] +
      "' style='font-weight:bold; font-size:1.2em; word-wrap:break-word;'>[" +
      data[4] +
      "] " +
      producto +
      "<br>" +
      cant_bandejas +
      " bandejas<br>(YA SALIÓ)</span>";
  }
  html += "</div>";
  return html;
} //FIN MAKEBOX

let html2 = "";
for (let i = 0; i < 300; i++) {
  html2 +=
    "<tr class='fila'><td></td><td></td><td></td><td></td><td></td><td></td><td></td></tr>";
}
$("#tablasalidas").find("tbody").html(html2);
$("#headerweek").html(getWeek2(current_date2, 1));
let dia = new Date().getDay();
$("#Salidas")
  .find("td:nth-child(" + dia.toString() + ")")
  .css({ backgroundColor: "#CEECF5" });
$("#dia_" + dia.toString()).css({ backgroundColor: "#CEECF5" });

function addToLista(id) {
  if (document.getElementById(id).checked) {
    listaseleccionados.push(id.replace("cbox_", ""));
    listacolumnas.push(
      $("#" + id)
        .closest("td")
        .index()
    );
  } else {
    let index = listaseleccionados.indexOf(id.replace("cbox_", ""));
    if (index > -1) {
      listacolumnas.splice(index, 1);
    }
    listaseleccionados = listaseleccionados.filter(
      (e) => e !== id.replace("cbox_", "")
    );
  }
}

function MostrarModalEstado2(id) {
  id_articulopedido = id;
  $("#id_artpedidocamara").html(id.toString());
  $.ajax({
    beforeSend: function () {},
    url: "data_ver_camara.php",
    type: "POST",
    data: { id: id, consulta: "cliente" },
    success: function (x) {
      $("#nombre_cliente").html(x);
    },
    error: function (jqXHR, estado, error) {
      $("#nombre_cliente").html(
        "Hubo un error al cargar la información del pedido"
      );
    },
  });
  consulta = "pedido";
  $.ajax({
    beforeSend: function () {},
    url: "data_ver_camara.php",
    type: "POST",
    data: { id: id, consulta: consulta },
    success: function (x) {
      $("#box_info").html(x);
    },

    error: function (jqXHR, estado, error) {
      $("#box_info").html("Hubo un error al cargar la información del pedido");
    },
  });

  $("#ModalVerEstado").modal("show");
}

function ActivarText() {
  $("#observacionescamara_txt").prop("disabled", false).focus();
  $("#btn_guardarobs").prop("disabled", false);
}

function GuardarObservaciones() {
  let observaciones = $("#observacionescamara_txt").val().trim();
  let id_artpedido = $("#id_artpedidocamara").text();

  $.ajax({
    beforeSend: function () {},
    url: "data_ver_camara.php",
    type: "POST",
    data: {
      consulta: "modificar_observaciones",
      id_artpedido: id_artpedido,
      observaciones: observaciones,
    },
    success: function (x) {
      if (x.trim() == "success") {
        swal(
          "Las observaciones fueron guardadas correctamente!",
          "",
          "success"
        );
        MostrarModalEstado2(id_artpedido);
      } else {
        swal("Ocurrió un error al guardar las observaciones", x, "error");
      }
    },
    error: function (jqXHR, estado, error) {
      swal("Ocurrió un error al guardar las observaciones", error, "error");
    },
  });
}

function toggleSelection(objeto) {
  let tr = $(objeto).parent();
  let estado = $(tr).find("td:last").text();
  if (estado.includes("SEMBRADO")) {
    if (tr.hasClass("selected2")) {
      tr.removeClass("selected2");
    } else {
      tr.addClass("selected2");
    }
  }
}

function eliminar_art(btn) {
  let row = btn.parentNode.parentNode;
  row.parentNode.removeChild(row);
}

function MostrarModalOrden(id) {
  if (listaseleccionados.length > 0) {
    let aux = listacolumnas[0];
    let puede = true;
    for (let i = 1; i < listacolumnas.length; i++) {
      if (listacolumnas[i] != aux) {
        puede = false;
        break;
      }
    }
    if (puede == true) {
      consulta = "orden";
      let jsonarray = JSON.stringify(listaseleccionados);
      $.ajax({
        beforeSend: function () {},
        url: "cargar_detalleestado.php",
        type: "POST",
        data: { jsonarray: jsonarray, consulta: consulta },
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
    } else {
      swal(
        "Sólo se pueden seleccionar productos de la misma columna",
        "",
        "error"
      );
    }
  }
}

function CerrarModalMesadas() {
  $("#ModalEnviaraMesadas").modal("hide");
}

function CerrarModalOrden() {
  $("#ModalVerOrden").modal("hide");
}

function enviar_a_Mesada() {
  loadMesadasModal();
  $(".cantidadbox").remove();
  $(".mesada-clicked").removeClass("mesada-clicked");
  $("#bandejas_pendientes").html(
    "Bandejas a Organizar: " + $("#cantidad_bandejas").text()
  );
  $("#quedan_bandejas").html($("#cantidad_bandejas").text());
  $("#ModalEnviaraMesadas").modal("show");
  indicescroll = 0;
}

function click_mesada(id) {
  $(id).toggleClass("mesada-clicked");
  let id_mesada = $(id).attr("id").replace("mesada_", "");
  if ($(id).hasClass("mesada-clicked")) {
    codigo =
      "<div class='row cantidadbox' id='cantidadtxt_" +
      id_mesada +
      "'>" +
      "<div class='col-md-8'>" +
      "<label class='control-label'>Cantidad Mesada " +
      id.id.replace("mesada_", "") +
      ":</label>" +
      "<input style='font-size:1.4em' type='number' id='input_" +
      id.id.replace("mesada_", "") +
      "' min='0' step='1' class='form-control cantidadmesada' onchange='setFaltante()' onkeyup='this.onchange();' onpaste='this.onchange();' oninput='this.onchange();'> " +
      "</div></div>";

    $("#contenedor_cantidades").append(codigo);
    let x = document.getElementsByClassName("cantidadbox");
    if (x.length == 1) {
      $("#input_" + id.id.replace("mesada_", "")).val(
        $("#cantidad_bandejas").text()
      );
    } else if (x.length >= 1) {
      $(x[0]).find("input").val("");
    }
  } else {
    $("#cantidadtxt_" + id.id.replace("mesada_", "")).remove();
  }

  setFaltante();
}

function setFaltante() {
  let x = document.getElementsByClassName("cantidadmesada");
  let cant_original = parseInt($("#cantidad_bandejas").text());
  for (let i = 0; i < x.length; i++) {
    let valor = parseInt(x[i].value);
    if (isNaN(valor)) {
      valor = 0;
    }
    cant_original -= valor;
  }
  $("#quedan_bandejas").html(cant_original.toString());
}

function marcarProblema(id_orden, tipo, titulo, id_artpedido) {
  $("#titulo-problema").html(titulo);
  $("#id_ordenproblemahide").html(id_orden);

  $("#tipo_problema").html(tipo);
  $("#contenedor_problema").html("");
  if (tipo <= 2) {
    $("#contenedor_problema").html(
      `
          <div class='row'>
          <div class="col-md-2">
            <label class="control-label">Días:</label>
          </div>
          <div class='col-md-10'>
            <select id="select_dias" class="selectpicker"  data-dropup-auto="false" data-size="8" title="Cantidad" data-style="btn-info">
                  <option style='font-size:1.6em' value="1">1</option>
                  <option style='font-size:1.6em' value="2">2</option>
                  <option style='font-size:1.6em' value="3">3</option>
                  <option style='font-size:1.6em' value="4">4</option>
                  <option style='font-size:1.6em' value="5">5</option>
                  <option style='font-size:1.6em' value="6">6</option>
                  <option style='font-size:1.6em' value="7">7</option>
                  <option style='font-size:1.6em' value="8">8</option>
                  <option style='font-size:1.6em' value="9">9</option>
                  <option style='font-size:1.6em' value="10">10</option>
                </select>
          </div>
        </div>
        `
    );
    $("#select_dias").val("default").selectpicker("refresh");
  } else {
    $("#contenedor_problema").html(
      `
          <div class='row'>
          <div class="col-md-2">
            <label class="control-label">Opción:</label>
          </div>
          <div class='col-md-10'>
            <select id="select_dias" class="selectpicker"  data-dropup-auto="false" title="Seleccionar" data-style="btn-info">
                  <option style='font-size:1.6em' value="1">Opción 1</option>
                  <option style='font-size:1.6em' value="2">Opción 2</option>
                  <option style='font-size:1.6em' value="3">Opción 3</option>
                </select>
          </div>
        </div>
        `
    );
    $("#select_dias").val("default").selectpicker("refresh");
  }

  $("#ModalProblema").css({ display: "block" });
}

function guardarProblema() {
  let tipo = parseInt($("#tipo_problema").text());
  let id_orden = $("#id_ordenproblemahide").text();
  let dias = $("#select_dias").val();
  if (dias && dias.length) {
    let id_artpedido = $("#id_artpedidocamara").text();
    $.ajax({
      beforeSend: function () {
        $("#ModalProblema").css({ display: "none" });
      },
      url: "data_ver_camara.php",
      type: "POST",
      data: {
        consulta: "guardar_problema",
        id_orden: id_orden,
        dias: dias,
        tipo: tipo,
      },
      success: function (x) {
        if (x.trim() == "success") {
          MostrarModalEstado(id_artpedido);
          loadPedidosEnCamara();
          swal("Problema de Cámara marcado correctamente!", "", "success");
        } else {
          swal("Ocurrió un error al marcar el Problema de Cámara", x, "error");
        }
      },
      error: function (jqXHR, estado, error) {
        swal("OCURRIO UN ERROR, intente nuevamente", "", "error");
        $("#ModalProblema").css({ display: "block" });
      },
    });
  } else {
    swal("Selecciona una opción!", "", "error");
  }
}

function quitarProblema(id_orden) {
  let id_artpedido = $("#id_artpedidocamara").text();
  $.ajax({
    beforeSend: function () {
      $("#ModalProblema").css({ display: "none" });
    },
    url: "data_ver_camara.php",
    type: "POST",
    data: { consulta: "quitar_problema", id_orden: id_orden },
    success: function (x) {
      if (x.trim() == "success") {
        MostrarModalEstado(id_artpedido);
        loadPedidosEnCamara();
        swal("Quitaste el Problema de Cámara!", "", "success");
      } else {
        swal("Ocurrió un error al quitar el Problema de Cámara", x, "error");
      }
    },
    error: function (jqXHR, estado, error) {
      swal("OCURRIO UN ERROR, intente nuevamente", "", "error");
      $("#ModalProblema").css({ display: "block" });
    },
  });
}

function GuardarMesadas() {
  let cantidades = document.getElementsByClassName("cantidadmesada");
  if (cantidades.length > 0) {
    let cantidadtotal = 0;
    let id_orden = document
      .getElementsByClassName("id_ordenreal")[0]
      .id.replace("ordenreal_", "");
    let cantidad_requerida = parseInt($("#cantidad_bandejas").text());
    let escero = false;
    let arraymesadas = [];
    for (let i = 0; i < cantidades.length; i++) {
      if (
        cantidades[i].value.trim().length == 0 ||
        isNaN(cantidades[i].value.trim()) ||
        parseInt(cantidades[i].value) == 0
      ) {
        escero = true;
        break;
      }
      cantidadtotal += parseInt(cantidades[i].value);
    }
    if (escero) {
      swal("Las cantidades ingresadas deben ser mayores a cero!", "", "error");
    } else if (cantidadtotal < cantidad_requerida) {
      swal("Están sobrando bandejas para asignar a las mesadas", "", "error");
    } else if (cantidadtotal > cantidad_requerida) {
      swal(
        "Ingresaste una cantidad mayor a la que hay que colocar en las mesadas",
        "",
        "error"
      );
    } else {
      let puede = true;
      for (let i = 0; i < cantidades.length; i++) {
        let id_mesada = cantidades[i].id.replace("input_", "");
        let libres = parseInt($("#libres_" + id_mesada).text());
        if (libres < parseInt(cantidades[i].value)) {
          puede = false;
        } else {
          arraymesadas.push([id_mesada, parseInt(cantidades[i].value)]);
        }
      }
      if (!puede) {
        swal(
          "No hay suficiente lugar libre para colocar las bandejas ingresadas",
          "",
          "error"
        );
      } else {
        CerrarModalMesadas();
        $("#ModalVerEstado").modal("hide");
        const jsonarray = JSON.stringify(arraymesadas);
        $.ajax({
          beforeSend: function () {},
          url: "data_ver_mesadas.php",
          type: "POST",
          data: {
            jsonarray: jsonarray,
            consulta: "guardar_mesadas",
            id_orden: id_orden,
          },
          success: function (x) {
            if (x.trim() == "success") {
              loadPedidosEnCamara();
              swal(
                "Las mesadas fueron asignadas correctamente!",
                "",
                "success"
              );
            } else {
              swal("Ocurrió un error al asignar las mesadas", x, "error");
            }
          },
          error: function (jqXHR, estado, error) {
            swal("Ocurrió un error al asignar las mesadas", error, "error");
          },
        });
      }
    }
  }
}

//NUEVO 20-10-2022

function imprimirCamara(tipo) {
  if (tipo == 1) {
    func_printBusqueda();
    document.getElementById("ocultar").style.display = "none";
    document.getElementById("miVentana").style.display = "block";
    $("footer").addClass("d-none");
  } else {
    document.getElementById("ocultar").style.display = "block";
    document.getElementById("miVentana").style.display = "none";
    $("#miVentana").html("");
    $("footer").removeClass("d-none");
  }
}

function func_printBusqueda() {
  const dia = $("#header").text();
  $("#miVentana").html(`<h4>ENTRADAS CÁMARA ${dia}</h4>`);

  $("#miVentana").append(document.getElementById("tablitaa").outerHTML);

  $("#miVentana")
    .find("tr,td,th")
    .css({ "font-size": "11px", "word-wrap": "break-word" });

  $("#miVentana").find(".th-first").css({ width: "10px !important" });

  $("#miVentana")
    .find("tr")
    .each(function () {
      $(this).find("td:eq(0)").css({ "font-size": "14px" });
      $(this).find("td:eq(1)").css({ "font-size": "14px" });
      $(this).find("td:eq(2)").css({ "font-size": "16px" });

      let estado = $(this).find("td:eq(3)");
      let estadito = $(estado).text();
      $(estado).css({ "font-size": "12px" });
      $(estado).html(estadito);
    });

  setTimeout("window.print();imprimirCamara(2)", 500);
}

function imprimirSalida(obj) {
  swal("Imprimir las Salidas de Cámara del Día?", "", {
    icon: "info",
    buttons: {
      cancel: "Salir",
      catch: {
        text: "Sí, IMPRIMIR",
        value: "catch",
      },
    },
  }).then((value) => {
    switch (value) {
      case "catch":
        funcImprimirSalida(obj)
        break;
    }
  });  
}

function funcImprimirSalida(obj){
  $("#miVentana").html("");
  const dia = $(obj).text();

  $("#miVentana").html(`<h5 class='mt-4 mb-5'>SALIDAS CÁMARA - ${dia}</h5>`);

  var columnNth = $(obj).index() + 1;

  $("#miVentana").append(`
    <div class="row mt-3">
    </div>`);

  let puedeImprimir = false;
  $("#tablasalidas tbody tr td:nth-child(" + columnNth + ")").each(function () {
    if (!$(this).children().length > 0) return;
    
    if (!puedeImprimir) puedeImprimir = true;
    $("#miVentana").find(".row").append(`
      <div class="col-md-3 mb-3">
        ${$(this).html()}
      </div>
    `);
  });

  if (!puedeImprimir) return;

  document.getElementById("ocultar").style.display = "none";
  document.getElementById("miVentana").style.display = "block";
  $("footer").addClass("d-none");

  setTimeout(() => {
    window.print();
    document.getElementById("ocultar").style.display = "block";
    document.getElementById("miVentana").style.display = "none";

    $("footer").removeClass("d-none");
  }, 500);
}

