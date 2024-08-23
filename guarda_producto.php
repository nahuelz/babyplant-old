<?php
include("class_lib/sesionSecurity.php");
require('class_lib/class_conecta_mysql.php');
require('class_lib/funciones.php');
$con = mysqli_connect($host, $user, $password,$dbname);
if (!$con) {
  die("Connection failed: " . mysqli_connect_error());
}
$tipo = $_POST['tipo'];
$nombre = $_POST['nombre'];
$subtipoart = $_POST['subtipoart'];
$id_tipo = $_POST['id_tipo'];
$listaprecios = json_decode($_POST['precios'], true); 
$id_variedad = $_POST['id_variedad'];

mysqli_query($con,"SET NAMES 'utf8'");
if ($tipo == "agregar" || $tipo == "editar"){
  $cadenaselect="SELECT * FROM variedades_producto WHERE eliminado IS NULL AND nombre = UPPER('$nombre') LIMIT 1;";
  $val = mysqli_query($con,$cadenaselect);
  $puede = true;

  if (mysqli_num_rows($val)>0){
    $row = mysqli_fetch_assoc($val);

    if ($tipo == "agregar"){
      echo "Ya existe una variedad asi";  
    }
    else if ($tipo == "editar" && $row["id_articulo"] != $id_variedad){
      echo "Ya existe una variedad asi";  
      $puede = false;
    }
  }
  if ($puede == true){
    $rowLength = count($listaprecios);
    $precio_288 = "NULL";
    $precio_200 = "NULL";
    $precio_162 = "NULL"; 
    $precio_128 = "NULL";
    $precio_72 = "NULL";
    $precio_50 = "NULL"; 
    $precio_25 = "NULL";
    $precio_49 = "NULL";

    $precio_288_s = "NULL";
    $precio_200_s = "NULL";
    $precio_162_s = "NULL"; 
    $precio_128_s = "NULL";
    $precio_72_s = "NULL";
    $precio_50_s = "NULL"; 
    $precio_25_s = "NULL";
    $precio_49_s = "NULL";
    
    for ($i = 0; $i < $rowLength; $i++){
      $bandeja = $listaprecios[$i]["bandeja"];
      $precios = $listaprecios[$i]["precio"];
      $precios_semilla = $listaprecios[$i]["precio_semilla"];

      if ($bandeja == "288"){
        if ($precios != NULL && (int)$precios > 0){
          $precio_288 = (int)$precios;    
        }
        if ($precios_semilla != NULL && (int)$precios_semilla > 0){
          $precio_288_s = (int)$precios_semilla;  
        }
      }  
      else if ($bandeja == "200"){
        if ($precios != NULL && (int)$precios > 0){
          $precio_200 = (int)$precios;    
        }
        if ($precios_semilla != NULL && (int)$precios_semilla > 0){
          $precio_200_s = (int)$precios_semilla;  
        }
      }
      else if ($bandeja == "162"){
        if ($precios != NULL && (int)$precios > 0){
          $precio_162 = (int)$precios;    
        }
        if ($precios_semilla != NULL && (int)$precios_semilla > 0){
          $precio_162_s = (int)$precios_semilla;  
        }
      }
      else if ($bandeja == "128"){
        if ($precios != NULL && (int)$precios > 0){
          $precio_128 = (int)$precios;    
        }
        if ($precios_semilla != NULL && (int)$precios_semilla > 0){
          $precio_128_s = (int)$precios_semilla;  
        }
      }
      else if ($bandeja == "72"){
        if ($precios != NULL && (int)$precios > 0){
          $precio_72 = (int)$precios;    
        }
        if ($precios_semilla != NULL && (int)$precios_semilla > 0){
          $precio_72_s = (int)$precios_semilla;  
        }
      }
      else if ($bandeja == "50"){
        if ($precios != NULL && (int)$precios > 0){
          $precio_50 = (int)$precios;    
        }
        if ($precios_semilla != NULL && (int)$precios_semilla > 0){
          $precio_50_s = (int)$precios_semilla;  
        }
      }
      else if ($bandeja == "25"){
        if ($precios != NULL && (int)$precios > 0){
          $precio_25 = (int)$precios;    
        }
        if ($precios_semilla != NULL && (int)$precios_semilla > 0){
          $precio_25_s = (int)$precios_semilla;  
        }
      }
      else if ($bandeja == "49"){
        if ($precios != NULL && (int)$precios > 0){
          $precio_49 = (int)$precios;    
        }
        if ($precios_semilla != NULL && (int)$precios_semilla > 0){
          $precio_49_s = (int)$precios_semilla;  
        }
      }
    }


    if ($tipo == "agregar"){
      $cadena2 = "INSERT INTO variedades_producto (nombre, id_subtipo, 
      precio_288, precio_200, precio_162, precio_128, precio_72, precio_50, precio_25, precio_49,
      precio_288_s, precio_200_s, precio_162_s, 
      precio_128_s, precio_72_s, precio_50_s, precio_25_s, precio_49_s) VALUES (UPPER('$nombre'), '$subtipoart', $precio_288, $precio_200, $precio_162, $precio_128, $precio_72, $precio_50, $precio_25, $precio_49, $precio_288_s, $precio_200_s, $precio_162_s, $precio_128_s, $precio_72_s, $precio_50_s, $precio_25_s, $precio_49_s)";
    }
    else if ($tipo == "editar"){
      

      $cadena2 = "UPDATE variedades_producto SET nombre = UPPER('$nombre'), precio_288 = $precio_288, precio_200 = $precio_200, precio_162 = $precio_162, precio_128 = $precio_128, precio_72 = $precio_72, precio_50 = $precio_50, precio_25 = $precio_25, precio_49 = $precio_49,
      precio_288_s = $precio_288_s, precio_200_s = $precio_200_s, precio_162_s = $precio_162_s, 
      precio_128_s = $precio_128_s, precio_72_s = $precio_72_s, precio_50_s = $precio_50_s, precio_25_s = $precio_25_s, precio_49_s = $precio_49_s WHERE id_articulo = $id_variedad";
      
    }

      
    mysqli_query($con, $cadena2);
  }

}

else if ($tipo == "agregar_tipo"){
  $tipos_bandeja = json_decode($_POST['tipos_bandeja'], true); 
  $precios = json_decode($_POST['listaprecios'], true); 
  $precios_semilla = json_decode($_POST['listaprecios_semilla'], true); 
  
  
  $rowLength = count($tipos_bandeja);
  $precio_288 = "NULL";
  $precio_200 = "NULL";
  $precio_162 = "NULL"; 
  $precio_128 = "NULL";
  $precio_72 = "NULL";
  $precio_50 = "NULL"; 
  $precio_25 = "NULL";
  $precio_49 = "NULL";

  $precio_288_s = "NULL";
  $precio_200_s = "NULL";
  $precio_162_s = "NULL"; 
  $precio_128_s = "NULL";
  $precio_72_s = "NULL";
  $precio_50_s = "NULL"; 
  $precio_25_s = "NULL";
  $precio_49_s = "NULL";
  
  for ($i = 0; $i < $rowLength; $i++){
    $bandeja = $tipos_bandeja[$i];
    if ($bandeja == "288"){
      $precio_288 = (int)$precios[$i];  
      $precio_288_s = (int)$precios_semilla[$i];  
    }  
    else if ($bandeja == "200"){
      $precio_200 = (int)$precios[$i];  
      $precio_200_s = (int)$precios_semilla[$i];  
    }
    else if ($bandeja == "162"){
      $precio_162 = (int)$precios[$i];  
      $precio_162_s = (int)$precios_semilla[$i];  
    }
    else if ($bandeja == "128"){
      $precio_128 = (int)$precios[$i];  
      $precio_128_s = (int)$precios_semilla[$i];  
    }
    else if ($bandeja == "72"){
      $precio_72 = (int)$precios[$i];  
      $precio_72_s = (int)$precios_semilla[$i];  
    }
    else if ($bandeja == "50"){
      $precio_50 = (int)$precios[$i];  
      $precio_50_s = (int)$precios_semilla[$i];  
    }
    else if ($bandeja == "25"){
      $precio_25 = (int)$precios[$i];  
      $precio_25_s = (int)$precios_semilla[$i];  
    }
    else if ($bandeja == "49"){
      $precio_49 = (int)$precios[$i];  
      $precio_49_s = (int)$precios_semilla[$i];  
    }
  }

  $cadenaselect="SELECT * FROM tipos_producto WHERE nombre = UPPER('$nombre');";
  $val = mysqli_query($con,$cadenaselect);
  if (mysqli_num_rows($val)>0){
    echo "Ya existe un tipo de producto con ese nombre!";
  }
  else{
    $cant_dias = $_POST['cant_dias'];
    $query = "INSERT INTO tipos_producto (nombre, dias_en_camara, precio_288, precio_200, precio_162, precio_128, precio_72, precio_50, precio_25, precio_49,
      precio_288_s, precio_200_s, precio_162_s, 
      precio_128_s, precio_72_s, precio_50_s, precio_25_s, precio_49_s
    ) VALUES (UPPER('$nombre'), $cant_dias,
      $precio_288, $precio_200, $precio_162, $precio_128, $precio_72, $precio_50, $precio_25, $precio_49, $precio_288_s, $precio_200_s, $precio_162_s, $precio_128_s, $precio_72_s, $precio_50_s, $precio_25_s, $precio_49_s);";
    mysqli_query($con, $query);
  }
}

else if ($tipo == "editar_tipo"){
  $id_tipo = $_POST['global_id_variedad'];
  $cant_dias = $_POST['cant_dias'];

  $tipos_bandeja = json_decode($_POST['tipos_bandeja'], true); 

  $precios = json_decode($_POST['listaprecios'], true); 
  $precios_semilla = json_decode($_POST['listaprecios_semilla'], true); 


  $rowLength = count($tipos_bandeja);
  
  $precio_288 = "NULL";
  $precio_200 = "NULL";
  $precio_162 = "NULL"; 
  $precio_128 = "NULL";
  $precio_72 = "NULL";
  $precio_50 = "NULL"; 
  $precio_25 = "NULL";
  $precio_49 = "NULL";
  
  $precio_288_s = "NULL";
  $precio_200_s = "NULL";
  $precio_162_s = "NULL"; 
  $precio_128_s = "NULL";
  $precio_72_s = "NULL";
  $precio_50_s = "NULL"; 
  $precio_25_s = "NULL";
  $precio_49_s = "NULL";

  for ($i = 0; $i < $rowLength; $i++){
    $bandeja = $tipos_bandeja[$i];
    if ($bandeja == "288"){
      $precio_288 = (int)$precios[$i];  
      $precio_288_s = (int)$precios_semilla[$i];  
    }  
    else if ($bandeja == "200"){
      $precio_200 = (int)$precios[$i];  
      $precio_200_s = (int)$precios_semilla[$i];  
    }
    else if ($bandeja == "162"){
      $precio_162 = (int)$precios[$i];  
      $precio_162_s = (int)$precios_semilla[$i];  
    }
    else if ($bandeja == "128"){
      $precio_128 = (int)$precios[$i];  
      $precio_128_s = (int)$precios_semilla[$i];  
    }
    else if ($bandeja == "72"){
      $precio_72 = (int)$precios[$i];  
      $precio_72_s = (int)$precios_semilla[$i];  
    }
    else if ($bandeja == "50"){
      $precio_50 = (int)$precios[$i];  
      $precio_50_s = (int)$precios_semilla[$i];  
    }
    else if ($bandeja == "25"){
      $precio_25 = (int)$precios[$i];  
      $precio_25_s = (int)$precios_semilla[$i];  
    }
    else if ($bandeja == "49"){
      $precio_49 = (int)$precios[$i];  
      $precio_49_s = (int)$precios_semilla[$i];  
    }
  }




  $query = "UPDATE tipos_producto SET nombre = UPPER('$nombre'), dias_en_camara = $cant_dias,
  precio_288 = $precio_288, precio_200 = $precio_200, precio_162 = $precio_162, precio_128 = $precio_128,
  precio_72 = $precio_72, precio_50 = $precio_50, precio_25 = $precio_25, precio_49 = $precio_49,
  precio_288_s = $precio_288_s, precio_200_s = $precio_200_s, precio_162_s = $precio_162_s, precio_128_s = $precio_128_s,
  precio_72_s = $precio_72_s, precio_50_s = $precio_50_s, precio_25_s = $precio_25_s, precio_49_s = $precio_49_s
   WHERE id_articulo = $id_tipo";
  mysqli_query($con, $query);
}


else if ($tipo == "agregar_subtipo"){
  $cadenaselect="SELECT * FROM subtipos_producto WHERE nombre = UPPER('$nombre');";
  $val = mysqli_query($con,$cadenaselect);
  if (mysqli_num_rows($val)>0){
    echo "Ya existe un subtipo de producto con ese nombre!";
  }
  else{
    $query = "INSERT INTO subtipos_producto (nombre, id_tipo) VALUES (UPPER('$nombre'), $id_tipo);";
    if (mysqli_query($con, $query)){
      echo "success";
    }
    else{
      print_r(mysqli_error($con));
    }
  }
}
else if ($tipo == "editar_subtipo"){
  $id_tipo = $_POST['global_id_variedad'];
  $query = "UPDATE subtipos_producto SET nombre = UPPER('$nombre') WHERE id_articulo = $id_tipo";
  mysqli_query($con, $query);
}


?>






