<?php
include("../tools/config.php");
$GLOBALS["dbconnec"] = connectDB();
include "../models/ScenarioMessage.php";

if(!isset($_POST["scenarioid"]) || !isset($_POST["messages"])){
    echo "error";
    return "error";
}

$sqlUpdate="";
$items = explode("~",$_POST["messages"]);
$sqlUpdate.="DELETE FROM scenariomessage WHERE scenarioid=".$_POST["scenarioid"].";";
foreach($items as $item){
    $item = explode(":",$item);
    if(count($item)<=1){
        continue;
    }
    if($item[1]=="undefined"){
        continue;
    }
    $messageId=$item[1];
    $pause="NULL";
    if(strlen($item[1]) >= 6){
        $string=explode('-',$item[1]);
        if(count($string) > 1){
            $messageId="NULL";
            $pause=$string[1];
        }
    }
    
    $sqlUpdate.="INSERT INTO scenariomessage(scenarioid, messageid, position,pause) VALUES (".$_POST["scenarioid"].",".$messageId.",".$item[0].", ".$pause.");";
}
//echo "SQL = >".$sqlUpdate;
$GLOBALS["dbconnec"]->query($sqlUpdate);
?>
