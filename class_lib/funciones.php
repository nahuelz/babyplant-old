<?php

function generarBoxEstado($estado, $fullWidth){
  $w100 = "";
  if ($fullWidth == true){
    $w100 = "w-100";
  }
  if ($estado == 0){//PENDIENTE
    return "<div class='d-inline-block cajita $w100' style='background-color:#A4A4A4; padding:5px;'>PENDIENTE</div>";
    }
  else if ($estado == 1){//PLANIFICADO
    return "<div class='d-inline-block cajita $w100' style='background-color:#FFFF00; padding:5px;'><span>PLANIFICADO</span></div>";
  }
  else if ($estado == 2){//SEMBRADO
    return "<div class='d-inline-block cajita $w100' style='background-color:#74DF00; padding:5px;'>SEMBRADO</div>";
  }
  else if ($estado == 3){//ENCAMARA
    return "<div class='d-inline-block cajita $w100' style='background-color:#2E9AFE; padding:5px;'>EN CÁMARA</div>";
  }
  else if ($estado == 4){//INVERNACULO
    return "<div class='d-inline-block cajita $w100' style='background-color:#04B404; padding:5px;'>EN INVERNÁCULO</div>";
  }
  else if ($estado == 5){//PARAENTREGAR
        return "<div class='d-inline-block cajita $w100' style='text-align:center;background-color:#01DFD7; padding:3px;'><div>EN AGENDA</div></div>";
    }
    else if ($estado == 6){//ENTREGADO PARCIALMENTE
        return "<div class='d-inline-block cajita $w100' style='text-align:center;background-color:#FFFF00; padding:3px; cursor:pointer;'><div>ENTREGADO PARCIALMENTE</div></div>";
        }
    else if ($estado == 7){//ENTREGADO COMPLETAMENTE
        return "<div class='d-inline-block cajita $w100' style='text-align:center;background-color:#A9F5BC; padding:3px; cursor:pointer;'><div>ENTREGADO COMPLETAMENTE</div></div>";
    }
  else if ($estado == 8){//EN STOCK
        return "<div class='d-inline-block cajita $w100' style='word-wrap:break-word;text-align:center;background-color:#FAAC58; padding:3px;cursor:pointer;'><div>EN STOCK</div></div>";
    }
    else if ($estado == -1){//CANCELADO
        return "<div class='d-inline-block cajita $w100' style='word-wrap:break-word;text-align:center;background-color:#FA5858; padding:3px; cursor:pointer;'>CANCELADO</div>";
    }
    else{
      return "<div class='d-inline-block cajita $w100' style='background-color:#A4A4A4; padding:5px;'>NO DEFINIDO</div>";
    
    }
}


function test_input($data){
  $data = trim($data);
  $data = stripslashes($data);
  $data = htmlspecialchars($data);
  return $data;
}
?>