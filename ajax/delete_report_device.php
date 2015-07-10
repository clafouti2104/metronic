<?php
include("../tools/config.php");
$GLOBALS["dbconnec"] = connectDB();
include "../models/ReportDevice.php";

if(!isset($_POST["reportDeviceId"])){
    echo "error";
    return "error";
}

$reportDevice= ReportDevice::getReportDevice($_POST["reportDeviceId"]);
$reportDevice->delete();

echo "done";
?>
