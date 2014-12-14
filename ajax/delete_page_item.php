<?php
include("../tools/config.php");
$GLOBALS["dbconnec"] = connectDB();
include "../models/PageItem.php";

if(!isset($_POST["iditempage"])){
    echo "error";
    return "error";
}

//Récupération du pageitem
$pageItem=PageItem::getPageItem($_POST["iditempage"]);
$pageItem->delete();
echo "success";

?>
