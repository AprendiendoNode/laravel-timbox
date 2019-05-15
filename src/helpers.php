<?php


// Esta funcion se utiliza para obtener la zona horaria
// en base al lugar de expedición
function zonaHorariaPorCP($lugarDeExpedicion){
    $f = fopen(__DIR__ .'/../docs/cat_postal_codes.csv', "r");
    $timeZone = '';
    while ($row = fgetcsv($f)) {
        if ($row[1] == $lugarDeExpedicion) {
            $timeZone = $row[6];
            break;
        }
    }
    fclose($f);
    return $timeZone;
}