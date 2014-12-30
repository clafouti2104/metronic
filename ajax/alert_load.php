<?php
include("../tools/config.php");
$GLOBALS["dbconnec"] = connectDB();
include("../models/Alert.php");

if(!isset($_POST["alertId"])){
    echo "error";
    return "error";
}
$output="";

$alert=Alert::getAlert($_POST["alertId"]);

$output="$('#alertOperator').val('".  addslashes($alert->operator)."');";
$output.="$('#alertValue').val('".  addslashes($alert->value)."');";
$output.="$('#alertPushingbox').val('".  addslashes($alert->notificationId)."');";

echo $output;
?>
