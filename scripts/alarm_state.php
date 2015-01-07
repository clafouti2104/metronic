<?php
require("../tools/config.php");
require("../models/Device.php");

$ini = parse_ini_file("/var/www/metronic/tools/parameters.ini");
foreach($ini as $title => $value){
    if($title == "myfox_token" && $value != ""){
        $type="myfox";
        $token=$value;
        break;
    }
    if($title == "calaos_login" && $value != ""){
        $type="calaos";
    }
}

if($type=="myfox"){
    if($token == ""){
        $token=getToken();
    }
    $securityState = exec("curl https://api.myfox.me:443/v2/site/10562/security?access_token=".$token);
    $securityState = json_decode($securityState);
    if(isset($securityState->status) && $securityState->status == "KO" && $securityState->error == "invalid_token"){
        $token=getToken();
        $securityState = exec("curl https://api.myfox.me:443/v2/site/10562/security?access_token=".$token);
        $securityState = json_decode($securityState);
    }
    $status = $securityState->payload->statusLabel;
    $result = array("state"=>strtolower($status));
    echo $status;
    //print_r(json_encode($result));
}
?>
