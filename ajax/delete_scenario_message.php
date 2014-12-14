<?php
include("../tools/config.php");
$GLOBALS["dbconnec"] = connectDB();
include "../models/ScenarioMessage.php";

if(!isset($_POST["scenarioMessageId"])){
    echo "error";
    return "error";
}

$scenarioMessage=  ScenarioMessage::getScenarioMessage($_POST["scenarioMessageId"]);
$scenarioMessage->delete();

echo "done";
?>
