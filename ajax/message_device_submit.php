<?php
include("../tools/config.php");
$GLOBALS["dbconnec"] = connectDB();
include_once "../models/MessageDevice.php";

if(!isset($_POST["deviceId"])){
    echo "error";
    return "error";
}
$active = ($_POST["active"] == "true") ? 1 : 0;
$action = ($_POST["action"] == "true") ? 1 : 0;
$params = (isset($_POST["slider"]) && $_POST["slider"]=="true") ? '{"slider":"true"}' : NULL;

if($_POST["messageId"] != ""){
    $message = MessageDevice::getMessageDevice($_POST["messageId"]);
    $message->name=$_POST["name"];
    $message->type=$_POST["type"];
    $message->command=$_POST["command"];
    //$message->action=$action;
    if(isset($_POST["slider"]) && $_POST["slider"]=="true"){
        $message->parameters='{"slider":"true"}';
    } else {
        $message->parameters='';
    }
    //$message->active=$_POST["active"];
    $message->update();
} else {
    $message = MessageDevice::createMessageDevice($_POST["deviceId"], $_POST["name"], 0, NULL, NULL, $_POST["type"], 1,$params, $_POST["command"]);
}

echo "success";
?>
