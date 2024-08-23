let edit_mode = false;
let global_id_usuario = "";

function busca_usuarios() {
  if (/Android|webOS|iPhone|iPad|iPod|BlackBerry/i.test(navigator.userAgent)) {
    $(".selectpicker").selectpicker("mobile");
  } else {
    let elements = document.querySelectorAll(".mobile-device");
    for (let i = 0; i < elements.length; i++) {
      elements[i].classList.remove("mobile-device");
    }
    $(".selectpicker").selectpicker({});
  }

  $.ajax({
    beforeSend: function () {
      $("#tabla_entradas").html("Cargando usuarios, espere...");
    },
    url: "data_ver_usuarios.php",
    type: "POST",
    data: { consulta: "busca_usuarios" },
    success: function (x) {
      $("#tabla_entradas").html(x);

      $("#tabla").DataTable({
        order: [[1, "asc"]],
        language: {
          lengthMenu: "Mostrando _MENU_ usuarios por página",
          zeroRecords: "No hay usuarios",
          info: "Página _PAGE_ de _PAGES_",
          infoEmpty: "No hay usuarios",
          infoFiltered: "(filtrado de _MAX_ usuarios en total)",
          lengthMenu: "Mostrar _MENU_ usuarios",
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

function ModificarUsuario(id_usuario, password, nombre, permisos) {
  let arraypermisos = null;
  if (permisos.length > 0) {
    if (permisos.includes(",")) {
      arraypermisos = permisos.split(", ");
    } else {
      arraypermisos = [permisos];
    }
  }

  $("#username_txt").val(nombre);

  $("#password_txt").val(password);
  $("#password2_txt").val(password);

  $("#select_permisos").val(arraypermisos).selectpicker("refresh");

  $("#ModalAgregarUsuario").find("#titulo").html("Modificar Usuario");
  edit_mode = true;
  global_id_usuario = id_usuario;
  $("#ModalAgregarUsuario").modal("show")
  document.getElementById("username_txt").focus();
}

function MostrarModalAgregarUsuario() {
  $("#ModalAgregarUsuario")
    .find("#username_txt,#password_txt,#password2_txt")
    .val("");
  $("#select_permisos").val("default").selectpicker("refresh");

  $("#ModalAgregarUsuario").find("#titulo").html("Agregar Usuario");
  edit_mode = false;
  global_id_usuario = "";
  $("#ModalAgregarUsuario").modal("show")
  
  document.getElementById("username_txt").focus();
}

function CerrarModal() {
  $("#ModalAgregarUsuario").modal("hide")
}

function GuardarUsuario() {
  let nombre = $("#username_txt").val().trim();
  let password1 = $("#password_txt").val().trim();
  let password2 = $("#password2_txt").val().trim();
  let permisos = $("#select_permisos").val();
  if (nombre.length < 3) {
    swal("Debes ingresar un nombre de al menos 3 letras", "", "error");
  } else if (password1.length < 1) {
    swal("Debes ingresar una contraseña!", "", "error");
  } else if (password1 != password2) {
    swal("Las contraseñas ingresadas no coinciden", "", "error");
  } else if (permisos.length == 0) {
    swal("Debes seleccionar al menos un permiso", "", "error");
  } else if (/^[A-Za-z0-9]+$/.test(password1) == false) {
    swal("La contraseña solo puede tener letras y/o números", "", "error");
  } else {
    let consulta = "";
    $("#ModalAgregarUsuario").modal("hide")
    if (!edit_mode) {
      consulta = "agregar";
      $.ajax({
        url: "data_ver_usuarios.php",
        type: "POST",
        data: {
          consulta: consulta,
          nombre: nombre,
          password: password1,
          permisos: JSON.stringify(permisos),
        },
        success: function (x) {
          if (x.trim() == "success") {
            swal("El usuario fue agregado correctamente!", "", "success");
            CerrarModal();
            busca_usuarios();
          } else if (x.includes("yaexiste")) {
            swal("Ya existe un usuario con ese nombre", "", "error");
            $("#ModalAgregarUsuario").modal("show")
          } else {
            swal("Ocurrió un error", x, "error");
          }
        },
        error: function (jqXHR, estado, error) {},
      });
    } else {
      consulta = "editar";

      $.ajax({
        url: "data_ver_usuarios.php",
        type: "POST",
        data: {
          consulta: consulta,
          nombre: nombre,
          id_usuario: global_id_usuario,
          password: password1,
          permisos: JSON.stringify(permisosreales),
        },
        success: function (x) {
          if (x.trim() == "success") {
            swal("El usuario se modificó correctamente!", "", "success");
            CerrarModal();
            busca_usuarios();
          } else {
            swal("Ocurrió un error al modificar el usuario", x, "error");
          }
        },
        error: function (jqXHR, estado, error) {},
      });
    }
  }
}
