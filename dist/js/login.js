$(document).ready(function () {
  $("#loginform").on("submit", function (event) {
    event.preventDefault();
    event.stopPropagation();
    
  });
});

function logear(){
    const user = $("#UserName").val().trim();
    const pass = $("#Pass").val().trim();
    login(user, pass);
    //alert("MICHO")
    
}

function login(user, pass) {
  $.ajax({
    url: "valida_usr.php",
    type: "POST",
    cache: false,
    data: { user: user, pass: pass },
    success: function (x) {
      $(".contenedor").html(x);
      console.log(x)
    },
    error: function (jqXHR, estado, error) {
      swal("Error al Iniciar Sesión", error, "error");
    },
  });
}
