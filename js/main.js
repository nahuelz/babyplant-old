$(document).ready(function(){
	$(".btn-exit-system").on("click", function(e){
		e.preventDefault();
		let urlDir=$(this).attr("href");
		
		swal("¿Estás seguro/a de Cerrar Sesión?","", {
			icon: "warning",
			buttons: {
			  cancel: "Cancelar",
			  catch: {
				text: "Cerrar Sesión",
				value: "catch",
			  }
			},
			})
			.then((value) => {
			  switch (value) {
				case "catch":
					window.location.href=urlDir;
				default:
					break;
			  }
			});


		
	});
});