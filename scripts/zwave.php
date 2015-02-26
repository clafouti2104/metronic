<?php
/**
 * Récupération des états des devices ZWave
 */
require("../tools/config.php");
require("../models/Device.php");

$db = connectDB();
$GLOBALS["dbconnec"] = connectDB();
//Récupération @IP razberry
$ini = parse_ini_file("/var/www/metronic/tools/parameters.ini");
foreach($ini as $title => $value){
    if($title == "zwave_ip_address"){
        $zwaveIpAddress=$value;
        break;
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

$timeout = array('http' => array('timeout' => 10));
$context = stream_context_create($timeout);

$date=new DateTime('now');
$dateInterval=new DateInterval('PT360S');
$dateInterval->invert=1;
$date->add($dateInterval);
//echo "<br/>DATE = ".$date->format('U')."<br/>";
$contentData=file_get_contents("http://".$zwaveIpAddress.":8083/ZWaveAPI/Data/".$date->format('U'), false, $context);
$content=json_decode($contentData,TRUE);

foreach($zwaves as $zwaveId=>$zwaveObjects){
    foreach($zwaveObjects as $infos){
        if($infos["path"] == ""){
            continue;
        }
        if($infos["deviceId"] == ""){
            continue;
        }
        $explPath = explode("/", $infos["path"]);
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
