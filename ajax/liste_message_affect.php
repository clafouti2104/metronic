<?php
include("../tools/config.php");
$GLOBALS["dbconnec"] = connectDB();
include "../models/ListeMessage.php";

if(!isset($_POST["listeDeviceId"])){
    echo "error";
    return "error";
}

if(!isset($_POST["msgId"])){
    echo "error";
    return "error";
}

$listeMessage= ListeMessage::getListeMessage($_POST["listeDeviceId"]);
$listeMessage->messageid=$_POST["msgId"];
$listeMessage->update();

echo "done";
?>
