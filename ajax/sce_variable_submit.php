<?php
include("../tools/config.php");
include("../models/Condition.php");
$GLOBALS["dbconnec"] = connectDB();

if(!isset($_POST["deviceId"])){
    echo "error";
    return "error";
}

if(!isset($_POST["variableId"])){
    return "error";
}
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

if($_POST["variableId"] != ""){
    $cond = Condition::getCondition($_POST["variableId"]);
    $cond->objectId=$_POST["deviceId"];
    $cond->operator=$_POST["operator"];
    $cond->value=$_POST["value"];
    $cond->update();
} else {
    $cond=Condition::createCondition($_POST["condId"], "variable", $_POST["operator"], $_POST["value"], $_POST["deviceId"]);
}

echo "success";
?>
