<?php
/*
 * Récupération des infos météo d'openWeatherMap
 */

require("../tools/config.php");
require("../models/Device.php");

$GLOBALS["dbconnec"] = connectDB();

//Récupération des devices meteo actifs 
$sql = "SELECT d.id, d.name, d.param1 as lat, d.param2 as lon, d.param3 as chemin";
$sql .= " FROM device d ";
$sql .= " WHERE d.product_id IN (SELECT id FROM product WHERE name='meteo') AND d.active=1";
$stmt = $db->prepare($sql);
$stmt->execute();

$coordonates=$devices=array();
while($row = $stmt->fetch()){
    if(!in_array($row["lat"]."#".$row["lon"], $coordonates)){
        $coordonates[]=$row["lat"]."#".$row["lon"];
    }
    $devices[$row["lat"]."#".$row["lon"]][]=array(
        "id"=>$row["id"],
        "name"=>$row["name"],
        "path"=>$row["chemin"]
    );
}

//Parcours des différentes coordonnées
$urlBase="http://api.openweathermap.org/data/2.5/forecast/daily?lat=";
foreach($coordonates as $coordonate){
    $coor=explode("#", $coordonate);
    $url=$urlBase.$coor[0]."&lon=".$coor[1]."&cnt=1&mode=json&units=metric&APPID=0e859b2587166e865b09855db19c8a65";
    
    $content=file_get_contents($url);
    $results=json_decode($content,TRUE);

    if(!isset($devices[$coordonate])){
        continue;
    }
    
    foreach($devices[$coordonate] as $devicesTmp){
        $state="";
        if($devicesTmp["path"] == ""){
            continue;
        }
        
        $elems=explode(".",$devicesTmp["path"]);
        $tab="";
        foreach($elems as $elem){
            $tab.="[".$elem."]";
        }
        if($tab==""){
            continue;
        }
        
        eval('$state=$results'.$tab.';');
        if($state != ""){
            Device::updateState($devicesTmp["id"], $state);
        }
    }
}
?>
