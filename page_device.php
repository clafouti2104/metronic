<?php
$color=$width=$bgcolor=$diffConso=$linkTendance="";

$width=$height='1';
$width= (isset($item->width)) ? $item->width : $width;
$height= (isset($item->height)) ? $item->height : $height;
//$height=$width*66;
$bgcolor=(isset($itemParams->color) && $itemParams->color != "") ? $itemParams->color : "blue";
$color="#FFF";

switch(strtolower($device->type)){
    case 'door' :
        if(strtolower($device->state) == "on"){
            $icon="icon-unlocked";
        } else {
            $icon="icon-locked";
        }
        break;
    case 'light' :
        if(strtolower($device->state) == "on"){
            $icon="icon-light-on";
        } else {
            $icon="icon-light-off";
        }
        break;
    case 'music' :
        $icon="fa fa-music";
        break;
    case 'sensor' :
        $icon="icon-thermometer14";
        break;
    case 'tv' :
        $icon="icon-television4";
        break;
    default :
}

?>
<div class="grid-stack-item cell cell-<?php echo $item->id; ?> itempage itempage-<?php echo $item->id; ?>" data-gs-x="<?php echo $item->position; ?>" data-gs-y="<?php echo $item->positiony; ?>" data-gs-width="<?php echo $item->width; ?>" data-gs-height="<?php echo $item->height; ?>"  type="device" elementId="<?php echo $device->id; ?>" iditempage="<?php echo $item->id; ?>">
    <div class="grid-stack-item-content dashboard-stat dashboard-stat-<?php echo $item->id; echo " ".$bgcolor; ?>" style="">
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
$moreMsg = FALSE;
$acceptedTypes = array(
    "on","off","open","close", "switch"
);
foreach($msgs as $msg){
    if($msg->active == "0"){
        continue;
    } 
    
    if(in_array(strtolower($msg->type), $acceptedTypes)){
        echo "<button class=\"btn default box-action\" type=\"message\" elementId=\"".$msg->id."\" deviceId=\"".$device->id."\" style=\"margin-right:2px;\" >".  strtoupper($msg->name)."</button>";
    } else {
        $moreMsg = TRUE;
    }
}
?>
            </div>            
        </div>				
        <div class="more ">
            &nbsp;
            <a class="btnEditPageItem" iditempage="<?php echo $item->id; ?>" href="ajax/user/itempage_edit.php?itemPageId=<?php echo $item->id; ?>" data-target="#ajaxEditPageItem" data-toggle="modal">
                <i style="float:left;color:#fff;cursor:pointer;" class="fa fa-edit"></i>
            </a>
<?php
if($moreMsg){
?>
          &nbsp;<a  class="btnMoreMessage" idDevice="<?php echo $device->id; ?>" data-toggle="modal" href="#ajaxShowMoreMessages" style="float:left;color:#fff;"><i class="fa fa-plus" ></i></a>
<?php
}
?>
          &nbsp;<a  class="btnDeletePageItem" iditempage="<?php echo $item->id; ?>" data-toggle="modal" href="page.php#deleteItemPage" style="float:right;color:#fff;"><i class="fa fa-trash-o" ></i></a>
        </div>
    </div>
</div>
