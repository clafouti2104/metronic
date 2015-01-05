<?php
/**
 * Récupération des états des teleinfo GCE Eco Device
 */
require("../../tools/config.php");
require("../../models/Device.php");

$db = connectDB();
$GLOBALS["dbconnec"] = connectDB();
//Récupération état alarme
$ini = parse_ini_file("/var/www/metronic/tools/parameters.ini");

$timeout = array('http' => array('timeout' => 10));
$context = stream_context_create($timeout);

//Récupération des devices actifs de type sonde de températures
$sql = "SELECT d.id, d.name, ip_address, last_update, param1 FROM device d, product p WHERE p.id=d.product_id AND p.name='gce_teleinfo' AND d.active=1";
$stmt = $db->prepare($sql);
$stmt->execute();
while($row = $stmt->fetch()){
    if($row["ip_address"] == ""){
        continue;
    }
    if($row["param1"] == ""){
        continue;
    }
    
    $url = "http://".$row["ip_address"]."/protect/settings/teleinfo1.xml";
    if(!$content = @file_get_contents($url, false, $context)){
        continue;
    }
    
    $xml = simplexml_load_file($url);
    print_r($xml->$row["param1"]);
    
    if(isset($xml->$row["param1"])){
        Device::updateState($row["id"],$xml->$row["param1"], "NOW()");
    }
}
?>
