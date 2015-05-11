<?php
/*
 * Eteint les lumières allumées si l'alarme est en service
 */
include("../tools/config.php");
include("../models/Device.php");

$GLOBALS["dbconnec"] = connectDB();

//Device alarme
$device=Device::getDevice(21);
//Si l'alarme n'est pas en service on arrête
if($device->state != "armed"){
    exit;
}

//Vérifie s'il y a des lumières allumées
$query = "SELECT COUNT(id) ";
$query .= " FROM device ";
$query .= " WHERE LOWER(type)='light' AND LOWER(state)='on' ";
$nbrDevice = $GLOBALS["dbconnec"]->query($query)->fetchColumn();

if($nbrDevice == 0){
    exit;
}

//Récupération état alarme
$ini = parse_ini_file("/var/www/metronic/tools/parameters.ini");
$content = "[parameters]";
foreach($ini as $title => $value){
    if($title == "myfox_token"){
        $token=$value;
        break;
    }
}
if($token == ""){
    $token=getToken();
}

//Extinction de ttes les lumières si MES
$response=exec("curl https://api.myfox.me:443/v2/site/10562/scenario/42429/play?access_token=".$token);

?>
