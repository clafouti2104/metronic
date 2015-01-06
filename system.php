<?php
$includeCSS = $includeJS = array();

$includeCSS[] = "/assets/global/plugins/clockface/css/clockface.css";
$includeCSS[] = "/assets/global/plugins/bootstrap-datepicker/css/datepicker3.css";
$includeCSS[] = "/assets/global/plugins/bootstrap-timepicker/css/bootstrap-timepicker.min.css";
$includeCSS[] = "/assets/global/plugins/bootstrap-colorpicker/css/colorpicker.css";
$includeCSS[] = "/assets/global/plugins/bootstrap-daterangepicker/daterangepicker-bs3.css";
$includeCSS[] = "/assets/global/plugins/bootstrap-datetimepicker/css/datetimepicker.css";
$includeCSS[] = "/assets/global/plugins/bootstrap-datepicker/css/datepicker3.css";
$includeCSS[] = "/assets/global/plugins/select2/select2.css";
$includeCSS[] = "/assets/global/plugins/data-tables/DT_bootstrap.css";

$includeJS[] = "/assets/global/plugins/bootstrap-datepicker/js/bootstrap-datepicker.js";
$includeJS[] = "/assets/global/plugins/bootstrap-timepicker/js/bootstrap-timepicker.min.js";
$includeJS[] = "/assets/global/plugins/clockface/js/clockface.js";
$includeJS[] = "/assets/global/plugins/bootstrap-daterangepicker/moment.min.js";
$includeJS[] = "/assets/global/plugins/bootstrap-daterangepicker/daterangepicker.js";
$includeJS[] = "/assets/global/plugins/bootstrap-colorpicker/js/bootstrap-colorpicker.js";
$includeJS[] = "/assets/global/plugins/bootstrap-datetimepicker/js/bootstrap-datetimepicker.min.js";
$includeJS[] = "/assets/global/plugins/bootstrap-datepicker/js/bootstrap-datepicker.js";
$includeJS[] = "/assets/admin/pages/scripts/components-pickers.js";
$includeJS[] = "/assets/global/plugins/select2/select2.min.js";
$includeJS[] = "/assets/global/plugins/data-tables/jquery.dataTables.min.js";
$includeJS[] = "/assets/global/plugins/data-tables/DT_bootstrap.js";
include "modules/header.php";
include "modules/sidebar.php";

$GLOBALS["dbconnec"] = connectDB();
include_once "models/Log.php";
include_once "models/Device.php";

$devices=Device::getDevices();
$deviceByType=$devicesArr=array();
foreach($devices as $device){
    $deviceByType[$device->type][]=$device;
    $devicesArr[$device->id]=$device;
}

$types=array("alert");

$isPost=FALSE;
if(isset($_POST["formname"]) && $_POST["formname"]=="system"){
    $isPost=TRUE;
}

$dateDebut=($isPost) ? $_POST["date_debut"] : date('d-m-Y');
$dateFin=($isPost) ? $_POST["date_fin"] : date('d-m-Y');
$type=($isPost) ? $_POST["type"] : NULL;

if($isPost){
    $logs=array();
    $sql="SELECT id FROM log ";
    $sql.= " WHERE 1=1 ";
    if($dateDebut != "" && $dateFin != ""){
        $dateDebutTmp=explode('-',$dateDebut);
        $dateFinTmp=explode('-',$dateFin);
        $sql.=" AND date BETWEEN '".$dateDebutTmp[2]."-".$dateDebutTmp[1]."-".$dateDebutTmp[0]." 00:00:00' AND '".$dateFinTmp[2]."-".$dateFinTmp[1]."-".$dateFinTmp[0]." 23:59:59'";
    }
    
    if(isset($_POST["devices"]) && count($_POST["devices"])>0){
        $sqlDevice="";
        foreach($_POST["devices"] as $deviceId){
            $sqlDevice .= ($sqlDevice == "") ? "" : ",";
            $sqlDevice .= $deviceId;
        }
        if($sqlDevice != ""){
            $sql.=" AND deviceid IN (".$sqlDevice.") ";
        }
    }
    
    if(isset($_POST["type"]) && $_POST["type"] != "-1"){
        $sql.=" AND rfid='".$_POST["type"]."' ";
    }
    
    $stmt = $GLOBALS["dbconnec"]->query($sql);
    $logs=Log::getLog($stmt->fetchAll(PDO::FETCH_COLUMN, 0));
} else {
    //Récupération Logs
    $logs = Log::getLastLogs(20);
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
                    Système				
                    <small>Recherche de Logs</small>
                </h3>
                <!-- END PAGE TITLE & BREADCRUMB-->
            </div>
    </div>
    <div class="row">
        <form class="form-horizontal" id="formsystem" method="POST" action="system.php">
            <div class="form-body">
            <input type="hidden" name="formname" id="formname" value="system" />
            <h3 class="form-section"><i class="fa fa-search"></i>&nbsp;Rechercher</h3>
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label class="control-label col-md-3" for="type">Type</label>
                        <div class="col-md-9">
                            <select id="type" name="type" class="form-control">
                                <option value="-1"></option>
                                <?php
                                foreach($types as $typeTmp){
                                    $selected = ($type==$typeTmp) ? " selected=\"selected\" " : "";
                                    echo "<option $selected value='".$typeTmp."'>Alerte</option>";
                                }
                                ?>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label class="control-label col-md-3" for="devices">Device</label>
                        <div class="col-md-9">
                             <select class="chosen" name="devices[]" id="devices" multiple="multiple" tabindex="6">
                                <option value=""></option>
                                <?php
                                foreach($deviceByType as $type=>$devicesTmp){
                                    echo "<optgroup label='".strtoupper($type)."'>";
                                    foreach($devicesTmp as $deviceTmp){
                                        $selected = (isset($_POST["devices"]) && in_array($deviceTmp->id, $_POST["devices"])) ? " selected='selected' " : "";
                                        echo "<option $selected value='".$deviceTmp->id."'>".$deviceTmp->name."</option>";
                                    }
                                }
                                ?>
                             </select>
                          </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-8">
                    <div class="form-group">
                        <label class="control-label col-md-3" for="date_debut">Entre</label>
                        <div class="col-md-9">
                            <div class=" input-group input-large date-picker input-daterange" data-date-format="dd-mm-yyyy" data-date="<?php echo $dateDebut; ?>">
                                <input class="form-control form-control-inline input-medium date-picker" type="text" name="date_debut" name="date_debut" value="<?php echo $dateDebut; ?>" />
                                <span class="input-group-addon"> et </span>
                                <input class="form-control form-control-inline input-medium date-picker" type="text" name="date_fin" name="date_fin" value="<?php echo $dateFin; ?>" />
                             </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <a class="btn blue" href="javascript:document.getElementById('formsystem').submit();">
                        <i class="fa fa-search"></i>
                        Rechercher
                    </a>
                </div>
                </div>
            </div>
            </div>
        </form>
    </div>
    <div class="row">
        <div class="col-md-12 ">
            <table class="table table-striped table-bordered table-hover" id="datatable">
                <thead>
                <tr>
                    <th>Device</th>
                    <th>Type</th>
                    <th>Evènement</th>
                    <th>Date</th>
                    <th>Niveau</th>
                </tr>
                </thead>
                <tbody>
                <?php
                foreach($logs as $log){
                    $date="";
                    if($log->date != ""){
                        $date=new DateTime($log->date);
                        $date=$date->format('d-m-Y H:i');
                    }
                    $deviceName="";
                    if(isset($log->deviceid)){
                        $device=Device::getDevice($log->deviceid);
                        $deviceName=$device->name;
                    }
                    
                    echo "<tr>";
                    echo "<td>".$deviceName."</td>";
                    echo "<td>".$log->rfid."</td>";
                    echo "<td>".$log->value."</td>";
                    echo "<td>".$date."</td>";
                    echo "<td>".$log->level."</td>";
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
    $(document).ready(function () {
        $('#datatable').dataTable({
            "aoColumns": [
              { "bSortable": true },
              { "bSortable": true},
              { "bSortable": true},
              { "bSortable": true},             
              { "bSortable": true }
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