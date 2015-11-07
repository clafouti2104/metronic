<?php
$color=$width=$name=$percent=$params=$linkTendance="";
$tuile=Tuile::getTuile($item->tuileId);
$tuileDevice=Device::getDevice($tuile->deviceid);

if($item->params != "" && $item->params !="Array"){
    $params=json_decode($item->params);
    if(isset($params->period)){
        $linkTendance= " data-target=\"#ajaxTendance\" data-toggle=\"modal\" ";
    }
}
$color=(isset($params->color)) ? $params->color : $tuile->color; 
$width=(isset($params->width)) ? $params->width : 2; 
$name=(isset($params->description)) ? $params->description : $tuile->name; 
$refreshClass=(isset($params->description)) ? "" : "stateDeviceId"; 
//Récupération des consommations
$state=(isset($params->period)) ? History::getCountForPeriod($tuile->deviceid,$params->period)." ".$tuileDevice->unite : $tuileDevice->showState(); 
$lastStateNow=(isset($params->period)) ? History::getCountForLastPeriodUntilNow($tuile->deviceid,$params->period) : 0; 

if($item->params != "" && isset($params->period) && $lastStateNow != 0){
    $percent = ($state/$lastStateNow)*100;
    $percent = number_format($percent,0);
    //$name .= " | ".$percent."%";
    $icon = ($percent > 100) ? " fa-thumbs-down " : " fa-thumbs-up ";
    $diffConso = ($percent > 100) ? "+".($percent-100) : "-".(100-$percent); 
    $diffConso = ($percent == 100) ? "0" : $diffConso;
}
?>
<div class="grid-stack-item cell cell-<?php echo $item->id; ?> boxPackery <?php if(isset($params->period)){echo "popupTendance ";} ?> itempage itempage-<?php echo $item->id; ?>" <?php echo $linkTendance; ?> href="ajax/user/tendance.php?deviceId=<?php echo $tuileDevice->id; ?>"  data-gs-x="<?php echo $item->position; ?>" data-gs-y="<?php echo $item->positiony; ?>" data-gs-width="<?php echo $item->width; ?>" data-gs-height="<?php echo $item->height; ?>"  deviceid="<?php echo $tuile->deviceid; ?>" iditempage="<?php echo $item->id; ?>">
    <div class="grid-stack-item-content dashboard-stat dashboard-stat-<?php echo $item->id; ?> <?php echo $color; ?>">
        <div class="visual">
            <?php
            if(isset($icon)){
                echo "<i class=\"fa ".$icon."\"></i>";
            } else {
                switch(strtolower($tuileDevice->type)){
                    case 'sensor' :
                        $icon="icon-thermometer14";
                        break;
                    case 'tv' :
                        $icon="icon-television4";
                        break;
                    case 'light' :
                        $icon="icon-light-on";
                        if(strtolower($tuileDevice->state) == "on"){
                            $icon="icon-light-on";
                        } else {
                            $icon="icon-light-off";
                        }
                        break;
                    default :
                }
                if(isset($icon)){
                    echo "<i class=\"".$icon." icon-status-".$device->id."\" type=\"".strtolower($tuileDevice->type)."\" statedeviceid=\"".$device->id."\"></i>";
                }
            }
            ?>
        </div>
        <div class="details">
                <div class="number <?php echo $refreshClass; ?> stateDeviceId-tile-<?php echo $tuileDevice->id; ?> stateDeviceId-<?php echo $tuileDevice->id; ?>" stateDeviceId="<?php echo $tuileDevice->id; ?>">
                    <?php echo $state; ?>
                </div>
        </div>
        <div class="more ">
            <a class="btnEditPageItem" iditempage="<?php echo $item->id; ?>" href="ajax/user/itempage_edit.php?itemPageId=<?php echo $item->id; ?>" data-target="#ajaxEditPageItem" data-toggle="modal">
                <i style="float:left;color:#fff;cursor:pointer;" class="fa fa-edit"></i>
            </a>
            &nbsp;<?php echo $name;if(isset($diffConso)){echo "| ".$diffConso."%";} ?>
            <a class="btnDeletePageItem" style="float:right;color:#fff;" iditempage="<?php echo $item->id; ?>" data-toggle="modal" href="page.php#deleteItemPage">
                <i class="fa fa-trash-o" ></i>
            </a>
        </div>
    </div>
</div>