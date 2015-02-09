<?php
include '../../tools/config.php';
include_once "../../models/PageItem.php";
include_once "../../models/Scenario.php";
include_once "../../models/MessageDevice.php";
include_once "../../models/Device.php";

$GLOBALS["dbconnec"] = connectDB();
if(!isset($_GET["idDevice"])){
    echo "ERROR";
    exit;
}
$device=Device::getDevice($_GET["idDevice"]);
$messages = MessageDevice::getMessageDevicesForDevice($device->id);
?>
<input type="hidden" id="idDevice" value="<?php echo $device->id; ?>" />
<div class="modal-content">
    <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
        <h4 class="modal-title"><i class="fa fa-flash"></i>&nbsp;&nbsp;Exécution de Commandes</h4>
    </div>
    <div class="modal-body">
        <div id="alert" class=""></div>
        <div class="row">
<?php 
foreach($messages as $message){
    $icon = "fa fa-gears";
    switch(strtolower($message->type)){
        case 'home':
            $icon = "fa fa-home";
            break;
        case 'mute':
            $icon = "fa fa-microphone-slash ";
            break;
        case 'next':
            $icon = "fa fa-step-forward ";
            break;
        case 'play':
            $icon = "fa fa-play";
            break;
        case 'power':
            $icon = "fa fa-power-off";
            break;
        case 'previous':
            $icon = "fa fa-step-backward ";
            break;
        case 'stop':
            $icon = "fa fa-stop";
            break;
        case 'vol_dec':
            $icon = "fa fa-volume-off ";
            break;
        case 'vol_inc':
            $icon = "fa fa-volume-up ";
            break;
        default:
    }
    //echo "<div class=\"col-md-4\">";
    echo "<a class=\"icon-btn box-action2\" type=\"message\" elementId=\"".$message->id."\" deviceId=\"".$message->deviceId."\" href=\"#\" >";
    echo "<i class=\"$icon\"></i>";
    echo "<div> ".$message->name." </div>";
    echo "</a>";
    //echo "</div>";
}
?>
        </div>
    </div>
    <!--<div class="modal-footer">
        <button type="button" class="btn btn-primary btnSubmitEditPageItem">Modifier</button>
    </div>-->
</div>
            <!-- /.modal-content -->
<script src="assets/global/plugins/jquery-1.11.0.min.js" type="text/javascript"></script>
<script type="text/javascript">
$( document ).ready(function() {
    $('.box-action2:visible').bind('click',function(e){
        //console.debug($(this).attr('class'));
        $.ajax({
            url: "ajax/action/execute.php",
            type: "POST",
            data: {
               type:  encodeURIComponent($(this).attr('type')),
               elementId: $(this).attr('elementId')
            },
            error: function(data){
                toastr.error("Une erreur est survenue");
            },
            complete: function(data){
                toastr.success("Action exécutée");
            }
        });
    });
});
</script>