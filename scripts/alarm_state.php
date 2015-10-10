<?php
require("../tools/config.php");
require("../models/Device.php");

$db = connectDB();
$GLOBALS["dbconnec"] = connectDB();

$ini = parse_ini_file("/var/www/metronic/tools/parameters.ini");
foreach($ini as $title => $value){
    if($title == "myfox_token" && $value != ""){
        $type="myfox";
        $token=$value;
    }
    if($title == "myfox_siteid"){
        $siteid=$value;
    }
    if($title == "calaos_login" && $value != ""){
        $type="calaos";
    }
}

if($type=="myfox"){
    if($token == ""){
        $token=getToken();
    }
    if(!isset($siteid)){
        addLog(LOG_ERR, "[ACTION]: MyFOX : no siteid set");
        return false;
    }
    $curl = curl_init( "https://api.myfox.me:443/v2/site/".$siteid."/security?access_token=".$token);
    curl_setopt( $curl, CURLOPT_RETURNTRANSFER, 1);
    $securityState = curl_exec( $curl );
    //$securityState = exec("curl https://api.myfox.me:443/v2/site/".$siteid."/security?access_token=".$token);
    $securityState = json_decode($securityState);
    if(isset($securityState->status) && $securityState->status == "KO" && $securityState->error == "invalid_token"){
        $token=getToken();
        $curl = curl_init( "https://api.myfox.me:443/v2/site/".$siteid."/security?access_token=".$token);
        curl_setopt( $curl, CURLOPT_RETURNTRANSFER, 1);
        $securityState = curl_exec( $curl );
        //$securityState = exec("curl https://api.myfox.me:443/v2/site/".$siteid."/security?access_token=".$token);
        $securityState = json_decode($securityState);
    }
    $status = $securityState->payload->statusLabel;
    $result = array("state"=>strtolower($status));
    
    //Récupération des devices actifs de type sonde de températures
    $sql = "SELECT d.id, d.name, last_update, param1 FROM device d, product p WHERE p.id=d.product_id AND p.name='myfox_alarm' AND d.active=1";
    $stmt = $db->prepare($sql);
    $stmt->execute();
    if($row = $stmt->fetch()){
        Device::updateState($row["id"],$status);
    }
}
?>
