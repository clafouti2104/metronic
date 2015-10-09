<?php
$website = (substr($name,0,4) == "http") ? $name : "http://".$name;
$streamUrl="";
if(isset($details->cameraStream) && $details->cameraStream != ""){
	$streamUrl = (substr($details->cameraStream, 0, 1) != "/") ? "/" : "";
}
$streamImg=(isset($details->ip) && isset($details->cameraStream)) ? "http://".$details->ip.$streamUrl.$details->cameraStream : "";

?>
<div class="cell col-lg-<?php echo $size; ?> col-md-<?php echo $size; ?> col-sm-6 col-xs-12 boxPackery itempage itempage-<?php echo $item->id; ?>" deviceid="" iditempage="<?php echo $item->id; ?>">
    <div class="dashboard-stat <?php echo $bgcolor; ?>" style="<?php echo $color;if(isset($params->height)){echo "height:".$params->height."px;";} ?>">
        <img src="<?php echo $streamImg; ?>" id="<?php echo $item->deviceId; ?>" width="100%" width="100%" />
        <a class="more btnDeletePageItem" iditempage="<?php echo $item->id; ?>" data-toggle="modal" href="page.php#deleteItemPage">
          <?php echo $name; ?><i class="fa fa-trash-o" ></i>
        </a>						
    </div>
</div>