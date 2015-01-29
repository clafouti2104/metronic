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

switch ($_POST["type"]){
    case "free-power":
        file_get_contents($urlFreebox."&key=power");
        break;
    case "free-enter":
        file_get_contents($urlFreebox."&key=ok");
        break;
    case "free-home":
        file_get_contents($urlFreebox."&key=home");
        break;
    case "free-mute":
        file_get_contents($urlFreebox."&key=mute");
        break;
    case "free-volumeup":
        file_get_contents($urlFreebox."&key=vol_inc");
        break;
    case "free-volumedown":
        file_get_contents($urlFreebox."&key=vol_dec");
        break;
    case "free-programup":
        file_get_contents($urlFreebox."&key=prgm_inc");
        break;
    case "free-programdown":
        file_get_contents($urlFreebox."&key=prgm_dec");
        break;
    case "tv-on":
        file_get_contents("http://".$ipRpi."/tvup.php");
        break;
    case "tv-off":
        file_get_contents("http://".$ipRpi."/tvdown.php");
        break;
    case "tv-free":
        file_get_contents("http://".$ipRpi."/tvfree.php");
        break;
    case "tv-popcorn":
        file_get_contents("http://".$ipRpi."/tvpopcorn.php");
        break;
    case "tv-ps3":
        file_get_contents("http://".$ipRpi."/tvps3.php");
        break;
    case "amp-on":
        file_get_contents("http://".$ipRpi."/amp/amp.php?action=KEY_POWER");
        break;
    case "amp-off":
        file_get_contents("http://".$ipRpi."/amp/amp.php?action=KEY_POWER2");
        break;
    case "amp":
        file_get_contents("http://".$ipRpi."/amp/amp.php?action=".$_POST["action"]);
        break;
    case "cd":
        file_get_contents("http://".$ipRpi."/cd/cd.php?action=".$_POST["action"]);
        break;
    case "popcorn":
        file_get_contents("http://".$ipPopcorn.":9999/c200remote_web/webrc200.php?fcmd=".$_POST["action"]);
        break;
    case "light":
        $token=getToken();
        file_get_contents("https://api.myfox.me:443/v2/site/10562/device/".$_POST["deviceId"]."/socket/".$_POST["action"]."?access_token=".$token);
        break;
    case "freebox":
        //Allume TV
        file_get_contents("http://".$ipRpi."/tvup.php");
        //Allume Freebox
        file_get_contents($urlFreebox."&key=power");
        //Allume Ampli
        file_get_contents("http://".$ipRpi."/amp/amp.php?action=KEY_POWER");
        sleep(7);
        //Allume Ampli
        file_get_contents("http://".$ipRpi."/amp/amp.php?action=KEY_AUX");
        //Allume Freebox
        file_get_contents($urlFreebox."&key=ok");
        break;
    case "extinction":
        //Eteint TV
        file_get_contents("http://".$ipRpi."/tvdown.php");
        //Eteint Freebox
        file_get_contents($urlFreebox."&key=power");
        //Eteint Ampli
        file_get_contents("http://".$ipRpi."/amp/amp.php?action=KEY_POWER2");
        //Eteint Ampli
        file_get_contents("http://".$ipRpi."/cd/cd.php?action=KEY_POWER");
        break;
    case "sce-cd":
        //Allume Ampli
        file_get_contents("http://".$ipRpi."/amp/amp.php?action=KEY_POWER");
        //Allume CD
        file_get_contents("http://".$ipRpi."/cd/cd.php?action=KEY_POWER");
        //Eteint TV
        file_get_contents("http://".$ipRpi."/tvdown.php");
        //Eteint Freebox
        file_get_contents($urlFreebox."&key=power");
        sleep(1);
        file_get_contents("http://".$ipRpi."/amp/amp.php?action=KEY_DVD");
        file_get_contents("http://".$ipRpi."/cd/cd.php?action=KEY_PLAY");
        break;
    case "alarm":
        $token=getToken();
        $response=file_get_contents("https://api.myfox.me:443/v2/site/10562/security/set/".$_POST['action']."?access_token=".$token);
        $response=json_decode($response);
        if($response->status == "KO"){
            echo 'ERROR request';
        }
        break;
    default:
        return false;
}
return true;
?>
