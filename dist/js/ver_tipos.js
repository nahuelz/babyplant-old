let edit_mode = false;

let global_id_variedad = "";

function busca_productos(filtro) {
  $.ajax({
    beforeSend: function () {
      $("#tabla_entradas").html("Cargando tipos de Producto, esperá...");
    },

    url: "busca_tipos.php",

    type: "POST",

    data: null,

    success: function (x) {
      $("#tabla_entradas").html(x);

      let table = $("#tabla").DataTable({
        pageLength: 20,
        language: {
          lengthMenu: "Mostrando _MENU_ productos por página",
          zeroRecords: "No hay productos",
          info: "Página _PAGE_ de _PAGES_",
          infoEmpty: "No hay productos",
          infoFiltered: "(filtrado de _MAX_ productos en total)",
          lengthMenu: "Mostrar _MENU_ productos",
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

      setClickevent();

      $("#tabla").on("draw.dt", function () {
        setClickevent();
      });
    },

    error: function (jqXHR, estado, error) {
      $("#tabla_entradas").html(
        "Ocurrió un error al cargar los datos: " + estado + " " + error
      );
    },
  });
}

function setClickevent() {
  let tabli = document.getElementById("tabla");
  let rows = tabli.getElementsByTagName("tr");
  for (i = 1; i < rows.length; i++) {
    let currentRow = tabli.rows[i];
    let createClickHandler = function (row) {
      return function () {
        let id = row.getElementsByTagName("td")[0].innerHTML;
        let variedad = row.getElementsByTagName("td")[1].innerHTML;
        let cant_dias = row.getElementsByTagName("td")[2].innerHTML;
        let bandejas = $(row).find("td:eq(3)").attr("x-bandejas");
        let arraycita = $(row).find("td:eq(3)").attr("x-precios").split(",");
        let arraycita2 = $(row).find("td:eq(3)").attr("x-precios-s").split(",");
        let tmp = $(row).find("td:eq(3)").attr("x-bandejas").split(", ");
        let precios = arraycita.map(Number);
        let precios_s = arraycita2.map(Number);
        let newarray = [];

        for (let i = 0; i < precios.length; i++) {
          newarray.push({
            bandeja: tmp[i],
            precio: precios[i],
            preciosemilla: precios_s[i],
            visible: true,
          });
        }
        MostrarModalAgregarProducto([
          id,
          variedad,
          cant_dias,
          bandejas,
          newarray,
        ]);
      };
    };

    currentRow.onclick = createClickHandler(currentRow);
  }
}

function MostrarModalAgregarProducto(id_producto) {
  $("#table-precios > tbody").html("");
  $("#select_tipobandeja").val("default").selectpicker("refresh");
  if (/Android|webOS|iPhone|iPad|iPod|BlackBerry/i.test(navigator.userAgent)) {
    $(".selectpicker").selectpicker("mobile");
  } else {
    let elements = document.querySelectorAll(".mobile-device");
    for (let i = 0; i < elements.length; i++) {
      elements[i].classList.remove("mobile-device");
    }
    $(".selectpicker").selectpicker({});
  }

  if (id_producto != null) {
    $("#ModalAgregarProducto").find("#titulo").html("Modificar Tipo");
    $("#nombrenewtipo_txt").val(id_producto[1]);
    $("#cant_dias_camara_txt").val(id_producto[2]);

    let listabandejas = id_producto[3].split(", ");
    $("#select_tipobandeja").selectpicker("val", listabandejas);

    let precios = id_producto[4];

    $("#table-precios tbody").html("");
    for (let i = 0; i < precios.length; i++) {
      $("#table-precios > tbody").append(
        `
            <tr>
              <th scope="row"><div style="margin-top: 5px;">${precios[i]["bandeja"]}</div></th>
              <td>
                <input type="text" class="form-control inputbandeja" placeholder="0" value="${precios[i]["precio"]}"> 
              </td>
              <td>
                <input type="text" class="form-control inputbandejasemilla" placeholder="0" value="${precios[i]["preciosemilla"]}"> 
              </td>
            </tr>

            `
      );
    }
    $(".inputbandeja,.inputbandejasemilla").on("keyup paste", function (e) {
      if (/\D/g.test(this.value)) {
        this.value = this.value.replace(/\D/g, "");
      }
    });

    $("#select_tipobandeja").on(
      "changed.bs.select",
      function (e, clickedIndex, newValue, oldValue) {
        $("#table-precios tbody").html("");
        if (clickedIndex != null) {
          const tipos = ["288", "200", "162", "128", "72", "50", "25", "49"];
          let indice = tipos[clickedIndex];
          if (newValue == false) {
            for (let i = 0; i < precios.length; i++) {
              if (indice == precios[i]["bandeja"]) {
                precios[i]["visible"] = newValue;
              }
            }
          } else if (newValue == true) {
            let existe = false;
            let index = null;
            for (let i = 0; i < precios.length; i++) {
              if (precios[i]["bandeja"] == indice) {
                existe = true;
                index = i;
                break;
              }
            }

            if (index != null && existe == true) {
              precios[index]["visible"] = true;
            } else {
              precios.push({
                bandeja: indice,
                precio: "",
                preciosemilla: "",
                visible: true,
              });
            }
          }
        }

        for (let i = 0; i < precios.length; i++) {
          if (precios[i]["visible"] == true) {
            $("#table-precios > tbody").append(
              `
              <tr>
                <th scope="row"><div style="margin-top: 5px;">${precios[i]["bandeja"]}</div></th>
                <td>
                  <input type="text" class="form-control inputbandeja" placeholder="0" value="${precios[i]["precio"]}"> 
                </td>
                <td>
                  <input type="text" class="form-control inputbandejasemilla" placeholder="0" value="${precios[i]["preciosemilla"]}"> 
                </td>
              </tr>

              `
            );
            $(".inputbandeja,.inputbandejasemilla").on(
              "keyup paste",
              function (e) {
                if (/\D/g.test(this.value)) {
                  this.value = this.value.replace(/\D/g, "");
                }
              }
            );
          }
        }
      }
    );

    document.getElementById("nombrenewtipo_txt").focus();
    global_id_variedad = id_producto[0];
    edit_mode = true;
  } else {
    document.getElementById("nombrenewtipo_txt").focus();
    $("#nombrenewtipo_txt").val("");
    $("#ModalAgregarProducto").find("#titulo").html("Agregar Tipo");
    let precios = [];
    edit_mode = false;
    global_id_variedad = "";

    $("#select_tipobandeja").on(
      "changed.bs.select",
      function (e, clickedIndex, newValue, oldValue) {
        $("#table-precios tbody").html("");
        if (clickedIndex != null) {
          const tipos = ["288", "200", "162", "128", "72", "50", "25", "49"];
          let indice = tipos[clickedIndex];
          if (newValue == false) {
            for (let i = 0; i < precios.length; i++) {
              if (indice == precios[i]["bandeja"]) {
                precios[i]["visible"] = newValue;
              }
            }
          } else if (newValue == true) {
            let existe = false;
            let index = null;
            for (let i = 0; i < precios.length; i++) {
              if (precios[i]["bandeja"] == indice) {
                existe = true;
                index = i;
                break;
              }
            }

            if (index != null && existe == true) {
              precios[index]["visible"] = true;
            } else {
              precios.push({
                bandeja: indice,
                precio: "",
                preciosemilla: "",
                visible: true,
              });
            }
          }
        }

        for (let i = 0; i < precios.length; i++) {
          if (precios[i]["visible"] == true) {
            $("#table-precios > tbody").append(
              `
              <tr>
                <th scope="row"><div style="margin-top: 5px;">${precios[i]["bandeja"]}</div></th>
                <td>
                  <input type="text" class="form-control inputbandeja" placeholder="0" value="${precios[i]["precio"]}"> 
                </td>
                <td>
                  <input type="text" class="form-control inputbandejasemilla" placeholder="0" value="${precios[i]["preciosemilla"]}"> 
                </td>
              </tr>

              `
            );
            $(".inputbandeja,.inputbandejasemilla").on(
              "keyup paste",
              function (e) {
                if (/\D/g.test(this.value)) {
                  this.value = this.value.replace(/\D/g, "");
                }
              }
            );
          }
        }
      }
    );
  }

  $("#ModalAgregarProducto").modal("show");
}

function CerrarModalProducto() {
  $("#ModalAgregarProducto").modal("hide")
}

function GuardarTipo() {
  let nombre = $("#nombrenewtipo_txt").val().trim();
  let cant_dias = $("#cant_dias_camara_txt").val().trim();
  let tipos_bandeja = $("#select_tipobandeja").val();
  let inputsprecio = $(".inputbandeja");
  let inputsprecio_semilla = $(".inputbandejasemilla");
  let puede = true;
  if (nombre.length < 3) {
    swal("ERROR", "Debes ingresar un nombre de al menos 3 letras", "error");
    puede = false;
  } else if (cant_dias.length == 0 || parseInt(cant_dias) <= 0) {
    swal("ERROR", "Debes ingresar una cantidad de DÍAS mayor a cero!", "error");
    puede = false;
  } else if (tipos_bandeja == undefined || tipos_bandeja.length == 0) {
    swal("ERROR", "Debes elegir al menos UN tipo de bandeja", "error");
    puede = false;
  }

  let listaprecios = [];
  let listaprecios_semilla = [];

  for (let i = 0; i < inputsprecio.length; i++) {
    if (
      $(inputsprecio[i]).val().trim() == "" ||
      $(inputsprecio[i]).val().trim() == "0" ||
      isNaN($(inputsprecio[i]).val().trim() == true) ||
      parseInt($(inputsprecio[i]).val().trim()) <= 0
    ) {
      listaprecios.push(0);
    } else {
      listaprecios.push(parseInt($(inputsprecio[i]).val().trim()));
    }
  }

  for (let i = 0; i < inputsprecio_semilla.length; i++) {
    if (
      $(inputsprecio_semilla[i]).val().trim() == "" ||
      $(inputsprecio_semilla[i]).val().trim() == "0" ||
      isNaN($(inputsprecio_semilla[i]).val().trim() == true) ||
      parseInt($(inputsprecio_semilla[i]).val().trim()) <= 0
    ) {
      listaprecios_semilla.push(0);
    } else {
      listaprecios_semilla.push(
        parseInt($(inputsprecio_semilla[i]).val().trim())
      );
    }
  }

  if (puede == true) {
    let tipo = "";
    tipos_bandeja = JSON.stringify(tipos_bandeja);
    listaprecios = JSON.stringify(listaprecios);
    listaprecios_semilla = JSON.stringify(listaprecios_semilla);
    if (!edit_mode) {
      tipo = "agregar_tipo";
      $.ajax({
        url: "guarda_producto.php",
        type: "POST",
        data: {
          tipo: tipo,
          nombre: nombre,
          cant_dias: cant_dias,
          tipos_bandeja: tipos_bandeja,
          listaprecios: listaprecios,
          listaprecios_semilla: listaprecios_semilla,
        },
        success: function (x) {
          if (x.includes("existe")) {
            swal(
              "ERROR",
              "Ya existe un tipo de producto con ese nombre",
              "error"
            );
          } else {
            busca_productos(null);
            $("#nombrenewtipo_txt").val("");
            $("#cant_dias_camara_txt").val("0");
            $("#nombrenewtipo_txt").focus();
            $("#ModalAgregarProducto").modal("hide")
            swal("El tipo de producto se agregó correctamente!", "", "success");
          }
        },
        error: function (jqXHR, estado, error) {
          alert("Hubo un error al agregar el producto " + error);
        },
      });
    } else {
      tipo = "editar_tipo";

      $.ajax({
        url: "guarda_producto.php",
        type: "POST",
        data: {
          tipo: tipo,
          global_id_variedad: global_id_variedad,
          nombre: nombre,
          cant_dias: cant_dias,
          tipos_bandeja: tipos_bandeja,
          listaprecios: listaprecios,
          listaprecios_semilla: listaprecios_semilla,
        },
        success: function (x) {
          $("#ModalAgregarProducto").modal("hide")
          swal(
            "El tipo de producto fue modificado correctamente!",
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
