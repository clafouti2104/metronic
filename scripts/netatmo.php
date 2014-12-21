<?php
/**
 * Récupération des états des sondes de températures netatmo
 */
require_once '../tools/netatmo/NAApiClient.php';
require_once '../tools/netatmo/Config.php';
require("../tools/config.php");
require("../models/Device.php");

$db = connectDB();
$GLOBALS["dbconnec"] = connectDB();
//Récupération état alarme
$ini = parse_ini_file("/var/www/metronic/tools/parameters.ini");
$content = "[parameters]";
$netatmo_client_id=$netatmo_client_secret=$netatmo_login=$netatmo_password="";

foreach($ini as $title => $value){
    if($title == "netatmo_client_id"){
        $netatmo_client_id=$value;
    }
    if($title == "netatmo_client_secret"){
        $netatmo_client_secret=$value;
    }
    if($title == "netatmo_login"){
        $netatmo_login=$value;
    }
    if($title == "netatmo_password"){
        $netatmo_password=$value;
    }
}

//Test si netatmo renseigné
if($netatmo_client_id == "" || $netatmo_client_secret == "" || $netatmo_login == "" || $netatmo_password == ""){
    echo "pas d'information renseignee\n";
    exit;
} 

$scope = NAScopes::SCOPE_READ_STATION;

$client = new NAApiClient(array("client_id" => $netatmo_client_id, "client_secret" => $netatmo_client_secret, "username" => $netatmo_login, "password" => $netatmo_password, "scope" => $scope));
$helper = new NAApiHelper($client);

try {
    $tokens = $client->getAccessToken();
} catch(NAClientException $ex) {
    echo "An error happend while trying to retrieve your tokens\n";
    exit(-1);
}

$devicelist = $helper->simplifyDeviceList();
$mesures = $helper->getLastMeasures();
print_r($mesures);
if(!is_array($mesures) || count($mesures) == 0){
    echo "no data returned\n";
    exit;
}
if(!isset($mesures[0]["modules"]) && count($mesures[0]["modules"]) < 2){
    echo "An error happend while trying retrieving data\n";
    exit;
}

$data=$mesures[0]["modules"];
//Récupération des devices actifs de type sonde de températures
$sql = "SELECT d.id, d.name, last_update, param1, model FROM device d, product p WHERE p.id=d.product_id AND p.name LIKE '%netatmo_meteo%' AND d.active=1";
$stmt = $db->prepare($sql);
$stmt->execute();
while($row = $stmt->fetch()){
    if($row["param1"] == ""){
        continue;
    }
    if($row["model"] == ""){
        continue;
    }
    
    $dateTmp=(strtolower($row["model"]) == "interieur") ? $data[0]["time_utc"] : $data[1]["time_utc"];
    
    $date=new DateTime();
    $date->setTimestamp($dateTmp);
    $value="";
    switch(strtolower($row["param1"])){
        case "temperature":
            if(strtolower($row["model"]) == "interieur"){
                $value=$data[0]["Temperature"];
            } else {
                $value=$data[1]["Temperature"];
            }
        case "humidite":
            if(strtolower($row["model"]) == "interieur"){
                $value=$data[0]["Humidity"];
            } else {
                $value=$data[1]["Humidity"];
            }
            break;
        default:
    }
    
    if($value != ""){
        Device::updateState($row["id"], $value, $date);
    }
}

?>