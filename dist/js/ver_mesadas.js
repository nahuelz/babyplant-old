let id_mesa_global = null;

if (document.location.href.includes("ver_mesadas")) {
  $(document).ready(function () {
    loadMesadasModal(null, true);
  });
}

function CrearMesada() {
  $("#select_producto").find("option").remove();
  $("#select_producto").val("default").selectpicker("refresh");
  $("#capacidad_txt").val("500");
  pone_tiposdeproducto(null);
  id_mesa_global = null;
  $("#titulo_modal").html("Agregar Mesada");
  $("#ModalMesadas").modal("show");

  $("#capacidad_txt").focus();
}

function GuardarMesada() {
  let capacidad = document.getElementById("capacidad_txt").value;
  if (isNaN(capacidad)) {
    swal("ERROR", "Ingresaste una cantidad inválida", "error");
  } else if (parseInt(capacidad) < 1) {
    swal("ERROR", "La capacidad debe ser mayor a cero", "error");
  } else {
    CerrarModalMesadas();
    let tipo_operacion = $("#titulo_modal").text();
    let consulta = "";
    if (tipo_operacion.includes("Agregar")) {
      consulta = "crear_mesada";
    } else {
      consulta = "editar_mesada";
    }
    let id_tipo = 0;
    if ($("#select_producto").find("option:selected").text().length > 0) {
      id_tipo = parseInt($("#select_producto").find("option:selected").val());
    }
    $.ajax({
      beforeSend: function () {},
      url: "data_ver_mesadas.php",
      type: "POST",
      data: {
        consulta: consulta,
        capacidad: parseInt(capacidad),
        id_tipo: id_tipo,
        id_mesa_global: id_mesa_global,
      },
      success: function (x) {
        if (x.includes("error")) {
          swal("ERROR", "Debes asignar una capacidad mayor", "error");
        } else {
          swal("La mesada fue configurada correctamente!", "", "success");
          loadMesadasModal(null, true);
        }
      },
      error: function (jqXHR, estado, error) {},
    });
  }
}

function pone_tiposdeproducto(id) {
  $.ajax({
    beforeSend: function () {
      $("#select_producto").html("Cargando productos...");
    },
    url: "pone_tiposdeproducto.php",
    type: "POST",
    data: { tipo: "pone_tiposdeproducto" },
    success: function (x) {
      $("#select_producto").selectpicker();
      $("#select_producto")
        .html(x)
        .append('<option value="0">NINGUNO</option>')
        .selectpicker("refresh");
      if (id != undefined) {
        $("#select_producto").selectpicker("val", [id.replace("tipo_", "")]);
      }
    },

    error: function (jqXHR, estado, error) {},
  });
}

function CerrarModalMesadas() {
  $("#ModalMesadas").modal("hide");
}

function CerrarModalVerMesada() {
  $("#ModalVerMesada").modal("hide");
}

function CerrarModalEnviarMesadas() {
  $("#ModalEnviaraMesadas").modal("hide");
}

function editMesada(id) {
  let num_mesada = id.replace("edit_", "");
  id_mesa_global = num_mesada;
  $("#capacidad_txt").val($("#capacidad_" + num_mesada).text());
  $("#titulo_modal").html("Editar Mesada");
  let id_tipo = $("#mesada_" + num_mesada)
    .find(".id_tipo")
    .attr("id");
  pone_tiposdeproducto(id_tipo);
  $("#ModalMesadas").modal("show");

  $("#capacidad_txt").focus();
}

function click_mesada(obj) {
  const id_mesada = $(obj).attr("x-id-mesada");
  $.ajax({
    beforeSend: function () {
      $("#tabla_contenidomesada tbody").html("Cargando productos...");
    },
    url: "data_ver_mesadas.php",
    type: "POST",
    data: { consulta: "cargar_infomesada", id_mesada: id_mesada },
    success: function (x) {
      console.log(x)
      $("#tabla_contenidomesada tbody").html(x);
      $("#num_mesadaview").html(id_mesada);
      $("#ModalVerMesada").modal("show");
      
    },
    error: function (jqXHR, estado, error) {},
  });
}

function MostrarModalEntregar(objeto) {
  swal(
    'Las entregas deben realizarse en la sección "Planificación de Entregas"',
    "",
    "info"
  );
}

function VerEstadoOrden(id) {
  if (!id) return;
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



function ModificarMesadas(id) {
  loadMesadasModal(true);
  $(".cantidadbox").remove();
  $("#bandejas_pendientes").html(
    "Bandejas a Organizar: " + $("#cantidad_bandejas").text()
  );
  $("#quedan_bandejas").html($("#cantidad_bandejas").text());
  $("#ModalEnviaraMesadas").modal("show");
}

function click_mesada2(id) {
  let id_mesada = $(id).attr("id").replace("mesada_", "");
  if ($(".mesada-clicked").length <= 1 && !$(id).hasClass("mesada-clicked")) {
    $(id).addClass("mesada-clicked");
    codigo = `<div class='row cantidadbox' id='cantidadtxt_${id_mesada}'>
        <div class='col-md-8'>
          <label class='control-label'>Cantidad Mesada ${id.id.replace(
            "mesada_",
            ""
          )}:</label>
          <input type='number' id='input_${id.id.replace(
            "mesada_",
            ""
          )}' min='0' step='1' class='form-control cantidadmesada' onchange='setFaltante()' onkeyup='this.onchange();' onpaste='this.onchange();' oninput='this.onchange();'>
        </div>
      </div>`;

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
    $(id).removeClass("mesada-clicked");
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

function GuardarReasignacionMesadas() {
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
    if (escero == true) {
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
          "ERROR",
          "No hay suficiente lugar libre para colocar las bandejas ingresadas",
          "error"
        );
      } else {
        CerrarModalEnviarMesadas();
        $("#ModalVerEstado").modal("hide");
        CerrarModalVerMesada();
        const jsonarray = JSON.stringify(arraymesadas);
        $.ajax({
          beforeSend: function () {},
          url: "data_ver_mesadas.php",
          type: "POST",
          data: {
            jsonarray: jsonarray,
            consulta: "guardar_reasignacion_mesadas",
            id_orden: id_orden,
          },
          success: function (x) {
            if (x.trim() == "success") {
              if (document.location.href.includes("ver_mesadas")) {
                loadMesadasModal(null, true);
              } else if (
                document.location.href.includes("ver_pedidos") ||
                document.location.href.includes("ver_ordenes")
              ) {
                busca_entradas();
              }
              swal(
                "Las mesadas fueron reasignadas correctamente!",
                "",
                "success"
              );
            } else {
              swal("Ocurrió un error al reasignar las mesadas", x, "error");
            }
          },
          error: function (jqXHR, estado, error) {
            swal("Ocurrió un error al reasignar las mesadas", error, "error");
          },
        });
      }
    }
  }
}

function ModificarCantidad(id_orden, objeto) {
  let cantiold = $(objeto).text();
  $("#cantidad_bandejas_nueva").val(cantiold);
  $("#ModalModificarCantidad").modal("show");
  $("#cantidad_bandejas_nueva").focus();
  $("#id_orden_mesada").html(id_orden);
}

function GuardarNuevaCantidad() {
  let nuevacanti = $("#cantidad_bandejas_nueva").val();
  if (isNaN(nuevacanti)) {
    swal("Debes ingresar una cantidad numérica", "", "error");
  } else if (parseInt(nuevacanti) < 0) {
    swal("La cantidad no debe ser menor a cero", "", "error");
  } else if (nuevacanti.trim().length <= 0) {
    swal("Debes ingresar una cantidad", "", "error");
  } else {
    let id_orden = $("#id_orden_mesada").text();
    let id_mesada = $("#num_mesadaview").text();
    $.ajax({
      beforeSend: function () {
        $("#ModalModificarCantidad").modal("hide");
      },
      url: "data_ver_mesadas.php",
      type: "POST",
      data: {
        consulta: "modifica_cantidad",
        cantidad: nuevacanti.trim(),
        id_orden: id_orden,
        id_mesada: id_mesada,
      },
      success: function (x) {
        click_mesada(id_mesada);
        swal(
          "La cantidad de bandejas se modificó correctamente!",
          "",
          "success"
        );
      },
      error: function (jqXHR, estado, error) {
        alert("OCURRIO UN ERROR AL GUARDAR LAS MESADAS, intente nuevamente");
      },
    });
  }
}

function loadMesadasModal(reasignar, editable) {
  $.ajax({
    beforeSend: function () {
      $(reasignar ? ".row-reasignar" : ".row-contenedor").html("");
    },
    url: "data_ver_mesadas.php",
    type: "POST",
    data: { consulta: "cargar_mesadas_camara" },
    success: function (x) {
      if (x && x.length) {
        try {
          let data = JSON.parse(x);
          let color = "white";
          console.log(data)
          data.forEach((e) => {
            const num_mesada = parseInt(e.id_mesada);
            const capacidad = e.capacidad;
            const cantidad = e.cantidad;
            let libres = parseInt(capacidad) - parseInt(cantidad);
            if (libres < 0) libres = 0;
            const tipo_producto = e.nombre ?? "-";
            if (tipo_producto) {
              if (tipo_producto.includes("TOMATE")) {
                color = "#FFACAC";
              } else if (tipo_producto.includes("PIMIENTO")) {
                color = "#BAE1A2";
              } else if (tipo_producto.includes("BERENJENA")) {
                color = "#D5B4FF";
              } else if (tipo_producto.includes("LECHUGA")) {
                color = "#D7FFBC";
              } else if (tipo_producto.includes("ACELGA")) {
                color = "#BFDCBC";
              } else if (tipo_producto.includes("REMOLACHA")) {
                color = "#eba5b5";
              } else if (
                tipo_producto.includes("COLES") ||
                tipo_producto.includes("HINOJO") ||
                tipo_producto.includes("APIO")
              ) {
                color = "#58ACFA";
              } else if (
                tipo_producto.includes("VERDEO") ||
                tipo_producto.includes("PUERRO")
              ) {
                color = "#F7BE81";
              } else {
                color = "#A9F5F2";
              }
              if (libres == 0) {
                color = "#A4A4A4";
              }
            } else {
              if (libres == 0) {
                color = "#A4A4A4";
              } else {
                color = "#A9F5F2";
              }
            }

            const codigomesada = `
                <div x-id-mesada='${num_mesada}' id='mesada_${num_mesada}' class='mesabox' onClick='click_mesada${
              reasignar ? "2" : ""
            }(this)' style='width:14em;background-color:${color};'>
                      <div class="row">
                        <div class="col text-center">
                          <div class='id_tipo'>${tipo_producto}
                          </div>
                        </div>
                      </div>
                      <div class="row">
                        <div class="col text-center">
                          Capacidad:
                          <span id='capacidad_${num_mesada}'>${capacidad}</span> - Libres: 
                          <span id='libres_${num_mesada}'>${libres}</span>
                        </div>
                      </div>
                      <div class="row">
                        <div class="col text-right">
                          <div class='pr-2 pb-2'><b>${num_mesada.toString()}</b>
                          </div>
                        </div>
                      </div>
                    </div>
              `;

            if (editable) {
              const codigoboton = `<button class='btn btn-sm btn-primary' style='font-size:1.2em' id='edit_${num_mesada}' onClick='editMesada(this.id)'>
                          <i class='fa fa-edit'>
                          </i>
                        </button>`;

              if (num_mesada % 2 == 1) {
                // IMPPARR
                $(".row-contenedor").append(`
                      <div class="col-md-6 mb-2">
                        <div class='d-flex' style='justify-content: space-between'>
                          ${codigoboton}
                          ${codigomesada}
                        </div>
                      </div>
                    `);
              } else {
                $(".row-contenedor").append(`
                      <div class="col-md-6 mb-2">
                        <div class='d-flex' style='justify-content: space-between'>
                          ${codigomesada}
                          ${codigoboton}
                        </div>
                      </div>
                    `);
              }
            } else {
              $(reasignar ? ".row-reasignar" : ".row-contenedor").append(`
                  <div class="col-md-6 mb-3">
                    <div class='d-flex' style='justify-content: space-between'>
                      ${codigomesada}
                    </div>
                  </div>
                `);
            }
          });
        } catch (error) {}
      }
    },
    error: function (jqXHR, estado, error) {},
  });
}
