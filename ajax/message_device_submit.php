<?php
include("../tools/config.php");
$GLOBALS["dbconnec"] = connectDB();
include "../models/MessageDevice.php";

if(!isset($_POST["deviceId"])){
    echo "error";
    return "error";
}
$active = ($_POST["active"] == "true") ? 1 : 0;

if($_POST["messageId"] != ""){
    $message = MessageDevice::getMessageDevice($_POST["messageId"]);
    $message->name=$_POST["name"];
    $message->type=$_POST["type"];
    $message->command=$_POST["command"];
    //$message->active=$_POST["active"];
    $message->update();
} else {
    $message = MessageDevice::createMessageDevice($_POST["deviceId"], $_POST["name"], 0, NULL, NULL, $_POST["type"], 1,NULL, $_POST["command"]);
}

echo "success";
?>
