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
$devices = Device::getDevices();

?>
<!-- BEGIN PAGE -->
<div class="page-content-wrapper">
<div class="page-content">
    <div class="container-fluid">
    <div class="row">
            <div class="col-md-12">
                <!-- BEGIN PAGE TITLE & BREADCRUMB-->			
                <h3 class="page-title">
                    Objets				
                    <small>Liste des objets</small>
                </h3>
                <!--<a href="edit_device.php" style="float:right;margin: 20px 0 15px;"><button class="btn green" type="button"><i class="icon-plus"></i>&nbsp;Ajouter un device</button></a>-->
                <ul class="page-breadcrumb breadcrumb">
                    <li class="btn-group">
                        <button class="btn btn-primary" type="button" onclick="javascript:location.href='edit_device.php';"><i class="fa fa-plus"></i>Ajouter un objet</button>
                    </li>
                    <li>
                        <i class="fa fa-home"></i>
                        <a href="index.php">Admin</a>
                        <i class="fa fa-angle-right"></i>
                    </li>
                    <li>   
                        <a href="#">Liste des objets</a>
                    </li>
                </ul>
                <!-- END PAGE TITLE & BREADCRUMB-->
            </div>
    </div>
    <div class="row">
        <div class="col-md-12">
            <table class="table table-striped table-bordered table-hover" id="datatable">
            <!--<table id="deviceList" class="table table-striped table-bordered table-hover" id="sample_1">-->
                <thead>
                <tr>
                    <th>Nom</th>
                    <th>Type</th>
                    <th>Etat</th>
                    <th>Date</th>
                    <th>Actif</th>
                    <th>Action</th>
                </tr>
                </thead>
                <tbody>
<?php
foreach($devices as $device){
    $actif=($device->active) ? "fa fa-check-square" : "fa fa-square";
    $lastUpdate = "";
    if($device->last_update != "" && !is_null($device->last_update)){
        $lastUpdate = new DateTime($device->last_update);
        $lastUpdate = $lastUpdate->format('d-m-Y H:i');
    }
    echo "<tr class=\"odd gradeX linedevice-".$device->id."\" >";
    echo "<td><a class=\"black\" href=\"edit_device.php?idDevice=".$device->id."\">".$device->name."</a></td>";
    echo "<td>".$device->type."</td>";
    echo "<td>".$device->state."</td>";
    echo "<td>".$lastUpdate."</td>";
    echo "<td><i class=\"".$actif."\"></i></td>";
    echo "<td><a href=\"edit_device.php?idDevice=".$device->id."\"><i class=\"fa fa-edit\" style=\"color:black;\"></i></a>";
    echo "<a href=\"#\" style=\"margin-left:5px;\"><i class=\"fa fa-minus-circle  btnDeleteDevice\" style=\"color:black;\" deviceId=\"".$device->id."\"></i></a>";
    echo "<a href=\"admin_device.php#duplicate\" data-toggle=\"modal\" title=\"Dupliquer\" style=\"margin-left:5px;\"><i class=\"fa fa-plus btnDuplicateDevice\" style=\"color:black;\" deviceId=\"".$device->id."\"></i></a></td>";
    echo "</tr>";
}
?>
                </tbody>
            </table>
        </div>
    </div>
    </div>
</div>
    
<div class="modal fade" id="duplicate" tabindex="-1" role="basic" aria-hidden="true">
    <div class="modal-dialog">
        <input type="hidden" id="iddevice" value="" />
        <div class="modal-content">
            <div class="modal-header">
                <i class="fa fa-edit"></i>&nbsp;<span>Duplication Objet</span>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-12">
                        <div class="form-group">
                            <label class="control-label col-md-3" for="quantite">Quantité</label>
                            <div class="col-md-9">
                                <input id="quantite" name="quantite" class="form-control" value="" type="text">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button class="btn btn-default btnCancelDuplicate" data-dismiss="modal" type="button">Annuler</button>
                <button class="btn btn-primary btnSubmitDuplicate" type="button">Dupliquer</button>
            </div>
        </div>
    </div>
</div>
    
<script>
    jQuery(document).ready(function() {			
        $('.btnDuplicateDevice').bind('click',function(e){
            var idDevice=$(this).attr('deviceId');
            $('#iddevice').val(idDevice);
        });
        
        
        $('.btnSubmitDuplicate').bind('click',function(e){
            if($('#quantite').val() == ""){
                toastr.error("Veuillez saisir une quantité");
                $('#quantite').focus();
                return '';
            }
            
            $.ajax({
                url: "ajax/device_duplicate.php",
                type: "POST",
                data: {
                    deviceId: $('#iddevice').val(),
                    quantite: $('#quantite').val()
                },
                error: function(data){
                    toastr.error("Une erreur est survenue");
                },
                success: function(data){
                    if(data.responseText == "quantite" || data == "quantite"){
                        toastr.error("Quantité incorrecte");
                        return '';
                    }else {
                        if(data == "error"){
                            toastr.error("Une erreur est survenue");
                        } 
                        if(data != "error" && data != "exist") {
                            $('.btnCancelDuplicate').click();
                            toastr.info("Veuillez recharger","Objet dupliqué");

                        }
                        
                    }
                }
            });
        });
        
        // initiate layout and plugins
        $('.btnDeleteDevice').bind('click',function(e){
            var deviceId=$(this).attr('deviceId');
            if(confirm("Etes vous sur de vouloir supprimer le device?")){
                $.ajax({
                    url: "ajax/device_delete.php",
                    type: "POST",
                    data: {
                        deviceId: deviceId
                    },
                    error: function(data){
                        toastr.error("Une erreur est survenue");
                    },
                    complete: function(data){
                        if(data = "done"){
                            toastr.info("Device supprimé");
                            $('.linedevice-'+deviceId).toggle('hide');
                        } else {
                            toastr.error("Une erreur est survenue");
                        }
                    }
                });
            }
        });
        
        // begin first table
        $('#datatable').dataTable({
            "aoColumns": [
              { "bSortable": true },
              { "bSortable": true},
              { "bSortable": true},
              { "bSortable": true},             
              { "bSortable": false },
              { "bSortable": false }
            ],
            "aLengthMenu": [
                [5, 15, 20, -1],
                [5, 15, 20, "All"] // change per page values here
            ],
            // set the initial value
            "iDisplayLength": 20,
            "sPaginationType": "bootstrap",
            "oLanguage": {
                "sLengthMenu": "_MENU_ entrées",
                "oPaginate": {
                    "sPrevious": "Préc.",
                    "sNext": "Suiv."
                }
            },
            "aoColumnDefs": [
            ]
        });
        
    });
    var ui2 ="datatable";
</script>
<?php
include "modules/footer.php";
?>