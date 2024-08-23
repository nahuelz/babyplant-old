let edit_mode = false;
let global_id_variedad = "";

function busca_productos(filtro) {
  $.ajax({
    beforeSend: function () {
      $("#tabla_entradas").html("Cargando tipos, espere...");
    },
    url: "data_ver_subtipos.php",
    type: "POST",
    data: {
      consulta: "busca_subtipos"
    },
    success: function (x) {
      $("#tabla_entradas").html(x);
      let table = $("#tabla").DataTable({
        pageLength: 20,
        language: {
          lengthMenu: "Mostrando _MENU_ subtipos por página",
          zeroRecords: "No hay subtipos",
          info: "Página _PAGE_ de _PAGES_",
          infoEmpty: "No hay subtipos",
          infoFiltered: "(filtrado de _MAX_ subtipos en total)",
          lengthMenu: "Mostrar _MENU_ subtipos",
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
  /*}else{
          alert("Proporcione un rango de fechas valido, para realizar la busqueda...");
        }*/
}


function pone_tipos() {
  $.ajax({
    beforeSend: function () {
      $("#select_tipo").html("Cargando tipos...");
    },
    url: "pone_articulos.php",
    type: "POST",
    data: null,
    success: function (x) {
      $(".selectpicker").selectpicker();
      $("#select_tipo2").html(x).selectpicker("refresh");
    },
    error: function (jqXHR, estado, error) {},
  });
}

function MostrarModalAgregarProducto(id_producto, subtipo) {
  if (id_producto != null) {
    $("#ModalAgregarProducto").find("#titulo").html("Modificar Subtipo");

    $("#select_tipo2").val("default");
    $("#select_tipo2").attr("disabled", "disabled");

    $("#select_tipo2").selectpicker("refresh");
    $("#nombrenewtipo_txt").val(subtipo);
    document.getElementById("nombrenewtipo_txt").focus();
    global_id_variedad = id_producto;
    edit_mode = true;
  } else {
    $("#select_tipo2").prop("disabled", false);
    $("#select_tipo2").val("default");
    $("#select_tipo2").selectpicker("refresh");
    pone_tipos();
    document.getElementById("nombrenewtipo_txt").focus();
    $("#nombrenewtipo_txt").val("");
    $("#ModalAgregarProducto").find("#titulo").html("Agregar Subtipo");

    edit_mode = false;
    global_id_variedad = "";
  }

  $("#ModalAgregarProducto").modal("show");
}

function CerrarModalProducto() {
  $("#ModalAgregarProducto").modal("hide");
}

function GuardarSubtipo() {
  let nombre = $("#nombrenewtipo_txt").val().trim();
  let tipoart = $("#select_tipo2 :selected").text();

  let puede = true;
  if (tipoart.length < 1) {
    if (edit_mode == false) {
      swal("ERROR", "Debes elegir un tipo de producto", "error");
      puede = false;
    }
  } else if (nombre.length < 3) {
    swal("ERROR", "Debes ingresar un nombre de al menos 3 letras", "error");
    puede = false;
  }

  if (puede == true) {
    let tipo = "";
    if (!edit_mode) {
      let id_tipo = $("#select_tipo2 :selected").val();
      $.ajax({
        url: "guarda_producto.php",
        type: "POST",
        data: { tipo: "agregar_subtipo", id_tipo: id_tipo, nombre: nombre },
        success: function (x) {
          if (x.includes("success")){
            busca_productos(null);
            $("#nombrenewtipo_txt").val("");
            $("#nombrenewtipo_txt").focus();
            swal(
              "El Subtipo de producto se agregó correctamente!",
              "",
              "success"
            );
            $("#ModalAgregarProducto").modal("hide")
          }
          else{
            swal("ERROR", x, "error");
          }
        },
        error: function (jqXHR, estado, error) {
          alert("Hubo un error al agregar el producto " + error);
        },
      });
    } else {
      $.ajax({
        url: "guarda_producto.php",
        type: "POST",
        data: {
          tipo: "editar_subtipo",
          global_id_variedad: global_id_variedad,
          nombre: nombre,
        },
        success: function (x) {
          $("#ModalAgregarProducto").modal("hide")
          swal(
            "El Subtipo de producto fue modificado correctamente!",
            "",
            "success"
          );
          busca_productos(null);
        },
        error: function (jqXHR, estado, error) {
          alert("Hubo un error al modificar el producto " + error);
        },
      });
    }
  }
}

function eliminarSubtipo(id){
  swal("Estás seguro de Eliminar este Subtipo?", "", {
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
        $.ajax({
          beforeSend: function () {},
          url: "data_ver_subtipos.php",
          type: "POST",
          data: { consulta: "eliminar", id: id },
          success: function (x) {
            if (x.trim() == "success") {
              swal("Eliminaste el Subtipo correctamente!", "", "success")
              busca_productos(null)
            } else {
              swal("Ocurrió un error al eliminar el Subtipo", x, "error");
            }
          },
          error: function (jqXHR, estado, error) {
            swal("Ocurrió un error al eliminar el Subtipo", error, "error");
          },
        });
        break;
    }
  });
}