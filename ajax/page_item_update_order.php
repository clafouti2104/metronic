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
    if(count($item)<=3){
        continue;
    }
    if($item[1]=="undefined"){
        continue;
    }
    $height=$item[1];
    $width=$item[2];
    $x=$item[3];
    $y=$item[4];

    
    $sqlUpdate.="UPDATE pageitem SET position=".$x.", positiony=".$y.", width=".$width.", height=".$height." WHERE id=".$item[0].";";
}
$GLOBALS["dbconnec"]->query($sqlUpdate);
?>
