<?php
include("../tools/config.php");
$GLOBALS["dbconnec"] = connectDB();
include "../models/Condition.php";

if(!isset($_POST["conditionId"])){
    echo "error";
    return "error";
}

$condition=Condition::getCondition($_POST["conditionId"]);
$condition->delete();

echo "done";
?>
