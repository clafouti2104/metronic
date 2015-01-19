<?php
$includeCSS = $includeJS = array();
$includeJS[] = "/assets/global/plugins/select2/select2.min.js";
$includeJS[] = "/assets/global/plugins/data-tables/jquery.dataTables.min.js";
$includeJS[] = "/assets/global/plugins/data-tables/DT_bootstrap.js";
$includeCSS[] = "/assets/global/plugins/select2/select2.css";
$includeCSS[] = "/assets/global/plugins/data-tables/DT_bootstrap.css";
include "modules/header.php";
include "modules/sidebar.php";

$GLOBALS["dbconnec"] = connectDB();
include_once "models/Device.php";
include_once "models/Log.php";

$isPost=FALSE;
if(isset($_POST["formname"]) && $_POST["formname"]=="adminplugins"){
    $isPost=TRUE;
}

if($isPost){}

$countrys=array(
    //"GE"=>"Allemagne",
    //"US"=>"Allemagne",
    "FR"=>"France"
);

//Récupère meteo
$meteos=$websites=$cameras=$gauges=array();
$sqlPlugins = "SELECT * FROM config ";
$sqlPlugins .= " WHERE name IN ('meteo','website','camera','gauge')";
$stmt = $GLOBALS["dbconnec"]->prepare($sqlPlugins);
$stmt->execute(array());
while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    switch($row["name"]){
        case 'meteo':
            $meteos[$row["id"]]=$row["value"];
            break;
        case 'website':
            $websites[$row["id"]]=$row["value"];
            break;
        case 'camera':
            $cameras[$row["id"]]=$row["value"];
            break;
        case 'gauge':
            $gauges[$row["id"]]=$row["value"];
            break;
        default:
    }
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
                        Plugins				
                        <small>Gestion des plugins</small>
                    </h3>
                    <ul class="page-breadcrumb breadcrumb">
                        <li>
                            <i class="fa fa-home"></i>
                            <a href="index.php">Admin</a>
                            <i class="fa fa-angle-right"></i>
                        </li>
                        <li><a href="#">Plugins</a></li>
                    </ul>
                    <!-- END PAGE TITLE & BREADCRUMB-->
                </div>
        </div>
        <div class="row">
            <div class="tabbable-custom">
                <ul class="nav nav-tabs">
                    <li class="active">
                        <a data-toggle="tab" href="admin_plugins.php#meteo"> Météo </a>
                    </li>
                    <li>
                        <a data-toggle="tab" href="admin_plugins.php#website"> Site Web </a>
                    </li>
                    <li>
                        <a data-toggle="tab" href="admin_plugins.php#camera"> Caméra </a>
                    </li>
                    <li>
                        <a data-toggle="tab" href="admin_plugins.php#gauge"> Jauge </a>
                    </li>
                </ul>
                <div class="tab-content">
                    <div id="meteo" class="tab-pane active">
                        <div class="row">
                            <div class="col-md-12">
                                <h3 class="form-section"><i class="fa fa-sun-o "></i>&nbsp;Météo
                                &nbsp;&nbsp;&nbsp;&nbsp;<a data-toggle="modal" href="admin_plugins.php#addMeteo" class="btn btn-primary btnAddMeteo" type="button"><i class="fa fa-plus"></i>&nbsp;Ajouter une ville</a>
                                </h3>
                            <?php if(count($meteos) > 0){ ?>
                                <table class="table table-striped table-hover">
                                    <tr>
                                        <th>Ville</th>
                                        <th>Pays</th>
                                        <th>Action</th>
                                    </tr>
                                <?php
                                foreach($meteos as $meteoId=>$meteoName){
                                    $ville = explode(",",$meteoName);
                                    echo "<tr id=\"line-meteo-".$meteoId."\">";
                                    echo "<td>".$ville[0]."</td>";
                                    echo "<td>".$ville[1]."</td>";
                                    echo "<td>";
                                    echo "<a href=\"admin_plugins.php#addMeteo\" data-toggle=\"modal\" ><i class=\"fa fa-edit btnEditMeteo\" title=\"Editer\" idMeteo=\"".$meteoId."\" style=\"cursor:pointer;color:black;\"></i></a>&nbsp;&nbsp;";
                                    echo "<a href=\"admin_plugins.php#deleteMeteo\" data-toggle=\"modal\" ><i class=\"fa fa-trash-o btnDeleteMeteo\" title=\"Supprimer\" idMeteo=\"".$meteoId."\" style=\"cursor:pointer;color:black;\"></i></a>";
                                    echo "</td>";
                                    echo "</tr>";
                                }
                                ?>
                                </table>
                            <?php } ?>
                            </div>
                        </div>
                    </div>
                    <div id="website" class="tab-pane">
                        <div class="row">
                            <div class="col-md-12">
                                <h3 class="form-section"><i class="fa fa-sitemap "></i>&nbsp;Site Web
                                    &nbsp;&nbsp;&nbsp;&nbsp;<a data-toggle="modal" href="admin_plugins.php#addWebsite" class="btn btn-primary btnAddWebsite" type="button"><i class="fa fa-plus"></i>&nbsp;Ajouter une site</a>
                                </h3>
                            <?php if(count($websites) > 0){ ?>
                                <table class="table table-striped table-hover">
                                    <tr>
                                        <th>URL</th>
                                        <th>Action</th>
                                    </tr>
                                <?php
                                foreach($websites as $websiteId=>$websiteUrl){
                                    echo "<tr id=\"line-website-".$websiteId."\">";
                                    echo "<td>".$websiteUrl."</td>";
                                    echo "<td>";
                                    echo "<a href=\"admin_plugins.php#addWebsite\" data-toggle=\"modal\" ><i class=\"fa fa-edit btnEditWebsite\" title=\"Editer\" idWebsite=\"".$websiteId."\" style=\"cursor:pointer;color:black;\"></i></a>&nbsp;&nbsp;";
                                    echo "<a href=\"admin_plugins.php#deleteWebsite\" data-toggle=\"modal\" ><i class=\"fa fa-trash-o btnDeleteWebsite\" title=\"Supprimer\" idWebsite=\"".$websiteId."\" style=\"cursor:pointer;color:black;\"></i></a>&nbsp;&nbsp;";
                                    echo "</td>";
                                    echo "</tr>";
                                }
                                ?>
                                </table>
                            <?php } ?>
                            </div>
                        </div>
                    </div>
                    <div id="camera" class="tab-pane">
                        <div class="row">
                            <div class="col-md-12">
                                <h3 class="form-section"><i class="fa fa-video-camera "></i>&nbsp;Caméra
                                    &nbsp;&nbsp;&nbsp;&nbsp;<a data-toggle="modal" href="admin_plugins.php#addCamera" class="btn btn-primary btnAddCamera" type="button"><i class="fa fa-plus"></i>&nbsp;Ajouter une caméra</a>
                                </h3>
                            <?php if(count($cameras) > 0){ ?>
                                <table class="table table-striped table-hover">
                                    <tr>
                                        <th>Nom</th>
                                        <th>Action</th>
                                    </tr>
                                <?php
                                foreach($cameras as $cameraId=>$cameraName){
                                    echo "<tr id=\"line-camera-".$cameraId."\">";
                                    echo "<td>".$cameraName."</td>";
                                    echo "<td>";
                                    echo "<a href=\"admin_plugins.php#addCamera\" data-toggle=\"modal\" ><i class=\"fa fa-edit btnEditCamera\" title=\"Editer\" idCamera=\"".$cameraId."\" style=\"cursor:pointer;color:black;\"></i></a>&nbsp;&nbsp;";
                                    echo "<a href=\"admin_plugins.php#deleteCamera\" data-toggle=\"modal\" ><i class=\"fa fa-trash-o btnDeleteCamera\" title=\"Supprimer\" idCamera=\"".$cameraId."\" style=\"cursor:pointer;color:black;\"></i></a>";
                                    echo "</td>";
                                    echo "</tr>";
                                }
                                ?>
                                </table>
                            <?php } ?>
                            </div>
                        </div>
                    </div>
                    <div id="gauge" class="tab-pane">
                        <div class="row">
                            <div class="col-md-12">
                                <h3 class="form-section"><i class="fa fa-dashboard "></i>&nbsp;Gauge
                                    &nbsp;&nbsp;&nbsp;&nbsp;<a data-toggle="modal" href="admin_plugins.php#addGauge" class="btn btn-primary btnAddGauge" type="button"><i class="fa fa-plus"></i>&nbsp;Ajouter un gauge</a>
                                </h3>
                            <?php if(count($gauges) > 0){ ?>
                                <table class="table table-striped table-hover">
                                    <tr>
                                        <th>Device</th>
                                        <th>Action</th>
                                    </tr>
                                <?php
                                foreach($gauges as $gaugeId=>$gaugeDevice){
                                    $deviceJauge = Device::getDevice($gaugeDevice);
                                    $typeTmp=($deviceJauge->type != "") ? " - ".$deviceJauge->type : ""; 
                                    echo "<tr id=\"line-gauge-".$gaugeId."\">";
                                    echo "<td>".$deviceJauge->name.$typeTmp."</td>";
                                    echo "<td>";
                                    echo "<a href=\"admin_plugins.php#addGauge\" data-toggle=\"modal\" ><i class=\"fa fa-edit btnEditGauge\" title=\"Editer\" idGauge=\"".$gaugeId."\" style=\"cursor:pointer;color:black;\"></i></a>&nbsp;&nbsp;";
                                    echo "<a href=\"admin_plugins.php#deleteGauge\" data-toggle=\"modal\" ><i class=\"fa fa-trash-o btnDeleteGauge\" title=\"Supprimer\" idGauge=\"".$gaugeId."\" style=\"cursor:pointer;color:black;\"></i></a>";
                                    echo "</td>";
                                    echo "</tr>";
                                }
                                ?>
                                </table>
                            <?php } ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
            
<div class="modal fade" id="deleteMeteo" tabindex="-1" role="basic" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-body">
                <p class="modal-title">Confirmez vous la suppression du site?</p>
            </div>
            <div class="modal-footer">
                <button class="btn btn-default btnCancelMeteoDeletion" data-dismiss="modal" type="button">Annuler</button>
                <button class="btn red btnDeleteMeteoConfirm" type="button">Supprimer</button>
            </div>
        </div>
    </div>
</div>
    
<div class="modal fade" id="addMeteo" tabindex="-1" role="basic" aria-hidden="true">
    <div class="modal-dialog">
        <input type="hidden" id="idmeteo" value="" />
        <div class="modal-content">
            <div class="modal-header">
                <i class="fa fa-edit"></i>&nbsp;<span>Ajout d'une meteo</span>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-12">
                        <div class="form-group">
                            <label class="control-label col-md-3" for="meteoVille">Ville</label>
                            <div class="col-md-9">
                                <input id="meteoVille" name="meteoVille" class="form-control" value="" type="text">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row" style="margin-top:10px;">
                    <div class="col-md-12">
                        <div class="form-group">
                            <label class="control-label col-md-3" for="meteoPays">Pays</label>
                            <div class="col-md-9">
                                <select id="meteoPays" name="meteoPays" class="form-control">
<?php
                                    foreach($countrys as $countryId=>$countryName){
                                        //$selected=($countryId==$color) ? " selected=\"selected\" " : "";
                                        echo "<option value=\"".$countryId."\">".ucwords($countryName)."</option>";
                                    }
?>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button class="btn btn-default btnCancelMeteo" data-dismiss="modal" type="button">Annuler</button>
                <button class="btn btn-primary btnSubmitMeteo" type="button">Ajouter</button>
            </div>
        </div>
    </div>
</div>
    
<div class="modal fade" id="addGauge" tabindex="-1" role="basic" aria-hidden="true">
    <div class="modal-dialog">
        <input type="hidden" id="idgauge" value="" />
        <div class="modal-content">
            <div class="modal-header">
                <i class="fa fa-edit"></i>&nbsp;<span>Ajout d'une jauge</span>
            </div>
            <div class="modal-body">
                <div class="row" style="margin-top:10px;">
                    <div class="col-md-12">
                        <div class="form-group">
                            <label class="control-label col-md-3" for="gaugeDevice">Objet</label>
                            <div class="col-md-9">
                                <select id="gaugeDevice" name="gaugeDevice" class="form-control">
<?php
                                    foreach(Device::getDevices() as $device){
                                        $typeTmp=($device->type != "") ? " - ".$device->type : ""; 
                                        //$selected=($countryId==$color) ? " selected=\"selected\" " : "";
                                        echo "<option value=\"".$device->id."\">".ucwords($device->name).$typeTmp."</option>";
                                    }
?>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row" style="margin-top:10px;">
                    <div class="col-md-12">
                        <div class="form-group">
                            <label class="control-label col-md-3" for="gaugeMinimum">Minimum</label>
                            <div class="col-md-9">
                                <input id="gaugeMinimum" name="gaugeMinimum" class="form-control" value="" type="text">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row" style="margin-top:10px;">
                    <div class="col-md-12">
                        <div class="form-group">
                            <label class="control-label col-md-3" for="gaugeMaximum">Maximum</label>
                            <div class="col-md-9">
                                <input id="gaugeMaximum" name="gaugeMaximum" class="form-control" value="" type="text">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button class="btn btn-default btnCancelGauge" data-dismiss="modal" type="button">Annuler</button>
                <button class="btn btn-primary btnSubmitGauge" type="button">Ajouter</button>
            </div>
        </div>
    </div>
</div>
        
<div class="modal fade" id="deleteGauge" tabindex="-1" role="basic" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-body">
                <p class="modal-title">Confirmez vous la suppression de la jauge?</p>
            </div>
            <div class="modal-footer">
                <button class="btn btn-default btnCancelGaugeDeletion" data-dismiss="modal" type="button">Annuler</button>
                <button class="btn red btnDeleteGaugeConfirm" type="button">Supprimer</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="addWebsite" tabindex="-1" role="basic" aria-hidden="true">
    <div class="modal-dialog">
        <input type="hidden" id="idwebsite" value="" />
        <div class="modal-content">
            <div class="modal-header">
                <i class="fa fa-edit"></i>&nbsp;<span>Ajout d'un site</span>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-12">
                        <div class="form-group">
                            <label class="control-label col-md-3" for="websiteUrl">Url</label>
                            <div class="col-md-9">
                                <input id="websiteUrl" class="form-control" value="" type="text">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button class="btn btn-default btnCancelWebsite" data-dismiss="modal" type="button">Annuler</button>
                <button class="btn btn-primary btnSubmitWebsite" type="button">Ajouter</button>
            </div>
        </div>
    </div>
</div>
        
<div class="modal fade" id="deleteWebsite" tabindex="-1" role="basic" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-body">
                <p class="modal-title">Confirmez vous la suppression du site?</p>
            </div>
            <div class="modal-footer">
                <button class="btn btn-default btnCancelWebsiteDeletion" data-dismiss="modal" type="button">Annuler</button>
                <button class="btn red btnDeleteWebsiteConfirm" type="button">Supprimer</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="addCamera" tabindex="-1" role="basic" aria-hidden="true">
    <div class="modal-dialog">
        <input type="hidden" id="idcamera" value="" />
        <div class="modal-content">
            <div class="modal-header">
                <i class="fa fa-edit"></i>&nbsp;<span>Ajout d'une caméra</span>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-12">
                        <div class="form-group">
                            <label class="control-label col-md-3" for="cameraName">Nom</label>
                            <div class="col-md-9">
                                <input id="cameraName" class="form-control" value="" type="text">
                            </div>
                        </div>
                    </div>
                    <div class="col-md-12" style="margin-top:10px;">
                        <div class="form-group">
                            <label class="control-label col-md-3" for="cameraIp">Adresse IP</label>
                            <div class="col-md-9">
                                <input id="cameraIp" class="form-control" value="" type="text">
                            </div>
                        </div>
                    </div>
                    <div class="col-md-12" style="margin-top:10px;">
                        <div class="form-group">
                            <label class="control-label col-md-3" for="cameraStream">Flux Vidéo</label>
                            <div class="col-md-9">
                                <input id="cameraStream" class="form-control" value="" type="text">
                            </div>
                        </div>
                    </div>
                    <div class="col-md-12" style="margin-top:10px;">
                        <div class="form-group">
                            <label class="control-label col-md-3" for="cameraStreamImage">Flux Image</label>
                            <div class="col-md-9">
                                <input id="cameraStreamImage" class="form-control" value="" type="text">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button class="btn btn-default btnCancelCamera" data-dismiss="modal" type="button">Annuler</button>
                <button class="btn btn-primary btnSubmitCamera" type="button">Ajouter</button>
            </div>
        </div>
    </div>
</div>
    
<div class="modal fade" id="deleteCamera" tabindex="-1" role="basic" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-body">
                <p class="modal-title">Confirmez vous la suppression de la caméra?</p>
            </div>
            <div class="modal-footer">
                <button class="btn btn-default btnCancelCameraDeletion" data-dismiss="modal" type="button">Annuler</button>
                <button class="btn red btnDeleteCameraConfirm" type="button">Supprimer</button>
            </div>
        </div>
    </div>
</div>
    
<script type="text/javascript">
$( document ).ready(function() {
    $('.btnSubmitMeteo').bind('click',function(e){
        $.ajax({
            url: "ajax/meteo_submit.php",
            type: "POST",
            data: {
                meteoId: $('#idmeteo').val(),
                meteoPays: $('#meteoPays').val(),
                meteoVille: $('#meteoVille').val()
            },
            error: function(data){
                toastr.error("Une erreur est survenue");
            },
            success: function(data){
                if(data.responseText == "exist" || data == "exist"){
                    toastr.error("La météo existe déjà pour cette ville");
                }
                if(data == "error"){
                    toastr.error("Une erreur est survenue");
                } 
                if(data != "error" && data != "exist") {
                    $('.btnCancelMeteo').click();
                    toastr.info("Veuillez recharger","Message ajouté");
                    
                }
            }
        });
        $('#idmeteo').val('');
    });
    
    $('.btnEditMeteo').bind('click',function(e){
        var idMeteo=$(this).attr('idMeteo');
        $('#idmeteo').val(idMeteo);
        $.ajax({
            url: "ajax/meteo_load.php",
            type: "POST",
            data: {
                meteoId: idMeteo
            },
            error: function(data){
                toastr.error("Une erreur est survenue");
            },
            success: function(data){
                if(data.responseText == "error"){
                    toastr.error("Une erreur est survenue");
                } else {
                    eval(data);
                    eval(data.responseText);
                    //eval(data.responseText);
                }
            }
        });
    });
    
    $('.btnDeleteMeteo').bind('click',function(e){
        var idMeteo=$(this).attr('idMeteo');
        $('#idmeteo').val(idMeteo);
    });
    
    $('.btnDeleteMeteoConfirm').bind('click',function(e){
        var meteoId=$('#idmeteo').val();
        $.ajax({
            url: "ajax/delete_meteo.php",
            type: "POST",
            data: {
                meteoId:  meteoId
            },
            error: function(data){
                toastr.error("Une erreur est survenue");
            },
            complete: function(data){
                if(data.responseText == "success"){
                    $('.btnCancelMeteoDeletion').click();
                    $('#line-meteo-'+meteoId).fadeOut(300, function(){ 
                       $('#line-meteo-'+meteoId).remove(); 
                    });
                    toastr.info("Message supprimé");
                }
                if(data.responseText == "error"){
                    toastr.error("Une erreur est survenue");
                }
            }
        });
        $('#idmeteo').val('');
    });
    $('.btnSubmitGauge').bind('click',function(e){
        $.ajax({
            url: "ajax/gauge_submit.php",
            type: "POST",
            data: {
                gaugeId: $('#idgauge').val(),
                gaugeMinimum: $('#gaugeMinimum').val(),
                gaugeMaximum: $('#gaugeMaximum').val(),
                gaugeDevice: $('#gaugeDevice').val()
            },
            error: function(data){
                toastr.error("Une erreur est survenue");
            },
            success: function(data){
                if(data.responseText == "exist" || data == "exist"){
                    toastr.error("La jauge existe déjà pour cet objet");
                }
                if(data == "error"){
                    toastr.error("Une erreur est survenue");
                } 
                if(data != "error" && data != "exist") {
                    $('.btnCancelGauge').click();
                    toastr.info("Veuillez recharger","Jauge ajouté");
                    
                }
            }
        });
        $('#idgauge').val('');
    });
    
    $('.btnEditGauge').bind('click',function(e){
        var idGauge=$(this).attr('idGauge');
        $('#idgauge').val(idGauge);
        $.ajax({
            url: "ajax/gauge_load.php",
            type: "POST",
            data: {
                gaugeId: idGauge
            },
            error: function(data){
                toastr.error("Une erreur est survenue");
            },
            success: function(data){
                if(data.responseText == "error"){
                    toastr.error("Une erreur est survenue");
                } else {
                    eval(data);
                    eval(data.responseText);
                }
            }
        });
    });
    
    $('.btnDeleteGauge').bind('click',function(e){
        var idGauge=$(this).attr('idGauge');
        $('#idgauge').val(idGauge);
    });
    
    $('.btnDeleteGaugeConfirm').bind('click',function(e){
        var gaugeId=$('#idgauge').val();
        $.ajax({
            url: "ajax/delete_gauge.php",
            type: "POST",
            data: {
                gaugeId:  gaugeId
            },
            error: function(data){
                toastr.error("Une erreur est survenue");
            },
            complete: function(data){
                if(data.responseText == "success"){
                    $('.btnCancelGaugeDeletion').click();
                    $('#line-gauge-'+gaugeId).fadeOut(300, function(){ 
                       $('#line-gauge-'+gaugeId).remove(); 
                    });
                    toastr.info("Message supprimé");
                }
                if(data.responseText == "error"){
                    toastr.error("Une erreur est survenue");
                }
            }
        });
        $('#idgauge').val('');
    });
    
    $('.btnSubmitWebsite').bind('click',function(e){
        $.ajax({
            url: "ajax/website_submit.php",
            type: "POST",
            data: {
                websiteId: $('#idwebsite').val(),
                urlSite: $('#websiteUrl').val()
            },
            error: function(data){
                toastr.error("Une erreur est survenue");
            },
            success: function(data){
                if(data.responseText == "exist" || data == "exist"){
                    toastr.error("Le site web existe déjà ");
                }
                if(data == "error"){
                    toastr.error("Une erreur est survenue");
                } 
                if(data != "error" && data != "exist") {
                    $('.btnCancelWebsite').click();
                    toastr.info("Veuillez recharger","Message ajouté");
                    
                }
            }
        });
    });
    
    $('.btnEditWebsite').bind('click',function(e){
        var idWebsite=$(this).attr('idWebsite');
        $('#idwebsite').val(idWebsite);
        $.ajax({
            url: "ajax/website_load.php",
            type: "POST",
            data: {
                websiteId: idWebsite
            },
            error: function(data){
                toastr.error("Une erreur est survenue");
            },
            success: function(data){
                if(data.responseText == "error"){
                    toastr.error("Une erreur est survenue");
                } else {
                    eval(data);
                    eval(data.responseText);
                    //eval(data.responseText);
                }
            }
        });
        $('#idwebsite').val('');
        
    });
    
    $('.btnDeleteWebsite').bind('click',function(e){
        var idWebsite=$(this).attr('idWebsite');
        $('#idwebsite').val(idWebsite);
    });
    
    
    $('.btnDeleteWebsiteConfirm').bind('click',function(e){
        var websiteId=$('#idwebsite').val();
        $.ajax({
            url: "ajax/delete_website.php",
            type: "POST",
            data: {
                websiteId:  websiteId
            },
            error: function(data){
                toastr.error("Une erreur est survenue");
            },
            complete: function(data){
                if(data.responseText == "success"){
                    $('.btnCancelWebsiteDeletion').click();
                    $('#line-website-'+websiteId).fadeOut(300, function(){ 
                       $('#line-website-'+websiteId).remove(); 
                    });
                    toastr.info("Site web supprimé");
                }
                if(data.responseText == "error"){
                    toastr.error("Une erreur est survenue");
                }
            }
        });
        $('#idwebsite').val('');
    });
    
    $('.btnSubmitCamera').bind('click',function(e){
        $.ajax({
            url: "ajax/camera_submit.php",
            type: "POST",
            data: {
                cameraId: $('#idcamera').val(),
                name: $('#cameraName').val(),
                ip: $('#cameraIp').val(),
                stream: $('#cameraStream').val(),
                streamImage: $('#cameraStreamImage').val()
            },
            error: function(data){
                toastr.error("Une erreur est survenue");
            },
            success: function(data){
                if(data.responseText == "exist" || data == "exist"){
                    toastr.error("Le site web existe déjà ");
                }
                if(data == "error"){
                    toastr.error("Une erreur est survenue");
                } 
                if(data != "error" && data != "exist") {
                    $('.btnCancelCamera').click();
                    toastr.info("Veuillez recharger","Message ajouté");
                    
                }
            }
        });
        $('#idcamera').val('');
    });
    
    $('.btnEditCamera').bind('click',function(e){
        var idCamera=$(this).attr('idCamera');
        $('#idcamera').val(idCamera);
        $.ajax({
            url: "ajax/camera_load.php",
            type: "POST",
            data: {
                cameraId: idCamera
            },
            error: function(data){
                toastr.error("Une erreur est survenue");
            },
            success: function(data){
                if(data.responseText == "error"){
                    toastr.error("Une erreur est survenue");
                } else {
                    eval(data);
                    eval(data.responseText);
                    //eval(data.responseText);
                }
            }
        });
        //
    });
    
    $('.btnDeleteCamera').bind('click',function(e){
        var idCamera=$(this).attr('idCamera');
        $('#idcamera').val(idCamera);
    });
    
    
    $('.btnDeleteCameraConfirm').bind('click',function(e){
        var cameraId=$('#idcamera').val();
        $.ajax({
            url: "ajax/delete_camera.php",
            type: "POST",
            data: {
                cameraId:  cameraId
            },
            error: function(data){
                toastr.error("Une erreur est survenue");
            },
            complete: function(data){
                if(data.responseText == "success"){
                    $('.btnCancelCameraDeletion').click();
                    $('#line-camera-'+cameraId).fadeOut(300, function(){ 
                       $('#line-camera-'+cameraId).remove(); 
                    });
                    toastr.info("Site web supprimé");
                }
                if(data.responseText == "error"){
                    toastr.error("Une erreur est survenue");
                }
            }
        });
    });
    
});
</script>
<?php
include "modules/footer.php";
?>