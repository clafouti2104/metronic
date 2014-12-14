<?php
include("../tools/config.php");
$GLOBALS["dbconnec"] = connectDB();
include "../models/PageItem.php";

if(!isset($_POST["pageid"]) || !isset($_POST["params"])){
    echo "error";
    return "error";
}

$sqlUpdate="";
$items = explode("~",$_POST["params"]);
foreach($items as $item){
    $item = explode(":",$item);
    if(count($item)<=1){
        continue;
    }
    if($item[1]=="undefined"){
        continue;
    }
    
    $sqlUpdate.="UPDATE pageitem SET position=".$item[0]." WHERE id=".$item[1].";";
}
$GLOBALS["dbconnec"]->query($sqlUpdate);
?>
