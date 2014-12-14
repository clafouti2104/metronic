<?php
include("../tools/config.php");
$GLOBALS["dbconnec"] = connectDB();
include "../models/Scenario.php";

if(!isset($_POST["scenarioId"])){
    echo "error";
    return "error";
}

$scenario=Scenario::getScenario($_POST["scenarioId"]);
$scenario->delete();

echo "done";
?>
