let current_date = new Date();
let original_weeknumber = null;
let current_weeknumber = null;
let listaseleccionados = [];
let listacolumnas = [];
let id_articulopedido = null;
let estadoactual = null;
let id_artpedido_orden = null;

$(document).ready(() => {
  original_weeknumber = getWeekNumber(current_date);
  current_weeknumber = original_weeknumber;

  $("#guardar_btn").prop("disabled", true);
  $("#num_semana").html("Semana Nº " + current_weeknumber);

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

  changeWeek(0);
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
  let today =
    dias[day] +
    " <span id='fecha_actual'>" +
    dd +
    "/" +
    mm +
    "/" +
    yyyy +
    "</span>";
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
  loadPedidos();
}

function loadPedidos() {
  LoadPedidosFunc();
}

function LoadPedidosFunc() {
  let fecha = getWeek(current_date, 2);
  $.ajax({
    beforeSend: function () {},
    url: "data_ver_agenda.php",
    type: "POST",
    data: { fecha: fecha, consulta: "busca_agenda" },
    success: function (x) {
      $("#tablitaa").find("tbody").html(x);
    },
    error: function (jqXHR, estado, error) {
      alert("ERROR! " + estado + " " + error);
    },
  });
}

function toggleSelection(objeto) {
  let tr = $(objeto).parent();
  if ($(tr).attr("x-estado") == "0") {
    if (tr.hasClass("selected2")) {
      tr.removeClass("selected2");
    } else if ($(".selected2").length < 6) {
      tr.addClass("selected2");
    }
  }
}

function marcarEntrega(id_agenda, cantidad) {
  swal("Marcar el Pedido como Entregado?", "", {
    icon: "info",
    buttons: {
      cancel: "Cancelar",
      catch: {
        text: "Sí, ENTREGAR",
        value: "catch",
      },
    },
  }).then((value) => {
    switch (value) {
      case "catch":
        $.ajax({
          beforeSend: function () {},
          url: "data_ver_agenda.php",
          type: "POST",
          data: {
            id_agenda: id_agenda,
            consulta: "marcar_entrega",
            cantidad: cantidad
          },
          success: function (x) {
            if (x.trim() == "success") {
              loadPedidos();
              swal("Marcaste el pedido como entregado!", "", "success");
            } else {
              swal("Ocurrió un error.", x, "error");
            }
          },
          error: function (jqXHR, estado, error) {
            swal("Ocurrió un error.", error, "error");
          },
        });
        break;
    }
  });
}

function eliminarEntrega(id_agenda, estado, id_remito) {
  swal(
    estado == 1 && id_remito && id_remito.length
      ? "Eliminar Remito?"
      : "Estás seguro/a de eliminar el Producto de la Agenda?",
    estado == 1 && id_remito && id_remito.length
      ? "SE ELIMINARÁ EL REMITO Y LOS PEDIDOS AGENDADOS DEL MISMO VOLVERÁN A ESTADO PENDIENTE"
      : "",
    {
      icon: "warning",
      buttons: {
        cancel: "Cancelar",
        catch: {
          text: "Sí, ELIMINAR",
          value: "catch",
        },
      },
    }
  ).then((value) => {
    switch (value) {
      case "catch":
        $.ajax({
          beforeSend: function () {},
          url: "data_ver_agenda.php",
          type: "POST",
          data: {
            id_agenda: id_agenda,
            consulta: "eliminar_entrega",
            estado: estado,
            id_remito: id_remito,
          },
          success: function (x) {
            if (x.trim() == "success") {
              swal(
                "Eliminaste el Pedido de la Agenda de Entregas",
                "",
                "success"
              );
              loadPedidos();
            } else {
              swal("Ocurrió un error al eliminar la entrega", x, "error");
            }
          },
          error: function (jqXHR, estado, error) {
            swal("Ocurrió un error al eliminar la entrega", error, "error");
          },
        });
    }
  });
}

function printOrdenEntrega(tipo) {
  if (tipo == 1) {
    func_printOrdenEntrega();
    document.getElementById("ocultar").style.display = "none";
    document.getElementById("miVentana").style.display = "block";
  } else {
    document.getElementById("ocultar").style.display = "block";
    document.getElementById("miVentana").style.display = "none";
  }
}

function func_printOrdenEntrega() {
  $("#miVentana").html(
    `<img src='dist/img/babyplant.png' style="width:200px; height:70px;"></img>`
  );
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
  $("#miVentana").append(
    "<span style='font-size:16px;font-weight:bold;'>ORDEN DE ENTREGA " +
      datetime +
      "</span>"
  );
  $("#miVentana").append(
    document.getElementById("tablitaordenentrega").innerHTML
  );
  $("#miVentana").find("th:last").remove();
  $("#miVentana")
    .find("tr")
    .each(function () {
      $(this).find("td:last").remove();
    });
  setTimeout("window.print();printOrdenEntrega(2, null);", 500);
}

function printAgenda(tipo) {
  if (tipo == 1) {
    func_printAgenda();
    document.getElementById("ocultar").style.display = "none";
    document.getElementById("miVentana").style.display = "block";
  } else {
    document.getElementById("ocultar").style.display = "block";
    document.getElementById("miVentana").style.display = "none";
    $("#miVentana").html("");
  }
}

function func_printAgenda() {
  $("#miVentana").html(
    "<div align='center' style='font-size:18px;font-weight:bold'>" +
      document.getElementById("header").innerHTML +
      "</div>"
  );
  $("#miVentana").append(
    "<span style='font-size:16px;font-weight:bold;'>AGENDA DEL DÍA</span><br><br>"
  );
  $("#miVentana").append(document.getElementById("tablitaa").outerHTML);
  $("#miVentana").find("th:last").remove();
  $("#miVentana").find("th:last").remove();
  $("#miVentana")
    .find("tr")
    .each(function () {
      $(this).find("td:last").remove();
      $(this).find("td:last").remove();
    });
  setTimeout("window.print();printAgenda(2, null);", 500);
}

function printRemito(tipo, id) {
  if (tipo == 1) {
    func_printRemito(id);
    document.getElementById("ocultar").style.display = "none";
    document.getElementById("miVentana").style.display = "block";
  } else {
    document.getElementById("ocultar").style.display = "block";
    document.getElementById("miVentana").style.display = "none";
    $("#miVentana").html("");
  }
}

function editEntrega(objeto) {
  $.datepicker.setDefaults($.datepicker.regional["es"]);
  $("#fecha_nuevaentrega")
    .datepicker({
      dateFormat: "dd/mm/yy",
      autoclose: true,
      disableTouchKeyboard: true,
      Readonly: true,
    })
    .attr("readonly", "readonly");

  $("#fecha_nuevaentrega").val($("#fecha_actual").text());
  $("#ModalEditarEntrega").attr(
    "x-id-agenda",
    $(objeto).closest("tr").attr("x-id-agenda")
  );
  $("#cantidad_entrega").val($(objeto).closest("tr").attr("x-cantidad"));
  $("#ModalEditarEntrega").modal("show");
}

function guardarCambioEntrega() {
  let cantidad = $("#cantidad_entrega").val().trim();
  let fecha = $("#fecha_nuevaentrega").val();

  if (parseInt(cantidad) < 1 || !cantidad.trim().length) {
    swal("La cantidad debe ser mayor a cero!", "", "error");
  } else {
    $("#ModalEditarEntrega").modal("hide");
    let id_agenda = $("#ModalEditarEntrega").attr("x-id-agenda");
    $.ajax({
      beforeSend: function () {},
      url: "data_ver_agenda.php",
      type: "POST",
      data: {
        consulta: "edita_entrega",
        id_agenda: id_agenda,
        cantidad: cantidad,
        fecha: fecha,
      },
      success: function (x) {
        console.log(x);
        if (x.trim() == "success") {
          swal(
            "Los datos de entrega fueron modificados correctamente!",
            "",
            "success"
          );
          loadPedidos();
        } else {
          swal("Ocurrió un error al modificar la entrega", x, "error");
        }
      },
      error: function (jqXHR, estado, error) {
        swal("Ocurrió un error al modificar la entrega", error, "error");
      },
    });
  }
}

// NUEVO 050622

async function modalEntrega() {
  if (!$(".selected2").length) {
    swal("Selecciona al menos un Pedido!", "", "error");
    return;
  }
  $("#ModalEntregaInmediata .btn-warning").remove();
  $("#ModalEntregaInmediata .box-title").html("Generar Remito");
  $("#ModalEntregaInmediata .btn-entregar-label").html("GUARDAR");

  let seleccionados = [];
  let id_cliente = null;
  let nombre_cliente = null;
  await $(".selected2").each(function (i, tr) {
    const tr_id_cliente = $(tr).attr("x-id-cliente");
    if (!id_cliente) {
      id_cliente = tr_id_cliente;
      nombre_cliente = $(tr).find(".td-cliente").text();
    } else if (id_cliente != tr_id_cliente) {
      swal("Los pedidos deben pertenecer al mismo cliente!", "", "error");
      return;
    }
    seleccionados.push($(tr).attr("x-id-agenda"));
  });

  if (seleccionados.length) {
    let jsonarray = JSON.stringify(seleccionados);
    $.ajax({
      beforeSend: function () {},
      url: "data_ver_agenda.php",
      type: "POST",
      data: { jsonarray: jsonarray, consulta: "generar_orden_entrega" },
      success: function (x) {
        $("#tabla_entregainmediata > tbody").html(x);
        $("#ModalEntregaInmediata").attr("x-id-clienteoriginal", id_cliente);
        $("#ModalEntregaInmediata").attr("x-id-cliente-entrega", id_cliente);

        $(".label-nombrecliente").html(
          "Cliente: " + nombre_cliente + ` (${id_cliente})`
        );
        $("#ModalEntregaInmediata").modal("show");
        calcularSubtotal();
      },
      error: function (jqXHR, estado, error) {
        $("#tabla_entregainmediata").html(
          "Hubo un error al cargar la información del pedido"
        );
      },
    });
    $("#ModalBuscarPedido").modal("hide");
  }
}

function calcularSubtotal() {
  let monto = 0;
  $("#tabla_entregainmediata > tbody  > tr").each(function (index, tr) {
    const precio = $(tr).find(".preciobox").val().trim();
    const cantidad = $(tr).attr("x-cantidad-agenda");
    if (
      precio.length &&
      cantidad.length &&
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

//GENERAR REMITO AGENDA
async function guardarEntregaInmediata() {
  let listaproductos = [];
  await $("#ModalEntregaInmediata .preciobox").each(function (i, input) {
    const precio = $(input).val().trim();
    if (!precio.length) {
      swal("Debes ingresar el precio de todos los productos!", "", "error");
      return;
    }
    const tr = $(input).closest("tr");
    const cantidad_agenda = parseInt($(tr).attr("x-cantidad-agenda"));
    const cantidad_restante = parseInt($(tr).attr("x-cantidad-restante"));
    if (cantidad_agenda > cantidad_restante) {
      swal(
        "La cantidad agendada es mayor a la que falta entregar",
        "Deberás modificar la cantidad agendada.",
        "error"
      );
      return;
    }

    listaproductos.push({
      id_agenda: $(tr).attr("x-id-agenda"),
      id_artpedido: $(tr).attr("x-id-artpedido"),
      id_orden: $(tr).find("td:eq(0)").text(),
      producto: $(tr).find("td:eq(1)").text(),
      cantidad: cantidad_agenda,
      precio: precio,
      subtotal: cantidad_agenda * parseFloat(precio),
    });
  });
  if (!$("#ModalEntregaInmediata .input-subtotal").val().trim().length) {
    swal("Ingresá el Monto total del pedido", "", "error");
    return;
  }
  const cliente = $("#ModalEntregaInmediata .label-nombrecliente").text();
  const subtotal = $("#ModalEntregaInmediata .input-subtotal").val().trim();
  const descuento = $("#ModalEntregaInmediata .input-descuento").val().trim();
  const tipodescuento = $("#ModalEntregaInmediata .select-descuento")
    .find("option:selected")
    .val();
  const id_cliente = $("#ModalEntregaInmediata").attr("x-id-clienteoriginal");

  $("#ModalEntregaInmediata").modal("hide");
  $.ajax({
    url: "data_ver_agenda.php",
    type: "POST",
    data: { id_cliente: id_cliente, consulta: "cargar_dataremito" },
    success: function (x) {
      if (x.length) {
        const data = JSON.parse(x);
        const { telefono, id_remito } = data;
        const codigo = funcGeneraRemitoInmediato(
          cliente,
          subtotal,
          descuento,
          tipodescuento,
          telefono,
          id_remito,
          listaproductos
        );

        const jsonarray = JSON.stringify(listaproductos);

        $.ajax({
          beforeSend: function () {},
          url: "data_ver_agenda.php",
          type: "POST",
          data: {
            id_cliente: id_cliente,
            jsonarray: jsonarray,
            consulta: "guarda_remito",
            subtotal: subtotal,
            tipodescuento: tipodescuento,
            descuento: isNaN(descuento) || !descuento.length ? 0 : descuento,
            codigo: codigo,
          },
          success: function (x) {
            if (x.trim() == "success") {
              printRemito(1);
              loadPedidos();
            } else {
              swal("Ocurrió un error al guardar el Remito", x, "error");
            }
          },
          error: function (jqXHR, estado, error) {
            swal("ERROR!", error.toString(), "error");
          },
        });
      } else {
        swal("Ocurrió un error al generar el Remito", "", "error");
        $("#ModalEntregaInmediata").modal("show");
      }
    },
  });
}

function printRemito(tipo) {
  if (tipo == 1) {
    document.getElementById("ocultar").style.display = "none";
    document.getElementById("miVentana").style.display = "block";
    setTimeout("window.print();printRemito(2);", 500);
  } else {
    document.getElementById("ocultar").style.display = "block";
    document.getElementById("miVentana").style.display = "none";
    document.title = "Agenda de Entregas";
  }
}

function funcGeneraRemitoInmediato(
  cliente,
  subtotal,
  descuento,
  tipodescuento,
  telefono,
  id_remito,
  seleccionados
) {
  $("#miVentana").html("");

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

  $("#miVentana").append(headerinfo);

  if (telefono.trim().length > 0) {
    $("#miVentana").append(`
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
    $("#miVentana").append(`
                <div class="row">
                  <div class="col-md-7">
                    
                  </div>
                  <div class="col-md-5">
                    <h3>Dir:</h3> 
                  </div>
                </div>
                `);
  }

  $("#miVentana").append(`
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
  $("#miVentana").append(tabla);
  for (i = 0; i < seleccionados.length; i++) {
    const { producto, cantidad, precio, subtotal, id_orden } = seleccionados[i];
    let row = `<tr>
                    <td style='word-wrap:break-word;font-size:1.7em !important;'>${producto} [Ord. ${id_orden}]</td>
                    <td>${cantidad}</td>
                    <td>$${precio}</td>
                    <td>$${subtotal}</td>
                  </tr>
                  `;

    $("#tabla_producto tbody").append(row);
  }

  if (descuento && descuento.length && tipodescuento && tipodescuento.length) {
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

  $("#miVentana").append(
    "<div style='display: block; page-break-before: always;'>"
  );

  $("#miVentana").append($("#miVentana").html().replace("ORIGINAL", "COPIA"));
  return $("#miVentana").html();
}