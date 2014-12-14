<?php
include("../tools/config.php");
$GLOBALS["dbconnec"] = connectDB();
include "../models/MessageDevice.php";

if(!isset($_POST["messageId"])){
    echo "error";
    return "error";
}

$message= MessageDevice::getMessageDevice($_POST["messageId"]);
//$message->delete();
$output="$('#msgName').val('".  addslashes($message->name)."');";
$output.="$('#msgCommand').val('".  addslashes($message->command)."');";
$output.="$('#msgType').val('".  addslashes($message->type)."');";
if($message->active == '1'){
    $output.="$('#msgActif').attr('checked','checked');";
}

echo $output;
?>
