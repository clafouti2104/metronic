<?php
$device=Device::getDevice($item->deviceId);
$msgs =MessageDevice::getMessageDevicesForDevice($item->deviceId);
$slider=false;
if(count($msgs) == 1){
    //Parcours msg
    foreach($msgs as $msg){
        $params = json_decode($msg->parameters);
        if(isset($params->slider)){
            $slider=true;
            break;
        }
    }
}

if(!$slider){
    return false;
}
$width='4';
$height=$width*66;
$bgcolor="blue";
$color="#FFF";
?>
<div class="cell col-md-<?php echo $width; ?> boxPackery itempage itempage-<?php echo $item->id; ?>" type="device" elementId="<?php echo $device->id; ?>" iditempage="<?php echo $item->id; ?>">
    <div class="dashboard-stat <?php echo $bgcolor; ?>" style="<?php echo $color; ?>">
        <div class="visual ">
            <i class="fa fa-play"></i>
        </div>
        <div class="details col-md-9" >
            <div class="number">
                    <?php echo $device->name; ?>
            </div>
            <div class="desc">									
                <div class="slider slider-basic slider-basic-<?php echo $item->deviceId; ?> bg-yellow stateDeviceId" elementId="<?php echo $msg->id; ?>" stateDeviceId="<?php echo $device->id; ?>" deviceId="<?php echo $device->id; ?>"></div>
            </div>
            
        </div>				
        <div class="more ">
          &nbsp;
          &nbsp;<a  class="btnDeletePageItem" iditempage="<?php echo $item->id; ?>" data-toggle="modal" href="page.php#deleteItemPage" style="float:right;color:#fff;"><i class="fa fa-trash-o" ></i></a>
        </div>
    </div>
</div>
