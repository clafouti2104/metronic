<?php
/**
 * Récupération des états des sondes de températures myfox
 */
require("../tools/config.php");
require("../models/Device.php");

$db = connectDB();
$GLOBALS["dbconnec"] = connectDB();
//Récupération état alarme
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

//Récupération des devices actifs de type sonde de températures
$sql = "SELECT d.id, d.name, last_update, param1 FROM device d, product p WHERE p.id=d.product_id AND p.name='myfox_temperature' AND d.active=1";
$stmt = $db->prepare($sql);
$stmt->execute();
while($row = $stmt->fetch()){
    if($row["param1"] == ""){
        continue;
    }
    $dateDebut=new Datetime($row["last_update"]);
    if(is_null($dateDebut)){
        $dateDebut=new DateTime(date('Y-m')."-01 00:00:00");
    }

    echo "https://api.myfox.me:443/v2/site/10562/device/".$row["param1"]."/data/temperature/get?dateFrom=".$dateDebut->format('Y-m-d')."T".$dateDebut->format('H:i:s')."Z&dateTo=".date('Y-m-d')."T".date('H:i:s')."Z&access_token=".$token;
    $response=file_get_contents("https://api.myfox.me:443/v2/site/10562/device/".$row["param1"]."/data/temperature/get?dateFrom=".$dateDebut->format('Y-m-d')."T".$dateDebut->format('H:i:s')."Z&dateTo=".date('Y-m-d')."T".date('H:i:s')."Z&access_token=".$token);
    $json=json_decode($response,true);
    print_r($json);
    if(!isset($json["payload"])){
        $token=getToken();
        $response=file_get_contents("https://api.myfox.me:443/v2/site/10562/device/".$row["param1"]."/data/temperature/get?dateFrom=".$dateDebut->format('Y-m-d')."T".$dateDebut->format('H:i:s')."Z&dateTo=".date('Y-m-d')."T".date('H:i:s')."Z&access_token=".$token);
        $json=json_decode($response,true);
        print_r($json);
    }
    $temp="";
    foreach($json["payload"] as $elem){
        $date=str_replace("T", " ", $elem['recordedAt']);
        $date=str_replace("Z", "", $date);

        $currentDate=new DateTime($date);
        echo "BDD=".$dateDebut->format('U')."  |  ".$currentDate->format('U');
        if($currentDate->format('U') > $dateDebut->format('U')){
            $sql = 'INSERT INTO temperature VALUES (NULL,"'.$row["name"].'","'.$date.'",'.$elem["celsius"].','.$row["id"].')';
            echo $sql;
            $stmt = $db->query($sql);
        }
        $temp=$elem["celsius"];
        if($temp != ""){
            //$sql = 'UPDATE device SET state="'.$temp.'", last_update="'.$date.'" WHERE id="'.$row["id"].'" and type="sensor"';
            //$stmt = $db->query($sql);
            Device::updateState($row["id"],$temp, $date);
        }
    }
}

?>