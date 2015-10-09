<?php
include("../tools/config.php");
$GLOBALS["dbconnec"] = connectDB();
include_once "../models/MessageDevice.php";

if(!isset($_POST["messageId"])){
    echo "error";
    return "error";
}

//Récupération du MessageDevice
$MessageDevice=MessageDevice::getMessageDevice($_POST["messageId"]);
$MessageDevice->delete();
echo "success";

?>
