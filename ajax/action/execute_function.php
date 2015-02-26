<?php
/*include_once "../../models/Device.php";
include_once "../../models/MessageDevice.php";
include_once "../../models/Scenario.php";
include_once "../../models/ScenarioMessage.php";*/

function executeMessage($messgeId){
    //echo "execute message";
    $message=MessageDevice::getMessageDevice($messgeId);
    $device=Device::getDevice($message->deviceId);
    $productName="";
    //echo "\nDev=".$device->name;
    if($device->product_id != ""){
        echo "\nProd not null";
        $product = Product::getProduct($device->product_id);
        $productName=$product->name;
    }
    //echo "\nProdcut=".$productName;
    switch(strtolower($productName)){
        case 'calaos_output':
            echo "calaos output";
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
            //Construction query JSON
            $json='{';
            $json.='"cn_user": "'.$login.'",';
            $json.='"cn_pass": "'.$password.'",';
            $json.='"action": "set_state",';
            $json.='"type": "output",';
            $json.='"id": "'.$device->param1.'",';
            $json.='"value": "'.$value.'"';
            $json.='}';
            
            file_put_contents("/var/www/metronic/scripts/calaos/action.json", $json);
            
            //RECUPERATION INFO CALAOS
            exec('wget --no-check-certificate --post-file /var/www/metronic/scripts/calaos/action.json --output-document /var/www/metronic/scripts/calaos/result.json https://'.$ipAddress.'/api.php',$response);
            addLog(LOG_INFO, "[ACTION]: Calaos OUTPUT ".$device->name." to ".$message->name);
            
            $results = file_get_contents('/var/www/metronic/scripts/calaos/result_action.json');
            $results = json_decode($results,TRUE);
            
            break;
        case 'calaos_input':
            echo "calaos input";
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
            //Construction query JSON
            $json='{';
            $json.='"cn_user": "'.$login.'",';
            $json.='"cn_pass": "'.$password.'",';
            $json.='"action": "set_state",';
            $json.='"type": "input",';
            $json.='"id": "'.$device->param1.'",';
            $json.='"value": "'.$value.'"';
            $json.='}';
            
            file_put_contents("/var/www/metronic/scripts/calaos/action.json", $json);
            
            //RECUPERATION INFO CALAOS
            exec('wget --no-check-certificate --post-file /var/www/metronic/scripts/calaos/action.json --output-document /var/www/metronic/scripts/calaos/result.json https://'.$ipAddress.'/api.php',$response);
            addLog(LOG_INFO, "[ACTION]: Calaos INPUT ".$device->name." to ".$message->name);

            $results = file_get_contents('/var/www/metronic/scripts/calaos/result_action.json');
            $results = json_decode($results,TRUE);
            
            break;
        case 'freebox':
            file_get_contents("http://".$device->ip_address."/remote_control?code=".$device->param1."&key=".$message->command);
            addLog(LOG_INFO, "[ACTION]: Freebox to ".$message->name);
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
            addLog(LOG_INFO, "[ACTION]: MyFOX light ".$device->name." to ".$message->name);
            $json=json_decode($response);
            print_r($json);
            if(isset($json->status) && $json->status == "KO" && $json->error == "invalid_token"){
                addLog(LOG_INFO, "[ACTION]: MyFOX Token expired");
                $token=getToken();
                $response=exec("curl https://api.myfox.me:443/v2/site/10562/security/set/".$message->command."?access_token=".$token);
                addLog(LOG_INFO, "[ACTION]: MyFOX light ".$device->name." to ".$message->name);
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
            addLog(LOG_INFO, "[ACTION]: MyFOX group ".$device->name." to ".$message->name);
            $json=json_decode($response);
            print_r($json);
            if(isset($json->status) && $json->status == "KO" && $json->error == "invalid_token"){
                addLog(LOG_INFO, "[ACTION]: MyFOX Token expired");
                $token=getToken();
                $response=exec("curl https://api.myfox.me:443/v2/site/10562/group/".$device->param1."/electric/".$message->command."?access_token=".$token);
                addLog(LOG_INFO, "[ACTION]: MyFOX group ".$device->name." to ".$message->name);
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
            print_r($json);
            addLog(LOG_INFO, "[ACTION]: MyFOX light ".$device->name." to ".$message->name);
            if(isset($json->status) && $json->status == "KO" && $json->error == "invalid_token"){
                addLog(LOG_INFO, "[ACTION]: MyFOX Token expired");
                $token=getToken();
                $response=exec("curl https://api.myfox.me:443/v2/site/10562/device/".$device->param1."/socket/".$message->command."?access_token=".$token);
                addLog(LOG_INFO, "[ACTION]: MyFOX light ".$device->name." to ".$message->name);
            }
            break;
        case 'popcorn':
            file_get_contents("http://".$device->ip_address.":9999/c200remote_web/webrc200.php?fcmd=".$message->command);
            addLog(LOG_INFO, "[ACTION]: Popcorn ".$message->name);
            break;
        case 'zwave_thermostat':
            $ini = parse_ini_file("/var/www/metronic/tools/parameters.ini");
            foreach($ini as $title => $value){
                if($title == "zwave_ip_address"){
                    $zwave_ip_address=$value;
                    break;
                }
            }
            if(!isset($zwave_ip_address)){
                addLog(LOG_ERR, "ERR [ACTION]: Any Zwave Ip Address is set");
                return FALSE;
            }
            file_get_contents("http://".$zwave_ip_address.":8083/ZWaveAPI/Run/devices[".$device->param1."].".$message->command);
            addLog(LOG_INFO, "[ACTION]: ZWave thermostat ".$device->name." ".$message->name);
            break;
        default:
            if(strtolower($message->type) == "http"){
                $prefixCommand=(substr($message->command, 0, 1) == "/") ? "" : "/";
                file_get_contents("http://".$device->ip_address.$prefixCommand.$message->command, false, $context);
                addLog(LOG_INFO, "[ACTION]: HTTP ".$device->name." ".$message->name);
            }
    }
}
?>
