<?php
/**
 * Récupération des états des devices ZWave
 */
require("../tools/config.php");
require("../models/Device.php");

$db = connectDB();
$GLOBALS["dbconnec"] = connectDB();
$zwaveLogin=$zwavePassword="admin";
//Récupération @IP razberry
$ini = parse_ini_file("/var/www/metronic/tools/parameters.ini");
foreach($ini as $title => $value){
    if($title == "zwave_ip_address"){
        $zwaveIpAddress=$value;
    }
    if($title == "zwave_login"){
        $zwaveLogin=$value;
    }
    if($title == "zwave_password"){
        $zwavePassword=$value;
    }
}

//Any zwave address specified
if(!isset($zwaveIpAddress)){
    exit;
}

//Récupération des devices actifs de type zwave
$zwaves=array();
$sql = "SELECT d.id, p.name as productName, param1, param2 FROM device d, product p WHERE p.id=d.product_id AND p.name LIKE 'zwave%' AND d.active=1";
$stmt = $db->prepare($sql);
$stmt->execute();
while($row = $stmt->fetch()){
    $zwaves[$row["param1"]][] = array(
        "deviceId"=>$row["id"],
        "path"=>$row["param2"]
    );
}

if(count($zwaves) == 0){
    exit;
}

foreach($zwaves as $zwaveId=>$zwaveObjects){
    //ZWayVDev_zway_2-0-67-1
    $url='http://'.$zwaveIpAddress.':8083/ZAutomation/api/v1/devices/'.$zwaveId;
    $cookie="/etc/domokine/cookie.txt";

    $c = curl_init($url);
    //curl_setopt($c, CURLOPT_VERBOSE, 1);
    curl_setopt($c, CURLOPT_COOKIEFILE, $cookie);
    curl_setopt($c, CURLOPT_RETURNTRANSFER, 1);
    $content = curl_exec($c);
    curl_close($c);
    $content=json_decode($content,TRUE);
    
    //Not authenticated
    if(isset($content['code']) && $content['code']='401'){
        $urlLogin='http://'.$zwaveIpAddress.':8083/ZAutomation/api/v1/login';
        $requestJson='{"form": true, "login": "'.$zwaveLogin.'", "password": "'.$zwavePassword.'", "keepme": true, "default_ui": 1}';

        $cLogin = curl_init($urlLogin);
        curl_setopt($cLogin,CURLOPT_POSTFIELDS,$requestJson);
        curl_setopt($cLogin, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($cLogin, CURLOPT_FOLLOWLOCATION, false);
        curl_setopt($cLogin, CURLOPT_RETURNTRANSFER, false);
        curl_setopt($cLogin, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($cLogin, CURLOPT_HTTPHEADER, array(
            'Content-Type: application/json',
            'Content-Length: ' . strlen($requestJson))
        );
        curl_setopt($cLogin, CURLOPT_COOKIEJAR, '/etc/domokine/cookie.txt');

        $content = curl_exec($cLogin);
        curl_close($cLogin);
    }

    $c = curl_init($url);
    //curl_setopt($c, CURLOPT_VERBOSE, 1);
    curl_setopt($c, CURLOPT_COOKIEFILE, $cookie);
    curl_setopt($c, CURLOPT_RETURNTRANSFER, 1);
    $content = curl_exec($c);
    curl_close($c);
    $content=json_decode($content,TRUE);

    foreach($zwaveObjects as $infos){
        if($infos["path"] == ""){
            continue;
        }
        if($infos["deviceId"] == ""){
            continue;
        }
        $explPath = explode(".", $infos["path"]);
        $buildResult = '$content';
        foreach($explPath as $item){
            $buildResult .= "['".$item."']";
        }
        $dataToStore="";
        eval('if(isset('.$buildResult.')){$dataToStore = '.$buildResult.';}');
        if($dataToStore == ""){
            continue;
        }
        Device::updateState($infos["deviceId"], $dataToStore);
        
    }
}
//print_r($content["devices.2.instances.0.commandClasses.67.data.1"]["val"]["value"]);
?>
