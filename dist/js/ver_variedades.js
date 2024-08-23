let edit_mode = false;

function busca_productos(filtro) {
  $.ajax({
    beforeSend: function () {
      $("#tabla_entradas").html("Cargando productos, espere...");
    },
    url: "data_ver_variedades.php",
    type: "POST",
    data: { consulta: "busca_variedades", filtro: filtro },
    success: function (x) {
      $("#tabla_entradas").html(x);
      let table = $("#tabla").DataTable({
        pageLength: 100,
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
      
    },
    error: function (jqXHR, estado, error) {
      swal("Ocurrió un error al cargar los datos", error.toString(), "error");
    },
  });
}

function MostrarModalAgregarProducto(id_producto, variedad, precios2) {
  $("#table-precios > tbody").html("");
  $("#table-precios-variedad > tbody").html("");

  if (id_producto) {
    $("#ModalAgregarProducto").find("#titulo").html("Modificar Variedad");
    $("#select_tipo2").attr("disabled", "disabled");
    $("#select_tipo2").val("default").selectpicker("refresh");
    $("#select_subtipo").attr("disabled", "disabled");
    $("#subtipos_btn").hide();
    $("#tipos_btn").hide();
    $("#select_subtipo").val("default").selectpicker("refresh");
    $("#variedadproducto_txt").val(variedad);
    $("#ModalAgregarProducto").attr("x-id-variedad", id_producto);
    const precios = JSON.parse(precios2);

    let keys = Object.keys(precios);
    keys.sort(function (a, b) {
      return b - a;
    });
    keys.forEach((key, index) => {
      if (precios[key]["visible"] == true) {
        const precioSinsemilla = isNaN(parseInt(precios[key]["sinsemilla"]))
          ? ""
          : parseInt(precios[key]["sinsemilla"]);
        const precioConsemilla = isNaN(parseInt(precios[key]["consemilla"]))
          ? ""
          : parseInt(precios[key]["consemilla"]);
        $("#table-precios-variedad > tbody").append(
          `
          <tr class='celda-variedad'>
              <th scope="row"><div style="margin-top: 5px;">${key}</div></th>
              <td>
                <input type="text" x-bandeja="${key}" class="form-control inputbandeja-sin" value="${precioSinsemilla}"> 
              </td>
              <td>
                <input type="text" x-bandeja="${key}" class="form-control inputbandeja-semilla" value="${precioConsemilla}"> 
              </td>
          </tr>
        `
        );
        $(".inputbandeja-sin,.inputbandeja-semilla").on(
          "keyup paste",
          function (e) {
            if (/\D/g.test(this.value)) {
              this.value = this.value.replace(/\D/g, "");
            }
          }
        );
      }
    });

    edit_mode = true;
  } else {
    $("#nombrenewtipo_txt").val("");
    $("#nombrenewsubtipo_txt").val("");
    $("#select_tipos_disponibles").val("default").selectpicker("refresh");
    $("#select_tipo2").removeAttr("disabled");
    $("#select_subtipo").removeAttr("disabled");
    $("#variedadproducto_txt").removeAttr("disabled");
    $("#variedadproducto_txt").val("");
    $("#select_subtipo").find("option").remove();
    $("#select_subtipo").val("default").selectpicker("refresh");
    $("#select_tipo2").val("default").selectpicker("refresh");
    $("#subtipos_btn").show();
    $("#tipos_btn").show();
    $("#ModalAgregarProducto").find("#titulo").html("Agregar Variedad");
    edit_mode = false;
  }

  openCity(event, "Variedades");

  $("#ModalAgregarProducto").modal("show");
}

function CerrarModalProducto() {
  $("#ModalAgregarProducto").modal("hide");
}

function GuardarProducto() {
  //GUARDAR VARIEDAD
  let tipoart = $("#select_tipo2 :selected").text();
  let subtipoart = $("#select_subtipo :selected").val();
  let nombre = $("#variedadproducto_txt").val().trim();
  let puede = true;
  if (tipoart.length < 1) {
    if (edit_mode == false) {
      swal("Debes elegir un tipo de producto", "", "error");
      puede = false;
    }
  } else if (subtipoart.length < 1) {
    if (edit_mode == false) {
      swal("Debes elegir un subtipo de producto", "", "error");
      puede = false;
    }
  } else if (nombre.length < 3) {
    swal("Debes ingresar un nombre de al menos 3 letras", "", "error");
    puede = false;
  }
  let inputsprecio = $(".inputbandeja-sin");
  let listaprecios = [];
  let inputspreciosemilla = $(".inputbandeja-semilla");

  for (let i = 0; i < inputsprecio.length; i++) {
    let value = isNaN($(inputsprecio[i]).val().trim())
      ? null
      : $(inputsprecio[i]).val().trim();
    let valuesemilla = isNaN($(inputspreciosemilla[i]).val().trim())
      ? null
      : $(inputspreciosemilla[i]).val().trim();

    if (value != null && parseInt(value) < 0) {
      puede = false;
      swal("Los precios no pueden ser negativos!", "", "error");
      break;
    } else if (valuesemilla != null && parseInt(valuesemilla) < 0) {
      puede = false;
      swal("Los precios no pueden ser negativos!", "", "error");
      break;
    } else {
      listaprecios.push({
        precio: value,
        precio_semilla: valuesemilla,
        bandeja: $(inputsprecio[i]).attr("x-bandeja"),
      });
    }
  }

  if (puede == true) {
    CerrarModalProducto();
    let tipo = "";
    if (!edit_mode) {
      tipo = "agregar";
      $.ajax({
        url: "guarda_producto.php",
        type: "POST",
        data: {
          tipo: tipo,
          subtipoart: subtipoart,
          nombre: nombre,
          precios: JSON.stringify(listaprecios),
        },
        success: function (x) {
          if (x.includes("existe")) {
            swal("Ya existe una variedad con ese nombre!", "", "error");
            $("#ModalAgregarProducto").modal("show");
          } else {
            busca_productos(null);
            $("#variedadproducto_txt").val("");
            $("#select_subtipo").find("option").remove();
            $("#select_subtipo").val("default").selectpicker("refresh");
            $("#select_tipo2").val("default").selectpicker("refresh");
            $("#variedadproducto_txt").focus();
            swal("El producto se agregó correctamente!", "", "success");
          }
        },
        error: function (jqXHR, estado, error) {
          swal("Ocurrió un error", error.toString(), "error");
        },
      });
    } else {
      tipo = "editar";
      $.ajax({
        url: "guarda_producto.php",
        type: "POST",
        data: {
          tipo: tipo,
          id_variedad: $("#ModalAgregarProducto").attr("x-id-variedad"),
          nombre: nombre,
          precios: JSON.stringify(listaprecios),
        },
        success: function (x) {
          console.log(x);
          swal("El producto fue modificado correctamente!", "", "success");
          busca_productos(null);
        },
        error: function (jqXHR, estado, error) {
          swal(
            "Ocurrió un error al modificar el producto",
            error.toString(),
            "error"
          );
          $("#ModalAgregarProducto").modal("show");
        },
      });
    }
  }
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
    swal("ERROR", "Debes ingresar una cantidad mayor a cero!", "error");
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
      puede = false;
      swal("ERROR", "Los precios deben ser mayores a cero!", "error");
      break;
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
      puede = false;
      swal("ERROR", "Los precios deben ser mayores a cero!", "error");
      break;
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
          pone_tipos();
          busca_productos(null);
          $("#select_tipobandeja").val("default").selectpicker("refresh");
          $("#nombrenewtipo_txt").val("");
          $("#cant_dias_camara_txt").val("0");
          $("#nombrenewtipo_txt").focus();
          $("#table-precios tbody").html("");
          swal("El tipo de producto se agregó correctamente!", "", "success");
        }
      },
      error: function (jqXHR, estado, error) {
        alert("Hubo un error al agregar el producto " + error);
      },
    });
  }
}

function GuardarSubTipo() {
  let nombre = $("#nombrenewsubtipo_txt").val().trim();
  let id_tipo = $("#select_tipo3 :selected").val();
  let puede = true;
  if (nombre.length < 1) {
    swal("Debes ingresar un nombre de Tipo de producto!", "", "error");
    puede = false;
  } else if (id_tipo.length < 1) {
    swal("Debes seleccionar un tipo de producto!", "", "error");
    puede = false;
  }
  if (puede == true) {
    CerrarModalProducto();
    let tipo = "";
    tipo = "agregar_subtipo";
    $.ajax({
      url: "guarda_producto.php",
      type: "POST",
      data: { tipo: tipo, nombre: nombre, id_tipo: id_tipo },
      success: function (x) {
        if (x.includes("existe")) {
          swal("Ya existe un subtipo de producto con ese nombre", "", "error");
          $("#ModalAgregarProducto").modal("show");
        } else {
          pone_tipos();
          busca_productos(null);
          $("#select_tipos3").val("default").selectpicker("refresh");
          $("#select_subtipos_disponibles")
            .val("default")
            .selectpicker("refresh");
          $("#nombrenewsubtipo_txt").val("");
          $("#nombrenewsubtipo_txt").focus();
          swal(
            "El subtipo de producto se agregó correctamente!",
            "",
            "success"
          );
        }
      },
      error: function (jqXHR, estado, error) {
        alert("Hubo un error al agregar el producto " + error);
      },
    });
  }
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
      $("#select_tipo").html(x).selectpicker("refresh");
      $("#select_tipo2").html(x).selectpicker("refresh");
      $("#select_tipo3").html(x).selectpicker("refresh");
      $("#select_tipos_disponibles").html(x).selectpicker("refresh");

      $("#select_tipo2").change(function () {
        pone_subtipos(this.value, 1);
        pone_precios(this.value);
      });
      $("#select_tipo3").on(
        "changed.bs.select",
        function (e, clickedIndex, newValue, oldValue) {
          pone_subtipos(this.value, 2);
        }
      );
    },
    error: function (jqXHR, estado, error) {},
  });
}

function pone_subtipos(id_tipo, index) {
  $.ajax({
    beforeSend: function () {
      $("#select_subtipo").html("Cargando subtipos...");
    },
    url: "pone_tiposdeproducto.php",
    type: "POST",
    data: { tipo: "carga_subtipos", id_tipo: id_tipo },
    success: function (x) {
      if (index == 1) {
        $(".selectpicker").selectpicker();
        $("#select_subtipo").val("default").selectpicker("refresh");
        $("#select_subtipo").html(x).selectpicker("refresh");
        $("#select_subtipo").on(
          "changed.bs.select",
          function (e, clickedIndex, newValue, oldValue) {
            $("#variedadproducto_txt").focus();
          }
        );
      } else if (index == 2) {
        $(".selectpicker").selectpicker();
        $("#select_subtipos_disponibles")
          .val("default")
          .selectpicker("refresh");
        $("#select_subtipos_disponibles").html(x).selectpicker("refresh");
      }
    },
    error: function (jqXHR, estado, error) {},
  });
}

function pone_precios(id_tipo) {
  $.ajax({
    beforeSend: function () {},
    url: "pone_tiposdeproducto.php",
    type: "POST",
    data: { tipo: "carga_precios", id_tipo: id_tipo },
    success: function (x) {
      $(".inputbandeja-sin").remove();
      $(".inputbandeja-semilla").remove();
      if (x.length > 0) {
        let precios = JSON.parse(x);

        let tipos = ["288", "200", "162", "128", "72", "50", "25", "49"];
        $("#table-precios tbody").html("");
        $("#table-precios-variedad tbody").html("");
        for (let i = 0; i < Object.keys(precios).length; i++) {
          const precioSinsemilla = isNaN(
            parseInt(precios[tipos[i]]["sinsemilla"])
          )
            ? ""
            : "GENERAL: " +
              parseInt(precios[tipos[i]]["sinsemilla"]).toString();
          if (precioSinsemilla != "") {
            const precioConsemilla = isNaN(
              parseInt(precios[tipos[i]]["consemilla"])
            )
              ? ""
              : "GENERAL: " +
                parseInt(precios[tipos[i]]["consemilla"]).toString();
            $("#table-precios-variedad > tbody").append(
              `
                  <tr class='celda-variedad'>
                    <th scope="row"><div style="margin-top: 5px;">${tipos[i]}</div></th>
                    <td>
                      <input type="text" x-bandeja="${tipos[i]}" class="form-control inputbandeja-sin" placeholder="${precioSinsemilla}"> 
                    </td>
                    <td>
                      <input type="text" x-bandeja="${tipos[i]}" class="form-control inputbandeja-semilla" placeholder="${precioConsemilla}"> 
                    </td>
                  </tr>
                `
            );
            $(".inputbandeja-sin,.inputbandeja-semilla").on(
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
    },
    error: function (jqXHR, estado, error) {},
  });
}

function openCity(evt, cityName) {
  let i, tabcontent, tablinks;
  tabcontent = document.getElementsByClassName("tabcontent");
  for (i = 0; i < tabcontent.length; i++) {
    tabcontent[i].style.display = "none";
  }

  tablinks = document.getElementsByClassName("tablinks");
  for (i = 0; i < tablinks.length; i++) {
    tablinks[i].className = tablinks[i].className.replace(" active", "");
  }
  // Show the current tab, and add an "active" class to the button that opened the tab
  document.getElementById(cityName).style.display = "block";
  evt.currentTarget.className += " active";
}

function mostrarVistaAgregarTipo() {
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

  document.getElementById("nombrenewtipo_txt").focus();
  $("#nombrenewtipo_txt").val("");
  let precios = [];

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

function eliminarVariedad(id){
  swal("Estás seguro de Eliminar esta Variedad?", "", {
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
          url: "data_ver_variedades.php",
          type: "POST",
          data: { consulta: "eliminar", id: id },
          success: function (x) {
            if (x.trim() == "success") {
              swal("Eliminaste la Variedad correctamente!", "", "success")
              busca_productos(null)
            } else {
              swal("Ocurrió un error al eliminar la Variedad", x, "error");
            }
          },
          error: function (jqXHR, estado, error) {
            swal("Ocurrió un error al eliminar la Variedad", error, "error");
          },
        });
        break;
    }
  });
}