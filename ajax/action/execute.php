<?php
//include("myfox.php");
include_once("../../tools/config.php");
include_once("../../tools/action.php");
$GLOBALS["dbconnec"]=connectDB();
include_once "../../models/Device.php";
include_once "../../models/MessageDevice.php";
include_once "../../models/Scenario.php";
include_once "../../models/ScenarioMessage.php";

$timeout = array('http' => array('timeout' => 10));
$context = stream_context_create($timeout);

if(!isset($_POST["type"])){
    echo "error";
    return false;
}
if(!isset($_POST["elementId"])){
    echo "error";
    return false;
}

$value=(isset($_POST["value"])) ? $_POST["value"] : NULL; 

switch (strtolower($_POST["type"])){
    case 'scenario':
        echo "scenario";
        executeScenario($_POST["elementId"]);
        break;
    case 'message':
        executeMessage($_POST["elementId"],$value);
        break;
    case 'device':
        //Get Message with command associated
        $messageTmp = MessageDevice::getMessageDeviceForCommandAndDevice($_POST["elementId"], $_POST["action"]);
        if(is_object($messageTmp)){
            executeMessage($messageTmp->id);
        }
        break;
    
    default:
}

return true;
?>
