<?php
include("../tools/config.php");
$GLOBALS["dbconnec"] = connectDB();
include "../models/Cond.php";

if(!isset($_POST["scenarioId"])){
    echo "error";
    return "error";
}

$scenario=Cond::getCond($_POST["scenarioId"]);
$scenario->delete();

echo "done";
?>
