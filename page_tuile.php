<?php
$tuile=Tuile::getTuile($item->tuileId);
$tuileDevice=Device::getDevice($tuile->deviceid);
$linkTendance=$percent="";
$name=(isset($params->description)) ? $params->description : $tuile->name; 
$state=(isset($params->description)) ? History::getCountForPeriod($tuile->deviceid,$params->period)." ".$tuileDevice->unite : $tuileDevice->showState();
$color=(isset($params->color)) ? $params->color : $tuile->color; 
$width=(isset($params->width)) ? $params->width : 2; 
$refreshClass=(isset($params->description)) ? "" : "stateDeviceId"; 

if($item->params != "" && $item->params !="Array"){
    $params=json_decode($item->params);
    $linkTendance= " data-target=\"#ajaxTendance\" data-toggle=\"modal\" ";
    $lastStateNow=History::getCountForLastPeriodUntilNow($tuile->deviceid,$params->period); 
    
    if($lastStateNow != 0){
        $percent = ($state/$lastStateNow)*100;
        $percent = number_format($percent,0);
        $name .= " | ".$percent."%";
        $icon = ($percent > 100) ? " fa-thumbs-down " : " fa-thumbs-up ";
    }
}

//Récupération des consommations
?>
<div class="cell col-lg-<?php echo $width; ?> col-md-<?php echo $width; ?> col-sm-6 col-xs-12 boxPackery <?php if($item->params != "" && $item->params !="Array"){echo "popupTendance ";} ?> itempage itempage-<?php echo $item->id; ?>" <?php echo $linkTendance; ?> href="ajax/user/tendance.php?deviceId=<?php echo $tuileDevice->id; ?>" deviceid="<?php echo $tuile->deviceid; ?>" iditempage="<?php echo $item->id; ?>">
    <div class="dashboard-stat <?php echo $color; ?>">
        <div class="visual">
            <?php
            if(isset($icon)){
                echo "<i class=\"fa ".$icon."\"></i>";
            }
            ?>
        </div>
        <div class="details">
                <div class="number <?php echo $refreshClass; ?> stateDeviceId-tile-<?php echo $tuileDevice->id; ?> stateDeviceId-<?php echo $tuileDevice->id; ?>" stateDeviceId="<?php echo $tuileDevice->id; ?>">
                    <?php echo $state; ?>
                </div>
        </div>
        <a class="more btnDeletePageItem" iditempage="<?php echo $item->id; ?>" data-toggle="modal" href="page.php#deleteItemPage">
          <?php echo $name; ?><i class="fa fa-trash-o" ></i>
        </a>						
    </div>
</div>