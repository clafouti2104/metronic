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

include "models/Scenario.php";
include "models/Device.php";
include "models/MessageDevice.php";
$devices = Device::getDevices();
$scenarios = Scenario::getScenarios();

?>
<!-- BEGIN PAGE -->
<div class="page-content-wrapper">
<div class="page-content">
    <div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <!-- BEGIN PAGE TITLE & BREADCRUMB-->			
            <h3 class="page-title">
                API				
                <small>Liste des commandes</small>
            </h3>
            <!--<a href="edit_device.php" style="float:right;margin: 20px 0 15px;"><button class="btn green" type="button"><i class="icon-plus"></i>&nbsp;Ajouter un device</button></a>-->
            <ul class="page-breadcrumb breadcrumb">
                <li>
                    <i class="fa fa-home"></i>
                    <a href="index.php">Admin</a>
                    <i class="fa fa-angle-right"></i>
                </li>
                <li>   
                    <a href="#">API: Commandes</a>
                </li>
            </ul>
            <!-- END PAGE TITLE & BREADCRUMB-->
        </div>
    </div>
    <div class="row">
        <blockquote style="font-size:13px;">
            <p>Veuillez utiliser la syntaxe suivante pour mettre à jour un status:<br/>http://192.168.1.104/metronic/update_status.php?idDevice=ID_DEVICE&status=STATUS</p>
            <footer>avec ID_DEVICE pour le numéro de l'objet et STATUS pour l'état à enregistrer </footer>
        </blockquote>
    </div>
    <div class="row">
        <div class="col-md-12">
            <p>Liste des Objets</p>
            <table class="table table-striped table-bordered table-hover" id="datatable">
                <thead>
                <tr>
                    <th>Nom</th>
                    <th>URL</th>
                </tr>
                </thead>
                <tbody>
<?php
foreach($devices as $device){
    $tmpType=($device->type != "") ? " - ".$device->type : "";
    echo "<tr class=\"odd gradeX linedevice-".$device->id."\" >";
    echo "<td><a class=\"black\" href=\"edit_device.php?idDevice=".$device->id."\">".$device->name.$tmpType."</a></td>";
    echo "<td>&nbsp;</td>";
    echo "</tr>";

    //State
    echo "<tr class=\"odd gradeX\">";
    echo "<td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Status</td>";
    echo "<td><input type=\"text\" class=\"form-control\" value=\"http://192.168.1.104/metronic/api/get_status.php?idDevice=".$device->id."\" /></td>";
    echo "</tr>";

    //Action
    $messages=MessageDevice::getMessageDevicesForDevice($device->id);
    foreach($messages as $messageTmp){
        echo "<tr class=\"odd gradeX\">";
        echo "<td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;".$messageTmp->name."</td>";
        echo "<td><input type=\"text\" class=\"form-control\" value=\"http://192.168.1.104/metronic/api/execute_message.php?idMessage=".$messageTmp->id."\" /></td>";
        echo "</tr>";
    }
}
?>
                </tbody>
            </table>
        </div>
        <div class="col-md-12">
            <p>Liste des Scénarios</p>
            <table class="table table-striped table-bordered table-hover" id="datatable">
                <thead>
                <tr>
                    <th>Nom</th>
                    <th>URL</th>
                </tr>
                </thead>
                <tbody>
<?php
foreach($scenarios as $scenario){
    echo "<tr class=\"odd gradeX linedevice-".$scenario->id."\" >";
    echo "<td><a class=\"black\" href=\"execute_scenario.php?idScenario=".$scenario->id."\">".$scenario->name."</a></td>";
    echo "<td><input type=\"text\" class=\"form-control\" value=\"http://192.168.1.104/metronic/api/execute_scenario.php?idScenario=".$scenario->id."\" /></td>";
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