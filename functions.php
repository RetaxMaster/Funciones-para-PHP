<?php

//Genera un string aleatorio, recibe la longitud del string que se desea -- RetaxMaster
function random_string(int $lenght) : string {
    $string_base = "ABCDEFGHIJLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789";
    $random_string = "";
    $string_base_lenght = strlen($string_base);

    for ($i=0; $i < $lenght ; $i++) {
      $random = mt_rand(0, $string_base_lenght-1);
      $random_string .= substr($string_base, $random, 1);
    }

    return $random_string;
}

//Sanea cualquier string, recibe dos parámetros, el string a sanear y el tipo de saneamiento -- RetaxMaster
function filter_string(string $string, string $type) : string {
    switch ($type) {
        case 'string':
            $sanitized = filter_var(trim($string), FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_LOW);
            break;

        case 'keep_html_characters':
            $sanitized = htmlentities($string);
            break;
        
        case 'remove_special_chars_low':
            $sanitized = preg_replace("/[^A-Za-z0-9á-úÁ-Ú., ?!.:;]/", "", $string);
            break;

        case 'remove_special_chars_medium':
            $sanitized = preg_replace("/[^A-Za-z0-9á-úÁ-Ú., ?!]/", "", $string);
            break;

        case 'remove_special_chars_high':
            $sanitized = preg_replace("/[^A-Za-z0-9á-úÁ-Ú ]|¿|¡/", "", $string);
            break;

        case 'keep_only_words':
            $sanitized = preg_replace("/\d/", "", $string);
            break;
        
        case 'keep_only_numbers':
            $sanitized = preg_replace("/\D/", "", $string);
            break;

        case 'email':
            $sanitized = filter_var(trim($string), FILTER_SANITIZE_EMAIL, FILTER_FLAG_STRIP_HIGH);
            break;
        
        default:
            $sanitized = trim($string);
            break;
    }
    return $sanitized;
}

//Valida cualquier string -- RetaxMaster
function validate_string(string $string, string $type) : bool {
    switch ($type) {
        case 'email':
            $validated = filter_var($string, FILTER_VALIDATE_EMAIL);
            break;

        case 'float':
            $validated = filter_var($string, FILTER_VALIDATE_FLOAT, FILTER_FLAG_ALLOW_THOUSAND);
            break;

        case 'int':
            $validated = ctype_digit($string);
            break;

        case 'ip':
            $validated = filter_var($string, FILTER_VALIDATE_IP);
            break;

        case 'url':
            $validated = filter_var($string, FILTER_VALIDATE_URL);
            break;
        
        default:
            $validated = false;
            break;
    }
    return $validated;
}

//Remueve acentos y "ñ", recibe el string de entrada -- RetaxMaster
function remove_accent(string $string) : string {
    $string = str_replace(
        array('á', 'à', 'ä', 'â', 'ª', 'Á', 'À', 'Â', 'Ä'),
        array('a', 'a', 'a', 'a', 'a', 'A', 'A', 'A', 'A'),
        $string
    );
    $string = str_replace(
        array('é', 'è', 'ë', 'ê', 'É', 'È', 'Ê', 'Ë'),
        array('e', 'e', 'e', 'e', 'E', 'E', 'E', 'E'),
        $string
    );
    $string = str_replace(
        array('í', 'ì', 'ï', 'î', 'Í', 'Ì', 'Ï', 'Î'),
        array('i', 'i', 'i', 'i', 'I', 'I', 'I', 'I'),
        $string
    );
    $string = str_replace(
        array('ó', 'ò', 'ö', 'ô', 'Ó', 'Ò', 'Ö', 'Ô'),
        array('o', 'o', 'o', 'o', 'O', 'O', 'O', 'O'),
        $string
    );
    $string = str_replace(
        array('ú', 'ù', 'ü', 'û', 'Ú', 'Ù', 'Û', 'Ü'),
        array('u', 'u', 'u', 'u', 'U', 'U', 'U', 'U'),
        $string
    );
    $string = str_replace(
        array('ñ', 'Ñ', 'ç', 'Ç'),
        array('n', 'N', 'c', 'C',),
        $string
    );

    return $string;
}

//Convierte un número en formato de moneda -- RetaxMaster
function parse_money(int $money) : string {
    return "$".number_format((float) $money, 2, '.', ',');
}

//Añade ceros a la izquierda -- RetaxMaster
function add_left_zeros(string $text, int $lenght) : string {
    return str_pad($text, $lenght, "0", STR_PAD_LEFT);
}

//Quita los ceros a la izquierda -- RetaxMaster
function remove_left_zeros(string $text) : string {
    return ltrim($text, "0");
}

//Obtiene la URL directorio raíz -- RetaxMaster
function get_url_root_dir() {
    $http = isset($_SERVER['HTTPS']) ? "https://" : "http://";
    echo "<pre>";
    var_dump(realpath($_SERVER['DOCUMENT_ROOT']));
    echo "<br>";
    var_dump(getcwd());
    echo "</pre>";
    $root = str_replace(realpath($_SERVER['DOCUMENT_ROOT']), "", getcwd());
    echo "<pre>";
    var_dump($root);
    echo "</pre>";
    $root = str_replace("\\", "/", $root);
    return $http.$_SERVER['HTTP_HOST'].$root;
}

//Convierte un string a formato URL Amigable -- RetaxMaster
function convert_string_to_url(string $word) : string {
    $wordCleaned = filter_string($word, "remove_special_chars_high");
    $wordCleaned = implode("-", explode(" ", filter_string($wordCleaned, "string")));
    $wordCleaned = remove_accent($wordCleaned);
    $wordCleaned = mb_strtolower($wordCleaned);
    return $wordCleaned;
}

//Capitaliza las palabras -- RetaxMaster
function capitalize(string $word) : string {
    return ucfirst(filter_string($word, "string"));
}

//Quita ceros o valores vacios de un array -- RetaxMaster
function remove_empty_values_from_array(array $array) : array {
    return array_values(array_filter($array, function($item){
        return $item != 0 || $item != "";
    }));
}

//Simplifica un array obtenido en un fetchAll en base a un índice dado (Puede ser un array de arrays) -- RetaxMaster
function simplify_array(array $array, string $index) : array {
    $newArray = array();
    foreach ($array as $item)
        array_push($newArray, $item[$index]);
    return $newArray;

    /* Ejemplo 

    $array = array(
        array (
            "id" => 1,
            "name" => "Aguascalientes"
        ),
        array (
            "id" => 1,
            "name" => "Baja California"
        )
    );

    Salida:
    $array = array("Aguascalientes", "Baja California");
    
    */
}

//Filtra el contenido de un array a formato URL (Debe ser un array ya simplificado de strings) -- RetaxMaster
function filter_array_url(array $array) : array {
    $newArray = array();
    foreach ($array as $item)
        array_push($newArray, convert_string_to_url($item));
    return $newArray;
    
    /* Ejemplo 

    $array = array("Aguascalientes", "Baja California");

    Salida:
    $array = array("aguascalientes", "baja-california");
    
    */
}

//Restaura las palabras de la URL, recibe un arreglo a comparar como parámetro opcional, esto en en el caso de que, existan elementos en la URL que sea dificil restaurar, por ejemplo, las palabras con acentos, si se tiene una base, se puede comparar la palabra sin acento con alguna de las palabras con acento aplicando un poco de ingenieria inversa, devuelve una cadena vacía si no lo encuentra -- RetaxMaster
function restore_url_value(string $word, array $arrayToCompare = []) : string {
    //Si envió un arreglo para hacer la comparación...
    if (count($arrayToCompare) > 0) {
        //Primero creo un arreglo de todas las posibles palabras que puede contener (Al enviar el arreglo, se da por hecho de que la palabra si existe, solo hay que encontrarla) Por ello es que se transforma a cómo se vería en la URL

        $posibleWords = filter_array_url($arrayToCompare);
        
        //En este punto, la posible palabra está dentro del arreglo $posibleWords, así que toca buscar su índice

        $index = array_search($word, $posibleWords);
        
        //En este punto ya encontré la palabra, así que solo queda retornar la palabra traducida de la siguiente forma:
            
        $word = ((string) $index != "") ? $arrayToCompare[$index] : "";

        /* Explicación con ejemplo:
        Quiero obtener un estado, en este caso, sería ciudad-de-mexico:
        $word = "ciudad-de-mexico";

        Entonces, como si tengo con qué comparar, se lo envío, en el caso de la linea 33 puede apreciarse como le envío el arreglo de los estados, porque yo se que ahí dentro va a estar "Ciudad de México" ya que me carga TODOS los estados del país

        Lo que hago es crear un arreglo con todas las posibles coincidencias (Hago un arreglo que contiene todas los estados como si fueran a ser puestos en la URL)

        Ahora tengo dos arreglos, uno con las palabras originales y otro con las palabras convertidas a URL, ambos con las mismas palabras en la misma posición, así que solo resta encontrar la posición de la palabra a buscar dentro de el arreglo que se creo que tiene todas las URL's convertidas

        array_search() Me devuelve el índice de esa palabra, y como ambos arreglos tienen las misma posiciones, simplemente devuelvo el arreglo original en el índice encontrado
        
        */
        
    }
    else {
        $word = implode(" ", explode("-", $word));
        $word = capitalize($word);
    }
    return $word;
}

//Envía un email
function send_mail(string $to, string $subject, string $message, string $name, string $email) : bool {
    try {
        $headers = "MIME-Version: 1.0\r\n";
        $headers .= "Content-type: text/html; charset=utf-8\r\n";
        $headers .= "From: $name < $email >\r\n";
        return mail($to, $subject, $message, $headers);
    } catch (Exception $e){
        return false;
    }
}

//Añade los espacios de la URL(%20) -- RetaxMaster
function add_url_spaces(string $text) : string {
    return implode("%20", explode(" ", $text));
}

//Encripta una cadena de texto -- RetaxMaster
function encrypt(string $string) : string {
    return hash("sha512", $string);
}

//Quita todos los espacios de una cadena
function remove_spaces(string $string) : string {
    return implode("", explode(" ", trim($string)));
}

//Envía una cURL -- RetaxMaster
function send_curl(string $url, array $params, string $contentType) : string {
    $ch = curl_init();

    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($params));
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
    
    $headers = array("Content-Type: $contentType");
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

    $result = curl_exec($ch);
    if (curl_errno($ch)) {
        return 'Error:' . curl_error($ch);
    }
    curl_close ($ch);
    return $result;
}

//Obtiene la IP actual con la cual está logueado el usuario -- RetaxMaster
function get_actual_ip() : string {
    return $_SERVER['HTTP_CLIENT_IP'] ?? $_SERVER['HTTP_X_FORWARDED_FOR'] ?? $_SERVER['HTTP_X_FORWARDED'] ?? $_SERVER['HTTP_FORWARDED_FOR'] ?? $_SERVER['HTTP_FORWARDED'] ?? $_SERVER['REMOTE_ADDR'] ?? "UNKNOW";
}

if (file_exists("date.php")) require("date.php");
if (file_exists("images.php")) require("images.php");

?>