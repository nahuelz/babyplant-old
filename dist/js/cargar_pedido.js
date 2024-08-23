let productosPedidos = [];

$(document).ready(function () {
  pone_clientes();
  $("#cantidad_semillas,#cantidad_plantas,#cantidad_band,#dias_produccion").on(
    "propertychange input",
    function () {
      this.value = this.value.replace(/\D/g, "");
    }
  );
  productosPedidos = [];

  $.datepicker.setDefaults($.datepicker.regional["es"]);
  $("#fechaentrega_picker")
    .datepicker({
      format: "dd-M-yyyy",
      autoclose: true,
      disableTouchKeyboard: true,
      Readonly: true,
      minDate: 28,
      dateFormat: "dd/mm/yy",
      onSelect: function (dateText, inst) {
        let date = $(this).val();
        let datesplit = date.split("/");
        let fecha = new Date(
          datesplit[2] + "/" + datesplit[1] + "/" + datesplit[0]
        );
        let cant_dias = document.getElementById("dias_produccion").value;
        fecha.setDate(fecha.getDate() - parseInt(cant_dias));
        if (fecha.getDay() == 0) {
          fecha.setDate(fecha.getDate() + 1);
        }
        $("#fechasiembra_txt").val(
          ("0" + fecha.getDate()).slice(-2) +
            "/" +
            ("0" + (fecha.getMonth() + 1)).slice(-2) +
            "/" +
            fecha.getFullYear()
        );
      },
    })
    .attr("readonly", "readonly");

  $("input").on("focus", function () {
    if ($(this).hasClass("datepicker") == false) {
      const textareaTop = $(this).offset().top;
      setTimeout(function () {
        $(".modal-content").scrollTop(textareaTop - 80);
      }, 1000);
    }
  });
  if (
    document.location.href.includes("cargar_pedido") &&
    !$(".pedido-vacio-msg").length
  ) {
    $(window).bind("beforeunload", function (e) {
      return "Desea salir?";
    });
  } else {
    $(window).unbind("beforeunload");
  }
});

function pone_clientes() {
  $.ajax({
    beforeSend: function () {
      $("#select_cliente").html("Cargando lista de clientes...");
    },
    url: "pone_clientes.php",
    type: "POST",
    data: null,
    success: function (x) {
      $("#select_cliente").html(x).selectpicker("refresh");
    },
    error: function (jqXHR, estado, error) {},
  });
}

function modalAgregarProducto(trEdit) {
  clearModalAgregarProducto();

  $("#modalAgregarProducto").modal("show");
  $("#modalAgregarProducto").removeAttr("x-index-edit")

  if (trEdit) {
    $("#modalAgregarProducto").attr("x-index-edit", $(trEdit).index())
    $("#select_tipo,#select_subtipo,#select_variedad,#select_bandeja")
      .prop("disabled", true)
      .selectpicker("refresh");

    const id_variedad = $(trEdit).attr("x-id-variedad");
    const variedad = $(trEdit).attr("x-variedad");
    const id_tipo = $(trEdit).attr("x-id-tipo");
    const tipo = $(trEdit).attr("x-tipo");
    const subtipo = $(trEdit).attr("x-subtipo");
    const id_subtipo = $(trEdit).attr("x-id-subtipo");
    const cant_plantas = $(trEdit).attr("x-cant-plantas");
    const cant_semillas = $(trEdit).attr("x-cant-semillas");
    const cant_bandejas = $(trEdit).attr("x-cant-bandejas");
    const fecha_siembra = $(trEdit).attr("x-fecha-siembra");
    const fecha_entrega = $(trEdit).attr("x-fecha-entrega");
    const con_semilla = $(trEdit).attr("x-con-semilla");
    const tipo_bandeja = $(trEdit).attr("x-tipo-bandeja");
    const nombre_producto = $(trEdit).attr("x-nombre-producto");

    $("#select_tipo")
      .html(
        `<option value='${id_tipo}'>${tipo}</option>`
      )
      .selectpicker("refresh");

    $("#select_subtipo")
      .html(`<option value='${id_subtipo}'>${subtipo}</option>`)
      .selectpicker("refresh");

    $("#select_variedad")
      .html(`<option value='${id_variedad}'>${variedad}</option>`)
      .selectpicker("refresh");

    $("#select_bandeja")
      .html(`<option value='${tipo_bandeja}'>${tipo_bandeja}</option>`)
      .selectpicker("refresh");

    $("#select_tipo").val(id_tipo).selectpicker("refresh")
    $("#select_variedad").val(id_variedad).selectpicker("refresh");
    $("#select_subtipo").val(id_subtipo).selectpicker("refresh");
    $("#select_bandeja").val(tipo_bandeja).selectpicker("refresh");
    
    $("#cantidad_plantas").val(cant_plantas);
    $("#cantidad_semillas").val(cant_semillas);
    $("#cantidad_band").val(cant_bandejas);
    $("#check_semilla").prop("checked", con_semilla == 1 ? true : false);

    $("#fechaentrega_picker").val(fecha_entrega);
    $("#fechasiembra_txt").val(fecha_siembra);
  } else {
    pone_tiposdeproducto();
    $("#select_tipo,#select_subtipo,#select_variedad,#select_bandeja")
      .prop("disabled", false)
      .selectpicker("refresh");
  }
}

function clearModalAgregarProducto() {
  $("#select_tipo").val("default").selectpicker("refresh");
  $("#select_variedad").find("option").remove();
  $("#select_bandeja").find("option").remove();
  $("#select_subtipo").find("option").remove();
  $("#select_variedad").val("default").selectpicker("refresh");
  $("#select_bandeja").val("default").selectpicker("refresh");
  $("#select_subtipo").val("default").selectpicker("refresh");
  $("#check_semilla").prop("checked", false);
  $("#modalAgregarProducto")
    .find("#cantidad_plantas,#cantidad_band,#cantidad_semillas")
    .val("");
  $("#modalAgregarProducto")
    .find("#fechaentrega_picker,#fechasiembra_txt")
    .val("DD/MM/YYYY");
  $("#dias_produccion").val("28");
}

function GuardarPedido() {
  let esCuaderno = document.location.href.includes("ver_stock") ? 1 : 0;

  const idPedidoEdit = $("body").attr("x-pedido-edit");

  const id_cliente = $("#select_cliente,#select-cliente")
    .find("option:selected")
    .val();
  productosPedidos = null;
  if (!id_cliente.trim().length) {
    swal("Seleccioná un cliente!", "", "error");
  } else if ($(".pedido-vacio-msg").length) {
    swal(
      "El pedido está vacío!",
      "Agregá algún producto para continuar.",
      "error"
    );
  } else {
    $("#btn_guardarpedido").prop("disabled", true);
    let articulos = [];
    $("#tabla_detail > tbody > tr").each(function (i, tr) {
      articulos.push({
        id_variedad: $(tr).attr("x-id-variedad"),
        cant_plantas: $(tr).attr("x-cant-plantas"),
        cant_semillas: $(tr).attr("x-cant-semillas"),
        cant_bandejas: $(tr).attr("x-cant-bandejas"),
        fecha_siembra: $(tr).attr("x-fecha-siembra"),
        fecha_entrega: $(tr).attr("x-fecha-entrega"),
        con_semilla: $(tr).attr("x-con-semilla"),
        tipo_bandeja: $(tr).attr("x-tipo-bandeja"),
        nombre_producto: $(tr).attr("x-nombre-producto"),
      });
    });

    if (articulos.length) {
      const jsonarray = JSON.stringify(articulos);
      const observaciones = $("#observaciones_txt").val().trim();
      const pagos = $("#ModalPagos").attr("x-monto");
      const conceptotxt = $("#ModalPagos").attr("x-concepto");
      productosPedidos = articulos;
      let pago = null;
      if (pagos && pagos.length && parseInt(pagos) > 0) {
        pago = parseInt(pagos);
      }

      let concepto = null;
      if (conceptotxt && conceptotxt.length) {
        concepto = conceptotxt.toUpperCase();
      }

      $.ajax({
        url: "data_cargar_pedido.php",
        type: "POST",
        data: {
          consulta: "guardar_pedido",
          id_cliente: id_cliente,
          pago: pago,
          jsonarray: jsonarray,
          observaciones: observaciones,
          concepto: concepto,
          esCuaderno: esCuaderno,
          idPedidoEdit:
            idPedidoEdit && idPedidoEdit.length ? idPedidoEdit : null,
        },
        success: function (x) {
          console.log(x);
          if (x.includes("pedidonum")) {
            $("#btn_guardarpedido").prop("disabled", false);

            if (esCuaderno == 0) {
              let idPedido = x.trim().includes("pedidonum:")
                ? x.trim().replace("pedidonum:", "")
                : null;
              showPedidoExitosoDialog();
              $("#ModalAdminPedido").attr("x-id-pedido", idPedido);
              $("#monto_pago").html("0.00");
              $("#ModalPagos").attr("x-monto", "");
              $("#ModalPagos").attr("x-concepto", "");
            } else {
              if (idPedidoEdit && idPedidoEdit.length) {
                document.getElementById("btn-tab-cuaderno-lista").click();
                swal(
                  "Editaste el Pedido del Cuaderno correctamente!",
                  "",
                  "success"
                );
              } else {
                swal(
                  "Agregaste el Pedido al Cuaderno correctamente!",
                  "",
                  "success"
                );
              }
              ClearPedido();
            }
          } else {
            swal("Ocurrió un error al guardar el Pedido", x, "error");
          }
        },
        error: function (jqXHR, estado, error) {
          swal(
            "Ocurrió un error al guardar el Pedido",
            error.toString(),
            "error"
          );
          $("#btn_guardarpedido").prop("disabled", false);
        },
      });
    } else {
      swal("Debes agregar algún producto al pedido!", "", "error");
    }
  }
}

function pone_tiposdeproducto() {
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
        function (e, clickedIndex, newValue, oldValue) {
          carga_subtipos(this.value);
          carga_bandejas(this.value);
          $("#select_variedad").find("option").remove();
          $("#select_variedad").val("default").selectpicker("refresh");
        }
      );
    },
    error: function (jqXHR, estado, error) {},
  });
}

function carga_subtipos(id_tipo) {
  $.ajax({
    beforeSend: function () {
      $("#select_bandejasin").html("Cargando subtipos...");
    },
    url: "pone_tiposdeproducto.php",
    type: "POST",
    data: { tipo: "carga_subtipos", id_tipo: id_tipo },
    success: function (x) {
      $(".selectpicker").selectpicker();
      $("#select_subtipo").val("default").selectpicker("refresh");
      $("#select_subtipo").html(x).selectpicker("refresh");
      $("#select_subtipo").on(
        "changed.bs.select",
        function (e, clickedIndex, newValue, oldValue) {
          carga_variedades(this.value);
          $("#modalAgregarProducto")
            .find("#cantidad_plantas,#cantidad_band,#cantidad_semillas")
            .val("");
        }
      );
    },
    error: function (jqXHR, estado, error) {},
  });
}

function carga_variedades(id_subtipo) {
  $.ajax({
    beforeSend: function () {
      $("#select_bandejasin").html("Cargando variedades...");
    },
    url: "pone_tiposdeproducto.php",
    type: "POST",
    data: { tipo: "carga_variedades", id_subtipo: id_subtipo },
    success: function (x) {
      $(".selectpicker").selectpicker();
      $("#select_variedad").val("default").selectpicker("refresh");
      $("#select_variedad").html(x).selectpicker("refresh");
      $("#select_variedad").on(
        "changed.bs.select",
        function (e, clickedIndex, newValue, oldValue) {
          $("#modalAgregarProducto")
            .find("#cantidad_plantas,#cantidad_band,#cantidad_semillas")
            .val("");
        }
      );
    },
    error: function (jqXHR, estado, error) {},
  });
}

function carga_bandejas(id_tipo) {
  $.ajax({
    beforeSend: function () {
      $("#select_bandeja").html("Cargando bandejas...");
    },
    url: "pone_tiposdeproducto.php",
    type: "POST",
    data: { tipo: "carga_bandejas", id_tipo: id_tipo },
    success: function (x) {
      $(".selectpicker").selectpicker();
      $("#select_bandeja").val("default").selectpicker("refresh");
      $("#select_bandeja").html(x).selectpicker("refresh");
      $("#select_bandeja").on(
        "changed.bs.select",
        function (e, clickedIndex, newValue, oldValue) {
          $("#modalAgregarProducto")
            .find("#cantidad_plantas,#cantidad_band,#cantidad_semillas")
            .val("");
          let canti = document.getElementById("cantidad_plantas");
          canti.focus();
          setTimeout(function () {
            canti.select();
          }, 50);
        }
      );
    },
    error: function (jqXHR, estado, error) {},
  });
}

function setCantidadSemillas(id_tipo) {
  let bandeja = $("#select_bandeja").find("option:selected").text();
  if (bandeja.length > 0) {
    if (isNaN($("#cantidad_semillas").val())) {
      return false;
    }
    document.getElementById("cantidad_semillas").value = Math.trunc(
      parseInt(id_tipo) * 1.1
    );
    bandeja = parseInt($("#select_bandeja option:selected").text());
    let semillas = parseInt(document.getElementById("cantidad_semillas").value);
    let cant_bandejas = Math.ceil(semillas / bandeja);
    document.getElementById("cantidad_band").value = cant_bandejas.toString();
  } else {
    swal("ERROR", "Debes elegir un producto y su bandeja!", "error");
  }
}

function setPlantas(valor) {
  let bandeja = $("#select_bandeja").find("option:selected").text();
  if (bandeja.length > 0) {
    bandeja = parseInt($("#select_bandeja option:selected").text());
    valor = parseInt(valor);
    $("#cantidad_semillas").val((bandeja * valor).toString());

    $("#cantidad_plantas").val(Math.trunc(bandeja * valor * 0.9).toString());
  } else {
    swal("ERROR", "Debes elegir un producto y su bandeja!", "error");
  }
}

function setPlantasconSemillas(valor) {
  let bandeja = $("#select_bandeja").find("option:selected").text();
  if (bandeja.length > 0) {
    bandeja = parseInt($("#select_bandeja option:selected").text());
    valor = parseInt(valor);
    $("#cantidad_band").val(Math.trunc((valor / bandeja) * 0.9).toString());
  } else {
    swal("ERROR", "Debes elegir un producto y su bandeja!", "error");
  }
}

function addToPedido() {
  const indexEdit = $("#modalAgregarProducto").attr("x-index-edit") && $("#modalAgregarProducto").attr("x-index-edit").length ? $("#modalAgregarProducto").attr("x-index-edit") : null;

  if (indexEdit){
    $("#tabla_detail > tbody").find(`tr:eq(${indexEdit})`).remove();
  }

  let producto = $("#select_tipo :selected").text();
  let subtipo = $("#select_subtipo :selected").text();
  let variedad = $("#select_variedad :selected").text();
  let bandeja = $("#select_bandeja :selected").text();
  let cantidad_plantas = parseInt($("#cantidad_plantas").val());
  let cantidad_bandejas = parseInt($("#cantidad_band").val());
  let cantidad_semillas = parseInt($("#cantidad_semillas").val());
  let fecha_entrega = $("#fechaentrega_picker").val();
  let fecha_siembra = $("#fechasiembra_txt").val();
  let cant_dias = $("#dias_produccion").val();
  let fecha_entregatest = fecha_entrega.split("/");
  let fecha_siembratest = fecha_siembra.split("/");
  if (
    isNaN(cantidad_plantas) ||
    isNaN(cantidad_bandejas) ||
    isNaN(cantidad_semillas)
  ) {
    swal("ERROR", "Ingresaste cantidades o valores inválidos", "error");
  } else if (!producto.length) {
    swal("ERROR", "Debes elegir un producto!", "error");
  } else if (!subtipo.length) {
    swal("ERROR", "Debes elegir un subtipo de producto!", "error");
  } else if (!variedad.length) {
    swal("ERROR", "Debes elegir una variedad de producto!", "error");
  } else if (!bandeja.length) {
    swal("ERROR", "Debes elegir un tipo de bandeja!", "error");
  } else if (cantidad_plantas == 0) {
    swal("ERROR", "La cantidad de plantas no puede ser cero!", "error");
  } else if (cantidad_bandejas == 0) {
    swal("ERROR", "La cantidad de bandejas no puede ser cero!", "error");
  } else if (parseInt(cant_dias) < 1) {
    swal(
      "Debes ingresar una cantidad de días de producción mayor a cero!",
      "",
      "error"
    );
  } else if (isNaN(parseInt(fecha_entregatest[0]))) {
    swal("Debes asignar una fecha de entrega!", "", "error");
  } else if (isNaN(parseInt(fecha_siembratest[0]))) {
    swal("Debes asignar una fecha de siembra!", "", "error");
  } else if (fecha_siembra.length != 10) {
    swal("Debes ingresar una fecha válida!", "", "error");
  } else if (fecha_siembra.includes("00/")) {
    swal("Debes ingresar una fecha válida!", "", "error");
  } else if (cant_dias.trim().length == 0 || isNaN(parseInt(cant_dias))) {
    swal(
      "Debes ingresar una cantidad de días de producción mayor a cero!",
      "",
      "error"
    );
  } else {
    $("#modalAgregarProducto").modal("hide");
    funcAddToPedido(
      producto,
      subtipo,
      variedad,
      bandeja,
      cantidad_plantas,
      cantidad_bandejas,
      fecha_entrega
    );
  }
}

function funcAddToPedido(
  producto,
  subtipo,
  variedad,
  bandeja,
  cantidad_plantas,
  cantidad_bandejas
) {
  let cantidad_semillas = $("#cantidad_semillas").val();

  let id_subtipo = $("#select_subtipo").find("option:selected").val();
  let id_tipo = $("#select_tipo").find("option:selected").val();

  let id_articulo = $("#select_variedad").find("option:selected").val();
  let fecha_entrega = $("#fechaentrega_picker")
    .datepicker({ dateFormat: "yy-mm-dd" })
    .val()
    .toString();
  let fecha_siembra = $("#fechasiembra_txt")
    .datepicker({ dateFormat: "dd-mm-yy" })
    .val()
    .toString();
  fecha_entrega = "" + fecha_entrega;
  fecha_siembra = "" + fecha_siembra;
  nombre_producto =
    producto +
    " " +
    subtipo +
    " " +
    variedad +
    " x" +
    bandeja +
    ($("#check_semilla").is(":checked") ? " CON " : " SIN ") +
    "SEMILLA";

  if ($(".pedido-vacio-msg").length) {
    $("#tabla_detail tbody").html("");
  }

  let celda = `<tr 
      x-id-variedad="${id_articulo}"
      x-id-subtipo="${id_subtipo}"
      x-id-tipo="${id_tipo}"
      x-variedad="${variedad}"
      x-subtipo="${subtipo}"
      x-tipo="${producto}"
      x-cant-plantas="${cantidad_plantas}"
      x-cant-semillas="${cantidad_semillas}"
      x-cant-bandejas="${cantidad_bandejas}"
      x-fecha-siembra="${fecha_siembra}"
      x-fecha-entrega="${fecha_entrega}"
      x-con-semilla="${$("#check_semilla").is(":checked") ? 1 : 0}"
      x-tipo-bandeja="${bandeja}"
      x-nombre-producto="${nombre_producto.replace('"', "")}"
    >
    <td>${nombre_producto}</td>
    <td>${cantidad_plantas}</td>
    <td>${cantidad_semillas}</td>
    <td>${cantidad_bandejas}</td>
    <td>${fecha_siembra}</td>
    <td>${fecha_entrega}</td>
    <td class="text-center">
      <div class='d-flex flex-row'>
        <button class='btn btn-sm btn-danger' onclick='eliminar_art(this)'><i class='fa fa-trash'></i></button>
        <button class='btn btn-sm btn-primary ml-2' onclick='modalAgregarProducto($(this).closest("tr"))'><i class='fa fa-edit'></i></button>
      </div>
    </td>
    </tr>`;
  $("#tabla_detail tbody").append(celda);
}

function eliminar_art(btn) {
  swal("¿ELIMINAR este Producto del Pedido?", "", {
    icon: "warning",
    buttons: {
      cancel: "Cancelar",
      catch: {
        text: "ELIMINAR",
        value: "catch",
      },
    },
  }).then((value) => {
    switch (value) {
      case "catch":
        $(btn).parent().parent().remove();
        if ($("#tabla_detail > tbody > tr").length < 1) {
          $("#tabla_detail > tbody").append(`
              <tr class="pedido-vacio-msg">
                <th scope="row" colspan="7" class="text-center"><span class="text-muted">El Pedido está vacío</span></th>
              </tr>
            `);
        }

      default:
        break;
    }
  });
}

function showPedidoExitosoDialog() {
  let modal = document.getElementById("ModalAdminPedido");
  modal.style.display = "block";
}

function ClearPedido() {
  try {
    document.getElementById("ModalAdminPedido").style.display = "none";
  } catch (error) {}

  $("#tabla_detail > tbody").html(`
              <tr class="pedido-vacio-msg">
                <th scope="row" colspan="7" class="text-center"><span class="text-muted">El Pedido está vacío</span></th>
              </tr>
  `);
  $("#select_cliente").val("").trigger("change");
  $("#observaciones_txt").val("");
  $("#btn_guardarpedido").prop("disabled", false);
  productosPedidos = null;
}

function print_Cliente(tipo) {
  if (tipo == 1) {
    func_printCliente1();
    document.getElementById("ocultar").style.display = "none";
    document.getElementById("miVentana").style.display = "block";
  } else {
    document.getElementById("ocultar").style.display = "block";
    document.getElementById("miVentana").style.display = "none";
    $("#miVentana").html("");
  }
}

function func_printCliente1() {
  let direccion = `
  <div class="row">
  <div class="col text-center">
    <img src='dist/img/babyplant.png' style="max-width: 250px !important;"></img>
    <address style='font-size:12px !important;padding-top:3px;padding-bottom:10px;'>
    <strong>BabyPlant SRL</strong><br>
    Avda. 44 Nº 4303, Lisandro Olmos<br> 
    La Plata, Buenos Aires<br>
    Tel.: +54 (221) 669-0199<br>
    Whatsapp: +54 (221) 306-2118<br>
    <p>E-mail: babyplantsrl@gmail.com</p>
    </address>
    </div></div>
  `;

  $("#miVentana").html(direccion);

  let date = new Date();
  let dateStr =
    ("00" + date.getDate()).slice(-2) +
    "/" +
    ("00" + (date.getMonth() + 1)).slice(-2) +
    "/" +
    date.getFullYear() +
    " " +
    ("00" + date.getHours()).slice(-2) +
    ":" +
    ("00" + date.getMinutes()).slice(-2);

  let cliente = $("#select_cliente").find("option:selected").text();
  let idPedido = $("#ModalAdminPedido").attr("x-id-pedido");
  $("#miVentana").append("<h4>Nº Pedido: " + idPedido + "</h4>");
  $("#miVentana").append("<h4>Cliente: " + cliente + "</h4>");
  $("#miVentana").append("<h4>Fecha: " + dateStr + "</h4>");

  let tabla = `
  <table class="table-cliente table table-responsive w-100 d-block d-md-table">
    <thead>
    <tr>
      <th>Producto</th>
      <th class="text-center">Cantidad de<br>Bandejas/Plantas</th>
      <th class="text-center">Fecha Entrega<br>Solicitada</th>
    </tr>
    </thead>

    <tbody>

    </tbody>

   </table>
  `;

  $("#miVentana").append(tabla);

  productosPedidos.forEach((producto) => {
    const { nombre_producto, cant_bandejas, cant_plantas, fecha_entrega } =
      producto;
    $(".table-cliente > tbody").append(`
        <tr>
        <td>${nombre_producto}</td>
        <td class="text-center">${cant_bandejas}<br><small>${cant_plantas}</small></td>
        <td class="text-center"s>${fecha_entrega}</td>
        </tr>
      `);
  });

  $("#miVentana").find("tr").css({ "font-size": "23px" });
  $("#miVentana").find("#table_total").html("");
  setTimeout("window.print();print_Cliente(2)", 500);
}

function print_Pedido(tipo) {
  if (tipo == 1) {
    func_printPedido2();
    document.getElementById("ocultar").style.display = "none";
    document.getElementById("miVentana").style.display = "block";
  } else {
    document.getElementById("ocultar").style.display = "block";
    document.getElementById("miVentana").style.display = "none";
    $("#miVentana").html("");
  }
}

function func_printPedido1() {
  let direccion =
    "<img src='dist/img/babyplant.png' width='160' height='70'></img>";
  $("#miVentana").html(direccion);
  let cliente = $("#select_cliente").find("option:selected").text();
  $("#miVentana").append("<h4>Cliente: " + cliente + "</h4>");
  $("#miVentana").append(document.getElementById("tabla_pedidos").innerHTML);
  let arr = [0];
  let filters = arr.map(function (val) {
    return "td:nth-child(" + (val + 1) + ")";
  });

  $("#tabla_detail").find(filters.join()).remove();
  $("#miVentana").find("th:first").remove();
  $("#miVentana").find("tr").css({ "font-size": "12px" });
  $("#miVentana").find("#table_total").html("");
  setTimeout("window.print();print_Pedido(2)", 500);
}

function func_printPedido2() {
  const cliente = $("#select_cliente").find("option:selected").text();
  const oTable = document.getElementById("tabla_detail");
  const rowLength = oTable.rows.length;
  let html = "";
  for (i = 1; i < rowLength; i++) {
    const oCells = oTable.rows.item(i).cells;
    html += `<div style='display: block;margin-top:30px;'>
        <h5>Cliente:${cliente}</h5>;
        <table style='font-size:12px;'>
      <tr>
        <th>Producto</th>
        <th>Bandejas</th>
        <th>Plantas</th>
        <th>Semillas</th>
        <th>Siembra Estimada</th>
        <th>Entrega Estimada</th>
      </tr>
      <tr>
        <td>${oCells.item(0).innerHTML}</td>
        <td>${oCells.item(3).innerHTML}</td>
        <td>${oCells.item(1).innerHTML}</td>
        <td>${oCells.item(2).innerHTML}</td>
        <td>${oCells.item(4).innerHTML}</td>
        <td>${oCells.item(5).innerHTML}</td>
      </tr>
      <tr>
        <td colspan=6><b>Nº de Sobres:</b></td>
      </tr>
      </table></div>`;
  }
  $("#miVentana").append(html);
  $("#miVentana").find("table").css({ border: "1px solid black" });
  $("#miVentana").find("tr").css({ "font-size": "12px" });
  $("#miVentana")
    .find("th,td")
    .css({ border: "1px solid black", padding: "10px" });
  setTimeout("window.print();print_Pedido(2)", 500);
}

function AgregarPagoModal() {
  $("#input_pago,#input_concepto").val("");
  $("#ModalPagos").modal("show");
  $("#input_pago").focus();
}

function agregarPago() {
  const monto = $("#input_pago").val().trim();
  const concepto = $("#input_concepto").val().trim();
  if (parseInt(monto) < 0) {
    swal("El pago no puede ser negativo!", "", "error");
  } else {
    if (!isNaN(monto) && parseInt(monto) > 0) {
      $("#monto_pago").html(parseFloat(monto).toFixed(2).toString());
    }
    $("#ModalPagos").attr("x-monto", monto);
    $("#ModalPagos").attr("x-concepto", concepto);
    $("#ModalPagos").modal("hide");
  }
}

function setFechaEntrega() {
  const cant_dias = document.getElementById("dias_produccion").value;
  $("#fechaentrega_picker").datepicker(
    "option",
    "minDate",
    parseInt(cant_dias)
  );
  let fecha = new Date();
  fecha.setDate(fecha.getDate() + parseInt(cant_dias));
  $("#fechaentrega_picker").val(
    ("0" + fecha.getDate()).slice(-2) +
      "/" +
      ("0" + (fecha.getMonth() + 1)).slice(-2) +
      "/" +
      fecha.getFullYear()
  );
}

function editarPedido(id) {
  $("body").attr("x-pedido-edit", "");
  $.ajax({
    beforeSend: function () {},
    url: "data_cargar_pedido.php",
    type: "POST",
    data: {
      id: id,
      consulta: "get_pedido_para_editar",
    },
    success: function (x) {
      if (!location.href.includes("ver_stock")) {
        return;
      }

      document.getElementById("btn-tab-cuaderno").click();

      console.log(x);
      try {
        const data = JSON.parse(x);
        const { pedido, productos } = data;
        const { id_cliente, observaciones } = pedido;

        $("#select-cliente").val(id_cliente).selectpicker("refresh");
        $("#observaciones_txt").val(
          observaciones && observaciones.length ? observaciones : ""
        );

        if (productos && productos.length) {
          $("body").attr("x-pedido-edit", id);
          productos.forEach(function (p) {
            funcAddToPedidoEdit(
              p.producto,
              p.id_articulo,
              p.variedad,
              p.bandeja,
              p.cant_plantas,
              p.cant_band,
              p.cant_semi,
              p.fecha_entrega,
              p.fecha_siembraestimada,
              p.con_semilla,
              p.id_subtipo,
              p.subtipo,
              p.tipo,
              p.id_tipo
            );
          });
        }
      } catch (error) {}
    },
    error: function (jqXHR, estado, error) {},
  });
}

function funcAddToPedidoEdit(
  producto,
  id_articulo,
  variedad,
  bandeja,
  cantidad_plantas,
  cantidad_bandejas,
  cantidad_semillas,
  fecha_entrega,
  fecha_siembra,
  con_semilla,
  id_subtipo,
  subtipo,
  tipo,
  id_tipo
) {
  nombre_producto =
    producto +
    " " +
    subtipo +
    " " +
    variedad +
    " x" +
    bandeja +
    (con_semilla == 1 ? " CON " : " SIN ") +
    "SEMILLA";

  if ($(".pedido-vacio-msg").length) {
    $("#tabla_detail tbody").html("");
  }

  let celda = `<tr 
      x-id-variedad="${id_articulo}"
      x-id-subtipo="${id_subtipo}"
      x-id-tipo="${id_tipo}"
      x-variedad="${variedad}"
      x-subtipo="${subtipo}"
      x-tipo="${tipo}"
      x-cant-plantas="${cantidad_plantas}"
      x-cant-semillas="${cantidad_semillas}"
      x-cant-bandejas="${cantidad_bandejas}"
      x-fecha-siembra="${fecha_siembra}"
      x-fecha-entrega="${fecha_entrega}"
      x-con-semilla="${con_semilla}"
      x-tipo-bandeja="${bandeja}"
      x-nombre-producto="${nombre_producto.replace('"', "")}"
    >
    <td>${nombre_producto}</td>
    <td>${cantidad_plantas}</td>
    <td>${cantidad_semillas}</td>
    <td>${cantidad_bandejas}</td>
    <td>${fecha_siembra}</td>
    <td>${fecha_entrega}</td>
    <td class="text-center">
      <div class='d-flex flex-row'>
        <button class='btn btn-sm btn-danger' onclick='eliminar_art(this)'><i class='fa fa-trash'></i></button>
        <button class='btn btn-sm btn-primary ml-2' onclick='modalAgregarProducto($(this).closest("tr"))'><i class='fa fa-edit'></i></button>
      </div>
    </td>
    </tr>`;
  $("#tabla_detail tbody").append(celda);
}
