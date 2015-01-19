<?php
$color=$width=$bgcolor=$itemParams=$params=$diffConso=$linkTendance="";

$width='4';
$width= (isset($itemParams->width)) ? $itemParams->width : $width;
$height=$width*66;
$bgcolor=(isset($itemParams->color) && $itemParams->color != "") ? $itemParams->color : "blue";
$sliderColor=(isset($itemParams->colorSlider) && $itemParams->colorSlider != "" && $itemParams->colorSlider != "NULL") ? $itemParams->colorSlider : "yellow";
$color="#FFF";
?>
<div class="cell cell-<?php echo $item->id; ?> col-lg-<?php echo $width; ?> col-md-<?php echo $width; ?> col-sm-6 col-xs-12 boxPackery itempage itempage-<?php echo $item->id; ?>" type="device" elementId="<?php echo $device->id; ?>" iditempage="<?php echo $item->id; ?>">
    <div class="dashboard-stat dashboard-stat-<?php echo $item->id; ?> <?php echo $bgcolor; ?>" style="<?php echo $color; ?>">
        <div class="visual ">
            <i class="fa fa-play"></i>
        </div>
        <div class="details col-md-9" style="width: 85%;">
            <div class="number">
                    <?php echo $device->name; ?>
            </div>
            <div class="desc">									
                <div class="slider slider-basic slider-basic-<?php echo $item->deviceId; ?> bg-<?php echo $sliderColor; ?> stateDeviceId" elementId="<?php echo $msg->id; ?>" stateDeviceId="<?php echo $device->id; ?>" deviceId="<?php echo $device->id; ?>"></div>
            </div>
            
        </div>				
        <div class="more ">
            &nbsp;
            <a class="btnEditPageItem" iditempage="<?php echo $item->id; ?>" href="ajax/user/itempage_edit.php?itemPageId=<?php echo $item->id; ?>" data-target="#ajaxEditPageItem" data-toggle="modal">
                <i style="float:left;color:#fff;cursor:pointer;" class="fa fa-edit"></i>
            </a>
            &nbsp;<a  class="btnDeletePageItem" iditempage="<?php echo $item->id; ?>" data-toggle="modal" href="page.php#deleteItemPage" style="float:right;color:#fff;"><i class="fa fa-trash-o" ></i></a>
        </div>
    </div>
</div>
