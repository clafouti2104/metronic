<?php
$color=$width=$bgcolor=$diffConso=$linkTendance="";

$width='4';
$width= (isset($itemParams->width)) ? $itemParams->width : $width;
$height=$width*66;
$bgcolor=(isset($itemParams->color) && $itemParams->color != "") ? $itemParams->color : "blue";
$color="#FFF";

switch(strtolower($device->type)){
    case 'sensor' :
        $icon="icon-thermometer14";
        break;
    case 'light' :
        if(strtolower($device->state) == "on"){
            $icon="icon-light-on";
        } else {
            $icon="icon-light-off";
        }
        break;
    default :
}

?>
<div class="cell cell-<?php echo $item->id; ?> col-lg-<?php echo $width; ?> col-md-<?php echo $width; ?> col-sm-6 col-xs-12 boxPackery itempage itempage-<?php echo $item->id; ?>" type="device" elementId="<?php echo $device->id; ?>" iditempage="<?php echo $item->id; ?>">
    <div class="dashboard-stat dashboard-stat-<?php echo $item->id; ?> <?php echo $bgcolor; ?>" style="">
        <div class="visual ">
<?php 
if(isset($icon)){
    echo "<i class=\"".$icon." stateDeviceId icon-status-".$device->id."\" type=\"".strtolower($device->type)."\" statedeviceid=\"".$device->id."\"></i>";
}
?>
        </div>
        <div class="details col-md-9" style="width: 85%;">
            <div class="number">
                <?php echo $device->name; ?>
            </div>
            <div class="desc">	
<?php

?>
                <button class="btn default" >ON</button>
                <button class="btn default" >OFF</button>
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
