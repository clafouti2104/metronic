<?php
/*
 * URL à appeler pour exécuter un scenario 
 * http://<IP>/metronic/api/execute_scenario.php?idScenario=<ID_SCENARIO>
 */

if(!isset($_GET["idScenario"])){
    echo "ID Scenario manquant";
    return false;
}

include("../tools/config.php");
include("../ajax/action/execute_function.php");
$GLOBALS["dbconnec"]=connectDB();
include_once "../models/Device.php";
include_once "../models/MessageDevice.php";
include_once "../models/Scenario.php";
include_once "../models/ScenarioMessage.php";

$messages=ScenarioMessage::getScenarioMessagesForScenario($_GET["idScenario"]);
foreach($messages as $message){
    echo "EXEC MESSAGE ".$message->messageid;
    if(!is_null($message->messageid)){
        executeMessage($message->messageid);
    } elseif(!is_null($message->pause)) {
        sleep($message->pause);
    }
}

?>
