<?php
include("../tools/config.php");
$GLOBALS["dbconnec"] = connectDB();
include "../models/Tuile.php";
include "../models/Device.php";
include "../models/Scenario.php";
include "../models/Chart.php";
include "../models/PageItem.php";

if(!isset($_POST["pageid"]) || !isset($_POST["scenarioid"]) || !isset($_POST["chartid"]) || !isset($_POST["tuileid"]) || !isset($_POST["listeid"]) || !isset($_POST["deviceid"]) || !isset($_POST["plugin"]) || !isset($_POST["width"])){
    echo "Une erreur est survenue";
    return FALSE;
}
$_POST["tuileid"] = ($_POST["tuileid"] == "") ? NULL : $_POST["tuileid"];
$_POST["scenarioid"] = ($_POST["scenarioid"] == "") ? NULL : $_POST["scenarioid"];
$_POST["chartid"] = ($_POST["chartid"] == "") ? NULL : $_POST["chartid"];
$_POST["listeid"] = ($_POST["listeid"] == "") ? NULL : $_POST["listeid"];
$_POST["deviceid"] = ($_POST["deviceid"] == "") ? NULL : $_POST["deviceid"];
$_POST["plugin"] = ($_POST["plugin"] == "") ? NULL : $_POST["plugin"];
$_POST["pluginType"] = ($_POST["pluginType"] == "") ? NULL : $_POST["pluginType"];
$_POST["incremental"] = ($_POST["incremental"] == "true") ? TRUE : FALSE;
$_POST["description"] = ($_POST["description"] == "") ? NULL : $_POST["description"];
$_POST["color"] = ($_POST["color"] == "") ? NULL : $_POST["color"];
$_POST["period"] = ($_POST["period"] == "") ? NULL : $_POST["period"];
$_POST["width"] = ($_POST["width"] == "") ? NULL : $_POST["width"];
$_POST["height"] = ($_POST["height"] == "") ? NULL : $_POST["height"];

$params="";
//Tuile Incremental ==> Consommation
if($_POST["incremental"]){
    $params=json_encode(array(
        'color'=>$_POST["color"],
        'description'=>$_POST["description"],
        'width'=>$_POST["width"],
        'period'=>$_POST["period"]
    ));
}
//Slider
if($_POST["slider"] == "true"){
    $params=json_encode(array(
        'color'=>$_POST["color"],
        //'description'=>$_POST["description"],
        'width'=>$_POST["width"],
        'colorSlider'=>$_POST["colorSlider"]
    ));
}
if($_POST["plugin"] != ""){
    $params = json_encode(array(
        "plugin"=>$_POST["pluginType"],
        "color"=>$_POST["color"],
        "width"=>$_POST["width"],
        "height"=>$_POST["height"],
        "id"=>$_POST["plugin"]
    ));
}

if( $_POST["scenarioid"] != "" || $_POST["chartid"] != "" || $_POST["tuileid"] != "" || $_POST["listeid"] != "" || $_POST["deviceid"] != ""){
    //Vérifie que le device n'est pas déjà présent sur cette page
    if(PageItem::PageItemExistsForPage($_POST["pageid"], $_POST["tuileid"], $_POST["scenarioid"], $_POST["chartid"], $_POST["listeid"], $_POST["deviceid"])){
        echo "L'objet existe déjà pour cette page";
        return FALSE;
    }
}

//Récupération de la derniere position
$nextPosition=PageItem::getNextPositionForPage($_POST["pageid"]);
//Création du PageItem
PageItem::createPageItem($_POST["pageid"], $nextPosition, $_POST["tuileid"], $_POST["scenarioid"], $_POST["chartid"], $_POST["listeid"], $params, $_POST["deviceid"]);

echo "success";
?>
