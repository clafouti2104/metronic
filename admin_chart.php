<?php
$includeCSS = $includeJS = array();
$includeJS[] = "/assets/global/plugins/select2/select2.min.js";
$includeJS[] = "/assets/global/plugins/data-tables/jquery.dataTables.min.js";
$includeJS[] = "/assets/global/plugins/data-tables/DT_bootstrap.js";
$includeJS[]="/assets/global/plugins/bootstrap-toastr/toastr.min.js";
$includeJS[]="/assets/admin/pages/scripts/ui-toastr.js";

$includeCSS[] = "/assets/global/plugins/select2/select2.css";
$includeCSS[] = "/assets/global/plugins/data-tables/DT_bootstrap.css";
$includeCSS[] = "/assets/global/plugins/bootstrap-toastr/toastr.min.css";

include "modules/header.php";
include "modules/sidebar.php";

$GLOBALS["dbconnec"] = connectDB();

include "models/Chart.php";
$charts = Chart::getCharts();

?>
<!-- BEGIN PAGE -->
<div class="page-content-wrapper">
<div class="page-content">
    <div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <!-- BEGIN PAGE TITLE & BREADCRUMB-->			
            <h3 class="page-title">
                Graphique				
                <small>Liste des graphiques</small>
            </h3>
            <!--<a href="edit_device.php" style="float:right;margin: 20px 0 15px;"><button class="btn green" type="button"><i class="icon-plus"></i>&nbsp;Ajouter un device</button></a>-->
            <ul class="page-breadcrumb breadcrumb">
                <li class="btn-group">
                    <button class="btn btn-primary" type="button" onclick="javascript:location.href='edit_chart.php';"><i class="fa fa-plus"></i>Ajouter un graphique</button>
                </li>
                <li>
                    <i class="fa fa-home"></i>
                    <a href="index.php">Admin</a>
                    <i class="fa fa-angle-right"></i>
                </li>
                <li>   
                    <a href="#">Liste des graphiques</a>
                </li>
            </ul>
            <!-- END PAGE TITLE & BREADCRUMB-->
        </div>
    </div>
    <div class="row">
        <div class="col-md-12">
            <table id="datatable" class="table table-striped table-bordered table-hover">
                <thead>
                <tr>
                    <th>Nom</th>
                    <th>Type</th>
                    <th>Prix</th>
                    <th>Action</th>
                </tr>
                </thead>
                <tbody>
<?php
foreach($charts as $chart){
    $name="";
    if($chart->deviceid != ""){
        //$device=Device::getDevice($chart->deviceid);
        //$name=(is_object($device)) ? $device->name : "";
    }
    $money=($chart->price) ? "<i class=\"fa fa-eur\"></i>" : "";
    echo "<tr class=\"odd gradeX linechart-".$chart->id."\" >";
    echo "<td><a class=\"black\" href=\"edit_chart.php?idChart=".$chart->id."\">".$chart->name."</a></td>";
    echo "<td>".$chart->type."</td>";
    echo "<td>".$money."</td>";
    echo "<td><a href=\"edit_chart.php?idChart=".$chart->id."\"><i class=\"fa fa-edit\" style=\"color:black;\"></i></a>";
    echo "<a href=\"#\" style=\"margin-left:5px;\"><i class=\"fa fa-minus-circle btnDeleteChart\" style=\"color:black;\" chartId=\"".$chart->id."\"></i></a></td>";
    echo "</tr>";
}
?>
                </tbody>
            </table>
        </div>
    </div>
    </div>
</div>

<script type="text/javascript">
    var ui="toastr";
    jQuery(document).ready(function() {			
        $('.btnDeleteChart').bind('click',function(e){
            var chartId=$(this).attr('chartId');
            console.debug('pl');
            if(confirm("Etes vous sur de vouloir supprimer le chart?")){
                $.ajax({
                    url: "ajax/chart_delete.php",
                    type: "POST",
                    data: {
                        chartId: chartId
                    },
                    error: function(data){
                        toastr.error("Une erreur est survenue");
                    },
                    complete: function(data){
                        if(data = "done"){
                            toastr.info("Graphique supprimé");
                            $('.linechart-'+chartId).toggle('hide');
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