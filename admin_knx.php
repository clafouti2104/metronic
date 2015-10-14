<?php
include "modules/header.php";
include "modules/sidebar.php";

$GLOBALS["dbconnec"] = connectDB();
include_once "models/Device.php";
include_once "models/Log.php";


$isPost=FALSE;
if(isset($_POST["formname"]) && $_POST["formname"]=="adminknx"){
    $isPost=TRUE;
}

$knxIpAddressBDD="";

$sql = "SELECT * FROM config WHERE name IN ('knx_ip_address')";
$stmt = $GLOBALS["dbconnec"]->prepare($sql);
$stmt->execute(array());
while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    switch(strtolower($row["name"])){
        case 'knx_ip_address':
            $knxIpAddressBDD = $row["value"];
            break;
        default:
    }
}

$knxIpAddress= ($isPost) ? $_POST["knx_ip_address"] : $knxIpAddressBDD;

if($isPost){
    $sql="UPDATE config SET value='".$knxIpAddress."' WHERE name='knx_ip_address';";
    $stmt = $GLOBALS["dbconnec"]->exec($sql);
    
    $info="Modifications enregistrées avec succès";
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
                        KNX				
                        <small>Configuration de la partie KNX</small>
                    </h3>
                    <?php if(isset($info)){echo "<div class=\"alert alert-success\">".$info."</div>";}?>
                    <!-- END PAGE TITLE & BREADCRUMB-->
                </div>
        </div>
        <form class="form-horizontal form" method="POST" action="admin_knx.php">
            <div class="form-body">
            <input type="hidden" name="formname" id="formname" value="adminknx" />
            <h3 class="form-section"><i class="fa fa-cog"></i>&nbsp;Routeur IP/KNX</h3>
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label class="control-label col-md-3" for="knx_ip_address">Adresse IP</label>
                        <div class="col-md-9">
                            <input class="form-control" name="knx_ip_address" id="knx_ip_address" type="text" value="<?php echo $knxIpAddress; ?>">
                            <span class="help-inline">Adresse IP du routeur KNX</span>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>
<?php
include "modules/footer.php";
?>