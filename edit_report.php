<?php
$includeJS[] = "/assets/global/plugins/fuelux/js/spinner.min.js";   
$includeJS[] = "/assets/global/plugins/bootstrap-wizard/jquery.bootstrap.wizard.min.js";   
$includeJS[] = "/assets/admin/pages/scripts/form-wizard.js";   
$includeJS[] = "/assets/global/plugins/select2/select2.min.js";   
$includeJS[] = "/assets/global/plugins/jquery-validation/js/jquery.validate.min.js";   
$includeJS[] = "/assets/global/plugins/jquery-validation/js/additional-methods.min.js"; 
$includeJS[] = "/assets/global/plugins/jquery-nestable/jquery.nestable.js"; 
//$includeJS[] = "/assets/admin/pages/scripts/ui-nestable.js"; 

$includeCSS[] = "/assets/global/plugins/select2/select2.css";   
$includeCSS[] = "/assets/global/plugins/jquery-nestable/jquery.nestable.css";   

include "modules/header.php";
include "modules/sidebar.php";

$GLOBALS["dbconnec"] = connectDB();
include_once "models/Report.php";
include_once "models/ReportDevice.php";
include_once "models/Chart.php";
include_once "models/MessageDevice.php";

$charts= Chart::getCharts();
$error="";
$periods=array();
$periods['1']="jour";
$periods['2']="semaine";
$periods['3']="mois";
$periods['4']="annee";

$isPost=FALSE;
if(isset($_POST["formname"]) && $_POST["formname"]=="editreport"){
    $isPost=TRUE;
}

if($isPost && isset($_POST["idreport"])){
    $name= $_POST["name"];
    $description= $_POST["description"];
    $period= $_POST["period"];
    $chart= (isset($_POST["chart"])) ? 1 : 0;
    $email= ($_POST["email"] == "" ) ? "" : $_POST["email"];
    $_POST["email"]= ($_POST["email"] == "" ) ? "" : $_POST["email"];
    $idReport=$_POST["idreport"];
    $report = Report::getReport($idReport);
} else {
    $idReport=0;
    $txtMode="Création";
    $txtModeDesc="Création d'une report";
    if(isset($_GET["idReport"])){
        $idReport=$_GET["idReport"];
        $report = Report::getReport($idReport);
    } 
    $name= (!is_object($report)) ? NULL : $report->name;
    $chart= (!is_object($report)) ? NULL : $report->chart;
    $description= (!is_object($report)) ? NULL : $report->description;
    $period= (!is_object($report)) ? NULL : $report->period;
    $email= (!is_object($report)) ? NULL : $report->contacts;
}

if($isPost){
    //Controle
    if($_POST["name"] == ""){
        $error="Veuillez renseigner le nom";
    }
    
    $_POST["chart"] = (isset($_POST["chart"])) ? 1 : 0;
    if($error == ""){
        if($_POST["idreport"]==0){
            $report=Report::createReport($_POST["name"], $_POST["description"], $_POST["chart"],$_POST["email"], $_POST["period"]);
            $idReport=$report->id;
            $info="Le rapport a été créé";
        } else {
            $sql="UPDATE report SET name='".$_POST["name"]."', description='".$_POST["description"]."', period='".$_POST["period"]."', size=".$_POST["size"].", icon='".$_POST["icon"]."'";
            $sql.=" WHERE id=".$_POST["idreport"];
            $report->name=$_POST["name"];
            $report->description=$_POST["description"];
            $report->contacts=$_POST["email"];
            $report->period=$_POST["period"];
            $report->chart=$_POST["chart"];
            $report->update();
            

            $stmt = $GLOBALS["dbconnec"]->exec($sql);
            $info="La rapport a été modifié";
        }
    }
}

if(isset($idReport) && $idReport > 0){
    $reportDevices = ReportDevice::getReportDevicesForReport($idReport);
    $txtMode="Edition";
    $txtModeDesc="Edition d'un rapport";
}
?>
<!-- BEGIN PAGE -->
<div class="page-content-wrapper">
<div class="page-content">
    <div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <!-- BEGIN PAGE TITLE & BREADCRUMB-->			
            <h3 class="page-title">
                Report				
                <small><?php echo $txtModeDesc; ?></small>
            </h3>
            <!--<a href="edit_device.php" style="float:right;margin: 20px 0 15px;"><button class="btn green" type="button"><i class="icon-plus"></i>&nbsp;Ajouter un device</button></a>-->
            <ul class="page-breadcrumb breadcrumb">
                <!--<li class="btn-group">
                    <button class="btn btn-primary" type="button" onclick="javascript:location.href='edit_report.php';"><i class="fa fa-plus"></i>Ajouter une report</button>
                </li>-->
                <li>
                    <i class="fa fa-home"></i>
                    <a href="index.php">Admin</a>
                    <i class="fa fa-angle-right"></i>
                </li>
                <li>   
                    <a href="admin_report.php">Liste des rapports</a>
                    <i class="fa fa-angle-right"></i>
                </li>
                <li>
                    <a href="#"><?php echo $txtMode; ?></a>
                    <i class="fa fa-angle-right"></i>
                </li>
            </ul>
            <?php if(isset($info)){echo "<div class=\"alert alert-success\">".$info."</div>";}?>
            <?php if($error!=""){echo "<div class=\"alert alert-danger\">".$error."</div>";}?>
            <!-- END PAGE TITLE & BREADCRUMB-->
        </div>
    </div>
    <div class="row">
        <form class="form-horizontal" method="POST" action="edit_report.php">
        <div class="portlet">
            <div class="portlet-title">
                <div class="caption">
                    <i class="fa fa-gears"></i>
                    Gestion
                </div>
                <div class="actions btn-set">
                    <a href="admin_report.php" class="btn default">
                        <i class="fa fa-angle-left"></i>
                        Retour
                    </a>
                    <button class="btn green" type="submit">
                        <i class="fa fa-check"></i>
                        Valider
                    </button>
                    <a href="edit_report.php" class="btn blue">
                        <i class="fa fa-plus"></i>
                        Ajouter
                    </a>
                </div>
            </div>
        </div>
        <div class="col-md-12" id="form_wizard_1">
                <input type="hidden" name="formname" id="formname" value="editreport" />
                <input type="hidden" name="idreport" id="idreport" value="<?php echo $idReport; ?>" />
                <div class="form-wizard">
                    <div class="form-body form">
                        <ul class="nav nav-pills nav-justified steps">
                            <li>
                                <a href="edit_report.php#parameters" data-toggle="tab" class="step active">
                                <span class="number">
                                1 </span>
                                <span class="desc">
                                <i class="fa fa-check"></i> Général </span>
                                </a>
                            </li>
                            <li>
                                <a href="edit_report.php#messages" data-toggle="tab" class="step">
                                <span class="number">
                                2 </span>
                                <span class="desc">
                                <i class="fa fa-check"></i> Actions </span>
                                </a>
                            </li>
                        </ul>
                        <div id="bar" class="progress progress-striped" role="progressbar">
                            <div class="progress-bar progress-bar-success">
                            </div>
                        </div>
                        <div class="tab-content">
                            <div class="tab-pane active" id="parameters">
                            <div class="row">
                                <div class="row">
                                    <div class="col-md-12 ">
                                        <div class="form-group">
                                            <label class="control-label col-md-3" for="name">Nom</label>
                                            <div class="col-md-9">
                                                <input id="name" name="name" class="form-control" value="<?php echo $name; ?>" type="text">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-12 ">
                                        <div class="form-group">
                                            <label class="control-label col-md-3" for="description">Description</label>
                                            <div class="col-md-9">
                                                <input id="description" name="description" class="form-control" value="<?php echo $description; ?>" type="text">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-12 ">
                                        <div class="form-group">
                                            <label class="control-label col-md-3" for="period">Période</label>
                                            <div class="col-md-9">
                                                <select name="period" id="period" class="form-control">

        <?php
                                                foreach($periods as $periodId=>$periodName){
                                                    $selected=($periodId==$period) ? " selected=\"selected\" " : "";
                                                    echo "<option value=\"".$periodId."\" $selected>".ucwords($periodName)."</option>";
                                                }
        ?>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-12 ">
                                        <div class="form-group">
                                            <label class="control-label col-md-3" for="email">Emails</label>
                                            <div class="col-md-9">
                                                <input id="email" name="email" class="form-control" value="<?php echo $email; ?>" type="text">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-12 ">
                                        <div class="form-group">
                                            <label class="control-label col-md-3" for="chart">Graphique</label>
                                            <div class="col-md-9">
                                                <input type="checkbox" name="chart" id="chart" class="form-control" <?php if($chart){echo " checked=\"checked\" ";} ?> />
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="tab-pane" id="messages">
                            <div class="dd col-md-6 " id="nestable_list_1">
                                <h4>Disponibles</h4>
                                <ol class="dd-list">
                                <?php 
                                $i=0;
                                foreach($charts as $chartTmp){
                                    $i++;
                                    echo "<li class=\"dd-item\" style=\"\" data-id=\"d".$i."\">";
                                    echo "<button type=\"button\" class=\"btnAddDevice\" deviceId=\"".$chartTmp->id."\" deviceName=\"".  addslashes(ucwords($chartTmp->name)." - ".ucwords($chartTmp->type))."\" style=\"display: block;float:right;\"><i class=\"fa fa-plus\"></i></button>";
                                    echo "<div class=\"dd-handle\" deviceId=\"".$chartTmp->id."\">".ucwords($chartTmp->name)." - ".ucwords($chartTmp->type)."</div>";
                                    echo "</li>";
                                    //echo "<i class=\"fa fa-plus btnAddDevice\" style=\"float: left; right:20px;margin-top:15px;\"></i>";
                                }
                                ?>
                                </ol>
                            </div>
                            <div class="dd  col-md-6" id="nestable_list_2">
                                <h4>Affectés</h4>
                                <ol class="dd-list affect">
                            <?php
                                    if(count($reportDevices) == 0){
                                        echo "<li class=\"dd-item\" data-id=\"1\">";
                                        echo "<div class=\"dd-handle\"></div>";
                                        echo "</li>";
                                    }
                                    $i=1;
                                    foreach($reportDevices as $reportMessage){
                                        $chartTmp = Chart::getChart($reportMessage->deviceid);
                                        
                                        echo "<li class=\"dd-item reportDeviceId-".$reportMessage->id."\" data-id=\"".$i."\">";
                                        echo "<div class=\"dd-handle\" deviceId=\"".$chartTmp->id."\">".ucwords($chartTmp->name);
                                        echo "</div>";
                                        echo "<i class=\"fa fa-times btnRemoveDevice\" reportDeviceId=\"".$reportMessage->id."\" style=\"float: right; cursor: pointer; position: absolute; top: 8px; right: 10px;\"></i>";
                                        echo "</li>";
                                        $i++;
                                    }
                            ?>
                                </ol>
                            </div>
                        </div><!-- class="tab-pane" id="messages" -->
                        
                    </div>
                    </div>
                </div>
        </div>
    </div>
    </form>
    </div>
</div>
    
<div class="modal fade" id="basic" tabindex="-1" role="basic" aria-hidden="true">
    <div class="modal-dialog" style="width: 800px;">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close modalClose" data-dismiss="modal" aria-hidden="true"></button>
                <h3 class="modal-title">Sélection d'une icône</h3>
            </div>
            <div class="modal-body">
                <div class="tab-content">
                    <?php include "icons.php"; ?>
                </div>
            </div>
        </div>
        <!-- /.modal-content -->
    </div>
    <!-- /.modal-dialog -->
</div>
<script deviceid="text/javascript">
$(document).ready(function () {
    $('#spinner1').spinner({min: 1, max: 12});
    
    $('.btnRemoveDevice').bind('click',function(e){
        var reportDeviceId = $(this).attr('reportDeviceId');
        $.ajax({
            url: "ajax/delete_report_device.php",
            method: "POST",
            data: {
                reportDeviceId:  reportDeviceId
            },
            error: function(data){
                toastr.error("Une erreur est survenue");
            },
            complete: function(data){
                if(data.responseText == "error"){
                    toastr.error("Une erreur est survenue");
                } else {
                    $('#nestable_list_2 .reportDeviceId-'+reportDeviceId).toggle('hide');
                    //domoUpdateOutput();
                }
            }
        });
    });
    
    $('.reportDeviceMessage').change(function() {
        var reportDeviceId = $(this).attr('reportDeviceId');
        //console.debug('ok');
        //console.debug($(this).val());
        
        $.ajax({
            url: "ajax/report_device_affect.php",
            method: "POST",
            data: {
                reportDeviceId:  reportDeviceId,
                msgId:  $(this).val()
            },
            error: function(data){
                toastr.error("Une erreur est survenue");
            },
            complete: function(data){
                if(data.responseText == "error"){
                    toastr.error("Une erreur est survenue");
                } else {
                    //$('#nestable_list_2 .reportDeviceId-'+reportDeviceId).toggle('hide');
                    //domoUpdateOutput();
                }
            }
        });
    });
    
    $('.fa').bind('click',function(e){
        //console.debug($('.fa').next().attr('class'));
        var classes=$(this).attr('class');
        classes.replace('','fa ');
        //console.debug(classes);
        $('.modalClose').click();
        $('#icon').val(classes);
    });
    
    
    var UINestable = function () {

    var updateOutput = function (e) {
        var list = e.length ? e : $(e.target),
            output = list.data('output');
        if (window.JSON) {
            output.val(window.JSON.stringify(list.nestable('serialize'))); //, null, 2));
        } else {
            output.val('JSON browser support required for this demo.');
        }
    };
    
    function domoUpdateOutput(){
        if($('#idreport').val() == ""){
            toastr.error("Une erreur est survenue", "Sauvegarder le report");
            return false;
        }
        
        var params="";
        var paramsMsg="";
        $('#nestable_list_2 div.dd-handle').each(function( index ) {
            var deviceId=$(this).attr('deviceId');
            if(deviceId != "" && typeof(deviceId) !== "undefined"){
                if(params != ""){
                    params = params + "~";
                }
                params = params + index + ":" + deviceId;
            }
        });
        $('#nestable_list_2 select.reportDeviceMessage').each(function( index ) {
            var valMsgId='';
            if($(this).val() != "" && typeof($(this).val()) !== "undefined"){
                var valMsgId=$(this).val();
            }
            console.debug(index + " "+valMsgId);
            if(paramsMsg != ""){
                paramsMsg = paramsMsg + "~";
            }
            paramsMsg = paramsMsg + index + ":" + valMsgId;
        });
        $.ajax({
            url: "ajax/report_update_order.php",
            method: "POST",
            data: {
                reportid:  $('#idreport').val(),
                devices: params,
                messages: paramsMsg
            },
            error: function(data){
                toastr.error("Une erreur est survenue");
            },
            complete: function(data){
                if(data.responseText == "error"){
                    toastr.error("Une erreur est survenue");
                }
            }
        });
        
        return true;
    }


    return {
        //main function to initiate the module
        init: function () {

            // activate Nestable for list 1
            $('#nestable_list_1').nestable({
                group: 1
                
            })
                .on('change', updateOutput);

            // activate Nestable for list 2
            $('#nestable_list_2').nestable({
                group: 1
            })
                .on('change', domoUpdateOutput);

            // output initial serialised data
            updateOutput($('#nestable_list_1').data('output', $('#nestable_list_1_output')));
            updateOutput($('#nestable_list_2').data('output', $('#nestable_list_2_output')));
            
            $('#nestable_list_menu').on('click', function (e) {
                var target = $(e.target),
                    action = target.data('action');
                if (action === 'expand-all') {
                    $('.dd').nestable('expandAll');
                }
                if (action === 'collapse-all') {
                    $('.dd').nestable('collapseAll');
                }
            });
            
            $('.dd').nestable('collapseAll');

        }

    };

    }();
    
    
    var nestablecount=4;
    $('.btnAddDevice').bind('click',function(e){
        //console.debug('ok rentre');
        var deviceId=$(this).attr('deviceId');
        var deviceName=$(this).attr('deviceName');
        $('ol.affect').append('<li class="dd-item" data-id="' + nestablecount + '"><div class="dd-handle" deviceId="'+deviceId+'">' + deviceName + '</div></li>');
        var params="";
        var paramsMsg="";
        $('#nestable_list_2 div.dd-handle').each(function( index ) {
            var deviceId=$(this).attr('deviceId');
            if(deviceId != "" && typeof(deviceId) !== "undefined"){
                if(params != ""){
                    params = params + "~";
                }
                params = params + index + ":" + deviceId;
            }
        });
        $('#nestable_list_2 select.reportDeviceMessage').each(function( index ) {
            /*var valMsgId=$(this).val();
            if(valMsgId != "" && typeof(valMsgId) !== "undefined"){
                if(paramsMsg != ""){
                    paramsMsg = paramsMsg + "~";
                }
                paramsMsg = paramsMsg + index + ":" + valMsgId;
            }*/
            
            var valMsgId='';
            if($(this).val() != "" && typeof($(this).val()) !== "undefined"){
                var valMsgId=$(this).val();
            }
            console.debug(index + " "+valMsgId);
            if(paramsMsg != ""){
                paramsMsg = paramsMsg + "~";
            }
            paramsMsg = paramsMsg + index + ":" + valMsgId;
        });
        $.ajax({
            url: "ajax/report_update_order.php",
            method: "POST",
            data: {
                reportid:  $('#idreport').val(),
                devices: params,
                messages: paramsMsg
            },
            error: function(data){
                toastr.error("Une erreur est survenue");
            },
            complete: function(data){
                if(data.responseText == "error"){
                    toastr.error("Une erreur est survenue");
                }
            }
        });
        nestablecount++;
    });
    
    UINestable.init();
});
var ui="wizard";
var ui2="nestable";
</script>
<?php
include "modules/footer.php";
?>