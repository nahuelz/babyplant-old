let current_date = new Date();
let original_weeknumber = null;
let current_weeknumber = null;
let listaseleccionados = [];
let listacolumnas = [];
let id_articulopedido = null;
let puedemover = false;
let estadoactual = null;
let pressTimer;

$(document).ready(function () {
  hasChanged = false;
  original_weeknumber = getWeekNumber(current_date);
  current_weeknumber = original_weeknumber;
  changeWeek(0);
  $("#guardar_btn").prop("disabled", true);
  $("#num_semana").html("Semana Nº " + current_weeknumber);
});

function getWeekNumber(date) {
  let onejan = new Date(date.getFullYear(), 0, 1);
  week = Math.ceil(((date - onejan) / 86400000 + onejan.getDay() + 1) / 7);
  return week;
}

function getWeek(d, tipo) {
  original = new Date(d);
  d = new Date(d);
  let day = d.getDay();
  if (day == 0) {
    day = 6;
    d = addDays(d, -1);
  }
  let lista = [],
    listafechas = [];
  let html = "";
  let dias = ["Lunes", "Martes", "Miércoles", "Jueves", "Viernes", "Sábado"];
  lista.push(addDays(d, -(day - 1)));
  lista.push(addDays(d, -(day - 2)));
  lista.push(addDays(d, -(day - 3)));
  lista.push(addDays(d, -(day - 4)));
  lista.push(addDays(d, -(day - 5)));
  lista.push(addDays(d, -(day - 6)));
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
    var today = dd + "/" + mm + "/" + yyyy;
    html +=
      "<th style='overflow-wrap: break-word;cursor:pointer' onClick='getTotales(\"" +
      today +
      "\")' id='dia_" +
      (i + 1).toString() +
      "'>" +
      dias[i] +
      " " +
      today +
      "</th>";
    listafechas.push(today);
  }

  if (tipo == 1) return html;
  else if (tipo == 2) return JSON.stringify(listafechas);
}

function getTotales(fecha) {
  $("#headerday").html(" " + fecha);
  $.ajax({
    beforeSend: function () {},
    url: "data_ver_planificacion.php",
    type: "POST",
    data: { fecha: fecha, consulta: "get_totales" },
    success: function (x) {
      $("#tablatotales tbody").html(x);
      $("#ModalGetTotales").modal("show");
    },
    error: function (jqXHR, estado, error) {
      $("#tablatotales").html("Hubo un error al cargar la información");
    },
  });
}

function addDays(date, days) {
  let result = new Date(date);
  result.setDate(result.getDate() + days);
  return result;
}

function changeWeek(tipo) {
  if (tipo == 1) {
    current_date.setDate(current_date.getDate() + 7);
  } else if (tipo == -1) {
    current_date.setDate(current_date.getDate() - 7);
  } else if (tipo == 0) {
    current_date = new Date();
  }

  current_weeknumber = getWeekNumber(current_date);
  $("#header").html(getWeek(current_date, 1));
  $("#num_semana").html("Semana Nº " + current_weeknumber);
  listaseleccionados = [];
  listacolumnas = [];
  let html2 = "";
  for (let i = 0; i < 300; i++) {
    html2 +=
      "<tr class='fila'><td ondrop='drop(event)' ondragover='allowDrop(event)'></td><td ondrop='drop(event)' ondragover='allowDrop(event)'></td><td ondrop='drop(event)' ondragover='allowDrop(event)'></td><td ondrop='drop(event)' ondragover='allowDrop(event)'></td><td ondrop='drop(event)' ondragover='allowDrop(event)'></td><td ondrop='drop(event)' ondragover='allowDrop(event)'></td></tr>";
  }
  $("#TABLA").find("tbody").html(html2);
  loadPedidos();
  let dia = new Date().getDay();
  if (current_weeknumber == original_weeknumber) {
    $("#TABLA")
      .find("td:nth-child(" + dia.toString() + ")")
      .css({ backgroundColor: "#CEECF5" });
    $("#dia_" + dia.toString()).css({ backgroundColor: "#CEECF5" });
    $("#dia_" + dia.toString()).addClass("hoy");
  } else {
    $("#TABLA").find("td").css({ backgroundColor: "" });
    $("#dia_" + dia.toString()).css({ backgroundColor: "" });
    $("#dia_" + dia.toString()).removeClass("hoy");
  }
  hasChanged = false;
}

function loadPedidos() {
  listaseleccionados = [];
  listacolumnas = [];
  let fechas = getWeek(current_date, 2);
  $.ajax({
    beforeSend: function () {},
    url: "data_ver_planificacion.php",
    type: "POST",
    data: { fechas: fechas, consulta: "carga_pedidos" },
    success: function (x) {
      $("#tablitaa tbody tr td").html("");
      if (x.trim().length) {
        const data = JSON.parse(x);
        const listafechas = JSON.parse(fechas);
        listafechas.forEach(fecha => {
          if (data[fecha] && data[fecha].length){
            for (let i = 0; i < data[fecha].length; i++){
              const index = listafechas.indexOf(fecha);
              $("#TABLA")
                .find(
                  "tr:eq(" +
                    (i + 1).toString() +
                    ") td:eq(" +
                    index.toString() +
                    ")"
                )
                .html(MakeBox(data[fecha][i]));
            }
          }
        });
      }
    },
    error: function (jqXHR, estado, error) {},
  });
  DeseleccionarTodo();
}

function MakeBox(data) {
  let color;
  let producto = data[0];
  let estado = data[2];
  let cant_bandejas = data[4].toString();
  let fecha = data[5];
  let cliente = data[6];
  let parts = fecha.split("/");
  let fechabox = new Date(parts[2], parts[1] - 1, parts[0]);
  let today = new Date();
  let myToday = new Date(
    today.getFullYear(),
    today.getMonth(),
    today.getDate(),
    0,
    0,
    0
  );
  if (myToday > fechabox && estado == 1) {
    color = "#FFFF00";
  } else {
    if (estado == 2) {
      color = "#848484";
    } else {
      if (producto.includes("TOMATE")) {
        if (estado == 0) {
          color = "#FFACAC";
        } else if (estado >= 1) {
          color = "#FF3B3B";
        }
      } else if (producto.includes("PIMIENTO")) {
        if (estado == 0) {
          color = "#BAE1A2";
        } else if (estado >= 1) {
          color = "#5AEE00";
        }
      } else if (producto.includes("BERENJENA")) {
        if (estado == 0) {
          color = "#e1c9ff";
        } else if (estado >= 1) {
          color = "#ad6eff";
        }
      } else if (producto.includes("LECHUGA")) {
        if (estado == 0) {
          color = "#D7FFBC";
        } else if (estado >= 1) {
          color = "#77FF1C";
        }
      } else if (producto.includes("ACELGA")) {
        if (estado == 0) {
          color = "#BFDCBC";
        } else if (estado >= 1) {
          color = "#348D2B";
        }
      } else if (producto.includes("REMOLACHA")) {
        if (estado == 0) {
          color = "#eba5b5";
        } else if (estado >= 1) {
          color = "#ee204e";
        }
      } else if (
        producto.includes("COLES") ||
        producto.includes("HINOJO") ||
        producto.includes("APIO")
      ) {
        if (estado == 0) {
          color = "#A9D0F5";
        } else if (estado >= 1) {
          color = "#58ACFA";
        }
      } else if (producto.includes("VERDEO") || producto.includes("PUERRO")) {
        if (estado == 0) {
          color = "#F5DA81";
        } else if (estado >= 1) {
          color = "#F7BE81";
        }
      } else {
        color = "#FFFFFF";
      }
    }
  }
  if (estado == 0) {
    clase = "pendiente";
  } else if (estado == 1) {
    clase = "planificado";
  } else if (estado == 2) {
    clase = "sembrado";
  }
  producto = producto.substring(producto.indexOf("|") + 1);
  let html =
    "<div id='id2_" +
    data[1] +
    "' class='cajita " +
    clase +
    "' draggable='true' ondragstart='drag(event);' onClick='toggleSelection(this)' style='touch-action: none;background-color:" +
    color +
    ";font-size:1.2em;'";
  html +=
    "ontouchmove='dropear(event)' ondblclick='MostrarModalEstado(" +
    data[1] +
    ")'>";
  html +=
    "<span id='id_" +
    data[1] +
    "'style='font-weight:bold; word-wrap: break-word;'>" +
    producto +
    "<br>" +
    cliente +
    "<br>" +
    cant_bandejas +
    " bandejas</span>";

  if (estado == 1) {
    html += `
            <div class="bg-light m-1"><p class='text-danger' style='font-size:0.8em'>
              YA PLANIFICADO
            </p></div>`;
  } else if (estado > 1) {
    html += `
            <div class="bg-light m-1"><p class='text-primary' style='font-size:0.8em'>
              YA SEMBRADO
            </p></div>`;
  }

  html += "</div>";
  return html;
}

let html2 = "";
for (let i = 0; i < 300; i++) {
  html2 +=
    "<tr class='fila'><td ondrop='drop(event)' ondragover='allowDrop(event)'></td><td ondrop='drop(event)' ondragover='allowDrop(event)'></td><td ondrop='drop(event)' ondragover='allowDrop(event)'></td><td ondrop='drop(event)' ondragover='allowDrop(event)'></td><td ondrop='drop(event)' ondragover='allowDrop(event)'></td><td ondrop='drop(event)' ondragover='allowDrop(event)'></td></tr>";
}

$("#TABLA").find("tbody").html(html2);
$("#header").html(getWeek(current_date, 1));
let dia = new Date().getDay();
$("#TABLA")
  .find("td:nth-child(" + dia.toString() + ")")
  .css({ backgroundColor: "#CEECF5" });
$("#dia_" + dia.toString()).css({ backgroundColor: "#CEECF5" });
$("#dia_" + dia.toString()).addClass("hoy");

function addToLista(objeto) {
  let id = $(objeto).attr("id").replace("id_", "").replace("id2_", "");
  if ($(objeto).hasClass("selected")) {
    listaseleccionados.push(id);
    listacolumnas.push($(objeto).closest("td").index());
  } else {
    let index = listaseleccionados.indexOf(id);
    if (index > -1) {
      listacolumnas.splice(index, 1);
    }
    listaseleccionados = listaseleccionados.filter((e) => e !== id);
  }
}

function MostrarModalEstado(id) {
  consulta = "cliente";
  id_articulopedido = id;
  $.ajax({
    beforeSend: function () {},
    url: "cargar_detalleestado.php",
    type: "POST",
    data: { id: id, consulta: consulta },
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
    url: "cargar_detalleestado.php",
    type: "POST",
    data: { id: id, consulta: consulta },
    success: function (x) {
      $("#box_info").html(x);
    },
    error: function (jqXHR, estado, error) {
      $("#box_info").html("Hubo un error al cargar la información del pedido");
    },
  });
  $("#ModalVerEstado").modal("show")
}

function GuardarCambios() {
  let listacajas = document.getElementsByClassName("cajita");
  let arraycita = [];
  let fechas = getWeek(current_date, 2);
  fechas = JSON.parse(fechas);

  for (let i = 0; i < listacajas.length; i++) {
    let indice = $("#" + listacajas[i].id)
      .closest("td")
      .index();
    let indice2 = $("#" + listacajas[i].id)
      .closest("tr")
      .index();
    arraycita.push([
      listacajas[i].id.replace("id2_", ""),
      fechas[indice],
      indice2,
    ]);
  }

  if (arraycita.length) {
    const jsonarray = JSON.stringify(arraycita);
    $.ajax({
      beforeSend: function () {},
      url: "data_ver_planificacion.php",
      type: "POST",
      data: { jsonarray: jsonarray, consulta: "actualiza_filas" },
      success: function (x) {
        if (x.trim() == "success"){
          loadPedidos();
          swal("Los cambios se guardaron correctamente!", "", "success");
          $("#guardar_btn").prop("disabled", true);
          hasChanged = false;
        }
        else{
          swal("Ocurrió un error al guardar los cambios", x, "error");
        }
      },
      error: function (jqXHR, estado, error) {
        console.log("ERROR");
      },
    });
  }
}

function GuardarOrden() {
  let oTable = document.getElementById("tabla_detallepedido");
  let rowLength = oTable.rows.length;
  let articulos = [];
  let puede = true;
  for (i = 1; i < rowLength; i++) {
    let oCells = oTable.rows.item(i).cells;
    let cellVal = oCells.item(0).innerHTML;
    let codigosobre = oCells.item(4).innerHTML;
    if (codigosobre.includes("button") || codigosobre.length < 1) {
      puede = false;
    }
    articulos.push(cellVal);
  }
  if (articulos.length > 0) {
    if (puede == true) {
      CerrarModalOrden();
      const fechas = JSON.parse(getWeek(current_date, 2));
      const fecha_planificacion = fechas[parseInt(listacolumnas[0])];
      const jsonarray = JSON.stringify(articulos);
      $.ajax({
        beforeSend: function () {},
        url: "data_ver_planificacion.php",
        type: "POST",
        data: {
          jsonarray: jsonarray,
          fecha_planificacion: fecha_planificacion,
          consulta: "agrega_orden",
        },
        success: function (x) {
          if (x.trim() == "success"){
            loadPedidos();
            if (articulos.length == 1) {
              swal(
                "La orden de siembra fue generada correctamente!",
                "",
                "success"
              );
            } else if (articulos.length > 1) {
              swal(
                "Las órdenes de siembra fueron generadas correctamente!",
                "",
                "success"
              );
            }
          }
          else{
            swal("Error al generar la/s Órden/es de Siembra", x, "error")
          }
        },
        error: function (jqXHR, estado, error) {
          swal("Error al generar la/s Órden/es de Siembra", error, "error")
        },
      });
    } else {
      swal(
        "No se puede planificar un producto sin número de sobre!",
        "",
        "error"
      );
    }
  }
}

function eliminar_art(btn) {
  let row = btn.parentNode.parentNode;
  row.parentNode.removeChild(row);
}

function MostrarModalOrden(id) {
  if (listaseleccionados.length > 0) {
    if (hasChanged == false) {
      let aux = listacolumnas[0];
      let error = "";
      let puede = true;
      for (let i = 1; i < listacolumnas.length; i++) {
        if (listacolumnas[i] != aux) {
          puede = false;
          error = "Sólo se pueden seleccionar productos de la misma columna";
          break;
        }
      }

      for (let i = 0; i < listaseleccionados.length; i++) {
        if ($("#id2_" + listaseleccionados[i]).hasClass("sembrado")) {
          puede = false;
          error = "La orden seleccionada ya está sembrada!";
          break;
        } else if ($("#id2_" + listaseleccionados[i]).hasClass("planificado")) {
          puede = false;

          error =
            "La orden seleccionada ya había sido planificada! Si desea que se siembre un día diferente, sólo hay que arrastrar el producto a la fecha deseada y clickear en GUARDAR POSICIONES";
          break;
        }
      }

      let fechas = JSON.parse(getWeek(current_date, 2));
      let fecha_planificacion = fechas[parseInt(listacolumnas[0])];
      let fecha = fecha_planificacion.replace("'", "").replace("'", "");
      let parts = fecha.split("/");
      let fechabox = new Date(parts[2], parts[1] - 1, parts[0]);
      let today = new Date();
      let myToday = new Date(
        today.getFullYear(),
        today.getMonth(),
        today.getDate(),
        0,
        0,
        0
      );
      if (myToday > fechabox) {
        puede = false;
        error =
          "No se puede planificar un pedido de una fecha anterior a la de hoy!";
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
        $("#ModalVerOrden").modal("show")
      } else {
        swal("ERROR", error, "error");
      }
    } else {
      swal(
        "Debes guardar los cambios realizados antes de generar una orden",
        "",
        "error"
      );
    }
  }
}


function CerrarModalOrden() {
  $("#ModalVerOrden").modal("hide")
}

function sendToWeek(tipo) {
  if (listaseleccionados.length > 0) {
    let txt;
    let r = confirm(
      "Estás seguro de enviar a otra semana los productos seleccionados?"
    );
    if (r == true) {
      let consulta = "change_week";
      let listacajas = document.getElementsByClassName("cajita");
      let arraycita = [];
      let fechas = getWeek(current_date, 2);
      fechas = JSON.parse(fechas);
      let fecha = fechas[1].split("/");
      fecha =
        fecha[2].replace("'", "").replace("'", "") +
        "/" +
        fecha[1].replace("'", "").replace("'", "") +
        "/" +
        fecha[0].replace("'", "").replace("'", "");

      let myDate = new Date(fecha);
      if (tipo == 1) myDate.setDate(myDate.getDate() + 6);
      else if (tipo == 0) myDate.setDate(myDate.getDate() - 6);
      dia = myDate.getDay();
      let nuevafecha =
        ("0" + myDate.getDate()).slice(-2) +
        "/" +
        ("0" + (myDate.getMonth() + 1)).slice(-2) +
        "/" +
        myDate.getFullYear();
      let jsonarray = JSON.stringify(listaseleccionados);
      $.ajax({
        beforeSend: function () {},
        url: "data_ver_planificacion.php",
        type: "POST",
        data: {
          jsonarray: jsonarray,
          consulta: consulta,
          nuevafecha: nuevafecha,
        },
        success: function (x) {
          loadPedidos();
          swal(
            "Cambiaste de semana a los productos seleccionados!",
            "",
            "success"
          );
        },
        error: function (jqXHR, estado, error) {
          alert("ERROR al cambiar la fecha de los productos");
        },
      });
    }
  } else {
    swal("ERROR", "Debes seleccionar algún producto", "error");
  }
}

function printOrdenSiembra(tipo) {
  if (tipo == 1) {
    func_printOrdenSiembra();
    document.getElementById("ocultar").style.display = "none";
    document.getElementById("miVentana").style.display = "block";
  } else {
    document.getElementById("ocultar").style.display = "block";
    document.getElementById("miVentana").style.display = "none";
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

function DeseleccionarTodo() {
  $(".cajita").removeClass("selected");
  $(".cajita").css({ border: "1px solid #00000033" });
  $(".cajita").parent().css({ "background-color": "" });
  listaseleccionados = [];
  listacolumnas = [];
}

function showDialogSobre(id) {
  let codigo = prompt("INGRESA CÓDIGO DE SOBRE");
  let id_art = id.replace("btn_", "");
  if (codigo) {
    if (codigo.trim().length > 0) {
      $.ajax({
        beforeSend: function () {},
        url: "data_ver_planificacion.php",
        type: "POST",
        data: { id_art: id_art, codigo: codigo.trim(), consulta: "actualiza_codsobre" },
        success: function (x) {
          if (x.trim() == "success"){
            MostrarModalOrden();
          }
          else{
            swal("Ocurrió un error al asignar el código", "", "error")
          }
        },
        error: function (jqXHR, estado, error) {
          swal("Ocurrió un error al asignar el código", error, "error")
        },
      });
    } else {
      showDialogSobre(id);
    }
  }
}

function showDialogSobre2(id) {
  let codigo = prompt("INGRESA CÓDIGO DE SOBRE");
  let id_art = id.replace("btn_", "");
  if (codigo) {
    if (codigo.trim().length > 0) {
      $.ajax({
        beforeSend: function () {},
        url: "data_ver_planificacion.php",
        type: "POST",
        data: { id_art: id_art, codigo: codigo.trim(), consulta: "actualiza_codsobre" },
        success: function (x) {
          if (x.trim() == "success"){
            MostrarModalEstado(id_art);
          }
          else{
            swal("Ocurrió un error al asignar el código", "", "error")
          }
        },
        error: function (jqXHR, estado, error) {
          swal("Ocurrió un error al asignar el código", error, "error")
        },
      });
    } else {
      showDialogSobre2(id);
    }
  }
}

function toggleSelection(objeto) {
  let tr = $(objeto);
  if (tr.hasClass("selected")) {
    tr.removeClass("selected");
    tr.css({ border: "1px solid #00000033" });
    tr.parent().css({ "background-color": "" });
    addToLista(objeto);
  } else {
    tr.addClass("selected");
    tr.css({ border: "3px solid #848484" });
    tr.parent().css({ "background-color": "#424242" });
    addToLista(objeto);
  }
}

function allowDrop(ev) {
  ev.preventDefault();
}

function dropear(ev) {
  let objetoselected;
  if (ev.target.tagName == "SPAN") {
    objetoselected = ev.target.parentNode;
  } else if (ev.target.tagName == "DIV") {
    objetoselected = ev.target;
  }
  let changedTouch = ev.changedTouches[0];
  let elem = document.elementFromPoint(
    changedTouch.clientX,
    changedTouch.clientY
  );
  if (elem.tagName == "TD") {
    if (elem.children.length == 0) {
      $(objetoselected).parent().css({ "background-color": "" });
      $(objetoselected).removeClass("selected");
      elem.appendChild(objetoselected);
      hasChanged = true;
      $("#guardar_btn").prop("disabled", false);
    }
  }
}

function drag(ev) {
  ev.dataTransfer.setData("text", ev.target.id);
  $(ev.target).parent().css({ "background-color": "" });
  $(ev.target).removeClass("selected");
}

function drop(ev) {
  ev.preventDefault();
  let data = ev.dataTransfer.getData("text");
  if (ev.target.tagName == "TD") {
    if (ev.target.children.length == 0) {
      ev.target.appendChild(document.getElementById(data));
      $("#guardar_btn").prop("disabled", false);
      let check = data.replace("id2_", "").replace("id_", "");
      let index = listaseleccionados.indexOf(check);
      if (index > -1) {
        listacolumnas.splice(index, 1);
      }
      $(data).removeClass("selected");
      listaseleccionados = listaseleccionados.filter((e) => e !== check);
      hasChanged = true;
    }
  }
}
