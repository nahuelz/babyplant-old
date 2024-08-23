$(function () {
  $("#daterange-btn").daterangepicker(
    {
      ranges: {
        Hoy: [moment(), moment()],
        Ayer: [moment().subtract(1, "days"), moment().subtract(1, "days")],
        "Semana Pasada": [
          moment().startOf("isoWeek").subtract(7, "days"),
          moment().startOf("isoWeek").subtract(1, "days"),
        ],
        "Los ultimos 7 dias": [moment().subtract(6, "days"), moment()],
        "Los ultimos 30 dias": [moment().subtract(29, "days"), moment()],
        "Los ultimos 6 meses": [moment().subtract(180, "days"), moment()],
        "Todo el año": [moment().startOf("year"), moment()],
      },
      startDate: moment().subtract(180, "days"),
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

$(document).ready(function () {
  $(".numeric-only").keypress(function (e) {
    if (String.fromCharCode(e.keyCode).match(/[^0-9]/g)) return false;
  });
});

function printPago(tipo, id_pago, objeto) {
  if (tipo == 1) {
    func_printPago(id_pago, objeto);

    document.getElementById("ocultar").style.display = "none";
    $("#ModalVerPagos").modal("hide")

    document.getElementById("miVentana").style.display = "block";
  } else {
    document.getElementById("ocultar").style.display = "block";
    document.getElementById("miVentana").style.display = "none";
    $("#ModalVerPagos").modal("show")
    $("#miVentana").html("");
  }
}

function func_printPago(id_pago, objeto) {
  let direccion = `
                        <div class="row">
                        <div class="col text-center">
                          <img src='dist/img/babyplant.png' style="max-width: 300px !important;"></img>
                          <address style='font-size:16px !important;padding-top:3px;padding-bottom:30px;'>
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

  let fecha = $(objeto).closest("tr").find("td:eq(0)").attr("x-fecha");
  let concepto = $(objeto).closest("tr").find("td:eq(1)").text();
  let monto = $(objeto).closest("tr").find("td:eq(2)").text();
  let cliente = $("#select_cliente option:selected").text();

  $("#miVentana").append(
    "<h3 style='padding-left: 10px;'>Fecha: " + fecha + "</h3>"
  );

  $("#miVentana").append(
    "<h3 style='padding-left: 10px;'>Recibimos la suma de: " + monto + "</h3>"
  );

  $("#miVentana").append(
    "<h3 style='padding-left: 10px;'>Por parte del Productor: " +
      cliente +
      "</h3>"
  );

  $("#miVentana").append(
    "<h3 style='padding-left: 10px;'>En concepto de: " +
      concepto +
      "</h3><br><br><br><br><br><br><br><br>"
  );

  $("#miVentana").append(
    "<div align='center'><hr style='width:180px; border: 0.5px solid black;'></hr></div>"
  );

  $("#miVentana").append(
    "<div align='center'><span style='font-size:18px;font-weight:bold;'>FIRMA</span></div>"
  );

  $("#miVentana").append(
    `<div style='display: block; page-break-before: always;'>`
  );

  $("#miVentana").append(direccion);

  $("#miVentana").append(
    "<h3 style='padding-left: 10px;'>Nº de Pago: " + id_pago + "</h3>"
  );

  $("#miVentana").append(
    "<h3 style='padding-left: 10px;'>Fecha: " + fecha + "</h3>"
  );

  $("#miVentana").append(
    "<h3 style='padding-left: 10px;'>Recibimos la suma de: $" + monto + "</h3>"
  );

  $("#miVentana").append(
    "<h3 style='padding-left: 10px;'>Por parte del Productor: " +
      cliente +
      "</h3>"
  );

  $("#miVentana").append(
    "<h3 style='padding-left: 10px;'>En concepto de: " +
      concepto +
      "</h3><br><br><br><br><br><br><br><br>"
  );

  $("#miVentana").append(
    "<div align='center'><hr style='width:150px; border: 0.5px solid black;'></hr></div>"
  );

  $("#miVentana").append(
    "<div align='center'><span style='font-size:15px;font-weight:bold;'>Firma</span></div></div>"
  );

  setTimeout("window.print();printPago(2, null);", 500);
}

function clearSearch(){
  $("#select_cliente").val("default").selectpicker("refresh");
  busca_entradas()
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
          busca_entradas(id_cliente);
        }
      );
    },
    error: function (jqXHR, estado, error) {},
  });
}

function busca_entradas(id_cliente) {
  let fecha = $("#fi").val();
  let fechaf = $("#ff").val();
  $.ajax({
    beforeSend: function () {
      $("#tabla_entradas").html(
        "<h4 style='margin-left: 20px'>Buscando remitos, espere...</h4>"
      );
    },
    url: "data_ver_situacion.php",
    type: "POST",
    data: {
      fechai: fecha,
      fechaf: fechaf,
      id_cliente: id_cliente &&id_cliente.length ? id_cliente : null,
      consulta: "busca_situacion",
    },
    success: function (x) {
      $("#tabla_entradas").html(x);
      busca_balance(id_cliente);

      $("#tabla").DataTable({
        order: [[3, "desc"]],
        pageLength: 100,
        language: {
          lengthMenu: "Mostrando _MENU_ remitos por página",
          zeroRecords: "No hay remitos",
          info: "Página _PAGE_ de _PAGES_",
          infoEmpty: "No hay remitos",
          infoFiltered: "(filtrado de _MAX_ remitos en total)",
          lengthMenu: "Mostrar _MENU_ remitos",
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
        "Ocurrió un error: contactá al desarrollador" +
          "     " +
          estado +
          " " +
          error
      );
    },
  });
}

function modalPagos(id_cliente) {
  $("#ModalPagos").attr("x-id-cliente", id_cliente);
  $("#ModalPagos").modal("show");
  $("#input_pago,#input_concepto").val("");
  $("#input_pago").focus();
}

function cerrarModalPagos() {
  $("#ModalPagos").modal("hide");
}

function agregarPago() {
  const monto = $("#input_pago").val().trim();
  const concepto = $("#input_concepto").val().trim();
  const id_cliente = $("#ModalPagos").attr("x-id-cliente");
  if (isNaN(monto) || parseFloat(monto) == 0.0) {
    swal("El pago debe ser mayor a cero!", "", "error");
  } else {
    $.ajax({
      beforeSend: function () {
        cerrarModalPagos();
      },
      url: "data_ver_situacion.php",
      type: "POST",
      data: {
        consulta: "agregar_pago",
        id_cliente: id_cliente,
        monto: monto,
        concepto: concepto,
      },
      success: function (x) {
        if (x.trim() == "success"){
          swal("El pago se agregó correctamente!", "", "success");
          busca_entradas(id_cliente);
        }
        else{
          swal("Ocurrió un error al agregar el Pago", x, "error")
        }
      },
      error: function (jqXHR, estado, error) {
        swal("Error al agregar el pago", error, "error");
        $("#ModalPagos").modal("show");
      },
    });
  }
}

function modalDeudas(id_cliente) {
  $("#ModalDeudas").attr("x-id-cliente", id_cliente);
  $("#ModalDeudas").modal("show");
  $("#input-monto-deuda,#input-obs-deuda").val("");
  $("#input-monto-deuda").focus();
}

function cerrarModalDeudas() {
  $("#ModalDeudas").modal("hide");
}

function agregarDeuda() {
  const monto = $("#input-monto-deuda").val().trim();
  const concepto = $("#input-obs-deuda").val().trim();
  const id_cliente = $("#ModalDeudas").attr("x-id-cliente");
  if (isNaN(monto) || parseFloat(monto) == 0.0) {
    swal("El pago debe ser mayor a cero!", "", "error");
  } else {
    $.ajax({
      beforeSend: function () {
        cerrarModalDeudas();
      },
      url: "data_ver_situacion.php",
      type: "POST",
      data: {
        consulta: "agregar_deuda",
        id_cliente: id_cliente,
        monto: monto,
        concepto: concepto,
      },
      success: function (x) {
        if (x.trim() == "success"){
          swal("La DEUDA se agregó correctamente!", "", "success");
          busca_entradas(id_cliente);
        }
        else{
          swal("Ocurrió un error al agregar la Deuda", x, "error")
        }
      },
      error: function (jqXHR, estado, error) {
        swal("Error al agregar la deuda", error, "error");
        $("#ModalDeudas").modal("show");
      },
    });
  }
}

function modalVerPagos(id_cliente) {
  $("#ModalVerPagos").attr("x-id-cliente", id_cliente);
  $("#ModalVerPagos").modal("show");
  loadPagos(id_cliente);
}

function cerrarModalVerPagos() {
  $("#ModalVerPagos").modal("hide");
}

function loadPagos(id_cliente) {
  $.ajax({
    beforeSend: function () {
      $(".body-pagos").html("Cargando...");
    },
    url: "data_ver_situacion.php",
    type: "POST",
    data: { consulta: "cargar_pagos", id_cliente: id_cliente },
    success: function (x) {
      $(".body-pagos").html(x);
      $("#tabla-pagos").DataTable({
        order: [[0, "desc"]],
        pageLength: 25,
        language: {
          lengthMenu: "Mostrando _MENU_ pagos por página",
          zeroRecords: "No hay pagos",
          info: "Página _PAGE_ de _PAGES_",
          infoEmpty: "No hay pagos",
          infoFiltered: "(filtrado de _MAX_ pagos en total)",
          lengthMenu: "Mostrar _MENU_ pagos",
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
      $(".body-pagos").html(
        "<h4 class='text-danger p-3'>Error al cargar los pagos</h4>"
      );
    },
  });
}

function eliminarPago(id_pago) {
  swal("Estás seguro/a de eliminar el Pago seleccionado?", "", {
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
        $.ajax({
          beforeSend: function () {},
          url: "data_ver_situacion.php",
          type: "POST",
          data: { consulta: "eliminar_pago", id_pago: id_pago },
          success: function (x) {
            if (x.trim() == "success"){
              swal("Eliminaste el pago correctamente!", "", "success");
              const id_cliente = $("#ModalVerPagos").attr("x-id-cliente");
              loadPagos(id_cliente);
              busca_entradas(id_cliente);
              busca_balance(id_cliente);
            }
            else{
              swal("Ocurrió un error al eliminar el Pago", x, "error")
            }
          },
          error: function (jqXHR, estado, error) {},
        });

        break;

      default:
        break;
    }
  });
}

function busca_balance(id_cliente) {
  $(".col-balance").html("");

  if (!id_cliente) return;
  
  $.ajax({
    beforeSend: function () {},
    url: "data_ver_situacion.php",
    type: "POST",
    data: { consulta: "busca_balance", id_cliente: id_cliente },
    success: function (x) {
      $(".col-balance").html(x);
    },
    error: function (jqXHR, estado, error) {},
  });
}

function marcarPagado(actual, id_remito) {
  let id_cliente = $("#select_cliente").find("option:selected").val();
  swal(actual == 0 ? "Marcar como Pagado?" : "Marcar como Impago?", "", {
    icon: "warning",
    buttons: {
      cancel: "Cancelar",
      catch: {
        text: "ACEPTAR",
        value: "catch",
      },
    },
  }).then((value) => {
    switch (value) {
      case "catch":
        $.ajax({
          beforeSend: function () {},
          url: "data_ver_situacion.php",
          type: "POST",
          data: {
            consulta: "marcar_pago",
            valor: actual == 1 ? 0 : 1,
            id_remito: id_remito,
          },
          success: function (x) {
            if (x.trim() == "success"){
              busca_entradas(id_cliente);
              busca_balance(id_cliente);
            }
            else{
              swal("Ocurrió un error", x, "error")
            }
          },
          error: function (jqXHR, estado, error) {
            swal("Ocurrió un error", error, "error")
          },
        });

        break;

      default:
        break;
    }
  });
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

