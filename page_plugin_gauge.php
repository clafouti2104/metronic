<?php
$pluginDevice=Device::getDevice($resultat->value);
?>
<div class="cell col-lg-<?php echo $size; ?> col-md-<?php echo $size; ?> col-sm-6 col-xs-12 boxPackery itempage itempage-<?php echo $item->id; ?>" deviceid="" iditempage="<?php echo $item->id; ?>">
    <div id="gauge-<?php echo $item->id; ?>" class="gauge gauge-<?php echo $item->id; ?> dashboard-stat " iditempage="<?php echo $item->id; ?>" idDevice="<?php echo $resultat->value; ?>" min="<?php echo $details->minimum; ?>" max="<?php echo $details->maximum; ?>" titleText="<?php echo addslashes($pluginDevice->name); ?>" value="<?php echo $pluginDevice->state; ?>">
        
            
        <div class="deleteItemPage" style="margin-bottom:10px;right:10px;">
            <a class="more btnDeletePageItem" iditempage="<?php echo $item->id; ?>" data-toggle="modal" href="page.php#deleteItemPage">
              <i class="fa fa-trash-o" ></i>
            </a>						
        </div>
    </div>
</div>