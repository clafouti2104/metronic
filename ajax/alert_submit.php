<?php
include("../tools/config.php");
$GLOBALS["dbconnec"] = connectDB();
include "../models/Alert.php";

if(!isset($_POST["alertId"])){
    echo "error";
    return "error";
}

if($_POST["alertId"] != ""){
    $alert = Alert::getAlert($_POST["alertId"]);
    $alert->operator=$_POST["operator"];
    $alert->value=$_POST["value"];
    $alert->notificationId=$_POST["pushingbox"];
    $alert->update();
} else {
    $alert = Alert::createAlert($_POST["deviceId"], $_POST["operator"], $_POST["value"], $_POST["pushingbox"]);
}

echo "success";
?>
