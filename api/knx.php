<?php
/*
 * URL qui met à jour le status d'un device KNX
 * Paramètres
 * 		knxgroup: adresse de groupe de status
 * 		value: valeur a enregistrer
 * http://<IP>/metronic/api/knx.php?knxgroup=<KNX_GROUP>&value=<VALUE>
 */

if(!isset($_GET["knxgroup"])){
    echo "Adresse de groupx KNX manquante";
    return false;
}
if($_GET["knxgroup"]==""){
    echo "Adresse de groupx KNX manquante";
    return false;
}
if(!isset($_GET["value"])){
    echo "Valeur manquante";
    return false;
}

include("../tools/config.php");
$GLOBALS["dbconnec"]=connectDB();
include_once "../models/Device.php";

//Récupère un ou plusieurs idDevice correspondant à l'adresse de groupe donnée
$knxGroup = str_replace("-", "/", $_GET["knxgroup"]);
$sql = "SELECT id ";
$sql .= " FROM device ";
$sql .= " WHERE param1='".$knxGroup."' ";

$stmt = $GLOBALS["dbconnec"]->query($sql);
while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
	$idDevice = $row["id"];
	Device::updateState($idDevice, $_GET["value"],"NOW()");
}

?>
