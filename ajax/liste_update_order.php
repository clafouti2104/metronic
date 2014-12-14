<?php
include("../tools/config.php");
$GLOBALS["dbconnec"] = connectDB();
include "../models/ListeMessage.php";

if(!isset($_POST["listeid"]) || !isset($_POST["devices"]) || !isset($_POST["messages"])){
    echo "error";
    return "error";
}

$sqlUpdate="";
$items = explode("~",$_POST["devices"]);
$itemsMsg = explode("~",$_POST["messages"]);
print_r($itemsMsg);

$sqlUpdate.="DELETE FROM listemessage WHERE listeid=".$_POST["listeid"].";";
$i=0;
foreach($items as $item){
    $itemMsg = explode(":",$itemsMsg[$i]);
    $messageId = (!isset($itemMsg[1]) || $itemMsg[1]=="" || $itemMsg[1]=="null") ? 0 : $itemMsg[1];
    var_dump($itemMsg[1]);
    $item = explode(":",$item);
    
    if(count($item)<=1){
        continue;
    }
    if($item[1]=="undefined"){
        continue;
    }
    $sqlUpdate.="INSERT INTO listemessage(listeid, deviceid, position,messageid) VALUES (".$_POST["listeid"].",".$item[1].",".$item[0].",".$messageId.");";
    $i++;
}
var_dump($sqlUpdate);
$GLOBALS["dbconnec"]->query($sqlUpdate);
?>
