<?php
$website = (substr($name,0,4) == "http") ? $name : "http://".$name;

?>
<div class="col-lg-<?php echo $size; ?> col-md-<?php echo $size; ?> col-sm-6 col-xs-12 boxPackery itempage itempage-<?php echo $item->id; ?>" deviceid="" iditempage="<?php echo $item->id; ?>">
    <div class="dashboard-stat <?php echo $bgcolor; ?>" style="<?php echo $color;if(isset($params->height)){echo "height:".$params->height."px;";} ?>">
        <iframe style="width: 100%;height: 100%;" src="<?php echo $website; ?>"></iframe>
        <a class="more btnDeletePageItem" iditempage="<?php echo $item->id; ?>" data-toggle="modal" href="page.php#deleteItemPage">
          <?php echo $name; ?><i class="fa fa-trash-o" ></i>
        </a>						
    </div>
</div>