<?php
$tuile=Tuile::getTuile($item->tuileId);
$tuileDevice=Device::getDevice($tuile->deviceid);
$linkTendance="";
if($item->params != ""){
    $params=json_decode($item->params);
    $linkTendance= " data-target=\"#ajaxTendance\" data-toggle=\"modal\" ";
}
$color=(isset($params->color)) ? $params->color : $tuile->color; 
$name=(isset($params->description)) ? $params->description : $tuile->name; 
$refreshClass=(isset($params->description)) ? "" : "stateDeviceId"; 
//Récupération des consommations
$state=(isset($params->description)) ? History::getCountForPeriod($tuile->deviceid,$params->period)." ".$tuileDevice->unite : $tuileDevice->showState(); 
$lastStateNow=(isset($params->description)) ? History::getCountForLastPeriodUntilNow($tuile->deviceid,$params->period) : 0; 

$percent="";
if($item->params != "" && $lastStateNow != 0){
    $percent = ($state/$lastStateNow)*100;
    $percent = number_format($percent,0);
    $name .= " | ".$percent."%";
}
?>
<div class="cell col-lg-2 col-md-2 col-sm-6 col-xs-12 boxPackery <?php if($item->params != ""){echo "popupTendance";} ?> itempage itempage-<?php echo $item->id; ?>" <?php echo $linkTendance; ?> href="ajax/user/tendance.php?deviceId=<?php echo $tuileDevice->id; ?>" deviceid="<?php echo $tuile->deviceid; ?>" iditempage="<?php echo $item->id; ?>">
    <div class="dashboard-stat <?php echo $color; ?>">
        <div class="visual">
            
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