<?php
include '../../tools/config.php';
include_once "../../models/PageItem.php";
include_once "../../models/Scenario.php";
include_once "../../models/MessageDevice.php";
include_once "../../models/Device.php";

$GLOBALS["dbconnec"] = connectDB();
if(!isset($_GET["idDevice"])){
    echo "ERROR";
    exit;
}
$output="$('.contentMoreMessage').empty();";
$output.="$('.modalTitleMoreMessage').empty();";
$output.="var data='';";
$device=Device::getDevice($_GET["idDevice"]);
$output.="$('.modalTitleMoreMessage').append('".  addslashes($device->name)."');";
$messages = MessageDevice::getMessageDevicesForDevice($device->id);
foreach($messages as $message){
    $icon = "fa fa-gears";
    switch(strtolower($message->type)){
        case 'home':
            $icon = "fa fa-home";
            break;
        case 'mute':
            $icon = "fa fa-microphone-slash ";
            break;
        case 'next':
            $icon = "fa fa-step-forward ";
            break;
        case 'play':
            $icon = "fa fa-play";
            break;
        case 'power':
            $icon = "fa fa-power-off";
            break;
        case 'previous':
            $icon = "fa fa-step-backward ";
            break;
        case 'stop':
            $icon = "fa fa-stop";
            break;
        case 'vol_dec':
            $icon = "fa fa-volume-off ";
            break;
        case 'vol_inc':
            $icon = "fa fa-volume-up ";
            break;
        default:
    }
    //echo "<div class=\"col-md-4\">";
    $output.= "var data=data + '<a class=\"icon-btn box-action\" onclick=\"executeAction(NULL,$message->id);\" type=\"message\" elementId=\"".$message->id."\" deviceId=\"".$message->deviceId."\" href=\"#\" >';";
    $output.= "var data=data + '<i class=\"$icon\"></i>';";
    $output.= "var data=data + '<div> ".addslashes($message->name)." </div>';";
    $output.= "var data=data + '</a>';";
    //echo "</div>";
}
$output .= "$('.contentMoreMessage').append(data);";
echo $output;
?>
