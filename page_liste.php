<?php
$liste=Liste::getListe($item->listeId);
$listeMessages=  ListeMessage::getListeMessagesForListe($liste->id);
$icon=($liste->icon == "") ? "fa-play-circle" : $liste->icon;
?>
<div class="col-md-<?php echo $liste->size; ?> boxPackery itempage itempage-<?php echo $item->id; ?>" type="liste" elementId="<?php echo $liste->id; ?>" iditempage="<?php echo $item->id; ?>">
    <div class="portlet box <?php echo $liste->color; ?> tabbable">
        <div class="portlet-title">
            <div class="caption">
                <?php echo $liste->name; ?>
            </div>
        </div>
        <div class="portlet-body">
            <div class="tab-content">
                <div id="portlet_tab1" class="tab-pane active">
                <table class="table table-striped table-hover">
                    <?php
                    
                    foreach($listeMessages as $listeMessageTmp){
                        $deviceTmp=Device::getDevice($listeMessageTmp->deviceid);
                        if(!is_null($listeMessageTmp->messageid) && $listeMessageTmp->messageid != "0"){
                            $msg=MessageDevice::getMessageDevice($listeMessageTmp->messageid);
                            $elem = " - ".ucwords($msg->name)."<a href=\"#\" class=\"btn green box-action\" type=\"message\" elementId=\"".$listeMessageTmp->messageid."\" deviceId=\"".$deviceTmp->id."\" style=\"float:right;\"><i class=\"fa fa-play\"></i></a>";
                        } else {
                            $messageDevices=MessageDevice::getMessageDevicesForDevice($listeMessageTmp->deviceid, TRUE);
                            if(count($messageDevices) >= 2){
                                if(strtolower($deviceTmp->state) == "on" || strtolower($deviceTmp->state) == "off"){
                                    $checked=(strtolower($deviceTmp->state)=="on") ? " checked " : "";
                                    $elem="<input type=\"checkbox\" class=\"make-switch make-switch-".$deviceTmp->id." stateDeviceId action-light-".$deviceTmp->id."\" ".$checked." data-on-color=\"success\" data-off-color=\"danger\"  stateDeviceId=\"".$deviceTmp->id."\" deviceId=\"".$deviceTmp->id."\" style=\"float:right;\">";
                                } else {
                                    $msgIdOn=$msgIdOff=0;
                                    foreach($messageDevices as $messageDevice){
                                        if(strtolower($messageDevice->type)=="on"){
                                            $msgIdOn=$messageDevice->id;
                                        }
                                        if(strtolower($messageDevice->type)=="off"){
                                            $msgIdOff=$messageDevice->id;
                                        }
                                    }
                                    $elem = "<a href=\"#\" class=\"btn green box-action\" type=\"message\" elementId=\"".$msgIdOn."\" deviceId=\"".$deviceTmp->id."\" style=\"float:right;\"><i class=\"fa fa-play\"></i></a>";
                                    $elem .= "<a href=\"#\" class=\"btn grey-cascade box-action\" type=\"message\" elementId=\"".$msgIdOff."\" deviceId=\"".$deviceTmp->id."\" style=\"float:right;\"><i class=\"fa fa-pause\"></i></a>";
                                }
                            } elseif(count($messageDevices) == 1){
                                $elem = "<a href=\"#\" class=\"btn green\" type=\"message\" elementId=\"".$deviceTmp->id."\" deviceId=\"".$deviceTmp->id."\" style=\"float:right;\"><i class=\"fa fa-play\"></i></a>";
                            } elseif(count($messageDevices) == 0) {
                                if(strtolower($deviceTmp->state) == "on" || strtolower($deviceTmp->state) == "off"){
                                    $class=(strtolower($deviceTmp->state) == "on") ? "success" : "danger";
                                    $elem = "<span class=\"badge badge-".$class." stateDeviceId stateDeviceId-badge-".$deviceTmp->id." stateDeviceId-".$deviceTmp->id."\" style=\"float:right;\" stateDeviceId=\"".$deviceTmp->id."\">&nbsp;&nbsp;&nbsp;</span>";
                                }else{
                                    $elem = "<span class=\"badge badge-success stateDeviceId stateDeviceId-".$deviceTmp->id."\" style=\"float:right;\" stateDeviceId=\"".$deviceTmp->id."\"> ".$deviceTmp->showState()." </span>";
                                }
                            }
                        }
                        
                        echo "<tr>";
                        echo "<td class=\"action \">";
                        echo ucwords($deviceTmp->name);
                        echo $elem;
                        echo "</td>";
                        echo "</tr>";
                    }
                    ?>
                </table>
                <a  class="btnDeletePageItem" iditempage="<?php echo $item->id; ?>" data-toggle="modal" href="page.php#deleteItemPage" style="float:right;color:#fff;margin-top:-18px;margin-right:8px;"><i class="fa fa-trash-o" ></i></a>
                </div>
            </div>
        </div>						
    </div>
</div>