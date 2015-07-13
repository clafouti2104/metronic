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

include "models/Report.php";
include "models/ReportDevice.php";
$reports = Report::getReports();
?>
<!-- BEGIN PAGE -->
<div class="page-content-wrapper">
<div class="page-content">
    <div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <!-- BEGIN PAGE TITLE & BREADCRUMB-->			
            <h3 class="page-title">
                Rapport
                <small>Liste des rapports</small>
            </h3>
            <!--<a href="edit_device.php" style="float:right;margin: 20px 0 15px;"><button class="btn green" type="button"><i class="icon-plus"></i>&nbsp;Ajouter un device</button></a>-->
            <ul class="page-breadcrumb breadcrumb">
                <li class="btn-group">
                    <button class="btn btn-primary" type="button" onclick="javascript:location.href='edit_report.php';"><i class="fa fa-plus"></i>Ajouter un rapport</button>
                </li>
                <li>
                    <i class="fa fa-home"></i>
                    <a href="index.php">Admin</a>
                    <i class="fa fa-angle-right"></i>
                </li>
                <li>   
                    <a href="#">Liste des rapports</a>
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
                    <th>Nbr Objets</th>
                    <th>Action</th>
                </tr>
                </thead>
                <tbody>
<?php
foreach($reports as $report){
    $reportMessages=ReportDevice::getReportDevicesForReport($report->id);
    echo "<tr class=\"odd gradeX linereport-".$report->id."\" >";
    echo "<td><a class=\"black\" href=\"edit_report.php?idReport=".$report->id."\">".$report->name."</a></td>";
    echo "<td>".count($reportMessages)."</td>";
    echo "<td>";
    echo "<a href=\"edit_report.php?idReport=".$report->id."\"><i class=\"fa fa-edit\"  style=\"color:black;\" ></i></a>";
    echo "<a href=\"show_report.php?idReport=".$report->id."\" style=\"margin-left:5px;\"><i class=\"fa fa-search\"  style=\"color:black;\" ></i></a>";
    echo "<a href=\"#\" style=\"margin-left:5px;\"><i class=\"fa fa-minus-circle btnDeleteReport\" reportId=\"".$report->id."\"  style=\"color:black;\"></i></a>";
    echo "</td>";
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
        $('.btnDeleteReport').bind('click',function(e){
            var reportId=$(this).attr('reportId');
            if(confirm("Etes vous sur de vouloir supprimer le report?")){
                $.ajax({
                    url: "ajax/report_delete.php",
                    type: "POST",
                    data: {
                        reportId: reportId
                    },
                    error: function(data){
                        toastr.error("Une erreur est survenue");
                    },
                    complete: function(data){
                        if(data = "done"){
                            toastr.info("Report supprimée");
                            $('.linereport-'+reportId).toggle('hide');
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
              { "bSortable": false},
              { "bSortable": false}
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