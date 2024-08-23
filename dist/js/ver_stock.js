let tiposLoaded = false;
let currentTab;

$(document).ready(function () {
  document.getElementById("defaultOpen").click();
  pone_clientes_stock("#select_cliente_reservar,#select_cliente_entregar,#select-cliente");
});

function busca_entradas(tabName, id_tipo) {
  $.ajax({
    beforeSend: function () {
      $(".tabla_entradas").html("Buscando stock, espere...");
    },
    url: "data_ver_stock.php",
    type: "POST",
    data: {
      consulta: "busca_" + tabName,
      id_tipo: id_tipo && id_tipo.length ? id_tipo : null,
    },
    success: function (x) {
      $(".tabla_entradas").html(x);
      $(".tabla-stock").DataTable({
        pageLength: 50,
        order: [[tabName == "stock" ? 1 : 0, "desc"]],
        language: {
          lengthMenu: "Mostrando _MENU_ registros por página",
          zeroRecords: "No hay registros",
          info: "Página _PAGE_ de _PAGES_",
          infoEmpty: "No hay registros",
          infoFiltered: "(filtrado de _MAX_ registros en total)",
          lengthMenu: "Mostrar _MENU_ registros",
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
      if (!tiposLoaded) {
        pone_tipos("#select_tipo_filtro");
        tiposLoaded = true;
      }
    },
    error: function (jqXHR, estado, error) {
      $(".tabla_entradas").html(
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
    "<div align='center'><img src='dist/img/babyplant.png' width='160' height='70'></img><h4>BabyPlant</h4>";

  $("#miVentana").html(direccion);

  $("#miVentana").append($(".table").first().parent().html());

  $("#miVentana")
    .find("tr")
    .css({ "font-size": "10px", "word-wrap": "break-word" });

  $("#miVentana").find("td").css({ "word-wrap": "break-word" });

  $("#miVentana").find("th").css({ "word-wrap": "break-word" });

  setTimeout("window.print();print_Busqueda(2)", 500);
}

function toggleSelection(objeto) {
  let tr = $(objeto).parent();
  if (tr.hasClass("selected")) {
    tr.removeClass("selected");
  } else {
    tr.addClass("selected");
  }
}

function DeseleccionarTodo() {
  $(".selected").removeClass("selected");
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

function pone_tipos(input) {
  $.ajax({
    beforeSend: function () {
      $(input).html("Cargando productos...");
    },
    url: "pone_tiposdeproducto.php",
    type: "POST",
    data: { tipo: "pone_tiposdeproducto" },
    success: function (x) {
      $(".selectpicker").selectpicker();
      $(input).html(x).selectpicker("refresh");

      $(input).on(
        "changed.bs.select",
        function (e, clickedIndex, newValue, oldValue) {
          if (input == "#select_tipo_filtro") {
            busca_entradas("stock", this.value);
          } else if (input == "#select-tipo") {
            pone_subtipos(this.value);
            pone_bandejas(this.value);
          }
        }
      );
    },
    error: function (jqXHR, estado, error) {},
  });
}

function carga_bandejas(id_tipo) {
  $.ajax({
    beforeSend: function () {
      $("#select-bandeja").html("Cargando bandejas...");
    },
    url: "pone_tiposdeproducto.php",
    type: "POST",
    data: { tipo: "carga_bandejas", id_tipo: id_tipo },
    success: function (x) {
      $(".selectpicker").selectpicker();
      $("#select-bandeja").val("default").selectpicker("refresh");
      $("#select-bandeja").html(x).selectpicker("refresh");
      $("#select-bandeja").on(
        "changed.bs.select",
        function (e, clickedIndex, newValue, oldValue) {
          $("#cantidad_bandejas").val("");

          $("#cantidad_bandejas").focus();
        }
      );
    },
    error: function (jqXHR, estado, error) {},
  });
}

function getMesadas(valor) {
  $.ajax({
    beforeSend: function () {
      $("#select_mesada").html("Cargando mesadas...");
    },
    url: "data_ver_stock.php",
    type: "POST",
    data: { consulta: "cargar_disponibles", cantidad: valor },
    success: function (x) {
      $(".selectpicker").selectpicker();
      $("#select_mesada").val("default").selectpicker("refresh");
      $("#select_mesada").html(x).selectpicker("refresh");
    },
    error: function (jqXHR, estado, error) {},
  });
}

function toggleselection(objeto) {
  let tr = $(objeto);
  if (tr.hasClass("selected2")) {
    tr.removeClass("selected2");
  } else {
    tr.addClass("selected2");
  }
}

function eliminar_stock() {
  let seleccionados = document.getElementsByClassName("selected2");
  if (seleccionados && seleccionados.length) {
    swal(
      "Estás seguro de eliminar los productos seleccionados de STOCK?",
      "ATENCIÓN: Se eliminarán también las reservas asociadas a estas bandejas en Stock.",
      {
        icon: "warning",
        buttons: {
          cancel: "Cancelar",
          catch: {
            text: "Eliminar",
            value: "catch",
          },
        },
      }
    ).then((value) => {
      switch (value) {
        case "catch":
          let array = [];
          for (let i = 0; i < seleccionados.length; i++) {
            array.push({
              id_stock: $(seleccionados[i]).attr("x-id"),
              tipo_stock: $(seleccionados[i]).attr("x-origen-short"),
            });
          }
          $.ajax({
            beforeSend: function () {},
            url: "data_ver_stock.php",
            type: "POST",
            data: {
              consulta: "eliminar_stock",
              jsonarray: JSON.stringify(array),
            },
            success: function (x) {
              if (x.trim() == "success") {
                swal(
                  "Éxito!",
                  "Se eliminaron correctamente los productos",
                  "success"
                );
                busca_entradas("stock", null);
              } else {
                swal("Ocurrió un error", x, "error");
              }
              //console.log(x);
            },
            error: function (jqXHR, estado, error) {},
          });

          break;

        default:
          break;
      }
    });
  }
}

function pone_clientes_stock(objeto) {
  $.ajax({
    beforeSend: function () {
      $(objeto).html("Recuperando lista de clientes...");
    },
    url: "pone_clientes.php",
    type: "POST",
    data: null,
    success: function (x) {
      $(objeto).html(x).selectpicker("refresh");
      $(objeto).on(
        "changed.bs.select",
        function (e, clickedIndex, newValue, oldValue) {}
      );
    },
    error: function (jqXHR, estado, error) {},
  });
}

function ModalDevoluciones() {
  pone_clientes_stock("#select_cliente2");
  $("#select_cliente2").on(
    "changed.bs.select",
    function (e, clickedIndex, newValue, oldValue) {
      cargar_pedidos(this.value);
    }
  );
  $("#tabla_busqueda > tbody").html("");
  $("#cantidad_devolucion").val("");
  $("#select_cliente2").val("default").selectpicker("refresh");
  $("#select_mesada2").val("default").selectpicker("refresh");
  $("#select_mesada2").find("option").remove();
  $("#ModalDevoluciones").modal("show");
}

function cargar_pedidos(id_cliente) {
  let consulta = "buscar_pedidos";
  $("#tabla_busqueda > tbody").html("");
  $.ajax({
    beforeSend: function () {},
    url: "data_ver_stock.php",
    type: "POST",
    data: { consulta: consulta, id_cliente: id_cliente },
    success: function (x) {
      $("#tabla_busqueda > tbody").html(x);
    },
    error: function (jqXHR, estado, error) {
      $("#tabla_busqueda").html(
        "Hubo un error al cargar la información del pedido"
      );
    },
  });
}

function selectDevol(id_artpedido, objeto) {
  let tr = $(objeto);
  if (tr.hasClass("seldevol")) {
    tr.removeClass("seldevol");
  } else {
    $(".seldevol").removeClass("seldevol");
    tr.addClass("seldevol");
    $("#cantidad_devolucion").focus();
  }
}

function guardarDevolucion() {
  let seleccionado = document.getElementsByClassName("seldevol");
  let cantidad = $("#cantidad_devolucion").val();
  let cantmax = parseInt($(seleccionado[0]).find("td:eq(2)").text());
  if (!seleccionado || !seleccionado.length) {
    swal("Debes seleccionar un pedido para devolver!", "", "error");
  } else if (
    cantidad.trim().length == 0 ||
    isNaN(cantidad) ||
    parseInt(cantidad) <= 0
  ) {
    swal("Debes ingresar una cantidad superior a cero!", "", "error");
  } else if ($("#select_mesada2").val().trim().length < 1) {
    swal("Debes elegir una mesada!", "", "error");
  } else if (isNaN(cantmax) || cantmax < parseInt(cantidad)) {
    swal(
      "La cantidad que ingresaste es superior a la que el cliente se llevó",
      "",
      "error"
    );
  } else {
    let id_mesada = $("#select_mesada2").find("option:selected").val();
    let id_artpedido = $(seleccionado[0]).attr("x-id-artpedido");
    $("#ModalDevoluciones").modal("hide");
    $.ajax({
      beforeSend: function () {},
      url: "data_ver_stock.php",
      type: "POST",
      data: {
        consulta: "devolver",
        id_artpedido: id_artpedido,
        cantidad: cantidad,
        id_mesada: id_mesada,
      },
      success: function (x) {
        if (x.trim() == "success") {
          swal("La devolución se guardó correctamente!", "", "success");
          busca_entradas("stock", null);
        } else {
          swal("Ocurrió un error al guardar la devolución", x, "error");
        }
      },
      error: function (jqXHR, estado, error) {},
    });
  }
}

function getMesadasDevolver(valor) {
  $.ajax({
    beforeSend: function () {
      $("#select_mesada2").html("Cargando mesadas...");
    },
    url: "data_ver_stock.php",
    type: "POST",
    data: { consulta: "cargar_disponibles", cantidad: valor },
    success: function (x) {
      $("#select_mesada2").selectpicker();
      $("#select_mesada2").val("default").selectpicker("refresh");
      $("#select_mesada2").html(x).selectpicker("refresh");
    },
    error: function (jqXHR, estado, error) {},
  });
}

function quitar_filtro() {
  const val = $("#select_tipo_filtro").find("option:selected").val();
  if (val && val.length) {
    $("#select_tipo_filtro").val("default").selectpicker("refresh");
    busca_entradas("stock", null);
  }
}

function abrirTab(evt, tabName) {
  $("body").removeAttr("x-pedido-edit");
  let i, tabcontent, tablinks;
  // Get all elements with class="tabcontent" and hide them
  tabcontent = document.getElementsByClassName("tabcontent");
  currentTab = tabName;
  for (i = 0; i < tabcontent.length; i++) {
    tabcontent[i].style.display = "none";
  }
  tablinks = document.getElementsByClassName("tablinks");
  for (i = 0; i < tablinks.length; i++) {
    tablinks[i].className = tablinks[i].className.replace(" active", "");
  }
  $(
    ".tab-stock,.tab-reservas,.tab-cuaderno,.tab-cuaderno-lista,.tab-entrega-rapida"
  ).addClass("d-none");
  $(".tab-" + tabName).removeClass("d-none");
  evt.currentTarget.className += " active";

  if (tabName == "entrega-rapida") {
  } else if (tabName == "cuaderno") {
    ClearPedido();
  } else if (tabName == "cuaderno-lista") {
    buscaListaCuaderno();
  } else {
    busca_entradas(tabName, null);
  }
}

function modalReservar() {
  if (!$(".selected2").length) {
    swal("Seleccioná los productos que vas a Reservar", "", "info");
    return;
  }

  $("#tabla_reservar > tbody").html("");
  $("#cantidad_reservar").val("");
  $("#select_cliente_reservar").val("default").selectpicker("refresh");

  $(".selected2").each(function () {
    const producto = $(this).attr("x-producto");
    const cant_band = $(this).attr("x-cant");
    const origen = $(this).attr("x-origen");
    const mesada = $(this).attr("x-mesada");
    const rowid = $(this).attr("x-id");

    $("#tabla_reservar > tbody").append(`
      <tr x-rowid='${rowid}' class='text-center'>
        <td>${producto}</td>
        <td>${cant_band}</td>
        <td>
          <input type="number" min="0" max="${cant_band}" step="1" placeholder="Bandejas" class="form-control text-center font-weight-bold input-reserva"></input>
        </td>
        <td>${origen}</td>
        <td>${mesada}</td>
      </tr>
    `);
  });

  $(".input-reserva").on("input", function () {
    let max = parseInt(this.max);

    if (parseInt(this.value) > max) {
      this.value = max;
    }
    this.value = this.value.replace(/\D/g, "");
  });

  $("#modal-reservar").modal("show");
}

function modalEntregar() {
  if (!$(".selected2").length) {
    swal("Seleccioná los productos que vas a Entregar", "", "info");
    return;
  }

  $("#tabla_entregar > tbody").html("");
  $("#cantidad_entregar").val("");
  $("#select_cliente_entregar").val("default").selectpicker("refresh");

  $(".selected2").each(function () {
    const producto = $(this).attr("x-producto");
    const cant_band = $(this).attr("x-cant");
    const origen = $(this).attr("x-origen");
    const mesada = $(this).attr("x-mesada");
    const rowid = $(this).attr("x-id");

    $("#tabla_entregar > tbody").append(`
      <tr x-rowid='${rowid}' class='text-center'>
        <td>${producto}</td>
        <td>${cant_band}</td>
        <td>
          <input type="number" min="0" max="${cant_band}" step="1" placeholder="Bandejas" class="form-control text-center font-weight-bold input-reserva"></input>
        </td>
        <td>
          <input style="max-width:150px;" type="search" onkeypress="return isNumberKey(event,this)" placeholder="Precio" class="form-control text-center font-weight-bold input-precio"></input>
        </td>
        <td>${origen}</td>
        <td>${mesada}</td>
      </tr>
    `);
  });

  $(".input-reserva").on("input", function () {
    let max = parseInt(this.max);

    if (parseInt(this.value) > max) {
      this.value = max;
    }
    this.value = this.value.replace(/\D/g, "");
  });

  $("#modal-entregar").modal("show");
}

function guardarReserva() {
  const id_cliente = $("#select_cliente_reservar")
    .find("option:selected")
    .val();
  if (!id_cliente || !id_cliente.length) {
    swal("Seleccioná un cliente!", "", "error");
    return;
  }
  let puede = true;
  $(".input-reserva").each(function () {
    const val = $(this).val().trim();
    if (!val.length || parseInt(val) < 1) {
      puede = false;
      return;
    }
  });

  if (!puede) {
    swal("Las cantidades a reservar deben ser mayores a CERO", "", "error");
    return;
  }

  $("#modal-reservar").modal("hide");

  let productos = [];

  $("#tabla_reservar > tbody")
    .find("tr")
    .each(function () {
      const rowid = $(this).attr("x-rowid"); //id_stock
      const cantidad = $(this).find(".input-reserva").val().trim();
      productos.push({
        rowid: rowid,
        cantidad: cantidad,
      });
    });

  $.ajax({
    url: "data_ver_stock.php",
    type: "POST",
    data: {
      consulta: "guarda_reserva",
      productos: JSON.stringify(productos),
      id_cliente: id_cliente,
    },
    success: function (x) {
      console.log(x);
      if (x.trim() == "success") {
        swal("Reservaste las bandejas correctamente", "", "success");
        busca_entradas("stock", null);
      } else {
        swal("Ocurrió un error al reservar las bandejas", "", "error");
      }
    },
    error: function (jqXHR, estado, error) {},
  });
}

function eliminar_reserva(id_reserva) {
  swal("Estás seguro/a de eliminar la reserva?", "", {
    icon: "warning",
    buttons: {
      cancel: "Cancelar",
      catch: {
        text: "Eliminar",
        value: "catch",
      },
    },
  }).then((value) => {
    switch (value) {
      case "catch":
        $.ajax({
          beforeSend: function () {},
          url: "data_ver_stock.php",
          type: "POST",
          data: {
            consulta: "eliminar_reserva",
            id_reserva: id_reserva,
          },
          success: function (x) {
            if (x.trim() == "success") {
              swal("Eliminaste la Reserva", "", "success");
              busca_entradas("reservas", null);
            } else {
              swal("Ocurrió un error", x, "error");
            }
            //console.log(x);
          },
          error: function (jqXHR, estado, error) {},
        });

        break;

      default:
        break;
    }
  });
}

function entregar_reserva(id_reserva) {
  $("#modal-entregar-reserva").modal("show");
  $("#modal-entregar-reserva").attr("x-id-reserva", id_reserva);
  $(".tabla-entrega > tbody").html("");
  $(".label-cliente-reserva").html("");
  $(".label-telefono-reserva").html("");
  $(".input-descuento,.input-subtotal").val("");
  $.ajax({
    beforeSend: function () {},
    url: "data_ver_stock.php",
    type: "POST",
    data: {
      consulta: "get_data_reserva",
      id_reserva: id_reserva,
    },
    success: function (x) {
      if (x.length) {
        try {
          const data = JSON.parse(x);
          if (data[0] && data[0].nombre_cliente) {
            $(".label-cliente-reserva").html(
              `Cliente: ${data[0].nombre_cliente} (${data[0].id_cliente})`
            );
          }
          if (data[0] && data[0].telefono && data[0].telefono.length) {
            $(".label-telefono-reserva").html(data[0].telefono);
          }
          if (data && data.length) {
            data.forEach((e, i) => {
              const { producto, cant_bandejas, origen, mesada } = e;
              $(".tabla-entrega > tbody").append(`
                <tr class="text-center" x-cant="${cant_bandejas}">
                  <td>${producto}</td>
                  <td>${cant_bandejas}</td>
                  <td>${mesada}</td>
                  <td>${origen}</td>
                  <td>
                    <input type='search' autocomplete='off' class='form-control font-weight-bold input-precio two-decimals text-center' onkeyup="calcularSubtotal(this)" placeholder='0.00' maxlength='8'></input>
                  </td>
                </tr>
              `);
            });

            $(".two-decimals").on("keypress", function (evt) {
              let $txtBox = $(this);
              let charCode = evt.which ? evt.which : evt.keyCode;
              if (
                charCode > 31 &&
                (charCode < 48 || charCode > 57) &&
                charCode != 46
              )
                return false;
              else {
                let len = $txtBox.val().length;
                let index = $txtBox.val().indexOf(".");
                if (index > 0 && charCode == 46) {
                  return false;
                }
                if (index > 0) {
                  let charAfterdot = len + 1 - index;
                  if (charAfterdot > 3) {
                    return false;
                  }
                }
              }
              return $txtBox; //for chaining
            });
          }
        } catch (error) {}
      } else {
        swal("Ocurrió un error", x, "error");
      }
      console.log(x);
    },
    error: function (jqXHR, estado, error) {},
  });
}

function guardarEntregaReserva() {
  if (!$(".tabla-entrega > tbody > tr").length) {
    swal("No hay productos para entregar", "", "error");
    return;
  }

  let faltaPrecio = false;
  let listaProductos = [];
  $(".input-precio").each(function (index, input) {
    if (!$(input).val().trim().length) {
      faltaPrecio = true;
    }
    listaProductos.push({});
  });

  if (faltaPrecio) {
    swal("Los precios de los productos no pueden quedar vacíos!", "", "error");
    return;
  }

  if (!$(".input-subtotal").val().trim().length) {
    swal("Falta asignar el Subtotal de la reserva", "", "error");
    return;
  }

  const id_reserva = $("#modal-entregar-reserva").attr("x-id-reserva");
  $("#modal-entregar-reserva").modal("hide");

  $.ajax({
    beforeSend: function () {},
    url: "data_ver_stock.php",
    type: "POST",
    data: {
      consulta: "get_id_remito",
    },
    success: function (x) {
      if (x.includes("id_remito")) {
        const id_remito = x.replace("id_remito:", "");
        const htmlremito = funcGenerarRemito(id_remito);
        $.ajax({
          beforeSend: function () {},
          url: "data_ver_stock.php",
          type: "POST",
          data: {
            consulta: "entregar_reserva",
            id_reserva: id_reserva,
            remito: htmlremito,
            id_remito: id_remito,
            subtotal: $(".input-subtotal").val().trim(),
          },
          success: function (x) {
            if (x.trim() == "success") {
              printRemito(1);
              busca_entradas("reservas", null);
            } else {
              swal("Ocurrió un error", x, "error");
              console.log(x);
            }
          },
          error: function (jqXHR, estado, error) {},
        });
      }
    },
  });
}

function funcGenerarRemito(id_remito) {
  $("#miVentana").html("");

  const date = new Date();
  const fecha = moment(date).format("DD/MM/YYYY");
  const hora = moment(date).format("HH:mm");
  const cliente = $(".label-cliente-reserva").text();

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

  const telefono = $(".label-telefono-reserva").text();
  if (telefono && telefono.length && telefono.trim().length > 0) {
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

  $(".tabla-entrega > tbody > tr").each(function (index, tr) {
    const producto = $(tr).find("td:first").text();
    const precio = $(tr).find(".input-precio").val().trim();
    const cantidad = $(tr).attr("x-cant");

    tr = `<tr>
                <td style='word-wrap:break-word;font-size:1.7em !important;'>${producto} (STOCK)</td>
                <td class='text-center' style="font-size: 1.7em !important;">${cantidad}</td>
                <td class='text-center' style="font-size: 1.7em !important;">$${precio}</td>
                <td class='text-center' style="font-size: 1.7em !important;">$${
                  parseFloat(precio) * parseInt(cantidad)
                }</td>
              </tr>
              `;

    $("#tabla_producto tbody").append(tr);
  });
  const descuento = $(".input-descuento").val().trim();
  const tipodescuento = $(".select-descuento")
    .first()
    .find("option:selected")
    .val();

  if (descuento && descuento.length) {
    if (tipodescuento == "porcentual") {
      $("#tabla_producto tbody").append(`
                <tr>
                  <td style="font-size:1.7em;">DESCUENTO</td>
                  <td></td>
                  <td></td>
                  <td class='text-center' style="font-size:1.7em;">-${descuento}%</td>
                </tr>`);
    } else {
      $("#tabla_producto tbody").append(`
                <tr>
                  <td style="font-size:1.7em;">DESCUENTO</td>
                  <td></td>
                  <td></td>
                  <td class='text-center' style="font-size:1.7em;">-$${descuento}</td>
                </tr>`);
    }
  }
  const total = $("#modal-entregar-reserva .input-subtotal").val().trim();
  $("#tabla_producto tbody").append(`
          <tr style='font-size:1.7em;text-align:center;'>
            <td colspan='2'></td>
            <td>TOTAL</td>
            <td>$${Number(total).toFixed(2)}</td>
          </tr>`);

  $("#miVentana").append(
    "<div style='display: block; page-break-before: always;'>"
  );

  $("#miVentana").append($("#miVentana").html().replace("ORIGINAL", "COPIA"));

  //printRemito(1);
  return $("#miVentana").html();
}

function printRemito(tipo) {
  if (tipo == 1) {
    document.getElementById("ocultar").style.display = "none";
    document.getElementById("miVentana").style.display = "block";
    setTimeout("window.print();printRemito(2);", 500);
  } else {
    document.getElementById("ocultar").style.display = "block";
    document.getElementById("miVentana").style.display = "none";

    document.title = "Bandejas en Stock";
  }
}

function calcularSubtotal(objeto) {
  let monto = 0;
  $(".tabla-entrega > tbody > tr").each(function (index, tr) {
    const precio = $(tr).find(".input-precio").val().trim();
    const cantidad = $(tr).attr("x-cant");
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

function modalIngresoManual() {
  pone_tipos("#select-tipo");
  $("#modal-ingreso-manual").modal("show");
}

function pone_subtipos(id_tipo) {
  $.ajax({
    beforeSend: function () {
      $("#select-subtipo").html("Cargando subtipos...");
      $("#select-variedad").val("default").selectpicker("refresh");
    },
    url: "pone_tiposdeproducto.php",
    type: "POST",
    data: { tipo: "carga_subtipos", id_tipo: id_tipo },
    success: function (x) {
      $("#select-subtipo").val("default").selectpicker("refresh");
      $("#select-subtipo").html(x).selectpicker("refresh");
      $("#select-subtipo").on(
        "changed.bs.select",
        function (e, clickedIndex, newValue, oldValue) {
          carga_variedades(this.value);
        }
      );
    },
    error: function (jqXHR, estado, error) {},
  });
}

function carga_variedades(id_subtipo) {
  $.ajax({
    beforeSend: function () {
      $("#select-variedad").html("Cargando variedades...");
    },
    url: "pone_tiposdeproducto.php",
    type: "POST",
    data: { tipo: "carga_variedades", id_subtipo: id_subtipo },
    success: function (x) {
      $("#select-variedad").val("default").selectpicker("refresh");
      $("#select-variedad").html(x).selectpicker("refresh");
    },
    error: function (jqXHR, estado, error) {},
  });
}

function pone_bandejas(id_tipo) {
  $.ajax({
    beforeSend: function () {
      $("#select-bandeja").html("Cargando bandejas...");
    },
    url: "pone_tiposdeproducto.php",
    type: "POST",
    data: { tipo: "carga_bandejas", id_tipo: id_tipo },
    success: function (x) {
      $(".selectpicker").selectpicker();
      $("#select-bandeja").val("default").selectpicker("refresh");
      $("#select-bandeja").html(x).selectpicker("refresh");
      $("#select-bandeja").on(
        "changed.bs.select",
        function (e, clickedIndex, newValue, oldValue) {
          $("#myModal")
            .find("#cantidad_plantas,#cantidad_band,#cantidad_semillas")
            .val("");
          let canti = document.getElementById("cantidad_plantas");
          canti.focus();
        }
      );
    },
    error: function (jqXHR, estado, error) {},
  });
}

function guardarIngresoManual() {
  const id_tipo = $("#select-tipo").find("option:selected").val();
  const id_subtipo = $("#select-subtipo").find("option:selected").val();
  const id_variedad = $("#select-variedad").find("option:selected").val();
  const tipo_bandeja = $("#select-bandeja").find("option:selected").val();
  const cant_bandejas = $("#input-cant-bandejas").val().trim();
  const mesada = $("#select_mesada").find("option:selected").val();

  if (!id_tipo || !id_tipo.length) {
    swal("Seleccioná un Tipo de Producto", "", "error");
    return;
  } else if (!id_subtipo || !id_subtipo.length) {
    swal("Seleccioná un Subtipo de Producto", "", "error");
    return;
  } else if (!id_variedad || !id_variedad.length) {
    swal("Seleccioná un Variedad de Producto", "", "error");
    return;
  } else if (!tipo_bandeja || !tipo_bandeja.length) {
    swal("Seleccioná el Tipo de Bandeja de Producto", "", "error");
    return;
  } else if (
    !cant_bandejas ||
    !cant_bandejas.length ||
    parseInt(cant_bandejas) < 1
  ) {
    swal("Ingresá la Cantidad de Bandejas", "", "error");
    return;
  } else if (!mesada || !mesada.length) {
    swal("Seleccioná la Mesada", "", "error");
    return;
  }

  $("#modal-ingreso-manual").modal("hide");

  $.ajax({
    url: "data_ver_stock.php",
    type: "POST",
    data: {
      consulta: "guardar_ingreso_manual",
      id_variedad: id_variedad,
      cant_bandejas: cant_bandejas,
      tipo_bandeja: tipo_bandeja,
      mesada: mesada,
    },
    success: function (x) {
      if (x.trim() == "success") {
        swal("Ingresaste las Bandejas a Stock", "", "success");
        busca_entradas("stock", null);
      } else {
        console.log(x);
        swal("Ocurrió un error al guardar las Bandejas", "", "error");
      }
    },
    error: function (jqXHR, estado, error) {},
  });
}

function cambiarMesada(id_stock, cantidad) {
  $("#select_mesada_cambiar").val("default").selectpicker("refresh");
  getMesadasCambiar(cantidad);
  $("#modal-cambiar-mesada").attr("x-id-stock", id_stock);
  $("#modal-cambiar-mesada").modal("show");
}

function getMesadasCambiar(valor) {
  $.ajax({
    beforeSend: function () {
      $("#select_mesada_cambiar").html("Cargando mesadas...");
    },
    url: "data_ver_stock.php",
    type: "POST",
    data: { consulta: "cargar_disponibles", cantidad: valor },
    success: function (x) {
      $("#select_mesada_cambiar").html(x).selectpicker("refresh");
    },
    error: function (jqXHR, estado, error) {},
  });
}

function guardarCambioMesada() {
  const id_mesada = $("#select_mesada_cambiar option:selected").val();
  if (!id_mesada || !id_mesada.length) {
    swal("Seleccioná una Mesada", "", "error");
    return;
  }

  const id_stock = $("#modal-cambiar-mesada").attr("x-id-stock");
  if (!id_stock || !id_stock.length) {
    return;
  }

  $("#modal-cambiar-mesada").modal("hide");
  $.ajax({
    url: "data_ver_stock.php",
    type: "POST",
    data: {
      consulta: "cambiar_mesada",
      id_mesada: id_mesada,
      id_stock: id_stock,
    },
    success: function (x) {
      if (x.includes("success")) {
        swal("Cambiaste la Mesada correctamente!", "", "success");
        busca_entradas("stock", null);
      } else {
        swal("Ocurrió un error al cambiar la mesada", x, "error");
        $("#modal-cambiar-mesada").modal("show");
      }
    },
    error: function (jqXHR, estado, error) {},
  });
}

function buscaListaCuaderno() {
  $.ajax({
    beforeSend: function () {
      $("#tabla-cuaderno-lista").html(
        "<h4 class='ml-1'>Buscando pedidos, espere...</h4>"
      );
    },
    url: "data_ver_stock.php",
    type: "POST",
    data: {
      consulta: "get_cuaderno_lista",
    },
    success: function (x) {
      $("#tabla-cuaderno-lista").html(x);
      $("#tabla-cuaderno").DataTable({
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

function eliminarPedidoCuaderno(id) {
  swal("Eliminar el Pedido del Cuaderno?", "", {
    icon: "warning",
    buttons: {
      cancel: "Cancelar",
      catch: {
        text: "Eliminar",
        value: "catch",
      },
    },
  }).then((value) => {
    switch (value) {
      case "catch":
        $.ajax({
          beforeSend: function () {},
          url: "data_ver_stock.php",
          type: "POST",
          data: {
            consulta: "eliminar_pedido_cuaderno",
            id: id,
          },
          success: function (x) {
            if (x.trim() == "success") {
              swal(
                "Éxito!",
                "Eliminaste el Pedido del Cuaderno correctamente!",
                "success"
              );
              buscaListaCuaderno();
            } else {
              swal("Ocurrió un error", x, "error");
            }
            //console.log(x);
          },
          error: function (jqXHR, estado, error) {},
        });

        break;

      default:
        break;
    }
  });
}

function confirmarPedidoCuaderno(id) {
  swal("Confirmar el Pedido?", "", {
    icon: "info",
    buttons: {
      cancel: "Cancelar",
      catch: {
        text: "CONFIRMAR",
        value: "catch",
      },
    },
  }).then((value) => {
    switch (value) {
      case "catch":
        $.ajax({
          beforeSend: function () {},
          url: "data_ver_stock.php",
          type: "POST",
          data: {
            consulta: "confirmar_pedido_cuaderno",
            id: id,
          },
          success: function (x) {
            if (x.trim().includes("pedidonum")) {
              swal("Éxito!", "Confirmaste el Pedido correctamente!", "success");
              buscaListaCuaderno();
            } else {
              swal("Ocurrió un error", x, "error");
            }
            //console.log(x);
          },
          error: function (jqXHR, estado, error) {},
        });

        break;

      default:
        break;
    }
  });
}

function guardarEntregaInmediata() {
  const id_cliente = $("#select_cliente_entregar")
    .find("option:selected")
    .val();
  if (!id_cliente || !id_cliente.length) {
    swal("Seleccioná un cliente!", "", "error");
    return;
  }
  let puede = true;
  $(".input-reserva").each(function () {
    const val = $(this).val().trim();
    if (!val.length || parseInt(val) < 1) {
      puede = false;
      return;
    }
  });

  if (!puede) {
    swal("Las cantidades a entregar deben ser mayores a CERO", "", "error");
    return;
  }

  $("#modal-entregar").modal("hide");

  let productos = [];
  let subtotal = 0.0;
  $("#tabla_entregar > tbody")
    .find("tr")
    .each(function () {
      const rowid = $(this).attr("x-rowid"); //id_stock
      const cantidad = $(this).find(".input-reserva").val().trim();
      let precio = $(this).find(".input-precio").val().trim();
      precio = precio && precio.length ? parseFloat(precio) : 0;
      productos.push({
        rowid: rowid,
        cantidad: cantidad,
        precio: precio
      });

      subtotal+=(cantidad*precio)
    });

  $.ajax({
    beforeSend: function () {},
    url: "data_ver_stock.php",
    type: "POST",
    data: {
      consulta: "get_id_remito",
    },
    success: function (x) {
      if (x.includes("id_remito")) {
        const id_remito = x.replace("id_remito:", "");
        const htmlremito = funcGenerarRemitoInmediato(id_remito);
        $.ajax({
          beforeSend: function () {},
          url: "data_ver_stock.php",
          type: "POST",
          data: {
            consulta: "guardar_entrega_inmediata",
            remito: htmlremito,
            id_remito: id_remito,
            productos: JSON.stringify(productos),
            id_cliente: id_cliente,
            subtotal: subtotal
          },
          success: function (x) {
            console.log(x)
            if (x.trim() == "success") {
              printRemito(1);
              busca_entradas("stock", null);
            } else {
              swal("Ocurrió un error", x, "error");
              console.log(x);
            }
          },
          error: function (jqXHR, estado, error) {},
        });
      }
    },
  });
}

function funcGenerarRemitoInmediato(id_remito) {
  $("#miVentana").html("");
  const date = new Date();
  const fecha = moment(date).format("DD/MM/YYYY");
  const hora = moment(date).format("HH:mm");
  const cliente = $("#select_cliente_entregar option:selected").text();

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

  const telefono = $("#select_cliente_entregar option:selected").attr("x-telefono");
  if (telefono && telefono.length && telefono.trim().length > 0) {
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

  let subtotal = 0.0;
  $("#tabla_entregar > tbody > tr").each(function (index, tr) {
    const producto = $(tr).find("td:first").text();
    const cantidad = $(tr).find(".input-reserva").val();
    let precio = $(tr).find(".input-precio").val();
    precio = precio && precio.length ? parseInt(precio) : 0
    tr = `<tr>
                <td style='word-wrap:break-word;font-size:1.7em !important;'>${producto} (STOCK)</td>
                <td class='text-center' style="font-size: 1.7em !important;">${cantidad}</td>
                <td class='text-center' style="font-size: 1.7em !important;">$${Number(precio).toFixed(2)}</td>
                <td class='text-center' style="font-size: 1.7em !important;">$${Number(precio*cantidad).toFixed(2)}</td>
              </tr>
              `;
    subtotal += (cantidad*precio)
    $("#tabla_producto tbody").append(tr);
  });

  $("#tabla_producto tbody").append(`
          <tr style='font-size:1.7em;text-align:center;'>
            <td colspan='2'></td>
            <td>TOTAL</td>
            <td>$${Number(subtotal).toFixed(2)}</td>
          </tr>`);

  const tmpcode = $("#miVentana").html().replace("ORIGINAL", "COPIA")
  $("#miVentana").append(
    "<div style='display: block; page-break-before: always;'>"
  );

  $("#miVentana").append(tmpcode);

  //printRemito(1);
  return $("#miVentana").html();
}