<?php
/**
 * Récupération des états des teleinfo GCE Eco Device
 */
require("../../tools/config.php");
require("../../models/Device.php");

$db = connectDB();
$GLOBALS["dbconnec"] = connectDB();

$timeout = array('http' => array('timeout' => 10));
$context = stream_context_create($timeout);

//Récupération des devices actifs de type sonde de températures
$sql = "SELECT d.id, d.name, ip_address, last_update, param1, p.name as productName FROM device d, product p WHERE p.id=d.product_id AND p.name='gce_teleinfo' AND d.active=1";
$stmt = $db->prepare($sql);
$stmt->execute();
while($row = $stmt->fetch()){
    if($row["ip_address"] == ""){
        continue;
    }
    if($row["param1"] == ""){
        continue;
    }
    
    switch(strtolower($row["productName"])){
        case 'gce_teleinfo':
            $url = "http://".$row["ip_address"]."/protect/settings/teleinfo1.xml";
            if(!$content = @file_get_contents($url, false, $context)){
                continue;
            }
            $xml = simplexml_load_file($url);
            print_r($xml->$row["param1"]);
            $value=$xml->$row["param1"];
            break;
        case 'gce_compteur':
            $url = "http://".$row["ip_address"]."/status.xml";
            if(!$content = @file_get_contents($url, false, $context)){
                continue;
            }
            $xml = simplexml_load_file($url);
            print_r($xml->$row["param1"]);
            $value=$xml->$row["param1"];
            break;
        default:
            continue;
    }
    
    if(isset($value)){
        Device::updateState($row["id"],$value, "NOW()");
    }
}
?>
