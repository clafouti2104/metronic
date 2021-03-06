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
include_once "models/Log.php";

$isPost=FALSE;
if(isset($_POST["formname"]) && $_POST["formname"]=="adminlogs"){
    $isPost=TRUE;
}

$levels=Log::getLevels();

$logLevelBDD = 10;

$sql = "SELECT * FROM config WHERE name IN ('log_level')";
$stmt = $GLOBALS["dbconnec"]->prepare($sql);
$stmt->execute(array());
while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    if($row["name"] == "log_level"){
        $logLevelBDD = $row["value"];
    }
}

$logLevel= ($isPost) ? $_POST["log_level"] : $logLevelBDD;

if($isPost){
    $sql="UPDATE config SET value='".$logLevel."' WHERE name='log_level'";
    $stmt = $GLOBALS["dbconnec"]->exec($sql);
}

$logfile = '/var/log/domokine.log';
$numlines = "20";
$cmd = "tail -$numlines '$logfile'";
$showLogs = shell_exec($cmd);
$showLogs = explode("\n", $showLogs);
//print_r(explode("\n", $showLogs));
?>
<!-- BEGIN PAGE -->
<div class="page-content-wrapper">
<div class="page-content">
    <div class="container-fluid">
    <div class="row">
            <div class="col-md-12">
                <!-- BEGIN PAGE TITLE & BREADCRUMB-->			
                <h3 class="page-title">
                    Logs				
                    <small>Gestion des logs</small>
                </h3>
                <ul class="page-breadcrumb breadcrumb">
                    <li class="btn-group">
                        <button class="btn btn-info btnRefresh" type="button"> <i class="fa fa-refresh "></i>Raffraîchir</button>
                    </li>
                    <li>
                        <i class="fa fa-home"></i>
                        <a href="index.php">Admin</a>
                        <i class="fa fa-angle-right"></i>
                    </li>
                    <li><a href="   #">Paramètres</a></li>
                </ul>
                <!-- END PAGE TITLE & BREADCRUMB-->
            </div>
    </div>
    <div class="row">
        <div class="col-md-12">
            <pre class="tailLogs">
<?php 
foreach($showLogs as $showLog){
    //$showLog = explode(" ", $showLog);
    //print_r($showLog);
    echo $showLog."<br/>";
} 
?>
            </pre>
        </div>
    </div>
    <div class="row">
        <div class="col-md-12">
            <form class="horizontal-form" method="POST" action="admin_logs.php">
                <div class="form-body">
                <input type="hidden" name="formname" id="formname" value="adminlogs" />
                <div class="form-group">
                    <label class="control-label col-md-3" for="log_level">Niveau de log</label>
                    <div class="col-md-9">
                        <select id="log_level" name="log_level" class="">
                            <option value="-1"></option>
<?php
foreach($levels as $tmpLevel=>$labelLevel){
    $selected=(strtolower($tmpLevel) == strtolower($logLevel)) ? " selected=\"selected\" " : "";
    echo "<option value=\"".$tmpLevel."\"$selected>".ucwords($labelLevel)."</option>";
}
?>
                        </select>
                        <span class="help-inline">Détermine les logs à afficher</span>
                    </div>
                </div>
                <div class="form-actions">
                    <button class="btn blue" type="submit">
                        <i class="fa fa-ok"></i>Valider
                    </button>
                    <a href="admin_device.php"><button class="btn" type="button">Retourner</button></a>
                </div>
                </div>
            </form>
        </div>
    </div>
    </div>
</div>
    
<script>
    jQuery(document).ready(function() {			
        $('.btnRefresh').bind('click',function(e){
            $.ajax({
                url: "ajax/refresh_logs.php",
                type: "POST",
                data: {
                    deviceId: 'none'
                },
                error: function(data){
                    toastr.error("Une erreur est survenue");
                },
                success: function(data){
                    eval(data);
                }
            });
        });
    });
</script>
<?php
include "modules/footer.php";
?>