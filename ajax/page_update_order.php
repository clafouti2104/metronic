<?php
include("../tools/config.php");
$GLOBALS["dbconnec"] = connectDB();
include "../models/Page.php";

if(!isset($_POST["pages"])){
    echo "error";
    return "error";
}

$sqlUpdate="";
$pages = explode("~",$_POST["pages"]);
foreach($pages as $item){
    print_r($item);
    $item = explode(":",$item);
    if(count($item)<=1){
        continue;
    }
    if($item[1]=="undefined"){
        continue;
    }
    $pageId=$item[1];
    $position=$item[0];
    
    $sqlUpdate.="UPDATE page SET position=".$position." WHERE id=".$pageId.";";
}
echo "SQL = >".$sqlUpdate;
$GLOBALS["dbconnec"]->query($sqlUpdate);
?>
