let edit_mode = false;

$(document).ready(function(){
  busca_clientes();
})

function busca_clientes() {
  $.ajax({
    beforeSend: function () {
      $("#tabla_entradas").html("Cargando clientes, espere...");
    },
    url: "data_ver_clientes.php",
    type: "POST",
    data: {consulta: "busca_clientes"},
    success: function (x) {
      $("#tabla_entradas").html(x);
      $("#tabla").DataTable({
        order: [[1, "asc"]],
        pageLength: 50,
        language: {
          lengthMenu: "Mostrando _MENU_ clientes por página",
          zeroRecords: "No hay clientes",
          info: "Página _PAGE_ de _PAGES_",
          infoEmpty: "No hay clientes",
          infoFiltered: "(filtrado de _MAX_ clientes en total)",
          lengthMenu: "Mostrar _MENU_ clientes",
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

function modificarCliente(id_cliente, tr) {
  const nombre = $(tr).attr("x-nombre");
  const domicilio = $(tr).attr("x-domicilio");
  const telefono = $(tr).attr("x-telefono");
  const mail = $(tr).attr("x-mail");
  const cuit = $(tr).attr("x-cuit");
  
  $("#ModalAgregarCliente").find(".box-title").html("Modificar Cliente");
  $("#nombrecliente_txt").val(nombre);
  $("#domiciliocliente_txt").val(domicilio);
  $("#telcliente_txt").val(telefono);
  $("#mailcliente_txt").val(mail);
  $("#cuitcliente_txt").val(cuit);
  $("#ModalAgregarCliente").attr("x-id-cliente", id_cliente)
  edit_mode = true;
  $("#ModalAgregarCliente").modal("show");
  $("#nombrecliente_txt").focus();
}

function modalAgregarCliente() {
  $("#ModalAgregarCliente")
    .find("input")
    .val("");
  $("#ModalAgregarCliente").find(".box-title").html("Agregar Cliente");
  edit_mode = false;
  $("#ModalAgregarCliente").removeAttr("x-id-cliente")
  $("#ModalAgregarCliente").modal("show");
  $("#nombrecliente_txt").focus();
}

function guardarCliente() {
  const nombre = $("#nombrecliente_txt").val().trim();
  const domicilio = $("#domiciliocliente_txt").val().trim();
  const telefono = $("#telcliente_txt").val().trim();
  const mail = $("#mailcliente_txt").val().trim();
  const cuit = $("#cuitcliente_txt").val().trim();

  if (nombre.length < 3) {
    swal("Debes ingresar un nombre de al menos 3 letras", "", "error");
  } else if (domicilio.length < 3) {
    swal("Debes ingresar un Domicilio!", "", "error");
  } else if (!telefono.length) {
    swal("Debes ingresar un teléfono o whatsapp!", "", "error");
  } else if (mail.length && !validateEmail(mail)) {
    swal("El E-Mail ingresado no es válido", "", "error");
  } else {
    $("#ModalAgregarCliente").modal("hide")
    if (!edit_mode) {
      $.ajax({
        url: "data_ver_clientes.php",
        type: "POST",
        data: {
          consulta: "agregar_cliente",
          nombre: nombre,
          domicilio: domicilio,
          telefono: telefono,
          mail: mail,
          cuit: cuit,
        },
        success: function (x) {
          if (x.trim() == "success"){
            swal("El cliente fue agregado correctamente!", "", "success");
            if (window.location.href.includes("ver_clientes")){
              busca_clientes()
            }
            else if (window.location.href.includes("cargar_pedido")){
              pone_clientes()
            }
            else if (window.location.href.includes("ver_stock")){
              setTimeout(()=>location.reload(),1500)
              
            }
          }
          else{
            swal("Ocurrió un error al modificar el Cliente", "", "error");
            console.log(x)
          } 
        },
        error: function (jqXHR, estado, error) {},
      });
    } else {
      $.ajax({
        url: "data_ver_clientes.php",
        type: "POST",
        data: {
          consulta: "modificar_cliente",
          id_cliente: $("#ModalAgregarCliente").attr("x-id-cliente"),
          nombre: nombre,
          domicilio: domicilio,
          telefono: telefono,
          mail: mail,
          cuit: cuit,
        },
        success: function (x) {
          if (x.trim() == "success"){
            swal("El cliente fue modificado correctamente!", "", "success");
            busca_clientes()
          }
          else{
            swal("Ocurrió un error al guardar el Cliente", "", "error");
            console.log(x)
          } 
        },
        error: function (jqXHR, estado, error) {},
      });
    }
  }
}

function validateEmail(email) {
  let re = /\S+@\S+\.\S+/;
  return re.test(email);
}