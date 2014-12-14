<?php
/*
 * URL à appeler pour exécuter un message
 * http://<IP>/metronic/api/execute_message.php?idMessage=<ID_MESSAGE>
 */

if(!isset($_GET["idMessage"])){
    echo "ID Message manquant";
    return false;
}

include("../tools/config.php");
include("../ajax/action/execute_function.php");
$GLOBALS["dbconnec"]=connectDB();
include_once "../models/Device.php";
include_once "../models/MessageDevice.php";
include_once "../models/Scenario.php";
include_once "../models/ScenarioMessage.php";

$message=  MessageDevice::getMessageDevice($_GET["idMessage"]);
executeMessage($message->id);

?>
