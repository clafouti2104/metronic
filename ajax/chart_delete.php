<?php
include("../tools/config.php");
$GLOBALS["dbconnec"] = connectDB();
include "../models/Chart.php";

if(!isset($_POST["chartId"])){
    echo "error";
    return "error";
}

$chart=Chart::getChart($_POST["chartId"]);
$chart->delete();

echo "done";
?>
