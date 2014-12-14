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
include_once "models/Liste.php";
include_once "models/ListeMessage.php";
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
if(isset($_POST["formname"]) && $_POST["formname"]=="editliste"){
    $isPost=TRUE;
}

if($isPost && isset($_POST["idliste"])){
    $name= $_POST["name"];
    $description= $_POST["description"];
    $color= $_POST["color"];
    $icon= $_POST["icon"];
    $size= ($_POST["size"] == "" ) ? 3 : $_POST["size"];
    $_POST["size"]= ($_POST["size"] == "" ) ? 3 : $_POST["size"];
    $idListe=$_POST["idliste"];
    $liste = Liste::getListe($idListe);
} else {
    $idListe=0;
    $txtMode="Création";
    $txtModeDesc="Création d'une liste";
    if(isset($_GET["idListe"])){
        $idListe=$_GET["idListe"];
        $liste = Liste::getListe($idListe);
    } 
    $name= (!is_object($liste)) ? NULL : $liste->name;
    $icon= (!is_object($liste)) ? NULL : $liste->icon;
    $description= (!is_object($liste)) ? NULL : $liste->description;
    $color= (!is_object($liste)) ? NULL : $liste->color;
    $size= (!is_object($liste)) ? NULL : $liste->size;
}

if($isPost){
    $_POST["icon"]=str_replace("fa ","",$_POST["icon"]);
    if($_POST["idliste"]>0){
        $sql="UPDATE liste SET name='".$_POST["name"]."', description='".$_POST["description"]."', color='".$_POST["color"]."', size=".$_POST["size"].", icon='".$_POST["icon"]."'";
        $sql.=" WHERE id=".$_POST["idliste"];
        
        $stmt = $GLOBALS["dbconnec"]->exec($sql);
        $info="La liste a été modifiée";
    } else {
        $liste=Liste::createListe($_POST["name"], $_POST["description"], $_POST["color"],$_POST["size"], $_POST["icon"]);
        $idListe=$liste->id;
        $info="Le liste a été créée";
    }
}

if(isset($idListe) && $idListe > 0){
    $listeMessages = ListeMessage::getListeMessagesForListe($idListe);
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
                Liste				
                <small><?php echo $txtModeDesc; ?></small>
            </h3>
            <!--<a href="edit_device.php" style="float:right;margin: 20px 0 15px;"><button class="btn green" type="button"><i class="icon-plus"></i>&nbsp;Ajouter un device</button></a>-->
            <ul class="page-breadcrumb breadcrumb">
                <li class="btn-group">
                    <button class="btn btn-primary" type="button" onclick="javascript:location.href='edit_liste.php';"><i class="fa fa-plus"></i>Ajouter une liste</button>
                </li>
                <li>
                    <i class="fa fa-home"></i>
                    <a href="index.php">Admin</a>
                    <i class="fa fa-angle-right"></i>
                </li>
                <li>   
                    <a href="admin_liste.php">Liste des listes</a>
                    <i class="fa fa-angle-right"></i>
                </li>
                <li>
                    <a href="#"><?php echo $txtMode; ?></a>
                    <i class="fa fa-angle-right"></i>
                </li>
            </ul>
            <?php if(isset($info)){echo "<div class=\"alert alert-success\">".$info."</div>";}?>
            <!-- END PAGE TITLE & BREADCRUMB-->
        </div>
    </div>
    <div class="row">
        <div class="col-md-12" id="form_wizard_1">
            <form class="form-horizontal" method="POST" action="edit_liste.php">
                <input type="hidden" name="formname" id="formname" value="editliste" />
                <input type="hidden" name="idliste" id="idliste" value="<?php echo $idListe; ?>" />
                <div class="form-wizard">
                    <div class="form-body form">
                        <ul class="nav nav-pills nav-justified steps">
                            <li>
                                <a href="edit_liste.php#parameters" data-toggle="tab" class="step active">
                                <span class="number">
                                1 </span>
                                <span class="desc">
                                <i class="fa fa-check"></i> Général </span>
                                </a>
                            </li>
                            <li>
                                <a href="edit_liste.php#messages" data-toggle="tab" class="step">
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
                                    echo "<li class=\"dd-item\" style=\"\" data-id=\"d".$i."\">";
                                    echo "<button type=\"button\" class=\"btnAddDevice\" deviceId=\"".$deviceTmp->id."\" deviceName=\"".  addslashes(ucwords($deviceTmp->name)." - ".ucwords($deviceTmp->type))."\" style=\"display: block;float:right;\"><i class=\"fa fa-plus\"></i></button>";
                                    echo "<div class=\"dd-handle\" deviceId=\"".$deviceTmp->id."\">".ucwords($deviceTmp->name)." - ".ucwords($deviceTmp->type)."</div>";
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
                                    if(count($listeMessages) == 0){
                                        echo "<li class=\"dd-item\" data-id=\"1\">";
                                        echo "<div class=\"dd-handle\"></div>";
                                        echo "</li>";
                                    }
                                    $i=1;
                                    foreach($listeMessages as $listeMessage){
                                        $deviceTmp = Device::getDevice($listeMessage->deviceid);
                                        
                                        echo "<li class=\"dd-item listeDeviceId-".$listeMessage->id."\" data-id=\"".$i."\">";
                                        echo "<div class=\"dd-handle\" deviceId=\"".$deviceTmp->id."\">".ucwords($deviceTmp->name)." - ".ucwords($deviceTmp->type);
                                        echo "</div>";
                                        echo "<select class=\"listeDeviceMessage listeDeviceId-".$listeMessage->id."\" listeDeviceId=\"".$listeMessage->id."\" style=\"float: right; cursor: pointer; position: absolute; top: 8px; right: 140px;\"><option></option>";
                                        foreach(MessageDevice::getMessageDevicesForDevice($deviceTmp->id) as $msgTmp){
                                            $selected = ($msgTmp->id == $listeMessage->messageid) ? " selected=\"selected\" " : "";
                                            echo "<option value=\"".$msgTmp->id."\" $selected>".ucwords($msgTmp->name)."</option>";
                                        }
                                        echo "</select>";
                                        echo "<i class=\"fa fa-times btnRemoveDevice\" listeDeviceId=\"".$listeMessage->id."\" style=\"float: right; cursor: pointer; position: absolute; top: 8px; right: 10px;\"></i>";
                                        echo "</li>";
                                        $i++;
                                    }
                            ?>
                                </ol>
                            </div>
                        </div><!-- class="tab-pane" id="messages" -->
                          
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-actions">
                                    <button class="btn blue" deviceid="submit">
                                        <i class="icon-ok"></i>Valider
                                    </button>
                                    <a href="admin_liste.php"><button class="btn" deviceid="button">Retourner</button></a>
                                </div>
                            </div>
                        </div>
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
    
    $('.btnRemoveDevice').bind('click',function(e){
        var listeDeviceId = $(this).attr('listeDeviceId');
        $.ajax({
            url: "ajax/delete_liste_message.php",
            method: "POST",
            data: {
                listeDeviceId:  listeDeviceId
            },
            error: function(data){
                toastr.error("Une erreur est survenue");
            },
            complete: function(data){
                if(data.responseText == "error"){
                    toastr.error("Une erreur est survenue");
                } else {
                    $('#nestable_list_2 .listeDeviceId-'+listeDeviceId).toggle('hide');
                    //domoUpdateOutput();
                }
            }
        });
    });
    
    $('.listeDeviceMessage').change(function() {
        var listeDeviceId = $(this).attr('listeDeviceId');
        //console.debug('ok');
        //console.debug($(this).val());
        
        $.ajax({
            url: "ajax/liste_message_affect.php",
            method: "POST",
            data: {
                listeDeviceId:  listeDeviceId,
                msgId:  $(this).val()
            },
            error: function(data){
                toastr.error("Une erreur est survenue");
            },
            complete: function(data){
                if(data.responseText == "error"){
                    toastr.error("Une erreur est survenue");
                } else {
                    //$('#nestable_list_2 .listeDeviceId-'+listeDeviceId).toggle('hide');
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
        if($('#idliste').val() == ""){
            toastr.error("Une erreur est survenue", "Sauvegarder le liste");
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
        $('#nestable_list_2 select.listeDeviceMessage').each(function( index ) {
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
            url: "ajax/liste_update_order.php",
            method: "POST",
            data: {
                listeid:  $('#idliste').val(),
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
        $('ol.affect').append('<li class="dd-item" data-id="' + nestablecount + '"><div class="dd-handle" deviceId="'+deviceId+'">' + deviceName + '</div><select class="listeDeviceMessage" style="float: right; cursor: pointer; position: absolute; top: 8px; right: 140px;"></select></li>');
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
        $('#nestable_list_2 select.listeDeviceMessage').each(function( index ) {
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
            url: "ajax/liste_update_order.php",
            method: "POST",
            data: {
                listeid:  $('#idliste').val(),
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