<?php
/*
 * RAZ des status si last_update > x pour les devices à historiser
 */
include("../tools/config.php");

$GLOBALS["dbconnec"] = connectDB();
include "../models/Device.php";
include_once "../models/Log.php";

//Récupère les devices pour lesquels une alerte de perte de communication a été renseigné
$sqlDevices="SELECT id,collect,last_update FROM device WHERE ";
$sqlDevices.=" active=1 AND collect IS NOT NULL AND collect > 0";
$stmt = $GLOBALS["dbconnec"]->prepare($sqlDevices);
$stmt->execute(array());

$sqlUpdate="";
$devices = array();
while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    $lastUpdate = new DateTime($row["last_update"]);
    $now = new DateTime("now");
    $interval = ($row["collect"] + 2) * 60;
    //On vérifie que la date de dernière récupération est antérieure
    if($lastUpdate->format('U') + $interval < $now->format('U')){
        $sqlUpdate.="UPDATE device SET ";
        $sqlUpdate.=" last_update=NULL,  ";
        $sqlUpdate.=" status=NULL  ";
        $sqlUpdate.=" WHERE id=".$row["id"].";";
    }
}

if($sqlUpdate != ""){
    $stmt = $GLOBALS["dbconnec"]->prepare($sqlUpdate);
    $stmt->execute(array());
}

?>