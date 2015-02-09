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
<link href="http://fonts.googleapis.com/css?family=Open+Sans:400,300,600,700&subset=all" rel="stylesheet" type="text/css"/>
<link href="/metronic/assets/global/plugins/font-awesome/css/font-awesome.min.css" rel="stylesheet" type="text/css"/>
<link href="/metronic/assets/global/plugins/simple-line-icons/simple-line-icons.min.css" rel="stylesheet" type="text/css"/>
<link href="/metronic/assets/global/plugins/bootstrap/css/bootstrap.min.css" rel="stylesheet" type="text/css"/>
<link href="/metronic/assets/global/plugins/uniform/css/uniform.default.css" rel="stylesheet" type="text/css"/>
<!-- END GLOBAL MANDATORY STYLES -->
<!-- BEGIN PAGE LEVEL STYLES -->
<link rel="stylesheet" type="text/css" href="/metronic/assets/global/plugins/bootstrap-wysihtml5/bootstrap-wysihtml5.css"/>
<link rel="stylesheet" type="text/css" href="/metronic/assets/global/plugins/bootstrap-markdown/css/bootstrap-markdown.min.css">
<link rel="stylesheet" type="text/css" href="/metronic/assets/global/plugins/bootstrap-summernote/summernote.css">
<link rel="stylesheet" type="text/css" href="/metronic/assets/global/plugins/bootstrap-toastr/toastr.min.css">
<!-- END PAGE LEVEL STYLES -->
<!-- BEGIN THEME STYLES -->
<link href="/metronic/assets/global/css/components.css" rel="stylesheet" type="text/css"/>
<link href="/metronic/assets/global/css/plugins.css" rel="stylesheet" type="text/css"/>
<link href="/metronic/assets/admin/layout/css/layout.css" rel="stylesheet" type="text/css"/>
<link id="style_color" href="/metronic/assets/admin/layout/css/themes/<?php echo $theme; ?>.css" rel="stylesheet" type="text/css"/>
<link href="/metronic/assets/admin/layout/css/custom.css" rel="stylesheet" type="text/css"/>
<!-- END THEME STYLES -->
<link rel="shortcut icon" href="favicon.ico"/>
        <script src="/metronic/assets/js/jquery-1.8.3.min.js"></script>	

<input type="hidden" id="idDevice" value="<?php echo $device->id; ?>" />
<div class="modal-content">
    <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
        <h4 class="modal-title"><i class="fa fa-flash"></i>&nbsp;&nbsp;Exécution de Commandes</h4>
    </div>
    <div class="modal-body">
        <div class="scroller" style="height:300px" data-always-visible="1" data-rail-visible1="1">
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