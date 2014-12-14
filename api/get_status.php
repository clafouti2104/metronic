<?php
/*
 * URL à appeler pour récupérer le status d'un device
 * http://<IP>/metronic/api/get_status.php?idDevice=<ID_DEVICE>
 */

if(!isset($_GET["idDevice"])){
    echo "ID Device manquant";
    return false;
}

include("../tools/config.php");
$GLOBALS["dbconnec"]=connectDB();
include_once "../models/Device.php";

$device= Device::getDevice($_GET["idDevice"]);
//echo utf8_decode($device->state);
echo utf8_decode($device->showState(FALSE));
?>
