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
include_once "models/Cond.php";
include_once "models/Condition.php";
include_once "models/CondAction.php";
include_once "models/Device.php";
include_once "models/MessageDevice.php";

//$devices=  Device::getDevices();
$variables=array();
$sqlPlugins = "SELECT * FROM config ";
$sqlPlugins .= " WHERE name ='variable'";
$stmt = $GLOBALS["dbconnec"]->prepare($sqlPlugins);
$stmt->execute(array());
while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    $variables[$row["id"]]=$row["value"];
}

$isPost=FALSE;
if(isset($_POST["formname"]) && $_POST["formname"]=="editscenario"){
    $isPost=TRUE;
}

if($isPost && isset($_POST["idscenario"])){
    $name= $_POST["name"];
    $description= $_POST["description"];
    $idCond=$_POST["idscenario"];
    if($idCond > 0){
        $scenario = Cond::getCond($idCond);
        $conditions=  Condition::getConditionForCond($idCond);
        $condActions= CondAction::getCondActionForCond($idCond);
    }
} else {
    $idCond=0;
    $txtMode="Création";
    $txtModeDesc="Création d'un scénario conditionnel";
    if(isset($_GET["idCond"])){
        $idCond=$_GET["idCond"];
        $scenario = Cond::getCond($idCond);
        $conditions=  Condition::getConditionForCond($idCond);
        $condActions= CondAction::getCondActionForCond($idCond);
        $txtMode="Edition";
        $txtModeDesc="Edition d'un scénario conditionnel";
    } 
    $name= (isset($scenario) && is_object($scenario)) ? $scenario->name : NULL ;
    $description= (isset($scenario) && is_object($scenario)) ? $scenario->description : NULL ;
}

if($isPost){
    //Controle
    if($_POST["name"] == ""){
        $error="Veuillez renseigner le nom";
    }
    
    if($error == ""){
        $_POST["icon"]=str_replace("fa ","",$_POST["icon"]);
        if($_POST["idscenario"]>0){
            $sql="UPDATE cond SET name='".$_POST["name"]."', description='".$_POST["description"]."'";
            $sql.=" WHERE id=".$_POST["idscenario"];

            $stmt = $GLOBALS["dbconnec"]->exec($sql);
            $info="La scenario a été modifié";
        } else {
            $scenario=Cond::createCond($_POST["name"], $_POST["description"]);
            $idCond=$scenario->id;
            $info="Le scenario a été créé";
        }   
    }
}

if(isset($idCond) && $idCond > 0){
    //$scenarioMessages = CondMessage::getCondMessagesForCond($idCond);
    $txtMode="Edition";
    $txtModeDesc="Edition d'un scénario conditionnel";
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
                Scénario conditionnel				
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
                    <a href="admin_conditional.php">Liste des scénarios</a>
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
            <form class="form-horizontal" method="POST" action="edit_sce_cond.php">
                <div class="portlet">
                    <div class="portlet-title">
                        <div class="caption">
                            <i class="fa fa-gears"></i>
                            Gestion
                        </div>
                        <div class="actions btn-set">
                            <a href="admin_conditional.php" class="btn default">
                                <i class="fa fa-angle-left"></i>
                                Retour
                            </a>
                            <button class="btn green" type="submit">
                                <i class="fa fa-check"></i>
                                Valider
                            </button>
                                
                            <a href="edit_scenario.php" class="btn blue">
                                <i class="fa fa-plus"></i>
                                Ajouter
                            </a>
                        </div>
                    </div>
                </div>
                <input type="hidden" name="formname" id="formname" value="editscenario" />
                <input type="hidden" name="idscenario" id="idscenario" value="<?php echo $idCond; ?>" />
                <div class="form-wizard">
                    <div class="form-body form">
                        <ul class="nav nav-pills nav-justified steps">
                            <li>
                                <a href="edit_sce_cond.php#parameters" data-toggle="tab" class="step active">
                                <span class="number">
                                1 </span>
                                <span class="desc">
                                <i class="fa fa-check"></i> Général </span>
                                </a>
                            </li>
                            <?php if($idCond != "" && $idCond > 0){ ?>
                            <li>
                                <a href="edit_sce_cond.php#conditions" data-toggle="tab" class="step">
                                <span class="number">
                                2 </span>
                                <span class="desc">
                                <i class="fa fa-check"></i> Conditions </span>
                                </a>
                            </li>
                            <li>
                                <a href="edit_sce_cond.php#messages" data-toggle="tab" class="step">
                                <span class="number">
                                2 </span>
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
                                </div>
                            </div>
                            <div class="tab-pane" id="conditions">
                                <div class="row">
                                    <div class="col-md-3 " style="clear:left;">
                                        <a data-toggle="modal" href="edit_sce_cond.php#editCondition" class="icon-btn btnAddDevice">
                                            <i class="fa fa-sitemap"></i>
                                            <div>
                                                 Status
                                            </div>
                                        </a>
                                        <a href="#" class="icon-btn btnAddHour" style="clear:left;">
                                            <i class="fa fa-clock-o"></i>
                                            <div>
                                                 Gestion Horaires
                                            </div>
                                        </a>
                                        <a data-toggle="modal" href="edit_sce_cond.php#editVariable" href="#" class="icon-btn btnAddVariable" style="clear:left;">
                                            <i class="fa fa-tasks"></i>
                                            <div>
                                                 Variables
                                            </div>
                                        </a>
                                    </div>
                                    <div class="col-md-9 ">
                                        <div class="table-responsive">
                                            <table class="table table-hover">
                                                <thead>
                                                    <tr>
                                                        <th>Type</th>
                                                        <th>Objet</th>
                                                        <th></th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                            <?php
                                                foreach($conditions as $condition){
                                                    $object="";
                                                    switch(strtolower($condition->type)){
                                                        case 'device':
                                                            $deviceTmp=Device::getDevice($condition->objectId);
                                                            //print_r($deviceTmp);
                                                            $object.="si "; 
                                                            $object.=$deviceTmp->printDeviceName();
                                                            $object.=" est ".$condition->operator." ".$condition->value;
                                                            break;
                                                        case 'variable':
                                                            if(isset($variables[$condition->objectId])){
                                                                $object.="si "; 
                                                                $object.=$variables[$condition->objectId];
                                                                $object.=" est ".$condition->operator." ".$condition->value;
                                                            }
                                                            break;
                                                        default:
                                                    }
                                                    
                                                    echo "<tr id=\"line-condition-".$condition->id."\">";
                                                    echo "<td>".$condition->type."</td>";
                                                    echo "<td>".$object."</td>";
                                                    echo "<td><a href=\"#\" style=\"margin-left:5px;\"><i class=\"fa fa-trash-o btnDeleteCondition\" style=\"color:black;\" conditionId=\"".$condition->id."\"></i></a></td>";
                                                    echo "</tr>";
                                                }
                                            ?>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="tab-pane" id="messages">
                                <div class="row">
                                    <div class="col-md-3 " style="clear:left;">
                                        <a data-target="#ajax" data-toggle="modal" href="ajax/user/sce_cond_action.php?idCond=<?php echo $idCond; ?>" class="icon-btn">
                                            <i class="fa fa-sitemap"></i>
                                            <div>
                                                 Action
                                            </div>
                                        </a>
                                        <!--<a href="#" class="icon-btn" style="clear:left;">
                                            <i class="fa fa-clock-o"></i>
                                            <div>
                                                 Scenario
                                            </div>
                                        </a>
                                        <a data-target="#ajax" data-toggle="modal" href="edit_sce_cond.php#execVariable" class="icon-btn">
                                            <i class="fa fa-tasks"></i>
                                            <div>
                                                 Variables
                                            </div>
                                        </a>-->
                                    </div>
                                    <div class="col-md-9 ">
                                        <div class="table-responsive">
                                            <table class="table table-hover">
                                                <thead>
                                                    <tr>
                                                        <th>Type</th>
                                                        <th>Objet</th>
                                                        <th></th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                            <?php
                                                foreach($condActions as $condAction){
                                                    $object=$type="";
                                                    switch(strtolower($condAction->type)){
                                                        case 'action_message':
                                                            $messageTmp=MessageDevice::getMessageDevice($condAction->action);
                                                            $deviceTmp=Device::getDevice($messageTmp->deviceId);
                                                            $type="Action";
                                                            $object.=$deviceTmp->name." - ".$messageTmp->name;
                                                            break;
                                                        case 'scenario':
                                                            $scenarioTmp=Scenario::getScenario($condAction->action);
                                                            $type="Scénario";
                                                            $object.=$scenarioTmp->name;
                                                            break;
                                                        default:
                                                    }
                                                    
                                                    echo "<tr id=\"line-condaction-".$condAction->id."\">";
                                                    echo "<td>".$type."</td>";
                                                    echo "<td>".$object."</td>";
                                                    echo "<td><a href=\"#\" style=\"margin-left:5px;\"><i class=\"fa fa-trash-o btnDeleteCondAction\" style=\"color:black;\" condActionId=\"".$condAction->id."\"></i></a></td>";
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
    
    $('.btnDeleteCondition').bind('click',function(e){
        var conditionId=$(this).attr('conditionId');
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
    });
    
    $('.btnDeleteCondAction').bind('click',function(e){
        var condActionId=$(this).attr('condActionId');
        if(confirm("Etes vous sur de vouloir supprimer l'action?")){
            $.ajax({
                url: "ajax/condaction_delete.php",
                type: "POST",
                data: {
                    condActionId: condActionId
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
                condId: $('#idscenario').val(),
                value: $('#conditionValue').val()
            },
            error: function(data){
                toastr.error("Une erreur est survenue");
            },
            success: function(data){
                if(data.responseText == "error"){
                    toastr.error("Une erreur est survenue");
                } else {
                    $('.btnCancelCondition').click();
                    toastr.info("Veuillez recharger","Condition ajoutée");
                    
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
                condId: $('#idscenario').val(),
                value: $('#variableValue').val()
            },
            error: function(data){
                toastr.error("Une erreur est survenue");
            },
            success: function(data){
                if(data.responseText == "error"){
                    toastr.error("Une erreur est survenue");
                } else {
                    $('.btnCancelVariable').click();
                    toastr.info("Veuillez recharger","Condition ajoutée");
                    
                }
            }
        });
    });
    
    var nestablecount=4;
    $('.btnAddMessage').bind('click',function(e){
        var messageId=$(this).attr('messageId');
        var deviceName=$(this).attr('deviceName');
        $('ol.affect').append('<li class="dd-item" data-id="' + nestablecount + '"><div class="dd-handle" messageId="'+messageId+'">' + deviceName + '</div></li>');
        var params="";
        $('#nestable_list_2 div.dd-handle').each(function( index ) {
            var messageId=$(this).attr('messageId');
            if(messageId != "" && typeof(messageId) !== "undefined"){
                if(params != ""){
                    params = params + "~";
                }
                params = params + index + ":" + messageId;
            }
        });
        $.ajax({
            url: "ajax/scenario_message_update_order.php",
            method: "POST",
            data: {
                scenarioid:  $('#idscenario').val(),
                messages: params
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
        if($('#idscenario').val() == ""){
            toastr.error("Une erreur est survenue", "Sauvegarder le scenario");
            return false;
        }
        
        var params="";
        $('#nestable_list_2 div.dd-handle').each(function( index ) {
            var messageId=$(this).attr('messageId');
            if(messageId != "" && typeof(messageId) !== "undefined"){
                if(params != ""){
                    params = params + "~";
                }
                params = params + index + ":" + messageId;
            }
        });
        $.ajax({
            url: "ajax/scenario_message_update_order.php",
            method: "POST",
            data: {
                scenarioid:  $('#idscenario').val(),
                messages: params
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
    
    $('.btnAddPause').bind('click',function(e){
        if($('#txtPause').val() == ""){
            toastr.error("Veuillez renseigner un temps de pause");
            return false;
        }
        
        $.ajax({
            url: "ajax/scenario_add_pause.php",
            method: "POST",
            data: {
                scenarioId:  $('#idscenario').val(),
                pause:  $('#txtPause').val()
            },
            error: function(data){
                toastr.error("Une erreur est survenue");
            },
            complete: function(data){
                if(data.responseText == "error"){
                    toastr.error("Une erreur est survenue");
                } else {
                    eval(data.responseText);
                }
            }
        });
    });
    
    $('.btnRemoveCondMessage').bind('click',function(e){
        var scenarioMessageId = $(this).attr('scenarioMessageId');
        var messageId = $(this).attr('messageId');
        $.ajax({
            url: "ajax/delete_scenario_message.php",
            method: "POST",
            data: {
                scenarioMessageId:  scenarioMessageId
            },
            error: function(data){
                toastr.error("Une erreur est survenue");
            },
            complete: function(data){
                if(data.responseText == "error"){
                    toastr.error("Une erreur est survenue");
                } else {
                    $('#nestable_list_2 .messageId-'+messageId).toggle('hide');
                    //domoUpdateOutput();
                }
            }
        });
    });
    
    UINestable.init();
});
var ui="wizard";
var ui2="nestable";
</script>
<?php
include "modules/footer.php";
?>