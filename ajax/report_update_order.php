<?php
include("../tools/config.php");
$GLOBALS["dbconnec"] = connectDB();
include "../models/ReportDevice.php";

if(!isset($_POST["reportid"]) || !isset($_POST["devices"]) || !isset($_POST["messages"])){
    echo "error";
    return "error";
}

$sqlUpdate="";
$items = explode("~",$_POST["devices"]);
$itemsMsg = explode("~",$_POST["messages"]);
print_r($itemsMsg);

$sqlUpdate.="DELETE FROM reportdevice WHERE reportid=".$_POST["reportid"].";";
$i=0;
foreach($items as $item){
    //$itemMsg = explode(":",$itemsMsg[$i]);
    //var_dump($itemMsg[1]);
    $item = explode(":",$item);
    
    if(count($item)<=1){
        continue;
    }
    if($item[1]=="undefined"){
        continue;
    }
    $sqlUpdate.="INSERT INTO reportdevice(reportid, deviceid, position) VALUES (".$_POST["reportid"].",".$item[1].",".$item[0].");";
    $i++;
}
var_dump($sqlUpdate);
$GLOBALS["dbconnec"]->query($sqlUpdate);
?>
