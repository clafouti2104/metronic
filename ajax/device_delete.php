<?php
include("../tools/config.php");
$GLOBALS["dbconnec"] = connectDB();
include "../models/Device.php";

if(!isset($_POST["deviceId"])){
    echo "error";
    return "error";
}

$device=Device::getDevice($_POST["deviceId"]);
$device->delete();

echo "done";
?>
