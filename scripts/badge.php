<?php
/*
 * Script exécuté lors du badgeage
 */

include("../tools/config.php");
include "../models/Log.php";

$GLOBALS["dbconnec"] = connectDB();

//Recherche dernier badgeage
$sql="SELECT * FROM log WHERE ";
$sql.=" rfid='badge' ORDER BY date DESC LIMIT 1";
$stmt = $GLOBALS["dbconnec"]->prepare($sql);
$stmt->execute(array());

$action=TRUE;
//Derniere date
if ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    $dateDernierBadge=new DateTime($row["date"]);
    //Différence en secondes
    $seconds = time() - $dateDernierBadge->format('U');
    //Si badgé il y a moins d'une minute
    if(($seconds)/20 < 1){
        $action = FALSE;
    }
}

$date = new DateTime('now');
//Badgé trop récemment
if(!$action){
    //Ajout d'un log
    Log::createLog("badge_refuse", "double badgeage", $date, NULL, 80);
    
    return true;
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
//Get Status Alarme
$securityState = exec("curl https://api.myfox.me:443/v2/site/10562/security?access_token=".$token);
$securityState = json_decode($securityState);
if(isset($securityState->status) && $securityState->status == "KO" && $securityState->error == "invalid_token"){
    $token=getToken();
    $securityState = exec("curl https://api.myfox.me:443/v2/site/10562/security?access_token=".$token);
    $securityState = json_decode($securityState);
}
if(isset($securityState->status) && $securityState->status == "KO" && $securityState->error == "invalid_token"){
    Log::createLog("myfox", "Probleme recuperation etat", $date, NULL, 80);
    exit;
}
$status = $securityState->payload->statusLabel;

//Exécution action inverse
switch(strtolower($status)){
    case "disarmed":
        $action="armed";
        break;
    case "armed":
        $action="disarmed";
        break;
    case "partial":
        $action="disarmed";
        break;
    default :
}

$response=exec("curl https://api.myfox.me:443/v2/site/10562/security/set/".$action."?access_token=".$token);
$json=json_decode($response);
if(isset($json->status) && $json->status == "KO" && $json->error == "invalid_token"){
    $token=getToken();
    $response=exec("curl https://api.myfox.me:443/v2/site/10562/security/set/".$action."?access_token=".$token);
}

//Allumage du groupe lumière salon si MHS
if($action == "disarmed"){
    $response=exec("curl https://api.myfox.me:443/v2/site/10562/scenario/42428/play?access_token=".$token);
} elseif($action == "armed"){
    //Extinction de ttes les lumières si MES
    $response=exec("curl https://api.myfox.me:443/v2/site/10562/scenario/42429/play?access_token=".$token);
}

//Extinction TV
file_get_contents("http://192.168.1.67/tvdown.php");

Log::createLog("badge", "Badge accepté", $date, NULL, 80);
?>
