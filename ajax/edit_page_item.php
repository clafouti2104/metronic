<?php
include("../tools/config.php");
$GLOBALS["dbconnec"] = connectDB();
include "../models/Tuile.php";
include "../models/Device.php";
include "../models/Scenario.php";
include "../models/Chart.php";
include "../models/PageItem.php";

if(!isset($_POST["pageItemid"]) || !isset($_POST["width"]) || !isset($_POST["color"])){
    echo "Une erreur est survenue";
    return FALSE;
}

$_POST["color"] = ($_POST["color"] == "") ? NULL : $_POST["color"];
$_POST["width"] = ($_POST["width"] == "") ? NULL : $_POST["width"];

$pageItem=PageItem::getPageItem($_POST["pageItemid"]);
$params = (is_object($pageItem) && $pageItem->params) ? json_decode($pageItem->params,TRUE) : "";
if($params == ""){
    $params = array(
        "color"=>$_POST["color"],
        "width"=>$_POST["width"]
    );
} else {
    if($params["color"]){
        $params["color"] = $_POST["color"];
    }
    if($params["width"]){
        $params["width"] = $_POST["width"];
    }
}

$pageItem->params = json_encode($params);
$pageItem->update();

echo "success";
?>
