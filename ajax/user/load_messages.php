<?php
include '../../tools/config.php';
include_once "../../models/MessageDevice.php";
$GLOBALS["dbconnec"] = connectDB();

$output="var messages ='';";
if(!isset($_POST["deviceId"]) || $_POST["deviceId"]==""){
    echo "error";
    return FALSE;
}

$messages=  MessageDevice::getMessageDevicesForDevice($_POST["deviceId"]);
foreach($messages as $message){
    $output .= "var messages = messages + '<option value=\"".$message->id."\">".addslashes($message->name)."</option>';";
    
}
$output .= "$('#selectCommande').empty();";
$output .= "$('#selectCommande').append(messages);";
echo $output;

?>