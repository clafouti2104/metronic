<?php
include_once "../models/Device.php";
include_once "../models/MessageDevice.php";
include_once "../models/ScenarioMessage.php";


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
        case 'calaos_output':
            echo "calaos output";
            calaos("output",$device,$message,$valueToSend);
            break;
        case 'calaos_input':
            echo "calaos input";
            calaos("input",$device,$message,$valueToSend);
            break;
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
        case 'runeaudio':
            echo "http://".$device->ip_address."/command/?cmd=".$message->command;
            file_get_contents("http://".$device->ip_address."/command/?cmd=".$message->command, false, $context);
            break;
        case 'zibase_actuator':
            echo "zibase_actuator";
            zibase("actuator",$device,$message,$valueToSend);
            
            break;
        case 'zwave_heat':
            echo "zwave_heat";
            zwave("heat",$device,$message,$valueToSend);
            
            break;
        default:
            if(strtolower($message->type) == "http"){
                $prefixCommand=(substr($message->command, 0, 1) == "/") ? "" : "/";
                echo "call "."http://".$device->ip_address.$prefixCommand.$message->command;
                file_get_contents("http://".$device->ip_address.$prefixCommand.$message->command, false, $context);
            }
    }
}


function executeScenario($idScenario){
    $messages=ScenarioMessage::getScenarioMessagesForScenario($idScenario);
    foreach($messages as $message){
        if(!is_null($message->messageid)){
            executeMessage($message->messageid);
        } elseif(!is_null($message->pause)) {
            sleep($message->pause);
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
    $value=($valueToSend != "") ? "set ".$valueToSend : $value;

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
    exec('wget --no-check-certificate --post-file /var/www/metronic/scripts/calaos/action.json --output-document /var/www/metronic/scripts/calaos/result_action.json https://'.$ipAddress.'/api.php',$response);

    $results = file_get_contents('/var/www/metronic/scripts/calaos/result_action.json');
    $results = json_decode($results,TRUE);
}

function zibase($type,$device,$message,$valueToSend=NULL){
    //Récupération état alarme
    $ini = parse_ini_file("/var/www/metronic/tools/parameters.ini");

    $login="maisonkling";
    $password="lamaison";
    $timeout = array('http' => array('timeout' => 10));
    $context = stream_context_create($timeout);
    
    $contentToken=file_get_contents("https://zibase.net/api/get/ZAPI.php?login=".$login."&password=".$password."&service=get&target=token", false, $context);
    if(is_null($contentToken)){
        die('Error getting token');
    }
    if($contentToken == ""){
        die('Error getting token');
    }

    $jsonToken = json_decode($contentToken);
    if(!isset($jsonToken->body->token)){
        die('Error getting token');
    }
    if(!isset($jsonToken->body->zibase)){
        die('Error getting zibase');
    }

    $zibase=$jsonToken->body->zibase;
    $token=$jsonToken->body->token;
    
    switch(strtolower($type)){
        case 'actuator':
            $valueToExec=(strtolower($message->command) == "on") ? "1" : "0";
            $contentProbe=file_get_contents("https://zibase.net/api/get/ZAPI.php?zibase=".$zibase."&token=".$token."&service=execute&target=actuator&id=".$device->param1."&action=".$valueToExec, false, $context);
            break;
        case 'scenario':
            $contentProbe=file_get_contents("https://zibase.net/api/get/ZAPI.php?zibase=".$zibase."&token=".$token."&service=execute&target=scenario&id=".$device->param1, false, $context);
            break;
    }
}

function zwave($heat,$device,$message,$valueToSend=NULL){
    $login="maisonkling";
    $password="lamaison";
    $timeout = array('http' => array('timeout' => 10));
    $context = stream_context_create($timeout);
    
    $urlAction=file_get_contents("http://".$device->ip_address.":8083", false, $context);
    
    return TRUE;
}

?>
