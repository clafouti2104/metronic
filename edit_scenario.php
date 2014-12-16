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
include_once "models/Scenario.php";
include_once "models/ScenarioMessage.php";
include_once "models/Device.php";
include_once "models/MessageDevice.php";

$devices=  Device::getDevices();
$colors=array();
$colors["bleu"]=array(
    "blue"=>"bleu",
    "blue-hoki"=>"bleu gris",
    "blue-steel"=>"bleu métal",
    "blue-madison"=>"bleu madison",
    "blue-chambray"=>"bleu chambray"
);
$colors["vert"]=array(
    "green"=>"vert",
    "green-meadow"=>"vert meadow",
    "green-seagreen"=>"vert mer",
    "green-turquoise"=>"vert turquoise"
);
$colors["rouge"]=array(
    "red"=>"rouge",
    "red-ping"=>"rouge rose",
    "red-sunglo"=>"rouge sunglo",
    "red-intense"=>"rouge intense",
    "red-thunderbird"=>"rouge thunderbird"
);
$colors["jaune"]=array(
    "yellow"=>"jaune",
    "yellow-gold"=>"jaune or",
    "yellow-casablanca"=>"jaune casablanca",
    "yellow-crusta"=>"jaune crusta",
    "yellow-lemon"=>"jaune citron"
);
$colors["violet"]=array(
    "purple"=>"violet",
    "purple-plum"=>"violet plum",
    "purple-medium"=>"violet medium",
    "purple-studio"=>"violet studio"
);
$colors["gris"]=array(
    "grey"=>"gris",
    "grey-cascade"=>"gris cascade",
    "grey-silver"=>"gris argent",
    "grey-steel"=>"gris metal"
);

$isPost=FALSE;
if(isset($_POST["formname"]) && $_POST["formname"]=="editscenario"){
    $isPost=TRUE;
}

if($isPost && isset($_POST["idscenario"])){
    $name= $_POST["name"];
    $description= $_POST["description"];
    $color= $_POST["color"];
    $icon= $_POST["icon"];
    $size= ($_POST["size"] == "" ) ? 3 : $_POST["size"];
    $_POST["size"]= ($_POST["size"] == "" ) ? 3 : $_POST["size"];
    $idScenario=$_POST["idscenario"];
    if($idScenario > 0){
        $scenario = Scenario::getScenario($idScenario);
    }
} else {
    $idScenario=0;
    $txtMode="Création";
    $txtModeDesc="Création d'un scenario";
    if(isset($_GET["idScenario"])){
        $idScenario=$_GET["idScenario"];
        $scenario = Scenario::getScenario($idScenario);
        $scenarioMessages = ScenarioMessage::getScenarioMessagesForScenario($idScenario);
        $txtMode="Edition";
        $txtModeDesc="Edition d'un scenario";
    } 
    $name= (isset($scenario) && is_object($scenario)) ? $scenario->name : NULL ;
    $icon= (isset($scenario) && is_object($scenario)) ? $scenario->icon : NULL ;
    $description= (isset($scenario) && is_object($scenario)) ? $scenario->description : NULL ;
    $color= (isset($scenario) && is_object($scenario)) ? $scenario->color : NULL ;
    $size= (isset($scenario) && is_object($scenario)) ? $scenario->size : NULL;
}

if($isPost){
    //Controle
    if($_POST["name"] == ""){
        $error="Veuillez renseigner le nom";
    }
    
    if($error == ""){
        $_POST["icon"]=str_replace("fa ","",$_POST["icon"]);
        if($_POST["idscenario"]>0){
            $sql="UPDATE scenario SET name='".$_POST["name"]."', description='".$_POST["description"]."', color='".$_POST["color"]."', size=".$_POST["size"].", icon='".$_POST["icon"]."'";
            $sql.=" WHERE id=".$_POST["idscenario"];

            $stmt = $GLOBALS["dbconnec"]->exec($sql);
            $info="La scenario a été modifié";
        } else {
            $scenario=Scenario::createScenario($_POST["name"], $_POST["description"], $_POST["color"],$_POST["size"], $_POST["icon"]);
            $idScenario=$scenario->id;
            $info="Le scenario a été créé";
        }   
    }
}

if(isset($idScenario) && $idScenario > 0){
    $scenarioMessages = ScenarioMessage::getScenarioMessagesForScenario($idScenario);
    $txtMode="Edition";
    $txtModeDesc="Edition d'une liste";
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
                Scénario				
                <small><?php echo $txtModeDesc; ?></small>
            </h3>
            <!--<a href="edit_device.php" style="float:right;margin: 20px 0 15px;"><button class="btn green" type="button"><i class="icon-plus"></i>&nbsp;Ajouter un device</button></a>-->
            <ul class="page-breadcrumb breadcrumb">
                <!--<li class="btn-group">
                    <button class="btn btn-primary" type="button" onclick="javascript:location.href='edit_scenario.php';"><i class="fa fa-plus"></i>Ajouter un scénario</button>
                </li>-->
                <li>
                    <i class="fa fa-home"></i>
                    <a href="index.php">Admin</a>
                    <i class="fa fa-angle-right"></i>
                </li>
                <li>   
                    <a href="admin_scenario.php">Liste des scénarios</a>
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
        <div class="col-md-12" id="form_wizard_1">
            <form class="form-horizontal" method="POST" action="edit_scenario.php">
                <div class="portlet">
                    <div class="portlet-title">
                        <div class="caption">
                            <i class="fa fa-gears"></i>
                            Gestion
                        </div>
                        <div class="actions btn-set">
                            <a href="admin_scenario.php" class="btn default">
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
                <input type="hidden" name="idscenario" id="idscenario" value="<?php echo $idScenario; ?>" />
                <div class="form-wizard">
                    <div class="form-body form">
                        <ul class="nav nav-pills nav-justified steps">
                            <li>
                                <a href="edit_scenario.php#parameters" data-toggle="tab" class="step active">
                                <span class="number">
                                1 </span>
                                <span class="desc">
                                <i class="fa fa-check"></i> Général </span>
                                </a>
                            </li>
                            <?php if($idScenario != "" && $idScenario > 0){ ?>
                            <li>
                                <a href="edit_scenario.php#messages" data-toggle="tab" class="step">
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
                                <div class="row">
                                    <div class="col-md-12 ">
                                        <div class="form-group">
                                            <label class="control-label col-md-3" for="color">Couleur</label>
                                            <div class="col-md-9">
                                                <select name="color" id="color" class="form-control">

        <?php
                                                foreach($colors as $colorType=>$array){
                                                    echo "<optgroup label=\"".ucwords($colorType)."\">";
                                                    foreach($array as $key=>$colorTmp){
                                                        $selected=($key==$color) ? " selected=\"selected\" " : "";
                                                        echo "<option value=\"".$key."\" $selected>".ucwords($colorTmp)."</option>";
                                                    }
                                                    echo "</optgroup>";
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
                                            <label class="control-label col-md-3" for="icon">Icône</label>
                                            <div class="col-md-4">
                                                <input id="icon" name="icon" class="form-control" value="<?php echo $icon; ?>" type="text">
                                            </div>
                                            <div class="col-md-4">
                                                <a class="btn btn-info"  data-toggle="modal" href="edit_scenario.php#basic">Icônes</a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-12 ">
                                        <div class="form-group">
                                            <label class="control-label col-md-3" for="size">Taille</label>
                                            <div class="col-md-9">
                                                <div id="spinner1">
                                                        <div class="input-group input-small">
                                                                <input type="text" name="size" id="size" class="spinner-input form-control" maxlength="3" value="<?php echo $size; ?>" readonly>
                                                                <div class="spinner-buttons input-group-btn btn-group-vertical">
                                                                        <button type="button" class="btn spinner-up btn-xs blue">
                                                                            <i class="fa fa-angle-up"></i>
                                                                        </button>
                                                                        <button type="button" class="btn spinner-down btn-xs blue">
                                                                            <i class="fa fa-angle-down"></i>
                                                                        </button>
                                                                </div>
                                                        </div>
                                                </div>
                                                <span class="help-block">
                                                De 1 à 12</span>
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
                                foreach($devices as $deviceTmp){
                                    $i++;
                                    $messages=MessageDevice::getMessageDevicesForDevice($deviceTmp->id);
                                    if(count($messages) == 0){
                                        continue;
                                    }
                                    echo "<li class=\"dd-item\" data-id=\"d".$i."\">";
                                    echo "<div class=\"dd-handle\">".ucwords($deviceTmp->name)." - ".ucwords($deviceTmp->type)."</div>";
                                    echo "<ol class=\"dd-list\">";
                                    foreach($messages as $messageTmp){
                                        echo "<li class=\"dd-item messageId-".$messageTmp->id."\" data-id=\"".$i."\" >";
                                        echo "<button type=\"button\" class=\"btnAddMessage\" messageId=\"".$messageTmp->id."\" deviceName=\"".  addslashes(ucwords($deviceTmp->name)." - ".ucwords($deviceTmp->type))."\" style=\"display: block;float:right;\"><i class=\"fa fa-plus\"></i></button>";
                                        echo "<div class=\"dd-handle\" messageId=\"".$messageTmp->id."\">".ucwords($messageTmp->name)." - ".ucwords($messageTmp->type)."</div>";
                                        echo "</li>";
                                        $i++;
                                    }
                                    echo "</ol>";
                                    echo "</li>";
                                }
                                ?>
                                </ol>
                            </div>
                            <div class="dd  col-md-6" id="nestable_list_2">
                                <h4 style="float:left;">Affectés</h4>
                                <div style="float:right;">
                                    <input type="text" id="txtPause" style="margin-right:20px;width:80px;"/>
                                    <button class="btn btn-primary btnAddPause" type="button">Ajouter Pause</button>
                                </div>
                                <ol class="dd-list affect" style="clear: left;">
                            <?php
                                if(isset($scenarioMessages)){
                                    if(count($scenarioMessages) == 0){
                                        echo "<li class=\"dd-item\" data-id=\"1\">";
                                        echo "<div class=\"dd-handle\"></div>";
                                        echo "</li>";
                                    }
                                    $i=1;
                                    foreach($scenarioMessages as $scenarioMessage){
                                        $messageId = $messageName = $messageType = $deviceName = "";
                                        if(!is_null($scenarioMessage->messageid)){
                                            $messageTmp = MessageDevice::getMessageDevice($scenarioMessage->messageid);
                                            $deviceTmp = Device::getDevice($messageTmp->deviceId);
                                            $messageId = $messageTmp->id;
                                            $messageName = $messageTmp->name;
                                            $messageType = $messageTmp->type;
                                            $deviceName = $deviceTmp->name;
                                            
                                        } elseif(!is_null($scenarioMessage->pause)) {
                                            $messageId="pause-".$scenarioMessage->pause;
                                            $deviceName="pause";
                                            $messageName=$scenarioMessage->pause."s";
                                        }
                                        echo "<li class=\"dd-item messageId-".$messageId."\" data-id=\"".$i."\">";
                                        echo "<div class=\"dd-handle\" messageId=\"".$messageId."\">".ucwords($deviceName)." - ".ucwords($messageName)." - ".ucwords($messageType)."</div>";
                                        echo "<i class=\"fa fa-times btnRemoveScenarioMessage\" scenarioMessageId=\"".$scenarioMessage->id."\" messageId=\"".$messageId."\" style=\"float: right; cursor: pointer; position: absolute; top: 8px; right: 10px;\"></i>";
                                        echo "</li>";
                                        $i++;
                                    }
                                }
                            ?>
                                </ol>
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
    
    $('.fa').bind('click',function(e){
        //console.debug($('.fa').next().attr('class'));
        var classes=$(this).attr('class');
        classes.replace('','fa ');
        //console.debug(classes);
        $('.modalClose').click();
        $('#icon').val(classes);
    });
    
    var nestablecount=4;
    $('.btnAddMessage').bind('click',function(e){
        console.debug('ok rentre');
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
    
    $('.btnRemoveScenarioMessage').bind('click',function(e){
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