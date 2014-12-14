<?php
include("../tools/config.php");
$GLOBALS["dbconnec"] = connectDB();
include "../models/Page.php";

if(!isset($_POST["pageId"])){
    echo "error";
    return "error";
}

$page=Page::getPage($_POST["pageId"]);
$page->delete();

echo "done";
?>
