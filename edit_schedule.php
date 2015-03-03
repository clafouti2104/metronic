<?php
$includeJS[] = "/assets/global/plugins/fuelux/js/spinner.min.js";   
$includeJS[] = "/assets/global/plugins/bootstrap-wizard/jquery.bootstrap.wizard.min.js";   
$includeJS[] = "/assets/admin/pages/scripts/form-wizard.js";   
$includeJS[] = "/assets/global/plugins/select2/select2.min.js";   
$includeJS[] = "/assets/global/plugins/jquery-validation/js/jquery.validate.min.js";   
$includeJS[] = "/assets/global/plugins/jquery-validation/js/additional-methods.min.js"; 
$includeJS[] = "/assets/global/plugins/bootstrap-timepicker/js/bootstrap-timepicker.min.js"; 
//$includeJS[] = "/assets/admin/pages/scripts/ui-nestable.js"; 

$includeCSS[] = "/assets/global/plugins/select2/select2.css";   
$includeCSS[] = "/assets/global/plugins/bootstrap-timepicker/css/bootstrap-timepicker.min.css";

include "modules/header.php";
include "modules/sidebar.php";

$GLOBALS["dbconnec"] = connectDB();
include_once "models/Schedule.php";
include_once "models/ScheduleAction.php";
include_once "models/Device.php";
include_once "models/MessageDevice.php";
include_once "models/Scenario.php";

//$devices=  Device::getDevices();
$variables=$notifications=array();
$sqlPlugins = "SELECT * FROM config ";
$sqlPlugins .= " WHERE name IN ('variable','pushing_box' )";
$stmt = $GLOBALS["dbconnec"]->prepare($sqlPlugins);
$stmt->execute(array());
while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    if($row["name"] == "variable"){
        $variables[$row["id"]]=$row["value"];
    }
    if($row["name"] == "pushing_box"){
        $notifications[$row["value"]]=$row["comment"];
    }
}

$isPost=FALSE;
if(isset($_POST["formname"]) && $_POST["formname"]=="editschedule"){
    $isPost=TRUE;
}

if($isPost && isset($_POST["idschedule"])){
    $name= $_POST["scheduleName"];
    $description= $_POST["description"];
    $idSchedule=$_POST["idschedule"];
    if($idSchedule > 0){
        $schedule = Schedule::getSchedule($idSchedule);
        $scheduleActions= ScheduleAction::getScheduleActionForSchedule($idSchedule);
    }
} else {
    $idSchedule=0;
    $txtMode="Création";
    $txtModeDesc="Création d'une tâche planifiée";
    if(isset($_GET["idSchedule"])){
        $idSchedule=$_GET["idSchedule"];
        $schedule = Schedule::getSchedule($idSchedule);
        $scheduleActions= ScheduleAction::getScheduleActionForSchedule($idSchedule);
        $txtMode="Edition";
        $txtModeDesc="Edition d'une tâche planifiée";
    } 
    $name= (isset($schedule) && is_object($schedule)) ? $schedule->name : NULL ;
    $description= (isset($schedule) && is_object($schedule)) ? $schedule->description : NULL ;
    $hour= (isset($schedule) && is_object($schedule)) ? $schedule->hour.":".$schedule->minute : NULL;
    $weekDays = (isset($schedule) && is_object($schedule)) ? explode(",",$schedule->weekdays) : array();
}

if($isPost){
    //print_r($_POST);
    //Controle
    if($_POST["scheduleName"] == ""){
        $error="Veuillez renseigner le nom";
    }
    if(!isset($_POST["hourDays"])){
        $error="Veuillez renseigner un jour";
    }
    if(!isset($_POST["hour"]) || $_POST["hour"] == ""){
        $error="Veuillez renseigner un jour";
    }
    
    if(!isset($error)){
        $weekDays=$_POST["hourDays"];
        $hour=$_POST["hour"];
        $hourTmp=explode(":",$_POST["hour"]);
        if($_POST["idschedule"]>0){
            $schedule=  Schedule::getSchedule($_POST["idschedule"]);
            $schedule->name=$_POST["scheduleName"];
            $schedule->description=$_POST["description"];
            $schedule->hour=$hourTmp[0];
            $schedule->minute=$hourTmp[1];
            $schedule->weekdays=implode(",",$_POST["hourDays"]);
            $schedule->update();
            $info="La tâche planifiée a été modifiée";
        } else {
            
            $schedule= Schedule::createSchedule($_POST["scheduleName"], $_POST["description"], implode(",",$_POST["hourDays"]),$hourTmp[0],$hourTmp[1]);
            $idSchedule=$schedule->id;
            $info="La tâche planifiée a été créée";
        }   
    }
}

if(isset($idSchedule) && $idSchedule > 0){
    //$scenarioMessages = CondMessage::getCondMessagesForCond($idSchedule);
    $txtMode="Edition";
    $txtModeDesc="Edition d'une tâche planifiée";
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
                Tâche Planifiée
                <small><?php echo $txtModeDesc; ?></small>
            </h3>
            <!--<a href="edit_device.php" style="float:right;margin: 20px 0 15px;"><button class="btn green" type="button"><i class="icon-plus"></i>&nbsp;Ajouter un device</button></a>-->
            <ul class="page-breadcrumb breadcrumb">
                <li>
                    <i class="fa fa-home"></i>
                    <a href="index.php">Admin</a>
                    <i class="fa fa-angle-right"></i>
                </li>
                <li>   
                    <a href="admin_schedule.php">Liste des tâches</a>
                    <i class="fa fa-angle-right"></i>
                </li>
                <li>
                    <a href="#"><?php echo $txtMode; ?></a>
                    <i class="fa fa-angle-right"></i>
                </li>
            </ul>
            <?php if(isset($info)){echo "<div class=\"alert alert-success\">".$info."</div>";}?>
            <?php if(isset($error) && $error!=""){echo "<div class=\"alert alert-danger\">".$error."</div>";}?>
            <!-- END PAGE TITLE & BREADCRUMB-->
        </div>
    </div>
    <div class="row">
        <div class="col-md-12" id="form_wizard_1">
            <form class="form-horizontal" method="POST" action="edit_schedule.php">
                <div class="portlet">
                    <div class="portlet-title">
                        <div class="caption">
                            <i class="fa fa-gears"></i>
                            Gestion
                        </div>
                        <div class="actions btn-set">
                            <a href="admin_schedule.php" class="btn default">
                                <i class="fa fa-angle-left"></i>
                                Retour
                            </a>
                            <button class="btn green" type="submit">
                                <i class="fa fa-check"></i>
                                Valider
                            </button>
                                
                            <a href="edit_schedule.php" class="btn blue">
                                <i class="fa fa-plus"></i>
                                Ajouter
                            </a>
                        </div>
                    </div>
                </div>
                <input type="hidden" name="formname" id="formname" value="editschedule" />
                <input type="hidden" name="idschedule" id="idschedule" value="<?php echo $idSchedule; ?>" />
                <div class="form-wizard">
                    <div class="form-body form">
                        <ul class="nav nav-pills nav-justified steps">
                            <li>
                                <a href="edit_schedule.php#parameters" data-toggle="tab" class="step active">
                                <span class="number">
                                1 </span>
                                <span class="desc">
                                <i class="fa fa-check"></i> Général </span>
                                </a>
                            </li>
                            <?php if($idSchedule != "" && $idSchedule > 0){ ?>
                            <li>
                                <a href="edit_schedule.php#messages" data-toggle="tab" class="step">
                                <span class="number">2</span>
                                <span class="desc">
                                <i class="fa fa-check"></i> Actions </span>
                                </a>
                            </li>
                            <?php } ?>
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
                                                <label class="control-label col-md-3" for="scheduleName">Nom</label>
                                                <div class="col-md-9">
                                                    <input id="scheduleName" name="scheduleName" class="form-control" value="<?php echo $name; ?>" type="text">
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
                                                <label class="control-label col-md-3" for="hourDay">Exécuter les jours suivants</label>
                                                <div class="col-md-9">
                                                    <div class="checkbox-list">
                                                        <label class="checkbox-inline">
                                                        <input type="checkbox" class="cbxEveryDays"> Tous les jours</label>
                                                        <label class="checkbox-inline">
                                                        <input type="checkbox" class="cbxWeekDays"> Jours Ouvrés </label>
                                                        <label class="checkbox-inline">
                                                        <input type="checkbox" class="cbxWeekEnd"> Week End</label>
                                                    </div>
                                                    <div class="checkbox-list">
                                                        <label class="checkbox-inline">
                                                        <input type="checkbox" class="hourDay week" name="hourDays[]" id="monday" value="1" <?php if(in_array("1",$weekDays)){ echo " checked=\"checked\" "; } ?>> Lundi </label>
                                                        <label class="checkbox-inline">
                                                        <input type="checkbox" class="hourDay week" name="hourDays[]" id="tuesday" value="2" <?php if(in_array("2",$weekDays)){ echo " checked=\"checked\" "; } ?>> Mardi </label>
                                                        <label class="checkbox-inline">
                                                        <input type="checkbox" class="hourDay week" name="hourDays[]" id="wednesday" value="3" <?php if(in_array("3",$weekDays)){ echo " checked=\"checked\" "; } ?>> Mercredi </label>
                                                        <label class="checkbox-inline">
                                                        <input type="checkbox" class="hourDay week" name="hourDays[]" id="thursday" value="4" <?php if(in_array("4",$weekDays)){ echo " checked=\"checked\" "; } ?>> Jeudi </label>
                                                        <label class="checkbox-inline">
                                                        <input type="checkbox" class="hourDay week" name="hourDays[]" id="friday" value="5" <?php if(in_array("5",$weekDays)){ echo " checked=\"checked\" "; } ?>> Vendredi </label>
                                                        <label class="checkbox-inline">
                                                        <input type="checkbox" class="hourDay weekEnd" name="hourDays[]" id="saturday" value="6" <?php if(in_array("6",$weekDays)){ echo " checked=\"checked\" "; } ?>> Samedi </label>
                                                        <label class="checkbox-inline">
                                                        <input type="checkbox" class="hourDay weekEnd" name="hourDays[]" id="sunday" value="0" <?php if(in_array("0",$weekDays)){ echo " checked=\"checked\" "; } ?>> Dimanche </label>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-12 ">
                                            <div class="form-group">
                                                <label class="control-label col-md-3" for="hour">Heure</label>
                                                <div class="col-md-9">
                                                    <div class="input-group">
                                                            <input type="text" id="hour" name="hour" value="<?php echo $hour; ?>" class="form-control timepicker timepicker-24">
                                                            <span class="input-group-btn">
                                                                <button class="btn default" type="button"><i class="fa fa-clock-o"></i></button>
                                                            </span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="tab-pane" id="messages">
                                <div class="row">
                                    <div class="col-md-3 " style="clear:left;">
                                        <a data-target="#ajax" data-toggle="modal" href="ajax/user/sce_cond_action.php?idSchedule=<?php echo $idSchedule; ?>" class="icon-btn">
                                            <i class="fa fa-sitemap"></i>
                                            <div>
                                                 Action
                                            </div>
                                        </a>
                                        <a data-target="#ajaxScenario" data-toggle="modal" href="ajax/user/sce_cond_scenario.php?idSchedule=<?php echo $idSchedule; ?>" class="icon-btn" style="clear:left;">
                                            <i class="fa fa-clock-o"></i>
                                            <div>
                                                 Scenario
                                            </div>
                                        </a>
                                        <a data-target="#ajaxVariable" data-toggle="modal" href="ajax/user/sce_cond_variable.php?idSchedule=<?php echo $idSchedule; ?>" class="icon-btn">
                                            <i class="fa fa-tasks"></i>
                                            <div>
                                                 Variables
                                            </div>
                                        </a>
                                        <a data-target="#ajaxNotification" data-toggle="modal" href="ajax/user/sce_cond_notification.php?idSchedule=<?php echo $idSchedule; ?>" class="icon-btn">
                                            <i class="fa fa-cloud-upload "></i>
                                            <div>
                                                 Notifications
                                            </div>
                                        </a>
                                        <a data-target="#ajaxCommandLine" data-toggle="modal" href="ajax/user/sce_cond_command_line.php?idSchedule=<?php echo $idSchedule; ?>" class="icon-btn">
                                            <i class="fa fa-code "></i>
                                            <div>
                                                 Ligne de Commandes
                                            </div>
                                        </a>
                                    </div>
                                    <div class="col-md-9 ">
                                        <div class="table-responsive">
                                            <table class="table table-hover" id="tableCondAction">
                                                <thead>
                                                    <tr>
                                                        <th>Type</th>
                                                        <th>Objet</th>
                                                        <th></th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                            <?php
                                                foreach($scheduleActions as $scheduleAction){
                                                    $object=$type="";
                                                    switch(strtolower($scheduleAction->type)){
                                                        case 'action_message':
                                                            $messageTmp=MessageDevice::getMessageDevice($scheduleAction->action);
                                                            $deviceTmp=Device::getDevice($messageTmp->deviceId);
                                                            $type="Action";
                                                            $object.=$deviceTmp->name." - ".$messageTmp->name;
                                                            break;
                                                        case 'action_scenario':
                                                            $scenarioTmp=Scenario::getScenario($scheduleAction->action);
                                                            $type="Scénario";
                                                            $object.=$scenarioTmp->name;
                                                            break;
                                                        case 'action_variable':
                                                            $object.=$scheduleAction->more; 
                                                            if(isset($variables[$scheduleAction->action])){
                                                                $object.=" ".$variables[$scheduleAction->action];
                                                                $object.=" ".$scheduleAction->value;
                                                            }
                                                            $type="variable";
                                                            break;
                                                        case 'commandline':
                                                            if(strlen($scheduleAction->action) > 10){
                                                                $object.=' exécute <span title="'.str_replace('"', '', $scheduleAction->action).'">'.substr($scheduleAction->action,0,10).'...</span>';
                                                            } else {
                                                                $object.=" exécute ".$scheduleAction->action;
                                                            }
                                                            $type="commande";
                                                            break;
                                                        case 'notification':
                                                            if(isset($notifications[$scheduleAction->action])){
                                                                $object.=" envoie ".$notifications[$scheduleAction->action];
                                                            }
                                                            $type="notification";
                                                            break;
                                                        default:
                                                    }
                                                    
                                                    echo "<tr id=\"line-condaction-".$scheduleAction->id."\">";
                                                    echo "<td>".$type."</td>";
                                                    echo "<td>".$object."</td>";
                                                    echo "<td><a href=\"#\" style=\"margin-left:5px;\"><i class=\"fa fa-trash-o btnDeleteCondAction\" style=\"color:black;\" condActionId=\"".$scheduleAction->id."\"></i></a></td>";
                                                    echo "</tr>";
                                                }
                                            ?>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div><!-- class="tab-pane" id="messages" -->
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
    </div>
</div>
    
<div class="modal fade" id="ajaxCommandLine" role="basic" aria-hidden="true">
    <div class="page-loading page-loading-boxed">
        <img src="metronic/assets/global/img/loading-spinner-grey.gif" alt="" class="loading">
        <span>&nbsp;&nbsp;Loading... </span>
    </div>
    <div class="modal-dialog" style="width:600px;">
        <div class="modal-content">
        </div>
    </div>
</div>
    
<div class="modal fade" id="ajaxVariable" role="basic" aria-hidden="true">
    <div class="page-loading page-loading-boxed">
        <img src="metronic/assets/global/img/loading-spinner-grey.gif" alt="" class="loading">
        <span>&nbsp;&nbsp;Loading... </span>
    </div>
    <div class="modal-dialog" style="width:800px;">
        <div class="modal-content">
        </div>
    </div>
</div>
    
<div class="modal fade" id="ajax" role="basic" aria-hidden="true">
    <div class="page-loading page-loading-boxed">
        <img src="metronic/assets/global/img/loading-spinner-grey.gif" alt="" class="loading">
        <span>&nbsp;&nbsp;Loading... </span>
    </div>
    <div class="modal-dialog" style="width:800px;">
        <div class="modal-content">
        </div>
    </div>
</div>
    
<div class="modal fade" id="ajaxScenario" role="basic" aria-hidden="true">
    <div class="page-loading page-loading-boxed">
        <img src="metronic/assets/global/img/loading-spinner-grey.gif" alt="" class="loading">
        <span>&nbsp;&nbsp;Loading... </span>
    </div>
    <div class="modal-dialog" style="width:400px;">
        <div class="modal-content">
        </div>
    </div>
</div>
    
<div class="modal fade" id="ajaxNotification" role="basic" aria-hidden="true">
    <div class="page-loading page-loading-boxed">
        <img src="metronic/assets/global/img/loading-spinner-grey.gif" alt="" class="loading">
        <span>&nbsp;&nbsp;Loading... </span>
    </div>
    <div class="modal-dialog" style="width:400px;">
        <div class="modal-content">
        </div>
    </div>
</div>

<div class="modal fade" id="editCondition" tabindex="-1" role="basic" aria-hidden="true">
    <div class="modal-dialog">
        <input type="hidden" id="idcondition" value="" />
        <div class="modal-content">
            <div class="modal-header">
                <i class="fa fa-gears"></i>&nbsp;<span id="labelEditMessageDevice">Condition</span>
            </div>
            <div class="modal-body">
                <div id="alertError" style="display:none;" class="alert alert-danger"></div>
                <div class="row">
                    <div class="col-md-12">
                        <div class="form-group">
                            <label class="control-label col-md-3" for="conditionDevice">Si</label>
                            <div class="col-md-9">
                                <select id="conditionDevice" name="conditionDevice" class="form-control">
                                    <?php
                                    foreach(Device::getDevices() as $deviceTmp){
                                        $type=($deviceTmp->type != "") ? " - ".$deviceTmp->type : "";
                                        echo "<option value=\"".$deviceTmp->id."\">".$deviceTmp->name.$type."</option>";
                                    }
                                    ?>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row" style="margin-top:10px;">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="control-label col-md-3" for="conditionOperator">est</label>
                            <div class="col-md-9">
                                <select id="conditionOperator" name="conditionOperator" class="form-control">
                                    <option value="<">inférieur</option>
                                    <option value=">">supérieur</option>
                                    <option value="=">égal</option>
                                    <option value="!=">différent</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6" style="margin-top:10px;">
                        <div class="form-group">
                            <label class="control-label col-md-3" for="conditionValue">à</label>
                            <div class="col-md-9">
                                <input id="conditionValue" name="conditionValue" class="form-control" value="" type="text">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button class="btn btn-default btnCancelCondition" data-dismiss="modal" type="button">Annuler</button>
                <button class="btn btn-primary btnSubmitCondition" type="button">Valider</button>
            </div>
        </div>
    </div>
</div>
    
<div class="modal fade" id="editVariable" tabindex="-1" role="basic" aria-hidden="true">
    <div class="modal-dialog">
        <input type="hidden" id="idvariable" value="" />
        <div class="modal-content">
            <div class="modal-header">
                <i class="fa fa-gears"></i>&nbsp;<span id="labelEditMessageDevice">Variable</span>
                <a href="#" class="btn btn-primary btnCreateVariable" style="float:right;">
                    Ajouter
                </a>
            </div>
            <div class="modal-body">
                <div id="variableError" style="display:none;" class="alert alert-danger"></div>
                <div class="showVariable">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label class="control-label col-md-3" for="variableDevice">Si</label>
                                <div class="col-md-9">
                                    <select id="variableDevice" name="variableDevice" class="form-control">
                                        <?php
                                        foreach($variables as $variableKey=>$variableTmp){
                                            echo "<option value=\"".$variableKey."\">".$variableTmp."</option>";
                                        }
                                        ?>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row" style="margin-top:10px;">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="control-label col-md-3" for="variableOperator">est</label>
                                <div class="col-md-9">
                                    <select id="variableOperator" name="variableOperator" class="form-control">
                                        <option value="<">inférieur</option>
                                        <option value=">">supérieur</option>
                                        <option value="=">égal</option>
                                        <option value="!=">différent</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6" style="margin-top:10px;">
                            <div class="form-group">
                                <label class="control-label col-md-3" for="variableValue">à</label>
                                <div class="col-md-9">
                                    <input id="variableValue" name="variableValue" class="form-control" value="" type="text">
                                </div>
                            </div>
                        </div>
                    </div>
                    
                </div>
                <div class="showVariable" style="display:none;">
                    <div class="row">
                        <div class="col-md-9">
                            <div class="form-group">
                                <label class="control-label col-md-3" for="variableName">Nom</label>
                                <div class="col-md-9">
                                    <input id="variableName" name="variableName" class="form-control" value="" type="text">
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <button class="btn btn-primary btnSubmitCreateVariable" type="button">Ajouter</button>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button class="btn btn-default btnCancelVariable" data-dismiss="modal" type="button">Annuler</button>
                <button class="btn btn-primary btnSubmitVariable" type="button">Valider</button>
            </div>
        </div>
    </div>
</div>
    
<script deviceid="text/javascript">
$(document).ready(function () {
    $('#spinner1').spinner({min: 1, max: 12});
    
    $('.cbxEveryDays').bind('click',function(e){
        if(this.checked) { 
            $('.hourDay').each(function() { 
                if(!$(this).is(':checked')){
                    $(this).click();
                }     
            });
        }else{
            $('.hourDay').each(function() {
                if($(this).is(':checked')){
                    $(this).click();
                }     
            });        
        }
    });
    
    $('.cbxWeekDays').bind('click',function(e){
        if(this.checked) { 
            $('.week').each(function() { 
                if(!$(this).is(':checked')){
                    $(this).click();
                }     
            });
        }else{
            $('.week').each(function() {
                if($(this).is(':checked')){
                    $(this).click();
                }     
            });        
        }
    });
    
    $('.cbxWeekEnd').bind('click',function(e){
        if(this.checked) { 
            $('.weekEnd').each(function() { 
                if(!$(this).is(':checked')){
                    $(this).click();
                }     
            });
        }else{
            $('.weekEnd').each(function() {
                if($(this).is(':checked')){
                    $(this).click();
                }     
            });        
        }
    });
    
    $('.btnDeleteCondition').bind('click',function(e){
        var conditionId=$(this).attr('conditionId');
        deleteCondition(conditionId);
    });
    
    $('.btnDeleteCondAction').bind('click',function(e){
        var condActionId=$(this).attr('condActionId');
        deleteCondAction(condActionId);
    });
    
    $('.fa').bind('click',function(e){
        var classes=$(this).attr('class');
        classes.replace('','fa ');
        $('.modalClose').click();
        $('#icon').val(classes);
    });
    
    $('.btnCreateVariable').bind('click',function(e){
        $('.showVariable').toggle();
        if($('#variableName').is(':visible')){
            $('.btnCreateVariable').text('Retour');
            $('#variableName').focus();
        } else {
            $('.btnCreateVariable').text('Ajouter');
        }
    });
    
    $('.btnSubmitCreateVariable').bind('click',function(e){
        $.ajax({
            url: "ajax/variable_submit.php",
            type: "POST",
            data: {
                variableId: '',
                name: $('#variableName').val()
            },
            error: function(data){
                toastr.error("Une erreur est survenue");
            },
            success: function(data){
                if(data.responseText == "error"){
                    toastr.error("Une erreur est survenue");
                } else {
                    if(data.responseText == "exists"){
                        toastr.error("Une erreur existe déjà avec ce nom");
                    }
                    eval(data.responseText);
                    eval(data);
                    $('.btnCreateVariable').click();
                    toastr.info("Variable ajoutée","");
                    
                }
            }
        });
    });
    
    $('.btnSubmitCondition').bind('click',function(e){
        if($('#conditionValue').val() == ""){
            $('#alertError').text('Veuillez saisir une valeur');
            $('#alertError').show();
            return true;
        }
        $('#alertError').hide();
        $('#alertError').text('');
        
        $.ajax({
            url: "ajax/sce_cond_submit.php",
            type: "POST",
            data: {
                variableId: $('#idvariable').val(),
                deviceId: $('#conditionDevice').val(),
                operator: $('#conditionOperator').val(),
                condId: $('#idschedule').val(),
                value: $('#conditionValue').val()
            },
            error: function(data){
                toastr.error("Une erreur est survenue");
            },
            success: function(data){
                if(data.responseText == "error"){
                    toastr.error("Une erreur est survenue");
                } else {
                    $('#tableCondition tbody:last').append('<tr id="line-condition-'+data.responseText+'"><td>Device</td><td>si '+$('#conditionDevice option:selected').text()+' est '+$('#conditionOperator').val()+' '+$('#conditionValue').val()+'</td><td><i class="fa fa-trash-o" style="cursor:pointer;" onclick="deleteCondition('+data.responseText+');"></i></td></tr>');
                    $('.close').click();
                    $('.btnCancelCondition').click();
                    toastr.info("Condition ajoutée");
                    
                }
            }
        });
    });
    
    $('.btnSubmitHour').bind('click',function(e){
        if($('.hourDay:checked').length == 0){
            $('#hourError').text('Veuillez sélectionner au moins un jour');
            $('#hourError').show();
            return true;
        }
        $('#hourError').hide();
        $('#hourError').text('');
        
        var days = new Array();
        //Status
        //$('.stateDeviceId:visible').each(function() {
        $('.hourDay:checked').each(function() {
            if ($(this).val() != 0) {
                if ($.inArray($(this).val(),days) == -1) {
                    days.push($(this).val());
                }
            }
        });
        
        $.ajax({
            url: "ajax/sce_cond_hour_submit.php",
            type: "POST",
            data: {
                condId: $('#idschedule').val(),
                days: days.join(','),
                hourBegin: $('#hourBegin').val(),
                hourEnd: $('#hourEnd').val()
            },
            error: function(data){
                toastr.error("Une erreur est survenue");
            },
            success: function(data){
                if(data.responseText == "error"){
                    toastr.error("Une erreur est survenue");
                } else if(data.responseText == "wrongError") {
                    toastr.error("Les heures saisies sont incorrectes");
                } else {
                    //$('#tableCondition tbody:last').append('<tr id="line-condition-'+data.responseText+'"><td>Device</td><td>si '+$('#conditionDevice option:selected').text()+' est '+$('#conditionOperator').val()+' '+$('#conditionValue').val()+'</td><td><i class="fa fa-trash-o" style="cursor:pointer;" onclick="deleteCondition('+data.responseText+');"></i></td></tr>');
                    $('.close').click();
                    $('.btnCancelCondition').click();
                    toastr.info("Condition ajoutée");
                    
                }
            }
        });
    });
    
    $('.btnSubmitVariable').bind('click',function(e){
        if($('#variableValue').val() == ""){
            $('#alertError').text('Veuillez saisir une valeur');
            $('#alertError').show();
            return true;
        }
        $('#alertError').hide();
        $('#alertError').text('');
        
        $.ajax({
            url: "ajax/sce_variable_submit.php",
            type: "POST",
            data: {
                variableId: $('#idvariable').val(),
                deviceId: $('#variableDevice').val(),
                operator: $('#variableOperator').val(),
                condId: $('#idschedule').val(),
                value: $('#variableValue').val()
            },
            error: function(data){
                toastr.error("Une erreur est survenue");
            },
            success: function(data){
                if(data.responseText == "error"){
                    toastr.error("Une erreur est survenue");
                } else {
                    $('#tableCondition tbody:last').append('<tr id="line-condition-'+data.responseText+'"><td>Variable</td><td>si '+$('#variableDevice option:selected').text()+' est '+$('#variableOperator').val()+' '+$('#variableValue').val()+'</td><td><i class="fa fa-trash-o" style="cursor:pointer;" onclick="deleteCondition('+data.responseText+');"></i></td></tr>');
                    $('.close').click();
                    $('.btnCancelVariable').click();
                    toastr.info("Action ajoutée");
                    //toastr.info("Veuillez recharger","Condition ajoutée");
                    
                }
            }
        });
    });
    
});
var ui="wizard";

function deleteCondAction(condActionId){
    if(confirm("Etes vous sur de vouloir supprimer l'action?")){
        $.ajax({
            url: "ajax/condaction_delete.php",
            type: "POST",
            data: {
                condActionId: condActionId,
                type: "schedule_task"
            },
            error: function(data){
                toastr.error("Une erreur est survenue");
            },
            complete: function(data){
                if(data = "done"){
                    toastr.info("Action supprimée");
                    $('#line-condaction-'+condActionId).toggle('hide');
                } else {
                    toastr.error("Une erreur est survenue");
                }
            }
        });
    }
}

function deleteCondition(conditionId){
    if(confirm("Etes vous sur de vouloir supprimer la condition?")){
        $.ajax({
            url: "ajax/condition_delete.php",
            type: "POST",
            data: {
                conditionId: conditionId
            },
            error: function(data){
                toastr.error("Une erreur est survenue");
            },
            complete: function(data){
                if(data = "done"){
                    toastr.info("Condition supprimée");
                    $('#line-condition-'+conditionId).toggle('hide');
                } else {
                    toastr.error("Une erreur est survenue");
                }
            }
        });
    }
}

</script>
<?php
include "modules/footer.php";
?>