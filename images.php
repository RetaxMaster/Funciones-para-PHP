<?php

//Devuelve la extensión de la imagen, recibe el nombre o el path, realmente da igual ya que hace un explode del "." y toma el último valor -- RetaxMaster
function get_image_extension(string $image_or_path) : string {
    $extension = explode(".", $image_or_path);
    return array_pop($extension);
}

//Devuelve el resource de una imagen según su tipo, recibe el path de la imagen -- RetaxMaster
function imagecreatefromanytype($path) {
    $extension = get_image_extension($path);
    switch ($extension) {
        case 'jpg': $image = imagecreatefromjpeg($path); break;
        case 'jpeg': $image = imagecreatefromjpeg($path); break;
        case 'png': $image = imagecreatefrompng($path); break;
        case 'gif': $image = imagecreatefromgif($path); break;
    }
    return $image;
}

//Exporta una imagen según su tipo, recibe el resource de la imagen a exportar y la ruta -- RetaxMaster
function export_image($image, string $path) : void {
    $extension = get_image_extension($path);
    switch ($extension) {
        case 'jpg': $image = imagejpeg($image, $path); break;
        case 'jpeg': $image = imagejpeg($image, $path); break;
        case 'png': $image = imagepng($image, $path); break;
        case 'gif': $image = imagegif($image, $path); break;
    }
}

//Cambia el tamaño de una imagen, recibe la ruta de la imagen y el ancho al que se le quiere dar y el alto, estos dos parámetros son relativamente opcionales, pero se debe especificar al menos uno, dependiendo del tipo de relación que se le quiera dar, o ambos  si no se le dará relación, y opcionalmente decir si mantendrá la relación entre el ancho y el alto, por defecto si se mantendrá la relación, también recibe si la relación se mantendrá por el ancho, por defecto si se mantendrá, si se manda como false, la relación se hará por el alto en caso de que se haya aceptado mantener la relación -- RetaxMaster
function resize_image(string $path, int $width = 0, int $height = 0, bool $keepRelation = true, bool $relationByWidth = true) : void {
    //Abrir foto original según su extensión
    $image = imagecreatefromanytype($path);
    //Obtenemos sus medidas
    $imageWidth = imagesx($image);
    $imageHeight = imagesy($image);

    //Definimos las medidas del lienzo
    if ($keepRelation) {
        if ($relationByWidth)
            //Relación por ancho
            $height = ($width * $imageHeight) / $imageWidth;
        else
            //Relación por alto
            $width = ($height * $imageWidth) / $imageHeight;
        
    }

    //Crear lienzo vacio
    $lienzo = imagecreatetruecolor($width, $height);

    //Copiar la imagen original en el lienzo vacio
    /*
    1-2 Destino - original
    3-4 Eje X - Eje Y desde donde iniciará a pegar la imagen
    5-6- Eje X - Eje Y de donde iniciara a tomar la imagen original para pegar
    7-8 Ancho destino - Alto destino (Ancho y alto al cual se va a redimensionar la image)
    9-10 Ancho y alto original de la imagen
    */
    imagecopyresampled($lienzo, $image, 0, 0, 0, 0, $width, $height, $imageWidth, $imageHeight);

    //Exportar imagen (Reemplazamos la imagen que fue subida por la imagen que acabamos de redimensionar)
    export_image($lienzo, $path);

    //Destruimos la imagen para liberar memoria
    imagedestroy($lienzo);
    imagedestroy($image);
}

//Le pone una marca de agua a las fotos, recibe la ruta de la marca de agua (Debe ser PNG), recibe la ruta de la imagen a la que se le pondrá la marca de agua (Debe ser JPG, PNG o GIF), la opacidad (Un valor entre 0 y 100) y opcionalmente recibe si la marca de agua tiene fondo transparente que por defecto está en true -- RetaxMaster
function set_watermark(string $waterMarkPath, string $destinationPath, int $opacity = 30, bool $transparentBackground = true, bool $resize = true, int $widthToResize = 500) : bool {
    $extension = get_image_extension($destinationPath);
    $waterMark = imagecreatefrompng($waterMarkPath);

    //Antes de crear la imagen, vamos a redimensionar las imágenes para que se mantengan en el mismo tamaño
    if($resize) resize_image($destinationPath, $widthToResize);

    //Ahora si obtenemos la imagen que ya fue redimensionada
    $originalImage = imagecreatefromanytype($destinationPath);
    //Obtenemos el ancho y alto de la imagen original
    $original_width = imagesx($originalImage);
    $original_height = imagesy($originalImage);

    //Obtenermos el ancho y el alto de la marca de agua
    $watermark_width = imagesx($waterMark);
    $watermark_height = imagesy($waterMark);

    //Si la marca de agua es más grande que la imagen, arroja un error
    if ($original_width < $watermark_width || $original_height < $watermark_height) throw new Exception("La marca de agua es más grande que la imagen. Dimensiones de la imagen: Alto: $original_height, Ancho: $watermark_width, Dimensiones de la marca de agua: Alto: $watermark_height, Ancho: $watermark_height");
    

    //Punto en donde va a iniciar la imagen
    //Primero calculamos el centro de la imagen de la original
    $original_center_x = $original_width/2;
    $original_center_y = $original_height/2;

    //Luego calculamos el centro de la marca de agua
    $mark_center_x = $watermark_width/2;
    $mark_center_y = $watermark_height/2;

    //Ahora restamos del centro de la imagen original menos el centro de la marca de agua y así lo tenemos centrado
    $start_point_x = $original_center_x - $mark_center_x;
    $start_point_y = $original_center_y - $mark_center_y;

    //Establece el fondo de la marca de agua transparente (Toma el primer pixel, y todos los pixeles con ese color los pone transparentes)
    if($transparentBackground) imagecolortransparent($waterMark,imagecolorat($waterMark,0,0));

    //Fusionamos la imagen con la marca de agua (Imagen original, Marca de agua, punto en x el que empezará con respecto a la imagen original, punto en y el que empezará con respecto a la imagen original, punto en x donde iniciara con respecto a la marca de agua, punto en y donde iniciara con respecto a la marca de agua, opacidad)
    imagecopymerge($originalImage, $waterMark, $start_point_x, $start_point_y, 0, 0, $watermark_width, $watermark_height, $opacity);

    //Creamos la imagen
    export_image($originalImage, $destinationPath);

    //Destruimos la imagen para liberar memoria
    imagedestroy($originalImage);
    imagedestroy($waterMark);
    return true;
}

?>