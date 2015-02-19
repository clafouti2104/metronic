<?php
include("../tools/config.php");

$GLOBALS["dbconnec"] = connectDB();
include "../models/Device.php";
include_once "../models/Log.php";

//Récupération de la durée de rappel d'alerte en jours
$alertRecall=1;
$sqlRecall="SELECT value FROM config WHERE name='alert_recall'";
$stmt = $GLOBALS["dbconnec"]->prepare($sqlRecall);
$stmt->execute(array());
if($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    $alertRecall=$row["value"];
}

//Récupère les devices pour lesquels une alerte de perte de communication a été renseigné
$sqlDevices="SELECT id,alert_lost_communication,last_alert,last_update FROM device WHERE ";
$sqlDevices.=" active=1 AND alert_lost_communication IS NOT NULL";
$stmt = $GLOBALS["dbconnec"]->prepare($sqlDevices);
$stmt->execute(array());

$sqlUpdate="";
$devices = array();
while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    $lastUpdate = new DateTime($row["last_update"]);
    $lastAlert = new DateTime($row["last_alert"]);
    $now = new DateTime("now");
    $interval = $row["alert_lost_communication"] * 60;
    //On vérifie que la date de dernière récupération est antérieure
    if($lastUpdate->format('U') + $interval >= $now->format('U')){
        continue;
    }
    
    if($row["last_alert"]!=""){
        if(($lastAlert->format('U') + $alertRecall*3600) < $lastUpdate->format('U')){
            $devices[]=Device::getDevice($row["id"]);
            $sqlUpdate.="UPDATE device SET last_alert=NOW() WHERE id=".$row["id"].";";
        }
    } else {
        //Il n'y a pas eu d'alerte envoyée
        $devices[]=Device::getDevice($row["id"]);
        $sqlUpdate.="UPDATE device SET last_alert=NOW() WHERE id=".$row["id"].";";
    }
}

if($sqlUpdate != ""){
    $stmt = $GLOBALS["dbconnec"]->prepare($sqlUpdate);
    $stmt->execute(array());
}

$subject="[DOMOKINE] Perte Communication";
$title="Perte Communication";
$content="";
foreach($devices as $device){
    //Generation d'un log d'alerte
    Log::createLog("alert", "lost_communication", date('d-m-Y H:i:s'), $device->id, 90);
    
    $content.="\n\nDevice ".$device->name;
}
if($content != ""){
    include("../controllers/mail.php");
}

?>
