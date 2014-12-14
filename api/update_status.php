<?php
/*
 * URL à appeler pour mettre à jour le status d'un device
 * http://<IP>/metronic/api/update_status.php?idDevice=<ID_DEVICE>&state=<STATE>
 */

if(!isset($_GET["idDevice"])){
    echo "ID Device manquant";
    return false;
}
if(!isset($_GET["state"])){
    echo "State manquant";
    return false;
}

include("../tools/config.php");
$GLOBALS["dbconnec"]=connectDB();
include_once "../models/Device.php";

$device= Device::getDevice($_GET["idDevice"]);
if(!is_object($device)){
    echo "Device ".$_GET["idDevice"]." inconnu";
    return false;
}
Device::updateState($_GET["idDevice"], $_GET["state"],"NOW()");
?>
