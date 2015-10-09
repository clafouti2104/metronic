<?php
include("../tools/config.php");
$GLOBALS["dbconnec"] = connectDB();
include_once "../models/MessageDevice.php";

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
} else {
    $output.="if($('#msgSlider').is(':checked')){ $('#msgSlider').click();}";
}
/*if($message->action == '1'){
    $output.="if($('#msgAction').is(':checked')){}else { $('#msgAction').click();}";
} else {
    $output.="if($('#msgAction').is(':checked')){ $('#msgAction').click();}";  
}*/
if($message->active == '1'){
    $output.="if($('#msgActif').is(':checked')){}else { $('#msgActif').click();}";
} else{
    $output.="if($('#msgActif').is(':checked')){ $('#msgActif').click();}";  
}
echo $output;
?>
