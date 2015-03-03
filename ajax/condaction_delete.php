<?php
include("../tools/config.php");
$GLOBALS["dbconnec"] = connectDB();
include "../models/CondAction.php";
include "../models/ScheduleAction.php";

if(!isset($_POST["condActionId"])){
    echo "error";
    return "error";
}
if(isset($_POST["type"]) && $_POST["type"]=="schedule_task"){
    $scheduleAction=  ScheduleAction::getScheduleAction($_POST["condActionId"]);
    $scheduleAction->delete();
}else {
    $condAction=CondAction::getCondAction($_POST["condActionId"]);
    $condAction->delete();
}

echo "done";
?>
