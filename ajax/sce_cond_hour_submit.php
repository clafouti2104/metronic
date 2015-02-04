<?php
include("../tools/config.php");
include("../models/Condition.php");
$GLOBALS["dbconnec"] = connectDB();

if(!isset($_POST["condId"])){
    echo "error";
    return "error";
}
if(!isset($_POST["days"])){
    return "error";
}
if(!isset($_POST["hourBegin"])){
    return "error";
}
if(!isset($_POST["hourEnd"])){
    return "error";
}

$hourBegin=explode(":",$_POST["hourBegin"]);
$hourEnd=explode(":",$_POST["hourEnd"]);

//Contrôle Heures
$beginHour=intval($hourBegin[0])*60+intval($hourBegin[1]);
$endHour=intval($hourEnd[0])*60+intval($hourEnd[0]);
if($beginHour > $endHour){
    echo "wrondHour";
    return "wrondHour";
}

$value=array();
$value["days"]=$_POST["days"];
$value["beginHour"]=$hourBegin[0];
$value["beginMinute"]=$hourBegin[1];
$value["endHour"]=$hourEnd[0];
$value["beginMinute"]=$hourEnd[1];

if($_POST["conditionId"] != ""){
    /*$cond = Condition::getCondition($_POST["conditionId"]);
    $cond->objectId=$_POST["deviceId"];
    $cond->operator=$_POST["operator"];
    $cond->value=$_POST["value"];
    $cond->update();*/
} else {
    $cond=Condition::createCondition($_POST["condId"], "hour", NULL, json_encode($value), NULL);
}

echo "success";
?>
