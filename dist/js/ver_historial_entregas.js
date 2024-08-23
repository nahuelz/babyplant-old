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

function busca_entradas() {
  let fecha = $("#fi").val();
  let fechaf = $("#ff").val();

  //if(fecha==""||fechaf!=""){
  $.ajax({
    beforeSend: function () {
      $("#tabla_entradas").html(
        "<h3 style='margin-left:10px'>Buscando pedidos, espere...</h3>"
      );
    },
    url: "data_ver_historial.php",
    type: "POST",
    data: { consulta: "busca_entregas", fechai: fecha, fechaf: fechaf },
    success: function (x) {
      $("#tabla_entradas").html(x);
      $("#tabla").DataTable({
        order: [[5, "desc"]],
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

function printOrdenEntrega(tipo) {
  if (tipo == 1) {
    func_printOrdenEntrega();
    document.getElementById("ocultar").style.display = "none";
    document.getElementById("miVentana").style.display = "block";
  } else {
    document.getElementById("ocultar").style.display = "block";
    document.getElementById("miVentana").style.display = "none";
    $("#miVentana").html("");
    //setTimeout("confirmaEstado();", 500);
  }
}

function func_printOrdenEntrega() {
  let direccion =
    "<img src='dist/img/babyplant.png' width='160' height='70'></img>";
  $("#miVentana").html(document.getElementById("contenedor_entrega").outerHTML);
  setTimeout("window.print();printOrdenEntrega(2, null);", 500);
  //setTimeout(function () { printtag(2); }, 100);
}

function mostrarInfoEntregas(id, tipo) {
  $("#ModalInfoEntregas").modal("show")
  $.ajax({
    beforeSend: function () {},
    url: "data_ver_historial.php",
    type: "POST",
    data: { consulta: "cargar_entregas", id: id, tipo },
    success: function (x) {
      $("#contenedor_entrega").html(x);
      
    },
    error: function (jqXHR, estado, error) {
      $("#contenedor_entrega").html(
        "Hubo un error al cargar la información del pedido"
      );
    },
  });
}

function cancelarEntrega(id_entrega, cantidad, tipo) {
  $("#ModalEnviaraMesadas,#ModalInfoEntregas").attr("x-cantidad", cantidad);
  $("#ModalEnviaraMesadas,#ModalInfoEntregas").attr("x-tipo", tipo);
  $("#ModalEnviaraMesadas,#ModalInfoEntregas").attr("x-id-entrega", id_entrega);
  $(".row-contenedor").html("");
  loadMesadasModal();
  $("#ModalEnviaraMesadas").modal("show");
}

function GuardarMesadas() {
  if ($(".mesada-clicked").length <= 0) {
    swal("Elegí una Mesada para devolver las bandejas!", "", "error");
  } else {
    const id = $(".mesada-clicked").attr("id").replace("mesada_", "");
    const libres = $("#libres_" + id).text();
    const cantidad = $("#ModalEnviaraMesadas").attr("x-cantidad");
    const tipo = $("#ModalEnviaraMesadas").attr("x-tipo");
    const id_entrega = $("#ModalEnviaraMesadas").attr("x-id-entrega");

    if (cantidad && !isNaN(cantidad) && parseInt(cantidad) > 0) {
      if (parseInt(cantidad) <= parseInt(libres)) {
        CerrarModalInfoEntregas();
        CerrarModalMesadas();
        $.ajax({
          beforeSend: function () {},
          url: "data_ver_historial.php",
          type: "POST",
          data: {
            consulta: "eliminar_entrega",
            id_entrega: id_entrega,
            tipo: tipo,
            cantidad: cantidad,
            mesada: id,
          },
          success: function (x) {
            if (x.trim() == "success") {
              swal("Cancelaste la entrega correctamente!", "", "success");
              setTimeout("busca_entradas();", 1000);
            } else {
              swal("Ocurrió un error al cancelar la entrega", x, "error");
              console.log(x);
            }
          },
          error: function (jqXHR, estado, error) {
            swal("Error al cancelar la entrega", error, "error");
          },
        });
      } else {
        swal(
          "No hay suficiente lugar libre en la Mesada que elegiste!",
          "",
          "error"
        );
      }
    }
  }
}

function click_mesada(id) {
  $(".mesada-clicked").removeClass("mesada-clicked");
  $(id).addClass("mesada-clicked");
}

function CerrarModalMesadas() {
  $("#ModalEnviaraMesadas").modal("hide");
}

function CheckCantidad(val) {
  let cajitas = document.getElementsByClassName("cantibox");
  let valor = 0;
  if (cajitas != undefined && cajitas != null && cajitas.length > 0) {
    for (let i = 0; i < cajitas.length; i++) {
      let valactual = parseInt($(cajitas[i]).val());
      if (valactual < 0) valactual = valactual * -1;
      if (isNaN(valactual)) {
        valor += 0;
      } else {
        valor += valactual;
      }
    }
  }
  $("#cantidad_a_entregar").html(valor.toString());
}

function CerrarModalInfoEntregas() {
  $("#ModalInfoEntregas").modal("hide");
}