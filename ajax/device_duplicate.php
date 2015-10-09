<?php
include("../tools/config.php");
$GLOBALS["dbconnec"] = connectDB();
include_once "../models/Device.php";
include_once "../models/MessageDevice.php";

if(!isset($_POST["deviceId"])){
    echo "error";
    return "error";
}
if(!isset($_POST["quantite"])){
    echo "error";
    return "error";
}
if(intval($_POST["quantite"]) == 0){
    echo "quantite";
    return "quantite";
}


//Récupération device
$device=Device::getDevice($_POST["deviceId"]);

for($i=1;$i<=$_POST["quantite"];$i++){
    $deviceTmp=Device::createDevice($device->name."_".$i,$device->type,NULL,$device->states,NULL,$device->ip_address,$device->model,$device->active,$device->parameters,$device->alert_lost_communication, $device->last_alert,$device->product_id,$device->param1,$device->param2,$device->param3,$device->param4,$device->param5, $device->collect, $device->incremental,$device->unite,$device->data_type);
    foreach(MessageDevice::getMessageDevicesForDevice($device->id) as $msg){
        MessageDevice::createMessageDevice($deviceTmp->id, $msg->name, NULL, $msg->value, NULL, $msg->type, $msg->active,$msg->parameters, $msg->command);
    }
}

echo "done";
?>
