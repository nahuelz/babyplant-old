if (!document.location.href.includes("ver_planentregas")) {
  $(function () {
    $("#daterange-btn").daterangepicker(
      {
        ranges: {
          Hoy: [moment(), moment()],
          Ayer: [moment().subtract(1, "days"), moment().subtract(1, "days")],
          "SEMANA PASADA": [
            moment().startOf("isoWeek").subtract(7, "days"),
            moment().startOf("isoWeek").subtract(1, "days"),
          ],
          "Los ultimos 7 dias": [moment().subtract(6, "days"), moment()],
          "Los ultimos 30 dias": [moment().subtract(29, "days"), moment()],
          "Los ultimos 3 meses": [moment().subtract(90, "days"), moment()],
          "Este mes": [moment().startOf("month"), moment().endOf("month")],
          //'El mes pasado': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')],
          "Todo el año": [moment().startOf("year"), moment()],
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
}



if (document.location.href.includes("ver_pedidos")) {
  $(document).ready(function () {
    busca_entradas();
    pone_tipos();
  });

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

    if (revision && revision.length) revision = parseInt(revision);
    if (revision && solucion.length) solucion = parseInt(solucion);
    let filtros = {
      tipo: tipos,
      subtipo: subtipos,
      variedad: variedad,
      cliente: cliente,
      estado: estados,
      revision: revision,
      solucion: solucion,
    };
    filtros = JSON.stringify(filtros);

    $.ajax({
      beforeSend: function () {
        $("#tabla_entradas").html(
          "<h4 class='ml-1'>Buscando pedidos, espere...</h4>"
        );
      },
      url: "data_ver_pedidos.php",
      type: "POST",
      data: {
        consulta: "busca_pedidos",
        fechai: fecha,
        fechaf: fechaf,
        filtros: filtros,
      },
      success: function (x) {
        $("#tabla_entradas").html(x);
        $("#tabla").DataTable({
          pageLength: 50,
          order: [[0, "desc"]],
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
}
function modalEstadoPedido(id_pedido, fecha) {
  $.ajax({
    url: "cargar_detallepedido.php",
    type: "POST",
    data: { id_pedido: id_pedido, consulta: "pedido" },
    success: function (x) {
      $("#tabla_detallepedido > tbody").html(x);
      $(".num_pedido").html(id_pedido);
      $(".fecha_pedido").html(fecha);
    },
    error: function (jqXHR, estado, error) {
      $("#tabla_detallepedido").html(
        "Hubo un error al cargar la información del pedido"
      );
    },
  });

  $.ajax({
    beforeSend: function () {},
    url: "cargar_detallepedido.php",
    type: "POST",
    data: { id_pedido: id_pedido, consulta: "cliente" },
    success: function (x) {
      $("#nombre_cliente").html(x);
    },
    error: function (jqXHR, estado, error) {
      $("#nombre_cliente").html(
        "Hubo un error al cargar la información del pedido"
      );
    },
  });
  $("#ModalVerPedido").modal("show");
}

function eliminar_producto(id_art) {
  let items =
    parseInt(document.getElementById("tabla_detallepedido").rows.length) - 1;

  if (items == 1) {
    swal(
      "No se puede eliminar el producto si sólo hay uno en el pedido",
      "",
      "error"
    );
  } else {
    swal("Estás seguro de Eliminar este Producto del Pedido?", "", {
      icon: "warning",
      buttons: {
        cancel: "Salir",
        catch: {
          text: "Sí, ELIMINAR",
          value: "catch",
        },
      },
    }).then((value) => {
      switch (value) {
        case "catch":
          let id_pedido = $(".num_pedido").first().text();

          $.ajax({
            beforeSend: function () {},
            url: "data_ver_pedidos.php",
            type: "POST",
            data: { consulta: "eliminar_producto_pedido", id_art: id_art },
            success: function (x) {
              if (x.trim() == "success") {
                modalEstadoPedido(id_pedido);
                swal("Eliminaste el Producto correctamente!", "", "error")
              } else {
                swal("Ocurrió un error al eliminar el Producto", x, "error");
              }
            },
            error: function (jqXHR, estado, error) {
              swal("Ocurrió un error al eliminar el Producto", error, "error");
            },
          });
          break;
      }
    });
  }
}

function printCliente_verpedidos(tipo) {
  if (tipo == 1) {
    $("#ModalVerPedido").modal("hide")
    func_printCliente2();
    document.getElementById("ocultar").style.display = "none";
    document.getElementById("miVentana").style.display = "block";
  } else {
    document.getElementById("ocultar").style.display = "block";
    document.getElementById("miVentana").style.display = "none";
    $("#ModalVerPedido").modal("show")
  }
}

function func_printCliente2() {
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
  $("#miVentana").append(document.getElementById("tablita").innerHTML);
  $("#miVentana").find("th:first").remove();
  $("#miVentana").find("th:last").remove();
  $("#miVentana").find("th:eq(2)").remove();
  $("#miVentana")
    .find("#tabla_detallepedido tr")
    .each(function () {
      $(this).find("td:first").remove();
      $(this).find("td:last").remove();
      $(this).find("td:eq(2)").remove();
    });

  $("#miVentana").find("tr").css({ "font-size": "23px" });

  $("#miVentana").find("#table_total").remove();
  setTimeout("window.print();printCliente_verpedidos(2)", 500);
}

function printInterno1(tipo) {
  if (tipo == 1) {
    func_printInterno();

    document.getElementById("ocultar").style.display = "none";

    document.getElementById("miVentana").style.display = "block";
  } else {
    document.getElementById("ocultar").style.display = "block";

    document.getElementById("miVentana").style.display = "none";

    $("#miVentana").html("");
  }
}

function func_printInterno() {
  let cliente = $("#nombre_cliente").text();
  let codigo = "";
  $("#tabla_detallepedido > tbody")
    .find("tr")
    .each(function (e) {
      codigo += `
    <div class='mt-3' style='display: block; page-break-before: always;'><h4>Cliente: ${cliente}</h4>
      <table> 
      <thead>
        <tr> 
        <th>Producto</th> 
        <th>Bandejas/Plantas</th> 
        <th>Siembra Estimada</th> 
        <th>Entrega Estimada</th> 
        </tr>
      </thead>
      <tbody> 
      <tr> 
        <td> 
        ${$(this).find("td:eq(1)").text()} 
        </td> 
        <td class='font-size:12px !important;text-align:center;'> 
        ${$(this).find("td:eq(2)").html()} 
        </td>  
        <td> 
        ${$(this).find("td:eq(3)").text()} 
        </td>  
        <td>  
        ${$(this).find("td:eq(4)").text()} 
        </td> 
      </tr> 
      <tr> 
        <td colspan=4><b>Nº de Sobres:</b></td> 
      </tr>
      </tbody> 
    </table></div>`;
    });

  $("#miVentana").html(codigo);

  $("#miVentana").find("table").css({ border: "1px solid black" });

  $("#miVentana").find("tr").css({ "font-size": "12px" });

  $("#miVentana")
    .find("th,td")
    .css({ border: "1px solid black", padding: "10px" });

  setTimeout("window.print();printInterno1(2)", 500);
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
    "<div align='center'><img src='dist/img/babyplant.png' style='width: 180px;height:55px;'></img>";

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

function expande_busqueda() {
  let contenedor = $("#contenedor_busqueda");
  if ($(contenedor).css("display") == "none")
    $(contenedor).css({ display: "block" });
  else {
    $(contenedor).css({ display: "none" });
    $(
      "#select_tipo_filtro,#select_estado,#busca_tiporevision,#busca_tiposolucion"
    )
      .val("default")
      .selectpicker("refresh");
    $("#busca_subtipo,#busca_variedad,#busca_cliente").val("");
  }
}

function quitar_filtros() {
  $(
    "#select_tipo_filtro,#select_estado,#busca_tiporevision,#busca_tiposolucion"
  )
    .val("default")
    .selectpicker("refresh");
  $("#busca_subtipo,#busca_variedad,#busca_cliente").val("");
  busca_entradas();
}

function mostrarModalRevision(id_artpedido) {
  $("#select_tiporevision").val("default").selectpicker("refresh");
  $("#id_artpedidorevision").html(id_artpedido);
  $("#ModalRevision").modal("show");
}

function GuardarProblema() {
  let observaciones = $("#obsproblema_txt").val().trim();

  if (observaciones.length == 0) observaciones = null;

  let id_artpedido = $("#id_artpedidoproblema").text();

  $.ajax({
    beforeSend: function () {
      $("#ModalProblema").modal("hide");
    },
    url: "data_ver_pedidos.php",
    type: "POST",
    data: {
      id_artpedido: id_artpedido,
      consulta: "marcarproblema",
      observaciones: observaciones,
    },
    success: function (x) {
      MostrarModalEstado(id_artpedido);
      busca_entradas();
      swal("Listo!", "Marcaste un Problema", "success");
    },
    error: function (jqXHR, estado, error) {
      console.log("ERROR");
    },
  });
}

function marcarProblema(id_artpedido) {
  $("#obsproblema_txt").val("");
  $("#id_artpedidoproblema").html(id_artpedido.toString());
  $("#ModalProblema").modal("show");
  $("#obsproblema_txt").focus();
}

function sendToRevision() {
  let id_artpedido = $("#id_artpedidorevision").text();
  let tipo_revision = $("#select_tiporevision").val();

  if (tipo_revision == null) {
    swal("ERROR!", "Debes elegir un tipo de revisión", "error");
  } else {
    $.ajax({
      beforeSend: function () {
        $("#ModalRevision").modal("hide");
      },
      url: "data_ver_pedidos.php",
      type: "POST",
      data: {
        tipo_revision: tipo_revision,
        id_artpedido: id_artpedido,
        consulta: "enviar_a_revision",
      },
      success: function (x) {
        if (x.trim() == "success") {
          MostrarModalEstado(id_artpedido);
          busca_entradas();
          swal("Listo!", "Marcaste una Revisión", "success");
        } else {
          swal("Ocurrió un error al marcar la revisión", x, "error");
        }
      },
      error: function (jqXHR, estado, error) {
        console.log("ERROR");
      },
    });
  }
}

function quitarRevision(id_artpedido) {
  $.ajax({
    beforeSend: function () {
      $("#btn_quitar_revision").css({ display: "none" });
    },
    url: "data_ver_pedidos.php",
    type: "POST",
    data: { id_artpedido: id_artpedido, consulta: "quitar_revision" },
    success: function (x) {
      MostrarModalEstado(id_artpedido);
      busca_entradas();
      swal("Los cambios se guardaron correctamente!", "", "success");
    },
    error: function (jqXHR, estado, error) {
      console.log("ERROR");
    },
  });
}

function quitarProblema(id_artpedido) {
  $.ajax({
    beforeSend: function () {
      $("#btn_quitar_problema").css({ display: "none" });
    },
    url: "data_ver_pedidos.php",
    type: "POST",
    data: { id_artpedido: id_artpedido, consulta: "quitarproblema" },
    success: function (x) {
      MostrarModalEstado(id_artpedido);
      busca_entradas();
      swal("Los cambios se guardaron correctamente!", "", "success");
    },
    error: function (jqXHR, estado, error) {
      console.log("ERROR");
    },
  });
}

function MostrarModalSolucion(id_artpedido) {
  $("#select_tiposolucion").val("default").selectpicker("refresh");
  $("#id_artpedidosolucion").html(id_artpedido);
  $("#ModalSolucion").modal("show");
}

function aplicarSolucion() {
  let id_artpedido = $("#id_artpedidosolucion").text();
  let tipo_revision = $("#select_tiposolucion").val();

  if (tipo_revision == null) {
    swal("ERROR!", "Debes elegir un tipo de solución", "error");
  } else {
    $.ajax({
      beforeSend: function () {
        $("#ModalSolucion").modal("hide");
      },
      url: "data_ver_pedidos.php",
      type: "POST",
      data: {
        tipo_revision: tipo_revision,
        id_artpedido: id_artpedido,
        consulta: "aplicar_solucion",
      },
      success: function (x) {
        MostrarModalEstado(id_artpedido);
        swal(
          "Listo! Para ver los cambios debes actualizar la página o hacer una nueva búsqueda",
          "",
          "success"
        );
      },
      error: function (jqXHR, estado, error) {
        console.log("ERROR");
      },
    });
  }
}

function MostrarModalEstado(id) {
  $("#ModalVerEstado").attr("x-id-artpedido", id);
  $("#ModalVerEstado").modal("show");
  $.ajax({
    beforeSend: function () {},
    url: "cargar_detalleestadopedido.php",
    type: "POST",
    data: { id: id, consulta: "pedido" },
    success: function (x) {
      $("#box_info").html(x);
    },
    error: function (jqXHR, estado, error) {
      $("#box_info").html("Hubo un error al cargar la información del pedido");
    },
  });
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

function MostrarModalPago(id) {
  consulta = "cargar_pagos";

  $.ajax({
    beforeSend: function () {},

    url: "cargar_detalleestadopedido.php",

    type: "POST",

    data: {
      id_artpedido: id.replace("btnpago_", ""),
      consulta: consulta,
    },

    success: function (x) {
      $("#tabla_pagos > tbody").html(x);
    },

    error: function (jqXHR, estado, error) {
      $("#tabla_pagos").html("ERROR AL CARGAR PAGOS");
    },
  });
  setFilter();
  $("#id_pedidoref").html(id.replace("btnpago_", ""));
  $("#ModalPagos").modal("show");
  $("#input_pago").val("").focus();
  $("#input_concepto").val("");
}

function CerrarModalPago() {
  $("#ModalPagos").modal("hide");
  MostrarModalEstado($("#id_pedidoref").text());
}

function cambiarCliente(id_artpedido, estado) {
  $("#ModalCambioCliente")
    .attr("x-id-artpedido", id_artpedido)
    .attr("x-estado", estado);
  $("#select_modocambio").html("");
  $("#select_modocambio").append(`
    <option value="1">PERMUTAR POR ORDEN DE SIEMBRA DE OTRO CLIENTE</option>
    <option value="2">CREAR NUEVO PEDIDO ASIGNANDO ÉSTA ORDEN A OTRO CLIENTE</option>
  `);
  if (estado == 4) {
    //INVERNACULO
    $("#select_modocambio").append(`
      <option value="3">ENVIAR TODAS LAS BANDEJAS A STOCK</option>
    `);
  }
  const bandejas_pedidas = $("#bandejaspedidas_original").html().length
    ? parseInt($("#bandejaspedidas_original").html())
    : null;
  const bandejas_sembradas = $("#cantidad_bandejas").html().length
    ? parseInt($("#cantidad_bandejas").html())
    : null;

  if (
    bandejas_pedidas &&
    bandejas_sembradas &&
    bandejas_pedidas < bandejas_sembradas &&
    estado >= 4 &&
    estado <= 7
  ) {
    //INVERNACULO - PARA ENTREGAR - ENTREGADO
    $("#select_modocambio").append(`
      <option x-sobran='${
        bandejas_sembradas - bandejas_pedidas
      }' value="4">ENVIAR BANDEJAS SOBRANTES A STOCK (SOBRAN ${
      bandejas_sembradas - bandejas_pedidas
    })</option>
    `);
    $("#ModalCambioCliente").attr(
      "x-sobran",
      bandejas_sembradas - bandejas_pedidas
    );
  } else {
    $("#ModalCambioCliente").removeAttr("x-sobran");
  }

  $("#select_modocambio,#select_mesada_stock")
    .val("default")
    .selectpicker("refresh");
  $(".selected2").removeClass("selected2");
  $("#modo_permutar").css({ display: "none" });
  $("#modo_asignarcliente").css({ display: "none" });
  $("#modo_enviar_stock_total,#modo_enviar_stock_sobrante").addClass("d-none");
  $("#ModalCambioCliente").modal("show");
}

function setModoCambio() {
  let tipo_entrega = parseInt(
    $("#select_modocambio").find("option:selected").val()
  );

  const id_artpedido = $("#ModalCambioCliente").attr("x-id-artpedido");
  if (tipo_entrega == 1) {
    //PERMUTAR
    $("#modo_asignarcliente").css({ display: "none" });
    $("#modo_enviar_stock_total,#modo_enviar_stock_sobrante").addClass(
      "d-none"
    );
    $.ajax({
      url: "data_ver_pedidos.php",
      type: "POST",
      data: {
        id_artpedido: id_artpedido,
        consulta: "cargar_ordenes_similares",
      },
      success: function (x) {
        $("#tabla_ordenes_similares tbody").html(x);
        document.getElementById("modo_permutar").style.display = "block";
      },
      error: function (jqXHR, estado, error) {},
    });
  } else if (tipo_entrega == 2) {
    //CREAR NUEVO PEDIDO
    $("#modo_enviar_stock_total,#modo_enviar_stock_sobrante").addClass(
      "d-none"
    );
    document.getElementById("modo_permutar").style.display = "none";
    pone_clientes();

    document.getElementById("modo_asignarcliente").style.display = "block";
  } else if (tipo_entrega == 3) {
    // ENVIAR TODO A STOCK
    document.getElementById("modo_asignarcliente").style.display = "none";
    document.getElementById("modo_permutar").style.display = "none";
    const mesada = $("#ModalVerEstado").find(".label-num-mesada").text();
    $("#ModalCambioCliente")
      .find(".label-mesada-actual")
      .html(` (Actualmente está en la Nº ${mesada})`);
    $("#ModalCambioCliente").attr("x-mesada-actual", mesada);
    getMesadas(id_artpedido, "#select_mesada_stock");
    $("#modo_enviar_stock_sobrante").addClass("d-none");
    $("#modo_enviar_stock_total").removeClass("d-none");
  } else if (tipo_entrega == 4) {
    document.getElementById("modo_asignarcliente").style.display = "none";
    document.getElementById("modo_permutar").style.display = "none";
    const mesada = $("#ModalVerEstado").find(".label-num-mesada").text();
    $("#ModalCambioCliente")
      .find(".label-mesada-actual")
      .html(` (Actualmente está en la Nº ${mesada})`);
    $("#ModalCambioCliente").attr("x-mesada-actual", mesada);
    getMesadas(id_artpedido, "#select_mesada_stock_sobrante");
    $("#modo_enviar_stock_total").addClass("d-none");
    $("#modo_enviar_stock_sobrante").removeClass("d-none");
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
  let tipo_entrega = parseInt(
    $("#select_modocambio").find("option:selected").val()
  );

  if (tipo_entrega == 1) {
    //PERMUTAR
    let seleccionado = document.getElementsByClassName("selected2")[0];

    if (seleccionado) {
      $("#ModalCambioCliente").modal("hide");
      $("#ModalVerEstado").modal("hide");
      const id_artpedidoseleccionado = $(seleccionado).attr("x-id-artpedido");
      const id_clienteseleccionado = $(seleccionado).attr("x-id-cliente");

      const id_artpedido = $("#ModalVerEstado").attr("x-id-artpedido");
      const id_clienteoriginal = $("#id_clienteoriginal").text();

      $.ajax({
        beforeSend: function () {},
        url: "data_ver_pedidos.php",
        type: "POST",
        data: {
          consulta: "permuta_clientes",
          id_artpedido: id_artpedido,
          id_clienteoriginal: id_clienteoriginal,
          id_artpedidoseleccionado: id_artpedidoseleccionado,
          id_clienteseleccionado: id_clienteseleccionado,
        },
        success: function (x) {
          if (x.trim() == "success") {
            swal("El pedido fue modificado correctamente!", "", "success");
            busca_entradas();
          } else {
            swal("Ocurrió un error al modificar el pedido", "", "error");
            console.log(x);
          }
        },
        error: function (jqXHR, estado, error) {
          swal("Ocurrió un error al modificar el pedido", "", "error");
          console.log(estado + " " + error);
        },
      });
    } else {
      swal("ERROR", "Debes seleccionar un pedido!", "success");
    }
  } else if (tipo_entrega == 2) {
    //CREAR NUEVO PEDIDO Y ASIGNARLO A OTRO CLIENTE
    $("#ModalCambioCliente").modal("hide");
    $("#ModalVerEstado").modal("hide");
    const id_artpedido = $("#ModalVerEstado").attr("x-id-artpedido");
    const id_clienteoriginal = $("#id_clienteoriginal").text();
    const id_cliente = $("#select_cliente").find("option:selected").val();
    if (id_cliente && id_cliente.length) {
      $.ajax({
        beforeSend: function () {},
        url: "data_ver_pedidos.php",
        type: "POST",
        data: {
          consulta: "asignar_pedido_otrocliente",
          id_artpedido: id_artpedido,
          id_cliente: id_cliente,
          id_clienteoriginal: id_clienteoriginal,
        },
        success: function (x) {
          if (x.trim() == "success") {
            swal(
              "Asignaste el pedido al nuevo cliente correctamente!",
              "",
              "success"
            );
            busca_entradas();
          } else {
            swal("Ocurrió un error al realizar la operación", "", "error");
            console.log(x);
          }
        },
        error: function (jqXHR, estado, error) {
          alert(estado + " " + error);
        },
      });
    } else {
      swal("ERROR", "Debes elegir un cliente", "error");
    }
  } else if (tipo_entrega == 3) {
    //ENVIAR TODO A STOCK
    const id_artpedido = $("#ModalCambioCliente").attr("x-id-artpedido");
    let id_clienteoriginal = $("#id_clienteoriginal").text();

    const id_mesada = $("#select_mesada_stock").find("option:selected").val();
    if (!id_mesada || !id_mesada.length) {
      swal("Debes elegir una Mesada de Destino!", "", "error");
      return;
    }
    $("#ModalCambioCliente").modal("hide");
    $("#ModalVerEstado").modal("hide");

    $.ajax({
      beforeSend: function () {},
      url: "data_ver_pedidos.php",
      type: "POST",
      data: {
        consulta: "envia_stock_total",
        id_artpedido: id_artpedido,
        id_mesada: id_mesada,
        id_clienteoriginal: id_clienteoriginal,
      },
      success: function (x) {
        if (x.trim() == "success") {
          swal("El pedido fue enviado a Stock!", "", "success");
          busca_entradas();
        } else {
          swal("Ocurrió un error al enviar el pedido a Stock", "", "error");
          console.log(x);
        }
      },
      error: function (jqXHR, estado, error) {
        alert(estado + " " + error);
      },
    });
  } else if (tipo_entrega == 4) {
    //ENVIAR SOBRANTE A STOCK
    const id_artpedido = $("#ModalCambioCliente").attr("x-id-artpedido");
    const id_mesada = $("#select_mesada_stock_sobrante")
      .find("option:selected")
      .val();
    const cantidad = $("#ModalCambioCliente").attr("x-sobran");
    if (!id_mesada || !id_mesada.length) {
      swal("Debes elegir una Mesada de Destino!", "", "error");
      return;
    }

    if (!cantidad || !cantidad.length || parseInt(cantidad) < 1) {
      swal(
        "No hay bandejas sobrantes para enviar a stock",
        `SOBRANTE REGISTRADO: ${cantidad}`,
        "error"
      );
    }
    $("#ModalCambioCliente").modal("hide");
    $("#ModalVerEstado").modal("hide");

    $.ajax({
      beforeSend: function () {},
      url: "data_ver_pedidos.php",
      type: "POST",
      data: {
        consulta: "envia_stock_sobrante",
        id_artpedido: id_artpedido,
        id_mesada: id_mesada,
        cantidad: cantidad,
      },
      success: function (x) {
        if (x.trim() == "success") {
          swal("Enviaste las bandejas sobrantes a Stock!", "", "success");
          busca_entradas();
        } else {
          swal("Ocurrió un error al enviar las bandejas a Stock", "", "error");
          console.log(x);
        }
      },
      error: function (jqXHR, estado, error) {
        alert(estado + " " + error);
      },
    });
  }
}

function ModificarCantidadPedida(id_artpedido) {
  $("#id_artpedidohide3").html(id_artpedido);
  $("#cantidadbandejas_txt").val($("#bandejaspedidas_original").text());
  $("#ModalCambioCantidad").modal("show");
  $("#cantidadbandejas_txt").focus().select();
}

function cancelarPedido() {
  let id_artpedido = $("#ModalVerEstado").attr("x-id-artpedido");

  swal("Estás seguro de Cancelar el Pedido?", "", {
    icon: "warning",
    buttons: {
      cancel: "Salir",
      catch: {
        text: "Sí",
        value: "catch",
      },
    },
  }).then((value) => {
    switch (value) {
      case "catch":
        $.ajax({
          beforeSend: function () {},
          url: "data_ver_pedidos.php",
          type: "POST",
          data: { id_artpedido: id_artpedido, consulta: "cancelar_pedido" },
          success: function (x) {
            if (x.trim() == "success") {
              swal("Cancelaste el pedido correctamente!", "", "success");
              MostrarModalEstado(id_artpedido);
            } else {
              swal("Ocurrió un error al cancelar el pedido", x, "error");
              console.log(x);
            }
          },
          error: function (jqXHR, estado, error) {
            alert(estado + " " + error);
          },
        });
        break;
    }
  });
}

function GuardarCambioCantidad() {
  let estado = $("#estado_txt").text();
  let nuevacant = $("#cantidadbandejas_txt").val();
  let id_artpedido = $("#id_artpedidohide3").text();
  if (estado.includes("PLANIFICADO") || estado.includes("PENDIENTE")) {
    if (parseInt(nuevacant) > 0) {
      $("#ModalCambioCantidad").modal("hide");
      $.ajax({
        url: "cargar_detalleestadopedido.php",
        type: "POST",
        data: {
          consulta: "modificacantidad_simple",
          id_artpedido: id_artpedido,
          nuevacant: nuevacant,
        },
        success: function (x) {
          console.log(x);
          swal(
            "La cantidad de bandejas fue modificada correctamente!",
            "",
            "success"
          );
          MostrarModalEstado(id_artpedido);
        },
        error: function (jqXHR, estado, error) {
          alert(estado + " " + error);
        },
      });
    } else {
      swal("ERROR", "La cantidad debe ser mayor a cero", "error");
    }
  }
}

function ActivarText() {
  $("#observaciones_txt").prop("disabled", false).focus();
  $("#btn_guardarobs").prop("disabled", false);
}

function GuardarObservaciones() {
  let observaciones = $("#observaciones_txt").val().trim();
  let id_artpedido = $("#ModalVerEstado").attr("x-id-artpedido");
  let consulta = "modificar_observaciones";

  $.ajax({
    beforeSend: function () {},
    url: "cargar_detalleestadopedido.php",
    type: "POST",
    data: {
      consulta: consulta,
      id_artpedido: id_artpedido,
      observaciones: observaciones,
    },
    success: function (x) {
      console.log(x);
      swal(
        "Las observaciones fueron modificadas correctamente!",
        "",
        "success"
      );
      MostrarModalEstado(id_artpedido);
    },
    error: function (jqXHR, estado, error) {
      alert(estado + " " + error);
    },
  });
}

function pone_tipos() {
  $.ajax({
    beforeSend: function () {
      $("#select_tipo_filtro").html("Cargando productos...");
    },
    url: "pone_tiposdeproducto.php",
    type: "POST",
    data: { tipo: "pone_tiposdeproducto" },
    success: function (x) {
      $(".selectpicker").selectpicker();
      $("#select_tipo_filtro").html(x).selectpicker("refresh");
      $("#select_tipo_filtro").on(
        "changed.bs.select",
        function (e, clickedIndex, newValue, oldValue) {}
      );
    },
    error: function (jqXHR, estado, error) {},
  });
}

function abrir(element) {
  if (element == 1) element = "archivo";
  else if (element == 2) element = "archivo2";
  else if (element == 3) element = "archivo3";
  let file = document.getElementById(element);
  file.dispatchEvent(
    new MouseEvent("click", {
      view: window,
      bubbles: true,
      cancelable: true,
    })
  );
}

function cambiofoto(tipo) {
  let formData = new FormData();
  let id_art = $("#ModalVerEstado").attr("x-id-artpedido");

  let idobj = "#archivo";
  if (tipo == 2) idobj = "#archivo2";
  else if (tipo == 3) idobj = "#archivo3";

  formData.append("file", $(idobj).prop("files")[0]);

  if (tipo == 1) formData.append("id_artpedido", id_art);
  else if (tipo == 2) formData.append("id_artpedido", id_art + "_2");
  else if (tipo == 3) formData.append("id_artpedido", id_art + "_3");

  if ($(idobj).prop("files")[0] && $(idobj).prop("files")[0] != null) {
    let boton = $("#btn-sacarfoto" + tipo.toString()).html();
    $.ajax({
      url: "subirfoto.php",
      type: "POST",
      data: formData,
      dataType: "script",
      contentType: false,
      processData: false,
      beforeSend: function () {
        $("#btn-sacarfoto" + tipo.toString()).html(
          "<span style='color:red'><b>Subiendo...</b></span>"
        );
      },
      success: function (datos) {
        $("#btn-sacarfoto" + tipo.toString()).html(boton);
        MostrarModalEstado(id_art);
        swal("Listo!", "Subiste la foto correctamente", "success");
      },
      error: function (jqXHR, estado, error) {
        $("#btn-sacarfoto" + tipo.toString()).html(boton);
        swal("ERROR!", error, "error");
      },
    });
  }
}

function verFoto(id, index) {
  let nombrefoto;
  if (index == 1) nombrefoto = id.toString();
  else if (index == 2) nombrefoto = id.toString() + "_" + index.toString();
  else if (index == 3) nombrefoto = id.toString() + "_" + index.toString();
  $("#ocultar").css({ display: "none" });
  $("#miVentana").html(
    `
      <div class='row'>
        <div class='col-md-6'>
          <button id="back-btn" style="display:block;font-size:4em;border-radius:100%;left: 10px;top:10px;" class="btn btn-primary btn-round fa fa-arrow-left" onclick="cerrarFoto()"></button>        
        </div>
        <div class='col-md-6'>
          <button id="rotate-btn" style="display:block;font-size:3em;border-radius:100%;left: 100px;top:10px;" class="pull-right btn btn-primary btn-round" onclick="rotarFoto()"><span style="color:white"><b>GIRAR</b></span></img></button>
        </div>
      </div>
      <div class="row">
        <div class="col">
          <div align="center">
            <img id='current-foto' style="max-width: 100%; height: auto;" src="imagenes/` +
      nombrefoto +
      `.jpg?t=` +
      new Date().getTime() +
      `"></img>
          </div>
        </div>
      </div>
        `
  );

  $("#miVentana").css({ display: "block" });
}

function cerrarFoto() {
  $("#miVentana").css({ display: "none" });
  $("#ocultar").css({ display: "block" });
}

let rotate_angle = 0;

function rotarFoto() {
  rotate_angle = (rotate_angle + 90) % 360;
  $("#current-foto").css({
    transform: "rotate(" + rotate_angle.toString() + "deg)",
  });
}

function getMesadas(id_artpedido, input) {
  $.ajax({
    beforeSend: function () {
      $(input).html("Cargando mesadas...");
    },
    url: "data_ver_pedidos.php",
    type: "POST",
    data: {
      consulta: "cargar_mesadas_disponibles",
      id_artpedido: id_artpedido,
    },
    success: function (x) {
      //alert(x)
      $(".selectpicker").selectpicker();
      $(input).val("default").selectpicker("refresh");
      $(input).html(x).selectpicker("refresh");
    },
    error: function (jqXHR, estado, error) {},
  });
}

function pone_clientes() {
  $.ajax({
    beforeSend: function () {
      $("#select_cliente").html("Cargando lista de clientes...");
    },
    url: "pone_clientes.php",
    type: "POST",
    data: null,
    success: function (x) {
      $("#select_cliente").val("default").html(x).selectpicker("refresh");
    },
    error: function (jqXHR, estado, error) {},
  });
}
