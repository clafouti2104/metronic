<?php
include("../tools/config.php");
$GLOBALS["dbconnec"] = connectDB();
include "../models/CondAction.php";

if(!isset($_POST["condId"]) && !isset($_POST["commandId"])){
    echo "error";
    return "error";
}

if($_POST["condActionId"] != ""){
    $condAction = CondAction::getCondAction($_POST["condActionId"]);
    $condAction->type='action_message';
    $condAction->action=$_POST["commandId"];
    $condAction->update();
} else {
    $condAction = CondAction::createCondAction($_POST["condId"], 'action_message', $_POST["commandId"],NULL);
}

echo "success";
?>
