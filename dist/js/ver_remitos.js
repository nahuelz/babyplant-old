$(function(){
  $('#daterange-btn').daterangepicker({
    ranges: {
      'Hoy': [moment(), moment()],
      'Ayer': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
      'Los ultimos 7 dias': [moment().subtract(6, 'days'), moment()],
      'Los ultimos 30 dias': [moment().subtract(29, 'days'), moment()],
      'Los ultimos 6 meses': [moment().subtract(180, 'days'), moment()],
      'Este mes': [moment().startOf('month'), moment().endOf('month')],
      'El mes pasado': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')],
      'Todo el año': [moment().startOf('year'), moment()]
    },
    startDate: moment().subtract(180, 'days'),
    endDate: moment()
  },
  function (start, end) {
    $('.fe').html(start.format('DD/MM/YYYY') + ' - ' + end.format('DD/MM/YYYY'));
    let xstart=start.format('YYYY-MM-DD');
    let xend=end.format('YYYY-MM-DD');
    $("#fi").val(xstart);
    $("#ff").val(xend);
  });
});

function busca_entradas(){
  let fecha=$("#fi").val();
  let fechaf=$("#ff").val();
  let tipos = $("#select_tipo").val();
  let cliente = $("#busca_cliente").val().trim().toUpperCase();
  if (cliente.length == 0) cliente = null;
  else if (cliente.includes(",")){
    cliente = cliente.replace(",","|");
  }
  let filtros = {
    "cliente" : cliente,
  };
  filtros = JSON.stringify(filtros);
  $.ajax({
    beforeSend: function(){
      $("#tabla_entradas").html("<h3 style='margin-left: 20px'>Buscando remitos, espere...</h3>");
    },
    url: 'busca_remitos.php',
    type: 'POST',
    data: {fechai:fecha,fechaf:fechaf, filtros:filtros},
    success: function(x){
      $("#tabla_entradas").html(x);
      $('#tabla').DataTable({
        "order": [[ 0, "desc" ]],
        "language": {
            "lengthMenu": "Mostrando _MENU_ remitos por página",
            "zeroRecords": "No hay remitos",
            "info": "Página _PAGE_ de _PAGES_",
            "infoEmpty": "No hay remitos",
            "infoFiltered": "(filtrado de _MAX_ remitos en total)",
            "lengthMenu":     "Mostrar _MENU_ remitos",
            "loadingRecords": "Cargando...",
            "processing":     "Procesando...",
            "search":         "Buscar:",
            "zeroRecords":    "No se encontraron resultados",
            "paginate": {
                "first":      "Primera",
                "last":       "Última",
                "next":       "Siguiente",
                "previous":   "Anterior"
            },
            "aria": {
                "sortAscending":  ": tocá para ordenar en modo ascendente",
                "sortDescending": ": tocá para ordenar en modo descendente"
            }
        }
      });
    },
    error: function(jqXHR,estado,error){
      $("#tabla_entradas").html('Ocurrió un error: contactá al desarrollador'+'     '+estado +' '+error);
    }
  });
}

function printRemito(tipo, id){
  $("#ModalVerRemito").modal("show")
  $.ajax({
    beforeSend: function(){
      
    },
    url: 'cargar_remitos.php',
    type: 'POST',
    data: {tipo:"cargar_remito", id_remito:id.replace("r_","")},
    success: function(x){
      $("#remito_container").html(x);
    },
    error: function(jqXHR,estado,error){
    }

  });
}

function print_Remito(tipo) {
  if (tipo == 1){  
    func_printBusqueda();
    document.getElementById("ocultar").style.display = "none";
    document.getElementById("miVentana").style.display = "block";
  }else{
    document.getElementById("ocultar").style.display = "block";
    document.getElementById("miVentana").style.display = "none";
    $("#miVentana").html("");
  }
}

function func_printBusqueda() {
  $("#miVentana").html("");
  $("#miVentana").append(document.getElementById("remito_container").outerHTML);       
  //$("#miVentana").find("tr,td,th").css({'font-size':'9px', 'word-wrap':'break-word'});
  setTimeout("window.print();print_Remito(2)", 500); 
}

function expande_busqueda(){
  let contenedor = $("#contenedor_busqueda");
  if ($(contenedor).css("display") == "none")
    $(contenedor).css({"display":"block"});
  else{
    $(contenedor).css({"display":"none"});
    $("#select_tipo,#select_estado,#busca_tiporevision").val('default').selectpicker("refresh");
    $("#busca_subtipo,#busca_variedad,#busca_cliente").val("");
  }
}

function quitar_filtros(){
  $("#select_tipo,#select_estado,#busca_tiporevision").val('default').selectpicker("refresh");
  $("#busca_subtipo,#busca_variedad,#busca_cliente").val("");
  busca_entradas();
}

function setSelected(objeto){
  let tr = $(objeto);
  if(tr.hasClass("selected2")) {
    tr.removeClass("selected2");
  } else {
    $(".selected2").removeClass("selected2");
    tr.addClass("selected2");
  }
}