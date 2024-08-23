<?php
include "./class_lib/sesionSecurity.php";
error_reporting(0);
include 'class_lib/class_conecta_mysql.php';

$con = mysqli_connect($host, $user, $password, $dbname);
// Check connection
if (!$con) {
    die("Connection failed: " . mysqli_connect_error());
}

$consulta = $_POST["consulta"];

if ($consulta == "pone_box_pedidos"){
    $cantidad="0";
    $query="Select COUNT(id_pedido) as cuenta FROM pedidos";
    $val = mysqli_query($con, $query);

    if (mysqli_num_rows($val)>0){
        $r=mysqli_fetch_assoc($val);
        $cantidad = $r['cuenta'];
    }

    echo "<a href=\"ver_pedidos.php\">
            <div class=\"small-box bg-aqua\">
            <div class=\"inner\">
                <h3>$cantidad</h3>
                <p class=\"titulo-seccion\">Pedidos</p>
            </div>
            <div class=\"icon\">
                <i class=\"ion ion-bag\"></i>
            </div>
            <span class=\"small-box-footer\">Ver Pedidos <i class=\"fa fa-arrow-circle-right\"></i></span>
            </div>
        </a>";
}
else if ($consulta == "pone_box_planificacion"){
    $query="Select COUNT(id_artpedido) as cantidad FROM articulospedidos WHERE estado = 0;";
    $val = mysqli_query($con, $query);
    $cantidad = 0;
    if (mysqli_num_rows($val)>0){
        $r=mysqli_fetch_assoc($val);
        $cantidad = $r["cantidad"];
    }
    echo "
        <a href=\"ver_planificacion.php\">
            <div class=\"small-box bg-red\">
                <div class=\"inner\">
                    <h3>$cantidad</h3>
                    <p>Pedidos para Planificar</p>
                </div>
                <div class=\"icon\">
                  <i class=\"ion ion-calendar\"></i>
                </div>
                <span class=\"small-box-footer\">Ver Planificación <i class=\"fa fa-arrow-circle-right\"></i></span>
            </div>
        </a>";
}
else if ($consulta == "pone_box_siembra"){
    $cantidad = 0;
    $query="Select COUNT(id_artpedido) as cantidad from articulospedidos WHERE estado = 2";
    $val = mysqli_query($con, $query);
    if (mysqli_num_rows($val)>0){
        $rf=mysqli_fetch_assoc($val);
        $cantidad=$rf['cantidad'];  
    } 
    echo "
        <a href=\"ver_plansiembra.php\">
            <div class=\"small-box bg-yellow\">
                <div class=\"inner\">
                    <h3>$cantidad</h3>
                    <p>Planificación de Siembra</p>
                </div>
                <div class=\"icon\">
                    <i class=\"fa fa-th-list\"></i>
                </div> 
                <span class=\"small-box-footer\">Ver Siembra <i class=\"fa fa-arrow-circle-right\"></i></span>
            </div>  
        </a>
    ";         
}
else if ($consulta == "pone_box_camara"){
    $cantidad=0;
    $query="Select count(id_artpedido) as cantidad FROM articulospedidos WHERE estado = 3";
    $val = mysqli_query($con, $query);
    if (mysqli_num_rows($val)>0){
        $rf=mysqli_fetch_assoc($val);
        $cantidad=$rf["cantidad"];
    }
    echo "
        <a href=\"ver_camara.php\">            
            <div class=\"small-box bg-green\">
                <div class=\"inner\">
                    <h3>$cantidad</h3>
                    <p>Órdenes en Cámara</p>
                </div>
                <div class=\"icon\">
                    <i class=\"fa fa-list\"></i>
                </div>
                <span class=\"small-box-footer\">Ver Cámara <i class=\"fa fa-arrow-circle-right\"></i></span>
            </div>
        </a>
    ";
}
else if ($consulta == "pone_box_ordenes"){
    $cantidad="0";
    $query="Select COUNT(id_orden) as cuenta FROM ordenes_siembra";
    $val = mysqli_query($con, $query);
    if (mysqli_num_rows($val)>0){
        $r=mysqli_fetch_assoc($val);
        $cantidad = $r['cuenta'];
    }
    echo "
        <a href=\"ver_ordenes_siembra.php\">
        <div class=\"small-box bg-aqua\">
        <div class=\"inner\">
            <h3>$cantidad</h3>
            <p class=\"titulo-seccion\">Órdenes de Siembra</p>
        </div>
        <div class=\"icon\">
            <i class=\"fa fa-leaf\"></i>
        </div>
        <span class=\"small-box-footer\">Ver Órdenes <i class=\"fa fa-arrow-circle-right\"></i></span>
        </div>
        </a>
    ";
}
else if ($consulta == "pone_box_problemas"){
    $cantidad="0";
    $query="Select COUNT(id_artpedido) as cuenta FROM articulospedidos WHERE problema = 1";
    $val = mysqli_query($con, $query);
    if (mysqli_num_rows($val)>0){
        $r=mysqli_fetch_assoc($val);
        $cantidad = $r['cuenta'];
    }
    echo "
        <a href=\"ver_problemas.php\">
            <div class=\"small-box\" style=\"background-color:red\">
                <div class=\"inner\" style=\"color:white\">
                    <h3>$cantidad</h3>
                    <p class=\"titulo-seccion\">Pedidos con Problemas</p>
                </div>
                <div class=\"icon\">
                    <i class=\"fa fa-exclamation-circle\"></i>
                </div>
                <span class=\"small-box-footer\">Ver Pedidos <i class=\"fa fa-arrow-circle-right\"></i></span>
            </div>
        </a>
    ";
}
else if ($consulta == "pone_box_planentregas"){
    $cantidad="0";
    $query="Select count(id_artpedido) as total from articulospedidos WHERE estado = 4;";
    $val = mysqli_query($con, $query);

    if (mysqli_num_rows($val)>0){
        $rf=mysqli_fetch_assoc($val);
        $cantidad=$rf['total'];
    }
    echo "
        <a href=\"ver_planentregas.php\">
            <div class=\"small-box bg-primary text-light\">
            <div class=\"inner\">
                <h3>$cantidad</h3>
                <p>Planificación de Entregas</p>
            </div>
            <div class=\"icon\">
                <i class=\"fa fa-truck\"></i>
            </div>
            <span class=\"small-box-footer\">Ver Entregas <i class=\"fa fa-arrow-circle-right\"></i></span>
            </div>
        </a>
    ";
}
else if ($consulta == "pone_box_agenda"){
    $cantidad="0";
    $query="Select count(id_agenda) as total from agenda_entregas WHERE estado = 0;";
    $val = mysqli_query($con, $query);

    if (mysqli_num_rows($val)>0){
        $rf=mysqli_fetch_assoc($val);
        $cantidad=$rf['total'];    
    }    
    echo "
        <a href=\"ver_agenda_entregas.php\">
            <div class=\"small-box text-black bg-lime\">
                <div class=\"inner\">
                    <h3>$cantidad</h3>
                    <p>Agenda de Entregas</p>
                </div>
                <div class=\"icon\">
                    <i class=\"fa fa-list-ul\"></i>
                </div>
                <span class=\"small-box-footer\">Ver Ordenes <i class=\"fa fa-arrow-circle-right\"></i></span>
            </div>
        </a>

    ";
}
else if ($consulta == "pone_box_historial"){
    $cantidad="0";
    $val = mysqli_query($con, "SELECT (
        Select IFNULL(COUNT(id_entrega),0) as cuenta FROM entregas
        )
         AS entregas,
        (
            Select IFNULL(COUNT(id_entrega),0) as cuenta FROM entregas_stock
        )  AS entregas_stock");
    
    
    if (mysqli_num_rows($val)>0){
        $r=mysqli_fetch_assoc($val);
        $cantidad = $r['entregas'] + $r["entregas_stock"];
    }
    echo "
        <a href=\"ver_historial_entregas.php\">
        <div class=\"small-box bg-purple\">
        <div class=\"inner\">
            <h3>$cantidad</h3>
            <p class=\"titulo-seccion\">Historial de Entregas</p>
        </div>
        <div class=\"icon\">
            <i class=\"fa fa-history\"></i>
        </div>
        <span class=\"small-box-footer\">Ver Historial <i class=\"fa fa-arrow-circle-right\"></i></span>
        </div>
        </a>
    ";
}
else if ($consulta == "pone_box_remitos"){
    $cantidad="0";
    $query="Select COUNT(id_remito) as cuenta FROM remitos";
    $val = mysqli_query($con, $query);

    if (mysqli_num_rows($val)>0){
        $r=mysqli_fetch_assoc($val);
        $cantidad = $r['cuenta'];
    }
    echo "
        <a href=\"ver_remitos.php\">
            <div class=\"small-box\" style=\"background-color:#AEB404\">
                <div class=\"inner\" style=\"color:white\">
                    <h3>$cantidad</h3>
                    <p class=\"titulo-seccion\">Remitos</p>
                </div>
                <div class=\"icon\">
                    <i class=\"fa fa-file\"></i>
                </div>
                <span class=\"small-box-footer\">Ver Remitos <i class=\"fa fa-arrow-circle-right\"></i></span>
            </div>
        </a>
    ";
}
else if ($consulta == "pone_box_mesadas"){
    $cantidad="0";
    $query="Select count(id_mesada) as total from mesadas;";
    $val = mysqli_query($con, $query);

    if (mysqli_num_rows($val)>0){
        $rf=mysqli_fetch_assoc($val);
        $cantidad=$rf['total'];
    }
    echo "
        <a href=\"ver_mesadas.php\">
            <div class=\"small-box bg-gray\">
                <div class=\"inner\">
                  <h3>$cantidad</h3>
                  <p>Mesadas</p>
                </div>
                <div class=\"icon\">
                  <i class=\"fa fa-table\"></i>
                </div>
                <span class=\"small-box-footer\">Ver Mesadas <i class=\"fa fa-arrow-circle-right\"></i></span>
            </div>
        </a>
    ";
}
else if ($consulta == "pone_box_stock") {
    $cantidad = "0";
    $query = "SELECT
    IFNULL(SUM(sb.cantidad),0) -
    IFNULL(SUM(rp.cantidad),0) as cuenta
    FROM stock_bandejas sb 
    LEFT JOIN reservas_productos rp ON rp.id_stock = sb.id_stock
    ";
    $val = mysqli_query($con, $query);

    if (mysqli_num_rows($val) > 0) {
        $r = mysqli_fetch_assoc($val);
        $cantidad = (int)$r['cuenta'] >= 0 ? $r["cuenta"] : 0;
    }

    echo "<a href=\"ver_stock.php\">
              <div class=\"small-box bg-orange\">
                <div class=\"inner\">
                  <h3>$cantidad</h3>
                  <p class=\"titulo-seccion\">Bandejas en Stock</p>
                </div>
                <div class=\"icon\">
                  <i class=\"fa fa-align-justify\"></i>
                </div>
                 <span class=\"small-box-footer\">Ver Stock <i class=\"fa fa-arrow-circle-right\"></i></span>
              </div>
              </a>";

}
