<?php
include("../tools/config.php");
$GLOBALS["dbconnec"] = connectDB();
include "../models/CondAction.php";

if(!isset($_POST["condActionId"])){
    echo "error";
    return "error";
}

$condAction=CondAction::getCondAction($_POST["condActionId"]);
$condAction->delete();

echo "done";
?>
