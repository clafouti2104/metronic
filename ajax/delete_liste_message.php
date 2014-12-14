<?php
include("../tools/config.php");
$GLOBALS["dbconnec"] = connectDB();
include "../models/ListeMessage.php";

if(!isset($_POST["listeDeviceId"])){
    echo "error";
    return "error";
}

$listeMessage= ListeMessage::getListeMessage($_POST["listeDeviceId"]);
$listeMessage->delete();

echo "done";
?>
