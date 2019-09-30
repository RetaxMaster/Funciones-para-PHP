<?php

//Transforma a una fecha legible un timestamp - RetaxMaster
function get_date_from_timestamp(string $timestamp) : string {
    $timestamp = substr($timestamp, 0, 10);
    $numeroDia = date('d', strtotime($timestamp));
    $dia = date('l', strtotime($timestamp));
    $mes = date('F', strtotime($timestamp));
    $anio = date('Y', strtotime($timestamp));
    $dias_ES = array("Lunes", "Martes", "Miércoles", "Jueves", "Viernes", "Sábado", "Domingo");
    $dias_EN = array("Monday", "Tuesday", "Wednesday", "Thursday", "Friday", "Saturday", "Sunday");
    $nombredia = str_replace($dias_EN, $dias_ES, $dia);
    $meses_ES = array("Enero", "Febrero", "Marzo", "Abril", "Mayo", "Junio", "Julio", "Agosto", "Septiembre", "Octubre", "Noviembre", "Diciembre");
    $meses_EN = array("January", "February", "March", "April", "May", "June", "July", "August", "September", "October", "November", "December");
    $nombreMes = str_replace($meses_EN, $meses_ES, $mes);
    return $nombredia." ".$numeroDia." de ".$nombreMes." del ".$anio;
}

//Transforma un timestamp en una fecha corta: dd/mm/yyyy - RetaxMaster
function get_short_date_from_timestamp(string $timestamp) : string {
    return date("d/m/Y", strtotime($timestamp));
}

//Añade puntos a am y pm retornando a.m o p.m - RetaxMaster
function add_dots_to_hour(string $hour) : string {
    return str_replace("pm", "p.m", str_replace("am", "a.m", $hour));
}

//Transforma a una hora legible un timestamp - RetaxMaster
function get_time_from_timestamp(string $time) : string {
    return add_dots_to_hour(date("g:ia", strtotime($time)));
}

//Obtiene el día de la semana según su numero - RetaxMaster
function get_day_of_the_week(int $day) : string {
    switch ($day) {
        case 1: $name = "Lunes"; break;
        case 2: $name = "Martes"; break;
        case 3: $name = "Miércoles"; break;
        case 4: $name = "Jueves"; break;
        case 5: $name = "Viernes"; break;
        case 6: $name = "Sábado"; break;
        case 7: $name = "Domingo"; break;
        default: $name = "Undefined";
    }
    return $name;
}

//Traduce el tiempo a español o ingles según se especifique, por ejemplo: "1 día" a inglés da: "1 day" y viceversa - RetaxMaster
function translate_time(string $time, bool $toSpanish) : string {
    //Separo el numero del texto
    $time = explode(" ", $time);
    $number = $time[0];
    $string = $time[1];
    //Creamos los arays con sus traducciones
    $es = array("segundo", "minuto", "hora", "día", "semana", "mes", "año", "segundos", "minutos", "horas", "días", "semanas", "meses", "años");
    $en = array("second", "minute", "hour", "day", "week", "month", "year", "seconds", "minutes", "hours", "days", "weeks", "months", "years");
    //Lo traducimos y retornamos la palabra traducida segun el idioma especificado
    $string = ($toSpanish) ? $es[array_search($string, $en)] : $en[array_search($string, $es)];
    return $number." ".$string;
}

//Añade la cantidad de tiempo indicada a un timestamp, si indicas "now" tomará la fecha actual, como segundo parámetro recibe la cantidad de tiempo a añadir en español, por ejemplo, "1 año", "7 dias", "4 semanas", etc... - RetaxMaster
function add_time(string $timestamp, string $timeToAdd) : string {
    $timestamp = ($timestamp == "now") ? date("Y-m-d  H:i:s") : $timestamp;

    $timeToAdd = explode(" + ", $timeToAdd);
    foreach ($timeToAdd as $key => $value) $timeToAdd[$key] = translate_time($value, false);
    $time = implode(" + ", $timeToAdd);

    $date = new DateTime($timestamp);
    $date->add(DateInterval::createFromDateString($time));
    return $date->format('Y-m-d H:i:s');
}

//Resta dos fechas en formato tiemstamp y retorna un DateInterval - RetaxMaster
function time_diff(string $timestamp1, string $timestamp2) : DateInterval {
    $timestamp1 = ($timestamp1 == "now") ? date("Y-m-d  H:i:s") : $timestamp1;
    $timestamp2 = ($timestamp2 == "now") ? date("Y-m-d  H:i:s") : $timestamp2;
    $date1 = new DateTime($timestamp1);
    $date2 = new DateTime($timestamp2);
    $interval = $date1->diff($date2);
    return $interval;//->format("%a días");
}

//Retorna la diferencia entre 2 horas
function hour_diff(string $h1, string $h2, bool $enable_distant_hours = true) : int {
    $hour1 = new DateTime($h1);
    $hour2 = new DateTime($h2);
    $diff = $hour1->diff($hour2);
    $diff = (int) $diff->format("%R%H");
    //Si sale un número negativo es porque se está obteniendo la diferencia entre horas distantes, ej: entre las 4 de la mañana y las 1 de la mañana
    $diff = ($diff < 0 && $enable_distant_hours) ? $diff + 24 : $diff;
    return $diff;
}

//Retorna si una hora dada está entre dos horas, recibe la hora inferior, la superior y la hora que se verificará - RetaxMaster
function hour_is_between(string $from, string $to, string $input) : bool {
    $dateFrom = DateTime::createFromFormat('!H:i:s', $from);
    $dateTo = DateTime::createFromFormat('!H:i:s', $to);
    $dateInput = DateTime::createFromFormat('!H:i:s', $input);
    if ($dateFrom > $dateTo) $dateTo->modify('+1 day');
    return ($dateFrom <= $dateInput && $dateInput <= $dateTo) || ($dateFrom <= $dateInput->modify('+1 day') && $dateInput <= $dateTo);
}

//Retorna si una fecha está entre dos fechas, recibe la hora inferior, la superior y la fecha que se verificará - RetaxMaster
function date_is_between(string $from, string $to, string $input) : bool {
    return strtotime($from) <= strtotime($input) && strtotime($input) <= strtotime($to);
}

//Obtiene una fecha entera a partir de un timestamp - RetaxMaster
function get_full_date(string $timestamp) : string {
    return get_date_from_timestamp($timestamp)." a las ".get_time_from_timestamp($timestamp);
}

//Convierte Timestamp de JavaScript a Timestamp de PHP -- RetaxMaster

function convert_javascript_timestamp_to_php_timestamp(string $timestamp) {
    $timestamp = substr($timestamp, 0, -1);
    return explode(".", implode(" ", explode("T", $timestamp)))[0];
}

//Obtiene una fecha entera a partir de un timestamp de JavaScript - RetaxMaster
function get_full_date_from_javascript_timestamp(string $timestamp) : string{
    $timestamp = convert_javascript_timestamp_to_php_timestamp($timestamp);
    return get_full_date($timestamp);
}

?>