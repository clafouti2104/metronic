<?php
/*
 * Récupération des status des raspberry
 */
require('../tools/config.php');
$db = connectDB();

//Récupération des devices actifs de type raspberry
$sql = "SELECT d.id, d.name, last_update, ip_address ";
$sql .= " FROM device d ";
$sql .= " WHERE d.type='raspberry' AND d.active=1";
$stmt = $db->prepare($sql);
$stmt->execute();
while($row = $stmt->fetch()){
    if($row["ip_address"] == ""){
        continue;
    }
    $temp=file_get_contents("http://".$row["ip_address"]."/rest.php");
    if($temp == ""){
        continue;
    }
    $temp=json_decode($temp);
    if(isset($temp->temperature_internal)){
        $sql = 'UPDATE device SET state="'.$temp->temperature_internal.'", last_update=NOW() WHERE id="'.$row["id"].'"';
        $stmt = $db->query($sql);
    }
    if(isset($temp->temperature_external)){
        $sql = 'UPDATE device SET state="'.$temp->temperature_external.'", last_update=NOW() WHERE id="'.$row["id"].'"';
        $stmt = $db->query($sql);
    }
    
}
?>
