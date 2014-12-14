<?php
$device=Device::getDevice($item->deviceId);
if(strtolower($device->type) != "website"){
    return false;
}
$width='4';
$height=$width*66;
?>
<div class="col-md-<?php echo $width; ?> boxPackery itempage itempage-<?php echo $item->id; ?>" type="device" elementId="<?php echo $device->id; ?>" iditempage="<?php echo $item->id; ?>">
    <?php
    if($device->param1 != ""){
        $url = (substr($device->param1, 0, 4) != 'http') ? "http://".$device->param1 : $device->param1; 
    ?>    
    <iframe src="<?php echo $url; ?>" style="width:100%;height:<?php echo $height;?>px;border:0px;"></iframe>
    <?php
    }
    ?>
</div>