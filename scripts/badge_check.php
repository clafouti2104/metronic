<?php
/*
 * Script exécuté lors du badgeage
 * Vérifie 
 *      si le badge est correct, MHS/MES alarme en fonction + action diverse
 *      sinon log le badge refusé
 */

include("../tools/config.php");
include "../models/Log.php";

$date = new DateTime('now');
$GLOBALS["dbconnec"] = connectDB();
if(!isset($_GET["uid"])){
    //Ajout d'un log
    Log::createLog("badge_refuse", "double badgeage", $date, NULL, 80);
    
    return true;
}

//Recherche badges autorisés
$badges = array();
$sql="SELECT * FROM config WHERE ";
$sql.=" name='badge'";
$stmt = $GLOBALS["dbconnec"]->prepare($sql);
$stmt->execute(array());
while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    $badges[] = $row["value"];
}

if(!in_array($_GET["uid"], $badges)){
    //Ajout d'un log
    Log::createLog("badge_refuse", "badge ".$_GET["uid"]." inconnu", $date, NULL, 80);
    
    return true;
}

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
    if($seconds < 20){
        $action = FALSE;
    }
}

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
    //Extinction Ampli
    file_get_contents("http://192.168.1.23/metronic/api/execute_message.php?idMessage=50");
    //Extinction TV
    file_get_contents("http://192.168.1.67/tvdown.php");
}

//Badge Pox
if($_GET["uid"] == "10E98225"){
    exec("curl \"http://autoremotejoaomgcd.appspot.com/sendmessage?key=APA91bFEqTQdzy_3S34KyDyYfwQd5gl7mSMpNXWieZhHtRJ4nZ-CaZq9Qd0i_RzRAbONL1ZzPTbnbjTokmOmfC_I1HNbAn3n4dgVPtLdwHX7zj4eYu-2Sew74_TSQC23rlL5nTsB2KOMkJ-EJUlkISvnFz7RlWGgyIvFUHK2sD6YtQfx-CbEd5o&message=badge_pox_".$action."\"");
}
if($_GET["uid"] == "439294F4"){
    exec("curl \"http://autoremotejoaomgcd.appspot.com/sendmessage?key=APA91bFEqTQdzy_3S34KyDyYfwQd5gl7mSMpNXWieZhHtRJ4nZ-CaZq9Qd0i_RzRAbONL1ZzPTbnTokmOmfC_I1HNbAn3n4dgVPtLdwHX7zj4eYu-2Sew74_TSQC23rlL5nTsB2KOMkJ-EJUlkISvnFz7RlWGgyIvFUHK2sD6YtQfx-CbEd5o&message=badge_pouch_".$action."\"");
}

Log::createLog("badge", "Badge ".$_GET["uid"]." accepté", $date, NULL, 80);
?>
