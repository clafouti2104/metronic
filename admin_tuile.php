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

include "models/Device.php";
include "models/Tuile.php";
$tuiles = Tuile::getTuiles();

?>
<!-- BEGIN PAGE -->
<div class="page-content-wrapper">
<div class="page-content">
    <div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <!-- BEGIN PAGE TITLE & BREADCRUMB-->			
            <h3 class="page-title">
                Tuile				
                <small>Liste des tuiles</small>
            </h3>
            <!--<a href="edit_device.php" style="float:right;margin: 20px 0 15px;"><button class="btn green" type="button"><i class="icon-plus"></i>&nbsp;Ajouter un device</button></a>-->
            <ul class="page-breadcrumb breadcrumb">
                <li class="btn-group">
                    <button class="btn btn-primary" type="button" onclick="javascript:location.href='edit_tuile.php';"><i class="fa fa-plus"></i>Ajouter une tuile</button>
                </li>
                <li>
                    <i class="fa fa-home"></i>
                    <a href="index.php">Admin</a>
                    <i class="fa fa-angle-right"></i>
                </li>
                <li>   
                    <a href="#">Liste des tuiles</a>
                </li>
            </ul>
            <!-- END PAGE TITLE & BREADCRUMB-->
        </div>
    </div>
    <div class="row">
        <div class="col-md-12">
            <table class="table table-striped table-bordered table-hover" id="datatable">
                <thead>
                <tr>
                    <th>Nom</th>
                    <th>Device</th>
                    <th>Action</th>
                </tr>
                </thead>
                <tbody>
<?php
foreach($tuiles as $tuile){
    $name="";
    if($tuile->deviceid != ""){
        $device=Device::getDevice($tuile->deviceid);
        $name=(is_object($device)) ? $device->name : "";
    }
    echo "<tr class=\"odd gradeX linetuile-".$tuile->id."\" >";
    echo "<td>".$tuile->name."</td>";
    echo "<td>".$name."</td>";
    echo "<td><a href=\"edit_tuile.php?idTuile=".$tuile->id."\"><i class=\"fa fa-edit\" style=\"color:black;\"></i></a>";
    echo "<a href=\"#\" style=\"margin-left:5px;\"><i class=\"fa fa-minus-circle btnDeleteTuile\" style=\"color:black;\" tuileId=\"".$tuile->id."\"></i></a></td>";
    echo "</tr>";
}
?>
                </tbody>
            </table>
        </div>
    </div>
    </div>
</div>
<script>
    jQuery(document).ready(function() {			
        $('.btnDeleteTuile').bind('click',function(e){
            var tuileId=$(this).attr('tuileId');
            if(confirm("Etes vous sur de vouloir supprimer le tuile?")){
                $.ajax({
                    url: "ajax/tuile_delete.php",
                    type: "POST",
                    data: {
                        tuileId: tuileId
                    },
                    error: function(data){
                        toastr.error("Une erreur est survenue");
                    },
                    complete: function(data){
                        if(data = "done"){
                            toastr.info("Scénario supprimé");
                            $('.linetuile-'+tuileId).toggle('hide');
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
              { "bSortable": true },
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
</script>
<?php
include "modules/footer.php";
?>