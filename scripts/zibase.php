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

$login="maisonkling";
$password="lamaison";
$timeout = array('http' => array('timeout' => 10));
$context = stream_context_create($timeout);

//Récupération des devices actifs de type sonde de températures
$sql = "SELECT d.id, d.name, ip_address, last_update, param1, p.name as product_name FROM device d, product p WHERE p.id=d.product_id AND p.name LIKE 'zibase_%' AND d.active=1";
$stmt = $db->prepare($sql);
$stmt->execute();
if($stmt->rowCount() > 0){
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

}
while($row = $stmt->fetch()){
    $type = explode('zibase_',$row["product_name"]);
    if(count($type) <= 1){
        continue;
    }
    
    if($type=="probe"){
        $contentProbe=file_get_contents("https://zibase.net/api/get/ZAPI.php?zibase=".$zibase."&token=".$token."&service=get&target=probe&id=".$row["param1"], false, $context);
        $jsonProbe = json_decode($contentProbe);
        print_r($jsonProbe);
        if(isset($jsonProbe->body->val1)){
            Device::updateState($row["id"],$jsonProbe->body->val1, "NOW()");
        }
    }
}


//$contentProbe=file_get_contents("https://zibase.net/api/get/ZAPI.php?zibase=".$zibase."&token=".$token."&service=get&target=probe&id=OS706330880", false, $context);
//$contentHome=file_get_contents("https://zibase.net/api/get/ZAPI.php?zibase=".$zibase."&token=".$token."&service=get&target=home", false, $context);




?>
