<?php
//include("myfox.php");
include("../../tools/config.php");
$GLOBALS["dbconnec"]=connectDB();
include "../../models/Device.php";
include "../../models/MessageDevice.php";
include "../../models/Scenario.php";
include "../../models/ScenarioMessage.php";

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
        $messages=ScenarioMessage::getScenarioMessagesForScenario($_POST["elementId"]);
        foreach($messages as $message){
            if(!is_null($message->messageid)){
                executeMessage($message->messageid);
            } elseif(!is_null($message->pause)) {
                sleep($message->pause);
            }
        }
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

function executeMessage($messgeId, $valueToSend=NULL){
    echo "execute message";
    $message=MessageDevice::getMessageDevice($messgeId);
    $device=Device::getDevice($message->deviceId);
    $productName="";
    echo "\nDev=".$device->name;
    if($device->product_id != ""){
        echo "\nProd not null";
        $product = Product::getProduct($device->product_id);
        $productName=$product->name;
    }
    echo "\nProdcut=".$productName;
    switch(strtolower($productName)){
        case 'freebox':
            echo "http://".$device->ip_address."/pub/remote_control?code=".$device->param1."&key=".$message->command;
            file_get_contents("http://".$device->ip_address."/pub/remote_control?code=".$device->param1."&key=".$message->command, false, $context);
            break;
        case 'myfox_alarm':
            echo "myfox_alarm";
            //Récupération token
            $ini = parse_ini_file("/var/www/metronic/tools/parameters.ini");
            $content = "[parameters]";
            foreach($ini as $title => $value){
                if($title == "myfox_token"){
                    $token=$value;
                    break;
                }
            }
            if($token == ""){
                $token=getToken();
            }
            $response=exec("curl https://api.myfox.me:443/v2/site/10562/security/set/".$message->command."?access_token=".$token);
            $json=json_decode($response);
            //print_r($json);
            if(isset($json->status) && $json->status == "KO" && $json->error == "invalid_token"){
                $token=getToken();
                $response=exec("curl https://api.myfox.me:443/v2/site/10562/security/set/".$message->command."?access_token=".$token);
            }
            break;
        case 'myfox_group':
            echo "myfox_group";
            //Récupération token
            $ini = parse_ini_file("/var/www/metronic/tools/parameters.ini");
            $content = "[parameters]";
            foreach($ini as $title => $value){
                if($title == "myfox_token"){
                    $token=$value;
                    break;
                }
            }
            if($token == ""){
                $token=getToken();
            }
            $response=exec("curl https://api.myfox.me:443/v2/site/10562/group/".$device->param1."/electric/".$message->command."?access_token=".$token);
            $json=json_decode($response);
            //print_r($json);
            if(isset($json->status) && $json->status == "KO" && $json->error == "invalid_token"){
                $token=getToken();
                $response=exec("curl https://api.myfox.me:443/v2/site/10562/group/".$device->param1."/electric/".$message->command."?access_token=".$token);
            }
            break;
        case 'myfox_light':
            echo "myfox_light";
            //Récupération token
            $ini = parse_ini_file("/var/www/metronic/tools/parameters.ini");
            $content = "[parameters]";
            foreach($ini as $title => $value){
                if($title == "myfox_token"){
                    $token=$value;
                    break;
                }
            }
            if($token == ""){
                $token=getToken();
            }
            $response=exec("curl https://api.myfox.me:443/v2/site/10562/device/".$device->param1."/socket/".$message->command."?access_token=".$token);
            $json=json_decode($response);
            //print_r($json);
            if(isset($json->status) && $json->status == "KO" && $json->error == "invalid_token"){
                $token=getToken();
                $response=exec("curl https://api.myfox.me:443/v2/site/10562/device/".$device->param1."/socket/".$message->command."?access_token=".$token);
            }
            break;
        case 'popcorn':
            file_get_contents("http://".$device->ip_address.":9999/c200remote_web/webrc200.php?fcmd=".$message->command);
            break;
        case 'calaos_output':
            echo "calaos output";
            calaos("output",$device,$message,$valueToSend);
            
            break;
        case 'calaos_input':
            echo "calaos input";
            calaos("input",$device,$message,$valueToSend);
            
            break;
        default:
            if(strtolower($message->type) == "http"){
                $prefixCommand=(substr($message->command, 0, 1) == "/") ? "" : "/";
                echo "call "."http://".$device->ip_address.$prefixCommand.$message->command;
                file_get_contents("http://".$device->ip_address.$prefixCommand.$message->command, false, $context);
            }
    }
}

function calaos($type,$device,$message,$valueToSend=NULL){
    //Récupération token
    $ini = parse_ini_file("/var/www/metronic/tools/parameters.ini");
    $content = "[parameters]";
    $login=$password=$ipAddress="";
    foreach($ini as $title => $value){
        if($title == "calaos_login"){
            $login=$value;
        }
        if($title == "calaos_password"){
            $password=$value;
        }
        if($title == "calaos_ip_address"){
            $ipAddress=$value;
        }
    }

    $value="";
    if(strtolower($message->command) == "on"){
        $value = 'true';
    }
    if(strtolower($message->command) == "off"){
        $value = 'false';
    }
    $value=($valueToSend != "") ? $valueToSend : $value;

    //Construction query JSON
    $json='{';
    $json.='"cn_user": "'.$login.'",';
    $json.='"cn_pass": "'.$password.'",';
    $json.='"action": "set_state",';
    $json.='"type": "'.$type.'",';
    $json.='"id": "'.$device->param1.'",';
    $json.='"value": "'.$value.'"';
    $json.='}';

    file_put_contents("/var/www/metronic/scripts/calaos/action.json", $json);

    //RECUPERATION INFO CALAOS
    exec('wget --no-check-certificate --post-file /var/www/metronic/scripts/calaos/action.json --output-document /var/www/metronic/scripts/calaos/result.json https://'.$ipAddress.'/api.php',$response);

    $results = file_get_contents('/var/www/metronic/scripts/calaos/result_action.json');
    $results = json_decode($results,TRUE);
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
