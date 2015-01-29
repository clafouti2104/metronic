<?php
include("../tools/config.php");
include("../models/Condition.php");
$GLOBALS["dbconnec"] = connectDB();

if(!isset($_POST["deviceId"])){
    echo "error";
    return "error";
}

//if(!isset($_POST["conditionId"])){
//    return "error";
//}
if(!isset($_POST["deviceId"])){
    return "error";
}
if(!isset($_POST["operator"])){
    return "error";
}
if(!isset($_POST["value"])){
    return "error";
}
if(!isset($_POST["condId"])){
    return "error";
}

if($_POST["conditionId"] != ""){
    $cond = Condition::getCondition($_POST["conditionId"]);
    $cond->objectId=$_POST["deviceId"];
    $cond->operator=$_POST["operator"];
    $cond->value=$_POST["value"];
    $cond->update();
} else {
    $cond=Condition::createCondition($_POST["condId"], "device", $_POST["operator"], $_POST["value"], $_POST["deviceId"]);
}

echo "success";
?>
