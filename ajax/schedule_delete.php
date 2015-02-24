<?php
include("../tools/config.php");
$GLOBALS["dbconnec"] = connectDB();
include "../models/Schedule.php";

if(!isset($_POST["scheduleId"])){
    echo "error";
    return "error";
}

$schedule=Schedule::getSchedule($_POST["scheduleId"]);
$schedule->delete();

echo "done";
?>
