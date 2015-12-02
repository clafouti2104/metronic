<?php
$website = (substr($name,0,4) == "http") ? $name : "http://".$name;
$streamUrl="";
if(isset($details->cameraStream) && $details->cameraStream != ""){
	$streamUrl = (substr($details->cameraStream, 0, 1) != "/") ? "/" : "";
}
$streamImg=(isset($details->ip) && isset($details->cameraStream)) ? "http://".$details->ip.$streamUrl.$details->cameraStream : "";

?>
<div class="grid-stack-item cell cell-<?php echo $item->id; ?> itempage itempage-<?php echo $item->id; ?>" data-gs-x="<?php echo $item->position; ?>" data-gs-y="<?php echo $item->positiony; ?>" data-gs-width="<?php echo $item->width; ?>" data-gs-height="<?php echo $item->height; ?>"  iditempage="<?php echo $item->id; ?>">
    <div class="grid-stack-item-content dashboard-stat dashboard-stat-<?php echo $item->id; echo " ".$bgcolor; ?>" style="">
    	<img class="dk-camera dk-camera-<?php echo $item->id;?>" idItem="<?php echo $item->id;?>" src="<?php echo $streamImg; ?>" id="<?php echo $item->deviceId; ?>" width="100%" width="100%" />
        <a class="more btnDeletePageItem" iditempage="<?php echo $item->id; ?>" data-toggle="modal" href="page.php#deleteItemPage">
          <?php echo $name; ?><i class="fa fa-trash-o" ></i>
        </a>
    </div>
</div>