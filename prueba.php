<!DOCTYPE html>
<html>
<head>
    <title>Listas en Columnas con Estilos y Funcionalidad Adicional</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        .column {
            padding: 10px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="row">
            <div class="col">
                <h2>Tipos de Producto</h2>
                <ul id="tipos" class="list-group" onclick="mostrarSubtipos(event)">
                    <li class="list-group-item">Tomate</li>
                    <li class="list-group-item">Lechuga</li>
                    <li class="list-group-item">Zanahoria</li>
                </ul>
            </div>

            <div class="col">
                <h2>Subtipos de Producto</h2>
                <ul id="subtipos" class="list-group" onclick="mostrarVariedades(event)" style="display: none;"></ul>
            </div>

            <div class="col">
                <h2>Variedades</h2>
                <ul id="variedades" class="list-group" onclick="agregarVariedad(event)" style="display: none;"></ul>
            </div>

            <div class="col">
                <h2>Variedades Seleccionadas</h2>
                <ul id="variedadesSeleccionadas" class="list-group"></ul>
            </div>
        </div>
    </div>

    <script>
        var subtiposSeleccionados = [];
        var variedadesSeleccionadas = [];

        function mostrarSubtipos(event) {
            var tipoSeleccionado = event.target;
            var subtipos = {};

            if (tipoSeleccionado.tagName === "LI") {
                // Remover clase "active" de todos los elementos
                var tipos = document.querySelectorAll("#tipos li");
                for (var i = 0; i < tipos.length; i++) {
                    tipos[i].classList.remove("active");
                }

                // Agregar clase "active" al elemento seleccionado
                tipoSeleccionado.classList.add("active");

                // Limpiar lista de subtipos, variedades y variedades seleccionadas
                var subtiposList = document.getElementById("subtipos");
                var variedadesList = document.getElementById("variedades");
                var variedadesSeleccionadasList = document.getElementById("variedadesSeleccionadas");
                subtiposList.style.display = "none";
                variedadesList.style.display = "none";
                variedadesList.innerHTML = "";
                variedadesSeleccionadasList.innerHTML = "";

                // Mostrar subtipos correspondientes
                var tipo = tipoSeleccionado.innerHTML;
                if (tipo === "Tomate") {
                    subtipos = {
                        "Tomate Redondo": ["Sakata X 200", "Otro subtipo del tomate redondo"],
                        "Tomate Perita": ["Perita 1", "Perita 2", "Otro subtipo del tomate perita"]
                    };
                } else if (tipo === "Lechuga") {
                    subtipos = {
                        "Lechuga Tipo 1": ["Variedad 1", "Variedad 2"],
                        "Lechuga Tipo 2": ["Variedad 3", "Variedad 4", "Variedad 5"],
                        "Lechuga Tipo 3": ["Variedad 6"]
                    };
                } else if (tipo === "Zanahoria") {
                    subtipos = {
                        "Zanahoria Tipo 1": ["Variedad 1"],
                        "Zanahoria Tipo 2": ["Variedad 2", "Variedad 3"]
                    };
                }

                // Mostrar lista de subtipos
                subtiposList.style.display = "block";
                subtiposList.innerHTML = ""; // Reiniciar la lista de subtipos al seleccionar un nuevo tipo

                for (var subtipo in subtipos) {
                    var li = document.createElement("li");
                    li.className = "list-group-item";
                    li.innerHTML = subtipo;
                    subtiposList.appendChild(li);
                }
            }
        }

        function mostrarVariedades(event) {
            var subtipoSeleccionado = event.target;
            var variedades = {};

            if (subtipoSeleccionado.tagName === "LI") {
                // Remover clase "active" de todos los elementos
                var subtipos = document.querySelectorAll("#subtipos li");
                for (var i = 0; i < subtipos.length; i++) {
                    subtipos[i].classList.remove("active");
                }

                // Agregar clase "active" al elemento seleccionado
                subtipoSeleccionado.classList.add("active");

                // Limpiar lista de variedades y variedades seleccionadas
                var variedadesList = document.getElementById("variedades");
                var variedadesSeleccionadasList = document.getElementById("variedadesSeleccionadas");
                variedadesList.style.display = "none";
                variedadesList.innerHTML = "";

                // Mostrar variedades correspondientes
                var subtipo = subtipoSeleccionado.innerHTML;
                if (subtipo === "Tomate Redondo") {
                    variedades = ["Sakata X 200", "Otro subtipo del tomate redondo"];
                } else if (subtipo === "Tomate Perita") {
                    variedades = ["Perita 1", "Perita 2", "Otro subtipo del tomate perita"];
                } else if (subtipo === "Lechuga Tipo 1") {
                    variedades = ["Variedad 1", "Variedad 2"];
                } else if (subtipo === "Lechuga Tipo 2") {
                    variedades = ["Variedad 3", "Variedad 4", "Variedad 5"];
                } else if (subtipo === "Lechuga Tipo 3") {
                    variedades = ["Variedad 6"];
                } else if (subtipo === "Zanahoria Tipo 1") {
                    variedades = ["Variedad 1"];
                } else if (subtipo === "Zanahoria Tipo 2") {
                    variedades = ["Variedad 2", "Variedad 3"];
                }

                // Mostrar lista de variedades
                variedadesList.style.display = "block";
                variedadesList.innerHTML = ""; // Reiniciar la lista de variedades al seleccionar un nuevo subtipo

                for (var i = 0; i < variedades.length; i++) {
                    var li = document.createElement("li");
                    li.className = "list-group-item";
                    li.innerHTML = variedades[i];
                    variedadesList.appendChild(li);
                }
            }
        }

        function agregarVariedad(event) {
            var variedadSeleccionada = event.target;
            if (variedadSeleccionada.tagName === "LI") {
                // Remover clase "active" de todos los elementos
                var variedades = document.querySelectorAll("#variedades li");
                for (var i = 0; i < variedades.length; i++) {
                    variedades[i].classList.remove("active");
                }

                // Agregar clase "active" al elemento seleccionado
                variedadSeleccionada.classList.add("active");

                // Obtener variedad seleccionada
                var variedad = variedadSeleccionada.innerHTML;

                // Verificar si la variedad ya existe en la lista de variedades seleccionadas
                var existente = false;
                var variedadesSeleccionadasList = document.getElementById("variedadesSeleccionadas");
                var variedadesSeleccionadasItems = variedadesSeleccionadasList.getElementsByTagName("li");
                for (var i = 0; i < variedadesSeleccionadasItems.length; i++) {
                    if (variedadesSeleccionadasItems[i].innerHTML === variedad) {
                        existente = true;
                        break;
                    }
                }

                if (existente) {
                    return; // Salir de la función si la variedad ya está seleccionada
                }

                // Agregar variedad a la lista de variedades seleccionadas
                variedadesSeleccionadas.push(variedad);

                // Crear elemento en la lista de variedades seleccionadas
                var li = document.createElement("li");
                li.className = "list-group-item d-flex justify-content-between align-items-center";
                li.innerHTML = variedad;
                var inputCantidad = document.createElement("input");
                inputCantidad.type = "number";
                inputCantidad.className = "form-control";
                inputCantidad.placeholder = "Cantidad";
                var button = document.createElement("button");
                button.className = "btn btn-danger btn-sm";
                button.innerHTML = "Eliminar";
                button.onclick = function() {
                    // Remover variedad de la lista de variedades seleccionadas
                    var index = variedadesSeleccionadas.indexOf(variedad);
                    if (index !== -1) {
                        variedadesSeleccionadas.splice(index, 1);
                    }
                    li.parentNode.removeChild(li);
                };
                li.appendChild(inputCantidad);
                li.appendChild(button);

                // Agregar elemento a la lista de variedades seleccionadas
                var variedadesSeleccionadasList = document.getElementById("variedadesSeleccionadas");
                variedadesSeleccionadasList.appendChild(li);
            }
        }
    </script>
</body>
</html>
