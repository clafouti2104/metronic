<?php
include("../tools/config.php");
$GLOBALS["dbconnec"] = connectDB();
include "../models/Report.php";

if(!isset($_POST["reportId"])){
    echo "error";
    return "error";
}

$Report=Report::getReport($_POST["reportId"]);
$Report->delete();

echo "done";
?>
