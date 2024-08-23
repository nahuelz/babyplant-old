let current_date = new Date();
let original_weeknumber = null;
let current_weeknumber = null;
let listaseleccionados = [];
let listacolumnas = [];
let id_articulopedido = null;
let estadoactual = null;
let hasChanged = false;

$(document).ready(function () {
  original_weeknumber = getWeekNumber(current_date);
  current_weeknumber = original_weeknumber;
  changeWeek(0);
  $("#guardar_btn").prop("disabled", true);
  $("#num_semana").html("Semana Nº " + current_weeknumber);

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
  if (day == 0) day = 6;
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
    let today = lista[i];
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
    html +=
      "<th id='dia_" +
      (i + 1).toString() +
      "' scope='col'>" +
      dias[i] +
      " " +
      today +
      "</th>";
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

function changeWeek(tipo, seleccionar) {
  if (tipo == 1) {
    current_date.setDate(current_date.getDate() + 7);
  } else if (tipo == -1) {
    current_date.setDate(current_date.getDate() - 7);
  } else if (tipo == 0) {
    current_date = new Date();
  } else {
    current_date = tipo;
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
  loadPedidos(seleccionar);

  let dia = new Date().getDay();
  if (current_weeknumber == original_weeknumber) {
    $("#TABLA")
      .find("td:nth-child(" + dia.toString() + ")")
      .css({ backgroundColor: "#CEECF5" });
    $("#dia_" + dia.toString()).css({ backgroundColor: "#CEECF5" });
  } else {
    $("#TABLA").find("td").css({ backgroundColor: "" });
    $("#dia_" + dia.toString()).css({ backgroundColor: "" });
  }
}

function loadPedidos(seleccionar) {
  listaseleccionados = [];
  listacolumnas = [];
  let fechas = getWeek(current_date, 2);
  $.ajax({
    beforeSend: function () {},
    url: "carga_planificacion_entregas.php",
    type: "POST",
    data: { fechas: fechas },
    success: function (x) {
      $("#tablitaa tbody tr td").html("");
      if (x.trim().length > 0) {
        let obj = JSON.parse(x);
        let sinpos = [];
        for (let j = 0; j < obj.length; j++) {
          for (let i = 0; i < obj[j].length; i++) {
            let indexrow = obj[j][i][3];
            if (indexrow != null) {
              indexrow = parseInt(indexrow);
              $("#TABLA")
                .find(
                  "tr:eq(" +
                    (indexrow + 1).toString() +
                    ") td:eq(" +
                    j.toString() +
                    ")"
                )
                .html(MakeBox(obj[j][i]));
            } else {
              let aux = obj[j][i];
              aux.push(j);
              sinpos.push(aux);
            }
          }
        }
        for (let x = 0; x < 6; x++) {
          let indicito = 0;
          let indices = [];
          $("tr").each(function () {
            let texto = $(this).find("td:eq(" + x.toString() + ")");
            let largo = texto.text().trim().length;
            if (largo == 0) {
              indices.push(indicito);
            }
            indicito++;
          });
          indices.shift();

          for (let i = 0; i < sinpos.length; i++) {
            if (indices.length > 0 && sinpos[i][10] == x) {
              let colindex = sinpos[i][10];
              $("#TABLA")
                .find(
                  "tr:eq(" +
                    indices[0].toString() +
                    ") td:eq(" +
                    colindex.toString() +
                    ")"
                )
                .html(MakeBox(sinpos[i]));
              indices.shift();
            }
          }
        }
        if (seleccionar != undefined && seleccionar != null) {
          let objeto = $("#id2_" + seleccionar);
          if (objeto != undefined && objeto != null) {
            SmoothScrollTo(objeto, 500);
            toggleSelection(objeto, 2);
          }
        }
      }
    },
    error: function (jqXHR, estado, error) {},
  });
  DeseleccionarTodo();
  hasChanged = false;
}

function MakeBox(data) {
  let color;
  let producto = data[0];
  let estado = data[2];
  let revision = data[8];
  let solucion = data[9];
  let cant_bandejas = data[4].toString();
  let tipos_revision = [
    "",
    "FALLA GERMINACIÓN",
    "GOLPE",
    "PAJARO",
    "RATA",
    "REALIZAR DESPUNTE",
    "USO PARA INJERTO",
    "D1 REALIZADO",
    "VER OBSERV.",
  ];
  let tipos_solucion = [
    "",
    "D1 CANCELADO",
    "CLASIFICACIÓN",
    "REPIQUE",
    "RESIEMBRA",
    "DEJAR FALLAS 12",
  ];

  if (producto.includes("TOMATE")) {
    if (estado == 4) {
      color = "#FFACAC";
    } else if (estado == 5) {
      color = "#FA5858";
    } else if (estado > 5) {
      color = "#A4A4A4";
    }
  } else if (producto.includes("PIMIENTO")) {
    if (estado == 4) {
      color = "#BAE1A2";
    } else if (estado == 5) {
      color = "#5AEE00";
    } else if (estado > 5) {
      color = "#A4A4A4";
    }
  } else if (producto.includes("BERENJENA")) {
    if (estado == 4) {
      color = "#e1c9ff";
    } else if (estado == 5) {
      color = "#ad6eff";
    } else if (estado > 5) {
      color = "#A4A4A4";
    }
  } else if (producto.includes("LECHUGA")) {
    if (estado == 4) {
      color = "#D7FFBC";
    } else if (estado == 5) {
      color = "#77FF1C";
    } else if (estado > 5) {
      color = "#A4A4A4";
    }
  } else if (producto.includes("ACELGA")) {
    if (estado == 4) {
      color = "#BFDCBC";
    } else if (estado == 5) {
      color = "#348D2B";
    } else if (estado > 5) {
      color = "#A4A4A4";
    }
  } else if (producto.includes("REMOLACHA")) {
    if (estado == 4) {
      color = "#eba5b5";
    } else if (estado == 5) {
      color = "#ee204e";
    } else if (estado > 5) {
      color = "#A4A4A4";
    }
  } else if (
    producto.includes("COLES") ||
    producto.includes("HINOJO") ||
    producto.includes("APIO")
  ) {
    if (estado == 4) {
      color = "#A9D0F5";
    } else if (estado == 5) {
      color = "#58ACFA";
    } else if (estado > 5) {
      color = "#A4A4A4";
    }
  } else if (producto.includes("VERDEO") || producto.includes("PUERRO")) {
    if (estado == 4) {
      color = "#F5DA81";
    } else if (estado == 5) {
      color = "#F7BE81";
    } else if (estado > 5) {
      color = "#A4A4A4";
    }
  } else {
    color = "#E6E6E6";
  }

  clase = "pendiente";
  if (estado == 4) {
    clase = "pendiente";
  } else if (estado == 5) {
    clase = "planificado";
  } else if (estado == 6) {
    clase = "entregadoparcialmente";
    color = "#FFFF00";
  } else if (estado == 7) {
    clase = "entregadocompletamente";
  } else if (estado == 8) {
    clase = "enstock";
    color = "#FAAC58";
  }
  let id_orden1 = data[6];
  if (id_orden1 == null) {
    id_orden1 = "STOCK";
  }

  if (revision != null && solucion == null) {
    color = "#F7D358;";
  }

  if (solucion != null) {
    color = "#A9F5A9;";
  }

  producto = producto.substring(producto.indexOf("|") + 1);
  let html =
    "<div id='id2_" +
    data[1] +
    "' class='cajita " +
    clase +
    "' draggable='true' ondragstart='drag(event);' onClick='toggleSelection(this,1)' style='touch-action: none;background-color:" +
    color +
    "; cursor: pointer; width:100%;font-size:1.2em'";
  html +=
    " ontouchmove='dropear(event)' ondblclick='MostrarModalEstado(" +
    data[1] +
    ")'>";
  html +=
    "<span id='id_" +
    data[1] +
    "'style='font-weight:bold; word-wrap: break-word;'>[" +
    id_orden1 +
    "] " +
    producto +
    "<br>" +
    cant_bandejas +
    " bandejas <br>" +
    "<span class='spancliente cliente_" +
    data[7] +
    "'>" +
    data[5];

  if (revision != null && solucion == null) {
    html +=
      "<br><span style='color:red'>" + tipos_revision[revision] + "</span>";
  }

  if (solucion != null) {
    html +=
      "<br><span style='color:red'>" +
      tipos_revision[revision] +
      "</span><br><span style='color:#0080FF'>" +
      tipos_solucion[solucion] +
      "</span>";
  }

  if (estado == 5) {
    html += `
          <div class="bg-light m-1"><p class='text-danger' style='font-size:0.8em'>
            YA PLANIFICADO
          </p></div>`;
  } else if (estado == 6) {
    html += `
          <div class="bg-light m-1"><p class='text-primary' style='font-size:0.8em'>
            ENTREGADO PARCIALMENTE
          </p></div>`;
  }

  html += "</span></div>";
  return html;
}

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

function toggleSelection(objeto, tipo) {
  if (tipo == 1) {
    let color = "#424242";
  } else if (tipo == 2) {
    let color = "#A5DF00";
  }
  let tr = $(objeto);
  if (tr.hasClass("selected")) {
    tr.removeClass("selected");
    tr.css({ border: "1px solid #00000033" });
    tr.parent().css({ "background-color": "" });
    tr.parent().css({ padding: "0" });
    addToLista(objeto);
  } else {
    tr.addClass("selected");
    tr.css({ border: "3px solid #848484" });
    tr.parent().css({ "background-color": color });
    tr.parent().css({ padding: "10px" });
    addToLista(objeto);
  }
}

function dropear(ev) {
  let objetoselected;

  if (ev.target.tagName == "SPAN") {
    objetoselected = ev.target.parentNode;
  } else if (ev.target.tagName == "DIV") {
    objetoselected = ev.target;
  }
  //ev.preventDefault();
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
  if (arraycita.length > 0) {
    const jsonarray = JSON.stringify(arraycita);
    $.ajax({
      beforeSend: function () {},
      url: "data_ver_planentregas.php",
      type: "POST",
      data: { jsonarray: jsonarray, consulta: "actualiza_filas" },
      success: function (x) {
        if (x.trim() == "success") {
          loadPedidos();
          hasChanged = false;
          swal("Los cambios se guardaron correctamente!", "", "success");
          $("#guardar_btn").prop("disabled", true);
        } else {
          swal("Ocurrió un error al guardar los cambios.", x, "error");
        }
      },
      error: function (jqXHR, estado, error) {
        swal("Ocurrió un error al guardar los cambios.", error, "error");
      },
    });
  }
}

function eliminar_art(btn) {
  let row = btn.parentNode.parentNode;
  row.parentNode.removeChild(row);
}

function eliminar_item(id) {
  $(".tr-" + id).remove();
}

function checkCantidad(objeto) {
  let max = parseInt($(objeto).attr("max"));
  let valor = parseInt($(objeto).val());
  if (valor > max) {
    $(objeto).val(max.toString());
  }

  if (valor < 0) {
    $(objeto).val("0");
  }
}

function calcularSubtotal(objeto) {
  let monto = 0;
  $("#tabla_entregainmediata > tbody  > tr").each(function (index, tr) {
    const precio = $(tr).find(".preciobox").val().trim();
    const cantidad = $(tr).find(".cantidadbox").val().trim();
    if (
      precio.length > 0 &&
      cantidad.length > 0 &&
      !isNaN(precio) &&
      !isNaN(cantidad)
    ) {
      monto += parseInt(precio) * parseInt(cantidad);
    } else {
      monto = 0;
    }
  });

  const tipodescuento = $(".select-descuento").val();
  let valordescuento = $(".input-descuento").val().trim();
  if (
    valordescuento.length > 0 &&
    !isNaN(valordescuento) &&
    monto > 0 &&
    parseInt(valordescuento) > 0
  ) {
    if (tipodescuento == "porcentual" && parseInt(valordescuento) < 100) {
      monto -= Math.trunc((monto * parseInt(valordescuento)) / 100);
    } else if (tipodescuento == "fijo" && parseInt(valordescuento) <= monto) {
      monto = monto - parseInt(valordescuento);
    } else {
      monto = 0;
    }
  }

  if (monto > 0) {
    $(".input-subtotal").val(monto.toString());
  } else {
    $(".input-subtotal").val("");
  }
}

function MostrarModalOrden(id) {
  if (listaseleccionados.length > 0) {
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
      if (
        document
          .getElementById("id2_" + listaseleccionados[i])
          .className.split(" ")[1] != "pendiente" &&
        document
          .getElementById("id2_" + listaseleccionados[i])
          .className.split(" ")[1] != "entregadoparcialmente" &&
        document
          .getElementById("id2_" + listaseleccionados[i])
          .className.split(" ")[1] != "enstock"
      ) {
        puede = false;
        error =
          "No se puede generar una orden de entrega de un producto ya entregado!";
        break;
      }
    }

    if (hasChanged == true) {
      puede = false;
      error = "Antes de generar la orden debes clickear en Guardar Posiciones!";
    }

    if (puede == true) {
      consulta = "orden";
      let jsonarray = JSON.stringify(listaseleccionados);
      $.ajax({
        beforeSend: function () {},
        url: "data_ver_planentregas.php",
        type: "POST",
        data: { jsonarray: jsonarray, consulta: consulta },
        success: function (x) {
          $("#tabla_ordenentrega > tbody").html(x);
        },
        error: function (jqXHR, estado, error) {
          $("#tabla_ordenentrega").html(
            "Hubo un error al cargar la información del pedido"
          );
        },
      });
      $("#currentfecha_txt").val("");
      $("#ModalVerOrden").modal("show");
    } else {
      swal("ERROR", error, "error");
    }
  }
}

function ModalEntregaInmediata() {
  let error = "";
  let puede = true;
  let seleccionados = document.getElementsByClassName("selected");
  let id_cliente = null;

  if (seleccionados.length > 0 || seleccionados != undefined) {
    let id_clienteinicial = $(seleccionados[0])
      .find(".spancliente")
      .attr("class")
      .split(/\s+/)[1];
    for (let i = 1; i < seleccionados.length; i++) {
      id_cliente = $(seleccionados[i])
        .find(".spancliente")
        .attr("class")
        .split(/\s+/)[1];
      if (id_cliente != id_clienteinicial) {
        puede = false;
        error = "Los pedidos seleccionados deben pertenecer al mismo cliente";
        break;
      }
    }
    for (let i = 0; i < seleccionados.length; i++) {
      let estado = $(seleccionados[i]).attr("class");
      if (
        estado.includes("pendiente") == false &&
        estado.includes("entregadoparcialmente") == false &&
        estado.includes("enstock") == false
      ) {
        puede = false;
        error = "Algún pedido de los seleccionados ya se entregó!";
        break;
      }
    }

    if (hasChanged == true) {
      puede = false;
      error = "Antes de generar la orden debes clickear en Guardar Posiciones!";
    }
    if (puede == true) {
      let consulta = "orden_inmediata";

      let jsonarray = JSON.stringify(listaseleccionados);

      $.ajax({
        beforeSend: function () {},
        url: "data_ver_planentregas.php",
        type: "POST",
        data: { jsonarray: jsonarray, consulta: consulta },
        success: function (x) {
          $("#tabla_entregainmediata > tbody").html(x);
          $(".cantidadbox").first().focus();
          const idCliente = $(seleccionados[0])
            .find(".spancliente")
            .attr("class")
            .split(/\s+/)[1]
            .replace("cliente_", "");
          $("#ModalEntregaInmediata").attr("x-id-clienteoriginal", idCliente);
          $("#ModalEntregaInmediata").attr("x-id-cliente-entrega", idCliente);

          $(".label-nombrecliente").html(
            "Cliente: " + $(seleccionados[0]).find(".spancliente").text()
          );
          $("#ModalEntregaInmediata").modal("show");
        },
        error: function (jqXHR, estado, error) {
          $("#tabla_entregainmediata").html(
            "Hubo un error al cargar la información del pedido"
          );
        },
      });
      $("#ModalBuscarPedido").modal("hide");
    } else {
      swal(error, "", "error");
    }
  }
}

function EntregaInmediataBusqueda(id) {
  let seleccionados = document.getElementsByClassName("selected2");
  let listaseleccionados = [];
  if (seleccionados.length > 0 || seleccionados != undefined) {
    for (let i = 0; i < seleccionados.length; i++) {
      listaseleccionados.push(
        $(seleccionados[i]).attr("id").replace("art_", "")
      );
    }

    let consulta = "orden_inmediata";
    let jsonarray = JSON.stringify(listaseleccionados);
    $.ajax({
      beforeSend: function () {},
      url: "data_ver_planentregas.php",
      type: "POST",
      data: { jsonarray: jsonarray, consulta: consulta },
      success: function (x) {
        $("#ModalEntregaInmediata").attr(
          "x-id-clienteoriginal",
          $("#ModalBuscarPedido").attr("x-id-cliente")
        );

        $("#ModalEntregaInmediata").attr(
          "x-id-cliente-entrega",
          $("#ModalBuscarPedido").attr("x-id-cliente")
        );

        $(".label-nombrecliente").html(
          "Cliente: " + $("#select_cliente2").find("option:selected").text()
        );

        $("#tabla_entregainmediata > tbody").html(x);
        $("#ModalBuscarPedido").modal("hide");
        $("#ModalEntregaInmediata").modal("show");
      },
      error: function (jqXHR, estado, error) {
        $("#tabla_entregainmediata").html(
          "Hubo un error al cargar la información del pedido"
        );
      },
    });
  }
}

function BusquedaEnAgenda() {
  let seleccionados = document.getElementsByClassName("selected2");
  listaseleccionados = [];
  if (seleccionados && seleccionados.length) {
    for (let i = 0; i < seleccionados.length; i++) {
      if ($(seleccionados[i]).attr("x-estado") == "5"){
        swal("Alguno de los pedidos seleccionados ya está en Agenda!", "Debes reprogramar la fecha o eliminar el Pedido en la sección Ver Agenda", "error")
        return;
      }
      listaseleccionados.push(
        $(seleccionados[i]).attr("x-id-artpedido")
      );
    }
    const jsonarray = JSON.stringify(listaseleccionados);
    $.ajax({
      beforeSend: function () {},
      url: "data_ver_planentregas.php",
      type: "POST",
      data: { jsonarray: jsonarray, consulta: "orden" },
      success: function (x) {
        $("#tabla_ordenentrega > tbody").html(x);
      },
      error: function (jqXHR, estado, error) {
        $("#tabla_ordenentrega").html(
          "Hubo un error al cargar la información del pedido"
        );
      },
    });

    $("#ModalVerOrden").modal("show");
    $("#ModalBuscarPedido").modal("hide");
  }
}

function guardarEntregaInmediata() {
  const id_clienteoriginal = $("#ModalEntregaInmediata").attr(
    "x-id-clienteoriginal"
  );
  const id_clienteentrega = $("#ModalEntregaInmediata").attr(
    "x-id-cliente-entrega"
  );
  if (id_clienteentrega == id_clienteoriginal) {
    funcGuardarEntregaInmediataNormal(id_clienteoriginal);
  } else {
    funcGuardarEntregaInmediataCambioCliente(id_clienteentrega);
  }
}

function funcGuardarEntregaInmediataNormal(id_cliente) {
  let cantidades = document.getElementsByClassName("cantidadbox");
  let precios = document.getElementsByClassName("preciobox");
  let idarts = document.getElementsByClassName("id_artpedidos");

  const cliente = $(".label-nombrecliente").text();
  if (idarts.length <= 6) {
    let puede = true;
    let articulos = [];
    for (let i = 0; i < cantidades.length; i++) {
      let valor = $(cantidades[i]).val().trim();
      let precio = $(precios[i]).val().trim();
      let max = $(cantidades[i]).attr("max");

      if (isNaN(valor) || valor.length == 0 || parseInt(valor) < 1) {
        puede = false;
        break;
      } else {
        let tipo_entrega = "";
        if (parseInt(valor) < parseInt(max)) {
          tipo_entrega = "parcial";
        } else {
          tipo_entrega = "completa";
        }
        articulos.push({
          id_artpedido: $(idarts[i]).attr("id").replace("art_", ""),
          cantidad: valor, //CANTIDAD
          tipo_entrega: tipo_entrega,
          precio: precio,
        });
      }
    }

    const subtotal = $(".input-subtotal").val().trim();
    const tipodescuento = $(".select-descuento").val();
    const descuento = $(".input-descuento").val().trim();

    if (isNaN(subtotal) || !subtotal.length || parseInt(subtotal) < 0) {
      puede = false;
    }

    if (articulos.length && puede) {
      $.ajax({
        url: "data_ver_agenda.php",
        type: "POST",
        data: { id_cliente: id_cliente, consulta: "cargar_dataremito" },
        success: function (x) {
          
          if (x && x.length) {
            const arrData = JSON.parse(x);
            const {telefono, id_remito} = arrData;
            const codigo = funcGeneraRemitoInmediato(
              id_cliente,
              cliente,
              subtotal,
              descuento.length && !isNaN(descuento) ? descuento : null,
              tipodescuento,
              telefono ? telefono : "",
              id_remito ? id_remito : ""
            );

            const jsonarray = JSON.stringify(articulos);

            $.ajax({
              beforeSend: function () {},
              url: "data_ver_planentregas.php",
              type: "POST",
              data: {
                id_cliente: id_cliente,
                jsonarray: jsonarray,
                consulta: "entrega_inmediata",
                subtotal: subtotal,
                tipodescuento: tipodescuento,
                descuento:
                  isNaN(descuento) || !descuento.length ? 0 : descuento,
                codigo: codigo,
              },
              success: function (x) {
                //ALTER TABLE entregas ADD CONSTRAINT fk_id_remito FOREIGN KEY (id_remito) REFERENCES remitos(id_remito);
                console.log(x);
                if (x.trim() == "success") {
                  $("#ModalEntregaInmediata").modal("hide");
                  $("#ModalEntregaExitosa").modal("show");
                  CerrarModalBuscar();
                } else {
                  swal("Ocurrió un error al guardar la Entrega", x, "error");
                }
              },
              error: function (jqXHR, estado, error) {
                swal("ERROR!", error.toString(), "error");
              },
            });
          } else {
            swal("Ocurrió un error al generar el remito", "", "error");
          }
        },
      });
    } else {
      swal(
        "La cantidad a entregar y los precios deben ser mayores a cero!",
        "",
        "error"
      );
    }
  } else {
    swal("Sólo se puede generar remitos de hasta 6 productos!", "", "error");
  }
}

function funcGuardarEntregaInmediataCambioCliente(id_cliente) {
  let cantidades = document.getElementsByClassName("cantidadbox");
  let precios = document.getElementsByClassName("preciobox");
  let idarts = document.getElementsByClassName("id_artpedidos");

  const cliente = $(".label-nombrecliente").text();
  if (idarts.length <= 6) {
    let puede = true;
    let articulos = [];
    for (let i = 0; i < cantidades.length; i++) {
      let valor = $(cantidades[i]).val().trim();
      let precio = $(precios[i]).val().trim();
      let max = $(cantidades[i]).attr("max");

      if (isNaN(valor) || valor.length == 0 || parseInt(valor) < 1) {
        puede = false;
        break;
      } else {
        let tipo_entrega = "";
        if (parseInt(valor) < parseInt(max)) {
          tipo_entrega = "parcial";
        } else {
          tipo_entrega = "completa";
        }
        articulos.push({
          id_artpedido: $(idarts[i]).attr("id").replace("art_", ""),
          cantidad: valor, //CANTIDAD
          tipo_entrega: tipo_entrega,
          precio: precio,
        });
      }
    }

    const subtotal = $(".input-subtotal").val().trim();
    const tipodescuento = $(".select-descuento").val();
    const descuento = $(".input-descuento").val().trim();

    if (isNaN(subtotal) || subtotal.length == 0 || parseInt(subtotal) < 0) {
      puede = false;
    }

    if (articulos.length > 0 && puede == true) {
      $.ajax({
        url: "data_ver_agenda.php",
        type: "POST",
        data: { id_cliente: id_cliente, consulta: "cargar_dataremito" },
        success: function (x) {
          const arrData = JSON.parse(x);
          if (arrData && arrData.length) {
            const {telefono, id_remito} = arrData;
            const codigo = funcGeneraRemitoInmediato(
              id_cliente,
              cliente,
              subtotal,
              descuento.length > 0 && !isNaN(descuento) ? descuento : null,
              tipodescuento,
              telefono != null ? telefono : "",
              id_remito != null ? id_remito : ""
            );

            let jsonarray = JSON.stringify(articulos);

            $.ajax({
              beforeSend: function () {},
              url: "data_ver_planentregas.php",
              type: "POST",
              data: {
                id_cliente: id_cliente,
                jsonarray: jsonarray,
                consulta: "entrega_inmediata_cambiocliente",
                subtotal: subtotal,
                tipodescuento: tipodescuento,
                descuento:
                  isNaN(descuento) || descuento.length == 0 ? 0 : descuento,
                codigo: codigo,
              },
              success: function (x) {
                console.log(x);
                if (!x.includes("ERROR:")) {
                  $("#ModalEntregaInmediata").modal("hide");
                  $("#ModalEntregaExitosa").modal("show");
                  CerrarModalBuscar();
                } else {
                  swal("Ocurrió un error", x, "error");
                }
              },
              error: function (jqXHR, estado, error) {
                swal("ERROR!", error.toString(), "error");
              },
            });
          } else {
            swal("Ocurrió un error al generar el remito", "", "error");
          }
        },
      });
    } else {
      swal(
        "La cantidad a entregar y los precios deben ser mayores a cero!",
        "",
        "error"
      );
    }
  } else {
    swal("Sólo se puede generar remitos de hasta 6 productos!", "", "error");
  }
}

function printRemito(tipo) {
  if (tipo == 1) {
    document.getElementById("ocultar").style.display = "none";
    document.getElementById("mi-ventana").style.display = "block";
    setTimeout("window.print();printRemito(2);", 500);
  } else {
    document.getElementById("ocultar").style.display = "block";
    document.getElementById("mi-ventana").style.display = "none";
    document.title = "Planificación de Entregas";
  }
}

function CerrarModalOrden() {
  $("ModalVerOrden").modal("hide");
}
function CerrarModalEntregaInmediata() {
  $("#ModalEntregaInmediata").modal("hide");
  DeseleccionarTodo();
}

function sendToWeek(tipo) {
  let r = confirm(
    "Estás seguro de enviar a otra semana los productos seleccionados?"
  );
  if (r == true) {
    let fechas = getWeek(current_date, 2);
    fechas = JSON.parse(fechas);
    let fecha = fechas[1].split("/");
    fecha = fecha[2] + "/" + fecha[1] + "/" + fecha[0];
    let myDate = new Date(fecha);
    let dia = myDate.getDay();
    if (tipo == 1) myDate.setDate(myDate.getDate() + 6);
    else if (tipo == 0) myDate.setDate(myDate.getDate() - 6);

    dia = myDate.getDay();
    let nuevafecha =
      ("0" + myDate.getDate()).slice(-2) +
      "/" +
      ("0" + (myDate.getMonth() + 1)).slice(-2) +
      "/" +
      myDate.getFullYear();

    if (listaseleccionados.length > 0) {
      let jsonarray = JSON.stringify(listaseleccionados);
      $.ajax({
        beforeSend: function () {},
        url: "data_ver_planentregas.php",
        type: "POST",
        data: {
          jsonarray: jsonarray,
          consulta: "change_week",
          nuevafecha: nuevafecha,
        },
        success: function (x) {
          if (x.trim() == "success") {
            loadPedidos();
            swal(
              "Cambiaste de semana a los productos seleccionados!",
              "",
              "success"
            );
          } else {
            swal("Ocurrió un error al realizar el cambio", x, "error");
          }
        },
        error: function (jqXHR, estado, error) {
          swal("Ocurrió un error al realizar el cambio", error, "error");
        },
      });
    } else {
      swal("Debes seleccionar algún producto", "", "error");
    }
  }
}

function DeseleccionarTodo() {
  $(".cajita").removeClass("selected");
  $(".cajita").css({ border: "1px solid #00000033" });
  $(".cajita").parent().css({ "background-color": "" });
  listaseleccionados = [];
  listacolumnas = [];
}

function allowDrop(ev) {
  ev.preventDefault();
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

async function GuardarOrden() {
  let fecha = $("#currentfecha_txt").val();
  if (fecha.trim().length < 1) {
    swal("Selecciona una fecha para agendar las entregas", "", "error");
  } else {
    let articulos = [];
    let puede = true;
    await $("#tabla_ordenentrega > tbody > tr").each(function (i, tr) {
      const domicilio = $(tr).find("td:eq(7)").text().trim();
      if (!domicilio.length){
        puede = false;
        return;
      }
      articulos.push({
        id_artpedido: $(tr).attr("x-id-artpedido"),
        domicilio: domicilio,
        cantidad: $(tr).find("td:eq(8)").text(),
        telefono: $(tr).find("td:eq(9)").text(),
      });
    });

    if (articulos.length && puede) {
      CerrarModalOrden();
      CerrarModalBuscar();
      const jsonarray = JSON.stringify(articulos);
      $.ajax({
        beforeSend: function () {},
        url: "data_ver_planentregas.php",
        type: "POST",
        data: { jsonarray: jsonarray, consulta: "agrega_orden", fecha: fecha },
        success: function (x) {
          if (x.trim() == "success") {
            loadPedidos();
            swal(
              "La orden de entrega fue generada correctamente!",
              "",
              "success"
            );
          } else {
            swal("Ocurrió un error al generar la Órden", x, "error");
          }
        },
        error: function (jqXHR, estado, error) {
          swal("Ocurrió un error al generar la Órden", error, "error");
        },
      });
    } else {
      swal(
        "Debes establecer los datos de entrega! (Domicilio, telefono, cantidad a entregar)",
        "",
        "error"
      );
    }
  }
}

function CerrarModalOrden() {
  $("#ModalVerOrden").modal("hide");
  DeseleccionarTodo();
}

function MostrarModalModoEntrega(objeto) {
  let id_cliente = $(objeto)
    .closest("tr")
    .find("td:eq(4)")
    .attr("id")
    .replace("cliente_", "");

  let band_pedidas = $(objeto).closest("tr").find("td:eq(2)").text();
  $("#id_artpedidoentrega").html($(objeto).attr("id").replace("boton_", ""));

  $("#cantidad_entregar").val(band_pedidas);
  $("#cantidad_entregar").attr({ max: band_pedidas });

  $("#id_cliente").html(id_cliente);
  $("#select_modoentrega").val("default").selectpicker("refresh");
  $("#ModalModoEntrega").modal("show");
  $("#domiciliocliente_txt").val("");
  $("#telefono_receptor").val("");

  setDomicilio();
}

function CerrarModalModoEntrega() {
  $("#ModalModoEntrega").modal("hide");
}

function modalCambiarCliente() {
  carga_clientes("#select_cambiarcliente");
  $("#ModalEntregaInmediata").modal("hide");
  $("#ModalCambiarCliente").modal("show");
}

function cerrarModalCambiarCliente() {
  $("#ModalCambiarCliente").modal("hide");
  $("#ModalEntregaInmediata").modal("show");
}

function guardarCambiarCliente() {
  const idCliente = $("#select_cambiarcliente option:selected").val();
  $("#ModalEntregaInmediata").attr("x-id-cliente-entrega", idCliente);
  $(".label-nombrecliente").html(
    $("#select_cambiarcliente option:selected").text()
  );
  cerrarModalCambiarCliente();
}

function setDomicilio() {
  let consulta = "domicilio";
  let id_cliente = $("#id_cliente").text();
  $.ajax({
    beforeSend: function () {},
    url: "data_ver_planentregas.php",
    type: "POST",
    data: { id_cliente: id_cliente, consulta: consulta },
    success: function (x) {
      $("#domiciliocliente_txt2").val(x);
    },
    error: function (jqXHR, estado, error) {
      $("#domiciliocliente_txt2").val("NO SE PUDO OBTENER EL DOMICILIO");
    },
  });
}

function GuardarModoEntrega() {
  let id_artpedido = $("#id_artpedidoentrega").text();
  let domicilio = $("#domiciliocliente_txt2").val().trim();
  let telefono = $("#telefono_receptor").val().trim();
  if (domicilio.length == 0) {
    swal("ERROR", "Debes ingresar un domicilio", "error");
  } else {
    let cantidad = $("#cantidad_entregar").val();
    if (parseInt(cantidad) > parseInt($("#cantidad_entregar").attr("max"))) {
      swal(
        "No se puede entregar más bandejas de las que pidió el cliente!",
        "",
        "error"
      );
    } else if (parseInt(cantidad) < 1) {
      swal("La cantidad no puede ser menor a 1", "", "error");
    } else {
      $("#modo_" + id_artpedido).html(domicilio.toUpperCase());
      $("#entregar_" + id_artpedido).html(cantidad);
      $("#telefono_" + id_artpedido).html(telefono);
    }
  }

  CerrarModalModoEntrega();
}

function cambiarCliente(id_artpedido) {
  $("#id_artpedidohide").html(id_artpedido);
  $("#select_modocambio").val("default").selectpicker("refresh");
  $(".selected2").removeClass("selected2");
  document.getElementById("modo_permutar").style.display = "none";
  document.getElementById("modo_asignarcliente").style.display = "none";
  $("#ModalCambioCliente").modal("show");
  $("#ModalVerEstado").modal("hide");
}

function CerrarModalCambioCliente() {
  $("#ModalCambioCliente").modal("hide");
  $("#ModalVerEstado").modal("show");
}

function setModoCambio() {
  let tipo_entrega = parseInt(
    $("#select_modocambio").find("option:selected").val()
  );
  if (tipo_entrega == 1) {
    document.getElementById("modo_asignarcliente").style.display = "none";
    let consulta = "cargar_ordenes_similares";
    let id_artpedido = $("#id_artpedidohide").text();
    $.ajax({
      beforeSend: function () {},
      url: "data_ver_planentregas.php",
      type: "POST",
      data: { id_artpedido: id_artpedido, consulta: consulta },
      success: function (x) {
        $("#tabla_ordenes_similares tbody").html(x);
        document.getElementById("modo_permutar").style.display = "block";
      },
      error: function (jqXHR, estado, error) {
        $("#tabla_ordenes_similares tbody").html(
          "NO SE PUDO OBTENER EL DOMICILIO"
        );
        document.getElementById("modo_permutar").style.display = "block";
      },
    });
  } else {
    document.getElementById("modo_permutar").style.display = "none";
    if (tipo_entrega == 2) {
      carga_clientes("#select_cliente");
      document.getElementById("modo_asignarcliente").style.display = "block";
    } else {
      document.getElementById("modo_asignarcliente").style.display = "none";
      document.getElementById("modo_permutar").style.display = "none";
    }
  }
}

function setSelected(objeto) {
  let tr = $(objeto);
  if (tr.hasClass("selected2")) {
    tr.removeClass("selected2");
  } else {
    $(".selected2").removeClass("selected2");
    tr.addClass("selected2");
  }
}

function GuardarCambioCliente() {
  let seleccionado = document.getElementsByClassName("selected2")[0];
  let tipo_entrega = parseInt(
    $("#select_modocambio").find("option:selected").val()
  );
  if (tipo_entrega == 1) {
    if (seleccionado != undefined) {
      CerrarModalCambioCliente();
      $("#ModalVerEstado").modal("hide")
      let id_artpedidoseleccionado = $(seleccionado)
        .find("td:eq(0)")
        .attr("id")
        .replace("art_", "");
      let id_clienteseleccionado = $(seleccionado)
        .find("td:eq(3)")
        .attr("id")
        .replace("cliente_", "");
      let id_artpedido = $("#id_artpedidohide").text();
      let id_clienteoriginal = $("#id_clienteoriginal").text();
      consulta = "permuta_clientes";
      $.ajax({
        beforeSend: function () {},
        url: "data_ver_planentregas.php",
        type: "POST",
        data: {
          consulta: consulta,
          id_artpedido: id_artpedido,
          id_clienteoriginal: id_clienteoriginal,
          id_artpedidoseleccionado: id_artpedidoseleccionado,
          id_clienteseleccionado: id_clienteseleccionado,
        },
        success: function (x) {
          swal("El pedido fue modificado correctamente!", "", "success");
          loadPedidos();
        },
        error: function (jqXHR, estado, error) {
          alert(estado + " " + error);
        },
      });
    } else {
      swal("ERROR", "Debes seleccionar un pedido!", "error");
    }
  } else if (tipo_entrega == 2) {
    CerrarModalCambioCliente();
    $("#ModalVerEstado").modal("hide")
    let id_artpedido = $("#id_artpedidohide").text();
    let id_clienteoriginal = $("#id_clienteoriginal").text();
    let id_cliente = $("#select_cliente").find("option:selected").val();
    if (id_cliente.trim().length > 0) {
      consulta = "asigna_cliente";
      $.ajax({
        beforeSend: function () {},
        url: "data_ver_planentregas.php",
        type: "POST",
        data: {
          consulta: consulta,
          id_artpedido: id_artpedido,
          id_cliente: id_cliente,
          id_clienteoriginal: id_clienteoriginal,
        },
        success: function (x) {
          swal("El cliente fue modificado correctamente!", "", "success");
          loadPedidos();
        },
        error: function (jqXHR, estado, error) {
          alert(estado + " " + error);
        },
      });
    } else {
      swal("ERROR", "Debes seleccionar un cliente!", "error");
    }
  }
}

function printOrdenEntrega(tipo) {
  if (tipo == 1) {
    func_printOrdenEntrega();
    document.getElementById("ocultar").style.display = "none";
    document.getElementById("mi-ventana").style.display = "block";
  } else {
    document.getElementById("ocultar").style.display = "block";
    document.getElementById("mi-ventana").style.display = "none";
  }
}

function func_printOrdenEntrega() {
  let direccion =
    "<img src='dist/img/babyplant.png' width='160' height='70'></img>";
  $("#mi-ventana").html(direccion);
  let now = new Date();
  let datetime =
    now.getDate() +
    "/" +
    (now.getMonth() + 1) +
    "/" +
    now.getFullYear() +
    " " +
    now.getHours() +
    ":" +
    (now.getMinutes() < 10 ? "0" + now.getMinutes() : now.getMinutes());
  $("#mi-ventana").append(
    "<span style='font-size:16px;font-weight:bold;'>ORDEN DE ENTREGA " +
      datetime +
      "</span>"
  );
  $("#mi-ventana").append(
    document.getElementById("tablitaordenentrega").innerHTML
  );
  $("#mi-ventana").find("th:last").remove();
  $("#mi-ventana")
    .find("tr")
    .each(function () {
      $(this).find("td:last").remove();
    });
  setTimeout("window.print();printOrdenEntrega(2, null);", 500);
}

function setFilter() {
  let parseInput = function (val) {
    let floatValue = parseFloat(val);
    return isNaN(floatValue) ? "" : floatValue;
  };
  $("#input_pago")
    .keyup(function () {
      let value = $(this).val() + "";
      if (value[value.length - 1] !== ".") {
        $(this).val(parseInput(value));
      }
    })
    .focusout(function () {
      $(this).val(parseInput($(this).val() + ""));
    });
}

function setDomicilio2() {
  let tipo_entrega = parseInt(
    $("#select_modoentrega5").find("option:selected").val()
  );
  if (tipo_entrega == 2) {
    let consulta = "domicilio";
    let id_cliente = $("#id_clienteorden").text();
    $.ajax({
      beforeSend: function () {},
      url: "data_ver_planentregas.php",
      type: "POST",
      data: { id_cliente: id_cliente, consulta: consulta }, //HASTA ACA LLEGUEEE
      success: function (x) {
        $("#domiciliocliente_txt4").val(x);
        document.getElementById("contenedor_domicilio").style.display = "block";
      },
      error: function (jqXHR, estado, error) {
        $("#domiciliocliente_txt4").val("NO SE PUDO OBTENER EL DOMICILIO");
        document.getElementById("contenedor_domicilio").style.display = "block";
      },
    });
  } else {
    document.getElementById("contenedor_domicilio").style.display = "none";
  }
}

function carga_clientes(objeto) {
  $.ajax({
    beforeSend: function () {
      $(objeto).html("Cargando lista de clientes...");
    },
    url: "pone_clientes.php",
    type: "POST",
    data: null,
    success: function (x) {
      $(objeto).html(x).selectpicker("refresh");
    },
    error: function (jqXHR, estado, error) {},
  });
}

function buscar_parciales() {
  carga_clientes("#select_cliente2");
  $("#select_cliente2").on(
    "changed.bs.select",
    function (e, clickedIndex, newValue, oldValue) {
      cargar_parciales(this.value);
      $("#ModalBuscarPedido").attr("x-id-cliente", this.value);
    }
  );
  $("#tabla_busqueda > tbody").html("");
  $("#ModalBuscarPedido").modal("show");
}

function CerrarModalBuscar() {
  $("#ModalBuscarPedido").modal("hide");
}

function cargar_parciales(id_cliente) {
  $("#tabla_busqueda > tbody").html("");
  $.ajax({
    beforeSend: function () {},
    url: "data_ver_agenda.php",
    type: "POST",
    data: { consulta: "buscar_parciales", id_cliente: id_cliente },
    success: function (x) {
      if (x.trim() != ""){
        $("#tabla_busqueda > tbody").html(x);
      }
      else{
        $("#tabla_busqueda > tbody").html(`
          <tr>
            <td colspan='8'>
            <div class='callout callout-danger'><b>No hay pedidos en Producción para el cliente seleccionado...</b></div>
            </td>
          </tr>
        `);
      }
    },
    error: function (jqXHR, estado, error) {
      $("#tabla_busqueda").html(
        "Hubo un error al cargar la información del pedido"
      );
    },
  });
}

function MarcarOrden(id_artpedido, objeto) {
  CerrarModalBuscar();
  let fecha_entrega = $(objeto).closest("tr").find("td:eq(5)").text();
  let parts = fecha_entrega.split("/");
  let fechabox = new Date(parts[2], parts[1] - 1, parts[0]);
  let fechas = JSON.parse(getWeek(current_date, 2));
  mismasemana = false;
  for (let i = 0; i < fechas.length; i++) {
    if (fechas[i].replace("'", "").replace("'", "") == fecha_entrega) {
      mismasemana = true;
    }
  }
  if (mismasemana == false) {
    changeWeek(fechabox, id_artpedido);
  } else {
    let objeto = $("#id2_" + id_artpedido);
    if (objeto != undefined && objeto != null) {
      let scrollpos = objeto.offset().top;
      SmoothScrollTo(objeto, 500);
      toggleSelection(objeto, 2);
    }
  }
}

function SmoothScrollTo(id_or_Name, timelength) {
  timelength = timelength || 1000;
  $("html, body").animate(
    {
      scrollTop: $(id_or_Name).offset().top - 50,
    },
    timelength,
    function () {
      window.location.hash = id_or_Name;
    }
  );
}

function seleccionTabla(objeto) {
  let tr = $(objeto);
  if (tr.hasClass("selected2")) {
    tr.removeClass("selected2");
  } else {
    tr.addClass("selected2");
  }
}

function MostrarModalCambioFecha(id) {
  $.datepicker.setDefaults($.datepicker.regional["es"]);
  $("#fechaplanificacion_txt")
    .datepicker({
      dateFormat: "dd/mm/yy",
      autoclose: true,
      disableTouchKeyboard: true,
      Readonly: true,
    })
    .attr("readonly", "readonly");
  let fechita = $("#" + id)
    .closest("tr")
    .find("td:eq(5)")
    .text();
  $("#fechaplanificacion_txt").val(fechita);
  $("#id_artpedidohide2").html(id.replace("art_", ""));
  $("#ModalCambiarFecha").modal("show");
}

function CerrarModalCambioFecha() {
  $("#ModalCambiarFecha").modal("hide");
}
function CerrarModalOrdenBusqueda() {
  $("#ModalOrdenconBusqueda").modal("hide");
}
function ModalOrdenBusqueda() {
  let seleccionados = document.getElementsByClassName("selected2");
  if (
    seleccionados != undefined &&
    seleccionados != null &&
    seleccionados.length > 0
  ) {
    $.datepicker.setDefaults($.datepicker.regional["es"]);
    $("#fecha_entregareal")
      .datepicker({
        dateFormat: "dd/mm/yy",
        autoclose: true,
        disableTouchKeyboard: true,
        Readonly: true,
      })
      .attr("readonly", "readonly");
    $("#id_clienteorden").html(
      $("#select_cliente2").find("option:selected").val()
    );
    $("#select_modoentrega5").val("default").selectpicker("refresh");
    $("#contenedor_domicilio").css({ display: "none" });
    $("#fecha_entregareal").val("<?php echo date('d/m/Y'); ?>");
    $("#ModalOrdenconBusqueda").modal("show");
  } else {
    swal("ERROR", "Debes seleccionar algún producto!", "error");
  }
}

function GuardarCambioFecha() {
  let fechanueva = $("#fechaplanificacion_txt")
    .datepicker({ dateFormat: "dd/mm/yy" })
    .val()
    .toString();
  if (fechanueva.length == 10 && fechanueva.includes("00/") == false) {
    CerrarModalCambioFecha();
    CerrarModalBuscar();
    let consulta = "guardar_cambiofecha";
    let id_artpedido = $("#id_artpedidohide2").text();
    $.ajax({
      beforeSend: function () {},
      url: "data_ver_agenda.php",
      type: "POST",
      data: {
        consulta: consulta,
        fechanueva: fechanueva,
        id_artpedido: id_artpedido,
      },
      success: function (x) {
        swal("La fecha fue cambiada correctamente!", "", "success");
        loadPedidos();
      },
      error: function (jqXHR, estado, error) {
        swal("ERROR al modificar la fecha!", "", "success");
      },
    });
  } else {
    swal("ERROR", "Debes ingresar una fecha válida!", "error");
  }
}

function GuardarEnAgenda() {
  let seleccionados = document.getElementsByClassName("selected2");
  let articulos = [];
  if (
    seleccionados != undefined &&
    seleccionados != null &&
    seleccionados.length > 0
  ) {
    for (let i = 0; i < seleccionados.length; i++) {
      let id_artpedido = $(seleccionados[i])
        .closest("tr")
        .attr("id")
        .replace("art_", "");
      let tipo_entrega = parseInt(
        $("#select_modoentrega5").find("option:selected").val()
      );
      let entrega = null;
      if (tipo_entrega == 1) {
        entrega = "RETIRA EL CLIENTE";
      } else if (tipo_entrega == 2) {
        entrega = $("#domiciliocliente_txt4").val();
      }
      articulos.push([id_artpedido, entrega]);
    }
    let jsonarray = JSON.stringify(articulos);
    let fecha_entrega = $("#fecha_entregareal").val().trim();
    consulta = "agrega_orden";
    CerrarModalOrdenBusqueda();
    $.ajax({
      beforeSend: function () {},
      url: "data_ver_planentregas.php",
      type: "POST",
      data: {
        jsonarray: jsonarray,
        consulta: consulta,
        fecha_entrega: fecha_entrega,
      },
      success: function (x) {
        loadPedidos();
        cargar_parciales($("#select_cliente2").find("option:selected").val());
        swal(
          "Se agregaron correctamente los pedidos a la Agenda de Entregas",
          "",
          "success"
        );
      },
      error: function (jqXHR, estado, error) {
        alert(estado + " " + error);
      },
    });
  } else {
    swal("ERROR", "Debes seleccionar alguna Orden de Siembra", "error");
  }
}

function funcGeneraRemitoInmediato(
  id_cliente,
  cliente,
  subtotal,
  descuento,
  tipodescuento,
  telefono,
  id_remito
) {
  $("#ventana-remito").html("");
  let seleccionados = document.getElementsByClassName("tr-entrega");
  const date = new Date();
  const fecha = moment(date).format("DD/MM/YYYY");
  const hora = moment(date).format("HH:mm");
  let headerinfo =
    `
              <div class="row">
                <div class="col text-center">
                  <h2 class='tipo-remito'>ORIGINAL</h2>
                </div>
              </div>
              <div class="row font-weight-bold">
                  <div class="col">
                      <h4>Remito Nº: ` +
    id_remito +
    `</h4> 
                  </div>
                  <div class="col text-center">
                       
                  </div>
                  <div class="col text-right">
                      <h3>` +
    fecha +
    `<span style="font-size:20px !important">, ${hora}</span></h3> 
                  </div>
              </div>
              <div class="row">
                <div class="col">
                  <h2>${cliente}</h2>
                </div>
                
              </div>

            `;

  $("#ventana-remito").append(headerinfo);

  if (telefono.trim().length > 0) {
    $("#ventana-remito").append(`
                <div class="row">
                  <div class="col-md-7">
                    <h3>Teléfono: ${telefono}</h3>
                  </div>
                  <div class="col-md-5">
                    <h3>Dir:</h3> 
                  </div>
                </div>
                `);
  } else {
    $("#ventana-remito").append(`
                <div class="row">
                  <div class="col-md-7">
                    
                  </div>
                  <div class="col-md-5">
                    <h3>Dir:</h3> 
                  </div>
                </div>
                `);
  }

  $("#ventana-remito").append(`
              <div class='row mt-5'>
                <div class='col'>
                  <h4>Remitimos a usted la siguiente mercadería: </h4>
                </div>
              </div>`);

  document.title = "Remito";
  let tabla = `<table style='width: 100%' id='tabla_producto' class='table table-bordered tableproductos mt-2' role='grid'>
                        <thead>
                        <tr role='row' style="font-size:1.4em !important">
                          <th class='text-center'>Producto</th>
                          <th class='text-center' style='width:140px'>Cantidad</th>
                          <th class='text-center' style='width:140px'>Precio U.</th>
                          <th class='text-center' style='width:140px'>Subtotal</th>
                          </tr>
                        </thead>
                        <tbody>
                        </tbody>
                      </table>`;
  $("#ventana-remito").append(tabla);
  let listaproductos = [];
  for (i = 0; i < seleccionados.length; i++) {
    const orden = $(seleccionados[i]).find("td:eq(0)").text();

    const artpedido = $(seleccionados[i])
      .find("td:eq(0)")
      .attr("id")
      .replace("art_", "");
    const producto = $(seleccionados[i])
      .find("td:eq(1)")
      .text()
      .replace(" SIN SEMILLA", "");
    const cantidad = $(seleccionados[i]).find(".cantidadbox").val();
    const precio = $(seleccionados[i]).find(".preciobox").val();

    const preciou = parseInt(precio) * parseInt(cantidad);
    let row = `<tr>
                    <td style='word-wrap:break-word;font-size:1.7em !important;'>${producto} [Ord. ${orden}]</td>
                    <td class='celdaproducto'>${cantidad}</td>
                    <td class='celdaproducto'>$${precio}</td>
                    <td class='celdaproducto'>$${preciou.toString()}</td>
                  </tr>
                  `;
    listaproductos.push([artpedido, cantidad]);

    $("#tabla_producto tbody").append(row);
  }

  if (descuento != null) {
    if (tipodescuento == "porcentual") {
      $("#tabla_producto tbody").append(`
                    <tr>
                      <td style="font-size:1.7em;">DESCUENTO</td>
                      <td></td>
                      <td></td>
                      <td class="celdaproducto">-${descuento}%</td>
                    </tr>`);
    } else {
      $("#tabla_producto tbody").append(`
                    <tr>
                      <td style="font-size:1.7em;">DESCUENTO</td>
                      <td></td>
                      <td></td>
                      <td class="celdaproducto">-$${descuento}</td>
                    </tr>`);
    }
  }

  $("#tabla_producto tbody").append(`
              <tr class='celdaproducto'>
                <td colspan='2'></td>
                <td style="font-size:1.2em;text-align:center;">TOTAL</td>
                <td style='text-align:center;font-size:1.2em'>$${parseInt(
                  subtotal
                ).toFixed(2)}</td>
              </tr>`);

  $("#ventana-remito").append(
    "<div style='display: block; page-break-before: always;'>"
  );

  $("#ventana-remito").append(
    $("#ventana-remito").html().replace("ORIGINAL", "COPIA")
  );

  $("#mi-ventana").html($("#ventana-remito").html());
  return $("#mi-ventana").html();
}
