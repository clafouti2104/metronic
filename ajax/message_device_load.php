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
$params = json_decode($message->parameters);
if(isset($params->slider)){    
    $output.="if($('#msgSlider').is(':checked')){}else { $('#msgSlider').click();}";
}
if($message->active == '1'){
    $output.="$('#msgActif').attr('checked','checked');";
}

echo $output;
?>
