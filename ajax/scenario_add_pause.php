<?php
include("../tools/config.php");
$GLOBALS["dbconnec"] = connectDB();
include "../models/Scenario.php";
include "../models/ScenarioMessage.php";

if(!isset($_POST["scenarioId"])){
    echo "error";
    return "error";
}

if(!isset($_POST["pause"])){
    echo "error";
    return "error";
}

$position= ScenarioMessage::getNextPositionForScenario($_POST["scenarioId"]);
$scenarioMessage=  ScenarioMessage::createScenarioMessage($_POST["scenarioId"], NULL, $position,$_POST["pause"]);

echo "$(\"#nestable_list_2 .dd-list\").append('<li class=\"dd-item\"><div class=\"dd-handle\" messageid=\"pause-".$_POST["pause"]."\">Pause - ".$_POST["pause"]."s</div></li>');";
?>
