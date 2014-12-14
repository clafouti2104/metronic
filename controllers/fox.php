<?php
header('HTTP/1.1 200 OK');
header('Content-type: text/plain');
header('Content-disposition: attachment; filename=delete-me.txt');

include("../tools/config.php");

$output = "ok";

$value=99;
if(isset($_GET["action"])){
    if($_GET["action"] == "armed"){
        $value=1;
    }
    if($_GET["action"] == "disarmed"){
        $value=0;
    }
    if($_GET["action"] == "partial"){
        $value=2;
    }
}

//Récupération du status
$sql = 'SELECT value FROM config WHERE name="myfox_armed"';
$sqlLogLevel=$db->query($sql);
$sqlLogLevel->setFetchMode(PDO::FETCH_OBJ);
if($row = $sqlLogLevel->fetch()){
    $logLevel = $row->value;
    if($logLevel == $value){
        die('Already in the same state');
    }
}

//MAJ Status
$sql = 'UPDATE config SET value="'.$value.'" WHERE name="myfox_armed"';
$stmt = $db->query($sql);

//MAJ LOGS
$sql = 'INSERT INTO log (rfId,value,date,deviceId,id,level) VALUES ("Alarm","'.$_GET["action"].'",NOW(),21,NULL,50)';
echo $sql;
$stmt = $db->query($sql);
echo $output.$sql;
?>