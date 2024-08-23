<?php
	if (isset($_FILES["file"])){
		$file = $_FILES["file"];
		$id_art = $_POST["id_artpedido"];
		$nombre = $file["name"];
		$tipo = $file["type"];
		$ruta_provisional = $file["tmp_name"];
		$size = $file["size"];
		$dimensiones = getimagesize($ruta_provisional);
		$width = $dimensiones[0];
		$height = $dimensiones[1];
		$carpeta = "imagenes/";

		if ($tipo != "image/jpg" && $tipo != "image/jpeg" && $tipo != "image/png"){
			echo "Error, el archivo no es una imagen";
		}
		else{
			$src = $carpeta.$id_art.".jpg";
			$imgData = resize_image($ruta_provisional, 300, 300);
		
			imagejpeg($imgData, $src, 70);

			chmod($src, 0666);
			echo "LA FOTO SE SUBIÓ CORRECTAMENTE";
		}

	}

   function resize_image($file, $w, $h, $crop=false) {
    list($width, $height) = getimagesize($file);
    $r = $width / $height;
    
        if ($w/$h > $r) {
            $newwidth = $h*$r;
            $newheight = $h;
        } else {
            $newheight = $w/$r;
            $newwidth = $w;
        }
    
    
    //Get file extension
    $exploding = explode(".",$file);
    $ext = end($exploding);
    
    switch($ext){
        case "png":
            $src = imagecreatefrompng($file);
        break;
        case "jpeg":
        case "jpg":
            $src = imagecreatefromjpeg($file);
        break;
        case "gif":
            $src = imagecreatefromgif($file);
        break;
        default:
            $src = imagecreatefromjpeg($file);
        break;
    }
    
    $dst = imagecreatetruecolor($width*0.65, $height*0.65);
    imagecopyresampled($dst, $src, 0, 0, 0, 0, $width*0.65, $height*0.65, $width, $height);

    return $dst;
}
?>